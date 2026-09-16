<?php
/**
 * Changelog Metabox Template
 * Displays and manages package changelog entries
 */
if (!defined('ABSPATH')) die('!');

$changelog = get_post_meta($post->ID, '__wpdm_changelog', true);
if (!is_array($changelog)) {
    $changelog = [];
}

// Sort by timestamp descending (newest first)
usort($changelog, function($a, $b) {
    $ts_a = isset($a['timestamp']) ? $a['timestamp'] : 0;
    $ts_b = isset($b['timestamp']) ? $b['timestamp'] : 0;
    return $ts_b - $ts_a;
});
?>

<style>
    .wpdm-changelog-metabox {
        --cl-text: #1e293b;
        --cl-text-muted: #64748b;
        --cl-border: #e2e8f0;
        --cl-bg: #f8fafc;
        --cl-radius: 4px;
    }

    .wpdm-changelog-metabox * {
        box-sizing: border-box;
    }

    /* Existing Changelogs List */
    .wpdm-changelog-list {
        margin-bottom: 20px;
    }

    .wpdm-changelog-item {
        background: #fff;
        border: 1px solid var(--cl-border);
        border-radius: var(--cl-radius);
        margin-bottom: 12px;
        overflow: hidden;
    }

    .wpdm-changelog-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: var(--cl-bg);
        border-bottom: 1px solid var(--cl-border);
        cursor: pointer;
    }

    .wpdm-changelog-header:hover {
        background: #f1f5f9;
    }

    .wpdm-changelog-version {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--admin-color, #6366f1);
        color: #fff;
        padding: 4px 10px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
    }

    .wpdm-changelog-date {
        font-size: 13px;
        color: var(--cl-text-muted);
    }

    .wpdm-changelog-actions {
        margin-left: auto;
        display: flex;
        gap: 6px;
    }

    /* Action buttons (edit/delete) */
    .wpdm-changelog-action-btn {
        width: 28px;
        height: 28px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #64748b;
        cursor: pointer;
        opacity: 0;
        transform: scale(0.9);
        transition: all 150ms ease;
    }

    .wpdm-changelog-action-btn svg {
        width: 14px;
        height: 14px;
    }

    .wpdm-changelog-header:hover .wpdm-changelog-action-btn {
        opacity: 1;
        transform: scale(1);
    }

    .wpdm-changelog-action-btn:hover {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border-color: #a5b4fc;
        color: #4f46e5;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.15);
    }

    .wpdm-changelog-action-btn--delete:hover {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border-color: #fca5a5;
        color: #dc2626;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.15);
    }

    .wpdm-changelog-btn--toggle {
        background: transparent;
        border: none;
        color: var(--cl-text-muted);
        padding: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .wpdm-changelog-btn--toggle svg {
        width: 16px;
        height: 16px;
        transition: transform 150ms ease;
    }

    .wpdm-changelog-item.is-collapsed .wpdm-changelog-btn--toggle svg {
        transform: rotate(-90deg);
    }

    .wpdm-changelog-body {
        padding: 16px;
        display: block;
    }

    .wpdm-changelog-item.is-collapsed .wpdm-changelog-body {
        display: none;
    }

    .wpdm-changelog-content {
        font-size: 14px;
        line-height: 1.6;
        color: var(--cl-text);
    }

    .wpdm-changelog-content ul,
    .wpdm-changelog-content ol {
        margin: 8px 0;
        padding-left: 20px;
    }

    .wpdm-changelog-content li {
        margin-bottom: 4px;
    }

    /* Edit Form (hidden by default) */
    .wpdm-changelog-edit-form {
        padding: 16px;
        background: #fffbeb;
        border-top: 1px solid #fef3c7;
        display: none;
    }

    .wpdm-changelog-item.is-editing .wpdm-changelog-edit-form {
        display: block;
    }

    .wpdm-changelog-item.is-editing .wpdm-changelog-body {
        display: none;
    }

    /* Add New Form */
    .wpdm-changelog-add {
        background: #fff;
        border: 2px dashed var(--cl-border);
        border-radius: var(--cl-radius);
        padding: 20px;
    }

    .wpdm-changelog-add-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--cl-text);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .wpdm-changelog-add-title svg {
        width: 18px;
        height: 18px;
        color: var(--admin-color, #6366f1);
    }

    .wpdm-changelog-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }

    .wpdm-changelog-form-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .wpdm-changelog-form-group--full {
        grid-column: 1 / -1;
    }

    .wpdm-changelog-label {
        font-size: 12px;
        font-weight: 500;
        color: var(--cl-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .wpdm-changelog-metabox .form-control {
        width: 100%;
    }

    .wpdm-changelog-metabox textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .wpdm-changelog-empty {
        text-align: center;
        padding: 24px;
        color: var(--cl-text-muted);
        font-size: 14px;
        background: var(--cl-bg);
        border-radius: var(--cl-radius);
        margin-bottom: 20px;
    }

    .wpdm-changelog-empty svg {
        width: 32px;
        height: 32px;
        margin-bottom: 8px;
        opacity: 0.5;
    }

    .wpdm-changelog-hint {
        font-size: 12px;
        color: var(--cl-text-muted);
        margin-top: 8px;
    }

    .wpdm-changelog-metabox .btn i {
        margin-right: 5px;
    }

    .wpdm-changelog-metabox .btn-group-actions {
        margin-top: 12px;
    }

    @media (max-width: 782px) {
        .wpdm-changelog-form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="wpdm-changelog-metabox w3eden">

    <!-- Existing Changelogs -->
    <?php if (!empty($changelog)): ?>
    <div class="wpdm-changelog-list">
        <?php foreach ($changelog as $index => $entry):
            $entry_id = isset($entry['id']) ? $entry['id'] : 'cl_' . $index;
            $version = isset($entry['version']) ? esc_attr($entry['version']) : '';
            $date = isset($entry['date']) ? esc_attr($entry['date']) : '';
            $changes = isset($entry['changes']) ? $entry['changes'] : '';
            $timestamp = isset($entry['timestamp']) ? $entry['timestamp'] : 0;
        ?>
        <div class="wpdm-changelog-item" data-id="<?php echo esc_attr($entry_id); ?>">
            <div class="wpdm-changelog-header" onclick="wpdmToggleChangelog(this)">
                <button type="button" class="wpdm-changelog-btn wpdm-changelog-btn--toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <span class="wpdm-changelog-version">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                    </svg>
                    v<?php echo esc_html($version); ?>
                </span>
                <span class="wpdm-changelog-date"><?php echo esc_html($date); ?></span>
                <div class="wpdm-changelog-actions" onclick="event.stopPropagation()">
                    <button type="button" class="wpdm-changelog-action-btn" onclick="wpdmEditChangelog('<?php echo esc_attr($entry_id); ?>')" title="<?php esc_attr_e('Edit', 'download-manager'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button type="button" class="wpdm-changelog-action-btn wpdm-changelog-action-btn--delete" onclick="wpdmDeleteChangelog('<?php echo esc_attr($entry_id); ?>')" title="<?php esc_attr_e('Delete', 'download-manager'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- View Mode -->
            <div class="wpdm-changelog-body">
                <div class="wpdm-changelog-content">
                    <?php echo wp_kses_post($changes); ?>
                </div>
            </div>

            <!-- Edit Mode -->
            <div class="wpdm-changelog-edit-form">
                <div class="wpdm-changelog-form-row">
                    <div class="wpdm-changelog-form-group">
                        <label class="wpdm-changelog-label"><?php _e('Version', 'download-manager'); ?></label>
                        <input type="text" class="form-control wpdm-edit-version" value="<?php echo esc_attr($version); ?>" placeholder="1.0.0">
                    </div>
                    <div class="wpdm-changelog-form-group">
                        <label class="wpdm-changelog-label"><?php _e('Date', 'download-manager'); ?></label>
                        <input type="date" class="form-control wpdm-edit-date" value="<?php echo esc_attr($date); ?>">
                    </div>
                </div>
                <div class="wpdm-changelog-form-group wpdm-changelog-form-group--full">
                    <label class="wpdm-changelog-label"><?php _e('Changes', 'download-manager'); ?></label>
                    <textarea class="form-control wpdm-edit-changes"><?php echo esc_textarea($changes); ?></textarea>
                </div>
                <div class="btn-group-actions">
                    <button type="button" class="btn btn-primary" onclick="wpdmSaveEditChangelog('<?php echo esc_attr($entry_id); ?>')">
                        <i class="fas fa-check"></i> <?php _e('Save Changes', 'download-manager'); ?>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="wpdmCancelEditChangelog('<?php echo esc_attr($entry_id); ?>')">
                        <?php _e('Cancel', 'download-manager'); ?>
                    </button>
                </div>
            </div>

            <!-- Hidden inputs for form submission -->
            <input type="hidden" name="file[changelog][<?php echo $index; ?>][id]" value="<?php echo esc_attr($entry_id); ?>" class="wpdm-cl-id">
            <input type="hidden" name="file[changelog][<?php echo $index; ?>][version]" value="<?php echo esc_attr($version); ?>" class="wpdm-cl-version">
            <input type="hidden" name="file[changelog][<?php echo $index; ?>][date]" value="<?php echo esc_attr($date); ?>" class="wpdm-cl-date">
            <input type="hidden" name="file[changelog][<?php echo $index; ?>][changes]" value="<?php echo esc_attr($changes); ?>" class="wpdm-cl-changes">
            <input type="hidden" name="file[changelog][<?php echo $index; ?>][timestamp]" value="<?php echo esc_attr($timestamp); ?>" class="wpdm-cl-timestamp">
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="wpdm-changelog-empty">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>
        <p><?php _e('No changelog entries yet', 'download-manager'); ?></p>
    </div>
    <?php endif; ?>

    <!-- Add New Changelog -->
    <div class="wpdm-changelog-add">
        <div class="wpdm-changelog-add-title">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <?php _e('Add New Changelog Entry', 'download-manager'); ?>
        </div>

        <div class="wpdm-changelog-form-row">
            <div class="wpdm-changelog-form-group">
                <label class="wpdm-changelog-label"><?php _e('Version', 'download-manager'); ?></label>
                <input type="text" id="wpdm-new-cl-version" class="form-control" value="" placeholder="1.0.0">
            </div>
            <div class="wpdm-changelog-form-group">
                <label class="wpdm-changelog-label"><?php _e('Date', 'download-manager'); ?></label>
                <input type="date" id="wpdm-new-cl-date" class="form-control" value="<?php echo wp_date('Y-m-d'); ?>">
            </div>
        </div>

        <div class="wpdm-changelog-form-group wpdm-changelog-form-group--full form-group">
            <label class="wpdm-changelog-label"><?php _e('Changes', 'download-manager'); ?></label>
            <?php
            wp_editor('', 'wpdm-new-cl-changes', array(
                'textarea_name' => 'wpdm_changelog_changes_temp',
                'textarea_rows' => 8,
                'media_buttons' => false,
                'teeny'         => true,
                'quicktags'     => array('buttons' => 'strong,em,ul,ol,li,link'),
                'tinymce'       => array(
                    'toolbar1' => 'bold,italic,bullist,numlist,link,unlink,undo,redo',
                    'toolbar2' => '',
                ),
            ));
            ?>
        </div>

        <div>
            <button type="button" class="btn btn-primary" onclick="wpdmAddChangelog()">
                <i class="fas fa-plus"></i> <?php _e('Add Changelog Entry', 'download-manager'); ?>
            </button>
        </div>

    </div>

    <!-- Hidden container for new entries (will be populated by JS) -->
    <div id="wpdm-changelog-new-entries"></div>

</div>

<script>
(function($) {
    'use strict';

    var newEntryIndex = <?php echo count($changelog); ?>;
    var $versionField = $('input[name="file[version]"]');
    var $clVersionField = $('#wpdm-new-cl-version');

    // Sync version from Package Settings
    function syncVersion() {
        var version = $versionField.val();
        if (version && !$clVersionField.data('user-modified')) {
            $clVersionField.val(version);
        }
    }

    // Initial sync
    syncVersion();

    // Sync on version field change
    $versionField.on('input change', syncVersion);

    // Track if user manually modified the changelog version
    $clVersionField.on('input', function() {
        $(this).data('user-modified', true);
    });

    // Toggle changelog item expand/collapse
    window.wpdmToggleChangelog = function(header) {
        var $item = $(header).closest('.wpdm-changelog-item');
        if (!$item.hasClass('is-editing')) {
            $item.toggleClass('is-collapsed');
        }
    };

    // Edit changelog entry
    window.wpdmEditChangelog = function(id) {
        var $item = $('.wpdm-changelog-item[data-id="' + id + '"]');
        $item.removeClass('is-collapsed').addClass('is-editing');
    };

    // Cancel edit
    window.wpdmCancelEditChangelog = function(id) {
        var $item = $('.wpdm-changelog-item[data-id="' + id + '"]');
        $item.removeClass('is-editing');

        // Restore original values
        var $hidden = $item.find('input[type="hidden"]');
        $item.find('.wpdm-edit-version').val($item.find('.wpdm-cl-version').val());
        $item.find('.wpdm-edit-date').val($item.find('.wpdm-cl-date').val());
        $item.find('.wpdm-edit-changes').val($item.find('.wpdm-cl-changes').val());
    };

    // Save edited changelog
    window.wpdmSaveEditChangelog = function(id) {
        var $item = $('.wpdm-changelog-item[data-id="' + id + '"]');

        var version = $item.find('.wpdm-edit-version').val();
        var date = $item.find('.wpdm-edit-date').val();
        var changes = $item.find('.wpdm-edit-changes').val();

        // Update hidden fields
        $item.find('.wpdm-cl-version').val(version);
        $item.find('.wpdm-cl-date').val(date);
        $item.find('.wpdm-cl-changes').val(changes);

        // Update display
        $item.find('.wpdm-changelog-version').html(
            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg> v' + $('<div>').text(version).html()
        );
        $item.find('.wpdm-changelog-date').text(date);
        $item.find('.wpdm-changelog-content').html(changes);

        $item.removeClass('is-editing');
    };

    // Delete changelog entry
    window.wpdmDeleteChangelog = function(id) {
        if (confirm('<?php echo esc_js(__('Are you sure you want to delete this changelog entry?', 'download-manager')); ?>')) {
            $('.wpdm-changelog-item[data-id="' + id + '"]').fadeOut(300, function() {
                $(this).remove();
                reindexChangelogs();
            });
        }
    };

    // Helper function to get TinyMCE content
    function getEditorContent(editorId) {
        var content = '';
        if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
            content = tinymce.get(editorId).getContent();
        } else {
            content = $('#' + editorId).val();
        }
        return content.trim();
    }

    // Helper function to set TinyMCE content
    function setEditorContent(editorId, content) {
        if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
            tinymce.get(editorId).setContent(content);
        }
        $('#' + editorId).val(content);
    }

    // Add new changelog entry
    window.wpdmAddChangelog = function() {
        var version = $('#wpdm-new-cl-version').val().trim();
        var date = $('#wpdm-new-cl-date').val();
        var changes = getEditorContent('wpdm-new-cl-changes');

        if (!version) {
            alert('<?php echo esc_js(__('Please enter a version number', 'download-manager')); ?>');
            $('#wpdm-new-cl-version').focus();
            return;
        }

        if (!changes) {
            alert('<?php echo esc_js(__('Please describe the changes', 'download-manager')); ?>');
            if (typeof tinymce !== 'undefined' && tinymce.get('wpdm-new-cl-changes')) {
                tinymce.get('wpdm-new-cl-changes').focus();
            } else {
                $('#wpdm-new-cl-changes').focus();
            }
            return;
        }

        var id = 'cl_new_' + Date.now();
        var timestamp = Math.floor(Date.now() / 1000);

        // Create new entry HTML
        var html = createChangelogItemHtml(id, version, date, changes, timestamp, newEntryIndex);

        // Prepend to list or create list
        if ($('.wpdm-changelog-list').length) {
            $('.wpdm-changelog-list').prepend(html);
        } else {
            $('.wpdm-changelog-empty').replaceWith('<div class="wpdm-changelog-list">' + html + '</div>');
        }

        newEntryIndex++;

        // Clear form and reset version sync
        setEditorContent('wpdm-new-cl-changes', '');
        $clVersionField.data('user-modified', false);
        syncVersion();

        // Reindex all entries
        reindexChangelogs();
    };

    function createChangelogItemHtml(id, version, date, changes, timestamp, index) {
        var versionEscaped = $('<div>').text(version).html();
        var dateEscaped = $('<div>').text(date).html();

        return '<div class="wpdm-changelog-item" data-id="' + id + '">' +
            '<div class="wpdm-changelog-header" onclick="wpdmToggleChangelog(this)">' +
                '<button type="button" class="wpdm-changelog-btn wpdm-changelog-btn--toggle">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>' +
                '</button>' +
                '<span class="wpdm-changelog-version">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg>' +
                    ' v' + versionEscaped +
                '</span>' +
                '<span class="wpdm-changelog-date">' + dateEscaped + '</span>' +
                '<div class="wpdm-changelog-actions" onclick="event.stopPropagation()">' +
                    '<button type="button" class="wpdm-changelog-action-btn" onclick="wpdmEditChangelog(\'' + id + '\')" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" /></svg></button>' +
                    '<button type="button" class="wpdm-changelog-action-btn wpdm-changelog-action-btn--delete" onclick="wpdmDeleteChangelog(\'' + id + '\')" title="Delete"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>' +
                '</div>' +
            '</div>' +
            '<div class="wpdm-changelog-body"><div class="wpdm-changelog-content">' + changes + '</div></div>' +
            '<div class="wpdm-changelog-edit-form">' +
                '<div class="wpdm-changelog-form-row">' +
                    '<div class="wpdm-changelog-form-group"><label class="wpdm-changelog-label">Version</label><input type="text" class="form-control wpdm-edit-version" value="' + versionEscaped + '"></div>' +
                    '<div class="wpdm-changelog-form-group"><label class="wpdm-changelog-label">Date</label><input type="date" class="form-control wpdm-edit-date" value="' + dateEscaped + '"></div>' +
                '</div>' +
                '<div class="wpdm-changelog-form-group wpdm-changelog-form-group--full"><label class="wpdm-changelog-label">Changes</label><textarea class="form-control wpdm-edit-changes">' + $('<div>').text(changes).html() + '</textarea></div>' +
                '<div class="btn-group-actions"><button type="button" class="btn btn-primary" onclick="wpdmSaveEditChangelog(\'' + id + '\')"><i class="fas fa-check"></i> Save Changes</button> <button type="button" class="btn btn-secondary" onclick="wpdmCancelEditChangelog(\'' + id + '\')">Cancel</button></div>' +
            '</div>' +
            '<input type="hidden" name="file[changelog][' + index + '][id]" value="' + id + '" class="wpdm-cl-id">' +
            '<input type="hidden" name="file[changelog][' + index + '][version]" value="' + versionEscaped + '" class="wpdm-cl-version">' +
            '<input type="hidden" name="file[changelog][' + index + '][date]" value="' + dateEscaped + '" class="wpdm-cl-date">' +
            '<input type="hidden" name="file[changelog][' + index + '][changes]" value="' + $('<div>').text(changes).html() + '" class="wpdm-cl-changes">' +
            '<input type="hidden" name="file[changelog][' + index + '][timestamp]" value="' + timestamp + '" class="wpdm-cl-timestamp">' +
        '</div>';
    }

    function reindexChangelogs() {
        $('.wpdm-changelog-item').each(function(index) {
            $(this).find('input[type="hidden"]').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    name = name.replace(/\[changelog\]\[\d+\]/, '[changelog][' + index + ']');
                    $(this).attr('name', name);
                }
            });
        });
    }

})(jQuery);
</script>
