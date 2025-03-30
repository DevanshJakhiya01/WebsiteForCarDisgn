<script>
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Common chart configuration
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        color: '#333'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFont: {
                        size: 16,
                        weight: 'bold',
                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                    },
                    bodyFont: {
                        size: 14,
                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                    },
                    padding: 12,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== undefined) {
                                label += '$' + context.parsed.y.toLocaleString();
                            } else {
                                label += '$' + context.raw.toLocaleString();
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
                        font: {
                            size: 12,
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        color: '#555'
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        },
                        font: {
                            size: 12,
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        color: '#555'
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        };

        // Helper function to safely render charts
        function renderChart(chartId, config) {
            const ctx = document.getElementById(chartId);
            if (!ctx) return null;
            
            try {
                return new Chart(ctx.getContext('2d'), config);
            } catch (error) {
                console.error(`Error rendering ${chartId}:`, error);
                return null;
            }
        }

        // Sales Chart - Line Chart
        <?php if (!empty($sales_data)): ?>
        renderChart('salesChart', {
            type: 'line',
            data: {
                labels: [<?php foreach($sales_data as $row): ?>'<?= date('M j', strtotime($row['order_date'])) ?>',<?php endforeach; ?>],
                datasets: [{
                    label: 'Daily Sales ($)',
                    data: [<?php foreach($sales_data as $row): ?><?= $row['total_sales'] ?>,<?php endforeach; ?>],
                    backgroundColor: 'rgba(255, 138, 101, 0.2)',
                    borderColor: 'rgba(255, 138, 101, 1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgba(255, 138, 101, 1)',
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    pointBorderColor: '#fff'
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    ...chartOptions.plugins,
                    title: {
                        display: true,
                        text: 'Daily Sales Trend',
                        font: {
                            size: 18,
                            weight: 'bold',
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        color: '#d81b60'
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    line: {
                        cubicInterpolationMode: 'monotone'
                    }
                }
            }
        });
        <?php endif; ?>

        // Products Chart - Horizontal Bar Chart
        <?php if (!empty($product_data)): ?>
        renderChart('productsChart', {
            type: 'bar',
            data: {
                labels: [<?php foreach($product_data as $row): ?>'<?= addslashes($row['product_name']) ?>',<?php endforeach; ?>],
                datasets: [{
                    label: 'Sales Amount ($)',
                    data: [<?php foreach($product_data as $row): ?><?= $row['total_sales'] ?>,<?php endforeach; ?>],
                    backgroundColor: [
                        'rgba(216, 27, 96, 0.7)',
                        'rgba(244, 81, 30, 0.7)',
                        'rgba(253, 216, 53, 0.7)',
                        'rgba(76, 175, 80, 0.7)',
                        'rgba(63, 81, 181, 0.7)',
                        'rgba(233, 30, 99, 0.7)',
                        'rgba(156, 39, 176, 0.7)',
                        'rgba(0, 150, 136, 0.7)',
                        'rgba(255, 152, 0, 0.7)',
                        'rgba(96, 125, 139, 0.7)'
                    ],
                    borderColor: [
                        'rgba(216, 27, 96, 1)',
                        'rgba(244, 81, 30, 1)',
                        'rgba(253, 216, 53, 1)',
                        'rgba(76, 175, 80, 1)',
                        'rgba(63, 81, 181, 1)',
                        'rgba(233, 30, 99, 1)',
                        'rgba(156, 39, 176, 1)',
                        'rgba(0, 150, 136, 1)',
                        'rgba(255, 152, 0, 1)',
                        'rgba(96, 125, 139, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 4,
                    hoverBorderWidth: 2
                }]
            },
            options: {
                ...chartOptions,
                indexAxis: 'y',
                plugins: {
                    ...chartOptions.plugins,
                    title: {
                        display: true,
                        text: 'Top Selling Products',
                        font: {
                            size: 18,
                            weight: 'bold',
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        color: '#d81b60'
                    }
                },
                scales: {
                    ...chartOptions.scales,
                    x: {
                        ...chartOptions.scales.x,
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                            drawBorder: false
                        }
                    },
                    y: {
                        ...chartOptions.scales.y,
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // Customers Chart - Doughnut Chart
        <?php if (!empty($customer_data)): ?>
        renderChart('customersChart', {
            type: 'doughnut',
            data: {
                labels: [<?php foreach($customer_data as $row): ?>'<?= addslashes($row['username']) ?>',<?php endforeach; ?>],
                datasets: [{
                    label: 'Amount Spent ($)',
                    data: [<?php foreach($customer_data as $row): ?><?= $row['total_spent'] ?>,<?php endforeach; ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)',
                        'rgba(83, 102, 255, 0.7)',
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(108, 117, 125, 0.7)'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverOffset: 15,
                    hoverBorderWidth: 3
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    ...chartOptions.plugins,
                    title: {
                        display: true,
                        text: 'Top Customers by Spending',
                        font: {
                            size: 18,
                            weight: 'bold',
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        color: '#d81b60'
                    },
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 20,
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        ...chartOptions.plugins.tooltip,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%',
                circumference: 180,
                rotation: -90
            }
        });
        <?php endif; ?>
    });
</script>