<?php
/**
 * Activity Reports Settings Page
 *
 * @package WPDM\Admin\views\settings
 * @since 7.0.2
 */

if (!defined('ABSPATH')) die('!');

// Get current settings
$enabled = (int) get_option('__wpdm_activity_report_enabled', 0);
$frequency = get_option('__wpdm_activity_report_frequency', 'weekly');
$day = (int) get_option('__wpdm_activity_report_day', 1);
$hour = (int) get_option('__wpdm_activity_report_hour', 9);
$includeAdmin = (int) get_option('__wpdm_activity_report_admin', 1);
$additionalEmails = get_option('__wpdm_activity_report_emails', '');
$sections = get_option('__wpdm_activity_report_sections', [
    'download_summary',
    'top_packages',
    'user_activity',
    'category_breakdown',
]);

if (!is_array($sections)) {
    $sections = ['download_summary', 'top_packages', 'user_activity'];
}

// Check if Premium Packages is active
$hasPremiumPackages = class_exists('\\WPDMPremiumPackage') || function_exists('wpdmpp_effective_price');

// Available sections
$availableSections = [
    'download_summary' => [
        'label' => __('Download Summary', 'download-manager'),
        'description' => __('Total downloads, comparison with previous period, daily average', 'download-manager'),
    ],
    'top_packages' => [
        'label' => __('Top Packages', 'download-manager'),
        'description' => __('Most downloaded packages this period', 'download-manager'),
    ],
    'trending_packages' => [
        'label' => __('Trending Packages', 'download-manager'),
        'description' => __('Packages with biggest growth percentage', 'download-manager'),
    ],
    'user_activity' => [
        'label' => __('User Activity', 'download-manager'),
        'description' => __('New users, unique downloaders, top downloaders', 'download-manager'),
    ],
    'category_breakdown' => [
        'label' => __('Category Breakdown', 'download-manager'),
        'description' => __('Downloads per category with percentage share', 'download-manager'),
    ],
    'revenue_summary' => [
        'label' => __('Revenue Summary', 'download-manager'),
        'description' => __('Total revenue, orders, top selling products', 'download-manager'),
        'requires' => 'premium',
    ],
    'storage_usage' => [
        'label' => __('Storage Usage', 'download-manager'),
        'description' => __('Total storage, file count, largest packages', 'download-manager'),
    ],
];

// Days of week
$daysOfWeek = [
    1 => __('Monday', 'download-manager'),
    2 => __('Tuesday', 'download-manager'),
    3 => __('Wednesday', 'download-manager'),
    4 => __('Thursday', 'download-manager'),
    5 => __('Friday', 'download-manager'),
    6 => __('Saturday', 'download-manager'),
    7 => __('Sunday', 'download-manager'),
];

// Get admin email
$adminEmail = get_option('admin_email');
?>

<style>
    .wpdm-ar-toggle {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
        vertical-align: middle;
    }
    .wpdm-ar-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .wpdm-ar-toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        border-radius: 26px;
        transition: 0.3s;
    }
    .wpdm-ar-toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: 0.3s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .wpdm-ar-toggle input:checked + .wpdm-ar-toggle-slider {
        background-color: #6366f1;
    }
    .wpdm-ar-toggle input:checked + .wpdm-ar-toggle-slider:before {
        transform: translateX(22px);
    }
    .wpdm-ar-section-card {
        display: flex;
        align-items: flex-start;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.15s;
    }
    .wpdm-ar-section-card:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .wpdm-ar-section-card.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .wpdm-ar-section-card label {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        cursor: pointer;
        flex: 1;
    }
    .wpdm-ar-section-card.disabled label {
        cursor: not-allowed;
    }
    .wpdm-ar-section-info {
        flex: 1;
    }
    .wpdm-ar-section-title {
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 2px 0;
    }
    .wpdm-ar-section-desc {
        font-size: 12px;
        color: #64748b;
        margin: 0;
    }
    .wpdm-ar-badge {
        display: inline-block;
        padding: 2px 6px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        border-radius: 4px;
        margin-left: 8px;
    }
    .wpdm-ar-badge-premium {
        background: #fef3c7;
        color: #92400e;
    }
    .wpdm-ar-schedule-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    @media (max-width: 768px) {
        .wpdm-ar-schedule-grid {
            grid-template-columns: 1fr;
        }
    }
    .wpdm-ar-test-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #475569;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
    }
    .wpdm-ar-test-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #1e293b;
    }
    .wpdm-ar-test-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .wpdm-ar-test-btn .fa-spin {
        animation: fa-spin 1s linear infinite;
    }
    @keyframes fa-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .wpdm-ar-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        margin-top: 16px;
    }
    .wpdm-ar-status-success {
        background: #d1fae5;
        color: #065f46;
    }
    .wpdm-ar-status-error {
        background: #fee2e2;
        color: #991b1b;
    }
    .wpdm-ar-info-box {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    .wpdm-ar-info-box-icon {
        color: #3b82f6;
        font-size: 18px;
        flex-shrink: 0;
    }
    .wpdm-ar-info-box-content {
        font-size: 13px;
        color: #1e40af;
        line-height: 1.5;
    }
</style>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e('Activity Reports', 'download-manager'); ?></div>
    <div class="panel-body">
        <div class="wpdm-ar-info-box">
            <i class="fas fa-info-circle wpdm-ar-info-box-icon"></i>
            <div class="wpdm-ar-info-box-content">
                <?php _e('Activity reports provide periodic summaries of your download statistics, user activity, and revenue (if applicable). Reports are sent via email to the configured recipients.', 'download-manager'); ?>
            </div>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                <label class="wpdm-ar-toggle">
                    <input type="hidden" name="__wpdm_activity_report_enabled" value="0" />
                    <input type="checkbox" name="__wpdm_activity_report_enabled" value="1" <?php checked($enabled, 1); ?> id="wpdm-ar-enable" />
                    <span class="wpdm-ar-toggle-slider"></span>
                </label>
                <span style="font-weight: 600; color: #1e293b;"><?php _e('Enable Activity Reports', 'download-manager'); ?></span>
            </label>
        </div>
    </div>
</div>

<div id="wpdm-ar-settings" style="<?php echo !$enabled ? 'opacity: 0.5; pointer-events: none;' : ''; ?>">

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e('Schedule', 'download-manager'); ?></div>
        <div class="panel-body">
            <div class="wpdm-ar-schedule-grid">
                <div class="form-group">
                    <label><?php _e('Frequency', 'download-manager'); ?></label>
                    <select name="__wpdm_activity_report_frequency" class="form-control" id="wpdm-ar-frequency">
                        <option value="weekly" <?php selected($frequency, 'weekly'); ?>><?php _e('Weekly', 'download-manager'); ?></option>
                        <option value="monthly" <?php selected($frequency, 'monthly'); ?>><?php _e('Monthly', 'download-manager'); ?></option>
                    </select>
                </div>

                <div class="form-group" id="wpdm-ar-day-group">
                    <label id="wpdm-ar-day-label"><?php _e('Send Day', 'download-manager'); ?></label>
                    <select name="__wpdm_activity_report_day" class="form-control" id="wpdm-ar-day">
                        <?php
                        // Weekly days
                        foreach ($daysOfWeek as $dayNum => $dayName) {
                            echo '<option value="' . $dayNum . '" data-type="weekly"' . selected($day, $dayNum, false) . '>' . esc_html($dayName) . '</option>';
                        }
                        // Monthly days
                        for ($i = 1; $i <= 28; $i++) {
                            $suffix = 'th';
                            if ($i === 1 || $i === 21) $suffix = 'st';
                            elseif ($i === 2 || $i === 22) $suffix = 'nd';
                            elseif ($i === 3 || $i === 23) $suffix = 'rd';
                            echo '<option value="' . $i . '" data-type="monthly"' . selected($day, $i, false) . '>' . $i . $suffix . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><?php _e('Send Time', 'download-manager'); ?></label>
                    <select name="__wpdm_activity_report_hour" class="form-control">
                        <?php for ($h = 0; $h < 24; $h++): ?>
                            <option value="<?php echo $h; ?>" <?php selected($hour, $h); ?>>
                                <?php echo sprintf('%02d:00', $h); ?> (<?php echo date('g:i A', strtotime(sprintf('%02d:00', $h))); ?>)
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e('Recipients', 'download-manager'); ?></div>
        <div class="panel-body">
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="hidden" name="__wpdm_activity_report_admin" value="0" />
                    <input type="checkbox" name="__wpdm_activity_report_admin" value="1" <?php checked($includeAdmin, 1); ?> />
                    <span><?php printf(__('Site Admin (%s)', 'download-manager'), '<code>' . esc_html($adminEmail) . '</code>'); ?></span>
                </label>
            </div>

            <div class="form-group">
                <label><?php _e('Additional Email Addresses', 'download-manager'); ?></label>
                <input type="text" name="__wpdm_activity_report_emails" class="form-control" value="<?php echo esc_attr($additionalEmails); ?>" placeholder="email1@example.com, email2@example.com" />
                <em class="note"><?php _e('Comma-separated list of additional email addresses to receive reports.', 'download-manager'); ?></em>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e('Report Sections', 'download-manager'); ?></div>
        <div class="panel-body">
            <?php foreach ($availableSections as $key => $section):
                $isDisabled = isset($section['requires']) && $section['requires'] === 'premium' && !$hasPremiumPackages;
                $isChecked = in_array($key, $sections) && !$isDisabled;
            ?>
            <div class="wpdm-ar-section-card <?php echo $isDisabled ? 'disabled' : ''; ?>">
                <label>
                    <input type="checkbox"
                           name="__wpdm_activity_report_sections[]"
                           value="<?php echo esc_attr($key); ?>"
                           <?php checked($isChecked, true); ?>
                           <?php echo $isDisabled ? 'disabled' : ''; ?> />
                    <div class="wpdm-ar-section-info">
                        <p class="wpdm-ar-section-title">
                            <?php echo esc_html($section['label']); ?>
                            <?php if (isset($section['requires']) && $section['requires'] === 'premium'): ?>
                                <span class="wpdm-ar-badge wpdm-ar-badge-premium"><?php _e('Premium Packages', 'download-manager'); ?></span>
                            <?php endif; ?>
                        </p>
                        <p class="wpdm-ar-section-desc"><?php echo esc_html($section['description']); ?></p>
                    </div>
                </label>
            </div>
            <?php endforeach; ?>

            <?php if (!$hasPremiumPackages): ?>
            <em class="note" style="display: block; margin-top: 12px;">
                <i class="fas fa-info-circle"></i>
                <?php _e('Revenue Summary requires the Premium Packages add-on to be active.', 'download-manager'); ?>
            </em>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e('Test Report', 'download-manager'); ?></div>
        <div class="panel-body">
            <p style="margin: 0 0 16px 0; color: #64748b;">
                <?php _e('Send a test report to verify your configuration is working correctly.', 'download-manager'); ?>
            </p>

            <div style="display: flex; align-items: center; gap: 12px;">
                <input type="email" id="wpdm-ar-test-email" class="form-control" style="max-width: 300px;" value="<?php echo esc_attr($adminEmail); ?>" placeholder="<?php esc_attr_e('Email address', 'download-manager'); ?>" />
                <button type="button" id="wpdm-ar-send-test" class="wpdm-ar-test-btn">
                    <i class="fas fa-paper-plane"></i>
                    <?php _e('Send Test Report', 'download-manager'); ?>
                </button>
            </div>

            <div id="wpdm-ar-test-result"></div>
        </div>
    </div>

</div>

<script>
jQuery(function($) {
    // Toggle settings visibility
    $('#wpdm-ar-enable').on('change', function() {
        if (this.checked) {
            $('#wpdm-ar-settings').css({ opacity: 1, pointerEvents: 'auto' });
        } else {
            $('#wpdm-ar-settings').css({ opacity: 0.5, pointerEvents: 'none' });
        }
    });

    // Update day options based on frequency
    function updateDayOptions() {
        var frequency = $('#wpdm-ar-frequency').val();
        var $daySelect = $('#wpdm-ar-day');
        var $dayLabel = $('#wpdm-ar-day-label');

        $daySelect.find('option').hide();
        $daySelect.find('option[data-type="' + frequency + '"]').show();

        // Select first visible option if current is hidden
        if ($daySelect.find('option:selected').is(':hidden')) {
            $daySelect.find('option[data-type="' + frequency + '"]:first').prop('selected', true);
        }

        if (frequency === 'weekly') {
            $dayLabel.text('<?php _e('Send Day', 'download-manager'); ?>');
        } else {
            $dayLabel.text('<?php _e('Day of Month', 'download-manager'); ?>');
        }
    }

    $('#wpdm-ar-frequency').on('change', updateDayOptions);
    updateDayOptions(); // Initial setup

    // Send test report
    $('#wpdm-ar-send-test').on('click', function() {
        var $btn = $(this);
        var $result = $('#wpdm-ar-test-result');
        var email = $('#wpdm-ar-test-email').val().trim();

        if (!email) {
            $result.html('<div class="wpdm-ar-status wpdm-ar-status-error"><i class="fas fa-exclamation-circle"></i> <?php _e('Please enter an email address.', 'download-manager'); ?></div>');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-sync fa-spin"></i> <?php _e('Sending...', 'download-manager'); ?>');
        $result.html('');

        $.post(ajaxurl, {
            action: 'wpdm_send_test_activity_report',
            email: email,
            nonce: '<?php echo wp_create_nonce('wpdm_activity_report_test'); ?>'
        }, function(response) {
            $btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> <?php _e('Send Test Report', 'download-manager'); ?>');

            if (response.success) {
                $result.html('<div class="wpdm-ar-status wpdm-ar-status-success"><i class="fas fa-check-circle"></i> ' + response.data.message + '</div>');
            } else {
                $result.html('<div class="wpdm-ar-status wpdm-ar-status-error"><i class="fas fa-exclamation-circle"></i> ' + (response.data ? response.data.message : '<?php _e('Failed to send test report.', 'download-manager'); ?>') + '</div>');
            }
        }).fail(function() {
            $btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> <?php _e('Send Test Report', 'download-manager'); ?>');
            $result.html('<div class="wpdm-ar-status wpdm-ar-status-error"><i class="fas fa-exclamation-circle"></i> <?php _e('Request failed. Please try again.', 'download-manager'); ?></div>');
        });
    });

    // After settings save, reschedule job
    WPDM.addAction("wpdm_save_settings", function(page, data) {
        if (page === 'activity-reports') {
            $.post(ajaxurl, {
                action: 'wpdm_reschedule_activity_report',
                nonce: '<?php echo wp_create_nonce('wpdm_reschedule_activity_report'); ?>'
            });
        }
    });
});
</script>
