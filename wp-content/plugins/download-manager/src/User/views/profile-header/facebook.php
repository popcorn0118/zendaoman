<?php
if(!defined('ABSPATH')) die('Dream more!');
?>
<div class="profile-card-inner facebook">
    <div class="media">
	<div id="logo-block" class="mr-4">
		<img class="shop-logo m-0 p-0 box-shadow-none" id="shop-logo" src="<?php echo isset($store['logo']) && $store['logo'] != '' ? $store['logo'] : get_avatar_url( get_current_user_id(), array('size' => 512) ); ?>"/>
	</div>
        <div class="media-body text-left">
            <h2 class="mt-0 mb-1" id="profile-title"><?php echo $store['title']; ?></h2>
	        <?php echo isset($store['intro']) && !empty($store['intro']) ? "<div class='mt-0 mb-2 store-intro'>{$store['intro']}</div>":""; ?>
            <div class="text-small mb-3"><?php echo $store['description']; ?></div>
            <div id="profile-buttons" class="mt-2">
		        <?php
		        foreach ($this->profile_menu as $id => $menu){
			        if(isset($menu['content']) && !empty($menu['content'])) {
				        ?>
                        <a class="profile-button ml-0 mr-1 with-content profile-button-<?php echo $id; ?>" data-menu="<?php echo $id; ?>" id="profile-button-<?php echo $id; ?>" href="#<?php echo $id; ?>"><i class="<?php echo $menu['icon'] ?> mr-2"></i><?php echo $menu['name']; ?></a>
				        <?php
			        } else {
				        ?>
                        <a class="profile-button ml-0 mr-1 instant profile-button-<?php echo $id; ?>" data-menu="<?php echo $id; ?>" id="profile-button-<?php echo $id; ?>" href="#"><i class="<?php echo $menu['icon'] ?> mr-2"></i><?php echo $menu['name']; ?></a>
				        <?php
			        }
		        }
		        ?>
            </div>
        </div>
    </div>

</div>

<style>
    .profile-card-inner.facebook {
        padding: 16px !important;
    }
    .facebook .media{
        margin-top: 100px !important;
    }
    .facebook #profile-buttons .profile-button {
        border-radius: 3px;
        padding: 7px 14px !important;
    }
    #logo-block img {
        box-shadow: none;
        width: 160px;
        height: 160px;
        border-radius: 500px;
        background: transparent;
    }
</style>