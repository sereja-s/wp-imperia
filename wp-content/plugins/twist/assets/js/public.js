(function( $ ) {
  'use strict';

$( document ).ready(function() {

  // Check if have Fancybox
  if (typeof $.fn.fancybox == 'function') {
    // Customize icons
    $.fancybox.defaults = $.extend(true, {}, $.fancybox.defaults, {
      btnTpl: {

        // Arrows
        arrowLeft: '<button data-fancybox-prev class="fancybox-button fancybox-button--arrow fancybox-button--arrow_left" title="{{PREV}}">' +
          '<span class="arrow-prev"></span>' +
          "</button>",

        arrowRight: '<button data-fancybox-next class="fancybox-button fancybox-button--arrow fancybox-button--arrow_right" title="{{NEXT}}">' +
          '<span class="arrow-next"></span>' +
          "</button>",


      },
      thumbs: {
        autoStart: false, // Display thumbnails on opening
        hideOnClose: true, // Hide thumbnail grid when closing animation starts
        parentEl: ".fancybox-container", // Container is injected into this element
        axis: "y" // Vertical (y) or horizontal (x) scrolling
      },
    });


    var selector = '.wpgs-wrapper .slick-slide:not(.slick-cloned) a';

    // Skip cloned elements
    $().fancybox({
      selector: selector,
      backFocus: false
    });

    // Attach custom click event on cloned elements, 
    // trigger click event on corresponding link
    $(document).on('click', '.wpgs-wrapper .slick-cloned a', function (e) {
      $(selector)
        .eq(($(e.currentTarget).attr("data-slick-index") || 0) % $(selector).length)
        .trigger("click.fb-start", {
          $trigger: $(this)
        });

      return false;
    });
   
  } 

  // Variation Data
 
  var get_thumb_first = $(document).find('.gallery_thumbnail_first');
  var get_main_first = $(document).find('.woocommerce-product-gallery__image');

  jQuery('.variations_form').each(function () {
    jQuery(this).on('show_variation', function (event, variation) {
      var thumb_src = variation.image.gallery_thumbnail_src,
        lightbox_image = get_main_first.find('img').attr("data-large_image"),
        first_thumb_src = get_main_first.find('img').attr("data-thumb_image");
      get_thumb_first.find('img').attr('src', thumb_src);
      get_main_first.find('img').removeAttr('srcset');
      get_main_first.find('a.woocommerce-product-gallery__lightbox').attr('href', lightbox_image);
      
      
    });
  });

  $('.thumbnail_image').each(function (index) {
    $(this).on('click', function () {
      $('.thumbnail_image').removeClass('slick-current');
      $(this).addClass('slick-current');
      $('.woocommerce-product-gallery__lightbox').css({ "display": "none" });
      setTimeout(function () {
        $('.slick-current .woocommerce-product-gallery__lightbox').css({ "display": "block", "opacity": "1" });
        $('.woocommerce-product-gallery__image .woocommerce-product-gallery__lightbox').css({ "display": "block", "opacity": "1" });
      }, 400);

    });
  });	
 
  // Reset Slider location to '0' when variation change
  $('.woocommerce-product-gallery__image .img-attr').load(function () {
    $('.wpgs-image').slick('slickGoTo', 0); 

  });
  // Remove SRCSET for Thumbanils
  $('.wpgs-thumb img').each(function () {
    $(this).removeAttr('srcset','data-thumb_image');
    $(this).removeAttr('data-thumb_image');
    $(this).removeAttr('sizes');
    $(this).removeAttr('class');
    $(this).removeAttr('data-large_image');
  });

  function ZoomIconApperce(){
    setTimeout(function () {
      $('.slick-current .woocommerce-product-gallery__lightbox').css({ "display": "block", "opacity": "1" });
      $('.woocommerce-product-gallery__image .woocommerce-product-gallery__lightbox').css({ "display": "block", "opacity": "1" });
    }, 400);
  }

  // On swipe event
  $('.wpgs-image').on('swipe', function (event, slick, direction) {
    $('.woocommerce-product-gallery__lightbox').css({ "display": "none" });
    ZoomIconApperce();
  });
  // On edge hit
  $('.wpgs-image').on('afterChange', function (event, slick, direction) {
    ZoomIconApperce();
  });
  $('.wpgs-image').on('click', '.slick-arrow ,.slick-dots', function () {
    $('.woocommerce-product-gallery__lightbox').css({ "display": "none"});
    ZoomIconApperce();
  });

 
  
 

});


})(jQuery);
 
// Other code using $ as an alias to the other library