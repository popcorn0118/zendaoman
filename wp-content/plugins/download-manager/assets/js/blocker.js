async function detectAdblock({ timeoutMs = 2500 } = {}) {
    // 0) Optional: neutral bait that your CSS won't hide by accident
    ensureBait();

    // 1) Control: should NEVER be blocked
    const controlOk = await loadScript(wpdm_url.site+'wp-content/plugins/download-manager/assets/js/control/ping.js', timeoutMs);

    // 2) Decoy: LIKELY to be blocked by ad-block lists
    const decoyOk = await loadScript(wpdm_url.site+'wp-content/plugins/download-manager/assets/js/ads/adserver.js', timeoutMs);

    // 3) Bait: if your styles/extensions hide ad-like selectors
    const baitHidden = isBaitHidden();

    // Decide:
    // - If control fails, environment is flaky/CSP — don't claim adblock.
    // - If control passes AND decoy fails -> likely adblock.
    // - BaitHidden is a weak signal, used only to strengthen confidence.
    const likelyAdblock = controlOk && !decoyOk;
    return {
        isAdblock: likelyAdblock || (likelyAdblock && baitHidden),
        details: { controlOk, decoyOk, baitHidden }
    };
}

function loadScript(src, timeoutMs) {
    return new Promise((resolve) => {
        const s = document.createElement('script');
        s.src = src + (src.includes('?') ? '&' : '?') + 'v=' + Date.now();
        s.async = true;

        let settled = false;
        const done = (ok) => { if (!settled) { settled = true; resolve(ok); } };

        const timer = setTimeout(() => done(false), timeoutMs);
        s.onload = () => { clearTimeout(timer); done(true); };
        s.onerror = () => { clearTimeout(timer); done(false); };

        // If a blocker removes the element outright, we still have the timeout fallback
        document.head.appendChild(s);
    });
}

function ensureBait() {
    if (document.getElementById('__ad_bait__')) return;
    const bait = document.createElement('div');
    bait.id = '__ad_bait__';
    bait.className = 'ads ad ad-banner adsbox ad-wrapper';
    Object.assign(bait.style, {
        position: 'absolute',
        left: '-9999px',
        top: '-9999px',
        width: '1px',
        height: '1px',
        pointerEvents: 'none'
    });
    document.body.appendChild(bait);
}

function isBaitHidden() {
    const el = document.getElementById('__ad_bait__');
    if (!el) return false;
    const cs = getComputedStyle(el);
    const hidden =
        el.offsetParent === null ||
        cs.display === 'none' ||
        cs.visibility === 'hidden' ||
        parseInt(cs.height, 10) === 0 ||
        parseInt(cs.width, 10) === 0 ||
        cs.opacity === '0';
    return hidden;
}

// Example usage:

jQuery(function($) {

    detectAdblock().then(({ isAdblock, details }) => {
        console.log('Adblock?', isAdblock, details);
        if (isAdblock) {
            const body = $('body');

            // Remove from existing elements
            $('.wpdm-download-link.download-on-click').removeAttr('data-downloadurl');

            // Handle click on dynamic elements via delegation
            body.on('click', '.wpdm-download-link.download-on-click', function (e) {
                $(this).removeAttr('data-downloadurl');
                WPDM.bootAlert("Ad blocker detected", abmsg);
                return false;
            });

            // MutationObserver to handle dynamically added elements
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            // Check if the added node itself matches
                            if ($(node).hasClass('wpdm-download-link') && $(node).hasClass('download-on-click')) {
                                $(node).removeAttr('data-downloadurl');
                            }
                            // Check descendants of the added node
                            $(node).find('.wpdm-download-link.download-on-click').removeAttr('data-downloadurl');
                        }
                    });
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            if( abmsgd === "site" || ( abmsgd === "package" && iswpdmpropage ))
                WPDM.bootAlert("Ad blocker detected", abmsg)
        }
    });

});

