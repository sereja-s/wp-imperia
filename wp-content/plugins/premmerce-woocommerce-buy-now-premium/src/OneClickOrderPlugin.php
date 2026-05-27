<?php namespace Premmerce\OneClickOrder;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\SDK\V2\Notifications\AdminNotifier;

use Premmerce\OneClickOrder\Admin\Admin;
use Premmerce\OneClickOrder\Frontend\Frontend;

/**
 * Class OneClickOrderPlugin
 *
 * @package Premmerce\OneClickOrder
 */
class OneClickOrderPlugin
{
    const SLUG = 'premmerce-woocommerce-buy-now';

    /**
     * @var FileManager
     */
    private $fileManager;

    /**
     * @var AdminNotifier
     */
    private $notifier;

    /**
     * OneClickOrderPlugin constructor.
     *
     * @param string $mainFile
     */
    public function __construct($mainFile)
    {
        $this->fileManager = new FileManager($mainFile, 'premmerce-woocommerce-buy-now');
        $this->notifier    = new AdminNotifier();

        add_action('init', array($this, 'loadTextDomain'));
        add_action('admin_init', array($this, 'checkRequirePlugins'));
    }

    /**
     * Run plugin part
     */
    public function run()
    {
        if (is_admin()) {
            new Admin($this->fileManager, $this->notifier);
        } else {
            $frontend                                    = new Frontend($this->fileManager);
            $GLOBALS['premmerce_oneclickorder_frontend'] = $frontend;
        }
    }

    /**
     * Load plugin translations
     */
    public function loadTextDomain()
    {
        $name = $this->fileManager->getPluginName();
        load_plugin_textdomain('premmerce-woocommerce-buy-now', false, $name . '/languages/');
    }

    /**
     * Validate required plugins
     *
     * @return array
     */
    private function validateRequiredPlugins()
    {
        $plugins = array();

        if (! function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        /**
         * Check if WooCommerce is active
         **/
        if (! (is_plugin_active('woocommerce/woocommerce.php') || is_plugin_active_for_network('woocommerce/woocommerce.php'))) {
            $plugins[] = '<a target="_blank" href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a>';
        }

        return $plugins;
    }

    /**
     * Check required plugins and push notifications
     */
    public function checkRequirePlugins()
    {
        $message = __('The %s plugin requires %s plugin to be active!', self::SLUG);

        $plugins = $this->validateRequiredPlugins();

        if (count($plugins)) {
            foreach ($plugins as $plugin) {
                $error = sprintf($message, 'Premmerce WooCommerce Buy Now', $plugin);
                $this->notifier->push($error, AdminNotifier::ERROR, false);
            }
        }
    }

    /**
     * Fired when the plugin is activated
     */
    public function activate()
    {
        update_option(self::SLUG, array(
            'name'    => 0,
            'email'   => 0,
            'phone'   => 1,
            'comment' => 0,
        ));
    }

    /**
     * Fired when the plugin is deactivated
     */
    public function deactivate()
    {
        delete_option(self::SLUG);
    }
}
