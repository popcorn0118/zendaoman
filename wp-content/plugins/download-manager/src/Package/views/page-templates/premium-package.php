<!-- WPDM Template: Premium Package -->
<?php if(!defined("ABSPATH")) die(); ?>
<style>
    /* ── Premium Package – scoped under .w3eden .wpdm-pp ── */
    .w3eden .wpdm-pp {
        --pp-primary: var(--color-primary, #6366f1);
        --pp-primary-rgb: var(--color-primary-rgb, 99, 102, 241);
        --pp-text: var(--color-text, #1e293b);
        --pp-text-secondary: #64748b;
        --pp-text-muted: var(--color-muted, #94a3b8);
        --pp-bg: var(--bg-body, #f8fafc);
        --pp-bg-card: var(--bg-body, #ffffff);
        --pp-bg-muted: #f1f5f9;
        --pp-border: var(--color-border, #e2e8f0);
        --pp-radius: 12px;
        --pp-radius-sm: 8px;
        --pp-shadow: 0 1px 3px 0 rgb(0 0 0 / .06), 0 1px 2px -1px rgb(0 0 0 / .06);
        --pp-shadow-lg: 0 4px 12px rgb(0 0 0 / .08);
        --pp-transition: 150ms cubic-bezier(.4, 0, .2, 1);
        max-width: 1600px;
        margin: 0 auto;
    }

    /* Anti-flash: hide empty-field elements before JS removes them */
    .w3eden .wpdm-pp .wpdm_hide { display: none; }

    /* ── Two-column layout ── */
    .w3eden .wpdm-pp__layout { display: flex; gap: 28px; }
    .w3eden .wpdm-pp__main { flex: 1 1 0; min-width: 0; }
    .w3eden .wpdm-pp__sidebar { flex: 0 0 340px; min-width: 280px; }

    /* ── Product image ── */
    .w3eden .wpdm-pp__image { margin-bottom: 32px; }
    .w3eden .wpdm-pp__image img { width: 100%; height: auto; border-radius: var(--pp-radius); display: block; }

    /* ── Sticky inner wrapper ── */
    .w3eden .wpdm-pp__sidebar-inner {
        position: sticky;
        top: 48px;
        background: var(--pp-bg-card);
        border: 1px solid var(--pp-border);
        border-radius: var(--pp-radius);
        box-shadow: var(--pp-shadow);
        overflow: hidden;
    }

    /* ── Price ── */
    .w3eden .wpdmpp-product-price {
        font-size: 26px;
        font-weight: 800;
        color: var(--pp-text);
        line-height: 1.2;
    }

    /* ── CTA area ── */
    .w3eden .wpdm-pp__cta {
        padding: 16px 20px;
    }
    .w3eden .wpdm-pp__cta .btn,
    .w3eden .wpdm-pp__cta a.btn,
    .w3eden .wpdm-pp__cta .wpdm-download-link .btn,
    .w3eden .wpdm-pp__cta form .btn {
        width: 100%;
        font-size: 15px;
        padding: 11px 20px;
        border-radius: var(--pp-radius-sm);
    }

    /* "or" divider */
    .w3eden .wpdm-pp__or {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 20px 12px;
        font-size: 12px;
        color: var(--pp-text-muted);
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .w3eden .wpdm-pp__or::before,
    .w3eden .wpdm-pp__or::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--pp-border);
    }

    /* Free download */
    .w3eden .wpdm-pp__free {
        padding: 0 20px 16px;
    }
    .w3eden .wpdm-pp__free .btn,
    .w3eden .wpdm-pp__free a.btn {
        width: 100%;
        font-size: 14px;
        padding: 9px 16px;
        border-radius: var(--pp-radius-sm);
    }

    /* ── Metadata 2×2 grid ── */
    .w3eden .wpdm-pp__meta-grid {
        margin: 0 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1px;
        background: var(--pp-border);
        border-radius: var(--pp-radius-sm);
        overflow: hidden;
    }
    .w3eden .wpdm-pp__meta-item {
        background: var(--pp-bg-card);
        padding: 14px 16px;
        text-align: center;
    }
    .w3eden .wpdm-pp__meta-label {
        display: block;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--pp-text-muted);
        margin-bottom: 4px;
    }
    .w3eden .wpdm-pp__meta-value {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--pp-text);
    }

    /* ── Author row ── */
    .w3eden .wpdm-pp__author {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        margin-top: 16px;
        border-top: 1px solid var(--pp-border);
    }
    .w3eden .wpdm-pp__author img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .w3eden .wpdm-pp__author-info {
        min-width: 0;
    }
    .w3eden .wpdm-pp__author-label {
        display: block;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--pp-text-muted);
        line-height: 1.4;
    }
    .w3eden .wpdm-pp__author-name {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--pp-text);
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ── Dates footer ── */
    .w3eden .wpdm-pp__dates {
        display: flex;
        justify-content: space-between;
        padding: 12px 20px;
        background: var(--pp-bg-muted);
        border-top: 1px solid var(--pp-border);
        font-size: 12px;
        color: var(--pp-text-secondary);
    }
    .w3eden .wpdm-pp__dates span {
        display: block;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--pp-text-muted);
        margin-bottom: 2px;
    }

    /* ── Content sections ── */
    .w3eden .wpdm-pp__section {
        margin-bottom: 32px;
    }

    .w3eden .wpdm-pp__section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 18px;
        font-weight: 700;
        color: var(--pp-text);
        margin: 0 0 16px !important;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--pp-border);
    }
    .w3eden .wpdm-pp__section-title svg {
        width: 20px;
        height: 20px;
        color: var(--pp-text-muted);
        flex-shrink: 0;
    }

    .w3eden .wpdm-pp__description {
        line-height: 1.75;
        color: var(--pp-text);
        font-size: 15px;
    }
    .w3eden .wpdm-pp__description p:last-child { margin-bottom: 0; }

    /* ── Taxonomy pills ── */
    .w3eden .wpdm-pp__taxonomies {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .w3eden .wpdm-pp__taxonomies a {
        display: inline-block;
        padding: 5px 14px;
        font-size: 13px;
        font-weight: 500;
        color: var(--pp-text-secondary);
        background: var(--pp-bg-muted);
        border: 1px solid var(--pp-border);
        border-radius: 20px;
        text-decoration: none;
        transition: all var(--pp-transition);
    }
    .w3eden .wpdm-pp__taxonomies a:hover {
        color: var(--pp-primary);
        border-color: rgba(var(--pp-primary-rgb), .3);
        background: rgba(var(--pp-primary-rgb), .06);
    }

    /* ── Responsive ── */
    @media (max-width: 767px) {
        .w3eden .wpdm-pp__layout { flex-direction: column; gap: 20px; }
        .w3eden .wpdm-pp__sidebar { flex: 1 1 100%; min-width: 0; }
        .w3eden .wpdm-pp__sidebar-inner { position: static; }
    }

    /* ── Dark mode (manual toggle) ── */
    .w3eden.dark-mode .wpdm-pp {
        --pp-text: var(--dm-text, #f1f5f9);
        --pp-text-secondary: var(--dm-text-secondary, #cbd5e1);
        --pp-text-muted: var(--dm-text-muted, #94a3b8);
        --pp-bg: var(--dm-bg, #0f172a);
        --pp-bg-card: var(--dm-bg-secondary, #1e293b);
        --pp-bg-muted: var(--dm-bg-tertiary, #334155);
        --pp-border: var(--dm-border, rgba(255, 255, 255, .1));
        --pp-shadow: 0 1px 3px 0 rgb(0 0 0 / .2);
        --pp-shadow-lg: 0 4px 12px rgb(0 0 0 / .3);
    }

</style>

<div class="wpdm-pp">
    <div class="wpdm-pp__layout">

        <!-- ── Left column: Image + Content ── -->
        <div class="wpdm-pp__main">

            <!-- Product Image -->
            <div class="wpdm-pp__image">
                [thumb_800x600]
            </div>

            <!-- Description -->
            <div class="wpdm-pp__section">
                <h3 class="wpdm-pp__section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    [txt=Description]
                </h3>
                <div class="wpdm-pp__description">[description]</div>
            </div>

            <!-- Changelog (renders its own heading) -->
            <div class="wpdm-pp__section [hide_empty:changelog]">
                [changelog]
            </div>

            <!-- Categories & Tags -->
            <div class="wpdm-pp__section [hide_empty:categories]">
                <h3 class="wpdm-pp__section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                    [txt=Categories] &amp; [txt=Tags]
                </h3>
                <div class="wpdm-pp__taxonomies">
                    [categories]
                    [tags]
                </div>
            </div>

            <!-- Similar Downloads -->
            <div class="wpdm-pp__section [hide_empty:similar_downloads]">
                <h3 class="wpdm-pp__section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                    [txt=Similar Downloads]
                </h3>
                [similar_downloads]
            </div>

        </div>

        <!-- ── Right column: Sticky Sidebar ── -->
        <div class="wpdm-pp__sidebar">
            <div class="wpdm-pp__sidebar-inner">

                <!-- CTA -->
                <div class="wpdm-pp__cta">
                    [download_link]
                </div>

                <!-- Free Download (shown only when available) -->
                <div class="[hide_empty:free_download_btn]">
                    <div class="wpdm-pp__or">[txt=or]</div>
                    <div class="wpdm-pp__free">[free_download_btn]</div>
                </div>

                <!-- 2×2 Metadata Grid -->
                <div class="wpdm-pp__meta-grid">
                    <div class="wpdm-pp__meta-item [hide_empty:version]">
                        <span class="wpdm-pp__meta-label">[txt=Version]</span>
                        <span class="wpdm-pp__meta-value">[version]</span>
                    </div>
                    <div class="wpdm-pp__meta-item [hide_empty:file_size]">
                        <span class="wpdm-pp__meta-label">[txt=File Size]</span>
                        <span class="wpdm-pp__meta-value">[file_size]</span>
                    </div>
                    <div class="wpdm-pp__meta-item [hide_empty:download_count]">
                        <span class="wpdm-pp__meta-label">[txt=Downloads]</span>
                        <span class="wpdm-pp__meta-value">[download_count]</span>
                    </div>
                    <div class="wpdm-pp__meta-item [hide_empty:file_count]">
                        <span class="wpdm-pp__meta-label">[txt=Files]</span>
                        <span class="wpdm-pp__meta-value">[file_count]</span>
                    </div>
                </div>

                <!-- Author -->
                <div class="wpdm-pp__author [hide_empty:author_name]">
                    [avatar]
                    <div class="wpdm-pp__author-info">
                        <span class="wpdm-pp__author-label">[txt=Author]</span>
                        <span class="wpdm-pp__author-name">[author_name]</span>
                    </div>
                </div>

                <!-- Dates Footer -->
                <div class="wpdm-pp__dates">
                    <div class="[hide_empty:create_date]">
                        <span>[txt=Published]</span>
                        [create_date]
                    </div>
                    <div class="[hide_empty:update_date]">
                        <span>[txt=Updated]</span>
                        [update_date]
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
