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


/**
 * Includes
 */
require_once get_stylesheet_directory() . '/inc/index-news.php';
require_once get_stylesheet_directory() . '/inc/photo-album.php';


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

// 隱藏 Rank Math 麵包屑中的 "Page N" 分頁項目
add_filter( 'rank_math/frontend/breadcrumb/settings', function( $settings ) {
    $settings['show_pagination'] = false;
    return $settings;
} );

// 修改 Astra 文章列表分頁的 Previous / Next 文字
add_filter( 'astra_default_strings', function( $strings ) {
    $strings['string-blog-navigation-previous'] = '<span class="ast-left-arrow" aria-hidden="true">&larr;</span> 上一頁';
    $strings['string-blog-navigation-next']     = '下一頁 <span class="ast-right-arrow" aria-hidden="true">&rarr;</span>';
    return $strings;
} );

// 文章單頁下方的上一篇 / 下一篇，限制在同分類內切換
add_filter( 'astra_single_post_navigation', function( $args ) {
    $args['in_same_term'] = true;
    $args['taxonomy']     = 'category';
    return $args;
} );



