<?php

use Premmerce\OneClickOrder\OneClickOrderPlugin;

/**
 *
 * Plugin Name:       Premmerce Buy Now for WooCommerce
 * Plugin URI:        https://premmerce.com
 * Description:       Allow customers to create an order with one click
 * Version:           1.1.4
 * Author:            premmerce
 * Author URI:        https://premmerce.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       premmerce-woocommerce-buy-now
 * Domain Path:       /languages
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 3.6
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

call_user_func( function () {

	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
	require_once plugin_dir_path( __FILE__ ) . 'freemius.php';

	$main = new OneClickOrderPlugin( __FILE__ );

	register_activation_hook( __FILE__, [ $main, 'activate' ] );

	register_deactivation_hook( __FILE__, [ $main, 'deactivate' ] );

	$main->run();
} );
