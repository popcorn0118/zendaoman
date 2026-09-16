<!-- WPDM Link Template: Spotlight Hero -->

<div class="wpdm-spotlight">
    <div class="wpdm-spotlight__image">
        [thumb_600x340]
        <div class="wpdm-spotlight__overlay">
            <a href="[page_url]" class="wpdm-spotlight__preview-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="M11 8v6"/><path d="M8 11h6"/></svg>
                <span>[txt=Preview]</span>
            </a>
        </div>
        <div class="wpdm-spotlight__stats">
            <div class="wpdm-spotlight__stat">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                [download_count]
            </div>
            <div class="wpdm-spotlight__stat">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                [view_count]
            </div>
        </div>
    </div>

    <div class="wpdm-spotlight__content">
        <div class="wpdm-spotlight__top">
            <div class="wpdm-spotlight__icon">
                [icon]
            </div>
            <div class="wpdm-spotlight__tags [hide_empty:categories]">
                [categories]
            </div>
        </div>

        <h3 class="wpdm-spotlight__title">[page_link]</h3>

        <div class="wpdm-spotlight__info">
            <div class="wpdm-spotlight__info-row">
                <div class="wpdm-spotlight__info-item">
                    <span class="wpdm-spotlight__info-label">[txt=Size]</span>
                    <span class="wpdm-spotlight__info-value">[file_size]</span>
                </div>
                <div class="wpdm-spotlight__info-item [hide_empty:version]">
                    <span class="wpdm-spotlight__info-label">[txt=Version]</span>
                    <span class="wpdm-spotlight__info-value">[version]</span>
                </div>
                <div class="wpdm-spotlight__info-item [hide_empty:update_date]">
                    <span class="wpdm-spotlight__info-label">[txt=Updated]</span>
                    <span class="wpdm-spotlight__info-value">[update_date]</span>
                </div>
            </div>
        </div>

        <div class="wpdm-spotlight__author [hide_empty:author_name]">
            <img src="[avatar_url]" alt="[author_name]" class="wpdm-spotlight__avatar">
            <div class="wpdm-spotlight__author-info">
                <span class="wpdm-spotlight__author-label">[txt=Created by]</span>
                <a href="[author_profile_url]" class="wpdm-spotlight__author-name">[author_name]</a>
            </div>
        </div>

        <div class="wpdm-spotlight__actions">

            <div class="wpdm-spotlight__download">
                [download_link]
            </div>
        </div>
    </div>
</div>

<style>
.w3eden .wpdm-spotlight {
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
    margin-bottom: 24px;
    transition: all 0.3s ease;
}

.w3eden .wpdm-spotlight:hover {
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
    transform: translateY(-4px);
}

.w3eden .wpdm-spotlight__image {
    position: relative;
    aspect-ratio: 16/9;
    overflow: hidden;
    background: linear-gradient(135deg, rgba(var(--color-secondary-rgb),0.1) 0%, rgba(var(--color-secondary-rgb),0.2) 100%);
}

.w3eden .wpdm-spotlight__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.w3eden .wpdm-spotlight:hover .wpdm-spotlight__image img {
    transform: scale(1.05);
}

.w3eden .wpdm-spotlight__overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.w3eden .wpdm-spotlight:hover .wpdm-spotlight__overlay {
    opacity: 1;
}

.w3eden .wpdm-spotlight__preview-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: rgba(255,255,255,0.95);
    color: #1e293b;
    border-radius: 30px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.w3eden .wpdm-spotlight__preview-btn:hover {
    background: #ffffff;
    transform: scale(1.05);
}

.w3eden .wpdm-spotlight__stats {
    position: absolute;
    bottom: 12px;
    right: 12px;
    display: flex;
    gap: 8px;
}

.w3eden .wpdm-spotlight__stat {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    color: #ffffff;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.w3eden .wpdm-spotlight__content {
    padding: 24px;
}

.w3eden .wpdm-spotlight__top {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.w3eden .wpdm-spotlight__icon {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
}

.w3eden .wpdm-spotlight__icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.w3eden .wpdm-spotlight__tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.w3eden .wpdm-spotlight__tags a {
    padding: 4px 12px;
    background: rgba(var(--color-secondary-rgb),0.1);
    color: var(--color-secondary);
    font-size: 12px;
    font-weight: 500;
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.w3eden .wpdm-spotlight__tags a:hover {
    background: rgba(var(--color-primary-rgb),0.15);
    color: var(--color-primary);
}

.w3eden .wpdm-spotlight__title {
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 16px 0;
    padding: 0;
    border: 0;
    line-height: 1.4;
}

.w3eden .wpdm-spotlight__title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s ease;
}

.w3eden .wpdm-spotlight__title a:hover {
    color: var(--color-primary);
}

.w3eden .wpdm-spotlight__info {
    margin-bottom: 20px;
    padding: 16px;
    background: rgba(var(--color-secondary-rgb),0.06);
    border-radius: 12px;
}

.w3eden .wpdm-spotlight__info-row {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
}

.w3eden .wpdm-spotlight__info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.w3eden .wpdm-spotlight__info-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-muted);
}

.w3eden .wpdm-spotlight__info-value {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

.w3eden .wpdm-spotlight__author {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(var(--color-secondary-rgb),0.2);
}

.w3eden .wpdm-spotlight__avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.w3eden .wpdm-spotlight__author-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.w3eden .wpdm-spotlight__author-label {
    font-size: 12px;
    color: var(--color-muted);
}

.w3eden .wpdm-spotlight__author-name {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    text-decoration: none;
}

.w3eden .wpdm-spotlight__author-name:hover {
    color: var(--color-primary);
}

.w3eden .wpdm-spotlight__actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

.w3eden .wpdm-spotlight__fav {
    flex-shrink: 0;
}

.w3eden .wpdm-spotlight__fav .btn {
    width: 48px;
    height: 48px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: rgba(var(--color-secondary-rgb),0.1);
    border: 1px solid rgba(var(--color-secondary-rgb),0.2);
    color: var(--color-secondary);
    transition: all 0.2s ease;
}

.w3eden .wpdm-spotlight__fav .btn:hover {
    background: rgba(var(--color-danger-rgb),0.1);
    border-color: rgba(var(--color-danger-rgb),0.2);
    color: var(--color-danger);
}

.w3eden .wpdm-spotlight__download {
    flex: 1;
}

.w3eden .wpdm-spotlight__download .btn {
    width: 100%;
    padding: 14px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-active) 100%);
    border: none;
    box-shadow: 0 4px 14px rgba(var(--color-primary-rgb),0.3);
    transition: all 0.3s ease;
}

.w3eden .wpdm-spotlight__download .btn:hover {
    box-shadow: 0 6px 20px rgba(var(--color-primary-rgb),0.4);
    transform: translateY(-2px);
}

/* Responsive */
@media (min-width: 640px) {
    .w3eden .wpdm-spotlight {
        flex-direction: row;
    }

    .w3eden .wpdm-spotlight__image {
        width: 45%;
        flex-shrink: 0;
        aspect-ratio: auto;
    }

    .w3eden .wpdm-spotlight__content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .w3eden .wpdm-spotlight__actions {
        margin-top: auto;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .w3eden:not(.light-mode) .wpdm-spotlight {
        background: #1e293b;
    }

    .w3eden:not(.light-mode) .wpdm-spotlight__title a {
        color: #f1f5f9;
    }

    .w3eden:not(.light-mode) .wpdm-spotlight__info {
        background: rgba(0,0,0,0.2);
    }

    .w3eden:not(.light-mode) .wpdm-spotlight__info-value {
        color: #e2e8f0;
    }

    .w3eden:not(.light-mode) .wpdm-spotlight__tags a {
        background: rgba(255,255,255,0.1);
        color: #cbd5e1;
    }

    .w3eden:not(.light-mode) .wpdm-spotlight__author {
        border-color: rgba(255,255,255,0.1);
    }

    .w3eden:not(.light-mode) .wpdm-spotlight__author-name {
        color: #f1f5f9;
    }

    .w3eden:not(.light-mode) .wpdm-spotlight__fav .btn {
        background: rgba(255,255,255,0.1);
        border-color: rgba(255,255,255,0.15);
        color: #cbd5e1;
    }
}

.w3eden.dark-mode .wpdm-spotlight {
    background: #1e293b;
}

.w3eden.dark-mode .wpdm-spotlight__title a {
    color: #f1f5f9;
}

.w3eden.dark-mode .wpdm-spotlight__info {
    background: rgba(0,0,0,0.2);
}

.w3eden.dark-mode .wpdm-spotlight__info-value {
    color: #e2e8f0;
}

.w3eden.dark-mode .wpdm-spotlight__tags a {
    background: rgba(255,255,255,0.1);
    color: #cbd5e1;
}

.w3eden.dark-mode .wpdm-spotlight__author {
    border-color: rgba(255,255,255,0.1);
}

.w3eden.dark-mode .wpdm-spotlight__author-name {
    color: #f1f5f9;
}

.w3eden.dark-mode .wpdm-spotlight__fav .btn {
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.15);
    color: #cbd5e1;
}
</style>
