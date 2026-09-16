<div id="lock-options" class="tab-pane">
    <div class="wpdm-locks">

        <!-- Header -->
        <div class="wpdm-locks__header">
            <div class="wpdm-locks__header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
            <div class="wpdm-locks__header-text">
                <h4><?php _e("Download Protection", "download-manager"); ?></h4>
                <p><?php _e("Add one or more locks to protect your package download", "download-manager"); ?></p>
            </div>
        </div>

        <div class="wpdm-locks__list">

            <!-- Terms & Conditions Lock -->
            <div class="wpdm-lock-card <?php echo get_post_meta($post->ID, '__wpdm_terms_lock', true) == '1' ? 'wpdm-lock-card--active' : ''; ?>">
                <div class="wpdm-lock-card__header">
                    <div class="wpdm-lock-card__icon wpdm-lock-card__icon--terms">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <div class="wpdm-lock-card__info">
                        <span class="wpdm-lock-card__title"><?php _e("Terms &amp; Conditions", "download-manager"); ?></span>
                        <span class="wpdm-lock-card__desc"><?php _e("Require users to accept terms before downloading", "download-manager"); ?></span>
                    </div>
                    <label class="wpdm-toggle">
                        <input type="checkbox" class="wpdm-lock-toggle" data-target="terms-options" name="file[terms_lock]" value="1" <?php checked(get_post_meta($post->ID, '__wpdm_terms_lock', true), '1'); ?>>
                        <span class="wpdm-toggle__slider"></span>
                    </label>
                </div>
                <div class="wpdm-lock-card__body" id="terms-options" <?php echo get_post_meta($post->ID, '__wpdm_terms_lock', true) != '1' ? 'style="display:none"' : ''; ?>>
                    <div class="wpdm-lock-field">
                        <label class="wpdm-lock-field__label"><?php _e("Terms Page", "download-manager"); ?></label>
                        <?php wp_dropdown_pages(['name' => 'file[terms_page]', 'id' => 'wpdm_terms_page', 'show_option_none' => __('Use custom content below', 'download-manager'), 'selected' => get_post_meta($post->ID, '__wpdm_terms_page', true)]); ?>
                    </div>
                    <div class="wpdm-lock-field">
                        <label class="wpdm-lock-field__label"><?php _e("Terms Title", "download-manager"); ?></label>
                        <input type="text" class="wpdm-lock-field__input" name="file[terms_title]" value="<?php echo esc_attr(stripslashes(get_post_meta($post->ID, '__wpdm_terms_title', true))); ?>" placeholder="<?php esc_attr_e('Terms and Conditions', 'download-manager'); ?>">
                    </div>
                    <div class="wpdm-lock-field">
                        <label class="wpdm-lock-field__label"><?php _e("Terms &amp; Conditions", "download-manager"); ?></label>
                        <?php wp_editor(stripslashes(get_post_meta($post->ID, '__wpdm_terms_conditions', true)), "tc_z", ['textarea_name' => 'file[terms_conditions]', 'media_buttons' => false, 'textarea_rows' => 6]); ?>
                    </div>
                    <div class="wpdm-lock-field">
                        <label class="wpdm-lock-field__label"><?php _e("Checkbox Label", "download-manager"); ?></label>
                        <input type="text" class="wpdm-lock-field__input" name="file[terms_check_label]" value="<?php echo esc_attr(stripslashes(get_post_meta($post->ID, '__wpdm_terms_check_label', true))); ?>" placeholder="<?php esc_attr_e('I agree to the terms and conditions', 'download-manager'); ?>">
                    </div>
                </div>
            </div>

            <!-- Password Lock -->
            <div class="wpdm-lock-card <?php echo get_post_meta($post->ID, '__wpdm_password_lock', true) == '1' ? 'wpdm-lock-card--active' : ''; ?>">
                <div class="wpdm-lock-card__header">
                    <div class="wpdm-lock-card__icon wpdm-lock-card__icon--password">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>
                    </div>
                    <div class="wpdm-lock-card__info">
                        <span class="wpdm-lock-card__title"><?php _e("Password Protection", "download-manager"); ?></span>
                        <span class="wpdm-lock-card__desc"><?php _e("Require a password to unlock the download", "download-manager"); ?></span>
                    </div>
                    <label class="wpdm-toggle">
                        <input type="checkbox" class="wpdm-lock-toggle" data-target="password-options" name="file[password_lock]" value="1" <?php checked(get_post_meta($post->ID, '__wpdm_password_lock', true), '1'); ?>>
                        <span class="wpdm-toggle__slider"></span>
                    </label>
                </div>
                <div class="wpdm-lock-card__body" id="password-options" <?php echo get_post_meta($post->ID, '__wpdm_password_lock', true) != '1' ? 'style="display:none"' : ''; ?>>
                    <div class="wpdm-lock-field">
                        <label class="wpdm-lock-field__label">
                            <?php _e("Password(s)", "download-manager"); ?>
                            <span class="wpdm-lock-field__hint"><?php _e("Use [pass1][pass2] format for multiple passwords", "download-manager"); ?></span>
                        </label>
                        <div class="wpdm-lock-field__group">
                            <input type="text" class="wpdm-lock-field__input" name="file[password]" id="pps_z" value="<?php echo esc_attr(get_post_meta($post->ID, '__wpdm_password', true)); ?>" placeholder="<?php esc_attr_e('Enter password', 'download-manager'); ?>">
                            <button type="button" class="wpdm-lock-field__btn" onclick="return generatepass('pps_z');">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>
                                <?php _e("Generate", "download-manager"); ?>
                            </button>
                        </div>
                    </div>
                    <div class="wpdm-lock-field">
                        <label class="wpdm-lock-field__label">
                            <?php _e("Usage Limit", "download-manager"); ?>
                            <span class="wpdm-lock-field__hint"><?php _e("Available for Pro users only", "download-manager"); ?></span>
                        </label>
                        <div class="wpdm-lock-field__group wpdm-lock-field__group--compact">
                            <input type="text" class="wpdm-lock-field__input" disabled="disabled" value="<?php esc_attr_e('Available for Pro users only', 'download-manager'); ?>">
                            <span class="wpdm-lock-field__suffix"><?php _e("per password", "download-manager"); ?></span>
                        </div>
                    </div>
                    <div class="wpdm-lock-field" style="opacity:.55;">
                        <label class="wpdm-lock-checkbox">
                            <input type="checkbox" disabled="disabled" value="0">
                            <span class="wpdm-lock-checkbox__box"></span>
                            <span class="wpdm-lock-checkbox__label"><?php _e("Reset password usage count", "download-manager"); ?></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Captcha Lock -->
            <div class="wpdm-lock-card <?php echo get_post_meta($post->ID, '__wpdm_captcha_lock', true) == '1' ? 'wpdm-lock-card--active' : ''; ?>">
                <div class="wpdm-lock-card__header">
                    <div class="wpdm-lock-card__icon wpdm-lock-card__icon--captcha">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="wpdm-lock-card__info">
                        <span class="wpdm-lock-card__title"><?php _e("reCAPTCHA", "download-manager"); ?></span>
                        <span class="wpdm-lock-card__desc"><?php _e("Require reCAPTCHA verification before download", "download-manager"); ?></span>
                    </div>
                    <label class="wpdm-toggle">
                        <input type="checkbox" class="wpdm-lock-toggle" data-target="captcha-options" name="file[captcha_lock]" value="1" <?php checked(get_post_meta($post->ID, '__wpdm_captcha_lock', true), '1'); ?>>
                        <span class="wpdm-toggle__slider"></span>
                    </label>
                </div>
                <div class="wpdm-lock-card__body" id="captcha-options" <?php echo get_post_meta($post->ID, '__wpdm_captcha_lock', true) != '1' ? 'style="display:none"' : ''; ?>>
                    <?php if (!get_option('_wpdm_recaptcha_site_key') || !get_option('_wpdm_recaptcha_secret_key')) { ?>
                        <div class="wpdm-lock-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <span><?php printf(__('reCAPTCHA keys are not configured. <a href="%s">Configure here</a>', 'download-manager'), 'edit.php?post_type=wpdmpro&page=settings'); ?></span>
                        </div>
                    <?php } else { ?>
                        <div class="wpdm-lock-info">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            <span><?php _e("Users will be asked for reCAPTCHA verification before download.", "download-manager"); ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <?php do_action('wpdm_download_lock_option', $post); ?>

        </div>
    </div>
    <div class="clear"></div>
</div>

<script>
    jQuery(function ($) {
        $('#lock-options').on('change', '.wpdm-lock-toggle', function () {
            var $card = $(this).closest('.wpdm-lock-card');
            var $body = $('#' + $(this).data('target'));
            if (this.checked) {
                $card.addClass('wpdm-lock-card--active');
                $body.stop(true, true).slideDown(200);
            } else {
                $card.removeClass('wpdm-lock-card--active');
                $body.stop(true, true).slideUp(200);
            }
        });
    });
</script>

<!-- Generate password dialog (rendered via WPDM.dialog) -->
<script type="text/template" id="generatepass-tpl">
    <div class="pfs panel panel-default card card-default" style="border-radius:0;box-shadow: none;border: 0 !important;margin: 0;">
        <div class="panel-heading card-header" style="border-radius:0;"><b><?php _e( "Password Lenght & Count" , "download-manager" ); ?></b></div>
        <div class="panel-body card-body">
            <div class="row">
                <div class="col-md-6">
                    <b><?php _e( "Number of passwords:" , "download-manager" ); ?></b><Br/>
                    <input class="form-control" type="number" id='pcnt' value="">
                </div>
                <div  class="col-md-6">
                    <b><?php _e( "Password length:" , "download-manager" ); ?></b><Br/>
                    <input  class="form-control" type="number" id='ncp' value="">
                </div>
            </div>
        </div>
        <div class="panel-heading card-header" style="border-radius:0;border-top: 1px solid #ddd"><b><?php _e( "Password Strength" , "download-manager" ); ?></b></div>
        <div class="panel-body card-body">
            <div class="row">
                <div class="col-md-6">
                    <input style="padding:0;" type="range" min="1" max="4" value="2" class="form-control" id="passtrn">
                    <div class="row">
                        <div class="col-md-6" style="color: var(--color-danger);"><?php echo __( "Weak", "download-manager" ) ?></div>
                        <div class="col-md-6 text-right" style="color: var(--color-success);"><?php echo __( "Strong", "download-manager" ) ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <input type="button" id="gpsc" class="btn btn-secondary btn-lg btn-block" value="Generate" />
                </div>
            </div>
        </div>
        <div class="panel-heading card-header" style="border-radius:0;border-top: 1px solid #dddddd"><b><?php _e( "Generated Passwords" , "download-manager" ); ?></b></div>
        <div class="panel-body card-body">
            <textarea id="ps" class="form-control"></textarea>
        </div>
    </div>
    <div class="text-right" style="margin-top: 12px">
        <input type="button" id="pins" class="btn btn-primary btn-lg btn-block" value="<?php esc_attr_e( "Insert Password(s)" , "download-manager" ); ?>" />
    </div>
</script>
