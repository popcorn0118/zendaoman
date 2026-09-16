<?php
/**
 * Cron Jobs Settings Tab
 *
 * Admin UI for managing WPDM cron jobs.
 *
 * @package WPDM\Admin
 * @since 7.0.1
 */

if (!defined('ABSPATH')) exit;

use WPDM\__\CronJob;
use WPDM\__\CronJobs;

$counts = CronJob::getCounts();
$jobs = CronJob::getAll(['limit' => 50, 'orderby' => 'created_at', 'order' => 'DESC']);
$scheduledEvents = CronJobs::getScheduledEvents();
$cronUrl = CronJobs::getCronUrl();
$isWpCronDisabled = CronJobs::isWpCronDisabled();
?>

<style>
.wpdm-cron-stats {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}
.wpdm-cron-stat {
    flex: 1;
    text-align: center;
    padding: 15px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}
.wpdm-cron-stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
}
.wpdm-cron-stat-value.text-warning { color: #f59e0b; }
.wpdm-cron-stat-value.text-info { color: #3b82f6; }
.wpdm-cron-stat-value.text-success { color: #10b981; }
.wpdm-cron-stat-value.text-danger { color: #ef4444; }
.wpdm-cron-stat-label {
    font-size: 11px;
    color: #64748b;
    text-transform: uppercase;
    margin-top: 4px;
}
</style>

<!-- Stats -->
<div class="wpdm-cron-stats">
    <div class="wpdm-cron-stat">
        <div class="wpdm-cron-stat-value"><?php echo esc_html($counts['total']); ?></div>
        <div class="wpdm-cron-stat-label"><?php esc_html_e('Total Jobs', 'download-manager'); ?></div>
    </div>
    <div class="wpdm-cron-stat">
        <div class="wpdm-cron-stat-value text-warning"><?php echo esc_html($counts[CronJob::STATUS_PENDING]); ?></div>
        <div class="wpdm-cron-stat-label"><?php esc_html_e('Pending', 'download-manager'); ?></div>
    </div>
    <div class="wpdm-cron-stat">
        <div class="wpdm-cron-stat-value text-info"><?php echo esc_html($counts[CronJob::STATUS_RUNNING]); ?></div>
        <div class="wpdm-cron-stat-label"><?php esc_html_e('Running', 'download-manager'); ?></div>
    </div>
    <div class="wpdm-cron-stat">
        <div class="wpdm-cron-stat-value text-success"><?php echo esc_html($counts[CronJob::STATUS_COMPLETED]); ?></div>
        <div class="wpdm-cron-stat-label"><?php esc_html_e('Completed', 'download-manager'); ?></div>
    </div>
    <div class="wpdm-cron-stat">
        <div class="wpdm-cron-stat-value text-danger"><?php echo esc_html($counts[CronJob::STATUS_FAILED]); ?></div>
        <div class="wpdm-cron-stat-label"><?php esc_html_e('Failed', 'download-manager'); ?></div>
    </div>
</div>

<?php if ($isWpCronDisabled): ?>
<!-- WP Cron Disabled Warning -->
<div class="alert alert-warning">
    <strong><?php esc_html_e('WordPress Cron is Disabled', 'download-manager'); ?></strong><br>
    <?php esc_html_e('DISABLE_WP_CRON is set to true. You need to set up a real cron job to process the job queue. Use the URL below with a cron service or server cron.', 'download-manager'); ?>
</div>
<?php endif; ?>

<!-- External Cron URL -->
<div class="panel panel-default">
    <div class="panel-heading"><?php esc_html_e('External Cron URL', 'download-manager'); ?></div>
    <div class="panel-body">
        <p class="text-muted" style="margin-bottom: 10px;">
            <?php esc_html_e('Use this URL to trigger job processing from an external cron service. Add this to your server crontab or use a service like EasyCron.', 'download-manager'); ?>
        </p>
        <div class="input-group">
            <input type="text" id="wpdm-cron-url" value="<?php echo esc_attr($cronUrl); ?>" class="form-control" readonly>
            <div class="input-group-btn">
                <button onclick="WPDM.copy('wpdm-cron-url')" type="button" class="btn btn-secondary ttip" title="Copy">
                    <i class="fa fa-copy"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scheduled Events -->
<div class="panel panel-default">
    <div class="panel-heading"><?php esc_html_e('Scheduled WordPress Cron Events', 'download-manager'); ?></div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Hook', 'download-manager'); ?></th>
                <th><?php esc_html_e('Description', 'download-manager'); ?></th>
                <th><?php esc_html_e('Next Run', 'download-manager'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scheduledEvents as $hook => $event): ?>
            <tr>
                <td><code><?php echo esc_html($hook); ?></code></td>
                <td><?php echo esc_html($event['description']); ?></td>
                <td>
                    <?php if ($event['next_run']): ?>
                        <?php echo esc_html(date_i18n('M j, Y H:i:s', $event['next_run'])); ?>
                    <?php else: ?>
                        <span class="text-danger"><?php esc_html_e('Not scheduled', 'download-manager'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Actions -->
<p>
    <button type="button" class="btn btn-success" id="wpdm-run-cron">
        <i class="fa fa-play"></i> <?php esc_html_e('Run Job Queue Now', 'download-manager'); ?>
    </button>
</p>

<!-- Jobs Table -->
<div class="panel panel-default">
    <div class="panel-heading"><?php esc_html_e('Recent Jobs', 'download-manager'); ?></div>
    <?php if (empty($jobs)): ?>
    <div class="panel-body" style="text-align: center; padding: 40px;">
        <img src="<?php echo WPDM_ASSET_URL; ?>/images/no-job.png" style="width: 128px; padding: 20px;" /><br/>
        <p class="lead"><?php esc_html_e('No jobs in the queue yet.', 'download-manager'); ?></p>
    </div>
    <?php else: ?>
    <div style="overflow-x: auto;">
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php esc_html_e('ID', 'download-manager'); ?></th>
                <th><?php esc_html_e('Type', 'download-manager'); ?></th>
                <th><?php esc_html_e('Queue', 'download-manager'); ?></th>
                <th><?php esc_html_e('Status', 'download-manager'); ?></th>
                <th><?php esc_html_e('Attempts', 'download-manager'); ?></th>
                <th><?php esc_html_e('Execute At', 'download-manager'); ?></th>
                <th><?php esc_html_e('Actions', 'download-manager'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jobs as $job): ?>
            <tr id="job-<?php echo esc_attr($job->ID); ?>">
                <td>#<?php echo esc_html($job->ID); ?></td>
                <td>
                    <?php
                    $typeParts = explode('\\', $job->type);
                    echo '<code>' . esc_html(end($typeParts)) . '</code>';
                    ?>
                </td>
                <td><?php echo esc_html($job->queue ?? 'default'); ?></td>
                <td>
                    <?php
                    $status = $job->status ?? 'pending';
                    $statusClass = [
                        'pending' => 'btn-warning',
                        'running' => 'btn-info',
                        'completed' => 'btn-success',
                        'failed' => 'btn-danger',
                        'cancelled' => 'btn-secondary',
                    ][$status] ?? 'btn-secondary';
                    ?>
                    <span class="btn btn-xs <?php echo esc_attr($statusClass); ?>" style="cursor: default;">
                        <?php echo esc_html(ucfirst($status)); ?>
                    </span>
                </td>
                <td><?php echo esc_html(($job->attempts ?? 0) . '/' . ($job->max_attempts ?? 3)); ?></td>
                <td>
                    <?php
                    if ($job->execute_at) {
                        echo esc_html(date_i18n('M j, H:i', $job->execute_at));
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td>
                    <?php if (($job->status ?? 'pending') === 'pending'): ?>
                    <button type="button" class="btn btn-success btn-xs wpdm-job-action" data-action="run_now" data-job="<?php echo esc_attr($job->ID); ?>">
                        <?php esc_html_e('Run', 'download-manager'); ?>
                    </button>
                    <button type="button" class="btn btn-secondary btn-xs wpdm-job-action" data-action="cancel" data-job="<?php echo esc_attr($job->ID); ?>">
                        <?php esc_html_e('Cancel', 'download-manager'); ?>
                    </button>
                    <?php elseif (($job->status ?? '') === 'failed'): ?>
                    <button type="button" class="btn btn-info btn-xs wpdm-job-action" data-action="retry" data-job="<?php echo esc_attr($job->ID); ?>">
                        <?php esc_html_e('Retry', 'download-manager'); ?>
                    </button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-danger btn-xs wpdm-job-action" data-action="delete" data-job="<?php echo esc_attr($job->ID); ?>">
                        <?php esc_html_e('Delete', 'download-manager'); ?>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<script>
jQuery(function($) {
    var nonce = '<?php echo wp_create_nonce('wpdm_job_action'); ?>';
    var cronNonce = '<?php echo wp_create_nonce('wpdm_cron_nonce'); ?>';

    // Run cron button
    $('#wpdm-run-cron').on('click', function() {
        var $btn = $(this);
        if ($btn.prop('disabled')) return;

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Running...');

        $.post(ajaxurl, {
            action: 'wpdm_run_cron',
            nonce: cronNonce
        }, function(response) {
            $btn.prop('disabled', false).html('<i class="fa fa-play"></i> Run Job Queue Now');

            if (response.success) {
                var r = response.data.results;
                alert('Processed: ' + r.processed + ', Succeeded: ' + r.succeeded + ', Failed: ' + r.failed);
                location.reload();
            } else {
                alert(response.data.message || 'Failed to run cron');
            }
        }).fail(function() {
            $btn.prop('disabled', false).html('<i class="fa fa-play"></i> Run Job Queue Now');
            alert('Request failed');
        });
    });

    // Job actions
    $(document).on('click', '.wpdm-job-action', function() {
        var $btn = $(this);
        var action = $btn.data('action');
        var jobId = $btn.data('job');

        if (action === 'delete' && !confirm('<?php esc_html_e('Are you sure you want to delete this job?', 'download-manager'); ?>')) {
            return;
        }

        $btn.prop('disabled', true).text('...');

        $.post(ajaxurl, {
            action: 'wpdm_job_action',
            nonce: nonce,
            job_action: action,
            job_id: jobId
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data.message || 'Action failed');
                $btn.prop('disabled', false);
            }
        }).fail(function() {
            alert('Request failed');
            $btn.prop('disabled', false);
        });
    });
});
</script>
