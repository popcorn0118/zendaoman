<?php

namespace WPDM\User;

use WPDM\__\Template;

class Dashboard
{

    public $dashboard_menu;
    public $dashboard_menu_actions;

    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    function __construct()
    {
        add_action("wp", [$this, 'menuInit']);
        add_shortcode("wpdm_user_dashboard", array($this, 'dashboard'));
    }

    function menuInit()
    {
        //$this->dashboard_menu
        $user[''] = ['name' => __("Profile", "download-manager"), 'callback' => [$this, 'profile']];
        $user['download-history'] = ['name' => __("Download History", "download-manager"), 'callback' => [$this, 'downloadHistory']];
        $account['edit-profile'] = ['name' => __("Edit Profile", "download-manager"), 'callback' => [WPDM()->user->editProfile, 'editProfile']];
        $user = apply_filters("wpdm_user_dashboard_menu", $user);
        $account = apply_filters("wpdm_user_dashboard_menu_account", $account);
        $this->dashboard_menu['user'] = array(
            'title' => '',
            'items' => $user
        );
        $this->dashboard_menu['account'] = array(
            'title' => __("Account", "download-manager"),
            'items' => $account
        );

        $this->dashboard_menu = apply_filters("wpdm_dashboard_menu", $this->dashboard_menu);

        $this->dashboard_menu_actions = apply_filters("wpdm_dashboard_menu_actions", $this->dashboard_menu_actions);
    }

    function dashboard($params = array())
    {
        global $wp_query, $WPDM;

        // Enqueue dashboard styles only when shortcode is used
        wp_enqueue_style('wpdm-dashboard', WPDM_ASSET_URL . 'css/wpdm-dashboard.min.css', [], WPDM_VERSION);

        // Enqueue media scripts for profile picture upload
        if (is_user_logged_in()) {
            wp_enqueue_media();
        }

        ob_start();
        if (!is_user_logged_in()) {
            echo WPDM()->user->login->form($params);
        } else {

            if (!isset($params) || !is_array($params)) $params = array();
            $all_dashboard_menu_items = array();
            if (is_array($this->dashboard_menu)) {
                foreach ($this->dashboard_menu as $section) {
                    $all_dashboard_menu_items += $section['items'];
                }
            }
            $udb_page = isset($wp_query->query_vars['udb_page']) ? $wp_query->query_vars['udb_page'] : '';
            $udb_page_parts = explode("/", $udb_page);
            $udb_page = $udb_page_parts[0];
            $udb_page_parts = array_merge($udb_page_parts, $params);
            $dashboard_contents = "";
            if (isset($all_dashboard_menu_items[$udb_page]['callback']))
                $dashboard_contents = call_user_func($all_dashboard_menu_items[$udb_page]['callback'], $udb_page_parts);
            else if (isset($all_dashboard_menu_items[$udb_page]['shortcode']))
                $dashboard_contents = do_shortcode($all_dashboard_menu_items[$udb_page]['shortcode']);
            else if (isset($all_dashboard_menu_items[$udb_page]))
                $dashboard_contents = call_user_func($all_dashboard_menu_items[$udb_page], $udb_page_parts);
            //else if(isset($this->dashboard_menu_actions[$udb_page]['shortcode']))
            //    $dashboard_contents = do_shortcode($this->dashboard_menu_actions[$udb_page]['shortcode']);

	        $default_icons[''] = 'wpdm-user';
	        $default_icons['purchases'] = 'wpdm-shopping-cart color-success';
	        $default_icons['messages'] = 'wpdm-chat color-success';
	        $default_icons['download-history'] = 'wpdm-layer-group color-info';
	        $default_icons['edit-profile'] = 'wpdm-user-edit color-green';
	        $default_icons['subscription-plan'] = 'wpdm-crown color-info';
	        $default_icons['subscription-download-area'] = 'wpdm-circle-down color-info';
	        $default_icons['affiliate-stats'] = 'wpdm-share color-info';
	        $default_icons['affiliates'] = 'wpdm-share color-info';
	        $default_icons['file-cart'] = 'wpdm-cart-arrow-down color-info';
	        $default_icons['my-downloads'] = 'wpdm-arrow-down color-info';
	        $default_icons['account-credits'] = 'wpdm-credit-card color-success';

            $default_icons = apply_filters("wpdm_user_dashboard_icons", $default_icons);

            include Template::locate("dashboard/dashboard.php", __DIR__ . '/views');
        }
        return ob_get_clean();
    }

    function profile($params = array())
    {
        ob_start();
        include Template::locate("dashboard/profile.php", __DIR__ . '/views');
        return ob_get_clean();
    }

    function downloadHistory()
    {
        global $wpdb, $current_user;
        ob_start();
        include Template::locate("dashboard/download-history.php", __DIR__ . '/views');
        return ob_get_clean();
    }


    function logout()
    {
        wp_logout();
    }

}


