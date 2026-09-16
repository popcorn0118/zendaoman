<!-- WPDM Template: Mosaic -->
<?php if(!defined("ABSPATH")) die(); ?>
<style>
    /* ── Metro Style 2 – bento mosaic layout ── */
    .w3eden .wpdm-mt2 {
        --mt2-primary: var(--color-primary, #2563eb);
        --mt2-primary-rgb: var(--color-primary-rgb, 37, 99, 235);
        --mt2-text: var(--color-text, #111827);
        --mt2-text-secondary: #4b5563;
        --mt2-text-muted: var(--color-muted, #9ca3af);
        --mt2-bg: var(--bg-body, #ffffff);
        --mt2-bg-card: #ffffff;
        --mt2-bg-muted: #f3f4f6;
        --mt2-border: var(--color-border, #e5e7eb);
        --mt2-transition: 120ms ease;
        max-width: 1600px;
        margin: 0 auto;
    }

    .w3eden .wpdm-mt2 .wpdm_hide { display: none; }

    /* ── Bento Grid ── */
    .w3eden .wpdm-mt2__bento {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        grid-template-areas:
            "image version size"
            "image downloads files";
        gap: 1px;
        background: var(--mt2-border);
        border: 1px solid var(--mt2-border);
    }

    .w3eden .wpdm-mt2__image {
        grid-area: image;
        background: var(--mt2-bg-muted);
        line-height: 0;
        min-height: 280px;
    }
    .w3eden .wpdm-mt2__image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* ── Stat tiles inside bento ── */
    .w3eden .wpdm-mt2__stat {
        background: var(--mt2-bg-card);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px 16px;
        text-align: center;
    }
    .w3eden .wpdm-mt2__stat--version   { grid-area: version; }
    .w3eden .wpdm-mt2__stat--size      { grid-area: size; }
    .w3eden .wpdm-mt2__stat--downloads { grid-area: downloads; }
    .w3eden .wpdm-mt2__stat--files     { grid-area: files; }

    .w3eden .wpdm-mt2__stat-label {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: var(--mt2-text-muted);
        margin-bottom: 4px;
    }
    .w3eden .wpdm-mt2__stat-value {
        display: block;
        font-size: 18px;
        font-weight: 800;
        color: var(--mt2-text);
    }

    /* ── Footer Strip: Author + Dates + CTA ── */
    .w3eden .wpdm-mt2__strip {
        display: flex;
        align-items: center;
        border: 1px solid var(--mt2-border);
        border-top: none;
        background: var(--mt2-bg-muted);
    }
    .w3eden .wpdm-mt2__author {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        flex: 1;
        min-width: 0;
    }
    .w3eden .wpdm-mt2__author img {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .w3eden .wpdm-mt2__author-label {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--mt2-text-muted);
    }
    .w3eden .wpdm-mt2__author-name {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--mt2-text);
    }
    .w3eden .wpdm-mt2__strip-right {
        display: flex;
        align-items: stretch;
        gap: 1px;
        background: var(--mt2-border);
        margin-left: auto;
        flex-shrink: 0;
    }
    .w3eden .wpdm-mt2__date-cell {
        background: var(--mt2-bg-muted);
        padding: 10px 20px;
        text-align: center;
        font-size: 12px;
        color: var(--mt2-text-secondary);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .w3eden .wpdm-mt2__date-label {
        display: block;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--mt2-text-muted);
        margin-bottom: 1px;
    }

    /* ── CTA cell (inside strip) ── */
    .w3eden .wpdm-mt2__cta {
        background: var(--mt2-bg-card);
        padding: 10px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .w3eden .wpdm-mt2 .wpdmpp-product-price {
        font-size: 18px;
        font-weight: 800;
        color: var(--mt2-text);
        line-height: 1;
        white-space: nowrap;
    }
    .w3eden .wpdm-mt2__cta .btn,
    .w3eden .wpdm-mt2__cta a.btn,
    .w3eden .wpdm-mt2__cta form .btn,
    .w3eden .wpdm-mt2__cta .wpdm-download-link .btn {
        font-size: 13px;
        padding: 8px 20px;
        white-space: nowrap;
    }
    .w3eden .wpdm-mt2__cta form { margin: 0; }

    /* ── Content sections ── */
    .w3eden .wpdm-mt2__content {
        margin-top: 36px;
    }
    .w3eden .wpdm-mt2__section {
        margin-bottom: 36px;
    }
    .w3eden .wpdm-mt2__section-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: var(--mt2-text-muted);
        margin: 0 0 16px !important;
        padding-left: 14px;
        border-left: 3px solid var(--mt2-primary);
    }
    .w3eden .wpdm-mt2__description {
        line-height: 1.8;
        color: var(--mt2-text-secondary);
        font-size: 15px;
    }
    .w3eden .wpdm-mt2__description p:last-child { margin-bottom: 0; }

    /* ── Taxonomy tiles ── */
    .w3eden .wpdm-mt2__taxonomies {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .w3eden .wpdm-mt2__taxonomies a {
        display: inline-block;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        color: var(--mt2-text-secondary);
        background: var(--mt2-bg-muted);
        border: 1px solid var(--mt2-border);
        text-decoration: none;
        transition: all var(--mt2-transition);
    }
    .w3eden .wpdm-mt2__taxonomies a:hover {
        color: #fff;
        background: var(--mt2-primary);
        border-color: var(--mt2-primary);
    }

    /* ── Responsive ── */
    @media (max-width: 767px) {
        .w3eden .wpdm-mt2__bento {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto auto;
            grid-template-areas:
                "image image"
                "version size"
                "downloads files";
        }
        .w3eden .wpdm-mt2__image { min-height: 200px; }
        .w3eden .wpdm-mt2__strip {
            flex-direction: column;
            align-items: stretch;
        }
        .w3eden .wpdm-mt2__strip-right {
            margin-left: 0;
            flex-wrap: wrap;
        }
        .w3eden .wpdm-mt2__cta {
            flex: 1 1 100%;
            justify-content: center;
        }
    }

    /* ── Dark mode (manual toggle) ── */
    .w3eden.dark-mode .wpdm-mt2 {
        --mt2-text: var(--dm-text, #f1f5f9);
        --mt2-text-secondary: var(--dm-text-secondary, #cbd5e1);
        --mt2-text-muted: var(--dm-text-muted, #94a3b8);
        --mt2-bg: var(--dm-bg, #0f172a);
        --mt2-bg-card: var(--dm-bg-secondary, #1e293b);
        --mt2-bg-muted: var(--dm-bg-tertiary, #334155);
        --mt2-border: var(--dm-border, rgba(255, 255, 255, .1));
    }

</style>

<div class="wpdm-mt2">

    <!-- ── Bento Grid: image + stat tiles ── -->
    <div class="wpdm-mt2__bento">
        <div class="wpdm-mt2__image">
            [thumb_800x400]
        </div>
        <div class="wpdm-mt2__stat wpdm-mt2__stat--version [hide_empty:version]">
            <span class="wpdm-mt2__stat-label">[txt=Version]</span>
            <span class="wpdm-mt2__stat-value">[version]</span>
        </div>
        <div class="wpdm-mt2__stat wpdm-mt2__stat--size [hide_empty:file_size]">
            <span class="wpdm-mt2__stat-label">[txt=Size]</span>
            <span class="wpdm-mt2__stat-value">[file_size]</span>
        </div>
        <div class="wpdm-mt2__stat wpdm-mt2__stat--downloads [hide_empty:download_count]">
            <span class="wpdm-mt2__stat-label">[txt=Downloads]</span>
            <span class="wpdm-mt2__stat-value">[download_count]</span>
        </div>
        <div class="wpdm-mt2__stat wpdm-mt2__stat--files [hide_empty:file_count]">
            <span class="wpdm-mt2__stat-label">[txt=Files]</span>
            <span class="wpdm-mt2__stat-value">[file_count]</span>
        </div>
    </div>

    <!-- ── Footer Strip: Author + Dates + CTA ── -->
    <div class="wpdm-mt2__strip">
        <div class="wpdm-mt2__author [hide_empty:author_name]">
            [avatar]
            <div>
                <span class="wpdm-mt2__author-label">[txt=Author]</span>
                <span class="wpdm-mt2__author-name">[author_name]</span>
            </div>
        </div>
        <div class="wpdm-mt2__strip-right">
            <div class="wpdm-mt2__date-cell [hide_empty:create_date]">
                <span class="wpdm-mt2__date-label">[txt=Published]</span>
                [create_date]
            </div>
            <div class="wpdm-mt2__date-cell [hide_empty:update_date]">
                <span class="wpdm-mt2__date-label">[txt=Updated]</span>
                [update_date]
            </div>
            <div class="wpdm-mt2__cta">
                [download_link]
            </div>
        </div>
    </div>

    <!-- ── Content sections ── -->
    <div class="wpdm-mt2__content">

        <!-- Description -->
        <div class="wpdm-mt2__section">
            <h3 class="wpdm-mt2__section-title">[txt=Description]</h3>
            <div class="wpdm-mt2__description">[description]</div>
        </div>

        <!-- Changelog -->
        <div class="wpdm-mt2__section [hide_empty:changelog]">
            [changelog]
        </div>

        <!-- Categories & Tags -->
        <div class="wpdm-mt2__section [hide_empty:categories]">
            <h3 class="wpdm-mt2__section-title">[txt=Categories] &amp; [txt=Tags]</h3>
            <div class="wpdm-mt2__taxonomies">
                [categories]
                [tags]
            </div>
        </div>

        <!-- Similar Downloads -->
        <div class="wpdm-mt2__section [hide_empty:similar_downloads]">
            <h3 class="wpdm-mt2__section-title">[txt=Similar Downloads]</h3>
            [similar_downloads]
        </div>

    </div>

</div>
