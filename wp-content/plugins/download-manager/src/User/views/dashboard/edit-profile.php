<?php
/**
 * User Dashboard - Edit Profile
 * Enterprise-grade design
 */

if (!defined('ABSPATH')) die();

global $current_user, $wpdb;
$user = get_userdata($current_user->ID);
$public_profile = get_user_meta(get_current_user_id(), '__wpdm_public_profile', true);
$profile_pic = wpdm_valueof($public_profile, 'logo');
?>

<div id="edit-profile-form">
    <form method="post" id="edit_profile" name="contact_form" action="" class="form">
        <?php wp_nonce_field(NONCE_KEY, '__wpdm_epnonce'); ?>

        <!-- Basic Profile Card -->
        <div class="wpdm-card">
            <div class="wpdm-card-header">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <?php esc_html_e('Basic Profile', 'download-manager'); ?>
                </h3>
            </div>
            <div class="wpdm-card-body">
                <div class="wpdm-form-grid">
                    <div class="wpdm-form-group">
                        <label for="fname"><?php esc_html_e('Display Name', 'download-manager'); ?> <span class="wpdm-required">*</span></label>
                        <input type="text" class="wpdm-input" required value="<?php echo esc_attr($user->display_name); ?>" name="wpdm_profile[display_name]" id="fname">
                    </div>
                    <div class="wpdm-form-group">
                        <label for="username"><?php esc_html_e('Username', 'download-manager'); ?></label>
                        <input type="text" class="wpdm-input wpdm-input--readonly" value="<?php echo esc_attr($user->user_login); ?>" id="username" readonly>
                    </div>
                    <div class="wpdm-form-group">
                        <label for="title"><?php esc_html_e('Title', 'download-manager'); ?></label>
                        <input type="text" class="wpdm-input" name="wpdm_profile[title]" value="<?php echo esc_attr(get_user_meta($user->ID, '__wpdm_title', true)); ?>" id="title">
                    </div>
                    <div class="wpdm-form-group">
                        <label for="email"><?php esc_html_e('Email', 'download-manager'); ?> <span class="wpdm-required">*</span></label>
                        <input type="email" class="wpdm-input" required name="wpdm_profile[user_email]" value="<?php echo esc_attr($user->user_email); ?>" id="email">
                    </div>
                    <div class="wpdm-form-group wpdm-form-group--full">
                        <label for="description"><?php esc_html_e('About Me', 'download-manager'); ?></label>
                        <textarea class="wpdm-textarea" name="wpdm_profile[description]" id="description" rows="4"><?php echo esc_textarea(get_user_meta($user->ID, 'description', true)); ?></textarea>
                    </div>
                </div>
                <?php do_action('wpdm_update_profile_filed_html', $user); ?>
                <?php do_action('wpdm_update_profile_field_html', $user); ?>
            </div>
        </div>

        <!-- Profile Picture Card -->
        <div class="wpdm-card" style="margin-top: 1.5rem;">
            <div class="wpdm-card-header">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <?php esc_html_e('Profile Picture', 'download-manager'); ?>
                </h3>
            </div>
            <div class="wpdm-card-body">
                <div class="wpdm-avatar-upload">
                    <div class="wpdm-avatar-preview" id="avatar-preview">
                        <?php if ($profile_pic): ?>
                            <img src="<?php echo esc_url($profile_pic); ?>" alt="">
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <?php endif; ?>
                    </div>
                    <div class="wpdm-avatar-actions">
                        <input type="hidden" name="__wpdm_profile_pic" id="store-logo" value="<?php echo esc_attr($profile_pic); ?>">
                        <button type="button" class="wpdm-btn wpdm-btn--secondary wpdm-media-upload" rel="#store-logo">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            <?php esc_html_e('Upload Image', 'download-manager'); ?>
                        </button>
                        <button type="button" class="wpdm-btn wpdm-btn--danger wpdm-btn--sm" id="remove-avatar" style="<?php echo $profile_pic ? '' : 'display: none;'; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            <?php esc_html_e('Remove', 'download-manager'); ?>
                        </button>
                        <p class="wpdm-help-text"><?php esc_html_e('Recommended: Square image, at least 256x256 pixels', 'download-manager'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Card -->
        <div class="wpdm-card" style="margin-top: 1.5rem;">
            <div class="wpdm-card-header">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <?php esc_html_e('Update Password', 'download-manager'); ?>
                </h3>
            </div>
            <div class="wpdm-card-body">
                <div class="wpdm-form-grid">
                    <div class="wpdm-form-group">
                        <label for="new_pass"><?php esc_html_e('New Password', 'download-manager'); ?></label>
                        <input autocomplete="new-password" type="password" class="wpdm-input" value="" name="password" id="new_pass" placeholder="<?php esc_attr_e('Enter new password', 'download-manager'); ?>">
                    </div>
                    <div class="wpdm-form-group">
                        <label for="re_new_pass"><?php esc_html_e('Confirm Password', 'download-manager'); ?></label>
                        <input autocomplete="new-password" type="password" class="wpdm-input" value="" name="cpassword" id="re_new_pass" placeholder="<?php esc_attr_e('Re-enter new password', 'download-manager'); ?>">
                    </div>
                </div>
                <div class="wpdm-alert wpdm-alert--info" style="margin-top: 1rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <span><?php esc_html_e('Leave empty if you don\'t want to change your password', 'download-manager'); ?></span>
                </div>
            </div>
        </div>

        <?php do_action("wpdm_edit_profile_form"); ?>

        <!-- Submit Button -->
        <div class="wpdm-card wpdm-card--action" style="margin-top: 1.5rem;">
            <button type="submit" class="wpdm-btn wpdm-btn--primary wpdm-btn--lg" id="edit_profile_sbtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                <?php esc_html_e('Save Changes', 'download-manager'); ?>
            </button>
        </div>

    </form>
    <div id="edit-profile-msg"></div>
</div>
<div id="wpdm-fixed-top-center"></div>

<style>
.wpdm-avatar-upload {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.wpdm-avatar-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 4px solid #fff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    flex-shrink: 0;
}
.wpdm-avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.wpdm-avatar-preview svg {
    color: #94a3b8;
}
.wpdm-avatar-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
}
.wpdm-avatar-actions .wpdm-help-text {
    margin: 0;
}
</style>

<script>
jQuery(function ($) {
    // Form submission
    $('#edit_profile').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#edit_profile_sbtn');
        var originalHtml = $btn.html();
        $btn.html('<svg class="wpdm-spin" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> <?php echo esc_js(__('Saving...', 'download-manager')); ?>').prop('disabled', true);

        $(this).ajaxSubmit({
            success: function (res) {
                WPDM.notify(res.msg, res.type, '#wpdm-fixed-top-center', 10000);
                $btn.html(originalHtml).prop('disabled', false);
            },
            error: function() {
                $btn.html(originalHtml).prop('disabled', false);
                WPDM.notify('<?php echo esc_js(__('An error occurred. Please try again.', 'download-manager')); ?>', 'error', '#wpdm-fixed-top-center', 10000);
            }
        });
    });

    // Avatar preview update
    $('#store-logo').on('change', function() {
        var url = $(this).val();
        var $preview = $('#avatar-preview');
        var $removeBtn = $('#remove-avatar');

        if (url) {
            $preview.html('<img src="' + url + '" alt="">');
            $removeBtn.show();
        } else {
            $preview.html('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>');
            $removeBtn.hide();
        }
    });

    // Remove avatar
    $('#remove-avatar').on('click', function() {
        $('#store-logo').val('').trigger('change');
    });
});
</script>
