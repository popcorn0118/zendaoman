<?php
/**
 * Admin → Add-Ons marketplace view.
 *
 * Renders a centered hero in .wpdm-admin-page-content. The framework's fixed
 * #wpdm-admin-page-header is kept (empty/invisible) ONLY as a paint anchor:
 * Pro's admin defers this view's first paint unless a fixed, painted element
 * exists on the page. Data prep is separated from presentation below.
 */
if (!defined('ABSPATH')) die();

if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$items = [];
$installed_count = 0;

if (is_array($data)) {
    foreach ($data as $package) {
        $files = (array) $package->files;
        $file  = str_replace('.zip', '', array_shift($files));
        $file  = explode('/', $file);
        $file  = end($file);

        $plugininfo = wpdm_plugin_data($file);

        $installed         = (bool) $plugininfo;
        $installed_version = $installed && isset($plugininfo['Version']) ? $plugininfo['Version'] : '';
        $active            = $installed && isset($plugininfo['plugin_index_file']) && is_plugin_active($plugininfo['plugin_index_file']);
        $update            = $installed && $installed_version && version_compare($installed_version, $package->version, '<');

        if ($installed) $installed_count++;

        $items[] = (object) [
            'ID'         => $package->ID,
            'title'      => $package->post_title,
            'excerpt'    => $package->excerpt,
            'link'       => $package->link,
            'version'    => $package->version,
            'price'      => $package->price,
            'currency'   => $package->currency,
            'file'       => $file,
            'categories' => implode(' ', array_map('sanitize_html_class', (array) $package->categories)),
            'installed'  => $installed,
            'active'     => $active,
            'update'     => $update,
        ];
    }
}

$total      = count($items);
$cat_count  = is_array($cats) ? count($cats) : 0;

$cat_counts = [];
foreach ($items as $countItem) {
    foreach (array_filter(explode(' ', $countItem->categories)) as $catSlug) {
        $cat_counts[$catSlug] = isset($cat_counts[$catSlug]) ? $cat_counts[$catSlug] + 1 : 1;
    }
}
?>
<style>
    #screen-meta-links { display: none; }
    .wrap.w3eden .updated,
    .wrap.w3eden #screen-meta { display: none; }

    /* The fixed header is kept only as a paint anchor (Pro defers this view's
       first paint without a fixed, painted element). Rendered slim + invisible;
       the real title is the centered hero in the content below. */
    #wpdm-admin-page-header { pointer-events: none; }
    #wpdm-admin-page-header #wpdm-admin-main-header { height: 40px; border: 0; box-shadow: none; }
    #wpdm-admin-page-header h3 { visibility: hidden; }

    .wpda {
        --wpda-surface: #ffffff;
        --wpda-bg: #f6f7fb;
        --wpda-border: #e6e8ef;
        --wpda-border-strong: #d3d7e3;
        --wpda-text: #1e2433;
        --wpda-text-muted: #6b7280;
        --wpda-text-soft: #9aa1ad;
        --wpda-primary: var(--color-primary, #4a8eff);
        --wpda-primary-rgb: var(--color-primary-rgb, 74, 142, 255);
        --wpda-success: var(--color-success, #10b981);
        --wpda-success-rgb: var(--color-success-rgb, 16, 185, 129);
        --wpda-warning: var(--color-warning, #f59e0b);
        --wpda-warning-rgb: 245, 158, 11;
        --wpda-danger: var(--color-danger, #ef4444);
        --wpda-radius: 12px;
        --wpda-radius-sm: 8px;
        --wpda-radius-pill: 999px;
        --wpda-shadow: 0 1px 2px rgba(16, 24, 40, .04), 0 1px 3px rgba(16, 24, 40, .06);
        --wpda-shadow-hover: 0 6px 16px rgba(16, 24, 40, .08), 0 14px 32px rgba(16, 24, 40, .10);
        --wpda-transition: 180ms cubic-bezier(.4, 0, .2, 1);
        font-family: var(--wpdm-font, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif);
        color: var(--wpda-text);
    }
    .wpda * { box-sizing: border-box; }

    /* slim invisible anchor (~40px) sits at the top; clear it, no big void */
    .wpdm-admin-page-content.wpda { padding-top: 48px; }

    /* ---- Hero ---- */
    .wpda-hero { display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 0; }
    .wpda-hero__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        margin-bottom: 18px;
        color: var(--wpda-primary);
        background: rgba(var(--wpda-primary-rgb), .1);
        border-radius: 14px;
    }
    .wpda-hero__icon svg { width: 26px; height: 26px; }
    .wpda .wpda-hero__title {
        margin: 0;
        padding: 0;
        font-size: clamp(22px, 2.4vw, 28px);
        font-weight: 800;
        letter-spacing: -.02em;
        line-height: 1.15;
        color: var(--wpda-text);
    }
    .wpda .wpda-hero__subtitle {
        max-width: 56ch;
        margin: 8px auto 20px;
        font-size: 13.5px;
        line-height: 1.5;
        color: var(--wpda-text-muted);
        text-align: center;
    }

    .wpda-stats { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin: 22px 0 0; padding: 0; }
    .wpda-stat {
        min-width: 92px;
        padding: 12px 16px;
        background: var(--wpda-surface);
        border: 1px solid var(--wpda-border);
        border-radius: var(--wpda-radius-sm);
        text-align: center;
    }
    .wpda-stat dt {
        margin: 0 0 2px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--wpda-text-soft);
    }
    .wpda-stat dd {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        line-height: 1;
        color: var(--wpda-text);
        font-variant-numeric: tabular-nums;
    }

    .wpda-sep { height: 1px; background: var(--wpda-border); margin: 28px -32px; }

    /* ---- Toolbar (search + scope) ---- */
    .wpda-toolbar {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .wpda-search { position: relative; flex: 0 1 460px; min-width: 220px; max-width: 460px; }
    .wpda-search__icon {
        position: absolute;
        left: 12px;
        top: 0;
        bottom: 0;
        margin: auto 0;
        width: 16px;
        height: 16px;
        color: var(--wpda-text-soft);
        pointer-events: none;
    }
    .wpda-search input {
        width: 100%;
        height: 38px;
        margin: 0;
        padding: 0 64px 0 36px;
        font-size: 13px;
        line-height: 38px;
        color: var(--wpda-text);
        background: var(--wpda-bg);
        border: 1px solid var(--wpda-border);
        border-radius: var(--wpda-radius-sm);
        box-shadow: none;
        transition: border-color var(--wpda-transition), box-shadow var(--wpda-transition), background var(--wpda-transition);
    }
    .wpda-search input:focus {
        outline: none;
        background: var(--wpda-surface);
        border-color: var(--wpda-primary);
        box-shadow: 0 0 0 3px rgba(var(--wpda-primary-rgb), .15);
    }
    .wpda-search input::placeholder { color: var(--wpda-text-soft); }
    .wpda-search__kbd {
        position: absolute;
        right: 10px;
        top: 0;
        bottom: 0;
        margin: auto 0;
        height: 20px;
        padding: 2px 7px;
        font-size: 11px;
        font-weight: 600;
        line-height: 16px;
        color: var(--wpda-text-soft);
        background: var(--wpda-surface);
        border: 1px solid var(--wpda-border);
        border-radius: 5px;
        pointer-events: none;
    }
    .wpda-search__clear {
        position: absolute;
        right: 8px;
        top: 0;
        bottom: 0;
        margin: auto 0;
        display: none;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        padding: 0;
        color: var(--wpda-text-muted);
        background: transparent;
        border: 0;
        border-radius: 50%;
        cursor: pointer;
        line-height: 1;
        transition: background var(--wpda-transition), color var(--wpda-transition);
    }
    .wpda-search__clear:hover { background: var(--wpda-border); color: var(--wpda-text); }
    .wpda-search.is-filled .wpda-search__kbd { display: none; }
    .wpda-search.is-filled .wpda-search__clear { display: inline-flex; }

    .wpda-scope {
        display: inline-flex;
        gap: 2px;
        padding: 3px;
        background: var(--wpda-bg);
        border: 1px solid var(--wpda-border);
        border-radius: var(--wpda-radius-sm);
    }
    .wpda-scope button {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--wpda-text-muted);
        background: transparent;
        border: 0;
        border-radius: 6px;
        cursor: pointer;
        transition: color var(--wpda-transition), background var(--wpda-transition), box-shadow var(--wpda-transition);
    }
    .wpda-scope button:hover { color: var(--wpda-text); }
    .wpda-scope button.is-active { color: var(--wpda-text); background: var(--wpda-surface); box-shadow: var(--wpda-shadow); }
    .wpda-scope__count {
        padding: 1px 7px;
        font-size: 11px;
        font-weight: 700;
        color: var(--wpda-text-muted);
        background: var(--wpda-border);
        border-radius: var(--wpda-radius-pill);
        font-variant-numeric: tabular-nums;
    }
    .wpda-scope button.is-active .wpda-scope__count { color: #fff; background: var(--wpda-primary); }

    /* ---- Filter bar (categories) ---- */
    .wpda-filterbar { display: flex; flex-direction: column; align-items: center; gap: 10px; margin-bottom: 22px; }
    .wpda-cats { display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin: 0; padding: 0; list-style: none; }
    .wpda-cats li { margin: 0; }
    .wpda-cats button {
        display: inline-block;
        padding: 6px 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--wpda-text-muted);
        background: var(--wpda-surface);
        border: 1px solid var(--wpda-border);
        border-radius: var(--wpda-radius-pill);
        cursor: pointer;
        transition: color var(--wpda-transition), background var(--wpda-transition), border-color var(--wpda-transition);
    }
    .wpda-cats button:hover { color: var(--wpda-text); border-color: var(--wpda-border-strong); }
    .wpda-cats button.is-active {
        color: #fff;
        background: var(--wpda-primary);
        border-color: var(--wpda-primary);
        box-shadow: 0 1px 2px rgba(var(--wpda-primary-rgb), .35);
    }
    .wpda-cats button .wpda-cats__n { margin-left: 6px; font-weight: 600; opacity: .5; font-variant-numeric: tabular-nums; }
    .wpda-cats button.is-active .wpda-cats__n { opacity: .72; }

    /* ---- Grid ---- */
    .wpda-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 18px;
    }

    .wpda-card {
        position: relative;
        display: flex;
        flex-direction: column;
        background: var(--wpda-surface);
        border: 1px solid var(--wpda-border);
        border-radius: 14px;
        box-shadow: var(--wpda-shadow);
        overflow: hidden;
        transition: transform var(--wpda-transition), box-shadow var(--wpda-transition), border-color var(--wpda-transition);
    }
    .wpda-card:hover { transform: translateY(-4px); border-color: rgba(var(--wpda-primary-rgb), .4); box-shadow: var(--wpda-shadow-hover); }
    .wpda-card.wpda-hide { display: none; }

    /* ---- Cover band ---- */
    .wpda-card__cover {
        position: relative;
        height: 64px;
        background:
            radial-gradient(130% 140% at 85% -20%, rgba(255, 255, 255, .55), transparent 60%),
            linear-gradient(135deg, hsl(var(--h, 220), 46%, 93%), hsl(calc(var(--h, 220) + 24), 42%, 89%));
    }
    .wpda-card__cover::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 62%, rgba(15, 23, 42, .035));
    }
    .wpda-card__cover-status {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 11px;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .01em;
        border-radius: var(--wpda-radius-pill);
        background: #fff;
        border: 1px solid var(--wpda-border);
        box-shadow: 0 1px 2px rgba(16, 24, 40, .08);
        white-space: nowrap;
    }
    .wpda-card__cover-status::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .wpda-card__cover-status.wpda-status--active { color: var(--wpda-success); }
    .wpda-card__cover-status.wpda-status--installed { color: var(--wpda-primary); }
    .wpda-card__cover-status.wpda-status--update { color: var(--wpda-warning); }

    /* ---- Body ---- */
    .wpda-card__body { position: relative; padding: 0 18px 16px; flex: 1; }
    .wpda-card__icon {
        position: relative;
        z-index: 1;
        margin: -26px 0 12px;
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 21px;
        font-weight: 800;
        line-height: 1;
        background: #fff;
        color: hsl(var(--h, 220), 58%, 46%);
        box-shadow: 0 4px 10px rgba(16, 24, 40, .14), 0 0 0 4px var(--wpda-surface);
    }
    .wpda-card__icon img { width: 100%; height: 100%; object-fit: cover; border-radius: 11px; }
    .wpda-card__title { margin: 0 0 7px; font-size: 15px; font-weight: 700; line-height: 1.3; }
    .wpda-card__title a { color: var(--wpda-text); text-decoration: none; transition: color var(--wpda-transition); }
    .wpda-card:hover .wpda-card__title a { color: var(--wpda-primary); }
    .wpda-card__title a:focus-visible { outline: 2px solid rgba(var(--wpda-primary-rgb), .5); outline-offset: 2px; border-radius: 3px; }
    .wpda-card__excerpt {
        margin: 0;
        font-size: 12.5px;
        line-height: 1.6;
        color: var(--wpda-text-muted);
        max-height: 60px;
        overflow: hidden;
    }

    /* ---- Footer ---- */
    .wpda-card__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 13px 18px;
        border-top: 1px solid var(--wpda-border);
    }
    .wpda-card__meta { display: flex; flex-direction: column; gap: 1px; min-width: 0; }
    .wpda-card__price { font-size: 16px; font-weight: 800; line-height: 1.2; color: var(--wpda-text); font-variant-numeric: tabular-nums; }
    .wpda-card__price--free { color: var(--wpda-success); }
    .wpda-card__version {
        font-size: 11px;
        font-weight: 600;
        color: var(--wpda-text-soft);
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .wpda-card__cta { display: inline-flex; align-items: center; gap: 8px; flex: none; }

    .wpda-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.2;
        border: 1px solid transparent;
        border-radius: var(--wpda-radius-sm);
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        transition: background var(--wpda-transition), border-color var(--wpda-transition), color var(--wpda-transition), box-shadow var(--wpda-transition), filter var(--wpda-transition);
    }
    .wpda-btn .fa, .wpda-btn svg { font-size: 11px; }
    .wpda a.wpda-btn--primary { color: #fff; background: var(--wpda-primary); box-shadow: 0 1px 2px rgba(var(--wpda-primary-rgb), .3); }
    .wpda a.wpda-btn--primary:hover { color: #fff; filter: brightness(.93); box-shadow: 0 4px 12px rgba(var(--wpda-primary-rgb), .42); }
    .wpda a.wpda-btn--update { color: #fff; background: var(--wpda-warning); box-shadow: 0 1px 2px rgba(var(--wpda-warning-rgb), .3); }
    .wpda a.wpda-btn--update:hover { color: #fff; filter: brightness(.95); box-shadow: 0 4px 12px rgba(var(--wpda-warning-rgb), .42); }
    .wpda a.wpda-btn--ghost { color: var(--wpda-text); background: var(--wpda-surface); border-color: var(--wpda-border-strong); }
    .wpda a.wpda-btn--ghost:hover { color: var(--wpda-primary); border-color: var(--wpda-primary); background: rgba(var(--wpda-primary-rgb), .06); }
    .wpda-btn:focus-visible { outline: 2px solid rgba(var(--wpda-primary-rgb), .5); outline-offset: 2px; }

    /* ---- States ---- */
    .wpda-state { max-width: 460px; margin: 40px auto; padding: 32px 24px; text-align: center; }
    .wpda-state__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        margin-bottom: 18px;
        color: var(--wpda-text-soft);
        background: var(--wpda-bg);
        border: 1px solid var(--wpda-border);
        border-radius: 50%;
    }
    .wpda-state__icon svg { width: 26px; height: 26px; }
    .wpda-state--error .wpda-state__icon { color: var(--wpda-danger); background: rgba(239, 68, 68, .08); border-color: rgba(239, 68, 68, .2); }
    .wpda-state__title { margin: 0 0 6px; font-size: 16px; font-weight: 700; color: var(--wpda-text); }
    .wpda-state__text { margin: 0; font-size: 13px; line-height: 1.6; color: var(--wpda-text-muted); }
    .wpda-empty-filter { grid-column: 1 / -1; }

    @media (prefers-reduced-motion: reduce) {
        .wpda-card, .wpda-card__title a, .wpda-btn,
        .wpda-cats button, .wpda-scope button, .wpda-search input { transition: none; }
        .wpda-card:hover { transform: none; }
    }
</style>

<div class="wrap w3eden">
    <div id="wpdm-admin-page-header" aria-hidden="true">
        <div id="wpdm-admin-main-header">
            <div class="media">
                <div class="media-body">
                    <div class="media">
                        <div class="media-body">
                            <h3><i class="fa fa-plug"></i> <?php _e('Add-Ons', 'download-manager'); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wpdm-admin-page-content wpda">
        <?php if ($total > 0) { ?>
            <header class="wpda-hero">
                <span class="wpda-hero__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><path d="M17.5 14v3.5M21 17.5h-3.5M17.5 21v-3.5M14 17.5h3.5"/></svg>
                </span>
                <h1 class="wpda-hero__title"><?php _e('Add-Ons', 'download-manager'); ?></h1>
                <p class="wpda-hero__subtitle"><?php _e('Extend WordPress Download Manager with official integrations — cloud storage, payment gateways, page builders, and more.', 'download-manager'); ?></p>
            </header>

            <dl class="wpda-stats">
                <div class="wpda-stat"><dt><?php _e('Add-ons', 'download-manager'); ?></dt><dd><?php echo (int) $total; ?></dd></div>
                <div class="wpda-stat"><dt><?php _e('Installed', 'download-manager'); ?></dt><dd><?php echo (int) $installed_count; ?></dd></div>
                <div class="wpda-stat"><dt><?php _e('Categories', 'download-manager'); ?></dt><dd><?php echo (int) $cat_count; ?></dd></div>
            </dl>

            <div class="wpda-sep"></div>

            <div class="wpda-toolbar">
                <div class="wpda-search" id="wpda-search-wrap">
                    <span class="wpda-search__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="search" id="wpda-search" autocomplete="off" aria-label="<?php esc_attr_e('Search add-ons', 'download-manager'); ?>" placeholder="<?php esc_attr_e('Search add-ons…', 'download-manager'); ?>">
                    <span class="wpda-search__kbd" aria-hidden="true">/</span>
                    <button type="button" class="wpda-search__clear" id="wpda-search-clear" aria-label="<?php esc_attr_e('Clear search', 'download-manager'); ?>">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="wpda-scope" role="tablist" aria-label="<?php esc_attr_e('Filter by install state', 'download-manager'); ?>">
                    <button type="button" class="is-active" data-scope="all" role="tab" aria-selected="true"><?php _e('All', 'download-manager'); ?></button>
                    <button type="button" data-scope="installed" role="tab" aria-selected="false">
                        <?php _e('Installed', 'download-manager'); ?>
                        <span class="wpda-scope__count"><?php echo (int) $installed_count; ?></span>
                    </button>
                </div>
            </div>

            <div class="wpda-filterbar">
                <ul class="wpda-cats" id="filter-mods" role="tablist" aria-label="<?php esc_attr_e('Filter by category', 'download-manager'); ?>">
                    <li><button type="button" class="is-active" data-cat="all"><?php _e('All Add-Ons', 'download-manager'); ?><span class="wpda-cats__n"><?php echo (int) $total; ?></span></button></li>
                    <?php if (is_array($cats)) { foreach ($cats as $cat) { $c_n = isset($cat_counts[$cat->slug]) ? (int) $cat_counts[$cat->slug] : 0; ?>
                        <li><button type="button" data-cat="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html($cat->name); ?><span class="wpda-cats__n"><?php echo $c_n; ?></span></button></li>
                    <?php } } ?>
                </ul>
            </div>

            <div class="wpda-grid" id="addonlist">
                <?php foreach ($items as $item) {
                    $search_index = strtolower(wp_strip_all_tags($item->title . ' ' . $item->excerpt));

                    $clean_title  = trim(wp_strip_all_tags($item->title));
                    $mono         = strtoupper(mb_substr($clean_title, 0, 1));
                    if ($mono === '') $mono = 'W';
                    $hue          = abs(crc32($item->title)) % 360;

                    if ($item->update)         { $status_class = 'update';    $status_label = __('Update available', 'download-manager'); }
                    elseif ($item->active)     { $status_class = 'active';    $status_label = __('Active', 'download-manager'); }
                    elseif ($item->installed)  { $status_class = 'installed'; $status_label = __('Installed', 'download-manager'); }
                    else                       { $status_class = '';          $status_label = ''; }

                    if ($item->update)            $linklabel = '<i class="fa fa-sync"></i> ' . esc_html__('Update', 'download-manager');
                    elseif ($item->installed)     $linklabel = '<i class="fa fa-sync"></i> ' . esc_html__('Re-Install', 'download-manager');
                    else                          $linklabel = '<i class="fa fa-plus-circle"></i> ' . esc_html__('Install', 'download-manager');
                    ?>
                    <article class="wpda-card all <?php echo esc_attr($item->categories); ?>"
                             style="--h: <?php echo (int) $hue; ?>"
                             data-search="<?php echo esc_attr($search_index); ?>"
                             data-installed="<?php echo $item->installed ? '1' : '0'; ?>">
                        <div class="wpda-card__cover">
                            <?php if ($status_label) { ?>
                                <span class="wpda-card__cover-status wpda-status--<?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_label); ?></span>
                            <?php } ?>
                        </div>
                        <div class="wpda-card__body">
                            <span class="wpda-card__icon" aria-hidden="true"><?php echo esc_html($mono); ?></span>
                            <h3 class="wpda-card__title">
                                <a target="_blank" rel="noopener" title="<?php echo esc_attr($item->title); ?>"
                                   href="<?php echo esc_url($item->link); ?>"><?php echo esc_html($item->title); ?></a>
                            </h3>
                            <p class="wpda-card__excerpt"><?php echo wp_kses_post($item->excerpt); ?></p>
                        </div>
                        <div class="wpda-card__footer">
                            <div class="wpda-card__meta">
                                <?php if ($item->price > 0) { ?>
                                    <span class="wpda-card__price"><?php echo esc_html($item->currency . $item->price); ?></span>
                                <?php } else { ?>
                                    <span class="wpda-card__price wpda-card__price--free"><?php _e('Free', 'download-manager'); ?></span>
                                <?php } ?>
                                <?php if ($item->version) { ?>
                                    <span class="wpda-card__version"><?php echo esc_html('v' . $item->version); ?></span>
                                <?php } ?>
                            </div>
                            <div class="wpda-card__cta">
                                <?php if ($item->price > 0) { ?>
                                    <a class="wpda-btn wpda-btn--primary" target="_blank" rel="noopener"
                                       href="<?php echo esc_url('https://www.wpdownloadmanager.com/cart/?addtocart=' . $item->ID); ?>">
                                        <i class="fa fa-shopping-cart"></i> <?php _e('Buy Now', 'download-manager'); ?>
                                    </a>
                                <?php } else { ?>
                                    <a class="wpda-btn <?php echo $item->update ? 'wpda-btn--update' : ($item->installed ? 'wpda-btn--ghost' : 'wpda-btn--primary'); ?>"
                                       target="_blank" rel="noopener"
                                       href="<?php echo esc_url($item->link); ?>"><?php echo $linklabel; ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </article>
                <?php } ?>

                <div class="wpda-state wpda-empty-filter" id="wpda-empty-filter" style="display:none">
                    <div class="wpda-state__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <h3 class="wpda-state__title"><?php _e('No add-ons match your filters', 'download-manager'); ?></h3>
                    <p class="wpda-state__text"><?php _e('Try a different search term or category, or reset to view all add-ons.', 'download-manager'); ?></p>
                </div>
            </div>
        <?php } else {
            \WPDM\__\Session::clear('wpdm_addon_store_data');
            \WPDM\__\Session::clear('wpdm_addon_store_cats');
            ?>
            <div class="wpda-state wpda-state--error">
                <div class="wpda-state__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <h3 class="wpda-state__title"><?php _e('Could not reach the add-on store', 'download-manager'); ?></h3>
                <p class="wpda-state__text"><?php _e('We couldn\'t connect to the server. Please check your connection and reload the page to try again.', 'download-manager'); ?></p>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    jQuery(function ($) {
        var $grid       = $('#addonlist');
        var $cards      = $grid.find('.wpda-card');
        var $emptyState = $('#wpda-empty-filter');
        var $searchWrap = $('#wpda-search-wrap');
        var $search     = $('#wpda-search');

        var state = { q: '', cat: 'all', scope: 'all' };

        function render() {
            var q = state.q.toLowerCase().trim();
            var visible = 0;

            $cards.each(function () {
                var $c = $(this);
                var matchQ     = !q || ($c.data('search') + '').indexOf(q) > -1;
                var matchCat   = state.cat === 'all' || $c.hasClass(state.cat);
                var matchScope = state.scope === 'all' || String($c.data('installed')) === '1';
                var show = matchQ && matchCat && matchScope;
                $c.toggleClass('wpda-hide', !show);
                if (show) visible++;
            });

            $emptyState.toggle(visible === 0);
        }

        $search.on('input', function () {
            state.q = this.value;
            $searchWrap.toggleClass('is-filled', this.value.length > 0);
            render();
        });

        $('#wpda-search-clear').on('click', function () {
            state.q = '';
            $search.val('').focus();
            $searchWrap.removeClass('is-filled');
            render();
        });

        $('#filter-mods button').on('click', function () {
            state.cat = $(this).data('cat');
            $('#filter-mods button').removeClass('is-active').attr('aria-selected', 'false');
            $(this).addClass('is-active').attr('aria-selected', 'true');
            render();
        });

        $('.wpda-scope button').on('click', function () {
            state.scope = $(this).data('scope');
            $('.wpda-scope button').removeClass('is-active').attr('aria-selected', 'false');
            $(this).addClass('is-active').attr('aria-selected', 'true');
            render();
        });

        $(document).on('keydown', function (e) {
            if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey) return;
            var tag = (e.target.tagName || '').toLowerCase();
            if (tag === 'input' || tag === 'textarea' || e.target.isContentEditable) return;
            e.preventDefault();
            $search.focus();
        });
    });
</script>
