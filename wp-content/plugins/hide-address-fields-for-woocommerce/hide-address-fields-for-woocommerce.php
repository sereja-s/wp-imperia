<?php

/**
 * Plugin Name: Hide Address Fields for WooCommerce
 * Plugin URI: https://en.condless.com/hide-address-fields-for-woocommerce/
 * Description: WooCommerce plugin for hiding the billing address fields on checkout based on the selected shipping/payment methods.
 * Version: 1.2.2
 * Author: Condless
 * Author URI: https://en.condless.com/
 * Developer: Condless
 * Developer URI: https://en.condless.com/
 * Contributors: condless
 * Text Domain: hide-address-fields-for-woocommerce
 * Domain Path: /i18n/languages
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.2
 * Tested up to: 6.8
 * Requires PHP: 7.0
 * WC requires at least: 3.4
 * WC tested up to: 9.8
 */

/**
 * Exit if accessed directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || get_site_option( 'active_sitewide_plugins') && array_key_exists( 'woocommerce/woocommerce.php', get_site_option( 'active_sitewide_plugins' ) ) ) {

	/**
	 * Hide Address Fields for WooCommerce Class.
	 */
	class WC_HAF {

		/**
		 * Construct class
		 */
		public function __construct() {
			add_action( 'before_woocommerce_init', function() {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
				}
			} );
			add_action( 'plugins_loaded', [ $this, 'init' ] );
		}

		/**
		 * WC init
		 */
		public function init() {
			$this->init_textdomain();
			$this->init_settings();
			if ( ! in_array( false, [ get_option( 'wc_haf_shipping_methods' ), get_option( 'wc_haf_payment_methods' ) ], true ) ) {
				$this->init_functions();
			}
		}

		/**
		 * Load text domain for internationalization
		 */
		public function init_textdomain() {
			load_plugin_textdomain( 'hide-address-fields-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages' );
		}

		/**
		 * WC settings init
		 */
		public function init_settings() {
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'wc_update_settings_link' ] );
			add_filter( 'plugin_row_meta', [ $this, 'wc_add_plugin_links' ], 10, 4 );
			add_filter( 'woocommerce_settings_tabs_array', [ $this, 'wc_add_settings_tab' ], 50 );
			add_action( 'woocommerce_settings_tabs_haf', [ $this, 'wc_settings_tab' ] );
			add_action( 'woocommerce_update_options_haf', [ $this, 'wc_update_settings' ] );
			add_filter( 'woocommerce_admin_settings_sanitize_option_wc_haf_shipping_methods', [ $this, 'wc_sanitize_option_wc_haf_shipping_methods' ], 10, 2 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_wc_haf_payment_methods', [ $this, 'wc_sanitize_option_wc_haf_payment_methods' ], 10, 2 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_wc_haf_top_state', [ $this, 'wc_sanitize_option_wc_haf_top_state' ], 10, 2 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_wc_haf_hide_postcode', [ $this, 'wc_sanitize_option_wc_haf_hide_postcode' ], 10, 2 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_wc_haf_hide_state', [ $this, 'wc_sanitize_option_wc_haf_hide_state' ], 10, 2 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_wc_haf_hide_city', [ $this, 'wc_sanitize_option_wc_haf_hide_city' ], 10, 2 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_wc_haf_hide_country', [ $this, 'wc_sanitize_option_wc_haf_hide_country' ], 10, 2 );
		}

		/**
		 * WC functions init
		 */
		public function init_functions() {
			add_shortcode( 'haf_shipping_options', [ $this, 'wc_haf_shipping_options_shortcode' ] );
			add_action( 'wp_ajax_wc_haf_update_shipping_methods', [ $this, 'wc_haf_update_shipping_methods' ] );
			add_action( 'wp_ajax_nopriv_wc_haf_update_shipping_methods', [ $this, 'wc_haf_update_shipping_methods' ] );
			add_filter( 'woocommerce_cart_needs_shipping', [ $this, 'wc_force_needs_shipping' ] );
			add_filter( 'pre_option_woocommerce_shipping_cost_requires_address', [ $this, 'wc_disable_cost_requires_address_option' ] );
			add_filter( 'woocommerce_formatted_address_replacements', [ $this, 'wc_fix_cart_address_display' ], 10, 2 );
			add_filter( 'woocommerce_shipping_calculator_enable_postcode', [ $this, 'wc_hide_cart_postcode' ] );
			add_filter( 'woocommerce_shipping_calculator_enable_city', [ $this, 'wc_hide_cart_city' ] );
			add_filter( 'woocommerce_shipping_calculator_enable_state', [ $this, 'wc_hide_cart_state' ] );
			add_filter( 'woocommerce_checkout_fields', [ $this, 'wc_sort_checkout_fields' ] );
			add_filter( 'woocommerce_get_country_locale', [ $this, 'wc_sort_locale_fields' ] );
			add_filter( 'woocommerce_shipping_chosen_method', [ $this, 'wc_set_default_shipping_method' ], 10, 2 );
			add_filter( 'woocommerce_cart_shipping_method_full_label', [ $this, 'wc_hide_shipping_price' ] );
			add_action( 'woocommerce_after_checkout_form', [ $this, 'wc_disable_address' ] );
			add_filter( 'woocommerce_validate_postcode', [ $this, 'wc_disable_postcode_validation' ], 10, 3 );
			add_action( 'woocommerce_after_checkout_validation', [ $this, 'wc_disable_validations' ], 10, 2 );
			add_action( 'woocommerce_checkout_update_customer', [ $this, 'wc_get_customer_address' ], 10, 2 );
			add_action( 'woocommerce_checkout_create_order', [ $this, 'wc_checkout_remove_fields' ], 5, 2 );
			if ( 'yes' === get_option( 'wc_haf_top_shipping_methods' ) ) {
				add_filter( 'wc_get_template', [ $this, 'wc_get_review_order_template' ], 10, 2 );
				add_filter( 'woocommerce_form_field_shipping_options', [ $this, 'wc_shipping_options_form' ] );
				add_filter( 'woocommerce_checkout_fields', [ $this, 'wc_shipping_options_field' ] );
				add_filter( 'wp_head', [ $this, 'wc_add_shipping_top_style' ] );
				add_filter( 'woocommerce_update_order_review_fragments', [ $this, 'wc_top_shipping_table_update' ] );
				add_action( 'woocommerce_review_order_before_order_total', [ $this, 'wc_display_shipping_price' ] );
				if ( 'yes' !== get_option( 'wc_haf_hide_country' ) && ( 'yes' === get_option( 'wc_haf_top_state' ) || 'yes' !== get_option( 'wc_haf_hide_postcode' ) ) ) {
					add_filter( 'default_checkout_billing_country', [ $this, 'wc_set_default_country' ] );
				}
			}
			if ( 'yes' === get_option( 'wc_haf_top_payment_methods' ) ) {
				add_filter( 'wc_get_template', [ $this, 'wc_get_payment_template' ], 10, 2 );
				add_filter( 'woocommerce_form_field_payment_options', [ $this, 'wc_payment_options_form' ] );
				add_filter( 'woocommerce_checkout_fields', [ $this, 'wc_payment_options_field' ] );
				add_filter( 'wp_head', [ $this, 'wc_add_payment_top_style' ] );
				add_filter( 'woocommerce_update_order_review_fragments', [ $this, 'wc_top_payment_methods_update' ] );
			}
		}

		/**
		 * Add plugin links to the plugin menu
		 * @param mixed $links
		 * @return mixed
		 */
		public function wc_update_settings_link( $links ) {
			array_unshift( $links, '<a href=' . esc_url( add_query_arg( 'page', 'wc-settings&tab=haf', get_admin_url() . 'admin.php' ) ) . '>' . __( 'Settings' ) . '</a>' );
			return $links;
		}

		/**
		 * Add plugin meta links to the plugin menu
		 * @param mixed $links_array
		 * @param mixed $plugin_file_name
		 * @param mixed $plugin_data
		 * @param mixed $status
		 * @return mixed
		 */
		public function wc_add_plugin_links( $links_array, $plugin_file_name, $plugin_data, $status ) {
			if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
				$sub_domain = 'he_IL' === get_locale() ? 'www' : 'en';
				$links_array[] = "<a href=https://$sub_domain.condless.com/hide-address-fields-for-woocommerce/>" . __( 'Docs', 'woocommerce' ) . '</a>';
				$links_array[] = "<a href=https://$sub_domain.condless.com/contact/>" . _x( 'Contact', 'Theme starter content' ) . '</a>';
			}
			return $links_array;
		}

		/**
		 * Add a new settings tab to the WooCommerce settings tabs array
		 * @param array $settings_tabs
		 * @return array
		 */
		public function wc_add_settings_tab( $settings_tabs ) {
			$settings_tabs['haf'] = __( 'Hide Address Fields', 'hide-address-fields-for-woocommerce' );
			return $settings_tabs;
		}

		/**
		 * Use the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function
		 * @uses woocommerce_admin_fields()
		 * @uses self::wc_get_settings()
		 */
		public function wc_settings_tab() {
			woocommerce_admin_fields( self::wc_get_settings() );
		}

		/**
		 * Use the WooCommerce options API to save settings via the @see woocommerce_update_options() function
		 * @uses woocommerce_update_options()
		 * @uses self::wc_get_settings()
		 */
		public function wc_update_settings() {
			woocommerce_update_options( self::wc_get_settings() );
		}

		/**
		 * Get all the settings for this plugin for @see woocommerce_admin_fields() function
		 * @return array Array of settings for @see woocommerce_admin_fields() function
		 */
		public function wc_get_settings() {
			foreach ( WC()->shipping->get_shipping_methods() as $key => $method ) {
				$shipping_options[ $key ] = $method->method_title;
			}
			foreach ( WC()->payment_gateways->payment_gateways() as $key => $method ) {
				$payment_options[ $key ] = $method->method_title;
			}
			$settings = [
				'methods_section'	=> [
					'name'	=> __( 'Conditions' ),
					'desc'	=> __( 'The fields will be hidden on checkout only when both conditions are met', 'hide-address-fields-for-woocommerce' ) . ' (' . __( 'use the CTRL key to select multiple methods', 'hide-address-fields-for-woocommerce' ) . ')' . '. <a href=https://' . ( 'he_IL' === get_locale() ? 'www' : 'en' ) . '.condless.com/contact/>' . __( 'Support' ) . '</a>',
					'type'	=> 'title',
					'id'	=> 'wc_haf_methods_section'
				],
				'shipping_methods'	=> [
					'name'		=> __( 'Shipping methods', 'woocommerce' ),
					'desc_tip'	=> __( 'Select shipping methods which do not require the customer address', 'hide-address-fields-for-woocommerce' ),
					'type'		=> 'multiselect',
					'options'	=> $shipping_options ?? [],
					'default'	=> apply_filters( 'woocommerce_local_pickup_methods', [ 'local_pickup' ] ),
					'id'		=> 'wc_haf_shipping_methods'
				],
				'payment_methods'	=> [
					'name'		=> __( 'Payment methods', 'woocommerce' ),
					'desc_tip'	=> __( 'Select payment methods which do not require the customer address', 'hide-address-fields-for-woocommerce' ),
					'type'		=> 'multiselect',
					'options'	=> $payment_options ?? [],
					'default'	=> [ 'bacs', 'cheque', 'cod' ],
					'id'		=> 'wc_haf_payment_methods'
				],
				'methods_section_end'	=> [
					'type'	=> 'sectionend',
					'id'	=> 'wc_haf_methods_section_end'
				],
				'top_section'	=> [
					'name'	=> __( 'Checkout Page', 'woocommerce' ) . ' ' . __( 'Layout' ),
					'type'	=> 'title',
					'id'	=> 'wc_haf_top_section'
				],
				'top_shipping_methods'	=> [
					'name'		=> __( 'Shipping methods', 'woocommerce' ) . ' ' . __( 'Top' ),
					'desc'		=> __( 'Moves the shipping options to the billing details section on checkout', 'hide-address-fields-for-woocommerce' ),
					'type'		=> 'checkbox',
					'default'	=> 'no',
					'id'		=> 'wc_haf_top_shipping_methods'
				],
				'top_payment_methods'	=> [
					'name'		=> __( 'Payment methods', 'woocommerce' ) . ' ' . __( 'Top' ),
					'desc'		=> __( 'Moves the payment methods to the billing details section on checkout', 'hide-address-fields-for-woocommerce' ),
					'type'		=> 'checkbox',
					'default'	=> 'no',
					'id'		=> 'wc_haf_top_payment_methods'
				],
				'top_state'	=> [
					'name'		=> __( 'State / County', 'woocommerce' ) . ' ' . __( 'Top' ),
					'desc'		=> __( 'Moves the state field for the shop country above the address fields on checkout', 'hide-address-fields-for-woocommerce' ),
					'desc_tip'	=> __( 'Must be enabled if the top shipping option is enabled, the hide state option is disabled, and shipping zones are restricted by the states of the shop country.', 'hide-address-fields-for-woocommerce' ),
					'type'		=> 'checkbox',
					'default'	=> 'no',
					'id'		=> 'wc_haf_top_state'
				],
				'top_section_end'	=> [
					'type'	=> 'sectionend',
					'id'	=> 'wc_haf_top_section_end'
				],
				'fields_section'	=> [
					'name'	=> __( 'Billing fields to hide', 'hide-address-fields-for-woocommerce' ),
					'desc'	=> __( 'Choose which billing fields to hide on checkout when conditions are met.', 'hide-address-fields-for-woocommerce' ) . ' ' . __( 'The hiding of fields which shipping zones are restricted by must be disabled (unless all shipping zones have the same type of shipping methods and each type of shipping methods which hide the fields have the same price in all of the shipping zones).', 'hide-address-fields-for-woocommerce' ),
					'type'	=> 'title',
					'id'	=> 'wc_haf_fields_section'
				],
				'hide_city'	=> [
					'name'		=> __( 'City', 'woocommerce' ),
					'desc'		=> __( 'When conditions are met hide the field', 'hide-address-fields-for-woocommerce' ) . ' ' . __( 'City', 'woocommerce' ),
					'type'		=> 'checkbox',
					'default'	=> 'yes',
					'id'		=> 'wc_haf_hide_city'
				],
				'hide_postcode'	=> [
					'name'		=> __( 'Postcode', 'woocommerce' ),
					'desc'		=> __( 'When conditions are met hide the field', 'hide-address-fields-for-woocommerce' ) . ' ' . __( 'Postcode', 'woocommerce' ),
					'desc_tip'	=> __( 'Must be disabled if shipping zones are restricted by postcode.', 'hide-address-fields-for-woocommerce' ),
					'type'		=> 'checkbox',
					'default'	=> 'yes',
					'id'		=> 'wc_haf_hide_postcode'
				],
				'hide_state'	=> [
					'name'		=> __( 'State / County', 'woocommerce' ),
					'type'		=> 'checkbox',
					'desc'		=> __( 'When conditions are met hide the field', 'hide-address-fields-for-woocommerce' ) . ' ' . __( 'State / County', 'woocommerce' ),
					'desc_tip'	=> __( 'Must be disabled if shipping zones are restricted by state / postcode.', 'hide-address-fields-for-woocommerce' ),
					'default'	=> 'no',
					'id'		=> 'wc_haf_hide_state'
				],
				'hide_country'	=> [
					'name'		=> __( 'Country / Region', 'woocommerce' ),
					'type'		=> 'checkbox',
					'desc'		=> __( 'When conditions are met hide the field', 'hide-address-fields-for-woocommerce' ) . ' ' . __( 'Country / Region', 'woocommerce' ),
					'desc_tip'	=> __( 'Must be disabled if shipping zones are restricted by country / state / postcode.', 'hide-address-fields-for-woocommerce' ),
					'default'	=> 'no',
					'id'		=> 'wc_haf_hide_country'
				],
				'hide_company'	=> [
					'name'		=> __( 'Company', 'woocommerce' ),
					'desc'		=> __( 'When conditions are met hide the field', 'hide-address-fields-for-woocommerce' ) . ' ' . __( 'Company', 'woocommerce' ),
					'type'		=> 'checkbox',
					'default'	=> 'no',
					'id'		=> 'wc_haf_hide_company'
				],
				'hide_phone'	=> [
					'name'		=> __( 'Phone', 'woocommerce' ),
					'desc'		=> __( 'When conditions are met hide the field', 'hide-address-fields-for-woocommerce' ) . ' ' . __( 'Phone', 'woocommerce' ),
					'type'		=> 'checkbox',
					'default'	=> 'no',
					'id'		=> 'wc_haf_hide_phone'
				],
				'hide_first_name'	=> [
					'name'		=> __( 'First name', 'woocommerce' ),
					'desc'		=> __( 'When conditions are met hide the field', 'hide-address-fields-for-woocommerce' ) . ' ' . __( 'First name', 'woocommerce' ),
					'type'		=> 'checkbox',
					'default'	=> 'no',
					'id'		=> 'wc_haf_hide_first_name'
				],
				'hide_last_name'	=> [
					'name'		=> __( 'Last name', 'woocommerce' ),
					'desc'		=> __( 'When conditions are met hide the field', 'hide-address-fields-for-woocommerce' ) . ' ' . __( 'Last name', 'woocommerce' ),
					'type'		=> 'checkbox',
					'default'	=> 'no',
					'id'		=> 'wc_haf_hide_last_name'
				],
				'hide_comments'	=> [
					'name'		=> __( 'Order notes', 'woocommerce' ),
					'desc'		=> __( 'When conditions are met hide the field', 'hide-address-fields-for-woocommerce' ) . ' ' . __( 'Order notes', 'woocommerce' ),
					'type'		=> 'checkbox',
					'default'	=> 'no',
					'id'		=> 'wc_haf_hide_comments'
				],
				'fields_section_end'	=> [
					'type'	=> 'sectionend',
					'id'	=> 'wc_haf_fields_section_end'
				],
			];
			return apply_filters( 'wc_haf_settings', $settings );
		}

		/**
		 * Sanitize the shipping methods option
		 * @param mixed $value
		 * @param mixed $option
		 * @return mixed
		 */
		public function wc_sanitize_option_wc_haf_shipping_methods( $value, $option ) {
			if ( empty( $value ) ) {
				WC_Admin_Settings::add_message( __( 'Please select shipping methods, otherwise the fields can be hidden only on orders of virtual products', 'hide-address-fields-for-woocommerce' ) );
			} elseif ( array_diff( $value, apply_filters( 'woocommerce_local_pickup_methods', [ 'local_pickup' ] ) ) ) {
				WC_Admin_Settings::add_message( __( 'Make sure the selected shipping methods do not require the customer address to proccess the order', 'hide-address-fields-for-woocommerce' ) );
			}
			if ( get_option( $option['id'] ) !== $value && function_exists( 'is_plugin_active' ) ) {
				if ( is_plugin_active( 'cities-shipping-zones-for-woocommerce/cities-shipping-zones-for-woocommerce.php' ) && ( ! has_filter( 'haf_shipping_options_priority' ) && ! has_filter( 'csz_shortcode_before_js' ) ) ) {
					$integrations[] = 'Cities Shipping Zones for WooCommerce';
				}
				if ( ! empty( $integrations ) ) {
					WC_Admin_Settings::add_message( __( 'Integrations are available for', 'hide-address-fields-for-woocommerce' ) . ' ' . implode( ', ', $integrations ) );				
				}
			}
			return $value;
		}

		/**
		 * Sanitize the payment methods option
		 * @param mixed $value
		 * @param mixed $option
		 * @return mixed
		 */
		public function wc_sanitize_option_wc_haf_payment_methods( $value, $option ) {
			if ( empty( $value ) ) {
				WC_Admin_Settings::add_message( __( 'Please select payment methods, otherwise the fields may be hidden only on orders of free products', 'hide-address-fields-for-woocommerce' ) );
			} elseif ( array_diff( $value, [ 'cod', 'bacs', 'cheque' ] ) ) {
				WC_Admin_Settings::add_message( __( 'Make sure the selected payment methods do not require the customer address to proccess the order', 'hide-address-fields-for-woocommerce' ) );
			}
			return $value;
		}

		/**
		 * Sanitize the top state option
		 * @param mixed $value
		 * @param mixed $option
		 * @return mixed
		 */
		public function wc_sanitize_option_wc_haf_top_state( $value, $option ) {
			if ( 'yes' === $value && 'yes' !== get_option( $option['id'] ) && function_exists( 'is_plugin_active' ) && ( is_plugin_active( 'woo-checkout-field-editor-pro/checkout-form-designer.php' ) || is_plugin_active( 'yith-woocommerce-checkout-manager/init.php' ) || is_plugin_active( 'woocommerce-jetpack/woocommerce-jetpack.php' ) || is_plugin_active( 'woocommerce-checkout-manager/woocommerce-checkout-manager.php' ) || is_plugin_active( 'flexible-checkout-fields/flexible-checkout-fields.php' ) ) ) {
				WC_Admin_Settings::add_message( __( 'Checkout Field Editor: make sure to enable the fields billing_country and billing_address_1 fields', 'hide-address-fields-for-woocommerce' ) );
				WC_Admin_Settings::add_message( __( 'Checkout Field Editor: set the order of the fields as follow: billing_country, billing_state, billing_address_1', 'hide-address-fields-for-woocommerce' ) );
			}
			return $value;
		}

		/**
		 * Sanitize the hide postcode option
		 * @param mixed $value
		 * @param mixed $option
		 * @return mixed
		 */
		public function wc_sanitize_option_wc_haf_hide_postcode( $value, $option ) {
			$old_option = get_option( $option['id'] );
			if ( 'yes' === $value && 'yes' !== $old_option ) {
				WC_Admin_Settings::add_message( __( 'Make sure to not restrict shipping zones by postcode or disable the hiding of the postcode field', 'hide-address-fields-for-woocommerce' ) );
				if ( wc_tax_enabled() && 'base' !== get_option( 'woocommerce_tax_based_on' ) && ( array_diff( $_POST['wc_haf_shipping_methods'], apply_filters( 'woocommerce_local_pickup_methods', [ 'local_pickup' ] ) ) || ! apply_filters( 'woocommerce_apply_base_tax_for_local_pickup', true ) ) ) {
					WC_Admin_Settings::add_message( __( 'Make sure to not restrict tax rates by postcode or disable its hiding', 'hide-address-fields-for-woocommerce' ) );
				}
			} elseif ( 'yes' !== $value && 'yes' === $old_option && function_exists( 'is_plugin_active' ) && ( is_plugin_active( 'woo-checkout-field-editor-pro/checkout-form-designer.php' ) || is_plugin_active( 'yith-woocommerce-checkout-manager/init.php' ) || is_plugin_active( 'woocommerce-jetpack/woocommerce-jetpack.php' ) || is_plugin_active( 'woocommerce-checkout-manager/woocommerce-checkout-manager.php' ) || is_plugin_active( 'flexible-checkout-fields/flexible-checkout-fields.php' ) ) ) {
				WC_Admin_Settings::add_message( __( 'Checkout Field Editor: make sure to enable the fields billing_country and billing_address_1 fields', 'hide-address-fields-for-woocommerce' ) );
				WC_Admin_Settings::add_message( __( 'Checkout Field Editor: if shipping zones are restricted by postcode set the order of the fields as follow: billing_country, billing_postcode, billing_address_1', 'hide-address-fields-for-woocommerce' ) );
			}
			return $value;
		}

		/**
		 * Sanitize the hide state option
		 * @param mixed $value
		 * @param mixed $option
		 * @return mixed
		 */
		public function wc_sanitize_option_wc_haf_hide_state( $value, $option ) {
			if ( 'yes' === $value && 'yes' !== get_option( $option['id'] ) ) {
				WC_Admin_Settings::add_message( __( 'Make sure to not restrict shipping zones by the state / postcode or disable the hiding of the state field', 'hide-address-fields-for-woocommerce' ) );
				if ( wc_tax_enabled() && 'base' !== get_option( 'woocommerce_tax_based_on' ) && ( array_diff( $_POST['wc_haf_shipping_methods'], apply_filters( 'woocommerce_local_pickup_methods', [ 'local_pickup' ] ) ) || ! apply_filters( 'woocommerce_apply_base_tax_for_local_pickup', true ) ) ) {
					WC_Admin_Settings::add_message( __( 'Make sure to not restrict tax rates by the states of the shop country or disable its hiding', 'hide-address-fields-for-woocommerce' ) );
				}
			}
			return $value;
		}

		/**
		 * Sanitize the hide city option
		 * @param mixed $value
		 * @param mixed $option
		 * @return mixed
		 */
		public function wc_sanitize_option_wc_haf_hide_city( $value, $option ) {
			if ( 'yes' === $value && 'yes' !== get_option( $option['id'] ) ) {
				if ( wc_tax_enabled() && 'base' !== get_option( 'woocommerce_tax_based_on' ) && ( array_diff( $_POST['wc_haf_shipping_methods'], apply_filters( 'woocommerce_local_pickup_methods', [ 'local_pickup' ] ) ) || ! apply_filters( 'woocommerce_apply_base_tax_for_local_pickup', true ) ) ) {
					WC_Admin_Settings::add_message( __( 'Make sure to not restrict tax rates by city or disable its hiding', 'hide-address-fields-for-woocommerce' ) );
				}
			}
			return $value;
		}

		/**
		 * Sanitize the hide country option
		 * @param mixed $value
		 * @param mixed $option
		 * @return mixed
		 */
		public function wc_sanitize_option_wc_haf_hide_country( $value, $option ) {
			if ( 'yes' === $value && 'yes' !== get_option( $option['id'] ) ) {
				WC_Admin_Settings::add_message( __( 'Make sure to not restrict shipping zones by country / state / postcode or disable the hiding of the country field', 'hide-address-fields-for-woocommerce' ) );
				if ( wc_tax_enabled() && 'base' !== get_option( 'woocommerce_tax_based_on' ) && ( array_diff( $_POST['wc_haf_shipping_methods'], apply_filters( 'woocommerce_local_pickup_methods', [ 'local_pickup' ] ) ) || ! apply_filters( 'woocommerce_apply_base_tax_for_local_pickup', true ) ) ) {
					WC_Admin_Settings::add_message( __( 'Make sure to not restrict tax rates by country or disable its hiding', 'hide-address-fields-for-woocommerce' ) );
				}
			}
			return $value;
		}

		/**
		 * Add shortcode that display the shipping options
		 * @param mixed $atts
		 * @return mixed
		 */
		public function wc_haf_shipping_options_shortcode( $atts ) {
			if ( isset( WC()->session, WC()->cart ) && ! is_cart() && ! is_checkout() ) {
				add_action( 'wp_footer', [ $this, 'wc_shipping_options_shortcode_js' ] );
				WC()->cart->calculate_shipping();
				ob_start();
				wc_cart_totals_shipping_html();
				return ob_get_clean();
			}
		}

		/**
		 * Trigger the update shipping method ajax action for the shipping options shortcode
		 * @param mixed $atts
		 * @return mixed
		 *
		 */
		public function wc_shipping_options_shortcode_js() {
			?>
			<script type="text/javascript">
			jQuery( function( $ ) {
				$( document ).on( 'change', 'select.shipping_method, input[name^="shipping_method"]', function() {
					var shipping_methods = {};
					$( 'select.shipping_method, :input[name^=shipping_method][type=radio]:checked, :input[name^=shipping_method][type=hidden]' ).each( function() {
						shipping_methods[ $( this ).data( 'index' ) ] = $( this ).val();
					} );
					var data = {
						action:		'wc_haf_update_shipping_methods',
						shipping_method:	shipping_methods
					};
					$.post( '<?php echo admin_url( 'admin-ajax.php' ); ?>', data, function( response ) {} );
				} );
			} );
			</script>
		<?php
		}

		/**
		 * Update the customer's shipping methods for the shipping options shortcode
		 */
		public function wc_haf_update_shipping_methods() {
			if ( isset( WC()->session ) && ! WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}
			$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
			$posted_shipping_methods = isset( $_POST['shipping_method'] ) ? wc_clean( wp_unslash( $_POST['shipping_method'] ) ) : [];
			if ( is_array( $posted_shipping_methods ) ) {
				foreach ( $posted_shipping_methods as $i => $value ) {
					$chosen_shipping_methods[ $i ] = $value;
				}
			}
			WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
			wp_send_json( '' );
		}

		/**
		 * Force cart needs shipping true so the shipping options shortcode will work even when cart is empty
		 */
		function wc_force_needs_shipping( $needs_shipping ) {
			return isset( WC()->session, WC()->cart ) && WC()->cart->is_empty() && ! is_cart() && ! is_checkout() ? true : $needs_shipping;
		}

		/**
		 * Force the display the shipping options even before address is enetered
		 */
		public function wc_disable_cost_requires_address_option() {
			return 'no';
		}

		/**
		 * Hide the hidden address fields on shipping calc
		 * @param mixed $replace
		 * @param mixed $args
		 * @return mixed
		 */
		public function wc_fix_cart_address_display( $replace, $args ) {
			if ( is_cart() ) {
				$none = 'none';
				foreach ( $args as $key => $arg ) {
					if ( $none === $arg ) {
						$replace["{{$key}}"] = '';
					}
				}
			}
			return $replace;
		}

		/**
		 * Hide the postcode field on shipping calc
		 * @param mixed $enabled
		 * @return mixed
		 */
		public function wc_hide_cart_postcode( $enabled ) {
			return 'yes' !== get_option( 'wc_haf_hide_postcode' ) ? $enabled : false;
		}

		/**
		 * Hide the city field on shipping calc
		 * @param mixed $enabled
		 * @return mixed
		 */
		public function wc_hide_cart_city( $enabled ) {
			return 'yes' !== get_option( 'wc_haf_hide_city' ) ? $enabled : false;
		}

		/**
		 * Hide the state field on shipping calc
		 * @param mixed $enabled
		 * @return mixed
		 */
		public function wc_hide_cart_state( $enabled ) {
			return 'yes' !== get_option( 'wc_haf_hide_state' ) ? $enabled : false;
		}

		/**
		 * Move hidden checkout fields below address fields
		 * @param mixed $fields
		 * @return mixed
		 */
		public function wc_sort_checkout_fields( $fields ) {
			if ( isset( $fields['billing']['billing_phone']['priority'] ) ) {
				if ( 'yes' === get_option( 'wc_haf_hide_first_name' ) ) {
					$fields['billing']['billing_first_name']['priority'] = $fields['billing']['billing_phone']['priority'] - 4;
				}
				if ( 'yes' === get_option( 'wc_haf_hide_last_name' ) ) {
					$fields['billing']['billing_last_name']['priority'] = $fields['billing']['billing_phone']['priority'] - 3;
				}
				if ( 'yes' === get_option( 'wc_haf_hide_company' ) ) {
					$fields['billing']['billing_company']['priority'] = $fields['billing']['billing_phone']['priority'] - 2;
				}
			}
			if ( 'yes' === get_option( 'wc_haf_top_state' ) && ( apply_filters( 'haf_force_state_update_totals', false ) || function_exists( 'is_plugin_active' ) && ( is_plugin_active( 'woocommerce-checkout-manager/woocommerce-checkout-manager.php' ) || is_plugin_active( 'flexible-checkout-fields/flexible-checkout-fields.php' ) ) ) ) {
				$fields['billing']['billing_state']['class'][] = 'update_totals_on_change';
			}
			return $fields;
		}

		/**
		 * Move fields which affect shipping zones above address fields
		 * @param mixed $fields
		 * @return mixed
		 */
		public function wc_sort_locale_fields( $locale ) {
			$country = WC()->countries->get_base_country();
			if ( 'yes' === get_option( 'wc_haf_top_state' ) ) {
				$locale[ $country ]['state']['priority'] = 43;
				$locale[ $country ]['state']['class'][] = 'update_totals_on_change';
			}
			if ( 'yes' !== get_option( 'wc_haf_hide_postcode' ) ) {
				$locale[ $country ]['postcode']['priority'] = 46;
			}
			return $locale;
		}


		/**
		 * Keep the previously chosen shipping method type when shipping zone is changed on checkout
		 * @param mixed $default
		 * @param mixed $rates
		 * @return mixed
		 */
		public function wc_set_default_shipping_method( $default, $rates ) {
			if ( apply_filters( 'haf_keep_previous_shipping_method_enabled', true ) && ! empty( WC()->session->get( 'chosen_shipping_methods' ) ) ) {
				$shipping_method = WC()->session->get( 'chosen_shipping_methods' )[0];
				$shipping_method_data = get_option( str_replace( ':', '_', "woocommerce_{$shipping_method}_settings" ) );
				foreach ( $rates as $rate_id => $rate ) {
					if ( 0 === strpos( $shipping_method, $rate->method_id ) ) {
						if ( isset( $shipping_method_data['title'] ) && $rate->label === $shipping_method_data['title'] ) {
							return $rate_id;
						}
						$matched_methods[] = $rate_id;
					}
				}
			}
			return ! empty( $matched_methods ) ? $matched_methods[0] : $default;
		}

		/**
		 * Remove the price from the shipping methods label
		 * @param mixed $label
		 * @return mixed
		 */
		public function wc_hide_shipping_price( $label ) {
			return apply_filters( 'haf_display_shipping_methods_price_enabled', is_cart() || is_checkout() ) ? $label : explode( ':', $label )[0];
		}

		/**
		 * Hide the fields by the configured conditions
		 */
		public function wc_disable_address() {
			if ( apply_filters( 'haf_hide_address', true ) ) {
				do_action( 'haf_before_conditions' );
				?>
				<script type="text/javascript">
				jQuery( function( $ ) {
					var nn = 'none', ad1 = '', ad2 = '', ct = '', pc = '', ph = '', fn = '', ln = '', cp = '', cm = '', sc = '<?php echo WC()->countries->get_base_country(); ?>', ss = '<?php echo WC()->countries->get_base_state(); ?>', hct = <?php echo wp_json_encode( 'yes' === get_option( 'wc_haf_hide_city', 'yes' ) ); ?>, hpc = <?php echo wp_json_encode( 'yes' === get_option( 'wc_haf_hide_postcode', 'yes' ) ); ?>, hcr = <?php echo wp_json_encode( 'yes' === get_option( 'wc_haf_hide_country' ) ); ?>, hst = <?php echo wp_json_encode( 'yes' === get_option( 'wc_haf_hide_state' ) ); ?>, hph = <?php echo wp_json_encode( 'yes' === get_option( 'wc_haf_hide_phone' ) ); ?>, hfn = <?php echo wp_json_encode( 'yes' === get_option( 'wc_haf_hide_first_name' ) ); ?>, hln = <?php echo wp_json_encode( 'yes' === get_option( 'wc_haf_hide_last_name' ) ); ?>, hcp = <?php echo wp_json_encode( 'yes' === get_option( 'wc_haf_hide_company' ) ); ?>, hcm = <?php echo wp_json_encode( 'yes' === get_option( 'wc_haf_hide_comments' ) ); ?>;
					if ( ! hpc ) {
						$( '#billing_postcode' ).blur( function() {
							if ( $( '#billing_country' ).val() === sc ) {
								$( 'body' ).trigger( 'update_checkout' );
							}
						} );
					}
					$( 'body' ).on( 'updated_checkout', function() {
						$( document.body ).trigger( 'haf_hide_fields' );
					} );
					$( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
						$( document.body ).trigger( 'haf_hide_fields' );
					} );
					$( document.body ).on( 'haf_hide_fields', function() {
						if ( ! $( '#billing_country_field' ).length || ! $( '#billing_address_1_field' ).length ) return;
						var shipping_method = '', payment_method = '', shipping_match = <?php echo wp_json_encode( ! WC()->cart->needs_shipping() ); ?>, payment_match = <?php echo wp_json_encode( ! WC()->cart->needs_payment() ); ?>;
						if ( ! shipping_match && <?php echo wp_json_encode( 1 >= count( WC()->cart->get_shipping_packages() ) ); ?> ) {
							shipping_method = $( 'select.shipping_method' ).val() || $( 'input[name^="shipping_method"][type="radio"]:checked' ).val() || $( 'input[name^="shipping_method"][type="hidden"]' ).val();
							if ( shipping_method ) {
								$( <?php echo wp_json_encode( get_option( 'wc_haf_shipping_methods' ) ); ?> ).each( function( index, element ) {
									if ( shipping_method.match( "^" + element ) ) {
										shipping_match = true;
										return;
									}
								} );
							}
						}
						if ( ! payment_match && $( 'input[name^="payment_method"]' ).length ) {
							payment_method = $( 'input[name^="payment_method"]:checked' ).val();
							payment_match = $.inArray( payment_method, <?php echo wp_json_encode( get_option( 'wc_haf_payment_methods' ) ); ?> ) > -1;
						}
						if ( shipping_match && payment_match && ! $( '#ship-to-different-address' ).find( 'input' ).is( ':checked' ) && false !== $( document.body ).triggerHandler( 'should_hide_fields', [ shipping_method, payment_method, shipping_match, payment_match ] ) ) {
							$( '.woocommerce-shipping-fields' ).hide();
							if ( hcr && sc ) {
								$( '#billing_country_field' ).hide();
								if ( $( '#billing_country' ).val() != sc ) {
									$( '#billing_country' ).val( sc ).trigger( 'change' );
								}
							}
							if ( hst && ss && $( '#billing_country' ).val() == sc ) {
								$( '#billing_state_field' ).hide();
								if ( $( '#billing_state' ).val() != ss ) {
									$( '#billing_state' ).val( ss ).trigger( 'change' );
								}
							}
							$( '#billing_address_1_field' ).hide();
							if ( $( '#billing_address_1' ).val() != nn ) {
								ad1 = $( '#billing_address_1' ).val();
								$( '#billing_address_1' ).val( nn );
							}
							$( '#billing_address_2_field' ).hide();
							if ( $( '#billing_address_2' ).val() != nn ) {
								ad2 = $( '#billing_address_2' ).val();
								$( '#billing_address_2' ).val( nn );
							}
							if ( hct ) {
								$( '#billing_city_field' ).hide();
								if ( $( '#billing_city' ).val() != nn ) {
									ct = $( '#billing_city' ).val();
									$( '#billing_city' ).val( nn );
								}
							}
							if ( hpc ) { 
								$( '#billing_postcode_field' ).hide();
								if ( $( '#billing_postcode' ).val() != nn ) {
									pc = $( '#billing_postcode' ).val();
									$( '#billing_postcode' ).val( nn );
								}
							}
							if ( hph ) {
								$( '#billing_phone_field' ).hide();
								if ( $( '#billing_phone' ).val() != nn ) {
									ph = $( '#billing_phone' ).val();
									$( '#billing_phone' ).val( nn );
								}
							}
							if ( hfn ) {
								$( '#billing_first_name_field' ).hide();
								if ( $( '#billing_first_name' ).val() != nn ) {
									fn = $( '#billing_first_name' ).val();
									$( '#billing_first_name' ).val( nn );
								}
							}
							if ( hln ) {
								$( '#billing_last_name_field' ).hide();
								if ( $( '#billing_last_name' ).val() != nn ) {
									ln = $( '#billing_last_name' ).val();
									$( '#billing_last_name' ).val( nn );
								}
							}
							if ( hcp ) { 
								$( '#billing_company_field' ).hide();
								if ( $( '#billing_company' ).val() != nn ) {
									cp = $( '#billing_company' ).val();
									$( '#billing_company' ).val( nn );
								}
							}
							if ( hcm ) { 
								$( '#order_comments_field' ).hide();
								if ( $( '#order_comments' ).val() != nn ) {
									cm = $( '#order_comments' ).val();
									$( '#order_comments' ).val( nn );
								}
							}
							$( document ).triggerHandler( 'methods_matched', [ shipping_method, payment_method ] );
						} else {
							if ( $( '#billing_address_1' ).is( ':hidden' ) ) {
								$( '#billing_country' ).trigger( 'change' );
							} else {
								if ( hcr ) {
									$( '#billing_country_field' ).show();
								}
								if ( $( '#billing_address_1' ).val() == nn ) {
									$( '#billing_address_1' ).val( ad1 );
								}
								if ( $( '#billing_address_2' ).val() == nn ) {
									$( '#billing_address_2' ).val( ad2 );
								}
								if ( $( '#billing_city' ).val() == nn ) {
									$( '#billing_city' ).val( ct );
								}
								if ( $( '#billing_postcode' ).val() == nn ) {
									$( '#billing_postcode' ).val( pc );
								}
								if ( hph ) {
									if ( $( '#billing_phone' ).val() == nn ) {
										$( '#billing_phone' ).val( ph );
									}
									$( '#billing_phone_field' ).show();
								}
								if ( hfn ) {
									if ( $( '#billing_first_name' ).val() == nn ) {
										$( '#billing_first_name' ).val( fn );
									}
									$( '#billing_first_name_field' ).show();
								}
								if ( hln ) {
									if ( $( '#billing_last_name' ).val() == nn ) {
										$( '#billing_last_name' ).val( ln );
									}
									$( '#billing_last_name_field' ).show();
								}
								if ( hcp ) {
									if ( $( '#billing_company' ).val() == nn ) {
										$( '#billing_company' ).val( cp );
									}
									$( '#billing_company_field' ).show();
								}
								if ( hcm ) {
									if ( $( '#order_comments' ).val() == nn ) {
										$( '#order_comments' ).val( cm );
									}
									$( '#order_comments_field' ).show();
								}
								if ( $( '#shipping_address_1' ).val() == nn ) {
									$( '#shipping_address_1' ).val( '' );
								}
								if ( $( '#shipping_address_2' ).val() == nn ) {
									$( '#shipping_address_2' ).val( '' );
								}
								if ( $( '#shipping_city' ).val() == nn ) {
									$( '#shipping_city' ).val( '' );
								}
								if ( $( '#shipping_postcode' ).val() == nn ) {
									$( '#shipping_postcode' ).val( '' );
								}
								$( '.woocommerce-shipping-fields' ).show();
								$( document ).triggerHandler( 'methods_not_matched', [ shipping_method, payment_method, shipping_match, payment_match ] );
							}
						}
					} );
				} );
				</script>
			<?php
			}
		}

		/**
		 * Disable the postcode validation when hidden
		 * @param mixed $valid
		 * @param mixed $postcode
		 * @param mixed $country
		 * @return mixed
		 */
		public function wc_disable_postcode_validation( $valid, $postcode, $country ) {
			return 'yes' === get_option( 'wc_haf_hide_postcode', 'yes' ) && wc_format_postcode( 'none', $country ) === $postcode ? true : $valid;
		}

		/**
		 * Disable the phone validation when hidden
		 * @param mixed $fields
		 * @param mixed $errors
		 */
		public function wc_disable_validations( $fields, $errors ) {
			if ( 'yes' === get_option( 'wc_haf_hide_phone' ) && 'none' === $fields['billing_phone'] ) {
				$errors->remove( 'billing_phone_validation' );
			}
		}

		/**
		 * Retrieve the original customer's address to be kept if address fields were hidden
		 * @param mixed $customer
		 * @param mixed $data
		 */
		public function wc_get_customer_address( $customer, $data ) {
			if ( apply_filters( 'haf_hide_address', true ) ) {
				$none = 'none';
				$user_id = $customer->get_id();
				if ( apply_filters( 'haf_keep_previous_address', true, $user_id ) && get_userdata( $user_id ) && $none === $data['billing_address_1'] ) {
					$base_country = WC()->countries->get_base_country();
					$user_billing_city = get_user_meta( $user_id, 'billing_city', true );
					$user_billing_postcode = get_user_meta( $user_id, 'billing_postcode', true );
					$user_billing_state = get_user_meta( $user_id, 'billing_state', true );
					$user_billing_country = get_user_meta( $user_id, 'billing_country', true );
					$_GET['billing_phone'] = get_user_meta( $user_id, 'billing_phone', true );
					$_GET['billing_first_name'] = get_user_meta( $user_id, 'billing_first_name', true );
					$_GET['billing_last_name'] = get_user_meta( $user_id, 'billing_last_name', true );
					if ( ( $data['billing_country'] === $user_billing_country || 'yes' === get_option( 'wc_haf_hide_country' ) ) && in_array( $data['billing_city'], [ $user_billing_city, $none ] ) && in_array( $data['billing_postcode'], [ $user_billing_postcode, wc_format_postcode( $none, $data['billing_country'] ) ] ) && ( $data['billing_state'] === $user_billing_state || 'yes' === get_option ( 'wc_haf_hide_state' ) && $data['billing_country'] === $base_country ) ) {
						$_GET['billing_address_1'] = get_user_meta( $user_id, 'billing_address_1', true );
						$_GET['billing_address_2'] = get_user_meta( $user_id, 'billing_address_2', true );
						$_GET['billing_city'] = $user_billing_city;
						$_GET['billing_postcode'] = $user_billing_postcode;
						$_GET['billing_state'] = $user_billing_state;
						$_GET['billing_country'] = $user_billing_country;
						$_GET['billing_company'] = get_user_meta( $user_id, 'billing_company', true );
					}
					$user_shipping_city = get_user_meta( $user_id, 'shipping_city', true );
					$user_shipping_postcode = get_user_meta( $user_id, 'shipping_postcode', true );
					$user_shipping_state = get_user_meta( $user_id, 'shipping_state', true );
					$user_shipping_country = get_user_meta( $user_id, 'shipping_country', true );
					if ( ( $data['shipping_country'] === $user_shipping_country || 'yes' === get_option( 'wc_haf_hide_country' ) ) && in_array( $data['shipping_city'], [ $user_shipping_city, $none ] ) && in_array( $data['shipping_postcode'], [ $user_shipping_postcode, wc_format_postcode( $none, $data['shipping_country'] ) ] ) && ( $data['shipping_state'] === $user_shipping_state || 'yes' === get_option( 'wc_haf_hide_state' ) && $data['shipping_country'] === $base_country ) ) {
						$_GET['shipping_address_1'] = get_user_meta( $user_id, 'shipping_address_1', true );
						$_GET['shipping_address_2'] = get_user_meta( $user_id, 'shipping_address_2', true );
						$_GET['shipping_city'] = $user_shipping_city;
						$_GET['shipping_postcode'] = $user_shipping_postcode;
						$_GET['shipping_state'] = $user_shipping_state;
						$_GET['shipping_country'] = $user_shipping_country;
						$_GET['shipping_first_name'] = get_user_meta( $user_id, 'shipping_first_name', true );
						$_GET['shipping_last_name'] = get_user_meta( $user_id, 'shipping_last_name', true );
						$_GET['shipping_company'] = get_user_meta( $user_id, 'shipping_company', true );
					}
				}
			}
		}

		/**
		 * Erase unnecessary order fields values and update user meta
		 * @param mixed $order
		 * @param mixed $data
		 */
		public function wc_checkout_remove_fields( $order, $data ) {
			if ( apply_filters( 'haf_hide_address', true ) ) {
				$none = 'none';
				$user_id = $order->get_user_id();
				$base_country = WC()->countries->get_base_country();
				if ( $none === $data['billing_address_1'] ) {
					$order->set_billing_address_1( '' );
					update_user_meta( $user_id, 'billing_address_1', isset( $_GET['billing_address_1'] ) ? wc_clean( $_GET['billing_address_1'] ) : '' );
					if ( $data['billing_country'] === $base_country && 'yes' === get_option( 'wc_haf_hide_state' ) ) {
						update_user_meta( $user_id, 'billing_state', isset( $_GET['billing_state'] ) ? wc_clean( $_GET['billing_state'] ) : '' );
						if ( ! apply_filters( 'haf_force_state_in_order', true ) ) {
							$order->set_billing_state( '' );
						}
					}
					if ( 'yes' === get_option( 'wc_haf_hide_country' ) ) {
						update_user_meta( $user_id, 'billing_country', isset( $_GET['billing_country'] ) ? wc_clean( $_GET['billing_country'] ) : '' );
						if ( ! apply_filters( 'haf_force_country_in_order', true ) ) {
							$order->set_billing_country( '' );
						}
					}
					do_action( 'haf_erase_order_hidden_fields_values', $order, $data );
				}
				if ( $none === $data['billing_address_2'] ) {
					$order->set_billing_address_2( '' );
					update_user_meta( $user_id, 'billing_address_2', isset( $_GET['billing_address_2'] ) ? wc_clean( $_GET['billing_address_2'] ) : '' );
				}
				if ( $none === $data['billing_city'] ) {
					$order->set_billing_city( '' );
					update_user_meta( $user_id, 'billing_city', isset( $_GET['billing_city'] ) ? wc_clean( $_GET['billing_city'] ) : '' );
				}
				if ( wc_format_postcode( $none, $data['billing_country'] ) === $data['billing_postcode'] ) {
					$order->set_billing_postcode( '' );
					update_user_meta( $user_id, 'billing_postcode', isset( $_GET['billing_postcode'] ) ? wc_clean( $_GET['billing_postcode'] ) : '' );
				}
				if ( $none === $data['billing_phone'] ) {
					$order->set_billing_phone( '' );
					update_user_meta( $user_id, 'billing_phone', isset( $_GET['billing_phone'] ) ? wc_clean( $_GET['billing_phone'] ) : '' );
				}
				if ( $none === $data['billing_first_name'] ) {
					$order->set_billing_first_name( '' );
					update_user_meta( $user_id, 'billing_first_name', isset( $_GET['billing_first_name'] ) ? wc_clean( $_GET['billing_first_name'] ) : '' );
				}
				if ( $none === $data['billing_last_name'] ) {
					$order->set_billing_last_name( '' );
					update_user_meta( $user_id, 'billing_last_name', isset( $_GET['billing_last_name'] ) ? wc_clean( $_GET['billing_last_name'] ) : '' );
				}
				if ( $none === $data['billing_company'] ) {
					$order->set_billing_company( '' );
					update_user_meta( $user_id, 'billing_company', isset( $_GET['billing_company'] ) ? wc_clean( $_GET['billing_company'] ) : '' );
				}
				if ( $none === $data['order_comments'] ) {
					$order->set_customer_note( '' );
				}
				if ( $none === $data['shipping_address_1'] ) {
					$order->set_shipping_address_1( '' );
					update_user_meta( $user_id, 'shipping_address_1', isset( $_GET['shipping_address_1'] ) ? wc_clean( $_GET['shipping_address_1'] ) : '' );
					if ( $data['shipping_country'] === $base_country && 'yes' === get_option( 'wc_haf_hide_state' ) ) {
						update_user_meta( $user_id, 'shipping_state', isset( $_GET['shipping_state'] ) ? wc_clean( $_GET['shipping_state'] ) : '' );
						if ( ! apply_filters( 'haf_force_state_in_order', true ) ) {
							$order->set_shipping_state( '' );
						}
					}
					if ( 'yes' === get_option( 'wc_haf_hide_country' ) ) {
						update_user_meta( $user_id, 'shipping_country', isset( $_GET['shipping_country'] ) ? wc_clean( $_GET['shipping_country'] ) : '' );
						if ( ! apply_filters( 'haf_force_country_in_order', true ) ) {
							$order->set_shipping_country( '' );
						}
					}
				}
				if ( $none === $data['shipping_address_2'] ) {
					$order->set_shipping_address_2( '' );
					update_user_meta( $user_id, 'shipping_address_2', isset( $_GET['shipping_address_2'] ) ? wc_clean( $_GET['shipping_address_2'] ) : '' );
				}
				if ( $none === $data['shipping_city'] ) {
					$order->set_shipping_city( '' );
					update_user_meta( $user_id, 'shipping_city', isset( $_GET['shipping_city'] ) ? wc_clean( $_GET['shipping_city'] ) : '' );
				}
				if ( wc_format_postcode( $none, $data['shipping_country'] ) === $data['shipping_postcode'] ) {
					$order->set_shipping_postcode( '' );
					update_user_meta( $user_id, 'shipping_postcode', isset( $_GET['shipping_postcode'] ) ? wc_clean( $_GET['shipping_postcode'] ) : '' );
				}
				if ( $none === $data['shipping_first_name'] ) {
					$order->set_shipping_first_name( '' );
					update_user_meta( $user_id, 'shipping_first_name', isset( $_GET['shipping_first_name'] ) ? wc_clean( $_GET['shipping_first_name'] ) : '' );
				}
				if ( $none === $data['shipping_last_name'] ) {
					$order->set_shipping_last_name( '' );
					update_user_meta( $user_id, 'shipping_last_name', isset( $_GET['shipping_last_name'] ) ? wc_clean( $_GET['shipping_last_name'] ) : '' );
				}
				if ( $none === $data['shipping_company'] ) {
					$order->set_shipping_company( '' );
					update_user_meta( $user_id, 'shipping_company', isset( $_GET['shipping_company'] ) ? wc_clean( $_GET['shipping_company'] ) : '' );
				}
			}
		}

		/**
		 * Create order review template which doesn't include the shipping options
		 * @param mixed $template
		 * @param mixed $template_name
		 * @return mixed
		 */
		public function wc_get_review_order_template( $template, $template_name ) {
			return 'checkout/review-order.php' !== $template_name ? $template : plugin_dir_path( __FILE__ ) . 'woocommerce/' . $template_name;
		}

		/**
		 * Create form field for the shipping options
		 */
		public function wc_shipping_options_form() {
			if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
				echo '<table class="form-row form-row-wide wcf-anim-label wc_shipping_methods_field">';
				do_action( 'woocommerce_review_order_before_shipping' );
				wc_cart_totals_shipping_html();
				do_action( 'woocommerce_review_order_after_shipping' );
				echo '</table>';
			}
		}

		/**
		 * Add the shipping option field on checkout
		 * @param mixed $fields
		 * @return mixed
		 */
		public function wc_shipping_options_field( $fields ) {
			$fields['billing']['billing_shipping_options'] = [
				'type'		=> 'shipping_options',
				'priority'	=> apply_filters( 'haf_shipping_options_priority', 'yes' === get_option( 'wc_haf_hide_country' ) ? $fields['billing']['billing_country']['priority'] - 4 : $fields['billing']['billing_address_1']['priority'] - 4 ),
			];
			return $fields;
		}

		/**
		 * Add the shipping option style
		 */
		public function wc_add_shipping_top_style() {
			echo
			'<style>
			.wc_shipping_methods_field label {
				display: inline;
			}
			</style>';
		}

		/**
		 * Update the shipping options on checkout_update
		 * @param mixed $fragments
		 * @return mixed
		 */
		public function wc_top_shipping_table_update( $fragments ) {
			ob_start();
			$this->wc_shipping_options_form();
			$fragments['.wc_shipping_methods_field'] = ob_get_clean();
			return $fragments;
		}

		/**
		 * Display the shipping price on the checkout order review
		 */
		public function wc_display_shipping_price() {
			$total = WC()->cart->get_cart_shipping_total();
			if ( __( 'Free!', 'woocommerce' ) !== $total ) {
			?>
				<tr>
					<th><?php _e( 'Shipping', 'woocommerce' ); ?></th>
					<td><?php echo $total; ?></td>
				</tr>
			<?php
			}
		}

		/**
		 * Create payment template which doesn't include the payment options
		 * @param mixed $template
		 * @param mixed $template_name
		 * @return mixed
		 */
		public function wc_get_payment_template( $template, $template_name ) {
			return 'checkout/payment.php' !== $template_name ? $template : plugin_dir_path( __FILE__ ) . 'woocommerce/' . $template_name;
		}

		/**
		 * Create form field for the shipping options
		 */
		public function wc_payment_options_form() {
			if ( WC()->cart->needs_payment() ) {
				echo '<div id="payment" class="form-row form-row-wide wcf-anim-label wc_payment_methods_field"><ul class="wc_payment_methods payment_methods methods"><table>';
				$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
				WC()->payment_gateways()->set_current_gateway( $available_gateways );
				if ( ! empty( $available_gateways ) ) {
					foreach ( $available_gateways as $gateway ) {
						wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
					}
				} else {
					echo '<li>';
					wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ), 'notice' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
					echo '</li>';
				}
				echo '</table></ul></div>';
			}
		}

		/**
		 * Add the payment option field on checkout
		 * @param mixed $fields
		 * @return mixed
		 */
		public function wc_payment_options_field( $fields ) {
			$fields['billing']['billing_payment_options'] = [
				'type'		=> 'payment_options',
				'priority'	=> apply_filters( 'haf_payment_options_priority', isset( $fields['billing']['billing_shipping_options']['priority'] ) ? $fields['billing']['billing_shipping_options']['priority'] + 2 : $fields['billing']['billing_address_1']['priority'] - 2 ),
			];
			return $fields;
		}

		/**
		 * Add the payment option style
		 */
		public function wc_add_payment_top_style() {
			echo
			'<style>
			#payment .place-order {
				margin-top: 0;
			}
			#order_review .shop_table {
				margin-bottom: 0;
			}
			</style>';
		}

		/**
		 * Update the payment options on checkout_update
		 * @param mixed $fragments
		 * @return mixed
		 */
		public function wc_top_payment_methods_update( $fragments ) {
			ob_start();
			$this->wc_payment_options_form();
			$fragments['.wc_payment_methods_field'] = ob_get_clean();
			return $fragments;
		}

		/**
		 * Set default country if not configured so the shipping/payment methods will be displayed by their priority
		 * @param mixed $country
		 * @return mixed
		 */
		public function wc_set_default_country( $country ) {
			return $country ?? WC()->countries->get_base_country();
		}
	}

	/**
	 * Instantiate class
	 */
	$hide_address_fields_for_woocommerce = new WC_HAF();
};
