<?php
/**
 * 相簿列表 (photo album) - [photo_album_list] shortcode
 *
 * 抓出所有 post_type=foogallery 的相簿，顯示首圖、標題與照片張數，
 * 點擊卡片進入相簿內頁 (single-foogallery.php)。
 *
 * @package astra-child
 */

// 還原先前誤將相簿設為公開時所刷新的固定連結規則 (只執行一次)
add_action( 'init', function () {
	if ( get_option( 'astra_child_foogallery_rewrite_flushed' ) ) {
		flush_rewrite_rules();
		delete_option( 'astra_child_foogallery_rewrite_flushed' );
	}
}, 20 );

// 取得相簿的第一張照片 attachment ID，做為列表首圖
function astra_child_foogallery_cover_id( $gallery ) {
	$attachment_ids = $gallery->item_attachment_ids();
	if ( ! empty( $attachment_ids ) ) {
		return (int) $attachment_ids[0];
	}
	return 0;
}

// 相簿列表短代碼 [photo_album_list count="-1"]
add_shortcode( 'photo_album_list', function ( $atts ) {
	if ( ! class_exists( 'FooGallery' ) ) {
		return '';
	}

	$atts = shortcode_atts( array(
		'count' => -1,
	), $atts, 'photo_album_list' );

	$query = new WP_Query( array(
		'post_type'      => 'foogallery',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $atts['count'],
		'orderby'        => 'title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	) );

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="photo-album-list">
		<?php while ( $query->have_posts() ) : $query->the_post();
			$gallery = FooGallery::get_by_id( get_the_ID() );
			if ( ! $gallery ) {
				continue;
			}
			$photo_count = $gallery->item_count();
			$cover_id    = astra_child_foogallery_cover_id( $gallery );
			?>
			<a class="photo-album-item" href="<?php the_permalink(); ?>">
				<div class="photo-album-thumb">
					<?php
					if ( $cover_id ) {
						echo wp_get_attachment_image( $cover_id, 'medium' );
					} elseif ( has_post_thumbnail() ) {
						the_post_thumbnail( 'medium' );
					}
					?>
				</div>
				<span class="photo-album-title"><?php the_title(); ?></span>
				<span class="photo-album-count"><?php echo esc_html( $photo_count ); ?>張照片</span>
			</a>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
	<?php
	return ob_get_clean();
} );
