<?php
if(!defined('ABSPATH')) die('Dream more!');
?>
<div class="profile-card-inner">
	<div id="logo-block">
		<img class="shop-logo m-0 p-0 box-shadow-none" id="shop-logo" src="<?php echo isset($store['logo']) && $store['logo'] != '' ? $store['logo'] : get_avatar_url( get_current_user_id(), array('size' => 512) ); ?>"/>
	</div>
	<h2 class="mt-4 mb-0" id="profile-title"><?php echo $store['title']; ?></h2>
	<?php echo isset($store['intro']) && !empty($store['intro']) ? "<div class='mt-2 mb-3 store-intro'>{$store['intro']}</div>":""; ?>
	<div class="text-small mb-3"><?php echo $store['description']; ?></div>
	<div id="profile-buttons" class="mt-4">
		<?php
		foreach ($this->profile_menu as $id => $menu){
			if(isset($menu['content']) && !empty($menu['content'])) {
				?>
				<a class="profile-button with-content profile-button-<?php echo $id; ?>" data-menu="<?php echo $id; ?>" id="profile-button-<?php echo $id; ?>" href="#<?php echo $id; ?>">
					<i class="<?php echo $menu['icon'] ?> mr-2"></i><?php echo $menu['name']; ?>
				</a>
				<?php
			} else {
				?>
				<a class="profile-button instant profile-button-<?php echo $id; ?>" data-menu="<?php echo $id; ?>" id="profile-button-<?php echo $id; ?>" href="#">
					<i class="<?php echo $menu['icon'] ?> mr-2"></i><?php echo $menu['name']; ?>
				</a>
				<?php
			}
		}
		?>
	</div>
</div>
