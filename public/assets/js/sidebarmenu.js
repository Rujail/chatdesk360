document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const layout = document.documentElement.getAttribute('data-layout');
    const currentPath = window.location.pathname;

    // Helper to normalize any href (absolute or relative) to just the pathname
    function getPathOnly(url) {
        try {
            return new URL(url, window.location.origin).pathname;
        } catch (e) {
            // Fallback for malformed or relative paths
            return url.startsWith('/') ? url : '/' + url;
        }
    }

    if (layout !== 'vertical') return;

    // Normalize current path once (remove trailing slash)
    const cleanCurrent = currentPath.replace(/\/$/, '');

    // -------------------------------
    // 1. Auto-select LEFT MINI NAV ITEMS (direct top-level links)
    // -------------------------------
    document.querySelectorAll('.mini-nav-item > a').forEach((a) => {
        const href = a.getAttribute('href');
        if (href !== 'javascript:void(0)') {
            const linkPath = getPathOnly(href).replace(/\/$/, '');
            if (linkPath === cleanCurrent) {
                a.parentElement.classList.add('selected');
            }
        }
    });

    // -------------------------------
    // 2. Auto-select SUBMENU + highlight items inside right sidebar
    // -------------------------------
    document.querySelectorAll('.mini-nav-item').forEach((item) => {
        const a = item.querySelector('a');
        if (!a) return;

        const href = a.getAttribute('href');
        const id = item.id;

        // Only process items that have a submenu
        if (!id || !id.startsWith('mini-')) return;

        const menu = document.getElementById('menu-right-' + id);
        if (!menu) return;

        // Check all links inside this submenu
        const links = menu.querySelectorAll('a');
        links.forEach((link) => {
            const linkHref = link.getAttribute('href');
            const linkPath = getPathOnly(linkHref).replace(/\/$/, '');

            // Match: exact OR current path is child of this link
            if (linkPath === cleanCurrent || cleanCurrent.startsWith(linkPath + '/')) {
                item.classList.add('selected');                    // highlight mini icon
                menu.classList.add('d-block');                     // show right sidebar
                document.body.setAttribute('data-sidebartype', 'full'); // expand layout
                link.classList.add('active');                      // highlight link

                const parentLi = link.closest('li');
                if (parentLi) parentLi.classList.add('selected');

                const parentUl = link.closest('ul');
                if (parentUl) parentUl.classList.add('in');
            }
        });
    });

    // -------------------------------
    // 3. Top menu (sidebar links) ACTIVE class
    // -------------------------------
    document.querySelectorAll('#sidebarnav a.sidebar-link').forEach((anchor) => {
        const href = anchor.getAttribute('href');
        if (!href || href === 'javascript:void(0)') return;

        const linkPath = getPathOnly(href).replace(/\/$/, '');

        if (linkPath === cleanCurrent || cleanCurrent.startsWith(linkPath + '/')) {
            anchor.classList.add('active');

            const parentLi = anchor.closest('li');
            if (parentLi) parentLi.classList.add('selected');

            const parentUl = anchor.closest('ul');
            if (parentUl) parentUl.classList.add('in');
        }
    });

    // -------------------------------
    // 4. Mini-nav click toggle (left icons)
    // -------------------------------
    document.querySelectorAll('.mini-nav .mini-nav-item').forEach((item) => {
        item.addEventListener('click', function (e) {
            const id = this.id;
            if (!id || !id.startsWith('mini-')) return;

            e.preventDefault();

            // Toggle sidebar type
            const currentType = document.body.getAttribute('data-sidebartype');
            document.body.setAttribute('data-sidebartype', currentType === 'mini-sidebar' ? 'full' : 'mini-sidebar');

            // Remove selected from others (but keep current page selected)
            document.querySelectorAll('.mini-nav .mini-nav-item').forEach((i) => {
                const a = i.querySelector('a');
                if (!a) return;
                const aPath = getPathOnly(a.getAttribute('href')).replace(/\/$/, '');
                if (aPath !== cleanCurrent) {
                    i.classList.remove('selected');
                }
            });

            this.classList.add('selected');

            // Hide all right menus, show the clicked one
            document.querySelectorAll('.sidebarmenu nav').forEach((nav) => nav.classList.remove('d-block'));
            const nav = document.getElementById('menu-right-' + id);
            if (nav) nav.classList.add('d-block');
        });
    });

    // --------------------------------------------
    // Highlight the active menu item + open parent menus
    // --------------------------------------------
    document.querySelectorAll('.sidebar-link').forEach((link) => {
        const href = link.getAttribute('href');
        if (!href) return;

        const linkPath = getPathOnly(href).replace(/\/$/, '');

        if (linkPath && (linkPath === cleanCurrent || cleanCurrent.startsWith(linkPath + '/'))) {
            link.classList.add('active');

            const parentItem = link.closest('li.sidebar-item');
            if (parentItem) parentItem.classList.add('active');

            // Open parent submenu if exists
            const submenu = link.closest('ul.collapse');
            if (submenu) {
                submenu.classList.add('show');
                submenu.setAttribute('aria-expanded', 'true');

                const parentLink = submenu.closest('li.sidebar-item')?.querySelector('a.sidebar-link');
                if (parentLink) parentLink.classList.add('active');
            }
        }
    });

    // ------------------------------------------------
    // Toggle submenu on click (arrow only)
    // ------------------------------------------------
    document.querySelectorAll('.sidebar-link').forEach((link) => {
        const arrow = link.querySelector('.toggle-arrow');

        if (arrow) {
            // Click on arrow → toggle submenu only
            arrow.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const parentItem = link.closest('li.sidebar-item');
                const submenu = parentItem.querySelector('ul.collapse');

                if (!submenu) return;

                arrow.classList.toggle('active');

                const isOpen = submenu.classList.contains('show');
                submenu.classList.toggle('show');
                submenu.setAttribute('aria-expanded', String(!isOpen));
            });

            // Click on link (not arrow) → normal navigation
            link.addEventListener('click', function (e) {
                if (e.target === arrow) return;

                const href = link.getAttribute('href');
                if (href && href !== 'javascript:void(0)' && href !== '#') {
                    // Let browser handle navigation
                    return;
                }
            });
        }
    });

    // Horizontal layout handling (unchanged, but added path normalization)
    if (layout === 'horizontal') {
        function findMatchingElement() {
            const anchors = document.querySelectorAll('#sidebarnavh ul#sidebarnav a');
            for (let i = 0; i < anchors.length; i++) {
                const anchorPath = getPathOnly(anchors[i].href);
                if (anchorPath === cleanCurrent) {
                    return anchors[i];
                }
            }
            return null;
        }

        let el = findMatchingElement();
        if (el) el.classList.add('active');

        document.querySelectorAll('#sidebarnavh ul#sidebarnav a.active').forEach(function (link) {
            link.closest('a')?.parentElement.classList.add('selected');
            link.closest('ul')?.parentElement.classList.add('selected');
        });

        // Assuming this function exists somewhere in your codebase
        // setSidebarFullIfActive();
    }
});