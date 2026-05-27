<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}?>


<h2>Вы просматривали</h2><div class="swiper mySwiper relateds"><ul class="products columns-4 swiper-wrapper">
    <?php echo do_shortcode('[recently_viewed_products]');?>
</ul><div class="swiper-pagination"></div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div></div>

<script>
var swiper = new Swiper(".relateds", {
	slidesPerView: 4,
	spaceBetween: 25,
	navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
	breakpoints: {
  // when window width is >= 320px
  320: {
    slidesPerView: 1,
    spaceBetween: 25
	
  },
  // when window width is >= 480px
  480: {
	slidesPerView: 1,
    spaceBetween: 25
  },
  // when window width is >= 640px
  640: {
	slidesPerView: 2,
    spaceBetween: 25
  },
		768: {
	slidesPerView: 2,
    spaceBetween: 25
  },
		1024: {
	slidesPerView: 3,
    spaceBetween: 25
  }
}
});
</script>