/**
 * 相簿列表排序切換：改用 AJAX 換卡片列表 + 分頁，不整頁跳轉。
 */
( function () {
	function initPhotoAlbumSort( wrap ) {
		var select = wrap.querySelector( '.photo-album-order-select' );
		var results = wrap.querySelector( '.photo-album-results' );

		if ( ! select || ! results || typeof AstraChildPhotoAlbum === 'undefined' ) {
			return;
		}

		select.addEventListener( 'change', function () {
			var order = select.value;

			select.disabled = true;
			results.setAttribute( 'aria-busy', 'true' );

			var body = new URLSearchParams( {
				action: 'photo_album_sort',
				order: order,
				count: wrap.dataset.count,
				base_url: wrap.dataset.baseUrl,
				nonce: wrap.dataset.nonce
			} );

			fetch( AstraChildPhotoAlbum.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body.toString()
			} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( json ) {
					if ( json && json.success && json.data && 'string' === typeof json.data.html ) {
						results.innerHTML = json.data.html;
						updateUrl( wrap, order );
					}
				} )
				.catch( function () {} )
				.finally( function () {
					select.disabled = false;
					results.removeAttribute( 'aria-busy' );
				} );
		} );
	}

	// 只更新網址列 (不觸發跳頁)，讓重新整理、分享連結時排序狀態不會跑掉
	function updateUrl( wrap, order ) {
		try {
			var url = new URL( wrap.dataset.baseUrl, window.location.href );
			if ( 'asc' === order ) {
				url.searchParams.set( 'photo_album_order', 'asc' );
			} else {
				url.searchParams.delete( 'photo_album_order' );
			}
			window.history.replaceState( null, '', url.toString() );
		} catch ( e ) {}
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.photo-album-wrap' ).forEach( initPhotoAlbumSort );
	} );
} )();
