<!-- WPDM Template: Simple -->
<?php if(!defined("ABSPATH")) die(); ?>
<style>
    /* ── Simple – clean editorial layout ── */
    .w3eden .wpdm-simple {
        --sp-primary: var(--color-primary, #6366f1);
        --sp-primary-rgb: var(--color-primary-rgb, 99, 102, 241);
        --sp-text: var(--color-text, #1e293b);
        --sp-text-secondary: #475569;
        --sp-text-muted: var(--color-muted, #94a3b8);
        --sp-bg: var(--bg-body, #ffffff);
        --sp-bg-muted: #f1f5f9;
        --sp-border: var(--color-border, #e2e8f0);
        --sp-radius: 10px;
        --sp-transition: 150ms ease;
        max-width: 960px;
        margin: 0 auto;
    }

    .w3eden .wpdm-simple .wpdm_hide { display: none; }

    /* ── Byline ── */
    .w3eden .wpdm-simple__byline {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }
    .w3eden .wpdm-simple__byline img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .w3eden .wpdm-simple__byline-name {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--sp-text);
    }
    .w3eden .wpdm-simple__byline-date {
        display: block;
        font-size: 12px;
        color: var(--sp-text-muted);
    }

    /* ── Featured image ── */
    .w3eden .wpdm-simple__figure {
        border-radius: var(--sp-radius);
        overflow: hidden;
        line-height: 0;
        background: var(--sp-bg-muted);
    }
    .w3eden .wpdm-simple__figure img {
        width: 100%;
        height: auto;
        display: block;
    }

    /* ── Metadata chips ── */
    .w3eden .wpdm-simple__chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 20px;
    }
    .w3eden .wpdm-simple__chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 600;
        color: var(--sp-text-secondary);
        background: var(--sp-bg-muted);
        border-radius: 20px;
    }
    .w3eden .wpdm-simple__chip svg {
        width: 13px;
        height: 13px;
        opacity: .55;
        flex-shrink: 0;
    }

    /* ── CTA ── */
    .w3eden .wpdm-simple__action {
        margin-top: 24px;
        max-width: 400px;
    }
    .w3eden .wpdm-simple .wpdmpp-product-price {
        font-size: 24px;
        font-weight: 800;
        color: var(--sp-text);
        line-height: 1;
        margin-bottom: 10px;
    }
    .w3eden .wpdm-simple__action .btn,
    .w3eden .wpdm-simple__action a.btn,
    .w3eden .wpdm-simple__action form .btn,
    .w3eden .wpdm-simple__action .wpdm-download-link .btn {
        width: 100%;
        font-size: 15px;
        padding: 12px 24px;
        border-radius: var(--sp-radius);
    }
    .w3eden .wpdm-simple__free {
        margin-top: 8px;
        text-align: center;
    }
    .w3eden .wpdm-simple__free-label {
        font-size: 11px;
        color: var(--sp-text-muted);
        margin-bottom: 4px;
    }
    .w3eden .wpdm-simple__free .btn,
    .w3eden .wpdm-simple__free a.btn {
        width: 100%;
        font-size: 13px;
        padding: 8px 16px;
        border-radius: var(--sp-radius);
    }

    /* ── Divider ── */
    .w3eden .wpdm-simple__divider {
        height: 1px;
        background: var(--sp-border);
        margin: 32px 0;
    }

    /* ── Prose (description) ── */
    .w3eden .wpdm-simple__prose {
        font-size: 15px;
        line-height: 1.85;
        color: var(--sp-text-secondary);
    }
    .w3eden .wpdm-simple__prose p:last-child { margin-bottom: 0; }

    /* ── Content sections ── */
    .w3eden .wpdm-simple__section {
        margin-top: 32px;
        padding-top: 32px;
        border-top: 1px solid var(--sp-border);
    }
    .w3eden .wpdm-simple__heading {
        font-size: 16px;
        font-weight: 700;
        color: var(--sp-text);
        margin: 0 0 16px !important;
    }

    /* ── Taxonomy pills ── */
    .w3eden .wpdm-simple__pills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .w3eden .wpdm-simple__pills a {
        display: inline-block;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 500;
        color: var(--sp-text-secondary);
        background: var(--sp-bg-muted);
        border-radius: 20px;
        text-decoration: none;
        transition: all var(--sp-transition);
    }
    .w3eden .wpdm-simple__pills a:hover {
        color: #fff;
        background: var(--sp-primary);
    }

    /* ── Footer ── */
    .w3eden .wpdm-simple__footer {
        margin-top: 32px;
        padding-top: 20px;
        border-top: 1px solid var(--sp-border);
        font-size: 12px;
        color: var(--sp-text-muted);
    }

    /* ── Responsive ── */
    @media (max-width: 575px) {
        .w3eden .wpdm-simple__action { max-width: 100%; }
        .w3eden .wpdm-simple__chips { gap: 6px; }
        .w3eden .wpdm-simple__chip { font-size: 11px; padding: 4px 10px; }
    }

    /* ── Dark mode ── */
    .w3eden.dark-mode .wpdm-simple {
        --sp-text: var(--dm-text, #f1f5f9);
        --sp-text-secondary: var(--dm-text-secondary, #cbd5e1);
        --sp-text-muted: var(--dm-text-muted, #94a3b8);
        --sp-bg: var(--dm-bg, #0f172a);
        --sp-bg-muted: var(--dm-bg-tertiary, #334155);
        --sp-border: var(--dm-border, rgba(255, 255, 255, .1));
    }

</style>

<div class="wpdm-simple">

    <!-- ── Author byline ── -->
    <div class="wpdm-simple__byline [hide_empty:author_name]">
        [avatar]
        <div>
            <span class="wpdm-simple__byline-name">[author_name]</span>
            <span class="wpdm-simple__byline-date">[txt=Published] [create_date]</span>
        </div>
    </div>

    <!-- ── Featured image ── -->
    <div class="wpdm-simple__figure">
        [thumb_800x400]
    </div>

    <!-- ── Metadata chips ── -->
    <div class="wpdm-simple__chips">
        <span class="wpdm-simple__chip [hide_empty:version]">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"/><line x1="16" y1="8" x2="2" y2="22"/><line x1="17.5" y1="15" x2="9" y2="15"/></svg>
            [version]
        </span>
        <span class="wpdm-simple__chip [hide_empty:file_size]">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            [file_size]
        </span>
        <span class="wpdm-simple__chip [hide_empty:download_count]">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            [download_count] [txt=Downloads]
        </span>
        <span class="wpdm-simple__chip [hide_empty:file_count]">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
            [file_count] [txt=Files]
        </span>
    </div>

    <!-- ── Download / Purchase ── -->
    <div class="wpdm-simple__action">
        [download_link]
        <div class="wpdm-simple__free [hide_empty:free_download_btn]">
            <div class="wpdm-simple__free-label">[txt=or download free]</div>
            [free_download_btn]
        </div>
    </div>

    <!-- ── Description ── -->
    <div class="wpdm-simple__divider"></div>
    <div class="wpdm-simple__prose">
        [description]
    </div>

    <!-- ── Changelog ── -->
    <div class="wpdm-simple__section [hide_empty:changelog]">
        [changelog]
    </div>

    <!-- ── Categories & Tags ── -->
    <div class="wpdm-simple__section [hide_empty:categories]">
        <h3 class="wpdm-simple__heading">[txt=Categories] &amp; [txt=Tags]</h3>
        <div class="wpdm-simple__pills">
            [categories]
            [tags]
        </div>
    </div>

    <!-- ── Similar Downloads ── -->
    <div class="wpdm-simple__section [hide_empty:similar_downloads]">
        <h3 class="wpdm-simple__heading">[txt=Similar Downloads]</h3>
        [similar_downloads]
    </div>

    <!-- ── Footer ── -->
    <div class="wpdm-simple__footer [hide_empty:update_date]">
        [txt=Last updated] [update_date]
    </div>

</div>
