<?php namespace Premmerce\OneClickOrder\Admin;

use Premmerce\OneClickOrder\OneClickOrderPlugin;
use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\SDK\V2\Notifications\AdminNotifier;

/**
 * Class Admin
 *
 * @package Premmerce\OneClickOrder\Admin
 */
class Admin
{
    /**
     * @var FileManager
     */
    private $fileManager;

    /**
     * @var AdminNotifier
     */
    private $notifier;

    /**
     * Admin constructor.
     *
     * Register menu items and handlers
     *
     * @param FileManager $fileManager
     * @param AdminNotifier $notifier
     */
    public function __construct(FileManager $fileManager, AdminNotifier $notifier)
    {
        $this->fileManager = $fileManager;
        $this->notifier    = $notifier;

        add_action('admin_menu', array($this, 'addMenuPage'));

        add_action('admin_post_premmerce_oneclickorder_save_settings', array($this, 'saveOneClickOrderSettings'));
    }

    /**
     * Add submenu to premmerce menu page
     *
     * @return false|string
     */
    public function addMenuPage()
    {
        global $admin_page_hooks;

        $premmerceMenuExists = isset($admin_page_hooks['premmerce']);

        $svg = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="20" height="16" style="fill:#82878c" viewBox="0 0 20 16"><g id="Rectangle_7"> <path d="M17.8,4l-0.5,1C15.8,7.3,14.4,8,14,8c0,0,0,0,0,0H8h0V4.3C8,4.1,8.1,4,8.3,4H17.8 M4,0H1C0.4,0,0,0.4,0,1c0,0.6,0.4,1,1,1 h1.7C2.9,2,3,2.1,3,2.3V12c0,0.6,0.4,1,1,1c0.6,0,1-0.4,1-1V1C5,0.4,4.6,0,4,0L4,0z M18,2H7.3C6.6,2,6,2.6,6,3.3V12 c0,0.6,0.4,1,1,1c0.6,0,1-0.4,1-1v-1.7C8,10.1,8.1,10,8.3,10H14c1.1,0,3.2-1.1,5-4l0.7-1.4C20,4,20,3.2,19.5,2.6 C19.1,2.2,18.6,2,18,2L18,2z M14,11h-4c-0.6,0-1,0.4-1,1c0,0.6,0.4,1,1,1h4c0.6,0,1-0.4,1-1C15,11.4,14.6,11,14,11L14,11z M14,14 c-0.6,0-1,0.4-1,1c0,0.6,0.4,1,1,1c0.6,0,1-0.4,1-1C15,14.4,14.6,14,14,14L14,14z M4,14c-0.6,0-1,0.4-1,1c0,0.6,0.4,1,1,1 c0.6,0,1-0.4,1-1C5,14.4,4.6,14,4,14L4,14z"/></g></svg>';
        $svg = 'data:image/svg+xml;base64,' . base64_encode($svg);

        if (! $premmerceMenuExists) {
            add_menu_page(
                'Premmerce',
                'Premmerce',
                'manage_options',
                'premmerce',
                '',
                $svg
            );
        }

        $page = add_submenu_page(
            'premmerce',
            __('Buy now', 'premmerce-woocommerce-buy-now'),
            __('Buy now', 'premmerce-woocommerce-buy-now'),
            'manage_options',
            OneClickOrderPlugin::SLUG,
            array($this, 'controller')
        );

        if (! $premmerceMenuExists) {
            global $submenu;
            unset($submenu['premmerce'][0]);
        }

        return $page;
    }

    /**
     * Module controller
     */
    public function controller()
    {
        $data = get_option(OneClickOrderPlugin::SLUG, array());

        $this->fileManager->includeTemplate('admin/main.php', array('data' => $data));
    }

    /**
     * Save plugin settings
     */
    public function saveOneClickOrderSettings()
    {
        $data = $this->prepare($_POST);

        update_option(OneClickOrderPlugin::SLUG, $data);

        $this->notifier->flash(__('Settings saved.', 'premmerce-woocommerce-buy-now'));

        wp_redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Prepare data to add/edit
     *
     * @param array $data
     *
     * @return array
     */
    private function prepare($data)
    {
        $defaults = array(
            'name'             => 0,
            'email'            => 0,
            'phone'            => 0,
            'comment'          => 0,
            'displayOnCatalog' => 0,
        );

        $defaults = array_replace($defaults, $data);

        return array(
            'name'             => $defaults['name'],
            'email'            => $defaults['email'],
            'phone'            => $defaults['phone'],
            'comment'          => $defaults['comment'],
            'displayOnCatalog' => $defaults['displayOnCatalog'],
        );
    }
}
