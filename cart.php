<?php
session_start();

$cars = [
    1 => [
        "name" => "Tata Tiago",
        "type" => "Hatchback",
        "price" => 500000
    ],
    2 => [
        "name" => "Dzire",
        "type" => "Sedan",
        "price" => 700000
    ],
    3 => [
        "name" => "Kia Syros",
        "type" => "SUV",
        "price" => 1200000
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .cart-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .cart-item button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .cart-item button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <h1>Your Cart</h1>
        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <?php foreach ($_SESSION['cart'] as $car_id): ?>
                <div class="cart-item">
                    <span><?php echo $cars[$car_id]['name']; ?> (<?php echo $cars[$car_id]['type']; ?>)</span>
                    <span>₹<?php echo number_format($cars[$car_id]['price'], 2); ?></span>
                    <button onclick="removeFromCart(<?php echo $car_id; ?>)">Remove</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <script>
        function removeFromCart(carId) {
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `car_id=${carId}`
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Show success message
                location.reload(); // Refresh the page
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>