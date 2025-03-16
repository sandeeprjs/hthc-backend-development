/******/ (() => { // webpackBootstrap
/*!************************************!*\
  !*** ./resources/js/navigation.js ***!
  \************************************/
/**
 * Ultimate Navigation Fix with Media Query Support
 */

// Function to check if we're on mobile
function isMobileView() {
  return window.innerWidth < 768;
}

// Create the emergency toggle button
var emergencyBtn = document.createElement('button');
emergencyBtn.id = 'emergency-toggle-btn';
emergencyBtn.style.cssText = "\n    position: fixed;\n    top: 15px;\n    left: 15px;\n    width: 50px;\n    height: 50px;\n    background-color: #FF5500;\n    color: white;\n    font-size: 28px;\n    border: none;\n    border-radius: 5px;\n    z-index: 9999999;\n    cursor: pointer;\n    align-items: center;\n    justify-content: center;\n    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);\n    display: none;\n";
emergencyBtn.innerHTML = '☰';
document.body.appendChild(emergencyBtn);

// Create overlay
var overlay = document.createElement('div');
overlay.id = 'emergency-overlay';
overlay.style.cssText = "\n    position: fixed;\n    top: 0;\n    left: 0;\n    width: 100%;\n    height: 100%;\n    background: rgba(0,0,0,0.5);\n    z-index: 9999;\n    display: none;\n";
document.body.appendChild(overlay);

// Toggle function
var isOpen = false;
function toggleEmergencyNavigation() {
  console.log('Emergency toggle clicked');

  // Find navigation element
  var sidebar = document.querySelector('#sidebar') || document.querySelector('.navigation') || document.querySelector('.navbar.side-bar');
  if (!sidebar) {
    console.error('No sidebar element found');
    return;
  }
  isOpen = !isOpen;
  if (isOpen) {
    // Copy the original sidebar and create a new one with forced styles
    var originalSidebar = sidebar;

    // Create a brand new forced sidebar
    var forcedNav = document.createElement('div');
    forcedNav.id = 'forced-navigation';
    forcedNav.innerHTML = originalSidebar.innerHTML;
    forcedNav.style.cssText = "\n            position: fixed;\n            top: 0;\n            left: 0;\n            width: 240px;\n            height: 100vh;\n            background: #1c2536;\n            color: white;\n            z-index: 99999;\n            overflow-y: auto;\n            padding: 15px;\n            box-shadow: 2px 0 10px rgba(0,0,0,0.5);\n        ";

    // Add force styles for all navigation elements
    var allElements = forcedNav.querySelectorAll('*');
    allElements.forEach(function (el) {
      // Skip invisible elements like script tags
      if (el.tagName === 'SCRIPT' || el.tagName === 'NOSCRIPT') return;

      // Force visibility and display properties
      el.style.visibility = 'visible';
      el.style.opacity = '1';

      // Style based on element type
      if (el.tagName === 'A' || el.classList.contains('nav-link')) {
        el.style.cssText += "\n                    display: flex;\n                    align-items: center;\n                    padding: 10px;\n                    color: rgba(255,255,255,0.8);\n                    text-decoration: none;\n                    margin-bottom: 5px;\n                ";
      } else if (el.tagName === 'BUTTON' || el.classList.contains('accordion')) {
        el.style.cssText += "\n                    display: flex;\n                    align-items: center;\n                    width: 100%;\n                    text-align: left;\n                    background: none;\n                    border: none;\n                    color: rgba(255,255,255,0.8);\n                    padding: 10px;\n                    cursor: pointer;\n                ";
      } else if (el.classList.contains('submenu')) {
        el.style.cssText += "\n                    display: none;\n                    padding-left: 20px;\n                    margin-top: 5px;\n                    margin-bottom: 5px;\n                ";
      }
    });

    // Add close button at the top
    var closeButton = document.createElement('button');
    closeButton.style.cssText = "\n            position: absolute;\n            top: 10px;\n            right: 10px;\n            background: none;\n            border: none;\n            color: white;\n            font-size: 20px;\n            cursor: pointer;\n        ";
    closeButton.innerHTML = '✕';
    closeButton.addEventListener('click', toggleEmergencyNavigation);
    forcedNav.insertBefore(closeButton, forcedNav.firstChild);

    // Add accordion functionality
    var accordions = forcedNav.querySelectorAll('.accordion');
    accordions.forEach(function (acc) {
      acc.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var submenu = this.nextElementSibling;
        if (submenu && submenu.classList.contains('submenu')) {
          var isVisible = submenu.style.display === 'block';
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
    var _forcedNav = document.getElementById('forced-navigation');
    if (_forcedNav) {
      _forcedNav.remove();
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
  $(document).ajaxStart(function () {
    // Keep correct visibility when AJAX starts
    setTimeout(updateToggleButtonVisibility, 100);
  });
  $(document).ajaxStop(function () {
    // Keep correct visibility when AJAX completes
    setTimeout(updateToggleButtonVisibility, 100);
  });
}

// Update visibility after page load
window.addEventListener('load', function () {
  updateToggleButtonVisibility();
});

// Additional check for after DOM content loaded
document.addEventListener('DOMContentLoaded', function () {
  updateToggleButtonVisibility();

  // Add additional check with delay to handle any post-load scripts
  setTimeout(updateToggleButtonVisibility, 500);
  setTimeout(updateToggleButtonVisibility, 1000);
  setTimeout(updateToggleButtonVisibility, 2000);
});
/******/ })()
;
//# sourceMappingURL=navigation.js.map