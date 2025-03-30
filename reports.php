<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_customization";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get date range for reports (default to current month)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Get sales report data - Modified to calculate total amount
$sales_report = $conn->query("
    SELECT 
        DATE(o.created_at) as order_date,
        COUNT(*) as total_orders,
        SUM(p.price * o.quantity) as total_sales
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
    GROUP BY DATE(o.created_at)
    ORDER BY order_date
");

// Get product sales data - Modified to calculate total amount
$product_report = $conn->query("
    SELECT 
        p.name as product_name,
        COUNT(*) as total_orders,
        SUM(p.price * o.quantity) as total_sales
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
    GROUP BY p.name
    ORDER BY total_sales DESC
    LIMIT 10
");

// Get customer orders data - Modified to calculate total amount
$customer_report = $conn->query("
    SELECT 
        u.username,
        COUNT(*) as total_orders,
        SUM(p.price * o.quantity) as total_spent
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN products p ON o.product_id = p.id
    WHERE o.created_at BETWEEN '$start_date' AND '$end_date 23:59:59'
    GROUP BY u.username
    ORDER BY total_spent DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Car Customization</title>
    <style>
        /* Your existing CSS styles remain the same */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-image: url("Images/doddles%20of%20car%20in%20whole%20page%20in%20pink%20and%20red%20color%20for%20website%20background.jpg");
            background-size: cover;
            color: #333;
            min-height: 100vh;
        }
        
        /* ... rest of your CSS styles ... */
    </style>
    <!-- Chart.js for visual reports -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <div class="admin-profile">
            <img src="Images/Devansh%203dxx.jpg" alt="Admin Photo">
            <p>Admin Panel</p>
        </div>
        <h2>Navigation</h2>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_users.php">Users</a></li>
            <li><a href="manage_categories.php">Categories</a></li>
            <li><a href="manage_products.php">Products</a></li>
            <li><a href="manage_orders.php">Orders</a></li>
            <li><a href="manage_payments.php">Payments</a></li>
            <li><a href="reports.php" class="active">Reports</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="logo">
            <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Car Customization Logo">
        </div>
        
        <div class="reports-container">
            <h1>Sales Reports</h1>
            
            <form method="GET" action="reports.php" class="date-filter">
                <div>
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="<?= $start_date ?>">
                </div>
                <div>
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" value="<?= $end_date ?>">
                </div>
                <button type="submit">Filter</button>
                <a href="reports.php" class="btn btn-primary">Reset</a>
            </form>
            
            <div class="report-section">
                <h2>Daily Sales Report (<?= date('M j, Y', strtotime($start_date)) ?> to <?= date('M j, Y', strtotime($end_date)) ?>)</h2>
                
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
                
                <?php if ($sales_report->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Orders</th>
                                <th>Total Sales</th>
                                <th>Average Order Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_sales = 0;
                            $total_orders = 0;
                            $sales_data = [];
                            while ($row = $sales_report->fetch_assoc()): 
                                $total_sales += $row['total_sales'];
                                $total_orders += $row['total_orders'];
                                $sales_data[] = $row;
                            ?>
                            <tr>
                                <td><?= date('M j, Y', strtotime($row['order_date'])) ?></td>
                                <td><?= $row['total_orders'] ?></td>
                                <td>$<?= number_format($row['total_sales'], 2) ?></td>
                                <td>$<?= number_format($row['total_sales'] / $row['total_orders'], 2) ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr style="font-weight: bold; background-color: #fff5f5;">
                                <td>Total</td>
                                <td><?= $total_orders ?></td>
                                <td>$<?= number_format($total_sales, 2) ?></td>
                                <td>$<?= number_format($total_sales / $total_orders, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="export_report.php?type=sales&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-export">Export Sales Report</a>
                <?php else: ?>
                    <div class="no-data">
                        <p>No sales data found for the selected period</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="report-section">
                <h2>Top Selling Products</h2>
                
                <div class="chart-container">
                    <canvas id="productsChart"></canvas>
                </div>
                
                <?php if ($product_report->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Orders</th>
                                <th>Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $product_data = [];
                            while ($row = $product_report->fetch_assoc()): 
                                $product_data[] = $row;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= $row['total_orders'] ?></td>
                                <td>$<?= number_format($row['total_sales'], 2) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <a href="export_report.php?type=products&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-export">Export Products Report</a>
                <?php else: ?>
                    <div class="no-data">
                        <p>No product sales data found for the selected period</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="report-section">
                <h2>Top Customers</h2>
                
                <div class="chart-container">
                    <canvas id="customersChart"></canvas>
                </div>
                
                <?php if ($customer_report->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Orders</th>
                                <th>Total Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $customer_data = [];
                            while ($row = $customer_report->fetch_assoc()): 
                                $customer_data[] = $row;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= $row['total_orders'] ?></td>
                                <td>$<?= number_format($row['total_spent'], 2) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <a href="export_report.php?type=customers&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-export">Export Customers Report</a>
                <?php else: ?>
                    <div class="no-data">
                        <p>No customer data found for the selected period</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: [<?php foreach($sales_data as $row): ?>'<?= date('M j', strtotime($row['order_date'])) ?>',<?php endforeach; ?>],
                datasets: [{
                    label: 'Daily Sales',
                    data: [<?php foreach($sales_data as $row): ?><?= $row['total_sales'] ?>,<?php endforeach; ?>],
                    backgroundColor: 'rgba(255, 138, 101, 0.2)',
                    borderColor: 'rgba(255, 138, 101, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Daily Sales Trend'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount ($)'
                        }
                    }
                }
            }
        });

        // Products Chart
        const productsCtx = document.getElementById('productsChart').getContext('2d');
        const productsChart = new Chart(productsCtx, {
            type: 'bar',
            data: {
                labels: [<?php foreach($product_data as $row): ?>'<?= $row['product_name'] ?>',<?php endforeach; ?>],
                datasets: [{
                    label: 'Sales Amount',
                    data: [<?php foreach($product_data as $row): ?><?= $row['total_sales'] ?>,<?php endforeach; ?>],
                    backgroundColor: 'rgba(216, 27, 96, 0.6)',
                    borderColor: 'rgba(216, 27, 96, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Top Selling Products'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount ($)'
                        }
                    }
                }
            }
        });

        // Customers Chart
        const customersCtx = document.getElementById('customersChart').getContext('2d');
        const customersChart = new Chart(customersCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php foreach($customer_data as $row): ?>'<?= $row['username'] ?>',<?php endforeach; ?>],
                datasets: [{
                    label: 'Amount Spent',
                    data: [<?php foreach($customer_data as $row): ?><?= $row['total_spent'] ?>,<?php endforeach; ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Top Customers by Spending'
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>