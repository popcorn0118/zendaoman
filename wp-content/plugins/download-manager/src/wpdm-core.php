<?php

/**
 * Warning!!!
 * Don't change any function from here
 *
 */

use WPDM\__\Messages;

global $stabs, $package, $wpdm_package;


function WPDM($global = null){
    if($global){
        global $$global;
        return $$global;
    }
    global $WPDM;
    return $WPDM;
}

/**
 * @param $tablink
 * @param $newtab
 * @param $func
 * @deprecated Deprecated from v4.2, use filter hook 'add_wpdm_settings_tab'
 * @usage Deprecated: From v4.2, use filter hook 'add_wpdm_settings_tab'
 */
function add_wdm_settings_tab($tablink, $newtab, $func)
{
    global $stabs;
    $stabs["{$tablink}"] = array('id' => $tablink, 'icon' => 'fa fa-cog', 'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=' . $tablink, 'title' => $newtab, 'callback' => $func);
}

/**
 * @param $tabid
 * @param $tabtitle
 * @param $callback
 * @param string $icon
 * @return array
 */
function wpdm_create_settings_tab($tabid, $tabtitle, $callback, $icon = 'fa fa-cog')
{
    return \WPDM\Admin\Menu\Settings::createMenu($tabid, $tabtitle, $callback, $icon);
}


/**
 * @usage Check user's download limit
 * @param $id
 * @return bool
 */
function wpdm_is_download_limit_exceed($id)
{
    return WPDM()->package->userDownloadLimitExceeded($id);
}


/**
 * @param (int|array) $package Package ID (INT) or Complete Package Data (Array)
 * @param string $ext
 * @return string|void
 */
function wpdm_download_url($package, $params = array())
{
    if (!is_array($package)) $package = intval($package);
    $id = is_int($package) ? $package : $package['ID'];
    return WPDM()->package->getDownloadURL($id, $params);
}


/**
 * @usage Check if a download manager category has child
 * @param $parent
 * @return bool
 */

function wpdm_cat_has_child($parent)
{
    $termchildren = get_term_children($parent, 'wpdmcategory');
    if (count($termchildren) > 0) return count($termchildren);
    return false;
}

/**
 * @usage Get category checkbox list
 * @param int $parent
 * @param int $level
 * @param array $sel
 */
function wpdm_cblist_categories($parent = 0, $level = 0, $sel = array())
{
    $cats = get_terms('wpdmcategory', array('hide_empty' => false, 'parent' => $parent));
    if (!$cats) $cats = array();
    if ($parent != '') echo "<ul>";
    foreach ($cats as $cat) {
        $id = $cat->slug;
        $pres = $level * 5;

        if (in_array($id, $sel))
            $checked = 'checked=checked';
        else
            $checked = '';
        echo "<li style='margin-left:{$pres}px;padding-left:0'><label><input id='c$id' type='checkbox' name='file[category][]' value='$id' $checked /> " . $cat->name . "</label></li>\n";
        wpdm_cblist_categories($cat->term_id, $level + 1, $sel);

    }
    if ($parent != '') echo "</ul>";
}

/**
 * @usage Get category dropdown list
 * @param string $name
 * @param string $selected
 * @param string $id
 * @param int $echo
 * @return string
 */
function wpdm_dropdown_categories($name = '', $selected = '', $id = '', $echo = 1)
{
    return wp_dropdown_categories(array('show_option_none' => __( "Select category" , "download-manager" ), 'hierarchical' => 1, 'show_count' => 0, 'orderby' => 'name', 'echo' => $echo, 'class' => 'form-control selectpicker', 'taxonomy' => 'wpdmcategory', 'hide_empty' => 0, 'name' => $name, 'id' => $id, 'selected' => $selected));

}


/**
 * @usage Post with cURL
 * @param $url
 * @param $data (array)
 * @param $headers (array)
 * @return bool|mixed|string
 */
function wpdm_remote_post($url, $data, $headers = [])
{

    $response = wp_remote_post($url, array(
            'method' => 'POST',
            'sslverify' => false,
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $headers,
            'body' => $data,
            'cookies' => array()
        )
    );
    $body = wp_remote_retrieve_body($response);
    return $body;
}

/**
 * Verify reCAPTCHA Enterprise token
 * @param string $token The reCAPTCHA token from the frontend
 * @param string $expected_action Optional expected action name
 * @return array ['success' => bool, 'score' => float, 'error' => string]
 */
function wpdm_recaptcha_enterprise_verify($token, $expected_action = '')
{
    $project_id = get_option('_wpdm_recaptcha_project_id', '');
    $api_key = get_option('_wpdm_recaptcha_secret_key', '');
    $site_key = get_option('_wpdm_recaptcha_site_key', '');

    if (empty($project_id) || empty($api_key) || empty($site_key)) {
        return ['success' => false, 'score' => 0, 'error' => 'reCAPTCHA Enterprise not configured'];
    }

    $url = 'https://recaptchaenterprise.googleapis.com/v1/projects/' . $project_id . '/assessments?key=' . $api_key;

    $body = [
        'event' => [
            'token' => $token,
            'siteKey' => $site_key,
        ]
    ];

    if (!empty($expected_action)) {
        $body['event']['expectedAction'] = $expected_action;
    }

    $response = wp_remote_post($url, [
        'method' => 'POST',
        'timeout' => 10,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => wp_json_encode($body),
    ]);

    if (is_wp_error($response)) {
        return ['success' => false, 'score' => 0, 'error' => $response->get_error_message(), 'error_code' => 'WP_ERROR'];
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $result = json_decode(wp_remote_retrieve_body($response), true);

    // Handle API-level errors (wrong API key, project ID, etc.)
    if ($response_code !== 200) {
        $api_error = isset($result['error']['message']) ? $result['error']['message'] : 'API request failed';
        $api_status = isset($result['error']['status']) ? $result['error']['status'] : '';
        return [
            'success' => false,
            'score' => 0,
            'error' => $api_error,
            'error_code' => $api_status,
            'error_details' => wpdm_recaptcha_get_api_error_help($api_status, $api_error)
        ];
    }

    if (!$result) {
        return ['success' => false, 'score' => 0, 'error' => 'Invalid response from reCAPTCHA Enterprise', 'error_code' => 'INVALID_RESPONSE'];
    }

    // Check if token is valid
    $valid = isset($result['tokenProperties']['valid']) && $result['tokenProperties']['valid'] === true;
    $score = isset($result['riskAnalysis']['score']) ? (float)$result['riskAnalysis']['score'] : 0;
    $invalid_reason = isset($result['tokenProperties']['invalidReason']) ? $result['tokenProperties']['invalidReason'] : '';

    // For checkbox mode, we mainly check validity
    // Score threshold can be configured if needed
    return [
        'success' => $valid,
        'score' => $score,
        'error' => $valid ? '' : wpdm_recaptcha_get_error_message($invalid_reason),
        'error_code' => $invalid_reason,
        'error_details' => $valid ? '' : wpdm_recaptcha_get_error_help($invalid_reason)
    ];
}

/**
 * Get human-readable error message for reCAPTCHA invalid reasons
 */
function wpdm_recaptcha_get_error_message($reason)
{
    $messages = [
        'INVALID_REASON_UNSPECIFIED' => 'Token validation failed',
        'UNKNOWN_INVALID_REASON' => 'Unknown validation error',
        'MALFORMED' => 'Malformed token',
        'EXPIRED' => 'Token has expired',
        'DUPE' => 'Token already used',
        'MISSING' => 'Token is missing',
        'BROWSER_ERROR' => 'Browser error during verification',
        'SITE_MISMATCH' => 'Site key mismatch',
    ];

    return isset($messages[$reason]) ? $messages[$reason] : 'Verification failed';
}

/**
 * Get troubleshooting help for reCAPTCHA errors
 */
function wpdm_recaptcha_get_error_help($reason)
{
    $help = [
        'INVALID_REASON_UNSPECIFIED' => 'This usually means the Site Key doesn\'t match the one registered in Google Cloud Console. Please verify: 1) The Site Key is correct, 2) Your domain is added to the allowed domains in reCAPTCHA settings, 3) The key type is "Checkbox" (not Score-based).',
        'UNKNOWN_INVALID_REASON' => 'An unexpected error occurred. Please try again or check Google Cloud Console for any issues.',
        'MALFORMED' => 'The reCAPTCHA token is corrupted. This may be caused by JavaScript conflicts. Try disabling other plugins temporarily.',
        'EXPIRED' => 'The CAPTCHA expired before verification. Please complete the CAPTCHA and submit quickly (within 2 minutes).',
        'DUPE' => 'This token was already verified. Each CAPTCHA completion can only be used once. Please complete the CAPTCHA again.',
        'MISSING' => 'No token was received. Ensure the reCAPTCHA widget loaded correctly and the form includes the token.',
        'BROWSER_ERROR' => 'The browser encountered an error. Try a different browser or check for JavaScript errors in the console.',
        'SITE_MISMATCH' => 'The Site Key used on this page doesn\'t match the key configured in Google Cloud. Verify your Site Key is correct.',
    ];

    return isset($help[$reason]) ? $help[$reason] : 'Please verify your reCAPTCHA Enterprise configuration in Google Cloud Console.';
}

/**
 * Get troubleshooting help for API-level errors
 */
function wpdm_recaptcha_get_api_error_help($status, $message)
{
    $help = [
        'PERMISSION_DENIED' => 'The API Key doesn\'t have permission. Common causes: 1) API Key has HTTP referrer restrictions - this will NOT work because verification happens server-side. Remove referrer restrictions or use IP address restrictions instead (your server IP). 2) reCAPTCHA Enterprise API is not enabled in your Google Cloud project. 3) The API Key is incorrect.',
        'INVALID_ARGUMENT' => 'Invalid configuration. Check that your Project ID and Site Key are correct and match your Google Cloud Console settings.',
        'NOT_FOUND' => 'Project or resource not found. Verify your Google Cloud Project ID is correct.',
        'UNAUTHENTICATED' => 'Authentication failed. Your API Key may be invalid or expired. Generate a new API Key in Google Cloud Console.',
    ];

    if (isset($help[$status])) {
        return $help[$status];
    }

    if (strpos($message, 'API key not valid') !== false) {
        return 'Your API Key is invalid. Please check: 1) The API Key is copied correctly (no extra spaces), 2) The key hasn\'t been deleted or regenerated, 3) If the key has restrictions, do NOT use HTTP referrer restrictions - use IP address (server IP) instead, as verification is done server-side.';
    }

    if (strpos($message, 'Requests from referer') !== false || strpos($message, 'referer') !== false) {
        return 'Your API Key has HTTP referrer restrictions which will not work. reCAPTCHA verification happens on the server, not in the browser, so referrer headers are not sent. Solution: In Google Cloud Console, edit your API Key and either remove all restrictions, or change to IP address restrictions using your server\'s IP address.';
    }

    return 'Please verify all credentials in Google Cloud Console: Project ID, Site Key, and API Key. Important: If your API Key has restrictions, do not use HTTP referrer restrictions - use IP address restrictions instead.';
}

/**
 * @usage Get with cURL
 * @param $url
 * @param $headers (array)
 * @return bool|mixed|string
 */
function wpdm_remote_get($url, $headers = [])
{
    $content = "";
    $response = wp_remote_get($url, array('timeout' => 5, 'sslverify' => false, 'headers' => $headers));
    if (is_array($response)) {
        $content = $response['body'];
    } else
        $content = Messages::error($response->get_error_message(), -1);
    return $content;
}


function wpdm_plugin_data($dir)
{
    $plugins = get_plugins();
    foreach ($plugins as $plugin => $data) {
        $data['plugin_index_file'] = $plugin;
        $plugin = explode("/", $plugin);
        if ($plugin[0] == $dir) return $data;
    }
    return false;
}

function wpdm_access_token(){
    return get_option("__wpdm_access_token", false);
}


function wpdm_plugin_update_email($plugin_name, $version, $update_url)
{

	$admin_email = get_option('admin_email');
	$hash = "__wpdm_" . md5($plugin_name . $version);
	$sent = get_option($hash, false);
	if (!$sent) {

		$message = 'New version available. Please update your copy.<br/><table class="email" style="width: 100%" cellpadding="5px"><tr><th>Plugin Name</th><th>Version</th></tr><tr><td>' . $plugin_name . '</td><td>' . $version . '</td></tr></table><div style="padding-top: 10px;"><a style="display: block;text-align: center" class="button" href="' . $update_url . '">Update Now</a></div>';

		$params = array(
			'subject' => sprintf(__("[%s] Update Available"), $plugin_name, 'download-manager'),
			'to_email' => get_option('admin_email'),
			'from_name' => 'WordPress Download Manager',
			'from_email' => 'support@wpdownloadmanager.com',
			'message' => $message
		);

		\WPDM\__\Email::send("default", $params);
		update_option($hash, 1, false);
	}
}

function wpdmpro_required(){
    ?>
    <div class="panel panel-default" style="position: relative;z-index: 99999999">
        <div class="panel-body">
            <div class="media">
                <div class="pull-right"><a href="https://www.wpdownloadmanager.com/pricing/" target="_blank" class="btn btn-primary btn-sm">Get WPDM Pro</a></div>
                <div class="media-body lead" style="line-height: 25px">This option is only available with the WordPress Download Manager Pro!</div>
            </div>
        </div>
    </div>
    <?php
}
