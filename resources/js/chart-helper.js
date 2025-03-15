// Chart.js Initialization Helper
import Chart from 'chart.js/auto';

// Ensure Chart is globally available
window.Chart = Chart;

// Generic Chart Initialization Function
function initializeChart(elementId, type, data, options = {}) {
    const ctx = document.getElementById(elementId);
    if (!ctx) {
        console.error(`Canvas element with id ${elementId} not found`);
        return null;
    }

    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        ...options
    };

    return new Chart(ctx, {
        type: type,
        data: data,
        options: defaultOptions
    });
}

// Expose the function globally
window.initializeChart = initializeChart;
