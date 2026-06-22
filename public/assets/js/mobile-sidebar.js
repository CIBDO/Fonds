/**
 * Sidebar tiroir mobile / tablette
 */
(function ($) {
    'use strict';

    var MOBILE_MQ = window.matchMedia('(max-width: 1199.98px)');

    function getBody() {
        return document.body;
    }

    function isMobileNav() {
        return document.documentElement.classList.contains('mobile-layout');
    }

    function resetSidebarPosition() {
        var sidebar = document.getElementById('sidebar');
        if (!sidebar) {
            return;
        }
        if (isMobileNav() && !document.documentElement.classList.contains('nav-open')) {
            sidebar.style.transform = 'translate3d(-100%, 0, 0)';
        } else {
            sidebar.style.transform = '';
        }
    }

    function closeSidebar() {
        document.documentElement.classList.remove('nav-open');
        document.documentElement.style.overflow = '';

        var body = getBody();
        if (body) {
            body.classList.remove('nav-open');
            body.style.overflow = '';
            body.style.position = '';
        }

        $('.main-wrapper').removeClass('slide-nav');
        $('#sidebar').removeClass('opened');
        $('.sidebar-overlay').removeClass('opened');
        $('html').removeClass('menu-opened');

        resetSidebarPosition();
    }

    function openSidebar() {
        document.documentElement.classList.add('nav-open');

        var body = getBody();
        if (body) {
            body.classList.add('nav-open');
        }

        var sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.style.transform = '';
        }

        $('.main-wrapper').addClass('slide-nav');
        $('#sidebar').addClass('opened');
        $('.sidebar-overlay').addClass('opened');
        $('html').addClass('menu-opened');

        if (isMobileNav() && body) {
            body.style.overflow = 'hidden';
        }
    }

    function toggleSidebar() {
        if (document.documentElement.classList.contains('nav-open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    function isNavigationLink(anchor) {
        if (!anchor || anchor.classList.contains('submenu-toggle')) {
            return false;
        }
        var href = anchor.getAttribute('href');
        return href && href !== '#' && href.indexOf('javascript:') !== 0;
    }

    function destroySidebarSlimScroll() {
        var $inner = $('.sidebar-inner.slimscroll');
        if ($inner.length && $inner.parent().hasClass('slimScrollDiv')) {
            try {
                $inner.slimScroll({ destroy: true });
            } catch (e) {
                /* ignore */
            }
        }
    }

    function disableLegacySidebarHandlers() {
        $('#sidebar-menu a').off('click');
        $('.sidebar-overlay').off('click');
        $('#mobile_btn').off('click');

        $('#sidebar-menu ul').each(function () {
            $(this).stop(true, true).removeAttr('style');
        });
        $('#sidebar-menu a.subdrop').removeClass('subdrop');
    }

    function initMobileSidebar() {
        disableLegacySidebarHandlers();

        if (!isMobileNav()) {
            closeSidebar();
            return;
        }

        closeSidebar();
        destroySidebarSlimScroll();
    }

    function resetPageScrollState() {
        document.documentElement.classList.remove('nav-open');
        document.documentElement.style.overflow = '';

        var body = getBody();
        if (body) {
            body.style.overflow = '';
            body.style.position = '';
            body.classList.remove('nav-open');
        }

        if (window.jQuery) {
            $('html').removeClass('menu-opened');
            $('.sidebar-overlay').removeClass('opened');
        }

        resetSidebarPosition();
    }

    window.DgtcpSidebar = {
        open: openSidebar,
        close: closeSidebar,
        toggle: toggleSidebar,
        isMobile: isMobileNav
    };

    $(function () {
        resetPageScrollState();
        initMobileSidebar();

        $(document).on('click', '#mobile_btn', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (isMobileNav()) {
                toggleSidebar();
            }
            return false;
        });

        $(document).on('click', '#sidebar_close_btn', function (e) {
            e.preventDefault();
            closeSidebar();
        });

        $(document).on('click', '.sidebar-overlay', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            closeSidebar();
        });

        $(document).on('click', '#sidebar-menu a', function () {
            if (!isMobileNav()) {
                return;
            }
            if (!isNavigationLink(this)) {
                return;
            }
            closeSidebar();
        });

        if (MOBILE_MQ.addEventListener) {
            MOBILE_MQ.addEventListener('change', initMobileSidebar);
        } else if (MOBILE_MQ.addListener) {
            MOBILE_MQ.addListener(initMobileSidebar);
        }

        $(window).on('resize', initMobileSidebar);
    });

    window.addEventListener('pageshow', resetPageScrollState);
})(jQuery);
