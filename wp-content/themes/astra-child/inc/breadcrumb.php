<?php
/**
 * 麵包屑 - [breadcrumb] shortcode
 *
 * @package astra-child
 */

add_shortcode( 'breadcrumb', function () {
	if ( ! function_exists( 'astra_get_breadcrumb' ) ) {
		return '';
	}

	return astra_get_breadcrumb( false );
} );
