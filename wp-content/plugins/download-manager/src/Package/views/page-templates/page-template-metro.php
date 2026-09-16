<!-- WPDM Template: Metro -->
<?php if(!defined("ABSPATH")) die(); ?>
<style>
    /* ── Metro Style – flat tile layout ── */
    .w3eden .wpdm-metro {
        --mt-primary: var(--color-primary, #2563eb);
        --mt-primary-rgb: var(--color-primary-rgb, 37, 99, 235);
        --mt-text: var(--color-text, #111827);
        --mt-text-secondary: #4b5563;
        --mt-text-muted: var(--color-muted, #9ca3af);
        --mt-bg: var(--bg-body, #ffffff);
        --mt-bg-card: #ffffff;
        --mt-bg-muted: #f3f4f6;
        --mt-border: var(--color-border, #e5e7eb);
        --mt-transition: 120ms ease;
        max-width: 1600px;
        margin: 0 auto;
    }

    .w3eden .wpdm-metro .wpdm_hide { display: none; }

    /* ── Header: image + action panel ── */
    .w3eden .wpdm-metro__header {
        display: flex;
        border: 1px solid var(--mt-border);
        overflow: hidden;
    }
    .w3eden .wpdm-metro__image {
        flex: 1 1 0;
        min-width: 0;
        line-height: 0;
        background: var(--mt-bg-muted);
    }
    .w3eden .wpdm-metro__image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .w3eden .wpdm-metro__panel {
        flex: 0 0 360px;
        background: var(--mt-bg-card);
        border-left: 3px solid var(--mt-primary);
        display: flex;
        flex-direction: column;
    }

    /* ── Panel: purchase area ── */
    .w3eden .wpdm-metro__purchase {
        padding: 20px 24px;
    }
    .w3eden .wpdm-metro .wpdmpp-product-price {
        font-size: 24px;
        font-weight: 800;
        color: var(--mt-text);
        line-height: 1;
    }
    .w3eden .wpdm-metro__purchase .btn,
    .w3eden .wpdm-metro__purchase a.btn,
    .w3eden .wpdm-metro__purchase form .btn,
    .w3eden .wpdm-metro__purchase .wpdm-download-link .btn {
        width: 100%;
        font-size: 15px;
        padding: 11px 20px;
    }
    .w3eden .wpdm-metro__free-row {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed var(--mt-border);
        text-align: center;
    }
    .w3eden .wpdm-metro__free-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--mt-text-muted);
        margin-bottom: 6px;
    }
    .w3eden .wpdm-metro__free-row .btn,
    .w3eden .wpdm-metro__free-row a.btn {
        width: 100%;
        font-size: 13px;
        padding: 8px 16px;
    }

    /* ── Panel: stat tiles (2x2 grid) ── */
    .w3eden .wpdm-metro__tiles {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1px;
        background: var(--mt-border);
    }
    .w3eden .wpdm-metro__tile {
        background: var(--mt-bg-card);
        padding: 14px 16px;
        text-align: center;
    }
    .w3eden .wpdm-metro__tile-label {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--mt-text-muted);
        margin-bottom: 3px;
    }
    .w3eden .wpdm-metro__tile-value {
        display: block;
        font-size: 15px;
        font-weight: 700;
        color: var(--mt-text);
    }

    /* ── Panel: author ── */
    .w3eden .wpdm-metro__author {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 24px;
        border-top: 1px solid var(--mt-border);
    }
    .w3eden .wpdm-metro__author img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .w3eden .wpdm-metro__author-label {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--mt-text-muted);
    }
    .w3eden .wpdm-metro__author-name {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--mt-text);
    }

    /* ── Panel: dates footer (pushed to bottom) ── */
    .w3eden .wpdm-metro__dates {
        display: flex;
        gap: 1px;
        background: var(--mt-border);
        margin-top: auto;
    }
    .w3eden .wpdm-metro__date {
        flex: 1;
        background: var(--mt-bg-muted);
        padding: 10px 16px;
        text-align: center;
        font-size: 12px;
        color: var(--mt-text-secondary);
    }
    .w3eden .wpdm-metro__date-label {
        display: block;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--mt-text-muted);
        margin-bottom: 2px;
    }

    /* ── Content sections ── */
    .w3eden .wpdm-metro__content {
        margin-top: 32px;
    }
    .w3eden .wpdm-metro__section {
        margin-bottom: 32px;
    }
    .w3eden .wpdm-metro__section-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: var(--mt-text-muted);
        margin: 0 0 16px !important;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--mt-border);
    }
    .w3eden .wpdm-metro__description {
        line-height: 1.8;
        color: var(--mt-text-secondary);
        font-size: 15px;
    }
    .w3eden .wpdm-metro__description p:last-child { margin-bottom: 0; }

    /* ── Taxonomy flat tiles ── */
    .w3eden .wpdm-metro__taxonomies {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .w3eden .wpdm-metro__taxonomies a {
        display: inline-block;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        color: var(--mt-text-secondary);
        background: var(--mt-bg-muted);
        border: 1px solid var(--mt-border);
        text-decoration: none;
        transition: all var(--mt-transition);
    }
    .w3eden .wpdm-metro__taxonomies a:hover {
        color: #fff;
        background: var(--mt-primary);
        border-color: var(--mt-primary);
    }

    /* ── Responsive ── */
    @media (max-width: 767px) {
        .w3eden .wpdm-metro__header { flex-direction: column; }
        .w3eden .wpdm-metro__panel {
            flex: 1 1 auto;
            border-left: none;
            border-top: 3px solid var(--mt-primary);
        }
        .w3eden .wpdm-metro__image img { height: auto; }
    }

    /* ── Dark mode (manual toggle) ── */
    .w3eden.dark-mode .wpdm-metro {
        --mt-text: var(--dm-text, #f1f5f9);
        --mt-text-secondary: var(--dm-text-secondary, #cbd5e1);
        --mt-text-muted: var(--dm-text-muted, #94a3b8);
        --mt-bg: var(--dm-bg, #0f172a);
        --mt-bg-card: var(--dm-bg-secondary, #1e293b);
        --mt-bg-muted: var(--dm-bg-tertiary, #334155);
        --mt-border: var(--dm-border, rgba(255, 255, 255, .1));
    }

</style>

<div class="wpdm-metro">

    <!-- ── Header: image + action panel ── -->
    <div class="wpdm-metro__header">
        <div class="wpdm-metro__image">
            [thumb_800x400]
        </div>
        <div class="wpdm-metro__panel">

            <!-- Download / Purchase -->
            <div class="wpdm-metro__purchase">
                [download_link]
                <div class="wpdm-metro__free-row [hide_empty:free_download_btn]">
                    <div class="wpdm-metro__free-label">[txt=or download free]</div>
                    [free_download_btn]
                </div>
            </div>

            <!-- Stat Tiles (2x2) -->
            <div class="wpdm-metro__tiles">
                <div class="wpdm-metro__tile [hide_empty:version]">
                    <span class="wpdm-metro__tile-label">[txt=Version]</span>
                    <span class="wpdm-metro__tile-value">[version]</span>
                </div>
                <div class="wpdm-metro__tile [hide_empty:file_size]">
                    <span class="wpdm-metro__tile-label">[txt=Size]</span>
                    <span class="wpdm-metro__tile-value">[file_size]</span>
                </div>
                <div class="wpdm-metro__tile [hide_empty:download_count]">
                    <span class="wpdm-metro__tile-label">[txt=Downloads]</span>
                    <span class="wpdm-metro__tile-value">[download_count]</span>
                </div>
                <div class="wpdm-metro__tile [hide_empty:file_count]">
                    <span class="wpdm-metro__tile-label">[txt=Files]</span>
                    <span class="wpdm-metro__tile-value">[file_count]</span>
                </div>
            </div>

            <!-- Author -->
            <div class="wpdm-metro__author [hide_empty:author_name]">
                [avatar]
                <div>
                    <span class="wpdm-metro__author-label">[txt=Author]</span>
                    <span class="wpdm-metro__author-name">[author_name]</span>
                </div>
            </div>

            <!-- Dates (pushed to bottom via margin-top: auto) -->
            <div class="wpdm-metro__dates">
                <div class="wpdm-metro__date [hide_empty:create_date]">
                    <span class="wpdm-metro__date-label">[txt=Published]</span>
                    [create_date]
                </div>
                <div class="wpdm-metro__date [hide_empty:update_date]">
                    <span class="wpdm-metro__date-label">[txt=Updated]</span>
                    [update_date]
                </div>
            </div>

        </div>
    </div>

    <!-- ── Content sections ── -->
    <div class="wpdm-metro__content">

        <!-- Description -->
        <div class="wpdm-metro__section">
            <h3 class="wpdm-metro__section-title">[txt=Description]</h3>
            <div class="wpdm-metro__description">[description]</div>
        </div>


        <!-- Changelog -->
        <div class="wpdm-metro__section [hide_empty:changelog]">
            [changelog]
        </div>

        <!-- Categories & Tags -->
        <div class="wpdm-metro__section [hide_empty:categories]">
            <h3 class="wpdm-metro__section-title">[txt=Categories] &amp; [txt=Tags]</h3>
            <div class="wpdm-metro__taxonomies">
                [categories]
                [tags]
            </div>
        </div>

        <!-- Similar Downloads -->
        <div class="wpdm-metro__section [hide_empty:similar_downloads]">
            <h3 class="wpdm-metro__section-title">[txt=Similar Downloads]</h3>
            [similar_downloads]
        </div>

    </div>

</div>
