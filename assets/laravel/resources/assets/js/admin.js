import {MediaLibrary} from './media-library/store';
import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        MediaLibrary: MediaLibrary
    }
});

require('./media-library/index');

// Confirm action component //
import ConfirmAction from './vue-components/confirm-action';
Vue.component('v-confirm-action', ConfirmAction);

// MaxLength directive //
import MaxLength from './vue-directives/maxlength';
Vue.directive('maxlength', MaxLength);

// Tabs component //
import Tabs from './vue-components/tabs/tabs';
Vue.component('v-tabs', Tabs);

new Vue({
    el: '#app',
    store,
    methods: {
        /**
         * Calculate page container height. Window height - navbars heights.
         */
        containerHeight() {
            var availableHeight = $(window).height() - ($('body > .navbar').outerHeight() || 0) - ($('body > .navbar + .navbar').outerHeight() || 0) - ($('body > .navbar + .navbar-collapse').outerHeight() || 0);

            $('.page-container').attr('style', 'min-height:' + availableHeight + 'px');
        },

        /**
         * Send post request.
         * @param {MouseEvent} event
         */
        automaticPostSend(event) {
            event.preventDefault();

            var $target = $(event.currentTarget);

            var data = null;
            if (event.target.nodeName === 'FORM') {
                $target = $(event.target);
                data = $target.serialize();
            }

            $target.lock();

            Request.post(event.currentTarget.href || event.target.action, data)
                .done(function (response) {
                    if (response.refresh) {
                        location.reload();
                    } else if (response.error) {
                        $.jGrowl(response.error, {
                            header: ' Chyba! ',
                            theme: ' bg-danger  alert-styled-left alert-styled-custom-danger'
                        });
                    }
                })
                .fail(function (response) {
                    if (response.status === 422) {
                        Form.addErrors($target, response.responseJSON.errors);
                    } else {
                        $.jGrowl(response.text, {
                            header: ' Chyba! ',
                            theme: ' bg-danger  alert-styled-left alert-styled-custom-danger'
                        });
                    }
                })
                .always(function () {
                    $target.unlock();
                });
        },

        fixModalBackdrop() {
            $.prototype.modal.Constructor.prototype.hideModal = function () {
                const self = this;

                this.$element.hide();
                this.backdrop(function () {
                    const backdrops = self.$body.find('.modal-backdrop');

                    if (!backdrops.length) {
                        self.$body.removeClass('modal-open');
                        self.resetAdjustments();
                        self.resetScrollbar();
                    }

                    self.$element.trigger('hidden.bs.modal');
                })
            }
        }
    },

    mounted() {
        const self = this;

        // Heading elements toggler
        // -------------------------

        // Add control button toggler to page and panel headers if have heading elements
        $('.panel-heading, .page-header-content, .panel-body').has('> .heading-elements').append('<a class="heading-elements-toggle"><i class="icon-menu"></i></a>');


        // Toggle visible state of heading elements
        $('.heading-elements-toggle').on('click', function () {
            $(this).parent().children('.heading-elements').toggleClass('visible');
        });

        // Breadcrumb elements toggler
        // -------------------------

        // Add control button toggler to breadcrumbs if has elements
        $('.breadcrumb-line').has('.breadcrumb-elements').append('<a class="breadcrumb-elements-toggle"><i class="icon-menu-open"></i></a>');


        // Toggle visible state of breadcrumb elements
        $('.breadcrumb-elements-toggle').on('click', function () {
            $(this).parent().children('.breadcrumb-elements').toggleClass('visible');
        });

        // Navbar navigation
        // -------------------------

        // Prevent dropdown from closing on click
        $(document).on('click', '.dropdown-content', function (e) {
            e.stopPropagation();
        });

        // Disabled links
        $('.navbar-nav .disabled a').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
        });

        // Show tabs inside dropdowns
        $('.dropdown-content a[data-toggle="tab"]').on('click', function (e) {
            $(this).tab('show')
        });

        //
        // Sidebar categories
        //

        // Hide if collapsed by default
        $('.category-collapsed').children('.category-content').hide();

        // Rotate icon if collapsed by default
        $('.category-collapsed').find('[data-action=collapse]').addClass('rotate-180');

        // Collapse on click
        $('.category-title [data-action=collapse]').click(function (e) {
            e.preventDefault();
            var $categoryCollapse = $(this).parent().parent().parent().nextAll();
            $(this).parents('.category-title').toggleClass('category-collapsed');
            $(this).toggleClass('rotate-180');

            self.containerHeight(); // adjust page height

            $categoryCollapse.slideToggle(150);
        });

        //
        // Panels
        //

        // Hide if collapsed by default
        $('.panel-collapsed').children('.panel-heading').nextAll().hide();

        // Rotate icon if collapsed by default
        $('.panel-collapsed').find('[data-action=collapse]').children('i').addClass('rotate-180');

        // Collapse on click
        $('.panel [data-action=collapse]').click(function (e) {
            e.preventDefault();
            var $panelCollapse = $(this).parent().parent().parent().parent().nextAll();
            $(this).parents('.panel').toggleClass('panel-collapsed');
            $(this).toggleClass('rotate-180');

            self.containerHeight(); // recalculate page height

            $panelCollapse.slideToggle(150);
        });


        // Remove elements
        // -------------------------

        // Panels
        $('.panel [data-action=close]').click(function (e) {
            e.preventDefault();
            var $panelClose = $(this).parent().parent().parent().parent().parent();

            self.containerHeight(); // recalculate page height

            $panelClose.slideUp(150, function () {
                $(this).remove();
            });
        });

        // Sidebar categories
        $('.category-title [data-action=close]').click(function (e) {
            e.preventDefault();
            var $categoryClose = $(this).parent().parent().parent().parent();

            self.containerHeight(); // recalculate page height

            $categoryClose.slideUp(150, function () {
                $(this).remove();
            });
        });


        // Main navigation
        // -------------------------

        // Add 'active' class to parent list item in all levels
        $('.navigation').find('li.active').parents('li').addClass('active');

        // Hide all nested lists
        $('.navigation').find('li').not('.active, .category-title').has('ul').children('ul').addClass('hidden-ul');

        // Highlight children links
        $('.navigation').find('li').has('ul').children('a').addClass('has-ul');

        // Add active state to all dropdown parent levels
        $('.dropdown-menu:not(.dropdown-content), .dropdown-menu:not(.dropdown-content) .dropdown-submenu').has('li.active').addClass('active').parents('.navbar-nav .dropdown:not(.language-switch), .navbar-nav .dropup:not(.language-switch)').addClass('active');


        // Main navigation tooltips positioning
        // -------------------------

        // Left sidebar
        $('.navigation-main > .navigation-header > i').tooltip({
            placement: 'right',
            container: 'body'
        });


        // Collapsible functionality
        // -------------------------

        // Main navigation
        $('.navigation-main').find('li').has('ul').children('a').on('click', function (e) {
            e.preventDefault();

            // Collapsible
            $(this).parent('li').not('.disabled').not($('.sidebar-xs').not('.sidebar-xs-indicator').find('.navigation-main').children('li')).toggleClass('active').children('ul').slideToggle(250);

            // Accordion
            if ($('.navigation-main').hasClass('navigation-accordion')) {
                $(this).parent('li').not('.disabled').not($('.sidebar-xs').not('.sidebar-xs-indicator').find('.navigation-main').children('li')).siblings(':has(.has-ul)').removeClass('active').children('ul').slideUp(250);
            }
        });

        // Alternate navigation
        $('.navigation-alt').find('li').has('ul').children('a').on('click', function (e) {
            e.preventDefault();

            // Collapsible
            $(this).parent('li').not('.disabled').toggleClass('active').children('ul').slideToggle(200);

            // Accordion
            if ($('.navigation-alt').hasClass('navigation-accordion')) {
                $(this).parent('li').not('.disabled').siblings(':has(.has-ul)').removeClass('active').children('ul').slideUp(200);
            }
        });

        // Mini sidebar
        // -------------------------

        // Toggle mini sidebar
        $('.sidebar-main-toggle').on('click', function (e) {
            e.preventDefault();

            // Toggle min sidebar class
            $('body').toggleClass('sidebar-xs');
        });

        // Sidebar controls
        // -------------------------

        // Disable click in disabled navigation items
        $(document).on('click', '.navigation .disabled a', function (e) {
            e.preventDefault();
        });

        // Adjust page height on sidebar control button click
        $(document).on('click', '.sidebar-control', function (e) {
            self.containerHeight();
        });

        // Hide main sidebar in Dual Sidebar
        $(document).on('click', '.sidebar-main-hide', function (e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-main-hidden');
        });

        // Toggle second sidebar in Dual Sidebar
        $(document).on('click', '.sidebar-secondary-hide', function (e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-secondary-hidden');
        });

        // Hide detached sidebar
        $(document).on('click', '.sidebar-detached-hide', function (e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-detached-hidden');
        });

        // Hide all sidebars
        $(document).on('click', '.sidebar-all-hide', function (e) {
            e.preventDefault();

            $('body').toggleClass('sidebar-all-hidden');
        });


        //
        // Opposite sidebar
        //

        // Collapse main sidebar if opposite sidebar is visible
        $(document).on('click', '.sidebar-opposite-toggle', function (e) {
            e.preventDefault();

            // Opposite sidebar visibility
            $('body').toggleClass('sidebar-opposite-visible');

            // If visible
            if ($('body').hasClass('sidebar-opposite-visible')) {

                // Make main sidebar mini
                $('body').addClass('sidebar-xs');

                // Hide children lists
                $('.navigation-main').children('li').children('ul').css('display', '');
            }
            else {

                // Make main sidebar default
                $('body').removeClass('sidebar-xs');
            }
        });


        // Hide main sidebar if opposite sidebar is shown
        $(document).on('click', '.sidebar-opposite-main-hide', function (e) {
            e.preventDefault();

            // Opposite sidebar visibility
            $('body').toggleClass('sidebar-opposite-visible');

            // If visible
            if ($('body').hasClass('sidebar-opposite-visible')) {

                // Hide main sidebar
                $('body').addClass('sidebar-main-hidden');
            }
            else {

                // Show main sidebar
                $('body').removeClass('sidebar-main-hidden');
            }
        });


        // Hide secondary sidebar if opposite sidebar is shown
        $(document).on('click', '.sidebar-opposite-secondary-hide', function (e) {
            e.preventDefault();

            // Opposite sidebar visibility
            $('body').toggleClass('sidebar-opposite-visible');

            // If visible
            if ($('body').hasClass('sidebar-opposite-visible')) {

                // Hide secondary
                $('body').addClass('sidebar-secondary-hidden');

            }
            else {

                // Show secondary
                $('body').removeClass('sidebar-secondary-hidden');
            }
        });


        // Hide all sidebars if opposite sidebar is shown
        $(document).on('click', '.sidebar-opposite-hide', function (e) {
            e.preventDefault();

            // Toggle sidebars visibility
            $('body').toggleClass('sidebar-all-hidden');

            // If hidden
            if ($('body').hasClass('sidebar-all-hidden')) {

                // Show opposite
                $('body').addClass('sidebar-opposite-visible');

                // Hide children lists
                $('.navigation-main').children('li').children('ul').css('display', '');
            }
            else {

                // Hide opposite
                $('body').removeClass('sidebar-opposite-visible');
            }
        });


        // Keep the width of the main sidebar if opposite sidebar is visible
        $(document).on('click', '.sidebar-opposite-fix', function (e) {
            e.preventDefault();

            // Toggle opposite sidebar visibility
            $('body').toggleClass('sidebar-opposite-visible');
        });


        // Mobile sidebar controls
        // -------------------------

        // Toggle main sidebar
        $('.sidebar-mobile-main-toggle').on('click', function (e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-mobile-main').removeClass('sidebar-mobile-secondary sidebar-mobile-opposite sidebar-mobile-detached');
        });

        // Toggle secondary sidebar
        $('.sidebar-mobile-secondary-toggle').on('click', function (e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-mobile-secondary').removeClass('sidebar-mobile-main sidebar-mobile-opposite sidebar-mobile-detached');
        });

        // Toggle opposite sidebar
        $('.sidebar-mobile-opposite-toggle').on('click', function (e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-mobile-opposite').removeClass('sidebar-mobile-main sidebar-mobile-secondary sidebar-mobile-detached');
        });

        // Toggle detached sidebar
        $('.sidebar-mobile-detached-toggle').on('click', function (e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-mobile-detached').removeClass('sidebar-mobile-main sidebar-mobile-secondary sidebar-mobile-opposite');
        });


        // Mobile sidebar setup
        // -------------------------

        $(window).on('resize', function () {
            setTimeout(function () {
                self.containerHeight();

                if ($(window).width() <= 768) {

                    // Add mini sidebar indicator
                    $('body').addClass('sidebar-xs-indicator');

                    // Place right sidebar before content
                    $('.sidebar-opposite').insertBefore('.content-wrapper');

                    // Place detached sidebar before content
                    $('.sidebar-detached').insertBefore('.content-wrapper');
                }
                else {

                    // Remove mini sidebar indicator
                    $('body').removeClass('sidebar-xs-indicator');

                    // Revert back right sidebar
                    $('.sidebar-opposite').insertAfter('.content-wrapper');

                    // Remove all mobile sidebar classes
                    $('body').removeClass('sidebar-mobile-main sidebar-mobile-secondary sidebar-mobile-detached sidebar-mobile-opposite');

                    // Revert left detached position
                    if ($('body').hasClass('has-detached-left')) {
                        $('.sidebar-detached').insertBefore('.container-detached');
                    }

                    // Revert right detached position
                    else if ($('body').hasClass('has-detached-right')) {
                        $('.sidebar-detached').insertAfter('.container-detached');
                    }
                }
            }, 100);
        }).resize();

        // Plugins
        // -------------------------

        // Popover
        $('[data-popup="popover"]').popover();


        // Tooltip
        $('[data-popup="tooltip"]').tooltip();

        // Automatic post
        $('*:not(form).automatic-post').on('click', this.automaticPostSend.bind(this));
        $('form.automatic-post').on('submit', this.automaticPostSend.bind(this));

        if (window.onAppReady) {
            window.onAppReady();
        }

        this.fixModalBackdrop();
    },

    data: {
        // make localization usable in vue templates as $root.localization.trans('foo.bar')
        localization: new Localization(window.cms_trans)
    }
});
