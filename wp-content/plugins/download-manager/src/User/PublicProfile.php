<?php
namespace WPDM\User;


use WPDM\__\__;
use WPDM\__\Crypt;
use WPDM\__\Messages;

class PublicProfile
{

    public $profile_menu;
    public $user;

    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    function __construct(){
        add_action("init", array($this, 'profileMenuInit'));
        add_action("wp_ajax_wpdm_get_profile_menu_content", array($this, 'menuContent'));
        add_action("wp_ajax_nopriv_wpdm_get_profile_menu_content", array($this, 'menuContent'));
        add_shortcode("wpdm_user_profile", array($this, 'profile'));
    }

    function profileMenuInit(){
        $this->profile_menu['downloads'] = array('icon' => 'fas fa-arrow-alt-circle-down', 'name'=> __( "Downloads" , "download-manager" ), 'content' => array($this, 'downloads'));
        $this->profile_menu['favourites'] = array('icon' => 'fas fa-heart', 'name'=> __( "Favourites" , "download-manager" ), 'content' => array($this, 'favourites'));
        $this->profile_menu = apply_filters("wpdm_user_profile_menu", $this->profile_menu);
    }

    function menuContent(){
        $menu = wpdm_query_var('__pmenu');
        // Only dispatch to registered profile-menu handlers.
        if(!isset($this->profile_menu[$menu]) || !is_callable($this->profile_menu[$menu]['content'])) die();
        // The profile owner id is signed into the __scp token emitted on the profile page. Requiring
        // __pu to match the signed token blocks unauthenticated enumeration of arbitrary users' profile
        // data ( e.g. favourites ) via a guessed/forged __pu ( CWE-862 Missing Authorization ).
        if($this->profileUser() !== wpdm_query_var('__pu', 'int')){ status_header(403); die(); }
        call_user_func($this->profile_menu[$menu]['content']);
        die();
    }

    /**
     * Authoritative profile-owner id, taken from the signed __scp token rather than the raw __pu
     * request var. Returns 0 when the token is missing, invalid or forged so callers can reject.
     */
    private function profileUser(){
        $scp = Crypt::decrypt(wpdm_query_var('__scp'), true);
        return is_array($scp) ? (int) __::valueof($scp, '__pu') : 0;
    }

    function profile($params = array()){
        global $wp_query;

        if(!isset($params) || !is_array($params)) $params = array();

        //if(is_admin()) return "";

        ob_start();

        $username = urldecode(get_query_var('profile'));
        if(is_author())
            $username = get_query_var('author_name');
        if($username)
            $user = get_user_by('slug', $username);
        else
            $user = wp_get_current_user();

        $cols = isset($params['cols'])?$params['cols']:3;
        $items_per_page = isset($params['items_per_page'])?$params['items_per_page']:$cols*3;
        $cols = 12/$cols;
        $template = isset($params['template'])?$params['template']:'link-template-panel.php';
		$header = __::valueof($params, 'header', ['default' => 'default']);
		$headers = ['default' => 'default.php', 'facebook' => 'facebook.php'];
		$header = isset($headers[$header]) ? $headers[$header] : 'default.php';

        $user_ID = $user->ID;
        $store = get_user_meta($user_ID, '__wpdm_public_profile', true);
        if(!is_array($store)) $store = array();
        $store['logo'] = isset($store['logo'])?$store['logo']:get_avatar_url($user_ID);
        $store['title'] = isset($store['title']) && $store['title'] != '' ? $store['title'] : $user->display_name;
        $store['intro'] = isset($store['intro']) && $store['intro'] != '' ? $store['intro'] : '';
        $store['description'] = isset($store['description']) && $store['description'] != '' ? $store['description'] : '';
        $store['banner'] = isset($store['banner']) && $store['banner'] != '' ? $store['banner'] : '';
        $store['bgcolor'] = isset($store['bgcolor']) && $store['bgcolor'] != '' ? $store['bgcolor'] : '#eeeeee';
        $store['txtcolor'] = isset($store['txtcolor']) && $store['txtcolor'] != '' ? $store['txtcolor'] : '#333333';
        $mydownloads = count_user_posts($user->ID, 'wpdmpro');
        $rgb = wpdm_hex2rgb($store['bgcolor']);
        $first_menu = array_keys($this->profile_menu);
        $first_menu = $first_menu[0];
        include WPDM()->template->locate('public-profile.php', __DIR__.'/views');
        return ob_get_clean();
    }


    function downloads(){
        $uid = $this->profileUser();
        if(!$uid) die();
        $params = Crypt::decrypt(wpdm_query_var('__scp'), true);
        if(!is_array($params)) $params = array();
        unset($params['__pu']);
        $params['author'] = $uid;
		$params['async'] = 1;
        echo WPDM()->package->shortCodes->packages($params);
    }

    function favourites(){
        $uid = $this->profileUser();
        if(!$uid) die();
        $params = Crypt::decrypt(wpdm_query_var('__scp'), true);
        if(!is_array($params)) $params = array();
        unset($params['__pu']);
        $myfavs = maybe_unserialize(get_user_meta($uid, '__wpdm_favs', true));
		if(!is_array($myfavs) || count($myfavs) === 0) {
			Messages::info("Do not have any favourite item yet!");
			die();
		}
        $params['post__in'] = implode(",", $myfavs);
        echo WPDM()->package->shortCodes->packages($params);

    }


}

