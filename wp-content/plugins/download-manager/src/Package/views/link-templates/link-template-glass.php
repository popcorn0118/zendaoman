<!-- WPDM Link Template: Glassmorphism Card -->

<div class="wpdm-glass-card">
    <div class="wpdm-glass-card__bg"></div>
    <div class="wpdm-glass-card__inner">
        <div class="wpdm-glass-card__header">
            <div class="wpdm-glass-card__icon">
                [icon]
            </div>
            <div class="wpdm-glass-card__badge [hide_empty:version]">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v12"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/></svg>
                [version]
            </div>
        </div>

        <div class="wpdm-glass-card__body">
            <h3 class="wpdm-glass-card__title">[page_link]</h3>
            <p class="wpdm-glass-card__excerpt">[excerpt_100]</p>
        </div>

        <div class="wpdm-glass-card__meta">
            <div class="wpdm-glass-card__meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>[download_count]</span>
            </div>
            <div class="wpdm-glass-card__meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                <span>[file_size]</span>
            </div>
            <div class="wpdm-glass-card__meta-item [hide_empty:file_count]">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <span>[file_count] [txt=files]</span>
            </div>
        </div>

        <div class="wpdm-glass-card__footer">
            <div class="wpdm-glass-card__categories [hide_empty:categories]">
                [categories]
            </div>
            <div class="wpdm-glass-card__action">
                [download_link]
            </div>
        </div>
    </div>
</div>

<style>
.w3eden .wpdm-glass-card {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 20px;
}

.w3eden .wpdm-glass-card__bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-purple) 50%, var(--color-info) 100%);
    z-index: 0;
}

.w3eden .wpdm-glass-card__bg::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 60%);
}

.w3eden .wpdm-glass-card__bg::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -30%;
    width: 80%;
    height: 80%;
    background: radial-gradient(circle, rgba(var(--color-primary-rgb),0.5) 0%, transparent 60%);
}

.w3eden .wpdm-glass-card__inner {
    position: relative;
    z-index: 1;
    margin: 3px;
    padding: 24px;
    border-radius: 17px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
}

.w3eden .wpdm-glass-card__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.w3eden .wpdm-glass-card__icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(var(--color-primary-rgb),0.15) 0%, rgba(var(--color-primary-rgb),0.08) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(var(--color-primary-rgb),0.15);
    border: 1px solid rgba(var(--color-primary-rgb),0.2);
}

.w3eden .wpdm-glass-card__icon img {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

.w3eden .wpdm-glass-card__badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-purple) 100%);
    color: #fff;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(var(--color-primary-rgb),0.3);
}

.w3eden .wpdm-glass-card__body {
    margin-bottom: 16px;
}

.w3eden .wpdm-glass-card__title {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 8px 0;
    padding: 0;
    border: 0;
    line-height: 1.4;
}

.w3eden .wpdm-glass-card__title a {
    color: #1a1a2e;
    text-decoration: none;
    transition: color 0.2s ease;
}

.w3eden .wpdm-glass-card__title a:hover {
    color: var(--color-primary);
}

.w3eden .wpdm-glass-card__excerpt {
    color: var(--color-muted);
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

.w3eden .wpdm-glass-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    padding: 16px 0;
    border-top: 1px solid rgba(var(--color-primary-rgb),0.15);
    border-bottom: 1px solid rgba(var(--color-primary-rgb),0.15);
    margin-bottom: 16px;
}

.w3eden .wpdm-glass-card__meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--color-secondary);
    font-size: 13px;
    font-weight: 500;
}

.w3eden .wpdm-glass-card__meta-item svg {
    color: var(--color-primary);
}

.w3eden .wpdm-glass-card__footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.w3eden .wpdm-glass-card__categories {
    flex: 1;
    font-size: 12px;
}

.w3eden .wpdm-glass-card__categories a {
    display: inline-block;
    padding: 4px 10px;
    background: rgba(var(--color-primary-rgb),0.12);
    color: var(--color-primary);
    border-radius: 12px;
    text-decoration: none;
    font-weight: 500;
    margin-right: 6px;
    margin-bottom: 4px;
    transition: all 0.2s ease;
}

.w3eden .wpdm-glass-card__categories a:hover {
    background: rgba(var(--color-primary-rgb),0.25);
}

.w3eden .wpdm-glass-card__action .btn {
    padding: 10px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-purple) 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(var(--color-primary-rgb),0.35);
    transition: all 0.3s ease;
}

.w3eden .wpdm-glass-card__action .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(var(--color-primary-rgb),0.45);
}

/* Hover effect */
.w3eden .wpdm-glass-card:hover .wpdm-glass-card__inner {
    background: rgba(255, 255, 255, 0.92);
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .w3eden:not(.light-mode) .wpdm-glass-card__inner {
        background: rgba(30, 41, 59, 0.85);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .w3eden:not(.light-mode) .wpdm-glass-card__title a {
        color: #f1f5f9;
    }

    .w3eden:not(.light-mode) .wpdm-glass-card__excerpt {
        color: #94a3b8;
    }

    .w3eden:not(.light-mode) .wpdm-glass-card__meta {
        border-color: rgba(255,255,255,0.1);
    }

    .w3eden:not(.light-mode) .wpdm-glass-card__meta-item {
        color: #cbd5e1;
    }

    .w3eden:not(.light-mode) .wpdm-glass-card__icon {
        background: rgba(var(--color-primary-rgb),0.2);
        border-color: rgba(var(--color-primary-rgb),0.3);
    }

    .w3eden:not(.light-mode) .wpdm-glass-card__categories a {
        background: rgba(var(--color-primary-rgb),0.2);
        color: var(--color-primary-hover);
    }

    .w3eden:not(.light-mode) .wpdm-glass-card:hover .wpdm-glass-card__inner {
        background: rgba(30, 41, 59, 0.92);
    }
}

.w3eden.dark-mode .wpdm-glass-card__inner {
    background: rgba(30, 41, 59, 0.85);
    border-color: rgba(255, 255, 255, 0.1);
}

.w3eden.dark-mode .wpdm-glass-card__title a {
    color: #f1f5f9;
}

.w3eden.dark-mode .wpdm-glass-card__excerpt {
    color: #94a3b8;
}

.w3eden.dark-mode .wpdm-glass-card__meta {
    border-color: rgba(255,255,255,0.1);
}

.w3eden.dark-mode .wpdm-glass-card__meta-item {
    color: #cbd5e1;
}

.w3eden.dark-mode .wpdm-glass-card__icon {
    background: rgba(var(--color-primary-rgb),0.2);
    border-color: rgba(var(--color-primary-rgb),0.3);
}

.w3eden.dark-mode .wpdm-glass-card__categories a {
    background: rgba(var(--color-primary-rgb),0.2);
    color: var(--color-primary-hover);
}

.w3eden.dark-mode .wpdm-glass-card:hover .wpdm-glass-card__inner {
    background: rgba(30, 41, 59, 0.92);
}
</style>
