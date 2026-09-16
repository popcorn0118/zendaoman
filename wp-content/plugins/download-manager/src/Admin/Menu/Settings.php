<?php

namespace WPDM\Admin\Menu;

use WPDM\__\__;
use WPDM\__\CronJob;
use WPDM\__\Installer;
use WPDM\__\Session;
use WPDM\__\Jobs\ActivityReportJob;

define('WPDMSET_NONCE_KEY', 'xV)Op=Oa<y{Z>~jJ{Y#;(kRz<61x&[Rf$R76?[`6kyGvVa}*/.S#%1{[*>tJw2rp');

class Settings
{

    function __construct()
    {
        //add_action('admin_init', array($this, 'checkSaveSettingsAuth'), 1);
        add_action('admin_init', array($this, 'initiateSettings'));
        add_action('wp_ajax_wpdm_settings', array($this, 'loadSettingsPage'));
        add_action('admin_menu', array($this, 'Menu'), 999999);

	    add_action('wp_ajax_wpdm_delete_cron', array($this, 'deleteCron'));
	    add_action('wp_ajax_wpdm_test_recaptcha', array($this, 'testRecaptcha'));
        add_action('wp_ajax_wpdm_send_test_activity_report', array($this, 'sendTestActivityReport'));
        add_action('wp_ajax_wpdm_reschedule_activity_report', array($this, 'rescheduleActivityReport'));
    }

    function Menu(){
        add_submenu_page('edit.php?post_type=wpdmpro', __( "Settings &lsaquo; Download Manager" , "download-manager" ), __( "Settings" , "download-manager" ), WPDM_ADMIN_CAP, 'wpdm-settings', array($this, 'UI'));
    }

    function checkSaveSettingsAuth(){
        if(wpdm_query_var('task') === 'wdm_save_settings') {
            check_ajax_referer(WPDMSET_NONCE_KEY, '__wpdms_nonce');
            if(!wp_verify_nonce($_POST['__wpdms_nonce'], WPDMSET_NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));
            if(!current_user_can('manage_options')) die(__( "You are not allowed to change settings!", "download-manager" ));
        }
    }

    function loadSettingsPage()
    {
        global $stabs;

        $this->checkSaveSettingsAuth();

        if (current_user_can(WPDM_MENU_ACCESS_CAP)) {
            $section = wpdm_query_var('section');
            if(isset($stabs[$section], $stabs[$section]['callback']))
                call_user_func($stabs[$section]['callback']);
            else "<div class='panel panel-danger'><div class='panel-body color-red'><i class='fa fa-exclamation-triangle'></i> ".__( "Something is wrong!", "download-manager" )."</div></div>";
        }
        die();
    }

    function UI(){
        include wpdm_admin_tpl_path("settings.php");
    }

    /**
     * @param $tabid
     * @param $tabtitle
     * @param $callback
     * @param string $icon
     * @return array
     */
    public static function createMenu($tabid, $tabtitle, $callback, $icon = 'fa fa-cog')
    {
        return array('id' => $tabid, 'icon'=>$icon, 'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=' . $tabid, 'title' => $tabtitle, 'callback' => $callback);
    }


    /**
     * @usage Initiate Settings Tabs
     */
    function initiateSettings()
    {
        global $stabs;
        $tabs = array();
        $tabs['basic'] = array('id' => 'basic','icon'=>'fa fa-cog', 'link' => 'edit.php?post_type=wpdmpro&page=wpdm-settings', 'title' => 'Basic', 'callback' => array($this, 'basic'));
        $tabs['wpdmui'] = array('id' => 'wpdmui','icon'=>'fas fa-fill-drip', 'link' => 'edit.php?post_type=wpdmpro&page=wpdm-settings', 'title' => 'User Interface', 'callback' => array($this, 'userInterface'));
        $tabs['frontend'] = array('id' => 'frontend','icon'=>'fa fa-desktop', 'link' => 'edit.php?post_type=wpdmpro&page=wpdm-settings&tab=frontend', 'title' => 'Frontend Access', 'callback' => array($this, 'Frontend'));

        // Add buddypress settings menu when buddypress plugin is active
        if (function_exists('bp_is_active')) {
            $tabs['buddypress'] = array('id' => 'buddypress','icon'=>'fa fa-users', 'link' => 'edit.php?post_type=wpdmpro&page=wpdm-settings&tab=buddypress', 'title' => 'BuddyPress', 'callback' => array($this, 'Buddypress'));
        }

        if(defined('WPDM_CLOUD_STORAGE')){
            $tabs['cloud-storage'] = array('id' => 'cloud-storage','icon'=>'fa fa-cloud-arrow-up',  'link' => 'edit.php?post_type=wpdmpro&page=wpdm-settings&tab=cloud-storage', 'title' => 'Cloud Storage', 'callback' => array($this, 'cloudStorage'));
        }

        if(!$stabs) $stabs = array();


        $stabs = $tabs + $stabs;

        $stabs = apply_filters("add_wpdm_settings_tab", $stabs);

	    $stabs['activity-reports'] = array('id' => 'activity-reports','icon'=>'fa fa-chart-line', 'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=activity-reports', 'title' => __('Activity Reports', 'download-manager'), 'callback' => array($this, 'activityReports'));
	    $stabs['wpdm-crons'] = array('id' => 'wpdm-crons','icon'=>'fa fa-clock-rotate-left',  'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=wpdm-crons', 'title' => __('Cron Jobs', 'download-manager'), 'callback' => array($this, 'cronJobs'));
	    $stabs['privacy'] = array('id' => 'privacy','icon'=>'fas fa-user-shield',  'link' => 'edit.php?post_type=wpdmpro&page=wpdm-settings&tab=privacy', 'title' => 'Privacy', 'callback' => array($this, 'privacy'));

    }


    /**
     * @usage  Admin Settings Tab Helper
     * @param string $sel
     */
    public static function renderMenu($sel = '')
    {
        global $stabs;

	    foreach ($stabs as $tab) {
		    $isactive = ($sel == $tab['id']) ? 'class="active"' : '';
		    echo "<li {$isactive}><a id='{$tab['id']}' data-icon='{$tab['icon']}' href='{$tab['link']}'><i class='{$tab['icon']}'></i>{$tab['title']}</a></li>";
	    }
    }

    function basic(){

        if (isset($_POST['task']) && $_POST['task'] == 'wdm_save_settings') {

            if(!current_user_can('manage_options')) die(__( "You are not allowed to change settings!", "download-manager" ));

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], WPDMSET_NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            foreach ($_POST as $optn => $optv) {
                if(strpos("__".$optn, '_wpdm_')) {
                    $optv = wpdm_sanitize_array($optv);
					if($optn === '_wpdm_file_browser_root') {
						$optv = realpath( get_home_path() . '/' . $optv );
						$optv = str_replace("\\", "/", $optv);
						if ( $optv ) {
							$optv = trailingslashit( $optv );
						}
					}
                    update_option($optn, $optv, false);
                }
            }

            WPDM()->apply->sfbAccess();

            if (!isset($_POST['__wpdm_skip_locks'])) delete_option('__wpdm_skip_locks');
            if (!isset($_POST['__wpdm_login_form'])) delete_option('__wpdm_login_form');
            if (!isset($_POST['__wpdm_cat_desc'])) delete_option('__wpdm_cat_desc');
            if (!isset($_POST['__wpdm_cat_img'])) delete_option('__wpdm_cat_img');
            if (!isset($_POST['__wpdm_cat_tb'])) delete_option('__wpdm_cat_tb');
            flush_rewrite_rules();
            global $wp_rewrite, $WPDM;
            $WPDM->registerPostTypeTaxonomy();
            $wp_rewrite->flush_rules();
            die('Settings Saved Successfully');
        }
	    $show_db_update_notice = 0;
	    if(Installer::dbUpdateRequired()){
		    $show_db_update_notice = 1;
		    Installer::updateDB();
	    }

        include wpdm_admin_tpl_path("settings/basic.php");

    }

    function userInterface(){

        if (isset($_POST['task']) && $_POST['task'] == 'wdm_save_settings' && current_user_can(WPDM_ADMIN_CAP)) {

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], WPDMSET_NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            foreach ($_POST as $optn => $optv) {
                if(strpos("__".$optn, '_wpdm_')) {
                    //$optv = wpdm_sanitize_array($optv);
	                $optv = wpdm_sanitize_array($optv, "/([^\#a-zA-Z0-9_\+\-\s:\.\;\@])/");
                    //echo $optn."=".$optv."<br/>";
                    update_option($optn, $optv, false);
                }
            }

            die(__( "Settings Saved Successfully", "download-manager" ));
        }
        include wpdm_admin_tpl_path("settings/user-interface.php");

    }


    function frontEnd(){
        if(isset($_POST['section']) && $_POST['section']=='frontend' && isset($_POST['task']) && $_POST['task']=='wdm_save_settings' && current_user_can(WPDM_ADMIN_CAP)){
            if(!wp_verify_nonce($_POST['__wpdms_nonce'], WPDMSET_NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            foreach($_POST as $k => $v){
                if(strpos("__".$k, '_wpdm_')){
                    $v = wpdm_sanitize_array($v);
                    update_option($k, $v, false);
                }
            }



            global $wp_roles;

            $roleids = array_keys($wp_roles->roles);
            $roles = maybe_unserialize(get_option('__wpdm_front_end_access',array()));
            $naroles = array_diff($roleids, $roles);

            foreach($roles as $role) {
                $role = get_role($role);
                if(is_object($role))
                    $role->add_cap('upload_files');
            }

            foreach($naroles as $role) {
                $role = get_role($role);
                if(!isset($role->capabilities['edit_posts']) || $role->capabilities['edit_posts']!=1)
                    $role->remove_cap('upload_files');
            }

            $refresh = 0;

            $page_id = wpdm_query_var('__wpdm_user_dashboard', 'int');
            if($page_id != '') {
                $page_name = get_post_field("post_name", $page_id);
                add_rewrite_rule('^' . $page_name . '/(.+)/?', 'index.php?page_id=' . $page_id . '&udb_page=$matches[1]', 'top');
                $refresh = 1;
            }


	        $page_id = wpdm_query_var('__wpdm_author_profile', 'int');
            if((int)$page_id > 0) {
                $page_name = get_post_field("post_name", $page_id);
                add_rewrite_rule('^' . $page_name . '/(.+)/?$', 'index.php?pagename=' . $page_name . '&profile=$matches[1]', 'top');
                $refresh = 1;
            }

            if($refresh == 1){
                global $wp_rewrite;
                $wp_rewrite->flush_rules(true);
            }

            die('Settings Saved Successfully!');
        }
        include wpdm_admin_tpl_path("settings/frontend.php");
    }

    function socialConnects(){
        if(isset($_POST['section']) && $_POST['section']=='social-connects' && isset($_POST['task']) && $_POST['task']=='wdm_save_settings' && current_user_can(WPDM_ADMIN_CAP)){

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], WPDMSET_NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            foreach($_POST as $k => $v){
                if(strpos("__".$k, '_wpdm_')){
                    update_option($k, wpdm_sanitize_array($v), false);
                }
            }
            die('Settings Saved Successfully!');
        }
        include wpdm_admin_tpl_path("settings/social-connects.php");
    }

    function Buddypress(){
        if(isset($_POST['section']) && $_POST['section']=='buddypress' && isset($_POST['task']) && $_POST['task']=='wdm_save_settings' && current_user_can(WPDM_ADMIN_CAP)){

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], WPDMSET_NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            foreach($_POST as $k => $v){
                if(strpos("__".$k, '_wpdm_')){
                    update_option($k, wpdm_sanitize_array($v), false);
                }
            }
            die('Settings Saved Successfully!');
        }
        include wpdm_admin_tpl_path("settings/buddypress.php");
    }

    function cloudStorage(){
        if(isset($_POST['section']) && $_POST['section']=='cloud-storage' && isset($_POST['task']) && $_POST['task']=='wdm_save_settings' && current_user_can(WPDM_ADMIN_CAP)){

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], WPDMSET_NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            foreach($_POST as $k => $v){
                if(strpos("__".$k, '_wpdm_')){
                    update_option($k, wpdm_sanitize_array($v), false);
                }
            }
            die('Settings Saved Successfully!');
        }
        include wpdm_admin_tpl_path("settings/cloud-storage.php");
    }



    function Privacy(){
        if (wpdm_query_var('task') == 'wdm_save_settings' && wpdm_query_var('section') == 'privacy') {
            update_option('__wpdm_noip', wpdm_query_var('__wpdm_noip', 'int', 0));
            update_option('__wpdm_delstats_on_udel', wpdm_query_var('__wpdm_delstats_on_udel', 'int', 0));
            update_option('__wpdm_checkout_privacy', wpdm_query_var('__wpdm_checkout_privacy', 'int', 0));
            update_option('__wpdm_checkout_privacy_label', wpdm_query_var('__wpdm_checkout_privacy_label', 'txt'));
	        update_option('__wpdm_tmp_storage', wpdm_query_var('__wpdm_tmp_storage', 'txt', 'db'));
	        update_option('__wpdm_auto_clean_cache', wpdm_query_var('__wpdm_auto_clean_cache', 'int', 0));
	        update_option('__wpdm_cron_key', wpdm_query_var('__wpdm_cron_key', 'alphanum', ''));
	        _e("Privacy Settings Saved Successfully", "download-manager");
            die();
        }
        include wpdm_admin_tpl_path("settings/privacy.php");
    }

	function cronJobs() {
		if (wpdm_query_var('task') == 'wdm_save_settings' && wpdm_query_var('section') == 'wpdm-crons') {
			_e("Nothing to update!", "download-manager");
			die();
		}
		include wpdm_admin_tpl_path("settings/cron-jobs.php");
	}

	function deleteCron() {
		__::isAuthentic('wpdmdcx', WPDM_PRI_NONCE, WPDM_ADMIN_CAP);
		CronJob::delete(wpdm_query_var('cronid', 'int'));
		wp_send_json(['success' => true]);
	}

	function testRecaptcha() {
		if (!current_user_can(WPDM_ADMIN_CAP)) {
			wp_send_json_error(['message' => __('Permission denied.', 'download-manager')]);
		}

		if (!wp_verify_nonce(wpdm_query_var('_wpnonce'), 'wpdm_test_recaptcha')) {
			wp_send_json_error(['message' => __('Security token expired. Please refresh the page.', 'download-manager')]);
		}

		$token = wpdm_query_var('token', 'txt');
		if (empty($token)) {
			wp_send_json_error(['message' => __('No reCAPTCHA token provided.', 'download-manager')]);
		}

		$result = wpdm_recaptcha_enterprise_verify($token, 'TEST');

		if ($result['success']) {
			wp_send_json_success([
				'message' => __('reCAPTCHA verification successful!', 'download-manager'),
				'score' => isset($result['score']) ? $result['score'] : 'N/A'
			]);
		} else {
			wp_send_json_error([
				'message' => isset($result['error']) ? $result['error'] : __('Verification failed.', 'download-manager'),
				'error_code' => isset($result['error_code']) ? $result['error_code'] : '',
				'error_details' => isset($result['error_details']) ? $result['error_details'] : ''
			]);
		}
	}

    /**
     * Activity Reports settings tab
     */
    function activityReports()
    {
        if (isset($_POST['section']) && $_POST['section'] == 'activity-reports' && isset($_POST['task']) && $_POST['task'] == 'wdm_save_settings' && current_user_can(WPDM_ADMIN_CAP)) {

            if (!wp_verify_nonce($_POST['__wpdms_nonce'], WPDMSET_NONCE_KEY)) {
                die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));
            }

            // Save enabled state
            update_option('__wpdm_activity_report_enabled', wpdm_query_var('__wpdm_activity_report_enabled', 'int', 0));

            // Save schedule settings
            update_option('__wpdm_activity_report_frequency', wpdm_query_var('__wpdm_activity_report_frequency', 'txt', 'weekly'));
            update_option('__wpdm_activity_report_day', wpdm_query_var('__wpdm_activity_report_day', 'int', 1));
            update_option('__wpdm_activity_report_hour', wpdm_query_var('__wpdm_activity_report_hour', 'int', 9));

            // Save recipients
            update_option('__wpdm_activity_report_admin', wpdm_query_var('__wpdm_activity_report_admin', 'int', 1));
            $emails = wpdm_query_var('__wpdm_activity_report_emails', 'txt', '');
            update_option('__wpdm_activity_report_emails', sanitize_text_field($emails));

            // Save sections
            $sections = isset($_POST['__wpdm_activity_report_sections']) ? array_map('sanitize_text_field', $_POST['__wpdm_activity_report_sections']) : [];
            update_option('__wpdm_activity_report_sections', $sections);

            // Reschedule the job
            ActivityReportJob::schedule();

            die(__('Activity Report Settings Saved Successfully', 'download-manager'));
        }

        include wpdm_admin_tpl_path("settings/activity-reports.php");
    }

    /**
     * AJAX handler: Send test activity report
     */
    function sendTestActivityReport()
    {
        if (!current_user_can(WPDM_ADMIN_CAP)) {
            wp_send_json_error(['message' => __('Permission denied.', 'download-manager')]);
        }

        if (!wp_verify_nonce(wpdm_query_var('nonce'), 'wpdm_activity_report_test')) {
            wp_send_json_error(['message' => __('Security token expired. Please refresh the page.', 'download-manager')]);
        }

        $email = sanitize_email(wpdm_query_var('email', 'txt'));
        if (!is_email($email)) {
            wp_send_json_error(['message' => __('Invalid email address.', 'download-manager')]);
        }

        $result = ActivityReportJob::sendTestReport($email);

        if ($result) {
            wp_send_json_success(['message' => sprintf(__('Test report sent to %s', 'download-manager'), $email)]);
        } else {
            wp_send_json_error(['message' => __('Failed to send test report. Please check your email settings.', 'download-manager')]);
        }
    }

    /**
     * AJAX handler: Reschedule activity report job
     */
    function rescheduleActivityReport()
    {
        if (!current_user_can(WPDM_ADMIN_CAP)) {
            wp_send_json_error(['message' => __('Permission denied.', 'download-manager')]);
        }

        if (!wp_verify_nonce(wpdm_query_var('nonce'), 'wpdm_reschedule_activity_report')) {
            wp_send_json_error(['message' => __('Security token expired.', 'download-manager')]);
        }

        $jobId = ActivityReportJob::schedule();

        if ($jobId) {
            wp_send_json_success(['message' => __('Activity report job rescheduled.', 'download-manager'), 'job_id' => $jobId]);
        } else {
            wp_send_json_success(['message' => __('Activity reports are disabled or no job needed.', 'download-manager')]);
        }
    }

}
