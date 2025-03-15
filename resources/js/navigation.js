/**
 * Ultimate Navigation Fix with Media Query Support
 */

// Function to check if we're on mobile
function isMobileView() {
    return window.innerWidth < 768;
}

// Create the emergency toggle button
const emergencyBtn = document.createElement('button');
emergencyBtn.id = 'emergency-toggle-btn';
emergencyBtn.style.cssText = `
    position: fixed;
    top: 15px;
    left: 15px;
    width: 50px;
    height: 50px;
    background-color: #FF5500;
    color: white;
    font-size: 28px;
    border: none;
    border-radius: 5px;
    z-index: 9999999;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
    display: none;
`;
emergencyBtn.innerHTML = '☰';
document.body.appendChild(emergencyBtn);

// Create overlay
const overlay = document.createElement('div');
overlay.id = 'emergency-overlay';
overlay.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    display: none;
`;
document.body.appendChild(overlay);

// Toggle function
let isOpen = false;
function toggleEmergencyNavigation() {
    console.log('Emergency toggle clicked');

    // Find navigation element
    const sidebar = document.querySelector('#sidebar') ||
        document.querySelector('.navigation') ||
        document.querySelector('.navbar.side-bar');

    if (!sidebar) {
        console.error('No sidebar element found');
        return;
    }

    isOpen = !isOpen;

    if (isOpen) {
        // Copy the original sidebar and create a new one with forced styles
        const originalSidebar = sidebar;

        // Create a brand new forced sidebar
        const forcedNav = document.createElement('div');
        forcedNav.id = 'forced-navigation';
        forcedNav.innerHTML = originalSidebar.innerHTML;

        forcedNav.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background: #1c2536;
            color: white;
            z-index: 99999;
            overflow-y: auto;
            padding: 15px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.5);
        `;

        // Add force styles for all navigation elements
        const allElements = forcedNav.querySelectorAll('*');
        allElements.forEach(el => {
            // Skip invisible elements like script tags
            if (el.tagName === 'SCRIPT' || el.tagName === 'NOSCRIPT') return;

            // Force visibility and display properties
            el.style.visibility = 'visible';
            el.style.opacity = '1';

            // Style based on element type
            if (el.tagName === 'A' || el.classList.contains('nav-link')) {
                el.style.cssText += `
                    display: flex;
                    align-items: center;
                    padding: 10px;
                    color: rgba(255,255,255,0.8);
                    text-decoration: none;
                    margin-bottom: 5px;
                `;
            } else if (el.tagName === 'BUTTON' || el.classList.contains('accordion')) {
                el.style.cssText += `
                    display: flex;
                    align-items: center;
                    width: 100%;
                    text-align: left;
                    background: none;
                    border: none;
                    color: rgba(255,255,255,0.8);
                    padding: 10px;
                    cursor: pointer;
                `;
            } else if (el.classList.contains('submenu')) {
                el.style.cssText += `
                    display: none;
                    padding-left: 20px;
                    margin-top: 5px;
                    margin-bottom: 5px;
                `;
            }
        });

        // Add close button at the top
        const closeButton = document.createElement('button');
        closeButton.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
        `;
        closeButton.innerHTML = '✕';
        closeButton.addEventListener('click', toggleEmergencyNavigation);
        forcedNav.insertBefore(closeButton, forcedNav.firstChild);

        // Add accordion functionality
        const accordions = forcedNav.querySelectorAll('.accordion');
        accordions.forEach(acc => {
            acc.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const submenu = this.nextElementSibling;
                if (submenu && submenu.classList.contains('submenu')) {
                    const isVisible = submenu.style.display === 'block';
                    submenu.style.display = isVisible ? 'none' : 'block';
                }
            });
        });

        // Add the forced navigation to the body
        document.body.appendChild(forcedNav);
        overlay.style.display = 'block';
        emergencyBtn.innerHTML = '✕';
        emergencyBtn.style.backgroundColor = '#333333';
    } else {
        // Remove the forced navigation
        const forcedNav = document.getElementById('forced-navigation');
        if (forcedNav) {
            forcedNav.remove();
        }

        overlay.style.display = 'none';
        emergencyBtn.innerHTML = '☰';
        emergencyBtn.style.backgroundColor = '#FF5500';
    }
}

// Update toggle button visibility based on screen size
function updateToggleButtonVisibility() {
    if (isMobileView()) {
        emergencyBtn.style.display = 'flex';
    } else {
        emergencyBtn.style.display = 'none';
    }
}

// Add event listeners
emergencyBtn.addEventListener('click', toggleEmergencyNavigation);
overlay.addEventListener('click', toggleEmergencyNavigation);

// Call initially
updateToggleButtonVisibility();

// Update on resize
window.addEventListener('resize', updateToggleButtonVisibility);

// Handle loader events
if (typeof $ !== 'undefined') {
    $(document).ajaxStart(function() {
        // Keep correct visibility when AJAX starts
        setTimeout(updateToggleButtonVisibility, 100);
    });

    $(document).ajaxStop(function() {
        // Keep correct visibility when AJAX completes
        setTimeout(updateToggleButtonVisibility, 100);
    });
}

// Update visibility after page load
window.addEventListener('load', function() {
    updateToggleButtonVisibility();
});

// Additional check for after DOM content loaded
document.addEventListener('DOMContentLoaded', function() {
    updateToggleButtonVisibility();

    // Add additional check with delay to handle any post-load scripts
    setTimeout(updateToggleButtonVisibility, 500);
    setTimeout(updateToggleButtonVisibility, 1000);
    setTimeout(updateToggleButtonVisibility, 2000);
});
