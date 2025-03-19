<?php
session_start(); // Start the session to manage cart data

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

// Function to handle form submission
function handleFormSubmission($conn) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Check if all required fields are set
        if (isset($_POST['car_name'], $_POST['wheels'], $_POST['paint'], $_POST['product_id'])) {
            $car_name = htmlspecialchars($_POST['car_name']);
            $wheels = htmlspecialchars($_POST['wheels']);
            $paint = htmlspecialchars($_POST['paint']);
            $product_id = intval($_POST['product_id']);

            // Add product to cart (stored in session)
            $cart_item = [
                "product_id" => $product_id,
                "car_name" => $car_name,
                "wheels" => $wheels,
                "paint" => $paint,
                "price" => 16000.00 // Example price
            ];

            // Initialize cart if it doesn't exist
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Add item to cart
            $_SESSION['cart'][] = $cart_item;

            // Redirect to cart page
            header("Location: cart.php");
            exit();
        } else {
            echo "<script>alert('All form fields are required.');</script>";
        }
    }
}

// Call the function to handle form submission
handleFormSubmission($conn);

// Sedan data
$sedans = [
    [
        "id" => 1, // Example product_id (must exist in the products table)
        "name" => "Volkswagen Polo",
        "image" => "Images/El nuevo Volkswagen Polo llegará a México en 2023.jpeg",
        "description" => "A stylish and efficient compact car.",
        "custom_images" => [
            "stock" => "Images/El nuevo Volkswagen Polo llegará a México en 2023.jpeg",
            "sport" => "Images/Firefly Volkswagen Polo in purple color with Sport Wheel 38905.jpg",
            "alloy" => "Images/Firefly Volkswagen Polo in purple color with Alloy Wheel 84813.jpg",
            "black" => "Images/Firefly Volkswagen Polo in purple color with Black Rims 88237.jpg",
            "candyred" => "Images/Firefly Volkswagen polo candy red color 36117.jpg",
            "perlblue" => "Images/Firefly Volkswagen polo purl blue color 70934.jpg",
            "detonagreen" => "Images/Firefly Volkswagen polo Detona Green color 70934.jpg",
            "blacksparidematte" => "Images/Firefly Volkswagen polo Black Sparide Matte color 97521.jpg"
        ]
    ],
    [
        "id" => 2, // Example product_id (must exist in the products table)
        "name" => "Audi A3",
        "image" => "Images/audi A3.jpg",
        "description" => "A premium compact sedan with advanced technology.",
        "custom_images" => [
            "stock" => "Images/audi A3.jpg",
            "sport" => "Images/Firefly Audi A3 Blue in color with Sport Wheel 10329.jpg",
            "alloy" => "Images/Firefly Audi A3 Blue in color with Alloy Wheel 10329.jpg",
            "black" => "Images/Firefly Audi A3 Blue in color with Black Rims 52221.jpg",
            "candyred" => "Images/Firefly Audi A3 candy red color car 94718.jpg",
            "perlblue" => "Images/Firefly Audi A3 purl blue color car 94718.jpg",
            "detonagreen" => "Images/Firefly Audi A3 Detona Green color car 94718.jpg",
            "blacksparidematte" => "Images/Firefly Audi A3 Black Sparide Matte color car 94718.jpg"
        ]
    ],
    [
        "id" => 3, // Example product_id (must exist in the products table)
        "name" => "Lexus ES",
        "image" => "Images/2024 Lexus ES.jpg",
        "description" => "A luxurious and comfortable mid-size sedan.",
        "custom_images" => [
            "stock" => "Images/2024 Lexus ES.jpg",
            "sport" => "Images/Firefly Lexus ES in Golden color with Sports Wheel 34255.jpg",
            "alloy" => "Images/Firefly Lexus ES in Golden color with Alloy Wheel 34255.jpg",
            "black" => "Images/Firefly Lexus ES in Golden color with Black Rims 34255.jpg",
            "candyred" => "Images/Firefly Lexus ES candy red color car 94718.jpg",
            "perlblue" => "Images/Firefly Lexus ES Perl Blue car 94718.jpg",
            "detonagreen" => "Images/Firefly Lexus ES Detona Green car 94718.jpg",
            "blacksparidematte" => "Images/Firefly Lexus ES Black Sparide Matte car 94718.jpg"
        ]
    ]
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sedans</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-image: url("Images/doddles%20of%20car%20in%20whole%20page%20in%20pink%20and%20red%20color%20for%20website%20background.jpg");
            background-size: auto;
            color: red;
            display: flex;
            flex-direction: column;
            align-items: center;
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

        .polaroid {
            width: 90%;
            max-width: 600px;
            background-color: white;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        }

        .polaroid img {
            width: 100%;
            height: auto;
            display: block;
        }

        .container {
            padding: 15px;
        }

        .select-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: red;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: darkred;
        }

        @media (min-width: 768px) {
            body {
                margin: 50px;
            }
            .polaroid {
                width: 80%;
            }
            .logo {
                width: 400px;
            }
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
    </div>
    <h1>Welcome to Sedans!</h1>
    <p>This is the sedan page.</p>

    <?php foreach ($sedans as $index => $sedan): ?>
        <div class="polaroid">
            <img src="<?= htmlspecialchars($sedan['image']) ?>" alt="<?= htmlspecialchars($sedan['name']) ?>" id="sedan-image-<?= $index ?>">
            <div class="container">
                <p><?= htmlspecialchars($sedan['name']) ?></p>
                <p><?= htmlspecialchars($sedan['description']) ?></p>
                <form method="POST" action="">
                    <div class="select-container">
                        <select name="wheels" onchange="changeImage(<?= $index ?>, this.value, 'wheels')">
                            <option value="stock">Stock Wheels</option>
                            <option value="sport">Sport Wheels</option>
                            <option value="alloy">Alloy Wheels</option>
                            <option value="black">Black Rims</option>
                        </select>
                        <select name="paint" onchange="changeImage(<?= $index ?>, this.value, 'paint')">
                            <option value="default">Default</option>
                            <option value="candyred">Candy Red</option>
                            <option value="perlblue">Perl Blue</option>
                            <option value="detonagreen">Detona Green</option>
                            <option value="blacksparidematte">Black Sparide Matte</option>
                        </select>
                        <input type="hidden" name="car_name" value="<?= htmlspecialchars($sedan['name']) ?>">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($sedan['id']) ?>">
                        <button type="submit">Add to Cart</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        const sedanImages = <?= json_encode(array_column($sedans, 'custom_images')) ?>;

        function changeImage(index, value, type) {
            let imageValue;
            if (type === "wheels") {
                imageValue = value;
            } else if (type === "paint") {
                imageValue = value !== 'default' ? value : 'stock';
            } else {
                imageValue = "stock";
            }

            const imageElement = document.getElementById(`sedan-image-${index}`);
            if (sedanImages[index] && sedanImages[index][imageValue]) {
                imageElement.src = sedanImages[index][imageValue];
            } else {
                console.error("Invalid image value:", value, "for sedan", index);
            }
        }
    </script>
</body>
</html>