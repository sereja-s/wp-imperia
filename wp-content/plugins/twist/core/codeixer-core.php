<?php

// default codes for our plugins
if ( !class_exists( 'Codeixer_Plugin_Core' ) ) {
class Codeixer_Plugin_Core{

    public $has_unyson_plugin;

	function __construct() {
		
        add_action( 'admin_enqueue_scripts',array($this,'codeixer_admin_scripts'));
        add_action('admin_menu',array($this,'codeixer_admin_menu'));
        add_action('admin_menu',array($this,'codeixer_sub_menu'));
        add_action('admin_menu',array($this,'later'),99);


        add_action('fw_backend_add_custom_extensions_menu',array($this,'_action_theme_custom_fw_settings_menu') );
        
        add_action('admin_init',function(){
            if( is_plugin_active('unyson/unyson.php') ) {
                $this->has_unyson_plugin = true;
            }
        });
   
		
    }

    
    function _action_theme_custom_fw_settings_menu($data) {

        if(true == $this->has_unyson_plugin){
            return;
        }
        add_menu_page(
            'placeholder','placeholder',
            $data['capability'],
            $data['slug'],
            $data['content_callback']
        );
       remove_menu_page($data['slug']);
    }

    
    public function codeixer_admin_scripts(){

            wp_enqueue_style('ci-admin', plugins_url( '/assets/css/ci-admin.css', __FILE__));
        
           
    }
    public function later(){
           /* === Remove Codeixer Sub-Links === */
            remove_submenu_page( 'codeixer', 'codeixer' );
    }
    
    public function codeixer_admin_menu(){
      
        add_menu_page( 'Codeixer', 'Codeixer', 'manage_options', 'codeixer', null, 'dashicons-codeixer', 60 );
       
    }

    
    public function codeixer_license()
    {?>
    <div class="wrap">
        
           <h2>Codeixer License Activation</h2>
      
      
      <!-- <p class="about-description">Enter your Purchase key here, to activate the product, and get full feature updates and premium support.</p> -->

     
    <?php
    do_action( 'codeixer_license_form' );
    do_action( 'codeixer_license_data' );

   
    }

    public function codeixer_sub_menu(){

        

        // * == License Activation Page ==
        add_submenu_page( 'codeixer', 'Dashboard', 'Dashboard', 'manage_options', 'codeixer-dashboard', array($this,'codeixer_license') );

    }

}

    new Codeixer_Plugin_Core();

}