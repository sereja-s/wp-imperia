<?php

/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

global $product;

if (! $product->is_purchasable()) {
	return;
}

echo wc_get_stock_html($product); // WPCS: XSS ok.

if ($product->is_in_stock()) : ?>
	<?php do_action('woocommerce_product_additional_information', $product); ?>
	<?php do_action('woocommerce_before_add_to_cart_form'); ?>
	<div class="single_variation_wrap">
		<form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
			<?php do_action('woocommerce_before_add_to_cart_button'); ?>
			<p class="<?php echo esc_attr(apply_filters('woocommerce_product_price_class', 'price')); ?>"><?php echo $product->get_price_html(); ?></p>
			<?php
			do_action('woocommerce_before_add_to_cart_quantity');

			woocommerce_quantity_input(
				array(
					'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
					'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
					'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
				)
			);

			do_action('woocommerce_after_add_to_cart_quantity');
			?>

			<button data-product_id="<?php the_id(); ?>" type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="add_to_cart_button product_type_simple single_add_to_cart_button button alt<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"><?php echo esc_html($product->single_add_to_cart_text()); ?></button>

			<?php do_action('woocommerce_after_add_to_cart_button'); ?>
		</form>
		<div class="oco-btn-wrap">
			<button class="button alt" data-oneclick-order--btn="<?php the_permalink(); ?>?wc-ajax=premmerce_click_order_popup" data-oneclick-order--product-id="<?php the_id(); ?>">Купить в 1 клик</button>
		</div>
		<?php do_action('woocommerce_after_add_to_cart_form'); ?>
	</div><span class="a2233">* Цена на товар может отличаться от указанной на сайте. Для получения актуальной информации о стоимости и наличии товара свяжитесь с нами<br>* Реальный внешний вид и технические характеристики товара могут отличаться от представленных на сайте. Уточняйте важные для вас параметры товара у наших продавцов</span>
<?php endif; ?>