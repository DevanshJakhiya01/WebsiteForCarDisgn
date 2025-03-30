<?php
session_start();
require_once 'db_connection.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$user_query = $conn->prepare("SELECT name, email, profile_image, role FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user = $user_query->get_result()->fetch_assoc();

// Report types for filter
$report_types = ['Sales', 'Inventory', 'Customers', 'Customizations', 'Financial'];

// Get filter parameters
$type_filter = $_GET['type'] ?? '';
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Base query
$query = "SELECT r.*, u.name as generated_by FROM reports r 
          JOIN users u ON r.user_id = u.id 
          WHERE r.generated_date BETWEEN ? AND ?";
$params = [$date_from, $date_to];
$types = "ss";

// Apply type filter
if (!empty($type_filter) && in_array($type_filter, $report_types)) {
    $query .= " AND r.report_type = ?";
    $params[] = $type_filter;
    $types .= "s";
}

// Add sorting
$sort = $_GET['sort'] ?? 'generated_date';
$order = $_GET['order'] ?? 'DESC';
$query .= " ORDER BY $sort $order";

// Prepare and execute
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$reports = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get stats for cards
$stats = [
    'total' => count($reports),
    'pending' => 0,
    'completed' => 0,
    'last_7_days' => 0
];

foreach ($reports as $report) {
    if ($report['status'] == 'pending') $stats['pending']++;
    if ($report['status'] == 'completed') $stats['completed']++;
    if (strtotime($report['generated_date']) >= strtotime('-7 days')) $stats['last_7_days']++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Car Customization System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6c63ff;
            --secondary-color: #4d44db;
            --accent-color: #ff6584;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            color: #333;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #2c3e50 100%);
            color: white;
            height: 100vh;
            position: fixed;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
            margin: 5px 0;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card .value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .report-table th {
            background-color: var(--primary-color);
            color: white;
        }
        
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-completed {
            background-color: #28a745;
            color: white;
        }
        
        .filter-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" style="width: 250px;">
            <div class="text-center mb-4">
                <img src="images/logo-white.png" alt="Logo" style="width: 80%; max-width: 180px;">
            </div>
            
            <div class="text-center mb-4">
                <img src="<?= htmlspecialchars($user['profile_image'] ?? 'images/default-avatar.jpg') ?>" 
                     class="img-fluid rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                <h6 class="mb-1"><?= htmlspecialchars($user['name']) ?></h6>
                <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php">
                        <i class="fas fa-car"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="customers.php">
                        <i class="fas fa-users"></i> Customers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="reports.php">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Reports Dashboard</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                    <i class="fas fa-plus me-2"></i>Generate Report
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h5 class="card-title text-muted">Total Reports</h5>
                            <div class="value"><?= $stats['total'] ?></div>
                            <p class="text-muted mb-0">All generated reports</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h5 class="card-title text-muted">Pending</h5>
                            <div class="value"><?= $stats['pending'] ?></div>
                            <p class="text-muted mb-0">In progress</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h5 class="card-title text-muted">Completed</h5>
                            <div class="value"><?= $stats['completed'] ?></div>
                            <p class="text-muted mb-0">Ready for download</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <h5 class="card-title text-muted">Recent</h5>
                            <div class="value"><?= $stats['last_7_days'] ?></div>
                            <p class="text-muted mb-0">Last 7 days</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section mb-4">
                <form method="get" class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Report Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">All Types</option>
                            <?php foreach ($report_types as $type): ?>
                                <option value="<?= $type ?>" <?= $type_filter == $type ? 'selected' : '' ?>><?= $type ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $date_from ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $date_to ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="reports.php" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Reports Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover report-table">
                            <thead>
                                <tr>
                                    <th>Report ID</th>
                                    <th>Report Name</th>
                                    <th>Type</th>
                                    <th>Generated By</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reports)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No reports found. Generate a new report to get started.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reports as $report): ?>
                                        <tr>
                                            <td>#<?= $report['report_id'] ?></td>
                                            <td><?= htmlspecialchars($report['report_name']) ?></td>
                                            <td><?= htmlspecialchars($report['report_type']) ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="images/default-avatar.jpg" class="user-avatar me-2">
                                                    <?= htmlspecialchars($report['generated_by']) ?>
                                                </div>
                                            </td>
                                            <td><?= date('M d, Y h:i A', strtotime($report['generated_date'])) ?></td>
                                            <td>
                                                <span class="badge rounded-pill <?= $report['status'] == 'completed' ? 'badge-completed' : 'badge-pending' ?>">
                                                    <?= ucfirst($report['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($report['status'] == 'completed'): ?>
                                                    <a href="download_report.php?id=<?= $report['report_id'] ?>" class="btn btn-sm btn-success me-1">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-info me-1" data-bs-toggle="modal" data-bs-target="#viewReportModal" 
                                                        data-id="<?= $report['report_id'] ?>" data-name="<?= htmlspecialchars($report['report_name']) ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteReportModal" 
                                                        data-id="<?= $report['report_id'] ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Report Modal -->
    <div class="modal fade" id="generateReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate New Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="generate_report.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reportName" class="form-label">Report Name</label>
                            <input type="text" class="form-control" id="reportName" name="report_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="reportType" class="form-label">Report Type</label>
                            <select class="form-select" id="reportType" name="report_type" required>
                                <option value="">Select a report type</option>
                                <?php foreach ($report_types as $type): ?>
                                    <option value="<?= $type ?>"><?= $type ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reportPeriod" class="form-label">Time Period</label>
                            <select class="form-select" id="reportPeriod" name="report_period">
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month" selected>This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="row g-2 mb-3" id="customDateRange" style="display: none;">
                            <div class="col-md-6">
                                <label for="customFrom" class="form-label">From</label>
                                <input type="date" class="form-control" id="customFrom" name="custom_from">
                            </div>
                            <div class="col-md-6">
                                <label for="customTo" class="form-label">To</label>
                                <input type="date" class="form-control" id="customTo" name="custom_to">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Report Modal -->
    <div class="modal fade" id="viewReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Report Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-4" id="reportLoading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading report details...</p>
                    </div>
                    <div id="reportDetails" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" class="btn btn-primary" id="downloadReportBtn">Download</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Report Modal -->
    <div class="modal fade" id="deleteReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this report? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="delete_report.php" method="post" style="display: inline;">
                        <input type="hidden" name="report_id" id="deleteReportId">
                        <button type="submit" class="btn btn-danger">Delete Report</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle custom date range
        document.getElementById('reportPeriod').addEventListener('change', function() {
            const customRange = document.getElementById('customDateRange');
            customRange.style.display = this.value === 'custom' ? 'flex' : 'none';
        });

        // View Report Modal
        const viewReportModal = document.getElementById('viewReportModal');
        viewReportModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const reportId = button.getAttribute('data-id');
            const reportName = button.getAttribute('data-name');
            
            document.querySelector('#viewReportModal .modal-title').textContent = reportName;
            document.getElementById('downloadReportBtn').href = `download_report.php?id=${reportId}`;
            
            // Simulate loading report details (in a real app, you'd fetch from server)
            setTimeout(() => {
                document.getElementById('reportLoading').style.display = 'none';
                document.getElementById('reportDetails').style.display = 'block';
                document.getElementById('reportDetails').innerHTML = `
                    <h6>Report Summary</h6>
                    <p>This would display detailed information about the report with ID ${reportId}.</p>
                    <div class="alert alert-info">
                        In a real implementation, this would show the actual report content, statistics, and visualizations.
                    </div>
                `;
            }, 1500);
        });

        // Reset view modal when closed
        viewReportModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('reportLoading').style.display = 'block';
            document.getElementById('reportDetails').style.display = 'none';
            document.getElementById('reportDetails').innerHTML = '';
        });

        // Delete Report Modal
        const deleteReportModal = document.getElementById('deleteReportModal');
        deleteReportModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const reportId = button.getAttribute('data-id');
            document.getElementById('deleteReportId').value = reportId;
        });
    </script>
</body>
</html>