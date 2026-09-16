<!-- WPDM Template: Folio ( Document Preview )-->
<?php if(!defined("ABSPATH")) die(); ?>
<style>
    /* ── Folio – document folio layout ── */
    .w3eden .wpdm-folio {
        --fo-primary: var(--color-primary, #6366f1);
        --fo-primary-rgb: var(--color-primary-rgb, 99, 102, 241);
        --fo-text: var(--color-text, #1e293b);
        --fo-text-secondary: #475569;
        --fo-text-muted: var(--color-muted, #94a3b8);
        --fo-bg: var(--bg-body, #ffffff);
        --fo-bg-muted: #f8fafc;
        --fo-bg-accent: #f1f5f9;
        --fo-border: var(--color-border, #e2e8f0);
        --fo-radius: 12px;
        --fo-transition: 180ms ease;
        max-width: 1600px;
        margin: 0 auto;
    }

    .w3eden .wpdm-folio .wpdm_hide { display: none; }

    /* ── Preview frame ── */
    .w3eden .wpdm-folio__frame {
        border: 1px solid var(--fo-border);
        border-radius: var(--fo-radius);
        overflow: hidden;
        background: var(--fo-bg-muted);
    }
    .w3eden .wpdm-folio__preview {
        padding: 3px;
        min-height: 400px;
        background: var(--fo-bg-muted);
    }
    .w3eden .wpdm-folio__preview iframe {
        width: 100%;
        min-height: 600px;
        border: none;
        display: block;
    }
    .w3eden .wpdm-folio__preview img {
        max-width: 100%;
        display: block;
        margin: 0 auto;
    }

    /* ── Info strip inside frame bottom ── */
    .w3eden .wpdm-folio__strip {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: var(--fo-bg-accent);
        border-top: 1px solid var(--fo-border);
    }
    .w3eden .wpdm-folio__meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        flex: 1;
        min-width: 0;
    }
    .w3eden .wpdm-folio__badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        color: var(--fo-text-secondary);
        background: var(--fo-bg);
        border: 1px solid var(--fo-border);
        border-radius: 6px;
    }
    .w3eden .wpdm-folio__badge svg {
        width: 12px;
        height: 12px;
        opacity: .5;
        flex-shrink: 0;
    }

    /* ── Action row ── */
    .w3eden .wpdm-folio__action {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 20px;
    }
    .w3eden .wpdm-folio__author {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }
    .w3eden .wpdm-folio__author img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .w3eden .wpdm-folio__author-name {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--fo-text);
    }
    .w3eden .wpdm-folio__author-date {
        display: block;
        font-size: 12px;
        color: var(--fo-text-muted);
    }
    .w3eden .wpdm-folio__cta {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .w3eden .wpdm-folio .wpdmpp-product-price {
        font-size: 22px;
        font-weight: 800;
        color: var(--fo-text);
        line-height: 1;
    }
    .w3eden .wpdm-folio__cta .btn,
    .w3eden .wpdm-folio__cta a.btn,
    .w3eden .wpdm-folio__cta form .btn,
    .w3eden .wpdm-folio__cta .wpdm-download-link .btn {
        font-size: 14px;
        padding: 10px 28px;
        border-radius: var(--fo-radius);
    }

    /* ── Free download ── */
    .w3eden .wpdm-folio__free {
        margin-top: 12px;
        text-align: right;
    }
    .w3eden .wpdm-folio__free-label {
        font-size: 11px;
        color: var(--fo-text-muted);
        margin-bottom: 4px;
    }
    .w3eden .wpdm-folio__free .btn,
    .w3eden .wpdm-folio__free a.btn {
        font-size: 13px;
        padding: 7px 20px;
        border-radius: var(--fo-radius);
    }

    /* ── Content sections ── */
    .w3eden .wpdm-folio__content {
        margin-top: 32px;
    }
    .w3eden .wpdm-folio__section {
        margin-bottom: 32px;
    }
    .w3eden .wpdm-folio__heading {
        font-size: 15px;
        font-weight: 700;
        color: var(--fo-text);
        margin: 0 0 14px !important;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--fo-border);
    }
    .w3eden .wpdm-folio__description {
        font-size: 15px;
        line-height: 1.8;
        color: var(--fo-text-secondary);
    }
    .w3eden .wpdm-folio__description p:last-child { margin-bottom: 0; }

    /* ── Taxonomy ── */
    .w3eden .wpdm-folio__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .w3eden .wpdm-folio__tags a {
        display: inline-block;
        padding: 5px 14px;
        font-size: 12px;
        font-weight: 600;
        color: var(--fo-text-secondary);
        background: var(--fo-bg-accent);
        border: 1px solid var(--fo-border);
        border-radius: 6px;
        text-decoration: none;
        transition: all var(--fo-transition);
    }
    .w3eden .wpdm-folio__tags a:hover {
        color: var(--fo-primary);
        border-color: var(--fo-primary);
        background: rgba(var(--fo-primary-rgb), .06);
    }

    /* ── Responsive ── */
    @media (max-width: 767px) {
        .w3eden .wpdm-folio__strip {
            flex-wrap: wrap;
        }
        .w3eden .wpdm-folio__action {
            flex-direction: column;
            align-items: stretch;
        }
        .w3eden .wpdm-folio__cta {
            flex-direction: column;
        }
        .w3eden .wpdm-folio__cta .btn,
        .w3eden .wpdm-folio__cta a.btn,
        .w3eden .wpdm-folio__cta form .btn,
        .w3eden .wpdm-folio__cta .wpdm-download-link .btn {
            width: 100%;
        }
        .w3eden .wpdm-folio__free { text-align: left; }
        .w3eden .wpdm-folio__preview iframe { min-height: 400px; }
    }

    /* ── Dark mode ── */
    .w3eden.dark-mode .wpdm-folio {
        --fo-text: var(--dm-text, #f1f5f9);
        --fo-text-secondary: var(--dm-text-secondary, #cbd5e1);
        --fo-text-muted: var(--dm-text-muted, #94a3b8);
        --fo-bg: var(--dm-bg, #0f172a);
        --fo-bg-muted: var(--dm-bg-secondary, #1e293b);
        --fo-bg-accent: var(--dm-bg-tertiary, #334155);
        --fo-border: var(--dm-border, rgba(255, 255, 255, .1));
    }

</style>

<div class="wpdm-folio">

    <!-- ── Document preview with info strip ── -->
    <div class="wpdm-folio__frame">
        <div class="wpdm-folio__preview">
            [doc_preview]
        </div>
        <div class="wpdm-folio__strip">
            <div class="wpdm-folio__meta">
                <span class="wpdm-folio__badge [hide_empty:version]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"/><line x1="16" y1="8" x2="2" y2="22"/><line x1="17.5" y1="15" x2="9" y2="15"/></svg>
                    [version]
                </span>
                <span class="wpdm-folio__badge [hide_empty:file_size]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                    [file_size]
                </span>
                <span class="wpdm-folio__badge [hide_empty:download_count]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    [download_count]
                </span>
                <span class="wpdm-folio__badge [hide_empty:file_count]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    [file_count] [txt=Files]
                </span>
            </div>
        </div>
    </div>

    <!-- ── Author + CTA ── -->
    <div class="wpdm-folio__action">
        <div class="wpdm-folio__author [hide_empty:author_name]">
            [avatar]
            <div>
                <span class="wpdm-folio__author-name">[author_name]</span>
                <span class="wpdm-folio__author-date">[txt=Published] [create_date] &middot; [txt=Updated] [update_date]</span>
            </div>
        </div>
        <div class="wpdm-folio__cta">
            [download_link]
        </div>
    </div>

    <!-- ── Free download ── -->
    <div class="wpdm-folio__free [hide_empty:free_download_btn]">
        <div class="wpdm-folio__free-label">[txt=or download free]</div>
        [free_download_btn]
    </div>

    <!-- ── Content ── -->
    <div class="wpdm-folio__content">

        <!-- Description -->
        <div class="wpdm-folio__section">
            <h3 class="wpdm-folio__heading">[txt=Description]</h3>
            <div class="wpdm-folio__description">[description]</div>
        </div>

        <!-- Changelog -->
        <div class="wpdm-folio__section [hide_empty:changelog]">
            [changelog]
        </div>

        <!-- Categories & Tags -->
        <div class="wpdm-folio__section [hide_empty:categories]">
            <h3 class="wpdm-folio__heading">[txt=Categories] &amp; [txt=Tags]</h3>
            <div class="wpdm-folio__tags">
                [categories]
                [tags]
            </div>
        </div>

        <!-- Similar Downloads -->
        <div class="wpdm-folio__section [hide_empty:similar_downloads]">
            <h3 class="wpdm-folio__heading">[txt=Similar Downloads]</h3>
            [similar_downloads]
        </div>

    </div>

</div>
