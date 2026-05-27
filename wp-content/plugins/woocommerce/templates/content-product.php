<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Check if the product is a valid WooCommerce product and ensure its visibility before proceeding.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'swiper-slide', $product ); ?>>
<div class="znaks">
<?php if( get_field('new') ) { ?><div class="znak znak-new">Новинка</div><?php }?><?php if( get_field('akcia') ) { ?><div class="znak znak-alcia">Акция</div><?php }?><?php if( get_field('hit') ) { ?><div class="znak znak-hit">Хит</div><?php }?>
</div>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );?>
<div class="und"><div class="col-md-6"><?php if ($product->is_in_stock()) {?>
<p class="stok">В наличии</p>
<?php } else{ ?>
<p class="stok no-stok">Нет в наличии</p>
	<?php } ?></div><div class="col-md-6 sku-block">Артикул: <?php echo $product->sku ?></div></div>
<?php	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );?>
	<?php
$subheadingvalues1 = get_the_terms( $product->id, 'pa_kollektsiya');
if ($subheadingvalues1): ?>
<p class="kol-c-p">Коллекция:
<?php foreach ( $subheadingvalues1 as $subheadingvalue1 ):
echo $subheadingvalue1->name;
echo "</p>";
endforeach;
endif; ?>
	<?php

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>
