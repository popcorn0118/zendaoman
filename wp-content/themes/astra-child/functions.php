<?php
/**
 * astra-child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package astra-child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );


// footer copyright
add_shortcode('copyright', function () {
    $year = date_i18n('Y');
    $name = get_bloginfo('name');

    return
        '<small class="site-copyright">' .
            '<span class="copyright-prefix">' . esc_html("Copyright © {$year} {$name}") . '</span> ' .
            '<span class="line">|</span> ' .
            '<span class="copyright-powered">Design by </span>' .
            '<a href="https://www.nss.com.tw/" target="_blank">戰國策</a>' .
        '</small>';
});