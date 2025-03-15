// Import core dependencies
require('./bootstrap');

// Import jQuery
window.$ = window.jQuery = require('jquery');

// Import Bootstrap
require('bootstrap');

// Import Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Import other plugins
import flatpickr from 'flatpickr';
window.flatpickr = flatpickr;

// Import SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Import Chart.js (fixed for v3+ or v4+)
import Chart from 'chart.js/auto'; // Correct import for Chart.js v3+ or v4+
window.Chart = Chart;

// Global chart initialization helper
window.initializeChart = function(elementId, type, chartData, options = {}) {
    const ctx = document.getElementById(elementId);
    if (!ctx) {
        console.error(`Canvas element with id ${elementId} not found`);
        return null;
    }

    // Default options with modern Chart.js v3+ configuration
    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                beginAtZero: true,
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    display: true
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        },
        ...options
    };

    try {
        return new Chart(ctx, {
            type: type,
            data: chartData,
            options: defaultOptions
        });
    } catch (error) {
        console.error(`Chart initialization error for ${elementId}:`, error);
        return null;
    }
};

// Optional: Logging to help with debugging
console.log('App.js loaded successfully');
