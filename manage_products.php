<?php
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

// Handle file upload
function uploadImage($file) {
    $target_dir = "Images/";
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ["success" => false, "message" => "File is not an image."];
    }

    // Check file size (max 5MB)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "File is too large. Maximum size is 5MB."];
    }

    // Allow only certain file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        return ["success" => false, "message" => "Only JPG, JPEG, PNG, and GIF files are allowed."];
    }

    // Upload the file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "file_path" => $target_file];
    } else {
        return ["success" => false, "message" => "Error uploading file."];
    }
}

// Handle form submission for adding a new product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $category_id = $_POST['category_id'];
    $product_color = $_POST['product_color']; // New field for color

    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $upload_result = uploadImage($_FILES['product_image']);
        if ($upload_result['success']) {
            $product_image = $upload_result['file_path'];
        } else {
            echo "<script>alert('Error: " . $upload_result['message'] . "');</script>";
            $product_image = null;
        }
    } else {
        $product_image = null;
    }

    // Insert new product into the database
    if ($product_image) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, category_id, image, color) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdiss", $product_name, $product_price, $category_id, $product_image, $product_color);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price, category_id, color) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdis", $product_name, $product_price, $category_id, $product_color);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Product added successfully!');</script>";
        echo "<script>window.location.href = 'manage_products.php';</script>";
    } else {
        echo "<script>alert('Error adding product: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Fetch products from the database
$sql = "SELECT products.id, products.name, products.price, products.image, products.color, categories.name AS category_name 
        FROM products 
        INNER JOIN categories ON products.category_id = categories.id";
$result = $conn->query($sql);

// Fetch categories for the dropdown
$category_sql = "SELECT id, name FROM categories";
$category_result = $conn->query($category_sql);

// Handle product deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM products WHERE id = $delete_id";
    if ($conn->query($delete_sql)) {
        echo "<script>alert('Product deleted successfully!');</script>";
        echo "<script>window.location.href = 'manage_products.php';</script>";
    } else {
        echo "<script>alert('Error deleting product: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
         body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-image: url("Images/doddles\ of\ car\ in\ whole\ page\ in\ pink\ and\ red\ color\ for\ website\ background.jpg");
            background-size: auto;
            color: red;
        }
        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .admin-profile {
            text-align: center;
            margin-bottom: 20px;
        }
        .admin-profile img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid red;
            margin-bottom: 10px;
        }
        .admin-profile p {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 15px 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .sidebar ul li a:hover {
            background-color: #555;
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
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
        .form-container {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 500px;
            margin: 0 auto 20px;
        }
        .form-container h2 {
            text-align: center;
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container select,
        .form-container input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: red;
            color: white;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: darkred;
        }
        .table-container {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 800px;
            margin: 0 auto;
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table-container th, .table-container td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .table-container th {
            background-color: #f2f2f2;
        }
        .table-container img {
            width: 100px;
            height: auto;
            display: block;
        }
        .table-container button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background-color: red;
            color: white;
            cursor: pointer;
        }
        .table-container button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="admin-profile">
            <img src="Images/Devansh%203dxx.jpg" alt="Admin Photo">
            <p>Admin</p>
        </div>
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php">Users</a></li>
            <li><a href="add_user.php">Add User</a></li>
            <li><a href="manage_categories.php">Categories</a></li>
            <li><a href="manage_products.php">Products</a></li>
            <li><a href="manage_orders.php">Orders</a></li>
            <li><a href="manage_payments.php">Payments</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="logo">
            <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
        </div>

        <div class="form-container">
            <h2>Add New Product</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" required>

                <label for="product_price">Product Price:</label>
                <input type="number" id="product_price" name="product_price" step="0.01" required>

                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <?php
                    if ($category_result->num_rows > 0) {
                        while ($row = $category_result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                        }
                    } else {
                        echo "<option value=''>No categories found</option>";
                    }
                    ?>
                </select>

                <label for="product_color">Product Color:</label>
                <input type="text" id="product_color" name="product_color" required>

                <label for="product_image">Product Image:</label>
                <input type="file" id="product_image" name="product_image" accept="image/*">

                <button type="submit">Add Product</button>
            </form>
        </div>

        <div class="table-container">
            <h2>Manage Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Color</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['price']}</td>
                                    <td>{$row['category_name']}</td>
                                    <td>{$row['color']}</td>
                                    <td><img src='{$row['image']}' alt='{$row['name']}'></td>
                                    <td>
                                        <a href='manage_products.php?delete_id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this product?');\"><button>Delete</button></a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No products found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>