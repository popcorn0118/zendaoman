<!-- WPDM Template: Compact -->
<?php if(!defined("ABSPATH")) die(); ?>
<style>
    /* ── Compact – simplified two-column layout ── */
    .w3eden .wpdm-compact {
        --cp-primary: var(--color-primary, #6366f1);
        --cp-primary-rgb: var(--color-primary-rgb, 99, 102, 241);
        --cp-text: var(--color-text, #1e293b);
        --cp-text-secondary: #475569;
        --cp-text-muted: var(--color-muted, #94a3b8);
        --cp-bg: var(--bg-body, #ffffff);
        --cp-bg-muted: #f8fafc;
        --cp-bg-accent: #f1f5f9;
        --cp-border: var(--color-border, #e2e8f0);
        --cp-radius: 10px;
        --cp-transition: 150ms ease;
        max-width: 1600px;
        margin: 0 auto;
    }

    .w3eden .wpdm-compact .wpdm_hide { display: none; }

    /* ── Layout ── */
    .w3eden .wpdm-compact__layout {
        display: flex;
        gap: 28px;
        align-items: flex-start;
    }

    /* ── Sidebar ── */
    .w3eden .wpdm-compact__sidebar {
        flex: 0 0 320px;
        max-width: 320px;
    }

    /* ── CTA card ── */
    .w3eden .wpdm-compact__card {
        background: var(--cp-bg);
        border: 1px solid var(--cp-border);
        border-radius: var(--cp-radius);
        padding: 20px;
        margin-bottom: 16px;
    }
    .w3eden .wpdm-compact .wpdmpp-product-price {
        font-size: 22px;
        font-weight: 800;
        color: var(--cp-text);
        line-height: 1;
        margin-bottom: 10px;
    }
    .w3eden .wpdm-compact__card .btn,
    .w3eden .wpdm-compact__card a.btn,
    .w3eden .wpdm-compact__card form .btn,
    .w3eden .wpdm-compact__card .wpdm-download-link .btn {
        width: 100%;
        font-size: 16px !important;
        padding: 14px 20px !important;

    }
    .w3eden .wpdm-compact__free {
        margin-top: 8px;
        text-align: center;
    }
    .w3eden .wpdm-compact__free-label {
        font-size: 11px;
        color: var(--cp-text-muted);
        margin-bottom: 4px;
    }
    .w3eden .wpdm-compact__free .btn,
    .w3eden .wpdm-compact__free a.btn {
        width: 100%;
        font-size: 13px;
        padding: 7px 16px;
        border-radius: var(--cp-radius);
    }

    /* ── Expire notice ── */
    .w3eden .wpdm-compact__expire {
        margin-top: 12px;
        padding: 8px 12px;
        font-size: 12px;
        color: #92400e;
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 6px;
    }

    /* ── Metadata list ── */
    .w3eden .wpdm-compact__meta {
        border: 1px solid var(--cp-border);
        border-radius: var(--cp-radius);
        overflow: hidden;
    }
    .w3eden .wpdm-compact__row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 14px;
        font-size: 13px;
        border-bottom: 1px solid var(--cp-border);
    }
    .w3eden .wpdm-compact__row:last-child { border-bottom: none; }
    .w3eden .wpdm-compact__row:nth-child(even) {
        background: var(--cp-bg-muted);
    }
    .w3eden .wpdm-compact__row-label {
        color: var(--cp-text-muted);
        font-weight: 500;
    }
    .w3eden .wpdm-compact__row-value {
        font-weight: 600;
        color: #ffffff;
        background-color: var(--color-primary);
        padding: 0 6px;
        border-radius: 3px;
    }

    /* ── Main content ── */
    .w3eden .wpdm-compact__main {
        flex: 1;
        min-width: 0;
    }
    .w3eden .wpdm-compact__description {
        font-size: 15px;
        line-height: 1.8;
        color: var(--cp-text-secondary);
    }
    .w3eden .wpdm-compact__description p:last-child { margin-bottom: 0; }

    /* ── Sections ── */
    .w3eden .wpdm-compact__section {
        margin-top: 28px;
        padding-top: 28px;
        border-top: 1px solid var(--cp-border);
    }
    .w3eden .wpdm-compact__heading {
        font-size: 16px;
        font-weight: 700;
        color: var(--cp-text);
        margin: 0 0 14px !important;
    }
    .w3eden .wpdm-compact__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .w3eden .wpdm-compact__tags a {
        display: inline-block;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 600;
        color: var(--cp-text-secondary);
        background: var(--cp-bg-accent);
        border-radius: 6px;
        text-decoration: none;
        transition: all var(--cp-transition);
    }
    .w3eden .wpdm-compact__tags a:hover {
        color: #fff;
        background: var(--cp-primary);
    }

    /* ── Responsive ── */
    @media (max-width: 767px) {
        .w3eden .wpdm-compact__layout {
            flex-direction: column;
        }
        .w3eden .wpdm-compact__sidebar {
            flex: none;
            max-width: 100%;
        }
    }

    /* ── Dark mode ── */
    .w3eden.dark-mode .wpdm-compact {
        --cp-text: var(--dm-text, #f1f5f9);
        --cp-text-secondary: var(--dm-text-secondary, #cbd5e1);
        --cp-text-muted: var(--dm-text-muted, #94a3b8);
        --cp-bg: var(--dm-bg, #0f172a);
        --cp-bg-muted: var(--dm-bg-secondary, #1e293b);
        --cp-bg-accent: var(--dm-bg-tertiary, #334155);
        --cp-border: var(--dm-border, rgba(255, 255, 255, .1));
    }
    .w3eden.dark-mode .wpdm-compact__expire {
        color: #fbbf24;
        background: rgba(251, 191, 36, .1);
        border-color: rgba(251, 191, 36, .2);
    }

</style>

<div class="wpdm-compact">

    <div class="wpdm-compact__layout">

        <!-- ── Sidebar ── -->
        <div class="wpdm-compact__sidebar">

            <div class="wpdm-compact__card">
                [download_link]
                <div class="wpdm-compact__free [hide_empty:free_download_btn]">
                    <div class="wpdm-compact__free-label">[txt=or download free]</div>
                    [free_download_btn]
                </div>
                <div class="wpdm-compact__expire [hide_empty:expire_date]">
                    [txt=Download is available until] [expire_date]
                </div>
            </div>

            <div class="wpdm-compact__meta">
                <div class="wpdm-compact__row [hide_empty:version]">
                    <span class="wpdm-compact__row-label">[txt=Version]</span>
                    <span class="wpdm-compact__row-value">[version]</span>
                </div>
                <div class="wpdm-compact__row [hide_empty:download_count]">
                    <span class="wpdm-compact__row-label">[txt=Download]</span>
                    <span class="wpdm-compact__row-value">[download_count]</span>
                </div>
                <div class="wpdm-compact__row [hide_empty:file_size]">
                    <span class="wpdm-compact__row-label">[txt=File Size]</span>
                    <span class="wpdm-compact__row-value">[file_size]</span>
                </div>
                <div class="wpdm-compact__row [hide_empty:file_count]">
                    <span class="wpdm-compact__row-label">[txt=File Count]</span>
                    <span class="wpdm-compact__row-value">[file_count]</span>
                </div>
                <div class="wpdm-compact__row [hide_empty:create_date]">
                    <span class="wpdm-compact__row-label">[txt=Create Date]</span>
                    <span class="wpdm-compact__row-value">[create_date]</span>
                </div>
                <div class="wpdm-compact__row [hide_empty:update_date]">
                    <span class="wpdm-compact__row-label">[txt=Last Updated]</span>
                    <span class="wpdm-compact__row-value">[update_date]</span>
                </div>
            </div>

        </div>

        <!-- ── Main ── -->
        <div class="wpdm-compact__main">

            <div class="wpdm-compact__description">[description]</div>

            <div class="wpdm-compact__section [hide_empty:categories]">
                <h3 class="wpdm-compact__heading">[txt=Categories] &amp; [txt=Tags]</h3>
                <div class="wpdm-compact__tags">
                    [categories]
                    [tags]
                </div>
            </div>

            <div class="wpdm-compact__section [hide_empty:changelog]">
                [changelog]
            </div>

            <div class="wpdm-compact__section [hide_empty:similar_downloads]">
                <h3 class="wpdm-compact__heading">[txt=Similar Downloads]</h3>
                [similar_downloads]
            </div>

        </div>

    </div>

</div>
