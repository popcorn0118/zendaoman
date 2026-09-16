<!-- WPDM Template: Exhibit -->
<?php if(!defined("ABSPATH")) die(); ?>
<style>
    /* ── Exhibit – extended file preview layout ── */
    .w3eden .wpdm-exhibit {
        --ex-primary: var(--color-primary, #6366f1);
        --ex-primary-rgb: var(--color-primary-rgb, 99, 102, 241);
        --ex-text: var(--color-text, #1e293b);
        --ex-text-secondary: #475569;
        --ex-text-muted: var(--color-muted, #94a3b8);
        --ex-bg: var(--bg-body, #ffffff);
        --ex-bg-muted: #f8fafc;
        --ex-bg-accent: #f1f5f9;
        --ex-border: var(--color-border, #e2e8f0);
        --ex-radius: 8px;
        --ex-transition: 150ms ease;
        max-width: 1600px;
        margin: 0 auto;
    }

    .w3eden .wpdm-exhibit .wpdm_hide { display: none; }

    /* ── Metadata bar ── */
    .w3eden .wpdm-exhibit__bar {
        display: flex;
        flex-wrap: wrap;
        gap: 1px;
        background: var(--ex-border);
        border: 1px solid var(--ex-border);
        border-radius: var(--ex-radius);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .w3eden .wpdm-exhibit__cell {
        flex: 1;
        min-width: 120px;
        padding: 12px 16px;
        text-align: center;
        background: var(--ex-bg);
    }
    .w3eden .wpdm-exhibit__cell-label {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--ex-text-muted);
        margin-bottom: 2px;
    }
    .w3eden .wpdm-exhibit__cell-value {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: var(--ex-text);
    }

    /* ── Description ── */
    .w3eden .wpdm-exhibit__description {
        font-size: 15px;
        line-height: 1.8;
        color: var(--ex-text-secondary);
        margin-bottom: 28px;
    }
    .w3eden .wpdm-exhibit__description p:last-child { margin-bottom: 0; }

    /* ── File list extended ── */
    .w3eden .wpdm-exhibit__files {
        margin-bottom: 24px;
    }
    .w3eden .wpdm-exhibit__heading {
        font-size: 16px;
        font-weight: 700;
        color: var(--ex-text);
        margin: 0 0 14px !important;
    }

    /* ── CTA ── */
    .w3eden .wpdm-exhibit__cta {
        padding: 20px;
        background: var(--ex-bg-accent);
        border: 1px solid var(--ex-border);
        border-radius: var(--ex-radius);
        margin-bottom: 28px;
    }
    .w3eden .wpdm-exhibit .wpdmpp-product-price {
        font-size: 22px;
        font-weight: 800;
        color: var(--ex-text);
        line-height: 1;
        margin-bottom: 10px;
    }
    .w3eden .wpdm-exhibit__cta .btn,
    .w3eden .wpdm-exhibit__cta a.btn,
    .w3eden .wpdm-exhibit__cta form .btn,
    .w3eden .wpdm-exhibit__cta .wpdm-download-link .btn {
        font-size: 15px;
        padding: 12px 32px;
        border-radius: var(--ex-radius);
    }
    .w3eden .wpdm-exhibit__free {
        margin-top: 10px;
    }
    .w3eden .wpdm-exhibit__free-label {
        font-size: 11px;
        color: var(--ex-text-muted);
        margin-bottom: 4px;
    }
    .w3eden .wpdm-exhibit__free .btn,
    .w3eden .wpdm-exhibit__free a.btn {
        font-size: 13px;
        padding: 7px 20px;
        border-radius: var(--ex-radius);
    }

    /* ── Sections ── */
    .w3eden .wpdm-exhibit__section {
        margin-top: 28px;
        padding-top: 28px;
        border-top: 1px solid var(--ex-border);
    }
    .w3eden .wpdm-exhibit__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .w3eden .wpdm-exhibit__tags a {
        display: inline-block;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 600;
        color: var(--ex-text-secondary);
        background: var(--ex-bg-muted);
        border-radius: var(--ex-radius);
        text-decoration: none;
        transition: all var(--ex-transition);
    }
    .w3eden .wpdm-exhibit__tags a:hover {
        color: #fff;
        background: var(--ex-primary);
    }

    /* ── Author ── */
    .w3eden .wpdm-exhibit__author {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid var(--ex-border);
        font-size: 13px;
    }
    .w3eden .wpdm-exhibit__author img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .w3eden .wpdm-exhibit__author-name {
        font-weight: 600;
        color: var(--ex-text);
    }
    .w3eden .wpdm-exhibit__author-date {
        margin-left: auto;
        color: var(--ex-text-muted);
        font-size: 12px;
    }

    /* ── Responsive ── */
    @media (max-width: 575px) {
        .w3eden .wpdm-exhibit__cell {
            min-width: calc(50% - 1px);
        }
    }

    /* ── Dark mode ── */
    .w3eden.dark-mode .wpdm-exhibit {
        --ex-text: var(--dm-text, #f1f5f9);
        --ex-text-secondary: var(--dm-text-secondary, #cbd5e1);
        --ex-text-muted: var(--dm-text-muted, #94a3b8);
        --ex-bg: var(--dm-bg, #0f172a);
        --ex-bg-muted: var(--dm-bg-secondary, #1e293b);
        --ex-bg-accent: var(--dm-bg-tertiary, #334155);
        --ex-border: var(--dm-border, rgba(255, 255, 255, .1));
    }

</style>

<div class="wpdm-exhibit">

    <!-- ── Metadata bar ── -->
    <div class="wpdm-exhibit__bar">
        <div class="wpdm-exhibit__cell [hide_empty:version]">
            <span class="wpdm-exhibit__cell-label">[txt=Version]</span>
            <span class="wpdm-exhibit__cell-value">[version]</span>
        </div>
        <div class="wpdm-exhibit__cell [hide_empty:file_size]">
            <span class="wpdm-exhibit__cell-label">[txt=File Size]</span>
            <span class="wpdm-exhibit__cell-value">[file_size]</span>
        </div>
        <div class="wpdm-exhibit__cell [hide_empty:download_count]">
            <span class="wpdm-exhibit__cell-label">[txt=Downloads]</span>
            <span class="wpdm-exhibit__cell-value">[download_count]</span>
        </div>
        <div class="wpdm-exhibit__cell [hide_empty:file_count]">
            <span class="wpdm-exhibit__cell-label">[txt=Files]</span>
            <span class="wpdm-exhibit__cell-value">[file_count]</span>
        </div>
        <div class="wpdm-exhibit__cell [hide_empty:create_date]">
            <span class="wpdm-exhibit__cell-label">[txt=Published]</span>
            <span class="wpdm-exhibit__cell-value">[create_date]</span>
        </div>
        <div class="wpdm-exhibit__cell [hide_empty:update_date]">
            <span class="wpdm-exhibit__cell-label">[txt=Updated]</span>
            <span class="wpdm-exhibit__cell-value">[update_date]</span>
        </div>
    </div>

    <!-- ── Description ── -->
    <div class="wpdm-exhibit__description">[description]</div>

    <!-- ── CTA ── -->
    <div class="wpdm-exhibit__cta">
        [download_link_extended]
        <div class="wpdm-exhibit__free [hide_empty:free_download_btn]">
            <div class="wpdm-exhibit__free-label">[txt=or download free]</div>
            [free_download_btn]
        </div>
    </div>

    <!-- ── Changelog ── -->
    <div class="wpdm-exhibit__section [hide_empty:changelog]">
        [changelog]
    </div>

    <!-- ── Categories & Tags ── -->
    <div class="wpdm-exhibit__section [hide_empty:categories]">
        <h3 class="wpdm-exhibit__heading">[txt=Categories] &amp; [txt=Tags]</h3>
        <div class="wpdm-exhibit__tags">
            [categories]
            [tags]
        </div>
    </div>

    <!-- ── Similar Downloads ── -->
    <div class="wpdm-exhibit__section [hide_empty:similar_downloads]">
        <h3 class="wpdm-exhibit__heading">[txt=Similar Downloads]</h3>
        [similar_downloads]
    </div>

    <!-- ── Author ── -->
    <div class="wpdm-exhibit__author [hide_empty:author_name]">
        [avatar]
        <span class="wpdm-exhibit__author-name">[author_name]</span>
        <span class="wpdm-exhibit__author-date">[txt=Updated] [update_date]</span>
    </div>

</div>
