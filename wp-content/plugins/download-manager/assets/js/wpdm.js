
(function ( $ ) {

    // Modern Modal Plugin
    $.fn.modal = function(option) {
        var modal = this;
        var modalId = modal.attr('id');
        var $backdrop = null;

        // Hide modal
        if (option === 'hide') {
            modal.removeClass('in show');
            $backdrop = $('.wpdm-modal-backdrop[data-modal="' + modalId + '"]');
            $backdrop.removeClass('in');

            // Remove after animation completes
            setTimeout(function() {
                modal.css('display', 'none');
                $backdrop.remove();
                $('body').removeClass('modal-open').css('overflow', '');
                modal.trigger('hidden.bs.modal');
            }, 250);

            // Remove event listeners
            $(document).off('keydown.wpdmModal');
            return modal;
        }

        // Show modal
        if (option === 'show' || option === undefined) {
            // Prevent body scroll
            $('body').addClass('modal-open').css('overflow', 'hidden');

            // Create backdrop
            $backdrop = $('<div class="modal-backdrop fade wpdm-modal-backdrop" data-modal="' + modalId + '"></div>');
            $('body').append($backdrop);

            // Show modal with proper display
            modal.css({
                'display': 'block',
                'z-index': 1050
            });

            // Trigger reflow for CSS transition
            modal[0].offsetHeight;

            // Add active classes after a tiny delay for animation
            setTimeout(function() {
                $backdrop.addClass('in');
                modal.addClass('in show');
                modal.trigger('shown.bs.modal');
            }, 10);

            // Handle dismiss button clicks
            modal.find('[data-dismiss="modal"]').off('click.wpdmModal').on('click.wpdmModal', function(e) {
                e.preventDefault();
                modal.modal('hide');
            });

            // Handle backdrop click to close
            $backdrop.on('click', function() {
                if (!modal.data('backdrop') || modal.data('backdrop') !== 'static') {
                    modal.modal('hide');
                }
            });

            // Handle Escape key
            $(document).off('keydown.wpdmModal').on('keydown.wpdmModal', function(e) {
                if (e.key === 'Escape' && modal.hasClass('in')) {
                    if (!modal.data('keyboard') || modal.data('keyboard') !== false) {
                        modal.modal('hide');
                    }
                }
            });
        }

        // Toggle modal
        if (option === 'toggle') {
            if (modal.hasClass('in')) {
                modal.modal('hide');
            } else {
                modal.modal('show');
            }
        }

        return modal;
    };

    $.fn.tooltip = function (options) {
        // Default options
        var settings = $.extend({
            background: "#333",
            color: "#fff",
            padding: "5px 10px",
            borderRadius: "4px",
            fontSize: "12px"
        }, options);

        // Create tooltip element once
        var $tooltip = $("<div class='simple-tooltip'></div>").css({
            position: "absolute",
            maxWidth: "200px",
            display: "none",
            zIndex: 9999,
            background: settings.background,
            color: settings.color,
            padding: settings.padding,
            borderRadius: settings.borderRadius,
            fontSize: settings.fontSize,
            pointerEvents: "none",
        }).appendTo("body");

        // Attach events
        return this.each(function () {
            var $elem = $(this);
            var title = $elem.attr("title");

            $elem.on("mouseenter", function (e) {
                if (!title) return;
                $elem.data("tip-title", title).removeAttr("title"); // prevent default browser tooltip
                $tooltip.text(title).fadeIn(150);
                $tooltip.css({
                    top: e.pageY + 10,
                    left: e.pageX + 10
                });
            }).on("mousemove", function (e) {
                $tooltip.css({
                    top: e.pageY + 10,
                    left: e.pageX + 10
                });
            }).on("mouseleave", function () {
                $tooltip.hide();
                $elem.attr("title", $elem.data("tip-title")); // restore original title
            });
        });
    };

}( jQuery ));



jQuery(function($) {
    var $body = $('body');

    // Collapse toggle
    $body.on('click', '.w3eden [data-toggle="collapse"]', function(e) {
        e.preventDefault();
        var target = $(this).attr('href') || $(this).data('target');
        $(target).slideToggle(200);
    });

    // Modal toggle via data attributes
    $body.on('click', '.w3eden [data-toggle="modal"], .w3eden[data-toggle="modal"]', function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        $(target).modal('show');
    });

    // Tab toggle
    $body.on('click', '.w3eden [data-toggle="tab"]', function(e) {
        e.preventDefault();
        var $this = $(this);
        var $container = $this.closest('.nav-tabs, .nav-pills');
        var target = $this.attr('href');

        // Deactivate all tabs and panes
        $container.find('[data-toggle="tab"]').each(function() {
            var $tab = $(this);
            $tab.removeClass('active');
            $tab.parent('li').removeClass('active');
            var pane = $tab.attr('href');
            if (pane) {
                $(pane).removeClass('active in');
            }
        });

        // Activate clicked tab and its pane
        $this.addClass('active');
        $this.parent('li').addClass('active');
        if (target) {
            $(target).addClass('active in');
        }

        // Trigger event
        $this.trigger('shown.bs.tab');
    });

    // Dropdown toggle
    $body.on('click', '.w3eden [data-toggle="dropdown"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $dropdown = $(this).closest('.dropdown, .btn-group');
        var isOpen = $dropdown.hasClass('open');

        // Close all other dropdowns
        $('.w3eden .dropdown, .w3eden .btn-group').removeClass('open');

        // Toggle this dropdown
        if (!isOpen) {
            $dropdown.addClass('open');
        }
    });

    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown, .btn-group').length) {
            $('.w3eden .dropdown, .w3eden .btn-group').removeClass('open');
        }
    });
});
