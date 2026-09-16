<?php


namespace WPDM\User;


use WPDM\__\__;
use WPDM\__\__MailUI;
use WPDM\__\Email;
use WPDM\__\Template;

if(!defined("ABSPATH")) die("Shit happens!");

class UserController
{
    private static $instance;

    public $data;
    public $dashboard;
    public $login;
    public $register;
    public $profile;
    public $authorDashboard;
    public $editProfile;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {

        $this->data             = User::getInstance();
        $this->login            = Login::getInstance();
        $this->register         = Register::getInstance();
        $this->profile          = PublicProfile::getInstance();
        $this->dashboard        = Dashboard::getInstance();
        $this->editProfile      = EditProfile::getInstance();

	    add_filter('manage_users_columns', [$this, 'addUserColumns']);
	    add_filter('manage_users_custom_column', [$this, 'addUserColumnData'], 9999, 3);

	    add_action('wp_ajax_wpdmdz_user_status', [$this, 'reviewUserStatus']);
	    add_action('wp_ajax_wpdmdz_update_user_status', [$this, 'updateUserStatus']);

	    add_action('personal_options', [$this, 'reviewUserStatusEP']);
    }

	/**
	 *
	 *
	 */
	public function addUserColumns($column)
	{
		$column['wpdm_user_status'] = __('Status', WPDM_TEXT_DOMAIN);
		return $column;
	}


	/**
	 *
	 *
	 */
	public function addUserColumnData($output, $column_name, $user_id)
	{
		switch ($column_name) {
			case 'wpdm_user_status':
				$colors = ['' => 'success', 'approved' => 'success', 'pending' => 'warinng', 'declined' => 'danger', 'suspended' => 'danger'];
				$_status = get_user_meta($user_id, '__wpdm_user_status', true);
				if(in_array($_status, ['', 'approved']))
					$status = '<i class="fa-solid fa-check-double"></i> '.__('Approved', WPDM_TEXT_DOMAIN);
				else if($_status === 'pending')
					$status = '<i class="fa-solid fa-clock"></i> '.__('Pending', WPDM_TEXT_DOMAIN);
				else if($_status === 'declined')
					$status = '<i class="fa-solid fa-times-circle"></i> '.__('Declined', WPDM_TEXT_DOMAIN);
				else
					$status = '<i class="fa-solid fa-ban"></i> Suspended';
				$url = admin_url('admin-ajax.php?action=wpdmdz_user_status&user='.$user_id."&wpdmusnonce=".wp_create_nonce(WPDM_PRI_NONCE));
				return "<div class='w3eden' id='usts-{$user_id}'><span onclick='WPDM.bootAlert(\"Review User Status\", {url: \"$url\"}, 500);' class='c-pointer text-{$colors[$_status]}'>".$status."</span></div>";
			default:
		}

		return $output;
	}

	function requiresApproval()
	{
		return (int)get_option('__wpdm_signups_need_approval', 0);
	}

	function isApproved($userID)
	{
		$status = get_user_meta($userID, '__wpdm_user_status', true);
		return  in_array($status, ['', 'approved']);
	}

	function getStatus($userID)
	{
		$status = get_user_meta($userID, '__wpdm_user_status', true);
		$status = $status ?: 'approved';
		return  $status;
	}

	function reviewUserStatus()
	{
        __::isAuthentic('wpdmusnonce',  WPDM_PRI_NONCE, 'manage_options', true);
		$id = __::query_var('user', 'int');
		$user = get_user_by('id', $id);
		$status = get_user_meta($id, '__wpdm_user_status', true);
		include Template::locate("review-user-status.php", __DIR__.'/views');
		die();
	}

	function reviewUserStatusEP($user)
	{
		$colors = ['' => 'success', 'approved' => 'success', 'pending' => 'warinng', 'declined' => 'danger', 'suspended' => 'danger'];
		$_status = get_user_meta($user->ID, '__wpdm_user_status', true);
		if(in_array($_status, ['', 'approved']))
			$status = '<i class="fa-solid fa-check-double"></i> '.__('Approved', WPDM_TEXT_DOMAIN);
		else if($_status === 'pending')
			$status = '<i class="fa-solid fa-clock"></i> '.__('Pending', WPDM_TEXT_DOMAIN);
		else if($_status === 'declined')
			$status = '<i class="fa-solid fa-times-circle"></i> '.__('Declined', WPDM_TEXT_DOMAIN);
		else
			$status = '<i class="fa-solid fa-ban"></i> Suspended';
		$url = admin_url('admin-ajax.php?action=wpdmdz_user_status&user='.$user->ID."&wpdmusnonce=".wp_create_nonce(WPDM_PRI_NONCE));

		?>
		<div class="w3eden">
			<div class="panel panel-default" style="display: inline-block">
				<div class="panel-body">
					<?php _e('Account status', WPDM_TEXT_DOMAIN); ?>: &nbsp;&nbsp;<strong id="usts-<?= $user->ID ?>"><span class="text-<?= $colors[$_status] ?>"><?= $status ?></span></strong>
				</div>
				<div class="panel-footer">
					<button type="button" onclick="WPDM.bootAlert('Review User Status', {url: '<?= $url ?>'}, 500);"  class="btn btn-block btn-info btn-sm"><?php _e('Update account status', WPDM_TEXT_DOMAIN); ?></button>
				</div>
			</div>
		</div>
		<?php
	}

	function updateUserStatus()
	{
		__::isAuthentic('__uscnonce', WPDM_PRI_NONCE, 'manage_options');
		$id = __::query_var('user', 'int');
		$user = get_user_by('id', $id);
		$params = [ 'to_email' => $user->user_email, 'name' => $user->display_name, 'username' => $user->user_login, 'display_name' => $user->display_name, 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'email' => $user->user_email ];

		$status = '';
		if(__::query_var('do') === 'approve') {
			//$params['subject'] = __("Congratulation! Your signup request is approved", WPDM_TEXT_DOMAIN);
			//$params['message'] = "Hello {$user->display_name},<br/>Congratulation!! Your signup request is approved! <hr/><a class='button green' href='".WPDM()->user->login->url()."'>Login</a>";
			update_user_meta($id, '__wpdm_user_status', 'approved');
			$status = 'approved';
		}
		if(__::query_var('do') === 'decline') {
			//wp_delete_user($id);
			update_user_meta($id, '__wpdm_user_status', 'declined');
			//$params['subject'] = __("Your signup request is declined", WPDM_TEXT_DOMAIN);
			//$params['message'] = "Hello {$user->display_name},<br/>Unfortunately we are unable to approve your signup!";
			//if(__::query_var('reason', 'txt') !== '')
			//	$params['message'] .= __MailUI::panel("Reason", [wpautop(__::query_var('reason', 'kses'))]);
            $params['reason'] = wpautop(__::query_var('reason', 'kses'));
			$status = 'declined';
		}
		if(__::query_var('do') === 'suspend') {
			//wp_delete_user($id);
			update_user_meta($id, '__wpdm_user_status', 'suspended');
			//$params['subject'] = __("Your account is suspended", WPDM_TEXT_DOMAIN);
			//$params['message'] = "Hello {$user->display_name},<br/>Unfortunately your account is suspended!";
			//if(__::query_var('reason', 'txt') !== '')
			//	$params['message'] .= __MailUI::panel("Reason", [wpautop(__::query_var('reason', 'kses'))]);
			$params['reason'] = wpautop(__::query_var('reason', 'kses'));
			$status = 'suspended';
		}
		Email::send("user-signup-{$status}", $params);
		wp_send_json(['success' => true, 'status' => $status, 'msg' => __('Operation executed successfully', WPDM_TEXT_DOMAIN)]);
		die();
	}
}
