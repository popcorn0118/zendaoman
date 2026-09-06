<?php
/**
 * 最新消息 (news) - category setup + [latest_news] shortcode
 *
 * @package astra-child
 */

// 確保「最新消息」分類存在 (slug: news)
function index_news_ensure_category() {
	if ( ! term_exists( 'news', 'category' ) ) {
		wp_insert_term( '最新消息', 'category', array( 'slug' => 'news' ) );
	}
}
add_action( 'after_setup_theme', 'index_news_ensure_category' );

// 最新消息短代碼 [latest_news count="3"]
add_shortcode( 'latest_news', function ( $atts ) {
	$atts = shortcode_atts( array(
		'count' => 3,
	), $atts, 'latest_news' );

	$query = new WP_Query( array(
		'category_name'       => 'news',
		'posts_per_page'      => (int) $atts['count'],
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );

	if ( ! $query->have_posts() ) {
		return '';
	}

	add_filter( 'excerpt_more', '__return_empty_string' );
	$index_news_excerpt_length = function () {
		return 100;
	};
	add_filter( 'excerpt_length', $index_news_excerpt_length );

	ob_start();
	?>
	<div class="index-news">
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<a class="index-news-item" href="#">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="index-news-thumb">
						<?php the_post_thumbnail( 'medium' ); ?>
					</div>
				<?php endif; ?>
				<div class="index-news-body">
					<span class="index-news-date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
					<h3 class="index-news-title"><?php the_title(); ?></h3>
					<p class="index-news-excerpt">
						<?php echo esc_html( trim( get_the_excerpt() ) ); ?>
					</p>
				</div>
			</a>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
	<?php
	remove_filter( 'excerpt_more', '__return_empty_string' );
	remove_filter( 'excerpt_length', $index_news_excerpt_length );

	return ob_get_clean();
} );
