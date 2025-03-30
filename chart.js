<script>
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
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.7)',
                titleFont: {
                    size: 16,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 14
                },
                padding: 12,
                usePointStyle: true,
                callbacks: {
                    label: function(context) {
                        return '$' + context.raw.toLocaleString();
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
                        size: 12
                    }
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    },
                    font: {
                        size: 12
                    }
                },
                grid: {
                    color: 'rgba(0,0,0,0.05)'
                }
            }
        }
    };

    // Sales Chart - Line Chart
    if (document.getElementById('salesChart')) {
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: [<?php foreach($sales_data as $row): ?>'<?= date('M j', strtotime($row['order_date'])) ?>',<?php endforeach; ?>],
                datasets: [{
                    label: 'Daily Sales ($)',
                    data: [<?php foreach($sales_data as $row): ?><?= $row['total_sales'] ?>,<?php endforeach; ?>],
                    backgroundColor: 'rgba(255, 138, 101, 0.2)',
                    borderColor: 'rgba(255, 138, 101, 1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgba(255, 138, 101, 1)',
                    pointRadius: 5,
                    pointHoverRadius: 7
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
                            weight: 'bold'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Products Chart - Bar Chart
    if (document.getElementById('productsChart')) {
        const productsCtx = document.getElementById('productsChart').getContext('2d');
        const productsChart = new Chart(productsCtx, {
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
                    borderWidth: 1
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    ...chartOptions.plugins,
                    title: {
                        display: true,
                        text: 'Top Selling Products',
                        font: {
                            size: 18,
                            weight: 'bold'
                        }
                    }
                },
                indexAxis: 'y' // Makes the bar chart horizontal
            }
        });
    }

    // Customers Chart - Doughnut Chart
    if (document.getElementById('customersChart')) {
        const customersCtx = document.getElementById('customersChart').getContext('2d');
        const customersChart = new Chart(customersCtx, {
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
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)'
                    ],
                    borderWidth: 1,
                    hoverOffset: 20
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
                            weight: 'bold'
                        }
                    },
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 20,
                            padding: 20
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
</script>