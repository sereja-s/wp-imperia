<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              http://codeixer.com
 * @since             1.0.0
 * @package           twist
 *
 * @wordpress-plugin
 * Plugin Name:       Product Gallery Slider for Woocommerce ( Formerly Twist )
 * Plugin URI:        https://codecanyon.net/item/x/14849108
 * Description:       Fully customizable image gallery slider for the product page.comes with vertical and horizontal gallery layouts, clicking, sliding, image navigation, fancybox 3 & many more exciting features.
 * Version:           993.1
 * Author:            Codeixer
 * Author URI:        https://codeixer.com
 * Text Domain:       wpgs-td
 * Domain Path:       /languages
 * Tested up to: 5.5.1
 * WC requires at least: 3.9
 * WC tested up to: 4.5.2
 * Requires PHP: 7.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

require __DIR__ . '/vendor/autoload.php';

define('WPGS', '3.0.1');
define('WPGS_NAME', 'Product Gallery Slider for Woocommerce');
define('WPGS_INC', plugin_dir_path(__FILE__) . 'inc/');
define('WPGS_ROOT', plugin_dir_path(__FILE__) . '');
define('WPGS_ROOT_URL', plugin_dir_url(__FILE__) . '');
define('WPGS_INC_URL', plugin_dir_url(__FILE__) . 'inc/');
define('WPGS_PLUGIN_BASE', plugin_basename(__FILE__) );


class cix_wpgs
{
    
    private $divi_builder;

    function __construct()
    {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('plugins_loaded', array($this, 'core_files'));
        add_action('plugins_loaded',array($this, 'after_woo_hooks'));
        add_action('after_setup_theme',array($this, 'remove_woo_support'),20);
        $this->divi_builder = ( self::option('check_divi_builder') == '1' ) ? 'true' : 'false';
        register_activation_hook( __FILE__, array($this, 'twist_plugin_activate') );
        // Switch for Divi Page builder conflict Issue
        if( $this->divi_builder == 'false' ){

            add_action('woocommerce_before_single_product_summary', array( $this, 'wpgs_templates') );
        }
        
        add_action('plugins_loaded', array($this, 'load_plugin_code'));
    }

    function twist_plugin_activate(){
        if(! get_option( 'twist_activation_time' ) ){
            update_option( 'twist_activation_time', current_time( 'timestamp' ) );
        }
        
    }

    function remove_woo_support() {
        
        remove_theme_support( 'wc-product-gallery-lightbox' );
        remove_theme_support( 'wc-product-gallery-slider' );

        $zoom = ( cix_wpgs::option('image_zoom') == 1 ) ? 'true' : 'false';
        if($zoom == 'true'){
           add_theme_support( 'wc-product-gallery-zoom' );
        }else{
            remove_theme_support( 'wc-product-gallery-zoom' );
			
		}
    
    }

    function after_woo_hooks() {
        
    remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 ); // Remove Default Image Gallery

    }

    static function wpgs_templates() {
    
    // Override with 'wpgs_get_template' Filter for displays
    // Custom Page
    wc_get_template( 'single-product/product-image.php' );

    }


    function core_files()
    {
    
        require WPGS_ROOT . 'core/codestar-framework/codestar-framework.php';
        require WPGS_ROOT . 'core/codeixer-core.php';
        require WPGS_INC . 'admin/admin.php';
        require WPGS_INC . 'admin/elementor-twist.php';
        
       
    }

    /**
     * Get the value of a settings field
     *
     * @param string $option settings field name
     * @param string $section the section name this field belongs to
     * @param string $default default text if it's not found
     * 
     * @return mixed
     */
    static function option( $option, $default = '' ) {

        $options = get_option('wpgs_form');

        if ( isset( $options[$option] ) ) {
            return $options[$option];
        }

        return $default;
    }


    function load_plugin_code()
    {
        require WPGS_INC . 'public/public.php';
    }



    function load_textdomain()
    {
        load_plugin_textdomain('wpgs-td', false, plugin_dir_url(__FILE__) . "/languages");
    }
}

new cix_wpgs;

/**
 *  Elite Licenser Activation Code 
 */
require WPGS_INC.'admin/TwistBase.php';

class Twist {
    public $plugin_file=__FILE__;
    public $responseObj;
    public $licenseMessage;
    public $showMessage=true;
    public $slug="twist";
    public $slug_page="codeixer-dashboard";
    function __construct() {
       
        $licenseKey=get_option("Twist_lic_Key","");
        $liceEmail=get_option( "Twist_lic_email","");
        TwistBase::addOnDelete(function(){
           delete_option("Twist_lic_Key");
        });
        if(TwistBase::CheckWPPlugin($licenseKey,$liceEmail,$this->licenseMessage,$this->responseObj,__FILE__)){
           
            add_action( 'admin_post_Twist_el_deactivate_license', [ $this, 'action_deactivate_license' ] );
            
            //Write you plugin's code here

            add_action('codeixer_license_data', [ $this, 'Activated' ]);

        }else{
            if(!empty($licenseKey) && !empty($this->licenseMessage)){
               $this->showMessage=true;
            }
            update_option("Twist_lic_Key","") || add_option("Twist_lic_Key","");
            add_action( 'admin_post_Twist_el_activate_license', [ $this, 'action_activate_license' ] );
           

            // codexier-lisence-active
            add_action('codeixer_license_form', [$this,'LicenseForm']);

        }
    }
   
   
   
    function action_activate_license(){
            check_admin_referer( 'el-license' );
            $licenseKey=!empty($_POST['el_license_key'])?$_POST['el_license_key']:"";
            $licenseEmail=!empty($_POST['el_license_email'])?$_POST['el_license_email']:"";
            update_option("Twist_lic_Key",$licenseKey) || add_option("Twist_lic_Key",$licenseKey);
            update_option("Twist_lic_email",$licenseEmail) || add_option("Twist_lic_email",$licenseEmail);
            wp_safe_redirect(admin_url( 'admin.php?page='.$this->slug_page));
        }
    function action_deactivate_license() {
        check_admin_referer( 'el-license' );
        $message="";
        if(TwistBase::RemoveLicenseKey(__FILE__,$message)){
            update_option("Twist_lic_Key","") || add_option("Twist_lic_Key","");
        }
        wp_safe_redirect(admin_url( 'admin.php?page='.$this->slug_page));
    }
    function Activated(){
        ?>
        <div class="cix-card-active">
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="Twist_el_deactivate_license"/>
                <div class="el-license-container">
                     <h3>Product Gallery Slider</h3>
                    
                       
                        <?php if ( $this->responseObj->is_valid ) : ?>
                               <div class="cix-badge b-register">
                                    <span class="dashicons dashicons-yes"></span>
                                    <?php echo esc_html__( 'Registerd', 'wpgs-td' ); ?>
                                </div>
                            
                        <?php endif; ?>
                       
                      <div class="el-license-field">
                         
                            </label>
                             <input type="text" class=" txt-disable" disabled value="<?php echo esc_attr($this->responseObj->license_key ); ?>">
                            </div>
                    
                    <div class="el-license-active-btn">
                        <?php wp_nonce_field( 'el-license' ); ?>
                        <?php submit_button('Deactivate'); ?>
                    </div>
                </div>
            </form>
            <div class="infobox">
				<div class="bluetitle">1 Purchase Code per Website</div>
				<div class="simpletext"><span><?php echo $this->responseObj->msg ;?></span><br>If you want to use <?php echo WPGS_NAME ; ?> on another domain, please <a href="#" target="_blank">purchase another license</a></div>
			</div>
        </div>
    <?php
    }

    function LicenseForm() {
        ?>
      
        <div class="cix-card-active">
            <h3>Product Gallery Slider</h3>

            <div class="cix-badge b-unregister">
                <span class="dashicons dashicons-warning"></span>
                <?php echo esc_html__( 'Unregistered', 'wpgs-td' ); ?>
            </div>
            
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="Twist_el_activate_license"/>
                <div class="el-license-container">
                    
                <?php
                
                    if(!empty($this->showMessage) && !empty($this->licenseMessage)){
                        ?>
                    <div class="notice-error">
                            <p><?php echo _e($this->licenseMessage,$this->slug); ?></p>
                        </div>
                        <?php
                    }
                    ?>
                
                    <div class="el-license-field">
                    
                        <input type="text" class=" code" name="el_license_key" size="50" placeholder="Enter Purchase Code" required="required">
                    </div>
                    <div class="el-license-field">
                    
                        <?php
                            $purchaseEmail   = get_option( "Twist_lic_email", get_bloginfo( 'admin_email' ));
                        ?>
                        <input type="text" class="code" name="el_license_email" size="50" value="<?php echo $purchaseEmail; ?>" placeholder="Email Address" required="required">
                        
                    </div>
                    <div class="el-license-active-btn">
                        <?php wp_nonce_field( 'el-license' );  ?>
                        <?php  submit_button('Register this Code'); ?>
                    </div>
                </div>
            </form>
            
        </div>
        
        <?php
    }
}

new Twist;

/* Elite Licenser Activation Code End */

