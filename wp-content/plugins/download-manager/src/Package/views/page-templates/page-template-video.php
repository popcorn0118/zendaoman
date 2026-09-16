<!-- WPDM Template: Screen ( Video ) -->
<?php if(!defined("ABSPATH")) die(); ?>
<style>
    /* ── Screen – spec-sheet video layout ── */
    .w3eden .wpdm-screen {
        --sc-primary: var(--color-primary, #6366f1);
        --sc-primary-rgb: var(--color-primary-rgb, 99, 102, 241);
        --sc-text: var(--color-text, #1e293b);
        --sc-text-secondary: #475569;
        --sc-text-muted: var(--color-muted, #94a3b8);
        --sc-bg: var(--bg-body, #ffffff);
        --sc-bg-card: #ffffff;
        --sc-bg-muted: #f8fafc;
        --sc-border: var(--color-border, #e2e8f0);
        --sc-radius: 8px;
        --sc-transition: 150ms ease;
        max-width: 1600px;
        margin: 0 auto;
    }

    .w3eden .wpdm-screen .wpdm_hide { display: none; }

    /* ── Video player ── */
    .w3eden .wpdm-screen__player {
        border: 1px solid var(--sc-border);
        border-radius: var(--sc-radius);
        overflow: hidden;
        line-height: 0;
        background: #000;
    }
    .w3eden .wpdm-screen__player .card {
        background: transparent;
        border: none;
        box-shadow: none;
        margin: 0;
        padding: 0;
    }
    .w3eden .wpdm-screen__player iframe,
    .w3eden .wpdm-screen__player video {
        width: 100%;
        display: block;
    }

    /* ── Spec strip (horizontal metadata) ── */
    .w3eden .wpdm-screen__specs {
        display: flex;
        border: 1px solid var(--sc-border);
        border-top: none;
        border-radius: 0 0 var(--sc-radius) var(--sc-radius);
        overflow: hidden;
        margin-top: -1px;
    }
    .w3eden .wpdm-screen__spec {
        flex: 1;
        padding: 14px 16px;
        text-align: center;
        background: var(--sc-bg-muted);
        border-right: 1px solid var(--sc-border);
    }
    .w3eden .wpdm-screen__spec:last-child { border-right: none; }
    .w3eden .wpdm-screen__spec-label {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--sc-text-muted);
        margin-bottom: 3px;
    }
    .w3eden .wpdm-screen__spec-value {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: var(--sc-text);
    }

    /* ── Action row (author + CTA) ── */
    .w3eden .wpdm-screen__action {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 0;
        border-bottom: 1px solid var(--sc-border);
    }
    .w3eden .wpdm-screen__author {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }
    .w3eden .wpdm-screen__author img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .w3eden .wpdm-screen__author-name {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--sc-text);
    }
    .w3eden .wpdm-screen__author-sub {
        display: block;
        font-size: 12px;
        color: var(--sc-text-muted);
    }
    .w3eden .wpdm-screen__cta {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .w3eden .wpdm-screen .wpdmpp-product-price {
        font-size: 22px;
        font-weight: 800;
        color: var(--sc-text);
        line-height: 1;
    }
    .w3eden .wpdm-screen__cta .btn,
    .w3eden .wpdm-screen__cta a.btn,
    .w3eden .wpdm-screen__cta form .btn,
    .w3eden .wpdm-screen__cta .wpdm-download-link .btn {
        font-size: 14px;
        padding: 10px 28px;
        border-radius: var(--sc-radius);
    }
    .w3eden .wpdm-screen__free {
        padding: 12px 0;
    }
    .w3eden .wpdm-screen__free-label {
        font-size: 11px;
        color: var(--sc-text-muted);
        margin-bottom: 4px;
    }
    .w3eden .wpdm-screen__free .btn,
    .w3eden .wpdm-screen__free a.btn {
        font-size: 13px;
        padding: 7px 20px;
        border-radius: var(--sc-radius);
    }

    /* ── Description ── */
    .w3eden .wpdm-screen__body {
        padding-top: 24px;
    }
    .w3eden .wpdm-screen__description {
        font-size: 15px;
        line-height: 1.8;
        color: var(--sc-text-secondary);
    }
    .w3eden .wpdm-screen__description p:last-child { margin-bottom: 0; }

    /* ── Content sections ── */
    .w3eden .wpdm-screen__section {
        margin-top: 32px;
        padding-top: 32px;
        border-top: 1px solid var(--sc-border);
    }
    .w3eden .wpdm-screen__heading {
        font-size: 18px;
        font-weight: 700;
        color: var(--sc-text);
        margin: 0 0 16px !important;
    }

    /* ── Taxonomy (outlined badges) ── */
    .w3eden .wpdm-screen__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .w3eden .wpdm-screen__tags a {
        display: inline-block;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 500;
        color: var(--sc-text-secondary);
        background: transparent;
        border: 1px solid var(--sc-border);
        border-radius: var(--sc-radius);
        text-decoration: none;
        transition: all var(--sc-transition);
    }
    .w3eden .wpdm-screen__tags a:hover {
        color: var(--sc-primary);
        border-color: var(--sc-primary);
        background: rgba(var(--sc-primary-rgb), .06);
    }

    /* ── Responsive ── */
    @media (max-width: 767px) {
        .w3eden .wpdm-screen__specs {
            flex-wrap: wrap;
        }
        .w3eden .wpdm-screen__spec {
            flex: 1 1 calc(50% - 0.5px);
            border-bottom: 1px solid var(--sc-border);
        }
        .w3eden .wpdm-screen__spec:nth-child(even) { border-right: none; }
        .w3eden .wpdm-screen__spec:nth-last-child(-n+2) { border-bottom: none; }
    }
    @media (max-width: 575px) {
        .w3eden .wpdm-screen__action {
            flex-direction: column;
            align-items: stretch;
        }
        .w3eden .wpdm-screen__cta {
            flex-direction: column;
        }
        .w3eden .wpdm-screen__cta .btn,
        .w3eden .wpdm-screen__cta a.btn,
        .w3eden .wpdm-screen__cta form .btn,
        .w3eden .wpdm-screen__cta .wpdm-download-link .btn {
            width: 100%;
        }
    }

    /* ── Dark mode ── */
    .w3eden.dark-mode .wpdm-screen {
        --sc-text: var(--dm-text, #f1f5f9);
        --sc-text-secondary: var(--dm-text-secondary, #cbd5e1);
        --sc-text-muted: var(--dm-text-muted, #94a3b8);
        --sc-bg: var(--dm-bg, #0f172a);
        --sc-bg-card: var(--dm-bg-secondary, #1e293b);
        --sc-bg-muted: var(--dm-bg-tertiary, #334155);
        --sc-border: var(--dm-border, rgba(255, 255, 255, .1));
    }

</style>

<div class="wpdm-screen">

    <!-- ── Video player ── -->
    <div class="wpdm-screen__player">
        [video_player_1200x800]
    </div>

    <!-- ── Spec strip ── -->
    <div class="wpdm-screen__specs">
        <div class="wpdm-screen__spec [hide_empty:version]">
            <span class="wpdm-screen__spec-label">[txt=Version]</span>
            <span class="wpdm-screen__spec-value">[version]</span>
        </div>
        <div class="wpdm-screen__spec [hide_empty:file_size]">
            <span class="wpdm-screen__spec-label">[txt=Size]</span>
            <span class="wpdm-screen__spec-value">[file_size]</span>
        </div>
        <div class="wpdm-screen__spec [hide_empty:download_count]">
            <span class="wpdm-screen__spec-label">[txt=Downloads]</span>
            <span class="wpdm-screen__spec-value">[download_count]</span>
        </div>
        <div class="wpdm-screen__spec [hide_empty:file_count]">
            <span class="wpdm-screen__spec-label">[txt=Files]</span>
            <span class="wpdm-screen__spec-value">[file_count]</span>
        </div>
        <div class="wpdm-screen__spec [hide_empty:create_date]">
            <span class="wpdm-screen__spec-label">[txt=Published]</span>
            <span class="wpdm-screen__spec-value">[create_date]</span>
        </div>
        <div class="wpdm-screen__spec [hide_empty:update_date]">
            <span class="wpdm-screen__spec-label">[txt=Updated]</span>
            <span class="wpdm-screen__spec-value">[update_date]</span>
        </div>
    </div>

    <!-- ── Author + CTA ── -->
    <div class="wpdm-screen__action">
        <div class="wpdm-screen__author [hide_empty:author_name]">
            [avatar]
            <div>
                <span class="wpdm-screen__author-name">[author_name]</span>
                <span class="wpdm-screen__author-sub">[txt=Author]</span>
            </div>
        </div>
        <div class="wpdm-screen__cta">
            [download_link]
        </div>
    </div>

    <!-- ── Free download ── -->
    <div class="wpdm-screen__free [hide_empty:free_download_btn]">
        <span class="wpdm-screen__free-label">[txt=or download free]</span>
        [free_download_btn]
    </div>

    <!-- ── Description ── -->
    <div class="wpdm-screen__body">
        <div class="wpdm-screen__description">[description]</div>
    </div>

    <!-- ── Changelog ── -->
    <div class="wpdm-screen__section [hide_empty:changelog]">
        [changelog]
    </div>

    <!-- ── Categories & Tags ── -->
    <div class="wpdm-screen__section [hide_empty:categories]">
        <h3 class="wpdm-screen__heading">[txt=Categories] &amp; [txt=Tags]</h3>
        <div class="wpdm-screen__tags">
            [categories]
            [tags]
        </div>
    </div>

    <!-- ── Similar Downloads ── -->
    <div class="wpdm-screen__section [hide_empty:similar_downloads]">
        <h3 class="wpdm-screen__heading">[txt=Similar Downloads]</h3>
        [similar_downloads]
    </div>

</div>
