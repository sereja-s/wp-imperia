<?php namespace Premmerce\OneClickOrder\Frontend;

use Premmerce\OneClickOrder\OneClickOrderPlugin;
use Premmerce\SDK\V2\FileManager\FileManager;
use WC_Product_Simple;
use WC_Product_Variable;

/**
 * Class Frontend
 *
 * @package Premmerce\OneClickOrder\Frontend
 */
class Frontend
{
    /**
     * @var FileManager
     */
    private $fileManager;

    /**
     * Frontend constructor.
     *
     * @param FileManager $fileManager
     */
    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
        $options           = get_option(OneClickOrderPlugin::SLUG, array());

        if (! empty($options['displayOnCatalog'])) {
            add_action(
                'woocommerce_after_shop_loop_item',
                array($this, 'renderOneClickOrderBtn'),
                35
            );
        }

        add_action(
            'woocommerce_single_product_summary',
            array($this, 'renderOneClickOrderBtn'),
            35
        );

        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('jquery');
            wp_enqueue_script(
                'oneclickorder-script',
                $this->fileManager->locateAsset('frontend/js/premmerce-oneclickorder.js'),
                array('jquery-blockui')
            );
            wp_enqueue_script(
                'magnific-popup',
                $this->fileManager->locateAsset('frontend/js/jquery.magnific-popup.min.js')
            );
            wp_enqueue_style(
                'magnific-popup',
                $this->fileManager->locateAsset('frontend/css/magnific-popup.css')
            );
            wp_enqueue_style(
                'oneclickorder-style',
                $this->fileManager->locateAsset('frontend/css/premmerce-oneclickorder.css')
            );
        });

        $this->hooks();
    }

    /**
     * Register hooks
     */
    public function hooks()
    {
        add_action(
            'wc_ajax_premmerce_click_order_popup',
            array($this, 'wcAjaxClickOrderPopup')
        );
        add_action(
            'wc_ajax_premmerce_click_order_add',
            array($this, 'wcAjaxClickOrderAdd')
        );

        add_action('rest_api_init', function () {
            register_rest_route('premmerce/oneclickorder', '/add', array(
                'methods'  => \WP_REST_Server::CREATABLE,
                'callback' => array($this, 'restClickOrderAdd'),
            ));
            register_rest_route('premmerce/oneclickorder', '/popup', array(
                'methods'  => \WP_REST_Server::READABLE,
                'callback' => array($this, 'restClickOrderPopup'),
            ));
        });
    }

    /**
     * @param int $product_id
     *
     * @return string
     */
    public function clickOrderPopup($product_id)
    {
        $options = get_option(OneClickOrderPlugin::SLUG, array(
            'name'    => 0,
            'email'   => 0,
            'phone'   => 0,
            'comment' => 0,
        ));

        $routeUrl = esc_url(add_query_arg(array(
            'wc-ajax' => 'premmerce_click_order_add'
        ), get_the_permalink()));

        return $this->fileManager->renderTemplate('frontend/oneclickorder-popup.php', array(
            'routeUrl'  => $routeUrl,
            'options'   => $options,
            'productId' => $product_id,
            'userData'  => is_user_logged_in()
                ? $this->getCustomerAddress(get_current_user_id()) : false,
            'success'   => false,
        ));
    }

    /**
     * Render plugin popup
     */
    public function wcAjaxClickOrderPopup()
    {
        $data = $_REQUEST;

        $productId = 0;

        if (isset($data['product_id']) && ! empty($data['product_id'])) {
            $productId = $data['product_id'];
        }
        echo $this->clickOrderPopup($productId);
    }

    public function restClickOrderPopup(\WP_REST_Request $request)
    {
        $data = $request->get_params();
        $productId = 0;

        if (isset($data['product_id']) && ! empty($data['product_id'])) {
            $productId = $data['product_id'];
        }

        return rest_ensure_response(
            $this->clickOrderPopup($productId)
        );
    }

    /**
     * @param $product_id
     * @param $data
     *
     * @return string
     * @throws \WC_Data_Exception
     */
    public function clickOrderAdd($product_id, $data)
    {
        $comment = '<b>' . __('Buy now', 'premmerce-woocommerce-buy-now') . ": " . '</b>' . PHP_EOL;

        $order = wc_create_order();

        $product = wc_get_product($product_id);

        if ($product) {
            $order->add_product($product);
            $order->calculate_totals();
        }

        $address = array();

        if (is_user_logged_in()) {
            $userId = get_current_user_id();
            $order->set_customer_id($userId);
            $address = $this->getCustomerAddress($userId);
        } else {
            if (isset($data['name'])) {
                $address['first_name'] = $data['name'];
            }
            if (isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $address['email'] = $data['email'];
            }
            if (isset($data['phone'])) {
                $address['phone'] = $data['phone'];
            }
        }

        $order->set_address($address, 'billing');

        if (isset($data['name'])) {
            $comment .= __('Name', 'premmerce-woocommerce-buy-now') . ": " . $data['name'] . PHP_EOL;
        }
        if (isset($data['email'])) {
            $comment .= __('E-mail', 'premmerce-woocommerce-buy-now') . ": " . $data['email'] . PHP_EOL;
        }
        if (isset($data['phone'])) {
            $comment .= __('Phone number', 'premmerce-woocommerce-buy-now') . ": " . $data['phone'] . PHP_EOL;
        }
        if (isset($data['comment'])) {
            $comment .= __('Comment', 'premmerce-woocommerce-buy-now') . ": " . $data['comment'] . PHP_EOL;
        }

        $order->add_order_note($comment);

        $order->set_status('on-hold');

        $response = $this->fileManager->renderTemplate(
            'frontend/oneclickorder-popup.php',
            array(
                'success'   => true,
                'routeUrl'  => '',
                'options'   => null,
                'productId' => 0,
                'userData'  => false,
            )
        );
        
        $order->save();
        
        do_action('woocommerce_checkout_order_processed', $order->get_id(), array(), $order);

        return $response;
    }

    /**
     * @throws \WC_Data_Exception
     */
    public function wcAjaxClickOrderAdd()
    {
        $data = $_REQUEST;

        $productId = isset($data['product_id']) ? $data['product_id'] : 0;

        header('Content-Type: text/json');

        echo json_encode(array(
            'html' => $this->clickOrderAdd($productId, $data)
        ));
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return mixed|\WP_REST_Response
     * @throws \WC_Data_Exception
     */
    public function restClickOrderAdd(\WP_REST_Request $request)
    {
        $data = $request->get_params();
        $productId = isset($data['product_id']) ? $data['product_id'] : 0;

        return rest_ensure_response(array(
            'html'    => $this->clickOrderAdd($productId, $data),
        ));
    }

    /**
     * Render one click order button
     */
    public function renderOneClickOrderBtn()
    {
        global $product;

        $catalog_show_rules = apply_filters('premmerce_buy_now_catalog_show_button', ($product instanceof WC_Product_Variable && is_single()));

        if ($product instanceof WC_Product_Simple || $catalog_show_rules) {
            if ($product->is_purchasable() && $product->is_in_stock()) {
                $addRoute = esc_url(add_query_arg(
                    array('wc-ajax' => 'premmerce_click_order_popup'),
                    get_the_permalink()
                ));

                $this->fileManager->includeTemplate('frontend/oneclickorder-btn.php', array(
                    'popupUrl'  => $addRoute,
                    'productId' => $product->get_id(),
                ));
            }
        }
    }

    /**
     * Get user billing address data
     *
     * @param int $id
     *
     * @return array
     */
    private function getCustomerAddress($id)
    {
        return array(
            'first_name' => get_user_meta($id, 'billing_first_name', true),
            'last_name'  => get_user_meta($id, 'billing_last_name', true),
            'company'    => get_user_meta($id, 'billing_company', true),
            'email'      => get_user_meta($id, 'billing_email', true),
            'phone'      => get_user_meta($id, 'billing_phone', true),
            'address_1'  => get_user_meta($id, 'billing_address_1', true),
            'address_2'  => get_user_meta($id, 'billing_address_2', true),
            'city'       => get_user_meta($id, 'billing_city', true),
            'state'      => get_user_meta($id, 'billing_state', true),
            'postcode'   => get_user_meta($id, 'billing_postcode', true),
            'country'    => get_user_meta($id, 'billing_country', true),
        );
    }
}
