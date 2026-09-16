<!-- WPDM Template: Cinema ( YouTube ) -->
<?php if(!defined("ABSPATH")) die(); ?>
<style>
    /* ── Cinema – video-first layout ── */
    .w3eden .wpdm-cinema {
        --cn-primary: var(--color-primary, #6366f1);
        --cn-primary-rgb: var(--color-primary-rgb, 99, 102, 241);
        --cn-text: var(--color-text, #1e293b);
        --cn-text-secondary: #475569;
        --cn-text-muted: var(--color-muted, #94a3b8);
        --cn-bg: var(--bg-body, #ffffff);
        --cn-bg-theater: #0f172a;
        --cn-bg-muted: #f1f5f9;
        --cn-border: var(--color-border, #e2e8f0);
        --cn-radius: 10px;
        --cn-transition: 150ms ease;
        max-width: 1600px;
        margin: 0 auto;
    }

    .w3eden .wpdm-cinema .wpdm_hide { display: none; }

    /* ── Theater (video container) ── */
    .w3eden .wpdm-cinema__theater {
        background: var(--cn-bg-theater);
        border-radius: var(--cn-radius);
        padding: 20px;
        margin-bottom: 0;
    }
    .w3eden .wpdm-cinema__theater .card {
        background: transparent;
        border: none;
        box-shadow: none;
        margin: 0;
    }
    .w3eden .wpdm-cinema__theater iframe,
    .w3eden .wpdm-cinema__theater video {
        width: 100%;
        border-radius: 6px;
        display: block;
    }

    /* ── Info bar ── */
    .w3eden .wpdm-cinema__info {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 0;
        margin-top: 16px;
        border-bottom: 1px solid var(--cn-border);
    }
    .w3eden .wpdm-cinema__stats {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        flex: 1;
        min-width: 0;
    }
    .w3eden .wpdm-cinema__stat {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
        color: var(--cn-text-secondary);
        background: var(--cn-bg-muted);
        border-radius: 20px;
    }
    .w3eden .wpdm-cinema__stat svg {
        width: 13px;
        height: 13px;
        opacity: .5;
        flex-shrink: 0;
    }
    .w3eden .wpdm-cinema__cta {
        flex-shrink: 0;
    }
    .w3eden .wpdm-cinema .wpdmpp-product-price {
        font-size: 20px;
        font-weight: 800;
        color: var(--cn-text);
        line-height: 1;
    }
    .w3eden .wpdm-cinema__cta .btn,
    .w3eden .wpdm-cinema__cta a.btn,
    .w3eden .wpdm-cinema__cta form .btn,
    .w3eden .wpdm-cinema__cta .wpdm-download-link .btn {
        font-size: 14px;
        padding: 9px 24px;
        border-radius: 20px;
    }

    /* ── Author row ── */
    .w3eden .wpdm-cinema__author {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 0;
    }
    .w3eden .wpdm-cinema__author img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .w3eden .wpdm-cinema__author-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--cn-text);
    }
    .w3eden .wpdm-cinema__author-date {
        font-size: 12px;
        color: var(--cn-text-muted);
    }

    /* ── Free download row ── */
    .w3eden .wpdm-cinema__free {
        padding-bottom: 14px;
    }
    .w3eden .wpdm-cinema__free-label {
        font-size: 11px;
        color: var(--cn-text-muted);
        margin-bottom: 4px;
    }
    .w3eden .wpdm-cinema__free .btn,
    .w3eden .wpdm-cinema__free a.btn {
        font-size: 13px;
        padding: 7px 20px;
        border-radius: 20px;
    }

    /* ── Description ── */
    .w3eden .wpdm-cinema__body {
        padding-top: 20px;
        border-top: 1px solid var(--cn-border);
    }
    .w3eden .wpdm-cinema__description {
        font-size: 15px;
        line-height: 1.8;
        color: var(--cn-text-secondary);
    }
    .w3eden .wpdm-cinema__description p:last-child { margin-bottom: 0; }

    /* ── Content sections ── */
    .w3eden .wpdm-cinema__section {
        margin-top: 28px;
        padding-top: 28px;
        border-top: 1px solid var(--cn-border);
    }
    .w3eden .wpdm-cinema__heading {
        font-size: 15px;
        font-weight: 700;
        color: var(--cn-text);
        margin: 0 0 14px !important;
    }

    /* ── Taxonomy pills ── */
    .w3eden .wpdm-cinema__pills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .w3eden .wpdm-cinema__pills a {
        display: inline-block;
        padding: 5px 14px;
        font-size: 13px;
        font-weight: 500;
        color: var(--cn-text-secondary);
        background: var(--cn-bg-muted);
        border-radius: 20px;
        text-decoration: none;
        transition: all var(--cn-transition);
    }
    .w3eden .wpdm-cinema__pills a:hover {
        color: #fff;
        background: var(--cn-primary);
    }

    /* ── Responsive ── */
    @media (max-width: 575px) {
        .w3eden .wpdm-cinema__theater { padding: 10px; }
        .w3eden .wpdm-cinema__info {
            flex-direction: column;
            align-items: stretch;
        }
        .w3eden .wpdm-cinema__cta {
            order: -1;
            margin-bottom: 4px;
        }
        .w3eden .wpdm-cinema__cta .btn,
        .w3eden .wpdm-cinema__cta a.btn,
        .w3eden .wpdm-cinema__cta form .btn,
        .w3eden .wpdm-cinema__cta .wpdm-download-link .btn {
            width: 100%;
        }
    }

    /* ── Dark mode ── */
    .w3eden.dark-mode .wpdm-cinema {
        --cn-text: var(--dm-text, #f1f5f9);
        --cn-text-secondary: var(--dm-text-secondary, #cbd5e1);
        --cn-text-muted: var(--dm-text-muted, #94a3b8);
        --cn-bg: var(--dm-bg, #0f172a);
        --cn-bg-theater: #020617;
        --cn-bg-muted: var(--dm-bg-tertiary, #334155);
        --cn-border: var(--dm-border, rgba(255, 255, 255, .1));
    }

</style>

<div class="wpdm-cinema">

    <!-- ── Video player ── -->
    <div class="wpdm-cinema__theater">
        [youtube_player]
    </div>

    <!-- ── Stats + CTA ── -->
    <div class="wpdm-cinema__info">
        <div class="wpdm-cinema__stats">
            <span class="wpdm-cinema__stat [hide_empty:view_count]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                [view_count] [txt=Views]
            </span>
            <span class="wpdm-cinema__stat [hide_empty:download_count]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                [download_count] [txt=Downloads]
            </span>
            <span class="wpdm-cinema__stat [hide_empty:version]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"/><line x1="16" y1="8" x2="2" y2="22"/><line x1="17.5" y1="15" x2="9" y2="15"/></svg>
                [version]
            </span>
            <span class="wpdm-cinema__stat [hide_empty:file_size]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                [file_size]
            </span>
        </div>
        <div class="wpdm-cinema__cta">
            [download_link]
        </div>
    </div>

    <!-- ── Author ── -->
    <div class="wpdm-cinema__author [hide_empty:author_name]">
        [avatar]
        <div>
            <span class="wpdm-cinema__author-name">[author_name]</span>
            <span class="wpdm-cinema__author-date">[txt=Published] [create_date]</span>
        </div>
    </div>

    <!-- ── Free download ── -->
    <div class="wpdm-cinema__free [hide_empty:free_download_btn]">
        <div class="wpdm-cinema__free-label">[txt=or download free]</div>
        [free_download_btn]
    </div>

    <!-- ── Description ── -->
    <div class="wpdm-cinema__body">
        <div class="wpdm-cinema__description">[description]</div>
    </div>

    <!-- ── Changelog ── -->
    <div class="wpdm-cinema__section [hide_empty:changelog]">
        [changelog]
    </div>

    <!-- ── Categories & Tags ── -->
    <div class="wpdm-cinema__section [hide_empty:categories]">
        <h3 class="wpdm-cinema__heading">[txt=Categories] &amp; [txt=Tags]</h3>
        <div class="wpdm-cinema__pills">
            [categories]
            [tags]
        </div>
    </div>

    <!-- ── Similar Downloads ── -->
    <div class="wpdm-cinema__section [hide_empty:similar_downloads]">
        <h3 class="wpdm-cinema__heading">[txt=Similar Downloads]</h3>
        [similar_downloads]
    </div>

</div>
