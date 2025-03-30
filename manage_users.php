<?php
// Start the session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
require_once "database.php";

// Fetch all users
$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM users WHERE id = $delete_id";
    if (mysqli_query($conn, $delete_sql)) {
        $success_message = "User deleted successfully!";
        // Refresh the page to show updated list
        header("Location: manage_users.php?success=" . urlencode($success_message));
        exit();
    } else {
        $error_message = "Error deleting user: " . mysqli_error($conn);
    }
}

// Display success message if exists
if (isset($_GET['success'])) {
    $success_message = $_GET['success'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-image: url("Images/doddles%20of%20car%20in%20whole%20page%20in%20pink%20and%20red%20color%20for%20website%20background.jpg");
            background-size: cover;
            color: red;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: rgba(51, 51, 51, 0.9);
            color: white;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }
        
        .admin-profile {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #444;
        }
        
        .admin-profile img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #ff4081;
            margin-bottom: 10px;
            object-fit: cover;
        }
        
        .admin-profile p {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
            color: #ff4081;
        }
        
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ff4081;
        }
        
        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar ul li {
            margin: 15px 0;
        }
        
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .sidebar ul li a:hover {
            background-color: #ff4081;
            color: white;
            transform: translateX(5px);
        }
        
        .main-content {
            flex-grow: 1;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            overflow-y: auto;
        }
        
        .logo {
            width: 300px;
            margin-bottom: 20px;
        }
        
        .logo img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .dashboard-container {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        
        h1 {
            text-align: center;
            color: #d81b60;
            margin-bottom: 20px;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            border-top: 4px solid darksalmon;
        }
        
        .stat-card h3 {
            margin-top: 0;
            color: #333;
        }
        
        .stat-card .value {
            font-size: 36px;
            font-weight: bold;
            color: #d81b60;
            margin: 10px 0;
        }
        
        .recent-orders {
            margin-top: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: darksalmon;
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #ffe6ee;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: darksalmon;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #e9967a;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 15px;
            }
            
            .main-content {
                padding: 20px;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="admin-profile">
            <img src="Images/admin-avatar.jpg" alt="Admin Avatar">
            <p><?php echo $_SESSION['admin_name']; ?></p>
            <p>Administrator</p>
        </div>
        
        <h2>Dashboard</h2>
        <ul>
            <li><a href="admin_dashboard.php">Overview</a></li>
            <li><a href="manage_products.php">Manage Products</a></li>
            <li><a href="manage_orders.php">Manage Orders</a></li>
            <li><a href="manage_users.php" style="background-color: #ff4081; color: white;">Manage Users</a></li>
            <li><a href="admin_logout.php">Logout</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-container">
            <h1>Manage Users</h1>
            
            <?php if (isset($success_message)): ?>
                <div class="success-message">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="value"><?php echo count($users); ?></div>
                </div>
            </div>
            
            <div class="recent-orders">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($user['address'] ?? 'N/A'); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">Edit</a>
                                <a href="manage_users.php?delete_id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>