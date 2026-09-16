<?php

namespace WPDM\Admin\Menu;

class Welcome
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'Menu'));
        add_action('admin_init', array($this, 'maybeRedirect'), 1); // Priority 1 to run early
        add_action('wp_ajax_wpdm_create_dashboard_page', array($this, 'createDashboardPage'));

        // Handle immediate redirect after plugin activation (non-AJAX activations)
        add_action('activated_plugin', array($this, 'activationRedirect'), 10, 2);
    }

    /**
     * Immediate redirect after plugin activation (for non-AJAX activations)
     */
    function activationRedirect($plugin, $network_wide)
    {
        // Only for this plugin (WPDM_BASE_DIR already has trailing slash)
        if ($plugin !== plugin_basename(WPDM_BASE_DIR . 'download-manager.php')) {
            return;
        }

        // Skip for network activation
        if ($network_wide) {
            return;
        }

        // Skip for bulk activations (check both the action and the flag)
        // During bulk activation, action is 'activate-selected', activate-multi is set after redirect
        if (isset($_GET['activate-multi']) ||
            (isset($_REQUEST['action']) && $_REQUEST['action'] === 'activate-selected') ||
            (isset($_REQUEST['action2']) && $_REQUEST['action2'] === 'activate-selected') ||
            (isset($_POST['checked']) && is_array($_POST['checked']) && count($_POST['checked']) > 1)) {
            return;
        }

        // Skip for AJAX requests (will be handled by maybeRedirect on next page load)
        if (wp_doing_ajax()) {
            return;
        }

        // Skip if headers already sent
        if (headers_sent()) {
            return;
        }

        // Redirect immediately
        wp_safe_redirect(admin_url('index.php?page=wpdm-welcome'));
        exit;
    }

    /**
     * AJAX handler to create dashboard page
     */
    function createDashboardPage()
    {
        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'wpdm_create_dashboard')) {
            wp_send_json_error(__('Security check failed', 'download-manager'));
        }

        if (!current_user_can('publish_pages')) {
            wp_send_json_error(__('Permission denied', 'download-manager'));
        }

        $page_id = wp_insert_post([
            'post_title'   => __('User Dashboard', 'download-manager'),
            'post_content' => '[wpdm_user_dashboard]',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);

        if (is_wp_error($page_id)) {
            wp_send_json_error($page_id->get_error_message());
        }

        update_option('__wpdm_user_dashboard', $page_id);

        wp_send_json_success([
            'page_id'  => $page_id,
            'title'    => get_the_title($page_id),
            'edit_url' => get_edit_post_link($page_id, 'raw'),
        ]);
    }

    function Menu()
    {
        add_dashboard_page('Welcome', 'Welcome', 'read', 'wpdm-welcome', array($this, 'UI'));
    }

    function UI()
    {
        update_option('__wpdm_welcome', WPDM_VERSION, false);
        delete_transient('wpdm_activation_redirect');
        delete_option('__wpdm_activation_redirect');
        //remove_submenu_page('index.php', 'wpdm-welcome');
        include wpdm_admin_tpl_path('welcome.php', dirname(__DIR__) . '/views');
    }

    /**
     * Redirect to welcome page if activation flag is set
     * The flag is set in WordPressDownloadManager::install() via register_activation_hook
     */
    function maybeRedirect()
    {
        // Check for redirect flag (transient or option as backup)
        $redirect_transient = get_transient('wpdm_activation_redirect');
        $redirect_option = get_option('__wpdm_activation_redirect');

        if ($redirect_transient !== 'yes' && $redirect_option !== 'yes') {
            return;
        }

        // Delete the flags immediately to prevent redirect loops
        delete_transient('wpdm_activation_redirect');
        delete_option('__wpdm_activation_redirect');

        // Skip for WP-CLI
        if (defined('WP_CLI') && WP_CLI) {
            return;
        }

        // Skip for bulk activations
        if (isset($_GET['activate-multi'])) {
            return;
        }

        // Skip for AJAX requests
        if (wp_doing_ajax()) {
            return;
        }

        // Skip for cron
        if (wp_doing_cron()) {
            return;
        }

        // Skip for REST API requests
        if (defined('REST_REQUEST') && REST_REQUEST) {
            return;
        }

        // Skip if user doesn't have capability
        if (!current_user_can('manage_options')) {
            return;
        }

        // Skip if headers already sent
        if (headers_sent()) {
            return;
        }

        // Skip if already on the welcome page
        if (isset($_GET['page']) && $_GET['page'] === 'wpdm-welcome') {
            return;
        }

        // Redirect to welcome page
        wp_safe_redirect(admin_url('index.php?page=wpdm-welcome'));
        exit;
    }
}
