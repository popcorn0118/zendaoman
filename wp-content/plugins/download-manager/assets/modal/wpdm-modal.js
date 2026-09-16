/**
 * WPDM Modal Dialog System
 * Enterprise-Grade Modal Dialogs for WordPress Download Manager
 * Version: 1.1.0
 *
 * Usage:
 *   WPDMDialog.alert('Title', 'Message');
 *   WPDMDialog.confirm('Title', 'Message').then(confirmed => { ... });
 *   WPDMDialog.prompt('Title', 'Message', { placeholder: 'Enter value' }).then(value => { ... });
 *   WPDMDialog.ajax('Title', '/api/endpoint').then(result => { ... });
 *   WPDMDialog.ajax('Title', { url: '/api', method: 'POST', data: {...} }, { size: 'lg' });
 *
 * Or via WPDM global (if available):
 *   WPDM.dialog.alert('Title', 'Message');
 *   WPDM.dialog.ajax('Title', '/api/endpoint');
 */

var WPDMDialog = (function($) {
    'use strict';

    // SVG Icons
    var icons = {
        info: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
        success: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
        warning: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        danger: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
        question: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        close: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>'
    };

    // Loading spinner HTML (uses CSS pseudo-elements + span for 3-dot bounce animation)
    var loadingSpinner = '<div class="wpdm-dialog__loading"><div class="wpdm-dialog__spinner"><span></span></div><p class="wpdm-dialog__loading-text">Loading...</p></div>';

    /**
     * Detect dark mode from WPDM settings or page classes/attributes
     * Priority: WPDM settings > page classes > system preference
     * @returns {string} 'light-mode', 'dark-mode', or '' (system preference)
     */
    function detectDarkMode() {

        if($('body').hasClass('wp-admin')) return 'light-mode';

        // Check WPDM color scheme setting first (from wpdm_js localized script)
        if (typeof wpdm_js !== 'undefined' && wpdm_js.color_scheme) {
            if (wpdm_js.color_scheme === 'light') return 'light-mode';
            if (wpdm_js.color_scheme === 'dark') return 'dark-mode';
            // 'system' falls through to check page classes or return empty
        }

        // Check page classes and attributes
        var isLightMode = $('body').hasClass('light-mode') ||
                          $('.w3eden').hasClass('light-mode') ||
                          $('html').attr('data-theme') === 'light' ||
                          $('body').attr('data-theme') === 'light';
        var isDarkMode = $('body').hasClass('dark-mode') ||
                         $('.w3eden').hasClass('dark-mode') ||
                         $('html').hasClass('dark-mode') ||
                         $('body').hasClass('dark') ||
                         $('html').attr('data-theme') === 'dark' ||
                         $('body').attr('data-theme') === 'dark';

        if (isLightMode) return 'light-mode';
        if (isDarkMode) return 'dark-mode';
        return ''; // System preference (CSS media query handles this)
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text Text to escape
     * @returns {string} Escaped text
     */
    function escapeHtml(text) {
        if (typeof text !== 'string') return text;
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * Generate unique ID for dialog
     * @returns {string} Unique ID
     */
    function uniqueId() {
        return 'wpdm-dialog-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    }

    /**
     * Create dialog HTML
     * @param {Object} options Dialog options
     * @returns {string} HTML string
     */
    function createDialogHtml(options) {

        var id = options.id || uniqueId();
        var darkModeClass = detectDarkMode();
        var sizeClass = options.size ? 'wpdm-dialog--' + options.size : '';
        var showIcon = options.icon !== false;
        var iconType = options.type || 'info';

        var html = '<div class="wpdm-dialog-wrapper w3eden ' + darkModeClass + '" id="' + id + '">';
        html += '<div class="wpdm-dialog-backdrop"></div>';
        html += '<div class="wpdm-dialog ' + sizeClass + '" role="dialog" aria-modal="true" aria-labelledby="' + id + '-title">';

        // Close button (if closable)
        if (options.closable !== false) {
            html += '<button type="button" class="wpdm-dialog__close" aria-label="Close">' + icons.close + '</button>';
        }

        // Header
        html += '<div class="wpdm-dialog__header">';
        if (showIcon) {
            html += '<div class="wpdm-dialog__icon wpdm-dialog__icon--' + iconType + '">' + icons[iconType] + '</div>';
        }
        html += '<div class="wpdm-dialog__header-content">';
        html += '<h3 class="wpdm-dialog__title" id="' + id + '-title">' + escapeHtml(options.title) + '</h3>';
        if (options.subtitle) {
            html += '<p class="wpdm-dialog__subtitle">' + escapeHtml(options.subtitle) + '</p>';
        }
        html += '</div>';
        html += '</div>';

        // Body
        html += '<div class="wpdm-dialog__body">';
        if (options.message) {
            html += '<p class="wpdm-dialog__message">' + (options.html ? options.message : escapeHtml(options.message)) + '</p>';
        }
        if (options.input) {
            html += '<div class="wpdm-dialog__input-wrapper">';
            html += '<input type="' + (options.inputType || 'text') + '" class="wpdm-dialog__input" placeholder="' + escapeHtml(options.placeholder || '') + '" value="' + escapeHtml(options.inputValue || '') + '">';
            html += '</div>';
        }
        if (options.content) {
            html += options.content;
        }
        html += '</div>';

        // Footer
        if (options.buttons && options.buttons.length > 0) {
            var footerClass = options.compactFooter ? 'wpdm-dialog__footer wpdm-dialog__footer--compact' : 'wpdm-dialog__footer';
            html += '<div class="' + footerClass + '">';
            options.buttons.forEach(function(btn, index) {
                var btnClass = 'wpdm-dialog__btn wpdm-dialog__btn--' + (btn.type || 'secondary');
                html += '<button type="button" class="' + btnClass + '" data-action="' + (btn.action || index) + '">' + escapeHtml(btn.text) + '</button>';
            });
            html += '</div>';
        }

        html += '</div>';
        html += '</div>';

        return html;
    }

    /**
     * Show dialog
     * @param {Object} options Dialog options
     * @returns {Promise} Resolves with { action: string, value: string|null }
     */
    function show(options) {
        return new Promise(function(resolve) {
            var html = createDialogHtml(options);
            var $dialog = $(html);
            $('body').append($dialog);

            // Focus trap elements
            var $wrapper = $dialog;
            var $input = $wrapper.find('.wpdm-dialog__input');
            var $buttons = $wrapper.find('.wpdm-dialog__btn');
            var $closeBtn = $wrapper.find('.wpdm-dialog__close');

            // Show dialog with animation
            requestAnimationFrame(function() {
                $wrapper.addClass('wpdm-dialog-visible');
                if ($input.length) {
                    $input.focus().select();
                } else if ($buttons.length) {
                    $buttons.last().focus();
                }
            });

            // Close function
            function close(result) {
                $wrapper.removeClass('wpdm-dialog-visible');
                $(document).off('keydown.wpdmDialog');
                setTimeout(function() {
                    $wrapper.remove();
                    resolve(result);
                }, 250);
            }

            // Button clicks
            $buttons.on('click', function() {
                var action = $(this).data('action');
                var inputValue = $input.length ? $input.val() : null;
                close({ action: action, value: inputValue });
            });

            // Close button click
            $closeBtn.on('click', function() {
                close({ action: 'close', value: null });
            });

            // Backdrop click (if not static)
            if (options.backdrop !== 'static') {
                $wrapper.find('.wpdm-dialog-backdrop').on('click', function() {
                    close({ action: 'backdrop', value: null });
                });
            }

            // Escape key
            if (options.keyboard !== false) {
                $(document).on('keydown.wpdmDialog', function(e) {
                    if (e.key === 'Escape') {
                        close({ action: 'escape', value: null });
                    }
                });
            }

            // Enter key for prompt
            if (options.input) {
                $input.on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        close({ action: 'confirm', value: $input.val() });
                    }
                });
            }
        });
    }

    // Public API
    return {
        /**
         * Show a custom dialog
         * @param {Object} options Dialog options
         * @returns {Promise}
         */
        show: show,

        /**
         * Show an alert dialog
         * @param {string} title Dialog title
         * @param {string} message Dialog message
         * @param {Object} options Additional options
         * @returns {Promise<string>}
         */
        alert: function(title, message, options) {
            options = options || {};
            options.buttons = options.buttons || [
                { text: options.buttonText || 'OK', type: 'primary', action: 'ok' }
            ];
            return show({
                title: title,
                message: message,
                type: options.type || 'info',
                icon: options.icon,
                size: options.size || 'sm',
                html: options.html,
                compactFooter: true,
                buttons: options.buttons,
                backdrop: 'static',
                keyboard: true
            }).then(function(result) {
                return result.action;
            });
        },

        /**
         * Show a success alert
         * @param {string} title Dialog title
         * @param {string} message Dialog message
         * @param {Object} options Additional options
         * @returns {Promise<string>}
         */
        success: function(title, message, options) {
            options = options || {};
            options.type = 'success';
            return this.alert(title, message, options);
        },

        /**
         * Show a warning alert
         * @param {string} title Dialog title
         * @param {string} message Dialog message
         * @param {Object} options Additional options
         * @returns {Promise<boolean>}
         */
        warning: function(title, message, options) {
            options = options || {};
            options.type = 'warning';
            return this.alert(title, message, options);
        },

        /**
         * Show an error alert
         * @param {string} title Dialog title
         * @param {string} message Dialog message
         * @param {Object} options Additional options
         * @returns {Promise<boolean>}
         */
        error: function(title, message, options) {
            options = options || {};
            options.type = 'danger';
            return this.alert(title, message, options);
        },

        /**
         * Show a confirm dialog
         * @param {string} title Dialog title
         * @param {string} message Dialog message
         * @param {Object} options Additional options
         * @returns {Promise<boolean>}
         */
        confirm: function(title, message, options) {
            options = options || {};
            options.buttons = options.buttons || [
                { text: options.cancelText || 'Cancel', type: 'secondary', action: 'cancel' },
                { text: options.confirmText || 'Confirm', type: options.confirmType || 'primary', action: 'confirm' }
            ];
            return show({
                title: title,
                message: message,
                type: options.type || 'question',
                icon: options.icon,
                size: options.size || 'sm',
                html: options.html,
                buttons: options.buttons,
                backdrop: options.backdrop || 'static',
                keyboard: options.keyboard !== false
            }).then(function(result) {
                return result.action === 'confirm';
            });
        },

        /**
         * Show a delete confirm dialog
         * @param {string} title Dialog title
         * @param {string} message Dialog message
         * @param {Object} options Additional options
         * @returns {Promise<boolean>}
         */
        confirmDelete: function(title, message, options) {
            options = options || {};
            options.type = 'danger';
            options.confirmText = options.confirmText || 'Delete';
            options.confirmType = 'danger';
            return this.confirm(title, message, options);
        },

        /**
         * Show a prompt dialog
         * @param {string} title Dialog title
         * @param {string} message Dialog message (optional)
         * @param {Object} options Additional options
         * @returns {Promise<string|null>}
         */
        prompt: function(title, message, options) {
            options = options || {};
            return show({
                title: title,
                message: message,
                type: options.type || 'question',
                icon: options.icon,
                size: options.size || 'md',
                input: true,
                inputType: options.inputType || 'text',
                inputValue: options.inputValue || '',
                placeholder: options.placeholder || '',
                html: options.html,
                buttons: [
                    { text: options.cancelText || 'Cancel', type: 'secondary', action: 'cancel' },
                    { text: options.confirmText || 'Submit', type: 'primary', action: 'confirm' }
                ],
                backdrop: options.backdrop || 'static',
                keyboard: options.keyboard !== false
            }).then(function(result) {
                return result.action === 'confirm' ? result.value : null;
            });
        },

        /**
         * Show a dialog with async content loaded via AJAX
         * @param {string} title Dialog title
         * @param {string|Object} urlOrOptions URL string or AJAX options object
         * @param {Object} options Additional dialog options
         * @returns {Promise} Resolves with { action: string, data: any }
         *
         * Usage:
         *   WPDMDialog.ajax('Title', '/api/endpoint');
         *   WPDMDialog.ajax('Title', '/api/endpoint', { size: 'lg' });
         *   WPDMDialog.ajax('Title', { url: '/api/endpoint', method: 'POST', data: { id: 123 } });
         */
        ajax: function(title, urlOrOptions, options) {
            options = options || {};
            var ajaxOptions = typeof urlOrOptions === 'string' ? { url: urlOrOptions } : urlOrOptions;

            return new Promise(function(resolve, reject) {
                var dialogId = uniqueId();
                var darkModeClass = detectDarkMode();
                var sizeClass = options.size ? 'wpdm-dialog--' + options.size : 'wpdm-dialog--md';

                // Build dialog HTML with loading state
                var html = '<div class="wpdm-dialog-wrapper w3eden ' + darkModeClass + '" id="' + dialogId + '">';
                html += '<div class="wpdm-dialog-backdrop"></div>';
                html += '<div class="wpdm-dialog ' + sizeClass + '" role="dialog" aria-modal="true" aria-labelledby="' + dialogId + '-title">';

                // Close button
                if (options.closable !== false) {
                    html += '<button type="button" class="wpdm-dialog__close" aria-label="Close">' + icons.close + '</button>';
                }

                // Header
                html += '<div class="wpdm-dialog__header">';
                if (options.icon !== false) {
                    var iconType = options.type || 'info';
                    html += '<div class="wpdm-dialog__icon wpdm-dialog__icon--' + iconType + '">' + icons[iconType] + '</div>';
                }
                html += '<div class="wpdm-dialog__header-content">';
                html += '<h3 class="wpdm-dialog__title" id="' + dialogId + '-title">' + escapeHtml(title) + '</h3>';
                if (options.subtitle) {
                    html += '<p class="wpdm-dialog__subtitle">' + escapeHtml(options.subtitle) + '</p>';
                }
                html += '</div></div>';

                // Body with loading spinner
                html += '<div class="wpdm-dialog__body wpdm-dialog__body--ajax">' + loadingSpinner + '</div>';

                // Footer placeholder (will be updated after AJAX)
                html += '<div class="wpdm-dialog__footer wpdm-dialog__footer--hidden"></div>';

                html += '</div></div>';

                var $dialog = $(html);
                $('body').append($dialog);

                var $wrapper = $dialog;
                var $body = $wrapper.find('.wpdm-dialog__body');
                var $footer = $wrapper.find('.wpdm-dialog__footer');
                var $closeBtn = $wrapper.find('.wpdm-dialog__close');

                // Show dialog
                requestAnimationFrame(function() {
                    $wrapper.addClass('wpdm-dialog-visible');
                });

                // Close function
                function close(result) {
                    $wrapper.removeClass('wpdm-dialog-visible');
                    $(document).off('keydown.wpdmDialog');
                    setTimeout(function() {
                        $wrapper.remove();
                        resolve(result);
                    }, 250);
                }

                // Close button click
                $closeBtn.on('click', function() {
                    close({ action: 'close', data: null });
                });

                // Backdrop click
                if (options.backdrop !== 'static') {
                    $wrapper.find('.wpdm-dialog-backdrop').on('click', function() {
                        close({ action: 'backdrop', data: null });
                    });
                }

                // Escape key
                if (options.keyboard !== false) {
                    $(document).on('keydown.wpdmDialog', function(e) {
                        if (e.key === 'Escape') {
                            close({ action: 'escape', data: null });
                        }
                    });
                }

                // Make AJAX request
                $.ajax({
                    url: ajaxOptions.url,
                    method: ajaxOptions.method || 'GET',
                    data: ajaxOptions.data || {},
                    dataType: ajaxOptions.dataType || 'html',
                    headers: ajaxOptions.headers || {},
                    timeout: ajaxOptions.timeout || 30000
                }).done(function(response) {
                    // Handle JSON response with content field
                    var content = response;
                    var buttons = options.buttons;
                    var responseData = null;

                    if (typeof response === 'object') {
                        responseData = response;
                        content = response.content || response.html || response.body || '';
                        // Allow response to override buttons
                        if (response.buttons) {
                            buttons = response.buttons;
                        }
                    }

                    // Update body content with animation
                    $body.removeClass('wpdm-dialog__body--ajax wpdm-dialog__body--loaded').html(content);

                    // Trigger reflow and add loaded class for animation
                    requestAnimationFrame(function() {
                        $body.addClass('wpdm-dialog__body--loaded');
                    });

                    // Update footer with buttons if provided
                    if (buttons && buttons.length > 0) {
                        var footerHtml = '';
                        buttons.forEach(function(btn, index) {
                            var btnClass = 'wpdm-dialog__btn wpdm-dialog__btn--' + (btn.type || 'secondary');
                            footerHtml += '<button type="button" class="' + btnClass + '" data-action="' + (btn.action || index) + '">' + escapeHtml(btn.text) + '</button>';
                        });
                        $footer.html(footerHtml).removeClass('wpdm-dialog__footer--hidden');

                        // Bind button clicks
                        $footer.find('.wpdm-dialog__btn').on('click', function() {
                            var action = $(this).data('action');
                            close({ action: action, data: responseData });
                        });
                    }

                    // Run callback if provided
                    if (typeof options.onLoad === 'function') {
                        options.onLoad($body, responseData);
                    }

                }).fail(function(xhr, status, error) {
                    // Show error state
                    var errorHtml = '<div class="wpdm-dialog__error">';
                    errorHtml += '<div class="wpdm-dialog__error-icon">' + icons.danger + '</div>';
                    errorHtml += '<p class="wpdm-dialog__error-message">' + escapeHtml(options.errorMessage || 'Failed to load content. Please try again.') + '</p>';
                    if (options.showRetry !== false) {
                        errorHtml += '<button type="button" class="wpdm-dialog__btn wpdm-dialog__btn--secondary wpdm-dialog__retry">Retry</button>';
                    }
                    errorHtml += '</div>';

                    // Update body with error and animate
                    $body.removeClass('wpdm-dialog__body--ajax wpdm-dialog__body--loaded').html(errorHtml);
                    requestAnimationFrame(function() {
                        $body.addClass('wpdm-dialog__body--loaded');
                    });

                    // Retry button
                    $body.find('.wpdm-dialog__retry').on('click', function() {
                        $body.addClass('wpdm-dialog__body--ajax').html(loadingSpinner);
                        // Re-trigger AJAX
                        $.ajax({
                            url: ajaxOptions.url,
                            method: ajaxOptions.method || 'GET',
                            data: ajaxOptions.data || {},
                            dataType: ajaxOptions.dataType || 'html',
                            headers: ajaxOptions.headers || {},
                            timeout: ajaxOptions.timeout || 30000
                        }).done(function(response) {
                            var content = response;
                            if (typeof response === 'object') {
                                content = response.content || response.html || response.body || '';
                            }
                            $body.removeClass('wpdm-dialog__body--ajax wpdm-dialog__body--loaded').html(content);
                            requestAnimationFrame(function() {
                                $body.addClass('wpdm-dialog__body--loaded');
                            });
                            if (typeof options.onLoad === 'function') {
                                options.onLoad($body, response);
                            }
                        }).fail(function() {
                            $body.removeClass('wpdm-dialog__body--ajax wpdm-dialog__body--loaded').html(errorHtml);
                            requestAnimationFrame(function() {
                                $body.addClass('wpdm-dialog__body--loaded');
                            });
                        });
                    });

                    // Call error callback if provided
                    if (typeof options.onError === 'function') {
                        options.onError(xhr, status, error);
                    }
                });
            });
        },

        /**
         * Show a dialog and load content into body from URL
         * Alias for ajax() with simplified options
         * @param {string} title Dialog title
         * @param {string} url URL to load
         * @param {Object} options Additional options
         * @returns {Promise}
         */
        load: function(title, url, options) {
            return this.ajax(title, url, options);
        }
    };
})(jQuery);

// Attach to WPDM global object after all JS is fully loaded
jQuery(function($) {
    if (typeof WPDM !== 'undefined') {
        WPDM.dialog = WPDMDialog;
    }
});
