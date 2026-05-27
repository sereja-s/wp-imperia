<?php
// Check that the class exists before trying to use it

if ( class_exists( 'CSF' ) && class_exists( 'Codeixer_Plugin_Core' ) ) {

    //
    // Set a unique slug-like ID
    $prefix = 'wpgs_form';

    CSF::createOptions( $prefix, array(
        'menu_title' => 'Product Gallery',
        'menu_slug' => 'cix-gallery-settings',
        'menu_type' => 'submenu',
        'menu_parent' => 'codeixer',
        'theme'          => 'dark',
        'framework_title'  => 'Product Gallery Slider for WooCommerce <small>by Codeixer</small>',
        'show_footer'             => false,
        'show_bar_menu'             => false,
        'save_defaults'           => true,
        'footer_credit'           => ' ',
        
    
    ) );

    //
    // Create a section
    CSF::createSection( $prefix, array(
        'title' => 'General Options',
        'icon'   => 'fas fa-sliders-h',
        'fields' => array(

            array(
            'id'         => 'slider_animation',
            'type'       => 'radio',
            'title'      => 'Slider Animation',
            'inline'      => true,
            'desc'         => 'Effect Between Product Images',
            'options'    => array(
                'false' => __('Slide', 'wpgs-td'),
                'true' => __('Fade', 'wpgs-td'),
                
            ),
            'default'    => 'false'
            ),
            array(
                'id'          => 'slider_lazy_laod',
                'type'        => 'select',
                'title'       => __('Slider Lazy Load', 'wpgs-td'),
                'placeholder' => 'Select an option',
                'options'     => array(
                    'disable' => __('Disable', 'wpgs-td'),
                    'ondemand' => __('On Demand', 'wpgs-td'),
                    'progressive' => __('Progressive', 'wpgs-td'),
                ),
                'default'     => 'disable',
                'desc'  => __('Useful for Page Loading Speed', 'wpgs-td'),
            ),
            array(
                'id'    => 'slider_infinity',
                'type'  => 'switcher',
                'title' => __('Slide Infinitely', 'wpgs-td'),
                'desc'  => __('Sliding Infinite Loop', 'wpgs-td'),
            ),
            array(
                'id'    => 'slider_adaptiveHeight',
                'type'  => 'switcher',
                'title' => __('Slide Adaptive Height', 'wpgs-td'),
                'desc'  => __('Resize the Gallery Section Height to Match the Image Height', 'wpgs-td'),
            ),
            array(
                'id'    => 'slider_rtl',
                'type'  => 'switcher',
                'title' => __('RTL Mode', 'wpgs-td'),
                'desc'  => __('Slider RTL Mode ', 'wpgs-td'),
            ),
            array(
            'id'    => 'slider_dragging',
            'type'  => 'switcher',
            'title' => __('Mouse Dragging', 'wpgs-td'),
            'desc'  => __('Move Slide on Mouse Dragging ', 'wpgs-td'),
            ),
            array(
            'id'    => 'slider_autoplay',
            'type'  => 'switcher',
            'title' => __('Slider Autoplay', 'wpgs-td'),
            
            ),
            array(
            'id'    => 'slider_autoplay_pause',
            'type'  => 'switcher',
            'title' => __('Pause Autoplay', 'wpgs-td'),
            'desc' => __('Pause Autoplay when the Mouse Hovers Over the Product Image or Dots.', 'wpgs-td'),
            'dependency' => array( 'slider_autoplay', '==', 'true' ),
            'default' => true,
            ),
            array(
                
            'id'    => 'autoplay_timeout',
            'type'  => 'slider',
            'title' => 'Autoplay Speed',
            'min'     => 1000,
            'max'     => 10000,
            'step'    => 1000,
            'unit'    => 'ms',
            'default' => 4000,
            'desc'  => __('1000 ms = 1 second', 'wpgs-td'),
            
            'dependency' => array( 'slider_autoplay', '==', 'true' )
            ),
            array(
            'id'    => 'dots',
            'type'  => 'switcher',
            'title' => __('Dots', 'wpgs-td'),
            'desc'  => __('Enable Dots/Bullets for Product Image', 'wpgs-td'),
            ),
            array(
            'id'    => 'slider_nav',
            'type'  => 'switcher',
            'title' => __('Navigation Arrows', 'wpgs-td'),
            'desc'  => __('Enable Navigation Arrows for Product Image Slider', 'wpgs-td'),
            'default' => true,
            ),
            array(
                'id'        => 'slider_icon',
                'type'      => 'image_select',
                'title'     => 'Navigation Arrows',
                'dependency' => array( 'slider_nav', '==', 'true' ),
                'class' => 'lightbox-icon-pixker',
                'desc'  => __('Select Icon for Slider Navigation Arrows', 'wpgs-td'),
                'options'   => array(
                    'icon-right-bold' => WPGS_ROOT_URL .'/assets/img/icon-right-bold.png',
                    'icon-right-dir' => WPGS_ROOT_URL .'/assets/img/icon-right-dir.png',
                    'icon-right-open-big' => WPGS_ROOT_URL .'/assets/img/icon-right-open-big.png',                  
                    'icon-right' => WPGS_ROOT_URL .'/assets/img/icon-right.png',
                ),
                'default'   => 'icon-right'
            ),
            array(
                'id'    => 'slider_nav_animation',
                'type'  => 'switcher',
                'title' => __('Arrows Animation', 'wpgs-td'),
                'desc'  => __('Enable Animation Slide effect for Appearing Arrows', 'wpgs-td'),
                'default' => true,
                'dependency' => array( 'slider_nav', '==', 'true' ),
            ),
            array(
                'id'      => 'slider_nav_color',
                'type'    => 'color',
                'title'   => __('Arrows Color', 'wpgs-td'),
                'desc'  => __('Set Arrows Color', 'wpgs-td'),
                'default' => '#fff',
                'dependency' => array( 'slider_nav', '==', 'true' ),
            ),
            array(
                'id'      => 'slider_nav_bg',
                'type'    => 'color',
                'title'   => __('Arrows Background', 'wpgs-td'),
                'desc'  => __('Set Arrows Background Color', 'wpgs-td'),
                'default' => '#000000',
                'dependency' => array( 'slider_nav', '==', 'true' ),
            ),
            
            

        ),
    ) );

    //
    // Create a section
    CSF::createSection( $prefix, array(
        'title' => 'Lightbox Options',
        'icon'   => 'fas fa-expand',
        'fields' => array(

            array(
                'id'    => 'lightbox_picker',
                'type'  => 'switcher',
                'default' => true,
                'desc' => esc_html__('Lightbox Feature on Product Image ', 'wpgs-td'),
                'title' => __('Image Lightbox', 'wpgs-td'),
            ),
            array(
                'id'    => 'thumbnails_lightbox',
                'type'  => 'switcher',
                'title' => __('LightBox For Thumbnails', 'wpgs-td'),
                'desc'  => __('Open Lightbox When click Thumbnails', 'wpgs-td'),
                'dependency' => array( 'lightbox_picker', '==', 'true' ),
            ),
            array(
                'id'          => 'lightbox_oc_effect',
                'type'        => 'select',
                'title'       => __('Lightbox Animation', 'wpgs-td'),
                'desc'  => __('Select Lightbox Open/close Animation Effect', 'wpgs-td'),
                'placeholder' => 'Select an option',
                'dependency' => array( 'lightbox_picker', '==', 'true' ),
                'options'     => array(
                    'fade' => __('Fade', 'wpgs-td'),
                    'slide' => __('Slide', 'wpgs-td'),                                        
                    'rotate' => __('Rotate', 'wpgs-td'),                                        
                    'circular' => __('Circular', 'wpgs-td'),                                        
                    'tube' => __('Tube', 'wpgs-td'),                                        
                    'zoom-in-out' => __('Zoom In Out', 'wpgs-td'),                                        
                    '' => __('None', 'wpgs-td'), 
                ),
                'default'     => 'fade'
            ),
            array(
                'id'          => 'lightbox_slide_effect',
                'type'        => 'select',
                'title'       => __('Slide Animation', 'wpgs-td'),
                'desc'  => __('Select Lightbox Slide Animation Effect', 'wpgs-td'),
                'placeholder' => 'Select an option',
                'dependency' => array( 'lightbox_picker', '==', 'true' ),
                'options'     => array(
                    'fade' => __('Fade', 'wpgs-td'),
                    'slide' => __('Slide', 'wpgs-td'),                                        
                    'rotate' => __('Rotate', 'wpgs-td'),                                        
                    'circular' => __('Circular', 'wpgs-td'),                                        
                    'tube' => __('Tube', 'wpgs-td'),                                        
                    'zoom-in-out' => __('Zoom In Out', 'wpgs-td'),                                        
                    '' => __('None', 'wpgs-td'), 
                ),
                'default'     => 'fade'
            ),
            array(
                'id'      => 'lightbox_bg',
                'type'    => 'color',
                'title'   => __('Lightbox Background', 'wpgs-td'),
                'desc'  => __('Set Lightbox Background Color', 'wpgs-td'),
                'default' => 'rgba(10,0,0,0.75)',
                'dependency' => array( 'lightbox_picker', '==', 'true' ),
            ),
            array(
                'id'      => 'lightbox_txt_color',
                'type'    => 'color',
                'title'   => __('Lightbox Text Color', 'wpgs-td'),
                'desc'  => __('Set Lightbox Text Color', 'wpgs-td'),
                'default' => '#fff',
                'dependency' => array( 'lightbox_picker', '==', 'true' ),
            ),
            array(
                'id'    => 'lightbox_img_count',
                'type'  => 'switcher',
                'default' => true,
                'title' => __('Display image count', 'wpgs-td'),
                'desc'  => __('Display image count on top corner.', 'wpgs-td'),
                'dependency' => array( 'lightbox_picker', '==', 'true' ),
            ),
            array(
                'id'    => 'lightbox_alt_text',
                'type'  => 'switcher',
                'default' => true,
                'title' => __('Image Caption', 'wpgs-td'),
                'desc'  => __('Display Image Caption / Title Text Under the Image.', 'wpgs-td'),
                'dependency' => array( 'lightbox_picker', '==', 'true' ),
            ),
            array(
                'id'      => 'lightbox_txt_color',
                'type'    => 'color',
                'title'   => __('Lightbox Text Color', 'wpgs-td'),
                'desc'  => __('Set Lightbox Text Color', 'wpgs-td'),
                'default' => '#fff',
                'dependency' => array( 'lightbox_picker', '==', 'true' ),
            ),
            array(
                'id'        => 'lightbox_icon',
                'type'      => 'image_select',
                'title'     => 'LightBox Icon',
                'class' => 'lightbox-icon-pixker',
                'desc'  => __('Select icon for lightbox Button.', 'wpgs-td'),
                'options'   => array(
                    'icon-picture' => WPGS_ROOT_URL .'/assets/img/pic.png',
                    'icon-resize-full' => WPGS_ROOT_URL .'/assets/img/resize.png',
                    'icon-resize-full-alt' => WPGS_ROOT_URL .'/assets/img/resize-2.png',
                    'icon-zoom-in' => WPGS_ROOT_URL .'/assets/img/zoom-glass.png',
                    'none' => WPGS_ROOT_URL .'/assets/img/none.png',
                ),
                'default'   => 'icon-picture',
                'dependency' => array( 'lightbox_picker', '==', 'true' ),
            ),
            array(
                'id'      => 'lightbox_icon_color',
                'type'    => 'color',
                'title'   => __('Icon Color', 'wpgs-td'),
                'desc'  => __('Set lightbox icon color', 'wpgs-td'),
                'default' => '#fff',
                'dependency' => array( 'lightbox_icon|lightbox_picker', '!=|==', 'none|true' ),
            ),
            array(
                'id'      => 'lightbox_icon_bg_color',
                'type'    => 'color',
                'title'   => __('Icon Background', 'wpgs-td'),
                'desc'  => __('Set icon background color', 'wpgs-td'),
                'default' => '#000',
                'dependency' => array( 'lightbox_icon|lightbox_picker', '!=|==', 'none|true' ),
            ),

        ),
    ) );
    // Create a section
    CSF::createSection( $prefix, array(
        'title' => 'Zoom Options',
        'icon' => 'fas fa-search-plus',
        'fields' => array(

            // A textarea field
            array(
                'id'    => 'image_zoom',
                'type'  => 'switcher',
                'default' => true,
                'title' => __('Zoom', 'wpgs-td'),
                'desc'  => __('Enable Zoom Feature for Product Image.', 'wpgs-td'),
                
            ),

        ),
    ) );
    // Create a top-tab
    CSF::createSection( $prefix, array(
        'id'    => 'thumbnail_tab', // Set a unique slug-like ID
        'title' => 'Thumbnails Options',
        'icon' => 'fas fa-image',
    ) );
    // Create a section
    CSF::createSection( $prefix, array(
        'parent' => 'thumbnail_tab', // The slug id of the parent section
        'title'  => 'Desktop',
        'fields' => array(

            array(
                'id'    => 'thumbnails',
                'type'  => 'switcher',
                'default' => true,
                'title' => __('Thumbnails', 'wpgs-td'),
                'desc'  => __('Show Thumbnails on Product Page.', 'wpgs-td'),
                
            ),
            array(
                'id'          => 'thumb_position',
                'type'        => 'select',
                'title'       => __('Thumbnails Position', 'wpgs-td'),
                'placeholder' => 'Select an option',
                'options'     => array(
                    'bottom' => __('Bottom', 'wpgs-td'),
                    'left' => __('Left', 'wpgs-td'),
                    'right' => __('Right', 'wpgs-td'), 
                ),
                'default'     => 'bottom',
                'desc'  => __('Select Thumbnails Position.', 'wpgs-td'),
                'dependency' => array( 'thumbnails', '==', 'true' ),
            ),
            array(
                'id'    => 'thumb_to_show',
                'type'  => 'number',
                'title' => __('Thumbnails To Show', 'wpgs-td'),
                'desc' => __('Set the Number of Thumbnails to Display', 'wpgs-td'),
                'default'     => 4,
                'dependency' => array( 'thumbnails', '==', 'true' ),
            ),
            array(
                'id'    => 'thumb_scroll_by',
                'type'  => 'number',
                'title' => __('Thumbnails Scroll By', 'wpgs-td'),
                'desc' => __('Set the Number of Thumbnails to Scroll when an Arrow is Clicked.', 'wpgs-td'),
                'default'     => 1,
                'dependency' => array( 'thumbnails', '==', 'true' ),
            ),
            array(
                'id'    => 'thumb_padding',
                'type'  => 'number',
                'title' => __('Thumbnails Margin', 'wpgs-td'),
                'desc' => __('Set the Padding Between Thumbnails.', 'wpgs-td'),
                'default'     => 3,
                'unit'     => 'px',
                'dependency' => array( 'thumbnails', '==', 'true' ),
            ),
            array(
                'id'    => 'thumb_nav',
                'type'  => 'switcher',
                'default' => true,
                'title' => __('Thumbnails Arrows', 'wpgs-td'),
                'dependency' => array( 'thumbnails', '==', 'true' ),
                'desc'  => __('Show Navigation Arrows for thumbnails.', 'wpgs-td'),
                
            ),
            array(
                'id'        => 'thumbnails_layout',
                'type'      => 'image_select',
                'title'     => 'Thumbnails Layout',
                'class' => 'image_picker_image',
                'options'   => array(
                    'opacity' => WPGS_ROOT_URL. '/assets/img/opcity.png',
                    
                    'border' => WPGS_ROOT_URL. '/assets/img/border.png',
                   
                ),
                'default'   => 'opacity',
                'dependency' => array( 'thumbnails', '==', 'true' ),
            ),
            array(
                'id'      => 'thumb_non_active_color',
                'type'    => 'color',
                'title'   => __('Non-active Thumbnail Color', 'wpgs-td'),
                'desc'  => __('Set Non-active Thumbnail Color Overlay', 'wpgs-td'),
                'default' => 'rgba(255,255,255,0.54)',
                'dependency' => array( 'thumbnails|thumbnails_layout', '==|==', 'true|opacity' ),
            ),
            array(
                'id'      => 'thumb_border_non_active_color',
                'type'    => 'color',
                'title'   => __('Non-Active Thumbnail Border', 'wpgs-td'),
                'desc'  => __('Set Non-Active Thumbnail Border', 'wpgs-td'),
                'default' => '#fff',
                'dependency' => array( 'thumbnails|thumbnails_layout', '==|==', 'true|border' ),
            ),
            array(
                'id'      => 'thumb_border_active_color',
                'type'    => 'color',
                'title'   => __('Active Thumbnail Border', 'wpgs-td'),
                'desc'  => __('Set Active Thumbnails Border', 'wpgs-td'),
                'default' => '#000',
                'dependency' => array( 'thumbnails|thumbnails_layout', '==|==', 'true|border' ),
            ),

        ),
    ) );
    CSF::createSection( $prefix, array(
        'parent' => 'thumbnail_tab', // The slug id of the parent section
        'title'  => 'Tablet',
        'fields' => array(
            array(
            'type'    => 'heading',
            'content' => 'Tablet : Screen width from 768px to 1024px',
            ),
            array(
                'id'          => 'thumbnails_tabs_thumb_position',
                'type'        => 'select',
                'title'       => __('Thumbnails Position', 'wpgs-td'),
                'placeholder' => 'Select an option',
                'options'     => array(
                    'bottom' => __('Bottom', 'wpgs-td'),
                    'left' => __('Left', 'wpgs-td'),
                    'right' => __('Right', 'wpgs-td'), 
                ),
                'default'     => 'bottom',
                'desc'  => __('Select Thumbnails Position.', 'wpgs-td'),
                
            ),
            array(
                'id'    => 'thumbnails_tabs_thumb_to_show',
                'type'  => 'number',
                'title' => __('Thumbnails To Show', 'wpgs-td'),
                'desc' => __('Set the Number of Thumbnails to Display', 'wpgs-td'),
                'default'     => 4,
                
            ),
            array(
                'id'    => 'thumbnails_tabs_thumb_scroll_by',
                'type'  => 'number',
                'title' => __('Thumbnails Scroll By', 'wpgs-td'),
                'desc' => __('Set the Number of Thumbnails to Scroll when an Arrow is Clicked.', 'wpgs-td'),
                'default'     => 1,
                
            ),
            array(
                'id'    => 'thumbnails_tabs_thumb_nav',
                'type'  => 'switcher',
                'default' => true,
                'title' => __('Thumbnails Arrows', 'wpgs-td'),
                'desc'  => __('Show Navigation Arrows for thumbnails.', 'wpgs-td'),
                
                
            ),
            

        ),
    ) );
    CSF::createSection( $prefix, array(
        'parent' => 'thumbnail_tab', // The slug id of the parent section
        'title'  => 'Smartphone',
        'fields' => array(
            array(
            'type'    => 'heading',
            'content' => 'SmartPhones : Screen width less than  768px',
            ),
            array(
                'id'          => 'thumbnails_mobile_thumb_position',
                'type'        => 'select',
                'title'       => __('Thumbnails Position', 'wpgs-td'),
                'placeholder' => 'Select an option',
                'options'     => array(
                    'bottom' => __('Bottom', 'wpgs-td'),
                    'left' => __('Left', 'wpgs-td'),
                    'right' => __('Right', 'wpgs-td'), 
                ),
                'default'     => 'bottom',
                'desc'  => __('Select Thumbnails Position.', 'wpgs-td'),
                
            ),
            array(
                'id'    => 'thumbnails_mobile_thumb_to_show',
                'type'  => 'number',
                'title' => __('Thumbnails To Show', 'wpgs-td'),
                'desc' => __('Set the Number of Thumbnails to Display', 'wpgs-td'),
                'default'     => 4,
                
            ),
            array(
                'id'    => 'thumbnails_mobile_thumb_scroll_by',
                'type'  => 'number',
                'title' => __('Thumbnails Scroll By', 'wpgs-td'),
                'desc' => __('Set the Number of Thumbnails to Scroll when an Arrow is Clicked.', 'wpgs-td'),
                'default'     => 1,
                
            ),
            array(
                'id'    => 'thumbnails_mobile_thumb_nav',
                'type'  => 'switcher',
                'default' => true,
                'title' => __('Thumbnails Arrows', 'wpgs-td'),
                'desc'  => __('Show Navigation Arrows for thumbnails.', 'wpgs-td'),
                
                
            ),
            

        ),
    ) );
    // Create a section
    CSF::createSection( $prefix, array(
        'title' => 'Advanced Options',
        'icon'   => 'fas fa-cog',
        'fields' => array(
            array(
                'id'    => 'check_divi_builder',
                'type'  => 'switcher',
                'title' => 'Divi Page Builder',
                'desc' => 'Enable this option if Divi Page Builder was used to create a custom product page .'
            ),
 
            array(
                'id'     => 'adv_single_image',
                'type'   => 'fieldset',
                'class'  => 'no-border-csf',
                'title'  => null,
                'default'        => array(
                    'main_image_width'     => wpgs_single_image_width(),
                    'main_image_height'    => 0,
                    'main_image_crop' => '',
                ),
                'fields' => array(
                    array(
                        'id'    => 'main_image_width',
                        'type'  => 'number',
                        'title' => __('Single Image Width', 'wpgs-td'),
                        'desc' => __('Default: \'woocommerce_single\' Image size. ', 'wpgs-td'),
                        'unit' => 'px',
                        'default'     => wpgs_single_image_width(),
                        
                    ),
                    array(
                        'id'    => 'main_image_height',
                        'type'  => 'number',
                        'title' => __('Single Image Height', 'wpgs-td'),
                        'desc' => __('Default: \'0\' for proportionally resized to fit inside dimensions. ', 'wpgs-td'),
                        'unit' => 'px',
                        'default'     => 0,
                        
                    ),
                    
                    array(
                        'id'    => 'main_image_crop',
                        'type'  => 'switcher',
                        'title' => 'Single Image Crop',
                        
                    ),
                    
                ),
            ),
            array(
                'id'     => 'adv_thumbs_image',
                'type'   => 'fieldset',
                'class'  => 'no-border-csf',
                'title'  => null,
                'default'        => array(
                    'i_width'     => 100,
                    'i_height'    => 100,
                    'i_crop' => '',
                ),
                'fields' => array(
                    array(
                        'id'    => 'i_width',
                        'type'  => 'number',
                        'title' => __('Thumbnail Image Width', 'wpgs-td'),
                        
                        'unit' => 'px',
                        'default'     => 100,
                        
                    ),
                    array(
                        'id'    => 'i_height',
                        'type'  => 'number',
                        'title' => __('Thumbnail Image Height', 'wpgs-td'),
                        'unit' => 'px',
                        'default'     => 100,
                        
                    ),
                    
                    array(
                        'id'    => 'i_crop',
                        'type'  => 'switcher',
                        'title' => 'Thumbnail Image Crop',
                        
                    ),
                    
                ),
            ),
             array(
                'id'       => 'custom_css',
                'type'     => 'code_editor',
                'title'    => 'Custom CSS',
                'desc'    => 'Add your custom CSS here',
                'settings' => array(
                    'theme'  => 'mbo',
                    'mode'   => 'css',
                ),
              
                'sanitize' => false,
            ),
  
        ),
    ) );

    CSF::createSection( $prefix, array(
    'title'  => 'Backup Settings',
    'icon'   => 'fas fa-sync',
    'fields' => array(

        array(
            'type' => 'backup',
        ),

    )
  ) );
   

}
