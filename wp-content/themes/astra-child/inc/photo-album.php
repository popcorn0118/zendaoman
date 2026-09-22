<?php
/**
 * 相簿列表 (photo album) - [photo_album_list] shortcode
 *
 * 抓出所有 post_type=foogallery 的相簿，顯示首圖、標題與照片張數，
 * 點擊卡片進入相簿內頁 (虛擬單頁，見下方 gallery_id 相關程式碼)。
 *
 * FooGallery 的 foogallery post type 註冊時是 public=false、rewrite=false
 * (沒有 Albums 擴充套件的話 Gallery 本身沒有 permalink)，所以這裡改用
 * ?gallery_id= 這個 query var 做出「虛擬單頁」，不需要修改 post type 的
 * 公開設定，也不需要 flush_rewrite_rules。
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

// 註冊 gallery_id、photo_album_page、photo_album_order query var，讓
// ?gallery_id=123、?photo_album_page=2、?photo_album_order=asc 這種網址參數
// 可以被 get_query_var() 讀到
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'gallery_id';
	$vars[] = 'photo_album_page';
	$vars[] = 'photo_album_order';
	return $vars;
} );

// 攔截帶有 gallery_id 參數的請求，直接輸出該相簿的「虛擬單頁」內容
add_action( 'template_redirect', function () {
	$gallery_id = absint( get_query_var( 'gallery_id' ) );
	if ( ! $gallery_id || ! class_exists( 'FooGallery' ) ) {
		return;
	}

	if ( get_post_type( $gallery_id ) !== 'foogallery' || get_post_status( $gallery_id ) !== 'publish' ) {
		return;
	}

	$gallery = FooGallery::get_by_id( $gallery_id );
	if ( ! $gallery ) {
		return;
	}

	$back_url    = remove_query_arg( 'gallery_id' );
	$photo_count = $gallery->item_count();

	get_header();
	?>
	<div class="photo-album-single">
		<p class="photo-album-single-back">
			<a href="<?php echo esc_url( $back_url ); ?>">&larr; 返回相簿列表</a>
		</p>
		<h1 class="photo-album-single-title"><?php echo esc_html( get_the_title( $gallery_id ) ); ?></h1>
		<p class="photo-album-single-count"><?php echo esc_html( $photo_count ); ?> 張照片</p>
		<div class="photo-album-single-gallery">
			<?php echo do_shortcode( '[foogallery id="' . $gallery_id . '"]' ); ?>
		</div>
		<p class="photo-album-single-back photo-album-single-back-bottom">
			<a href="<?php echo esc_url( $back_url ); ?>">&larr; 返回相簿列表</a>
		</p>
	</div>
	<?php
	get_footer();
	exit;
} );

// 取得相簿的第一張照片 attachment ID，做為列表首圖
function astra_child_foogallery_cover_id( $gallery ) {
	$attachment_ids = $gallery->item_attachment_ids();
	if ( ! empty( $attachment_ids ) ) {
		return (int) $attachment_ids[0];
	}
	return 0;
}

// 依排序/分頁條件查詢相簿列表，同時給短代碼初次渲染與下方 AJAX 切換排序共用
function astra_child_photo_album_query( $order, $paged, $per_page ) {
	$query_args = array(
		'post_type'      => 'foogallery',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'orderby'        => 'date',
		'order'          => $order,
	);

	if ( $per_page > 0 ) {
		$query_args['paged'] = $paged;
	} else {
		$query_args['no_found_rows'] = true;
	}

	return new WP_Query( $query_args );
}

// 渲染相簿卡片列表 + 分頁，同時給短代碼初次渲染與下方 AJAX 切換排序共用
function astra_child_render_photo_album_results( WP_Query $query, $order, $paged, $per_page, $list_page_url ) {
	if ( ! $query->have_posts() ) {
		return '';
	}

	// 分頁、相簿連結都要帶著目前的排序方式，換頁/進入相簿後排序狀態才不會跑掉
	// (預設 desc 不加參數，網址乾淨一點)
	$list_page_url_with_order = ( 'desc' === $order )
		? $list_page_url
		: add_query_arg( 'photo_album_order', $order, $list_page_url );

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
			$gallery_url = add_query_arg( 'gallery_id', get_the_ID(), $list_page_url_with_order );
			?>
			<a class="photo-album-item" href="<?php echo esc_url( $gallery_url ); ?>">
				<div class="photo-album-thumb">
					<?php
					// 卡片實際顯示寬度可能到 400px 以上，用 medium (300px) 會被拉伸模糊，
					// 改用 large (WP 預設長邊 1024px) 讓圖片有足夠解析度。
					if ( $cover_id ) {
						echo wp_get_attachment_image( $cover_id, 'large' );
					} elseif ( has_post_thumbnail() ) {
						the_post_thumbnail( 'large' );
					}
					?>
				</div>
				<span class="photo-album-title"><?php the_title(); ?></span>
				<span class="photo-album-count"><?php echo esc_html( $photo_count ); ?>張照片</span>
			</a>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
	<?php if ( $per_page > 0 && $query->max_num_pages > 1 ) : ?>
		<nav class="photo-album-pagination">
			<?php for ( $i = 1; $i <= $query->max_num_pages; $i++ ) : ?>
				<?php
				$page_url = ( 1 === $i )
					? $list_page_url_with_order
					: add_query_arg( 'photo_album_page', $i, $list_page_url_with_order );
				?>
				<a class="photo-album-page-link<?php echo $i === $paged ? ' is-current' : ''; ?>" href="<?php echo esc_url( $page_url ); ?>"><?php echo esc_html( $i ); ?></a>
			<?php endfor; ?>
		</nav>
	<?php endif;
	return ob_get_clean();
}

// 相簿列表短代碼 [photo_album_list count="12"]
// count：每頁顯示幾個相簿，預設 12 筆做分頁；設為 -1 則不分頁、一次顯示全部。
add_shortcode( 'photo_album_list', function ( $atts ) {
	if ( ! class_exists( 'FooGallery' ) ) {
		return '';
	}

	$atts = shortcode_atts( array(
		'count' => 12, //預設頁數
	), $atts, 'photo_album_list' );

	$per_page = (int) $atts['count'];
	$paged    = max( 1, absint( get_query_var( 'photo_album_page' ) ) );

	// 排序方式：依相簿建立日期，desc=從新到舊 (預設)、asc=從舊到新
	$order = 'asc' === get_query_var( 'photo_album_order' ) ? 'asc' : 'desc';

	$query = astra_child_photo_album_query( $order, $paged, $per_page );

	if ( ! $query->have_posts() ) {
		return '';
	}

	// 先取得目前頁面 (放 [photo_album_list] 短代碼的頁面) 的網址，
	// 待會用來組出 ?gallery_id=xxx 的虛擬單頁連結，以及分頁連結，
	// 也會傳給前端 JS，讓切換排序時用 AJAX 重新組連結，不用整頁跳轉。
	$list_page_url = remove_query_arg( array( 'photo_album_page', 'photo_album_order' ), get_permalink() );

	// 統計資訊：總共幾本相簿、每頁顯示幾本、總共幾頁
	// count=-1 (不分頁) 時沒有 found_posts，改用實際撈到的筆數。
	// 這幾個數字跟排序 (新到舊/舊到新) 無關，切換排序時不用重新算，所以不放進 AJAX 換回來的區塊。
	$total_galleries = $per_page > 0 ? (int) $query->found_posts : (int) $query->post_count;
	$total_pages     = $per_page > 0 ? (int) $query->max_num_pages : 1;
	$per_page_label  = $per_page > 0 ? $per_page : $total_galleries;

	ob_start();
	?>
	<div class="photo-album-wrap" data-base-url="<?php echo esc_url( $list_page_url ); ?>" data-count="<?php echo esc_attr( $per_page ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'photo_album_sort' ) ); ?>">
		<div class="photo-album-toolbar">
			<div class="photo-album-order">
				<select class="photo-album-order-select">
					<option value="desc" <?php selected( $order, 'desc' ); ?>>從新到舊</option>
					<option value="asc" <?php selected( $order, 'asc' ); ?>>從舊到新</option>
				</select>
				<span class="photo-album-order-arrow" aria-hidden="true">
					<svg viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg"><path d="M1 1l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</span>
			</div>
			<div class="photo-album-summary">
				共 <?php echo esc_html( $total_galleries ); ?> 本相簿・每頁 <?php echo esc_html( $per_page_label ); ?> 本・共 <?php echo esc_html( $total_pages ); ?> 頁
			</div>
		</div>
		<div class="photo-album-results">
			<?php echo astra_child_render_photo_album_results( $query, $order, $paged, $per_page, $list_page_url ); ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
} );

// 前端切換排序用的 AJAX：換排序時只重新抓「卡片列表 + 分頁」這塊 HTML 換掉，不整頁跳轉。
// 未登入的訪客也要能用，所以 wp_ajax_ 跟 wp_ajax_nopriv_ 都要註冊。
add_action( 'wp_ajax_photo_album_sort', 'astra_child_ajax_photo_album_sort' );
add_action( 'wp_ajax_nopriv_photo_album_sort', 'astra_child_ajax_photo_album_sort' );
function astra_child_ajax_photo_album_sort() {
	check_ajax_referer( 'photo_album_sort', 'nonce' );

	if ( ! class_exists( 'FooGallery' ) ) {
		wp_send_json_error();
	}

	$order    = isset( $_POST['order'] ) && 'asc' === $_POST['order'] ? 'asc' : 'desc';
	$per_page = isset( $_POST['count'] ) ? (int) $_POST['count'] : 12;
	$base_url = isset( $_POST['base_url'] ) ? esc_url_raw( wp_unslash( $_POST['base_url'] ) ) : home_url( '/' );
	$paged    = 1; // 切換排序時一律回到第一頁

	$query = astra_child_photo_album_query( $order, $paged, $per_page );

	wp_send_json_success( array(
		'html' => astra_child_render_photo_album_results( $query, $order, $paged, $per_page, $base_url ),
	) );
}

// 掛上前端切換排序用的 JS
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_script(
		'astra-child-photo-album',
		get_stylesheet_directory_uri() . '/assets/js/photo-album.js',
		array(),
		CHILD_THEME_ASTRA_CHILD_VERSION,
		true
	);
	wp_localize_script( 'astra-child-photo-album', 'AstraChildPhotoAlbum', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
	) );
}, 20 );
