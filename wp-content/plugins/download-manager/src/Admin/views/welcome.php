<?php
/**
 * Download Manager - Welcome & Onboarding Page
 * Modern redesign with setup wizard
 */

if (!defined('ABSPATH')) exit;

// Check if setup is complete
$setup_complete = get_option('wpdm_setup_complete', false);
$current_step = isset($_GET['step']) ? intval($_GET['step']) : ($setup_complete ? 0 : 1);

// Handle step submissions
if (isset($_POST['wpdm_setup_step']) && wp_verify_nonce($_POST['_wpnonce'], 'wpdm_setup')) {
    $step = intval($_POST['wpdm_setup_step']);

    switch ($step) {
        case 2:
            // Save basic settings
            if (isset($_POST['wpdm_purl_base'])) {
                update_option('__wpdm_purl_base', sanitize_text_field($_POST['wpdm_purl_base']));
            }
            if (isset($_POST['wpdm_curl_base'])) {
                update_option('__wpdm_curl_base', sanitize_text_field($_POST['wpdm_curl_base']));
            }
            if (isset($_POST['wpdm_color_scheme'])) {
                $scheme = sanitize_text_field($_POST['wpdm_color_scheme']);
                if (in_array($scheme, ['light', 'dark', 'system'])) {
                    update_option('__wpdm_color_scheme', $scheme);
                }
            }
            // Dashboard page ID is saved via AJAX when button is clicked
            $current_step = 3;
            break;

        case 3:
            // Save user type preference
            if (isset($_POST['wpdm_user_type'])) {
                update_option('__wpdm_user_type', sanitize_text_field($_POST['wpdm_user_type']));
            }
            $current_step = 4;
            break;

        case 4:
            // Mark setup complete
            update_option('wpdm_setup_complete', true);
            $current_step = 5;
            break;
    }
}

// Skip setup
if (isset($_GET['skip_setup'])) {
    update_option('wpdm_setup_complete', true);
    wp_redirect(admin_url('edit.php?post_type=wpdmpro'));
    exit;
}

// Reset setup (for testing)
if (isset($_GET['reset_setup']) && current_user_can('manage_options')) {
    delete_option('wpdm_setup_complete');
    wp_redirect(admin_url('index.php?page=wpdm-welcome'));
    exit;
}

$purl_base = get_option('__wpdm_purl_base', 'download');
$curl_base = get_option('__wpdm_curl_base', 'download-category');
$color_scheme = get_option('__wpdm_color_scheme', 'light');
$dashboard_page = get_option('__wpdm_user_dashboard', 0);
$package_count = wp_count_posts('wpdmpro')->publish ?? 0;
?>

<style>
:root {
    --wpdm-primary: #10b981;
    --wpdm-primary-dark: #059669;
    --wpdm-primary-light: #ecfdf5;
    --wpdm-primary-glow: rgba(16, 185, 129, 0.4);
    --wpdm-accent: #6366f1;
    --wpdm-accent-light: #eef2ff;
    --wpdm-success: #10b981;
    --wpdm-success-light: #d1fae5;
    --wpdm-warning: #f59e0b;
    --wpdm-danger: #ef4444;
    --wpdm-gray-50: #f8fafc;
    --wpdm-gray-100: #f1f5f9;
    --wpdm-gray-200: #e2e8f0;
    --wpdm-gray-300: #cbd5e1;
    --wpdm-gray-400: #94a3b8;
    --wpdm-gray-500: #64748b;
    --wpdm-gray-600: #475569;
    --wpdm-gray-700: #334155;
    --wpdm-gray-800: #1e293b;
    --wpdm-gray-900: #0f172a;
}

* {
    box-sizing: border-box;
}
#wpbody-content{
    padding: 0 !important;
}
/* Hide WordPress admin footer on this page */
#wpfooter {
    display: none !important;
}

.wpdm-onboarding {
    min-height: 100vh;
    margin: -20px 0 0 -20px;
    padding: 60px 20px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    position: relative;
    overflow: hidden;
}

/* Subtle decorative elements */
.wpdm-onboarding::before {
    content: '';
    position: absolute;
    width: 600px;
    height: 600px;
    top: -300px;
    right: -200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
    pointer-events: none;
}

.wpdm-onboarding::after {
    content: '';
    position: absolute;
    width: 400px;
    height: 400px;
    bottom: -200px;
    left: -100px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.06) 0%, transparent 70%);
    pointer-events: none;
}

.wpdm-onboarding-container {
    max-width: 680px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

/* Header */
.wpdm-onboarding-header {
    text-align: center;
    margin-bottom: 32px;
}

.wpdm-logo {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 88px;
    height: 88px;
    background: linear-gradient(145deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(9, 65, 104, 0.12), 0 1px 3px rgba(9, 65, 104, 0.08);
    margin-bottom: 24px;
    border: 1px solid rgba(60, 128, 228, 0.15);
}

.wpdm-logo svg {
    width: 56px;
    height: 56px;
}

.wpdm-onboarding-header h1 {
    color: var(--wpdm-gray-900);
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 10px;
    letter-spacing: -0.5px;
}

.wpdm-onboarding-header p {
    color: var(--wpdm-gray-500);
    font-size: 17px;
    margin: 0;
}

/* Progress Steps - Clean design */
.wpdm-progress {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0;
    margin-bottom: 24px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--wpdm-gray-200);
    border-radius: 50px;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
}

.wpdm-progress-step {
    display: flex;
    align-items: center;
    gap: 0;
}

.wpdm-progress-dot {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--wpdm-gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 600;
    color: var(--wpdm-gray-400);
    transition: all 0.15s ease;
}

.wpdm-progress-step.active .wpdm-progress-dot {
    background: var(--wpdm-primary);
    color: #fff;
}

.wpdm-progress-step.completed .wpdm-progress-dot {
    background: var(--wpdm-success);
    color: #fff;
}

.wpdm-progress-line {
    width: 40px;
    height: 2px;
    background: var(--wpdm-gray-200);
    margin: 0 4px;
    transition: background 0.15s ease;
}

.wpdm-progress-step.completed + .wpdm-progress-step .wpdm-progress-line,
.wpdm-progress-step.completed .wpdm-progress-line {
    background: var(--wpdm-success);
}

/* Card - Clean professional design */
.wpdm-onboarding-card {
    background: #fff;
    border-radius: 6px;
    border: 1px solid var(--wpdm-gray-200);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    max-width: 100%;
}

.wpdm-card-header {
    background: var(--wpdm-gray-800);
    padding: 18px 24px;
    border-bottom: 1px solid var(--wpdm-gray-700);
}

.wpdm-card-header .wpdm-card-title {
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.wpdm-card-header .wpdm-card-title svg {
    width: 20px;
    height: 20px;
    opacity: 0.8;
}

.wpdm-card-body {
    padding: 28px 24px;
    overflow: hidden;
}

.wpdm-card-subtitle {
    font-size: 14px;
    color: var(--wpdm-gray-500);
    margin: 0 0 24px;
    line-height: 1.5;
}

/* Form Elements */
.wpdm-form-group {
    margin-bottom: 24px;
}

.wpdm-form-section {
    padding: 20px;
    border: 1px solid var(--wpdm-gray-200);
    border-radius: 6px;
    background: #fff;
}

.wpdm-form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--wpdm-gray-700);
    margin-bottom: 10px;
}

.wpdm-form-hint {
    font-size: 13px;
    color: var(--wpdm-gray-400);
    margin-top: 8px;
}

.wpdm-form-input {
    width: 100%;
    padding: 10px 14px;
    font-size: 14px;
    border: 1px solid var(--wpdm-gray-200);
    border-radius: 4px;
    background: var(--wpdm-gray-50);
    color: var(--wpdm-gray-800);
    transition: all 0.2s;
}

.wpdm-form-input:focus {
    outline: none;
    border-color: var(--wpdm-primary);
    background: #fff;
    box-shadow: 0 0 0 3px var(--wpdm-primary-light);
}

.wpdm-input-group {
    display: flex;
    align-items: center;
    background: var(--wpdm-gray-50);
    border: 1px solid var(--wpdm-gray-200);
    border-radius: 4px;
    overflow: hidden;
    transition: all 0.2s;
}

.wpdm-input-group:focus-within {
    border-color: var(--wpdm-primary);
    background: #fff;
    box-shadow: 0 0 0 3px var(--wpdm-primary-light);
}

.wpdm-input-prefix {
    padding: 10px 0 10px 14px;
    color: var(--wpdm-gray-400);
    font-size: 14px;
    white-space: nowrap;
}

.wpdm-input-group input {
    flex: 1;
    padding: 10px 14px 10px 4px;
    border: none;
    background: transparent;
    font-size: 14px;
    font-weight: 500;
    color: var(--wpdm-gray-800);
}

.wpdm-input-group input:focus {
    outline: none;
}

/* Form Row (side by side) */
.wpdm-form-row {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
}

.wpdm-form-row .wpdm-form-section {
    flex: 1;
}

.wpdm-form-half {
    flex: 1;
    margin-bottom: 0;
}

/* Color Scheme Selector */
.wpdm-scheme-selector {
    display: flex;
    gap: 12px;
}

.wpdm-scheme-option {
    flex: 1;
    cursor: pointer;
}

.wpdm-scheme-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.wpdm-scheme-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 16px 12px;
    background: #fff;
    border: 1px solid var(--wpdm-gray-200);
    border-radius: 6px;
    transition: all 0.2s;
}

.wpdm-scheme-option:hover .wpdm-scheme-card {
    border-color: var(--wpdm-gray-300);
}

.wpdm-scheme-option.selected .wpdm-scheme-card,
.wpdm-scheme-option input:checked + .wpdm-scheme-card {
    border-color: var(--wpdm-primary);
    background: var(--wpdm-primary-light);
}

.wpdm-scheme-preview {
    width: 100%;
    height: 60px;
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid var(--wpdm-gray-200);
}

.wpdm-scheme-light {
    background: #ffffff;
}

.wpdm-scheme-light .wpdm-scheme-header {
    height: 16px;
    background: linear-gradient(90deg, #f1f5f9 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

.wpdm-scheme-light .wpdm-scheme-content {
    padding: 10px;
}

.wpdm-scheme-light .wpdm-scheme-line {
    height: 8px;
    background: linear-gradient(90deg, #e2e8f0 0%, #f1f5f9 100%);
    border-radius: 4px;
    margin-bottom: 8px;
}

.wpdm-scheme-light .wpdm-scheme-line.short {
    width: 55%;
    margin-bottom: 0;
}

.wpdm-scheme-dark {
    background: #1e293b;
}

.wpdm-scheme-dark .wpdm-scheme-header {
    height: 16px;
    background: linear-gradient(90deg, #0f172a 0%, #1e293b 100%);
    border-bottom: 1px solid #334155;
}

.wpdm-scheme-dark .wpdm-scheme-content {
    padding: 10px;
}

.wpdm-scheme-dark .wpdm-scheme-line {
    height: 8px;
    background: linear-gradient(90deg, #334155 0%, #475569 100%);
    border-radius: 4px;
    margin-bottom: 8px;
}

.wpdm-scheme-dark .wpdm-scheme-line.short {
    width: 55%;
    margin-bottom: 0;
}

.wpdm-scheme-system {
    display: flex;
}

.wpdm-scheme-half-light,
.wpdm-scheme-half-dark {
    flex: 1;
}

.wpdm-scheme-half-light {
    background: #ffffff;
}

.wpdm-scheme-half-light .wpdm-scheme-header {
    height: 16px;
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
}

.wpdm-scheme-half-light .wpdm-scheme-content {
    padding: 8px;
}

.wpdm-scheme-half-light .wpdm-scheme-line {
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
}

.wpdm-scheme-half-dark {
    background: #1e293b;
}

.wpdm-scheme-half-dark .wpdm-scheme-header {
    height: 16px;
    background: #0f172a;
    border-bottom: 1px solid #334155;
}

.wpdm-scheme-half-dark .wpdm-scheme-content {
    padding: 8px;
}

.wpdm-scheme-half-dark .wpdm-scheme-line {
    height: 6px;
    background: #334155;
    border-radius: 3px;
}

.wpdm-scheme-label {
    font-size: 14px;
    font-weight: 700;
    color: var(--wpdm-gray-700);
}

/* Dashboard Status - Clean bordered */
.wpdm-dashboard-status {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    background: #fff;
    border: 1px solid var(--wpdm-gray-200);
    border-radius: 6px;
}

.wpdm-dashboard-exists {
    background: var(--wpdm-primary-light);
    border-color: var(--wpdm-primary);
}

.wpdm-dashboard-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: var(--wpdm-primary);
    color: #fff;
}

.wpdm-dashboard-icon.missing {
    background: var(--wpdm-gray-100);
    color: var(--wpdm-gray-500);
}

.wpdm-dashboard-icon svg {
    width: 20px;
    height: 20px;
}

.wpdm-dashboard-info {
    flex: 1;
    min-width: 0;
}

.wpdm-dashboard-title {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--wpdm-gray-800);
    margin-bottom: 2px;
}

.wpdm-dashboard-meta {
    display: block;
    font-size: 12px;
    color: var(--wpdm-gray-500);
}

.wpdm-btn-small {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
    background: #fff;
    color: var(--wpdm-gray-700);
    border: 1px solid var(--wpdm-gray-300);
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
    flex-shrink: 0;
}

.wpdm-btn-small:hover {
    background: var(--wpdm-gray-50);
    border-color: var(--wpdm-gray-400);
    color: var(--wpdm-gray-800);
}

.wpdm-btn-small.wpdm-btn-create {
    background: var(--wpdm-primary);
    color: #fff;
    border: none;
}

.wpdm-btn-small.wpdm-btn-create:hover {
    background: var(--wpdm-primary-dark);
    color: #fff;
}

.wpdm-btn-small.wpdm-btn-loading {
    pointer-events: none;
    opacity: 0.7;
}

/* Buttons - Clean flat design */
.wpdm-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    border: none;
}

.wpdm-btn-primary {
    background: var(--wpdm-primary);
    color: #fff;
}

.wpdm-btn-primary:hover {
    background: var(--wpdm-primary-dark);
    color: #fff;
}

.wpdm-btn-secondary {
    background: #fff;
    color: var(--wpdm-gray-700);
    border: 1px solid var(--wpdm-gray-300);
}

.wpdm-btn-secondary:hover {
    background: var(--wpdm-gray-50);
    border-color: var(--wpdm-gray-400);
    color: var(--wpdm-gray-800);
}

.wpdm-btn-success {
    background: var(--wpdm-success);
    color: #fff;
}

.wpdm-btn-success:hover {
    background: #059669;
    color: #fff;
}

.wpdm-btn svg {
    width: 16px;
    height: 16px;
}

.wpdm-card-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid var(--wpdm-gray-200);
}

/* Skip link */
.wpdm-skip-link {
    color: var(--wpdm-gray-400);
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    display: block;
    text-align: center;
    margin-top: 24px;
    transition: all 0.2s;
}

.wpdm-skip-link:hover {
    color: var(--wpdm-primary);
}

/* Welcome Step Features - Clean bordered cards */
.wpdm-features {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin: 24px 0;
}

.wpdm-feature {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    background: #fff;
    border: 1px solid var(--wpdm-gray-200);
    border-radius: 6px;
    transition: border-color 0.15s ease;
}

.wpdm-feature:hover {
    border-color: var(--wpdm-gray-300);
}

.wpdm-feature-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.wpdm-feature-icon svg {
    width: 20px;
    height: 20px;
}

.wpdm-feature-icon.blue {
    background: #eff6ff;
    color: #2563eb;
}

.wpdm-feature-icon.green {
    background: #ecfdf5;
    color: #059669;
}

.wpdm-feature-icon.purple {
    background: #f5f3ff;
    color: #7c3aed;
}

.wpdm-feature-icon.orange {
    background: #fff7ed;
    color: #ea580c;
}

.wpdm-feature-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--wpdm-gray-800);
    margin-bottom: 2px;
}

.wpdm-feature-desc {
    font-size: 13px;
    color: var(--wpdm-gray-500);
    line-height: 1.4;
}

/* Success Step - Clean design */
.wpdm-success-icon {
    width: 80px;
    height: 80px;
    background: var(--wpdm-primary-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    border: 2px solid var(--wpdm-primary);
}

.wpdm-success-icon svg {
    width: 40px;
    height: 40px;
    color: var(--wpdm-success);
}

.wpdm-success-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--wpdm-gray-900);
    text-align: center;
    margin-bottom: 8px;
}

.wpdm-success-subtitle {
    font-size: 14px;
    color: var(--wpdm-gray-500);
    text-align: center;
    margin-bottom: 28px;
    line-height: 1.5;
}

/* Quick Actions - Clean bordered cards */
.wpdm-quick-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.wpdm-quick-action {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    background: #fff;
    border-radius: 6px;
    text-decoration: none;
    transition: border-color 0.15s ease;
    border: 1px solid var(--wpdm-gray-200);
}

.wpdm-quick-action:hover {
    border-color: var(--wpdm-primary);
}

.wpdm-quick-action-icon {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.wpdm-quick-action-icon svg {
    width: 22px;
    height: 22px;
}

.wpdm-quick-action-icon.blue {
    background: #2563eb;
    color: #fff;
}

.wpdm-quick-action-icon.green {
    background: #059669;
    color: #fff;
}

.wpdm-quick-action-icon.purple {
    background: #7c3aed;
    color: #fff;
}

.wpdm-quick-action-icon.orange {
    background: #ea580c;
    color: #fff;
}

.wpdm-quick-action-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--wpdm-gray-800);
    margin-bottom: 2px;
}

.wpdm-quick-action-desc {
    font-size: 12px;
    color: var(--wpdm-gray-500);
}

/* Stats - Clean bordered cards */
.wpdm-stats {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}

.wpdm-stat {
    flex: 1;
    text-align: center;
    padding: 20px 16px;
    background: #fff;
    border: 1px solid var(--wpdm-gray-200);
    border-radius: 6px;
}

.wpdm-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--wpdm-gray-900);
}

.wpdm-stat-label {
    font-size: 12px;
    font-weight: 500;
    color: var(--wpdm-gray-500);
    margin-top: 4px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* User Type Selection - Clean bordered cards */
.wpdm-user-types {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}

.wpdm-user-type {
    position: relative;
    padding: 20px;
    background: #fff;
    border: 1px solid var(--wpdm-gray-200);
    border-radius: 6px;
    cursor: pointer;
    transition: border-color 0.15s ease;
}

.wpdm-user-type:hover {
    border-color: var(--wpdm-gray-300);
}

.wpdm-user-type.selected {
    border-color: var(--wpdm-primary);
    background: var(--wpdm-primary-light);
}

.wpdm-user-type input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.wpdm-user-type-icon {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}

.wpdm-user-type-icon svg {
    width: 22px;
    height: 22px;
}

.wpdm-user-type-icon.blue { background: #eff6ff; color: #2563eb; }
.wpdm-user-type-icon.green { background: #ecfdf5; color: #059669; }
.wpdm-user-type-icon.purple { background: #f5f3ff; color: #7c3aed; }
.wpdm-user-type-icon.orange { background: #fff7ed; color: #ea580c; }

.wpdm-user-type.selected .wpdm-user-type-icon.blue { background: #2563eb; color: #fff; }
.wpdm-user-type.selected .wpdm-user-type-icon.green { background: #059669; color: #fff; }
.wpdm-user-type.selected .wpdm-user-type-icon.purple { background: #7c3aed; color: #fff; }
.wpdm-user-type.selected .wpdm-user-type-icon.orange { background: #ea580c; color: #fff; }

.wpdm-user-type-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--wpdm-gray-800);
    margin-bottom: 4px;
}

.wpdm-user-type-desc {
    font-size: 13px;
    color: var(--wpdm-gray-500);
    line-height: 1.4;
}

.wpdm-user-type-check {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--wpdm-primary);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transform: scale(0.5);
    transition: all 0.15s ease;
}

.wpdm-user-type.selected .wpdm-user-type-check {
    opacity: 1;
    transform: scale(1);
}

.wpdm-user-type-check svg {
    width: 14px;
    height: 14px;
}

/* Recommended Add-ons */
.wpdm-addons-section {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid var(--wpdm-gray-200);
    overflow: hidden;
}

.wpdm-addons-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--wpdm-gray-800);
    margin-bottom: 6px;
}

.wpdm-addons-subtitle {
    font-size: 13px;
    color: var(--wpdm-gray-500);
    margin-bottom: 16px;
}

.wpdm-addons-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.wpdm-addon-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #fff;
    border: 1px solid var(--wpdm-gray-200);
    border-radius: 6px;
    text-decoration: none;
    transition: border-color 0.15s ease;
    overflow: hidden;
}

.wpdm-addon-card:hover {
    border-color: var(--wpdm-primary);
}

.wpdm-addon-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: var(--wpdm-gray-100);
    color: var(--wpdm-gray-600);
}

.wpdm-addon-icon svg {
    width: 18px;
    height: 18px;
}

.wpdm-addon-icon.blue { background: #eff6ff; color: #2563eb; }
.wpdm-addon-icon.green { background: #ecfdf5; color: #059669; }
.wpdm-addon-icon.purple { background: #f5f3ff; color: #7c3aed; }
.wpdm-addon-icon.orange { background: #fff7ed; color: #ea580c; }
.wpdm-addon-icon.pink { background: #fdf2f8; color: #db2777; }
.wpdm-addon-icon.cyan { background: #ecfeff; color: #0891b2; }

.wpdm-addon-info {
    flex: 1;
    min-width: 0;
    overflow: hidden;
}

.wpdm-addon-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--wpdm-gray-800);
    margin-bottom: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.wpdm-addon-desc {
    font-size: 11px;
    color: var(--wpdm-gray-500);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.wpdm-addon-badge {
    font-size: 10px;
    font-weight: 600;
    padding: 3px 6px;
    border-radius: 4px;
    background: var(--wpdm-primary-light);
    color: var(--wpdm-primary-dark);
    flex-shrink: 0;
}

/* Pro Promo Section */
.wpdm-pro-promo {
    margin-top: 28px;
    padding: 24px;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    border-radius: 8px;
    text-align: left;
}

.wpdm-pro-promo-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.wpdm-pro-promo-icon {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    flex-shrink: 0;
}

.wpdm-pro-promo-icon svg {
    width: 24px;
    height: 24px;
}

.wpdm-pro-promo-title {
    font-size: 16px;
    font-weight: 700;
    color: #0c4a6e;
    margin: 0 0 2px;
}

.wpdm-pro-promo-subtitle {
    font-size: 13px;
    color: #0369a1;
    margin: 0;
}

.wpdm-pro-features {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}

.wpdm-pro-feature {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #0c4a6e;
}

.wpdm-pro-feature svg {
    width: 16px;
    height: 16px;
    color: #0ea5e9;
    flex-shrink: 0;
}

.wpdm-pro-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.15s ease;
}

.wpdm-pro-cta:hover {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #fff;
}

.wpdm-pro-cta svg {
    width: 16px;
    height: 16px;
}

@media (max-width: 768px) {
    .wpdm-pro-features {
        grid-template-columns: 1fr;
    }
    .wpdm-features,
    .wpdm-quick-actions,
    .wpdm-user-types,
    .wpdm-addons-grid {
        grid-template-columns: 1fr;
    }

    .wpdm-card-body {
        padding: 32px 24px;
    }

    .wpdm-stats {
        flex-direction: column;
        gap: 12px;
    }

    .wpdm-form-row {
        flex-direction: column;
        gap: 0;
    }

    .wpdm-form-half {
        margin-bottom: 24px;
    }

    .wpdm-scheme-selector {
        flex-direction: column;
        gap: 12px;
    }
}
</style>

<div class="wpdm-onboarding">
    <div class="wpdm-onboarding-container">

        <!-- Header -->
        <div class="wpdm-onboarding-header">
            <div class="wpdm-logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 327.3 327.3"><defs><style>.wpdm-logo-arrow{fill:#3c80e4;}.wpdm-logo-circle{fill:#094168;}</style></defs><path class="wpdm-logo-arrow" d="M149.87,180.29l-91.41-91A20,20,0,0,1,58.39,61L64,55.36a20,20,0,0,1,28.29-.07l71.52,71.18,71-71.7a20,20,0,0,1,28.29-.13l5.68,5.62a20,20,0,0,1,.14,28.29l-90.75,91.64A20,20,0,0,1,149.87,180.29Z"/><path class="wpdm-logo-circle" d="M186.66,202.9a32,32,0,0,1-45.29.16L97.58,159.48a78,78,0,1,0,132.49-.41Z"/></svg>
            </div>
            <h1><?php _e('Welcome to Download Manager', 'download-manager'); ?></h1>
            <p><?php _e('Let\'s get your download system set up in just a few steps', 'download-manager'); ?></p>
        </div>

        <?php if ($current_step > 0 && $current_step < 5): ?>
        <!-- Progress Steps -->
        <div class="wpdm-progress">
            <div class="wpdm-progress-step <?php echo $current_step >= 1 ? ($current_step > 1 ? 'completed' : 'active') : ''; ?>">
                <div class="wpdm-progress-dot">
                    <?php if ($current_step > 1): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" width="16" height="16">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    <?php else: ?>
                        1
                    <?php endif; ?>
                </div>
            </div>
            <div class="wpdm-progress-step <?php echo $current_step >= 2 ? ($current_step > 2 ? 'completed' : 'active') : ''; ?>">
                <div class="wpdm-progress-line"></div>
                <div class="wpdm-progress-dot">
                    <?php if ($current_step > 2): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" width="16" height="16">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    <?php else: ?>
                        2
                    <?php endif; ?>
                </div>
            </div>
            <div class="wpdm-progress-step <?php echo $current_step >= 3 ? ($current_step > 3 ? 'completed' : 'active') : ''; ?>">
                <div class="wpdm-progress-line"></div>
                <div class="wpdm-progress-dot">
                    <?php if ($current_step > 3): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" width="16" height="16">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    <?php else: ?>
                        3
                    <?php endif; ?>
                </div>
            </div>
            <div class="wpdm-progress-step <?php echo $current_step >= 4 ? ($current_step > 4 ? 'completed' : 'active') : ''; ?>">
                <div class="wpdm-progress-line"></div>
                <div class="wpdm-progress-dot">
                    <?php if ($current_step > 4): ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" width="16" height="16">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    <?php else: ?>
                        4
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Card -->
        <div class="wpdm-onboarding-card">
                <?php if ($current_step === 1): ?>
                <!-- Step 1: Welcome -->
                <div class="wpdm-card-header">
                    <h2 class="wpdm-card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                            <line x1="9" y1="9" x2="9.01" y2="9"/>
                            <line x1="15" y1="9" x2="15.01" y2="9"/>
                        </svg>
                        <?php _e('Welcome! Let\'s get started', 'download-manager'); ?>
                    </h2>
                </div>
                <div class="wpdm-card-body">
                <p class="wpdm-card-subtitle"><?php _e('Download Manager helps you manage, track, and control file downloads on your WordPress site.', 'download-manager'); ?></p>

                <div class="wpdm-features">
                    <div class="wpdm-feature">
                        <div class="wpdm-feature-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-feature-title"><?php _e('File Management', 'download-manager'); ?></div>
                            <div class="wpdm-feature-desc"><?php _e('Upload and organize your files easily', 'download-manager'); ?></div>
                        </div>
                    </div>
                    <div class="wpdm-feature">
                        <div class="wpdm-feature-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-feature-title"><?php _e('Access Control', 'download-manager'); ?></div>
                            <div class="wpdm-feature-desc"><?php _e('Control who can download your files', 'download-manager'); ?></div>
                        </div>
                    </div>
                    <div class="wpdm-feature">
                        <div class="wpdm-feature-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 3v18h18"/>
                                <path d="M18 17V9"/>
                                <path d="M13 17V5"/>
                                <path d="M8 17v-3"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-feature-title"><?php _e('Download Analytics', 'download-manager'); ?></div>
                            <div class="wpdm-feature-desc"><?php _e('Track downloads and user activity', 'download-manager'); ?></div>
                        </div>
                    </div>
                    <div class="wpdm-feature">
                        <div class="wpdm-feature-icon orange">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <line x1="3" y1="9" x2="21" y2="9"/>
                                <line x1="9" y1="21" x2="9" y2="9"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-feature-title"><?php _e('Beautiful Templates', 'download-manager'); ?></div>
                            <div class="wpdm-feature-desc"><?php _e('Display downloads with stunning designs', 'download-manager'); ?></div>
                        </div>
                    </div>
                </div>

                <div class="wpdm-card-actions">
                    <span></span>
                    <a href="<?php echo admin_url('index.php?page=wpdm-welcome&step=2'); ?>" class="wpdm-btn wpdm-btn-primary">
                        <?php _e('Get Started', 'download-manager'); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>

                </div>
                <?php elseif ($current_step === 2): ?>
                <!-- Step 2: Basic Settings -->
                <div class="wpdm-card-header">
                    <h2 class="wpdm-card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                        </svg>
                        <?php _e('Configure Basic Settings', 'download-manager'); ?>
                    </h2>
                </div>
                <div class="wpdm-card-body">
                <p class="wpdm-card-subtitle"><?php _e('Set up your URL structure, appearance, and user dashboard.', 'download-manager'); ?></p>

                <form method="post">
                    <?php wp_nonce_field('wpdm_setup'); ?>
                    <input type="hidden" name="wpdm_setup_step" value="2">

                    <!-- URL Settings -->
                    <div class="wpdm-form-row">
                        <div class="wpdm-form-group wpdm-form-half">
                            <label class="wpdm-form-label"><?php _e('Package URL Slug', 'download-manager'); ?></label>
                            <div class="wpdm-input-group">
                                <span class="wpdm-input-prefix"><?php echo esc_html(wp_parse_url(home_url(), PHP_URL_HOST)); ?>/</span>
                                <input type="text" name="wpdm_purl_base" value="<?php echo esc_attr($purl_base); ?>" placeholder="download">
                            </div>
                            <p class="wpdm-form-hint"><?php _e('Base URL for download pages', 'download-manager'); ?></p>
                        </div>
                        <div class="wpdm-form-group wpdm-form-half">
                            <label class="wpdm-form-label"><?php _e('Category URL Slug', 'download-manager'); ?></label>
                            <div class="wpdm-input-group">
                                <span class="wpdm-input-prefix"><?php echo esc_html(wp_parse_url(home_url(), PHP_URL_HOST)); ?>/</span>
                                <input type="text" name="wpdm_curl_base" value="<?php echo esc_attr($curl_base); ?>" placeholder="download-category">
                            </div>
                            <p class="wpdm-form-hint"><?php _e('Base URL for category pages', 'download-manager'); ?></p>
                        </div>
                    </div>

                    <!-- Color Scheme -->
                    <div class="wpdm-form-group">
                        <label class="wpdm-form-label"><?php _e('Color Scheme', 'download-manager'); ?></label>
                        <div class="wpdm-scheme-selector">
                            <label class="wpdm-scheme-option <?php echo $color_scheme === 'light' ? 'selected' : ''; ?>">
                                <input type="radio" name="wpdm_color_scheme" value="light" <?php checked($color_scheme, 'light'); ?>>
                                <div class="wpdm-scheme-card">
                                    <div class="wpdm-scheme-preview wpdm-scheme-light">
                                        <div class="wpdm-scheme-header"></div>
                                        <div class="wpdm-scheme-content">
                                            <div class="wpdm-scheme-line"></div>
                                            <div class="wpdm-scheme-line short"></div>
                                        </div>
                                    </div>
                                    <span class="wpdm-scheme-label"><?php _e('Light', 'download-manager'); ?></span>
                                </div>
                            </label>
                            <label class="wpdm-scheme-option <?php echo $color_scheme === 'dark' ? 'selected' : ''; ?>">
                                <input type="radio" name="wpdm_color_scheme" value="dark" <?php checked($color_scheme, 'dark'); ?>>
                                <div class="wpdm-scheme-card">
                                    <div class="wpdm-scheme-preview wpdm-scheme-dark">
                                        <div class="wpdm-scheme-header"></div>
                                        <div class="wpdm-scheme-content">
                                            <div class="wpdm-scheme-line"></div>
                                            <div class="wpdm-scheme-line short"></div>
                                        </div>
                                    </div>
                                    <span class="wpdm-scheme-label"><?php _e('Dark', 'download-manager'); ?></span>
                                </div>
                            </label>
                            <label class="wpdm-scheme-option <?php echo $color_scheme === 'system' ? 'selected' : ''; ?>">
                                <input type="radio" name="wpdm_color_scheme" value="system" <?php checked($color_scheme, 'system'); ?>>
                                <div class="wpdm-scheme-card">
                                    <div class="wpdm-scheme-preview wpdm-scheme-system">
                                        <div class="wpdm-scheme-half-light">
                                            <div class="wpdm-scheme-header"></div>
                                            <div class="wpdm-scheme-content">
                                                <div class="wpdm-scheme-line"></div>
                                            </div>
                                        </div>
                                        <div class="wpdm-scheme-half-dark">
                                            <div class="wpdm-scheme-header"></div>
                                            <div class="wpdm-scheme-content">
                                                <div class="wpdm-scheme-line"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="wpdm-scheme-label"><?php _e('System', 'download-manager'); ?></span>
                                </div>
                            </label>
                        </div>
                        <p class="wpdm-form-hint"><?php _e('Choose how Download Manager appears on the frontend.', 'download-manager'); ?></p>
                    </div>

                    <!-- Dashboard Page -->
                    <div class="wpdm-form-group">
                        <label class="wpdm-form-label"><?php _e('User Dashboard Page', 'download-manager'); ?></label>
                        <?php if ($dashboard_page && get_post($dashboard_page)): ?>
                            <div class="wpdm-dashboard-status wpdm-dashboard-exists">
                                <div class="wpdm-dashboard-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                </div>
                                <div class="wpdm-dashboard-info">
                                    <span class="wpdm-dashboard-title"><?php echo esc_html(get_the_title($dashboard_page)); ?></span>
                                    <span class="wpdm-dashboard-meta"><?php _e('Dashboard page is ready', 'download-manager'); ?></span>
                                </div>
                                <a href="<?php echo esc_url(get_edit_post_link($dashboard_page)); ?>" class="wpdm-btn-small" target="_blank">
                                    <?php _e('Edit', 'download-manager'); ?>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="wpdm-dashboard-status wpdm-dashboard-missing" id="wpdm-dashboard-create">
                                <div class="wpdm-dashboard-icon missing">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="3" y1="9" x2="21" y2="9"/>
                                        <line x1="9" y1="21" x2="9" y2="9"/>
                                    </svg>
                                </div>
                                <div class="wpdm-dashboard-info">
                                    <span class="wpdm-dashboard-title"><?php _e('No dashboard page yet', 'download-manager'); ?></span>
                                    <span class="wpdm-dashboard-meta"><?php _e('Create a page with [wpdm_user_dashboard] shortcode', 'download-manager'); ?></span>
                                </div>
                                <button type="button" class="wpdm-btn-small wpdm-btn-create" id="wpdm-create-dashboard-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                        <line x1="12" y1="5" x2="12" y2="19"/>
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                    </svg>
                                    <?php _e('Create Page', 'download-manager'); ?>
                                </button>
                            </div>
                            <div class="wpdm-dashboard-status wpdm-dashboard-exists" id="wpdm-dashboard-created" style="display: none;">
                                <div class="wpdm-dashboard-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                </div>
                                <div class="wpdm-dashboard-info">
                                    <span class="wpdm-dashboard-title" id="wpdm-created-page-title"><?php _e('User Dashboard', 'download-manager'); ?></span>
                                    <span class="wpdm-dashboard-meta"><?php _e('Dashboard page created!', 'download-manager'); ?></span>
                                </div>
                                <a href="#" class="wpdm-btn-small" id="wpdm-edit-dashboard-link" target="_blank">
                                    <?php _e('Edit', 'download-manager'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <p class="wpdm-form-hint"><?php _e('Users can view their downloads, orders, and profile on this page.', 'download-manager'); ?></p>
                    </div>

                    <div class="wpdm-card-actions">
                        <a href="<?php echo admin_url('index.php?page=wpdm-welcome&step=1'); ?>" class="wpdm-btn wpdm-btn-secondary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"/>
                                <polyline points="12 19 5 12 12 5"/>
                            </svg>
                            <?php _e('Back', 'download-manager'); ?>
                        </a>
                        <button type="submit" class="wpdm-btn wpdm-btn-primary">
                            <?php _e('Continue', 'download-manager'); ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <script>
                // Color scheme selector
                document.querySelectorAll('.wpdm-scheme-option').forEach(function(option) {
                    option.addEventListener('click', function() {
                        document.querySelectorAll('.wpdm-scheme-option').forEach(function(o) {
                            o.classList.remove('selected');
                        });
                        this.classList.add('selected');
                    });
                });

                // Create dashboard page
                var createBtn = document.getElementById('wpdm-create-dashboard-btn');
                if (createBtn) {
                    createBtn.addEventListener('click', function() {
                        var btn = this;
                        var originalText = btn.innerHTML;
                        btn.classList.add('wpdm-btn-loading');
                        btn.innerHTML = '<?php _e('Creating...', 'download-manager'); ?>';

                        var formData = new FormData();
                        formData.append('action', 'wpdm_create_dashboard_page');
                        formData.append('_wpnonce', '<?php echo wp_create_nonce('wpdm_create_dashboard'); ?>');

                        fetch(ajaxurl, {
                            method: 'POST',
                            body: formData
                        })
                        .then(function(response) { return response.json(); })
                        .then(function(data) {
                            if (data.success) {
                                document.getElementById('wpdm-dashboard-create').style.display = 'none';
                                document.getElementById('wpdm-dashboard-created').style.display = 'flex';
                                document.getElementById('wpdm-created-page-title').textContent = data.data.title;
                                document.getElementById('wpdm-edit-dashboard-link').href = data.data.edit_url;
                            } else {
                                btn.classList.remove('wpdm-btn-loading');
                                btn.innerHTML = originalText;
                                alert(data.data || '<?php _e('Failed to create page', 'download-manager'); ?>');
                            }
                        })
                        .catch(function() {
                            btn.classList.remove('wpdm-btn-loading');
                            btn.innerHTML = originalText;
                            alert('<?php _e('Failed to create page', 'download-manager'); ?>');
                        });
                    });
                }
                </script>

                </div>
                <?php elseif ($current_step === 3): ?>
                <!-- Step 3: Choose Your Focus -->
                <div class="wpdm-card-header">
                    <h2 class="wpdm-card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <?php _e('How will you use Download Manager?', 'download-manager'); ?>
                    </h2>
                </div>
                <div class="wpdm-card-body">
                <p class="wpdm-card-subtitle"><?php _e('Select your primary use case to get personalized add-on recommendations.', 'download-manager'); ?></p>

                <form method="post" id="wpdm-user-type-form">
                    <?php wp_nonce_field('wpdm_setup'); ?>
                    <input type="hidden" name="wpdm_setup_step" value="3">
                    <input type="hidden" name="wpdm_user_type" id="wpdm_user_type" value="content_creator">

                    <div class="wpdm-user-types">
                        <!-- Content Creator -->
                        <label class="wpdm-user-type selected" data-type="content_creator">
                            <input type="radio" name="user_type_radio" value="content_creator" checked>
                            <div class="wpdm-user-type-icon blue">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 19l7-7 3 3-7 7-3-3z"/>
                                    <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/>
                                    <path d="M2 2l7.586 7.586"/>
                                    <circle cx="11" cy="11" r="2"/>
                                </svg>
                            </div>
                            <div class="wpdm-user-type-title"><?php _e('Content Creator', 'download-manager'); ?></div>
                            <div class="wpdm-user-type-desc"><?php _e('Share documents, PDFs, media files with your audience', 'download-manager'); ?></div>
                            <div class="wpdm-user-type-check">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                        </label>

                        <!-- Digital Seller -->
                        <label class="wpdm-user-type" data-type="digital_seller">
                            <input type="radio" name="user_type_radio" value="digital_seller">
                            <div class="wpdm-user-type-icon green">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1"/>
                                    <circle cx="20" cy="21" r="1"/>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                </svg>
                            </div>
                            <div class="wpdm-user-type-title"><?php _e('Digital Seller', 'download-manager'); ?></div>
                            <div class="wpdm-user-type-desc"><?php _e('Sell software, ebooks, music, or digital products', 'download-manager'); ?></div>
                            <div class="wpdm-user-type-check">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                        </label>

                        <!-- Developer -->
                        <label class="wpdm-user-type" data-type="developer">
                            <input type="radio" name="user_type_radio" value="developer">
                            <div class="wpdm-user-type-icon purple">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="16 18 22 12 16 6"/>
                                    <polyline points="8 6 2 12 8 18"/>
                                </svg>
                            </div>
                            <div class="wpdm-user-type-title"><?php _e('Developer', 'download-manager'); ?></div>
                            <div class="wpdm-user-type-desc"><?php _e('Integrate downloads via API, shortcodes, or custom code', 'download-manager'); ?></div>
                            <div class="wpdm-user-type-check">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                        </label>

                        <!-- Page Builder User -->
                        <label class="wpdm-user-type" data-type="page_builder">
                            <input type="radio" name="user_type_radio" value="page_builder">
                            <div class="wpdm-user-type-icon orange">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="3" y1="9" x2="21" y2="9"/>
                                    <line x1="9" y1="21" x2="9" y2="9"/>
                                </svg>
                            </div>
                            <div class="wpdm-user-type-title"><?php _e('Page Builder User', 'download-manager'); ?></div>
                            <div class="wpdm-user-type-desc"><?php _e('Use Elementor, Gutenberg, or other page builders', 'download-manager'); ?></div>
                            <div class="wpdm-user-type-check">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                        </label>
                    </div>

                    <!-- Recommended Add-ons Section -->
                    <div class="wpdm-addons-section">
                        <h3 class="wpdm-addons-title"><?php _e('Recommended Free Add-ons', 'download-manager'); ?></h3>
                        <p class="wpdm-addons-subtitle"><?php _e('Based on your selection, these add-ons will enhance your experience.', 'download-manager'); ?></p>

                        <!-- Content Creator Add-ons -->
                        <div class="wpdm-addons-grid" id="addons-content_creator">
                            <a href="https://www.wpdownloadmanager.com/download/gutenberg-blocks/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Gutenberg Blocks', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Add downloads using block editor', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/wpdm-button-templates/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon purple">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="8" width="18" height="8" rx="2"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Button Templates', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Beautiful download button designs', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/wpdm-page-templates/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon green">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Page Templates', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Pre-designed download page layouts', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/google-drive-explorer/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon cyan">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Google Drive', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Host files on Google Drive', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                        </div>

                        <!-- Digital Seller Add-ons -->
                        <div class="wpdm-addons-grid" id="addons-digital_seller" style="display: none;">
                            <a href="https://www.wpdownloadmanager.com/download/premium-package-wordpress-digital-store-solution/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon green">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Premium Package', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Complete e-commerce solution', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/advanced-sales-report/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Sales Report', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Advanced sales analytics', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/google-tags-for-wordpress-download-manager/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon orange">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                        <line x1="7" y1="7" x2="7.01" y2="7"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Google Tags', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Track conversions with GTM', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/gutenberg-blocks/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon purple">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Gutenberg Blocks', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Add downloads using block editor', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                        </div>

                        <!-- Developer Add-ons -->
                        <div class="wpdm-addons-grid" id="addons-developer" style="display: none;">
                            <a href="https://www.wpdownloadmanager.com/download/wpdm-api/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon purple">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('REST API', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Full REST API for integration', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/wpdm-extended-short-codes/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Extended Shortcodes', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Advanced shortcode options', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/gutenberg-blocks/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon green">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Gutenberg Blocks', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Block editor components', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/advanced-tinymce-button/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon orange">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('TinyMCE Button', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Classic editor integration', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                        </div>

                        <!-- Page Builder Add-ons -->
                        <div class="wpdm-addons-grid" id="addons-page_builder" style="display: none;">
                            <a href="https://www.wpdownloadmanager.com/download/download-manager-addons-for-elementor/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon pink">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Elementor Widgets', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Native Elementor integration', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/gutenberg-blocks/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Gutenberg Blocks', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Native block editor support', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/wpdm-button-templates/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon purple">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="8" width="18" height="8" rx="2"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Button Templates', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Styled download buttons', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                            <a href="https://www.wpdownloadmanager.com/download/wpdm-page-templates/" target="_blank" class="wpdm-addon-card">
                                <div class="wpdm-addon-icon green">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                </div>
                                <div class="wpdm-addon-info">
                                    <div class="wpdm-addon-name"><?php _e('Page Templates', 'download-manager'); ?></div>
                                    <div class="wpdm-addon-desc"><?php _e('Pre-designed layouts', 'download-manager'); ?></div>
                                </div>
                                <span class="wpdm-addon-badge"><?php _e('Free', 'download-manager'); ?></span>
                            </a>
                        </div>
                    </div>

                    <div class="wpdm-card-actions">
                        <a href="<?php echo admin_url('index.php?page=wpdm-welcome&step=2'); ?>" class="wpdm-btn wpdm-btn-secondary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"/>
                                <polyline points="12 19 5 12 12 5"/>
                            </svg>
                            <?php _e('Back', 'download-manager'); ?>
                        </a>
                        <button type="submit" class="wpdm-btn wpdm-btn-primary">
                            <?php _e('Continue', 'download-manager'); ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <script>
                document.querySelectorAll('.wpdm-user-type').forEach(function(card) {
                    card.addEventListener('click', function() {
                        // Remove selected from all
                        document.querySelectorAll('.wpdm-user-type').forEach(function(c) {
                            c.classList.remove('selected');
                        });
                        // Add selected to clicked
                        this.classList.add('selected');
                        // Update hidden input
                        document.getElementById('wpdm_user_type').value = this.dataset.type;
                        // Show corresponding add-ons
                        document.querySelectorAll('.wpdm-addons-grid').forEach(function(grid) {
                            grid.style.display = 'none';
                        });
                        document.getElementById('addons-' + this.dataset.type).style.display = 'grid';
                    });
                });
                </script>

                </div>
                <?php elseif ($current_step === 4): ?>
                <!-- Step 4: Create First Package -->
                <div class="wpdm-card-header">
                    <h2 class="wpdm-card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="12" y1="18" x2="12" y2="12"/>
                            <line x1="9" y1="15" x2="15" y2="15"/>
                        </svg>
                        <?php _e('Create Your First Download', 'download-manager'); ?>
                    </h2>
                </div>
                <div class="wpdm-card-body">
                <p class="wpdm-card-subtitle"><?php _e('You\'re all set! Create your first download package or explore the dashboard.', 'download-manager'); ?></p>

                <div class="wpdm-quick-actions" style="margin-bottom: 24px;">
                    <a href="<?php echo admin_url('post-new.php?post_type=wpdmpro'); ?>" class="wpdm-quick-action">
                        <div class="wpdm-quick-action-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-quick-action-title"><?php _e('Create Package', 'download-manager'); ?></div>
                            <div class="wpdm-quick-action-desc"><?php _e('Add your first downloadable file', 'download-manager'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=wpdmpro&page=settings'); ?>" class="wpdm-quick-action">
                        <div class="wpdm-quick-action-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-quick-action-title"><?php _e('More Settings', 'download-manager'); ?></div>
                            <div class="wpdm-quick-action-desc"><?php _e('Configure advanced options', 'download-manager'); ?></div>
                        </div>
                    </a>
                </div>

                <form method="post">
                    <?php wp_nonce_field('wpdm_setup'); ?>
                    <input type="hidden" name="wpdm_setup_step" value="4">

                    <div class="wpdm-card-actions">
                        <a href="<?php echo admin_url('index.php?page=wpdm-welcome&step=3'); ?>" class="wpdm-btn wpdm-btn-secondary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"/>
                                <polyline points="12 19 5 12 12 5"/>
                            </svg>
                            <?php _e('Back', 'download-manager'); ?>
                        </a>
                        <button type="submit" class="wpdm-btn wpdm-btn-success">
                            <?php _e('Complete Setup', 'download-manager'); ?>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </button>
                    </div>
                </form>

                </div>
                <?php elseif ($current_step === 5): ?>
                <!-- Step 5: Success -->
                <div class="wpdm-card-body" style="text-align: center; padding: 40px 24px;">
                <div class="wpdm-success-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <h2 class="wpdm-success-title"><?php _e('You\'re All Set!', 'download-manager'); ?></h2>
                <p class="wpdm-success-subtitle"><?php _e('Download Manager is ready to use. Here are some quick actions to get started.', 'download-manager'); ?></p>

                <div class="wpdm-quick-actions">
                    <a href="<?php echo admin_url('post-new.php?post_type=wpdmpro'); ?>" class="wpdm-quick-action">
                        <div class="wpdm-quick-action-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-quick-action-title"><?php _e('Add New File', 'download-manager'); ?></div>
                            <div class="wpdm-quick-action-desc"><?php _e('Create your first download', 'download-manager'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=wpdmpro'); ?>" class="wpdm-quick-action">
                        <div class="wpdm-quick-action-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-quick-action-title"><?php _e('All Files', 'download-manager'); ?></div>
                            <div class="wpdm-quick-action-desc"><?php _e('View and manage downloads', 'download-manager'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=wpdmpro&page=settings'); ?>" class="wpdm-quick-action">
                        <div class="wpdm-quick-action-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-quick-action-title"><?php _e('Settings', 'download-manager'); ?></div>
                            <div class="wpdm-quick-action-desc"><?php _e('Configure plugin options', 'download-manager'); ?></div>
                        </div>
                    </a>
                    <a href="https://www.wpdownloadmanager.com/docs/" target="_blank" class="wpdm-quick-action">
                        <div class="wpdm-quick-action-icon orange">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-quick-action-title"><?php _e('Documentation', 'download-manager'); ?></div>
                            <div class="wpdm-quick-action-desc"><?php _e('Learn how to use WPDM', 'download-manager'); ?></div>
                        </div>
                    </a>
                </div>

                <p style="margin-top: 24px;">
                    <a href="<?php echo esc_url(admin_url('index.php?page=wpdm-welcome&reset_setup=1')); ?>" style="color: #6b7280; text-decoration: none; font-size: 13px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                            <path d="M1 4v6h6"/><path d="M23 20v-6h-6"/><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                        </svg>
                        <?php _e('Restart Setup Wizard', 'download-manager'); ?>
                    </a>
                </p>

                <!-- Pro Promo -->
                <div class="wpdm-pro-promo">
                    <div class="wpdm-pro-promo-header">
                        <div class="wpdm-pro-promo-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="wpdm-pro-promo-title"><?php _e('Unlock More with Pro', 'download-manager'); ?></h3>
                            <p class="wpdm-pro-promo-subtitle"><?php _e('Take your downloads to the next level', 'download-manager'); ?></p>
                        </div>
                    </div>
                    <div class="wpdm-pro-features">
                        <div class="wpdm-pro-feature">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php _e('Multi-file package support', 'download-manager'); ?>
                        </div>
                        <div class="wpdm-pro-feature">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php _e('Email & Social lock options', 'download-manager'); ?>
                        </div>
                        <div class="wpdm-pro-feature">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php _e('CSV Import/Export', 'download-manager'); ?>
                        </div>
                        <div class="wpdm-pro-feature">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <?php _e('Priority support & Many more...', 'download-manager'); ?>
                        </div>
                    </div>
                    <a href="https://www.wpdownloadmanager.com/pricing/" target="_blank" class="wpdm-pro-cta">
                        <?php _e('See Pro Features', 'download-manager'); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>

                </div>
                <?php else: ?>
                <!-- Dashboard Mode (setup already complete) -->
                <div class="wpdm-card-header">
                    <h2 class="wpdm-card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <line x1="3" y1="9" x2="21" y2="9"/>
                            <line x1="9" y1="21" x2="9" y2="9"/>
                        </svg>
                        <?php _e('Quick Actions', 'download-manager'); ?>
                    </h2>
                </div>
                <div class="wpdm-card-body">
                <div class="wpdm-stats">
                    <div class="wpdm-stat">
                        <div class="wpdm-stat-value"><?php echo number_format($package_count); ?></div>
                        <div class="wpdm-stat-label"><?php _e('Packages', 'download-manager'); ?></div>
                    </div>
                    <div class="wpdm-stat">
                        <div class="wpdm-stat-value"><?php echo number_format(get_option('__wpdm_total_downloads', 0)); ?></div>
                        <div class="wpdm-stat-label"><?php _e('Total Downloads', 'download-manager'); ?></div>
                    </div>
                    <div class="wpdm-stat">
                        <div class="wpdm-stat-value"><?php echo WPDM_VERSION; ?></div>
                        <div class="wpdm-stat-label"><?php _e('Version', 'download-manager'); ?></div>
                    </div>
                </div>

                <p class="wpdm-card-subtitle"><?php _e('Jump to common tasks and settings.', 'download-manager'); ?></p>

                <div class="wpdm-quick-actions">
                    <a href="<?php echo admin_url('post-new.php?post_type=wpdmpro'); ?>" class="wpdm-quick-action">
                        <div class="wpdm-quick-action-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-quick-action-title"><?php _e('Add New Package', 'download-manager'); ?></div>
                            <div class="wpdm-quick-action-desc"><?php _e('Create a new download', 'download-manager'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=wpdmpro'); ?>" class="wpdm-quick-action">
                        <div class="wpdm-quick-action-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-quick-action-title"><?php _e('All Packages', 'download-manager'); ?></div>
                            <div class="wpdm-quick-action-desc"><?php _e('View and manage downloads', 'download-manager'); ?></div>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=wpdmpro&page=settings'); ?>" class="wpdm-quick-action">
                        <div class="wpdm-quick-action-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-quick-action-title"><?php _e('Settings', 'download-manager'); ?></div>
                            <div class="wpdm-quick-action-desc"><?php _e('Configure plugin options', 'download-manager'); ?></div>
                        </div>
                    </a>
                    <a href="https://www.wpdownloadmanager.com/downloads/starter-add-on-bundle/" target="_blank" class="wpdm-quick-action">
                        <div class="wpdm-quick-action-icon orange">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </div>
                        <div>
                            <div class="wpdm-quick-action-title"><?php _e('Add-ons', 'download-manager'); ?></div>
                            <div class="wpdm-quick-action-desc"><?php _e('Extend functionality', 'download-manager'); ?></div>
                        </div>
                    </a>
                </div>

                <p style="margin-top: 24px; text-align: center;">
                    <a href="<?php echo esc_url(admin_url('index.php?page=wpdm-welcome&reset_setup=1')); ?>" style="color: #6b7280; text-decoration: none; font-size: 13px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                            <path d="M1 4v6h6"/><path d="M23 20v-6h-6"/><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                        </svg>
                        <?php _e('Restart Setup Wizard', 'download-manager'); ?>
                    </a>
                </p>

                <?php endif; ?>
                </div>
        </div>

        <?php if ($current_step > 0 && $current_step < 5): ?>
        <a href="<?php echo admin_url('index.php?page=wpdm-welcome&skip_setup=1'); ?>" class="wpdm-skip-link">
            <?php _e('Skip setup and go to dashboard', 'download-manager'); ?>
        </a>
        <?php endif; ?>

    </div>
</div>
