<?php
// THIS FILE IS NOT LINKING WITH THE PLUGIN
// IT'S FOR DEVELOPER TO KNOW MORE ABOUT THE HOOKS

/**
 * Hide Featured Image from Gallary
 * @var boolen ;
 */
add_filter( 'wpgs_show_featured_image_in_gallery', '__return_false', 20 );

/**
 * @var string
 * Filter : gallery_slider_lightbox_image_size
 */

/**
 * @var boolen
 * Filter : gallery_slider_lightbox_image_size
 */
add_filter( 'wpgs_carousel_mode', '__return_false', 20 );

// Themify Ultra conflict
// Make sure the 'default woocommerce gallery' option seleted from theme settings

// add class into main wrapper
add_filter('wpgs_wrapper_add_classes','wpgs_no_gallery_class' , 20 ,2);

function wpgs_no_gallery_class($class , $attachment_ids){
    
    return ( empty($attachment_ids) ) ? ' wpgs-no-gallery-images' : ' wpgs-has-gallery-images' ;

}