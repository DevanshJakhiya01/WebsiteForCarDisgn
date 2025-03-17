<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = intval($_POST['car_id']);

    // Initialize cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add car to cart
    if (!in_array($car_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $car_id;
        echo "Car added to cart successfully!";
    } else {
        echo "Car is already in the cart!";
    }
} else {
    echo "Invalid request!";
}
?>