<?php


namespace WPDM\Package;


class RestAPI
{

    function __construct()
    {
        add_action('rest_api_init', array($this, 'introduceEndpoints'));
    }

    function introduceEndpoints()
    {

        register_rest_route('wpdm', '/validate-captcha', array(
            'methods' => 'POST',
            'callback' => [new PackageLocks(), 'validateCaptcha'],
            'permission_callback' => '__return_true'
        ));

        register_rest_route('wpdm', '/validate-password', array(
            'methods' => 'POST',
            'callback' => [new PackageLocks(), 'validatePassword'],
            'permission_callback' => '__return_true'
        ));

        register_rest_route('wpdm', '/search', array(
            'methods' => 'GET',
            'callback' => [new PackageController(), 'search'],
            'permission_callback' => '__return_true'
        ));

        // NOTE: The POST /wpdm/view-count route was removed. It exposed a
        // state-changing write (__wpdm_view_count post meta, on any post ID)
        // to unauthenticated callers via permission_callback => '__return_true'.
        // Nothing in the plugin ever called it - view counts are recorded
        // through the 'wpdm_view_count' admin-ajax action (see addViewCount()),
        // which verifies a nonce and constrains the ID to the wpdmpro type.

    }
}
