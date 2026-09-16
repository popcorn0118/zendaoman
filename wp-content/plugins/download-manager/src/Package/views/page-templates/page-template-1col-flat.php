<!-- WPDM Template: Slate -->
<?php if(!defined("ABSPATH")) die(); ?>
<style>
    /* ── Slate – flat minimal layout ── */
    .w3eden .wpdm-slate {
        --sl-primary: var(--color-primary, #6366f1);
        --sl-primary-rgb: var(--color-primary-rgb, 99, 102, 241);
        --sl-text: var(--color-text, #1e293b);
        --sl-text-secondary: #475569;
        --sl-text-muted: var(--color-muted, #94a3b8);
        --sl-bg: var(--bg-body, #ffffff);
        --sl-bg-muted: #f8fafc;
        --sl-bg-accent: #f1f5f9;
        --sl-border: var(--color-border, #e2e8f0);
        --sl-radius: 8px;
        --sl-transition: 150ms ease;
        max-width: 1600px;
        margin: 0 auto;
    }

    .w3eden .wpdm-slate .wpdm_hide { display: none; }

    /* ── Featured image ── */
    .w3eden .wpdm-slate__figure {
        border-radius: var(--sl-radius);
        overflow: hidden;
        line-height: 0;
        background: var(--sl-bg-muted);
        margin-bottom: 20px;
    }
    .w3eden .wpdm-slate__figure img {
        width: 100%;
        height: auto;
        display: block;
    }

    /* ── Inline metadata ── */
    .w3eden .wpdm-slate__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding-bottom: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--sl-border);
        font-size: 13px;
    }
    .w3eden .wpdm-slate__meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .w3eden .wpdm-slate__meta-label {
        color: var(--sl-text-muted);
        font-weight: 500;
    }
    .w3eden .wpdm-slate__meta-value {
        font-weight: 700;
        color: var(--sl-text);
    }

    /* ── CTA ── */
    .w3eden .wpdm-slate__cta {
        margin-bottom: 24px;
    }
    .w3eden .wpdm-slate .wpdmpp-product-price {
        font-size: 22px;
        font-weight: 800;
        color: var(--sl-text);
        line-height: 1;
        margin-bottom: 10px;
    }
    .w3eden .wpdm-slate__cta .btn,
    .w3eden .wpdm-slate__cta a.btn,
    .w3eden .wpdm-slate__cta form .btn,
    .w3eden .wpdm-slate__cta .wpdm-download-link .btn {
        width: 100%;
        font-size: 15px;
        padding: 12px 24px;
        border-radius: var(--sl-radius);
    }
    .w3eden .wpdm-slate__free {
        margin-top: 8px;
        text-align: center;
    }
    .w3eden .wpdm-slate__free-label {
        font-size: 11px;
        color: var(--sl-text-muted);
        margin-bottom: 4px;
    }
    .w3eden .wpdm-slate__free .btn,
    .w3eden .wpdm-slate__free a.btn {
        width: 100%;
        font-size: 13px;
        padding: 8px 16px;
        border-radius: var(--sl-radius);
    }

    /* ── Description ── */
    .w3eden .wpdm-slate__heading {
        font-size: 15px;
        font-weight: 700;
        color: var(--sl-text);
        margin: 0 0 12px !important;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .w3eden .wpdm-slate__description {
        font-size: 15px;
        line-height: 1.8;
        color: var(--sl-text-secondary);
    }
    .w3eden .wpdm-slate__description p:last-child { margin-bottom: 0; }

    /* ── Sections ── */
    .w3eden .wpdm-slate__section {
        margin-top: 28px;
        padding-top: 28px;
        border-top: 1px solid var(--sl-border);
    }
    .w3eden .wpdm-slate__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .w3eden .wpdm-slate__tags a {
        display: inline-block;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 600;
        color: var(--sl-text-secondary);
        background: var(--sl-bg-accent);
        border-radius: var(--sl-radius);
        text-decoration: none;
        transition: all var(--sl-transition);
    }
    .w3eden .wpdm-slate__tags a:hover {
        color: #fff;
        background: var(--sl-primary);
    }

    /* ── Author ── */
    .w3eden .wpdm-slate__author {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid var(--sl-border);
    }
    .w3eden .wpdm-slate__author img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .w3eden .wpdm-slate__author-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--sl-text);
    }
    .w3eden .wpdm-slate__author-date {
        font-size: 12px;
        color: var(--sl-text-muted);
        margin-left: auto;
    }

    /* ── Dark mode ── */
    .w3eden.dark-mode .wpdm-slate {
        --sl-text: var(--dm-text, #f1f5f9);
        --sl-text-secondary: var(--dm-text-secondary, #cbd5e1);
        --sl-text-muted: var(--dm-text-muted, #94a3b8);
        --sl-bg: var(--dm-bg, #0f172a);
        --sl-bg-muted: var(--dm-bg-secondary, #1e293b);
        --sl-bg-accent: var(--dm-bg-tertiary, #334155);
        --sl-border: var(--dm-border, rgba(255, 255, 255, .1));
    }

</style>

<div class="wpdm-slate">

    <!-- ── Featured image ── -->
    <div class="wpdm-slate__figure">
        [thumb_900x0]
    </div>

    <!-- ── Inline metadata ── -->
    <div class="wpdm-slate__meta">
        <div class="wpdm-slate__meta-item [hide_empty:version]">
            <span class="wpdm-slate__meta-label">[txt=Version]</span>
            <span class="wpdm-slate__meta-value">[version]</span>
        </div>
        <div class="wpdm-slate__meta-item [hide_empty:download_count]">
            <span class="wpdm-slate__meta-label">[txt=Download]</span>
            <span class="wpdm-slate__meta-value">[download_count]</span>
        </div>
        <div class="wpdm-slate__meta-item [hide_empty:file_size]">
            <span class="wpdm-slate__meta-label">[txt=File Size]</span>
            <span class="wpdm-slate__meta-value">[file_size]</span>
        </div>
        <div class="wpdm-slate__meta-item [hide_empty:file_count]">
            <span class="wpdm-slate__meta-label">[txt=File Count]</span>
            <span class="wpdm-slate__meta-value">[file_count]</span>
        </div>
        <div class="wpdm-slate__meta-item [hide_empty:create_date]">
            <span class="wpdm-slate__meta-label">[txt=Create Date]</span>
            <span class="wpdm-slate__meta-value">[create_date]</span>
        </div>
        <div class="wpdm-slate__meta-item [hide_empty:update_date]">
            <span class="wpdm-slate__meta-label">[txt=Last Updated]</span>
            <span class="wpdm-slate__meta-value">[update_date]</span>
        </div>
    </div>

    <!-- ── CTA ── -->
    <div class="wpdm-slate__cta">
        [download_link]
        <div class="wpdm-slate__free [hide_empty:free_download_btn]">
            <div class="wpdm-slate__free-label">[txt=or download free]</div>
            [free_download_btn]
        </div>
    </div>

    <!-- ── Description ── -->
    <div class="wpdm-slate__section">
        <h3 class="wpdm-slate__heading">[txt=Description]</h3>
        <div class="wpdm-slate__description">[description]</div>
    </div>

    <!-- ── Changelog ── -->
    <div class="wpdm-slate__section [hide_empty:changelog]">
        [changelog]
    </div>

    <!-- ── Categories & Tags ── -->
    <div class="wpdm-slate__section [hide_empty:categories]">
        <h3 class="wpdm-slate__heading">[txt=Categories] &amp; [txt=Tags]</h3>
        <div class="wpdm-slate__tags">
            [categories]
            [tags]
        </div>
    </div>

    <!-- ── Similar Downloads ── -->
    <div class="wpdm-slate__section [hide_empty:similar_downloads]">
        <h3 class="wpdm-slate__heading">[txt=Similar Downloads]</h3>
        [similar_downloads]
    </div>

    <!-- ── Author ── -->
    <div class="wpdm-slate__author [hide_empty:author_name]">
        [avatar]
        <span class="wpdm-slate__author-name">[author_name]</span>
        <span class="wpdm-slate__author-date">[txt=Updated] [update_date]</span>
    </div>

</div>
