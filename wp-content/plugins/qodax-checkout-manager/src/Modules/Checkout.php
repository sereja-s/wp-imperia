<?php

namespace Qodax\CheckoutManager\Modules;

use Qodax\CheckoutManager\Component\DisplayRule\DisplayRuleBuilder;
use Qodax\CheckoutManager\DB\Repositories\CheckoutFieldRepository;
use Qodax\CheckoutManager\Factories\FieldFactory;

if ( ! defined('ABSPATH')) {
    exit;
}

class Checkout extends AbstractModule
{
    private CheckoutFieldRepository $checkoutFieldRepository;

    // todo: refactor
    private ?array $fieldsCache = null;

    public function __construct(CheckoutFieldRepository $checkoutFieldRepository)
    {
        $this->checkoutFieldRepository = $checkoutFieldRepository;
    }

    public function boot(): void
    {
        add_filter('woocommerce_checkout_fields' , [ $this, 'overrideCheckoutFields' ], 1000);
        add_filter('woocommerce_get_country_locale', [$this, 'overrideCountryLocale']);
        add_filter('woocommerce_get_country_locale_base', [$this, 'overrideCountryLocaleDefault']);
        add_action('woocommerce_checkout_fields', [ $this, 'validateFields' ]);
        add_filter('woocommerce_default_address_fields' , [ $this, 'overrideDefaultFields' ]);
        add_action('woocommerce_checkout_update_order_meta', [ $this, 'updateOrderMeta' ]);
        add_action('woocommerce_admin_order_data_after_billing_address', [ $this, 'displayBillingOrderMeta' ]);
        add_action('woocommerce_admin_order_data_after_shipping_address', [ $this, 'displayShippingOrderMeta' ]);
        add_action('wp_head', [ $this, 'injectCheckoutStyles' ]);
        add_action('wp_enqueue_scripts', [ $this, 'enqueueScripts' ], 99);
    }

    public function overrideCheckoutFields(array $fields): array
    {
        $dbFields = $this->getAllFields();

        foreach ($dbFields as $dbField) {
            $checkoutField = FieldFactory::fromDB($dbField);

            if ($checkoutField->isActive()) {
                $fields[$dbField['section']][$dbField['field_name']] = $checkoutField->toWooCommerce();
            } else {
                unset($fields[$dbField['section']][$dbField['field_name']]);
            }
        }

        return $fields;
    }

    public function overrideCountryLocale(array $locale): array
    {
        $dbFields = $this->getAllFields();
        $preparedFields = [];
        $fieldsToCheck = ['address_1', 'postcode', 'city', 'state'];
        foreach ($dbFields as $field) {
            $fieldName = str_replace(['billing_', 'shipping_'], '', $field['field_name']);
            if (in_array($fieldName, $fieldsToCheck) && !isset($preparedFields[$fieldName])) {
                $fieldData = FieldFactory::fromDB($field)->toWooCommerce();
                $preparedFields[$fieldName] = [
                    'label' => $fieldData['label'],
                    'placeholder' => $fieldData['placeholder'],
                    'default' => $fieldData['default'],
                    'required' => $fieldData['required'],
                ];
            }
        }

        foreach ($locale as $country => $fields) {
            $locale[$country] = array_merge($fields, $preparedFields);
        }

        return $locale;
    }

    public function overrideCountryLocaleDefault(array $fields): array
    {
        $dbFields = $this->getAllFields();
        $preparedFields = [];
        $fieldsToCheck = ['address_1', 'postcode', 'city', 'state'];
        foreach ($dbFields as $field) {
            $fieldName = str_replace(['billing_', 'shipping_'], '', $field['field_name']);
            if (in_array($fieldName, $fieldsToCheck) && !isset($preparedFields[$fieldName])) {
                $fieldData = FieldFactory::fromDB($field)->toWooCommerce();
                $preparedFields[$fieldName] = [
                    'label' => $fieldData['label'],
                    'placeholder' => $fieldData['placeholder'],
                    'default' => $fieldData['default'],
                    'required' => $fieldData['required'],
                ];
            }
        }

        foreach ($preparedFields as $fieldName => $field) {
            $fields[$fieldName] = $field;
        }

        return $fields;
    }

    public function updateOrderMeta($orderId)
    {
        $order = wc_get_order((int)$orderId);
        if (!$order) {
            return;
        }

        $fields = $this->checkoutFieldRepository->getCustomFields();
        $needSave = false;

        foreach ($fields as $field) {
            $key = $field['field_name'];

            if ($this->isFieldHidden($field)) {
                continue;
            }

            if ( ! empty($_POST[$key])) {
                $order->update_meta_data('qxcm_' . $key, sanitize_text_field($_POST[$key]));
                $needSave = true;
            }
        }

        if ($needSave) {
            $order->save();
        }
    }

    public function displayBillingOrderMeta(\WC_Order $order)
    {
        $this->displayAdminOrderMeta($order, 'billing');
    }

    public function displayShippingOrderMeta(\WC_Order $order)
    {
        $this->displayAdminOrderMeta($order, 'shipping');
    }

    public function overrideDefaultFields($fields)
    {
        // todo: provide method to disable default fields validation

        return $fields;
    }

    public function injectCheckoutStyles()
    {
        if (get_option('qxcm_column_layout') === '1-column') {
            ?>
            <style>
                .woocommerce .woocommerce-checkout .col2-set .col-1,
                .woocommerce .woocommerce-checkout .col2-set .col-2 {
                    width: 100% !important;
                }

                .woocommerce .woocommerce-checkout .col2-set .col-1 {
                    margin-bottom: 30px;
                }
            </style>
            <?php
        }
    }

    public function enqueueScripts(): void
    {
        wp_enqueue_script(
            'qodax_checkout_manager_checkout_js',
            QODAX_CHECKOUT_MANAGER_PLUGIN_URL . 'assets/js/checkout.min.js',
            [ 'jquery' ],
            filemtime(QODAX_CHECKOUT_MANAGER_PLUGIN_DIR . 'assets/js/checkout.min.js'),
            true
        );

        // Display rules
        $dbFields = $this->getAllFields();
        $displayRules = [];
        $builder = new DisplayRuleBuilder();

        foreach ($dbFields as $dbField) {
            $checkoutField = FieldFactory::fromDB($dbField);

            if ($checkoutField->isActive()) {
                $rules = [];

                foreach ($checkoutField->getDisplayRules() as $rule) {
                    try {
                        $rules[] = $builder->buildFromArray($rule);
                    } catch (\Exception $e) {

                    }
                }

                if (count($rules) > 0) {
                    $displayRules[$dbField['field_name']] = $rules;
                }
            }
        }

        $fieldSkipMap = [
            'nova_poshta_shipping' => [
                'billing_address_1_field',
                'billing_address_2_field',
                'billing_city_field',
                'billing_state_field',
                'billing_postcode_field',
                'shipping_address_1_field',
                'shipping_address_2_field',
                'shipping_city_field',
                'shipping_state_field',
                'shipping_postcode_field',
            ],
        ];

        /**
         * Allows to set fields for which plugin doesn't apply display rules in checkout
         * This important for some friendly plugins like WC Ukraine Shipping
         * which also controls some WC fields in checkout
         *
         * @since 1.2.2
         */
        $fieldSkipMap = apply_filters('qxcm_field_skip_map', $fieldSkipMap);

        wp_localize_script('qodax_checkout_manager_checkout_js', 'qodax_checkout_manager_globals', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'csrf_token' => wp_create_nonce('qodax_checkout_manager'),
            'fieldSkipMap' => apply_filters('qxcm_field_skip_map', $fieldSkipMap),
            'displayRules' => $displayRules,
        ]);
    }

    public function validateFields(array $fields)
    {
        if (!wp_doing_ajax() || empty($_POST)) {
            return $fields;
        }

        $dbFields = $this->getAllFields();
        foreach ($dbFields as $dbField) {
            if ($this->isFieldHidden($dbField)) {
                unset($fields[$dbField['section']][$dbField['field_name']]);
            }
        }

        return $fields;
    }

    private function displayAdminOrderMeta(\WC_Order $order, string $section)
    {
        $fields = $this->checkoutFieldRepository->findBySection($section);

        foreach ($fields as $field) {
            if ((int)$field['native']) {
                continue;
            }

            $checkoutField = FieldFactory::fromDB($field);
            $label = $checkoutField->getMeta('label', $checkoutField->name);
            $metaValue = get_post_meta($order->get_id(), 'qxcm_' . $checkoutField->name, true);
            if (!$metaValue) {
                // Try to use legacy naming
                $metaValue = get_post_meta($order->get_id(), $checkoutField->name, true);
            }

            if ($metaValue) {
                echo '<p><strong>' . esc_html($label) . ':</strong> ' . esc_html($metaValue) . '</p>';
            }
        }
    }

    private function isFieldHidden(array $dbField): bool
    {
        $builder = new DisplayRuleBuilder();
        $checkoutField = FieldFactory::fromDB($dbField);

        if (!$checkoutField->isActive()) {
            return false;
        }

        $rules = [];
        foreach ($checkoutField->getDisplayRules() as $rule) {
            try {
                $rules[] = $builder->buildFromArray($rule);
            } catch (\Exception $e) {
                return true;
            }
        }

        $show = true;
        foreach ($rules as $rule) {
            $show = $rule->showField($checkoutField);
        }

        return !$show;
    }

    private function getAllFields(): array
    {
        if ($this->fieldsCache === null) {
            $this->fieldsCache = $this->checkoutFieldRepository->all();
        }

        return $this->fieldsCache;
    }
}
