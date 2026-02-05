document.addEventListener('DOMContentLoaded', function () {
    // Check if data exists
    if (typeof window.dashboardData === 'undefined') {
        console.error('Dashboard data not found');
        return;
    }

    const ctx = document.getElementById('spendingChart').getContext('2d');

    // Create Gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    // rgba(59, 130, 246, 0.4) -> blue-500 with opacity
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

    const labels = window.dashboardData.labels;
    const values = window.dashboardData.values;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Order Total',
                data: values,
                borderColor: '#3b82f6', // blue-500
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4, // Smooth curves
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#3b82f6',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // We use our custom header legend
                },
                tooltip: {
                    backgroundColor: '#1f2937', // gray-800
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 10,
                        color: '#9ca3af' // gray-400
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6', // gray-100
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af', // gray-400
                        callback: function (value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
});
