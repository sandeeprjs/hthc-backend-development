/**
 * Custom JavaScript functionality
 * Navigation is now handled by navigation.js
 */
document.addEventListener('DOMContentLoaded', function() {
    // ========== Navigation functionality moved to navigation.js ==========
    /*
    // Sidebar Toggle Elements - COMMENTED OUT to avoid conflicts
    const sidebarToggle = document.getElementById('sidebarToggle');
    const closeSidebar = document.getElementById('closeSidebar');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const navToggleBtn = document.getElementById('nav-toggle') || document.querySelector('.nav-toggle');
    const navigation = document.querySelector('.navigation');
    const mainContainer = document.querySelector('.main-container');

    // Create overlay for mobile navigation if it doesn't exist
    if (!document.getElementById('mobileNavOverlay')) {
        const overlay = document.createElement('div');
        overlay.id = 'mobileNavOverlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1029;
            display: none;
        `;
        document.body.appendChild(overlay);

        // Close navigation when clicking overlay
        overlay.addEventListener('click', function() {
            toggleSidebar();
        });
    }

    const mobileNavOverlay = document.getElementById('mobileNavOverlay');

    // Toggle Functions
    function toggleSidebar() {
        // For Bootstrap sidebar component
        if (sidebar) {
            sidebar.classList.toggle('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('show');
            }
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        }

        // For custom navigation component
        if (navigation && mainContainer) {
            navigation.classList.toggle('mobile-open');
            mainContainer.classList.toggle('navigation-open');
            if (mobileNavOverlay) {
                mobileNavOverlay.style.display = navigation.classList.contains('mobile-open') ? 'block' : 'none';
            }
        }
    }

    // Event Listeners
    // Sidebar toggle buttons
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    if (closeSidebar) {
        closeSidebar.addEventListener('click', toggleSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', toggleSidebar);
    }

    if (navToggleBtn) {
        navToggleBtn.addEventListener('click', toggleSidebar);
    }

    // Accordion Menu
    // Handle both .accordion elements and .has-submenu > a elements
    const accordionElements = document.querySelectorAll('.accordion, .has-submenu > a');

    accordionElements.forEach(function(accordion) {
        accordion.addEventListener('click', function(e) {
            e.preventDefault();

            // Toggle the active state
            this.classList.toggle('accordion-active');

            // Find the submenu (works for both structures)
            const submenu = this.nextElementSibling;

            if (submenu && (submenu.classList.contains('submenu') || submenu.tagName === 'UL')) {
                // Find parent section for closing other menus
                const parentSection = this.closest('.nav-section');

                if (parentSection) {
                    // Close other open submenus in the same section
                    parentSection.querySelectorAll('.submenu, ul').forEach(otherSubmenu => {
                        if (otherSubmenu !== submenu && otherSubmenu.style.display === 'block') {
                            otherSubmenu.style.display = 'none';
                            const otherAccordion = otherSubmenu.previousElementSibling;
                            otherAccordion.classList.remove('accordion-active');

                            // Rotate chevron for the other menu
                            const otherChevron = otherAccordion.querySelector('.submenu-icon, .fa-chevron-right');
                            if (otherChevron) {
                                otherChevron.style.transform = 'rotate(0deg)';
                            }
                        }
                    });
                }

                // Toggle the current submenu
                if (submenu.style.display === 'block') {
                    submenu.style.display = 'none';

                    // Rotate chevron back
                    const chevron = this.querySelector('.submenu-icon, .fa-chevron-right');
                    if (chevron) {
                        chevron.style.transform = 'rotate(0deg)';
                    }
                } else {
                    // Show this submenu
                    submenu.style.display = 'block';

                    // Rotate chevron
                    const chevron = this.querySelector('.submenu-icon, .fa-chevron-right');
                    if (chevron) {
                        chevron.style.transform = 'rotate(90deg)';
                    }
                }
            }
        });
    });

    // Dropdown Handling
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.closest('.dropdown');

            if (dropdown) {
                const menu = dropdown.querySelector('.dropdown-menu');

                if (menu) {
                    // Close other open dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(openMenu => {
                        if (openMenu !== menu) {
                            openMenu.classList.remove('show');
                        }
                    });

                    // Toggle current dropdown
                    menu.classList.toggle('show');

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function closeDropdown(event) {
                        if (!dropdown.contains(event.target)) {
                            menu.classList.remove('show');
                            document.removeEventListener('click', closeDropdown);
                        }
                    });
                }
            }
        });
    });

    // Responsive Handling
    function handleResponsiveness() {
        const isMobile = window.innerWidth <= 768;

        if (!isMobile) {
            // Reset for desktop
            if (sidebar) {
                sidebar.classList.remove('show');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }
            if (navigation) {
                navigation.classList.remove('mobile-collapsed', 'mobile-open');
                navigation.style.left = '0';
            }
            if (mainContainer) {
                mainContainer.classList.remove('navigation-open');
                mainContainer.style.marginLeft = '200px';
                mainContainer.style.width = 'calc(100% - 200px)';
            }
            if (mobileNavOverlay) {
                mobileNavOverlay.style.display = 'none';
            }
            document.body.style.overflow = '';
        } else {
            // Mobile specific defaults
            if (navigation) {
                navigation.classList.add('mobile-collapsed');
                navigation.style.left = '-250px';
            }
            if (mainContainer) {
                mainContainer.style.marginLeft = '0';
                mainContainer.style.width = '100%';
            }
        }
    }

    // Initial responsiveness check
    handleResponsiveness();

    // Recheck on window resize
    window.addEventListener('resize', handleResponsiveness);

    // Initialize Active Elements
    // Make sure navigation links are active based on current URL
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.navigation a, .sidebar-menu a, .nav-link');

    navLinks.forEach(link => {
        // Handle null/undefined href safely
        const linkPath = link.getAttribute('href');
        if (linkPath && currentPath.includes(linkPath) && linkPath !== '/' && linkPath !== '#') {
            link.classList.add('active');

            // If in a submenu, expand the parent menu
            const parentLi = link.closest('li.has-submenu');
            if (parentLi) {
                parentLi.classList.add('open');
                const submenu = parentLi.querySelector('ul');
                if (submenu) {
                    submenu.style.display = 'block';
                }
            }

            // For accordion menus
            const parentAccordion = link.closest('.submenu, ul')?.previousElementSibling;
            if (parentAccordion && parentAccordion.classList.contains('accordion')) {
                parentAccordion.classList.add('accordion-active');
                const chevron = parentAccordion.querySelector('.submenu-icon, .fa-chevron-right');
                if (chevron) {
                    chevron.style.transform = 'rotate(90deg)';
                }
            }
        }
    });

    // Initialize Active Accordions
    document.querySelectorAll('.accordion.active, .has-submenu.active > a').forEach(function(activeAccordion) {
        const submenu = activeAccordion.nextElementSibling;
        if (submenu && (submenu.classList.contains('submenu') || submenu.tagName === 'UL')) {
            submenu.style.display = 'block';

            // Rotate chevron for active accordions
            const chevron = activeAccordion.querySelector('.submenu-icon, .fa-chevron-right');
            if (chevron) {
                chevron.style.transform = 'rotate(90deg)';
            }
        }
    });
    */

    // ========== Global Loader ==========
    // Keep this part as it's still useful and doesn't conflict
    const globalLoader = document.getElementById('global-loader');

    if (globalLoader) {
        // Show loader when page is loading
        window.addEventListener('load', function() {
            globalLoader.style.display = 'none';
        });

        // Show loader on page navigation
        window.addEventListener('beforeunload', function() {
            globalLoader.style.display = 'block';
        });

        // Handle AJAX calls if jQuery is available
        if (typeof $ !== 'undefined') {
            $(document).ajaxStart(function() {
                globalLoader.style.display = 'block';
            });

            $(document).ajaxStop(function() {
                globalLoader.style.display = 'none';
            });
        }
    }

    // Add any custom, non-navigation related code here
    // For example:
    // 1. Form validation
    // 2. Data visualization
    // 3. Custom widgets
    // 4. etc.

});
