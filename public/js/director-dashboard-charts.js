/**
 * Director Media Development and Governance Dashboard Charts - Chart.js Initialization Functions
 * 
 * This file provides reusable chart initialization functions for the Director Media Development and Governance Dashboard.
 * All charts use a consistent color scheme and responsive configuration.
 * 
 * Color Scheme:
 * - Primary Blue: #3b82f6
 * - Yellow: #facc15 / #eab308
 * - Green: #10b981
 * - Orange: #f59e0b
 * - Red: #ef4444
 * - Purple: #8b5cf6
 * - Cyan: #06b6d4
 * - Pink: #ec4899
 * - Gray: #9ca3af
 * - Black: #000000
 */

// Color palette for consistent styling
const CHART_COLORS = {
    primary: '#3b82f6',
    yellow: '#facc15',
    yellowDark: '#eab308',
    green: '#10b981',
    orange: '#f59e0b',
    red: '#ef4444',
    purple: '#8b5cf6',
    cyan: '#06b6d4',
    pink: '#ec4899',
    gray: '#9ca3af',
    black: '#000000'
};

// Multi-color palette for pie/doughnut charts
const MULTI_COLORS = [
    CHART_COLORS.primary,
    CHART_COLORS.yellow,
    CHART_COLORS.green,
    CHART_COLORS.orange,
    CHART_COLORS.purple,
    CHART_COLORS.cyan,
    CHART_COLORS.pink
];

// Default chart options
const DEFAULT_OPTIONS = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top'
        }
    }
};

/**
 * Initialize Monthly Trends Line Chart
 * 
 * @param {string} canvasId - Canvas element ID
 * @param {Array} labels - Month labels (e.g., ['Jan', 'Feb', 'Mar'])
 * @param {Array} datasets - Array of dataset objects with label, data, and optional color
 * @param {Object} options - Additional Chart.js options
 * @returns {Chart} Chart.js instance
 */
function initMonthlyTrendsChart(canvasId, labels, datasets, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error(`Canvas element with ID "${canvasId}" not found`);
        return null;
    }

    // Process datasets to add default colors and styling
    const processedDatasets = datasets.map((dataset, index) => {
        const colors = [
            CHART_COLORS.primary,
            CHART_COLORS.yellow,
            CHART_COLORS.orange,
            CHART_COLORS.purple
        ];
        const color = dataset.borderColor || colors[index % colors.length];
        
        return {
            label: dataset.label,
            data: dataset.data,
            borderColor: color,
            backgroundColor: dataset.backgroundColor || color.replace(')', ', 0.1)').replace('rgb', 'rgba').replace('#', 'rgba('),
            borderWidth: 2,
            tension: 0.3,
            fill: dataset.fill !== undefined ? dataset.fill : true,
            pointRadius: 3,
            pointHoverRadius: 5
        };
    });

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: processedDatasets
        },
        options: {
            ...DEFAULT_OPTIONS,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            ...options
        }
    });
}

/**
 * Initialize Revenue Breakdown Pie Chart
 * 
 * @param {string} canvasId - Canvas element ID
 * @param {Array} labels - Category labels
 * @param {Array} data - Data values
 * @param {Array} colors - Optional custom colors (defaults to MULTI_COLORS)
 * @param {Object} options - Additional Chart.js options
 * @returns {Chart} Chart.js instance
 */
function initRevenueBreakdownChart(canvasId, labels, data, colors = null, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error(`Canvas element with ID "${canvasId}" not found`);
        return null;
    }

    const backgroundColor = colors || MULTI_COLORS.slice(0, data.length);

    return new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: backgroundColor,
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            ...DEFAULT_OPTIONS,
            plugins: {
                ...DEFAULT_OPTIONS.plugins,
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            },
            ...options
        }
    });
}

/**
 * Initialize Processing Time Bar Chart
 * 
 * @param {string} canvasId - Canvas element ID
 * @param {Array} labels - Category labels
 * @param {Array} data - Data values
 * @param {string} color - Bar color (defaults to primary blue)
 * @param {Object} options - Additional Chart.js options
 * @returns {Chart} Chart.js instance
 */
function initProcessingTimeChart(canvasId, labels, data, color = null, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error(`Canvas element with ID "${canvasId}" not found`);
        return null;
    }

    const backgroundColor = color || CHART_COLORS.primary;

    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Processing Time (Days)',
                data: data,
                backgroundColor: backgroundColor,
                borderWidth: 0,
                borderRadius: 4
            }]
        },
        options: {
            ...DEFAULT_OPTIONS,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + ' days';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                ...DEFAULT_OPTIONS.plugins,
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.y} days`;
                        }
                    }
                }
            },
            ...options
        }
    });
}

/**
 * Initialize Category Distribution Doughnut Chart
 * 
 * @param {string} canvasId - Canvas element ID
 * @param {Array} labels - Category labels
 * @param {Array} data - Data values
 * @param {Array} colors - Optional custom colors (defaults to MULTI_COLORS)
 * @param {Object} options - Additional Chart.js options
 * @returns {Chart} Chart.js instance
 */
function initCategoryDistributionChart(canvasId, labels, data, colors = null, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error(`Canvas element with ID "${canvasId}" not found`);
        return null;
    }

    const backgroundColor = colors || MULTI_COLORS.slice(0, data.length);

    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: backgroundColor,
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            ...DEFAULT_OPTIONS,
            cutout: '60%',
            plugins: {
                ...DEFAULT_OPTIONS.plugins,
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            ...options
        }
    });
}

/**
 * Initialize Approval Ratio Stacked Bar Chart
 * 
 * @param {string} canvasId - Canvas element ID
 * @param {Array} labels - Category labels
 * @param {Array} approvedData - Approved counts
 * @param {Array} rejectedData - Rejected counts
 * @param {Object} options - Additional Chart.js options
 * @returns {Chart} Chart.js instance
 */
function initApprovalRatioChart(canvasId, labels, approvedData, rejectedData, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error(`Canvas element with ID "${canvasId}" not found`);
        return null;
    }

    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Approved',
                    data: approvedData,
                    backgroundColor: CHART_COLORS.green,
                    borderWidth: 0,
                    borderRadius: 4
                },
                {
                    label: 'Rejected',
                    data: rejectedData,
                    backgroundColor: CHART_COLORS.red,
                    borderWidth: 0,
                    borderRadius: 4
                }
            ]
        },
        options: {
            ...DEFAULT_OPTIONS,
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: true,
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    stacked: true,
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                ...DEFAULT_OPTIONS.plugins,
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y || 0;
                            return `${label}: ${value}`;
                        },
                        footer: function(tooltipItems) {
                            const total = tooltipItems.reduce((sum, item) => sum + item.parsed.y, 0);
                            return `Total: ${total}`;
                        }
                    }
                }
            },
            ...options
        }
    });
}

/**
 * Initialize Generic Bar Chart
 * 
 * @param {string} canvasId - Canvas element ID
 * @param {Array} labels - Category labels
 * @param {string} datasetLabel - Dataset label
 * @param {Array} data - Data values
 * @param {string} color - Bar color
 * @param {Object} options - Additional Chart.js options
 * @returns {Chart} Chart.js instance
 */
function initBarChart(canvasId, labels, datasetLabel, data, color = null, options = {}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error(`Canvas element with ID "${canvasId}" not found`);
        return null;
    }

    const backgroundColor = color || CHART_COLORS.primary;

    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: datasetLabel,
                data: data,
                backgroundColor: backgroundColor,
                borderWidth: 0,
                borderRadius: 4
            }]
        },
        options: {
            ...DEFAULT_OPTIONS,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            ...options
        }
    });
}

/**
 * Destroy chart instance if it exists
 * Useful for updating charts dynamically
 * 
 * @param {Chart} chartInstance - Chart.js instance to destroy
 */
function destroyChart(chartInstance) {
    if (chartInstance && typeof chartInstance.destroy === 'function') {
        chartInstance.destroy();
    }
}

/**
 * Helper function to convert hex color to rgba
 * 
 * @param {string} hex - Hex color code
 * @param {number} alpha - Alpha value (0-1)
 * @returns {string} RGBA color string
 */
function hexToRgba(hex, alpha = 1) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initMonthlyTrendsChart,
        initRevenueBreakdownChart,
        initProcessingTimeChart,
        initCategoryDistributionChart,
        initApprovalRatioChart,
        initBarChart,
        destroyChart,
        hexToRgba,
        CHART_COLORS,
        MULTI_COLORS
    };
}
