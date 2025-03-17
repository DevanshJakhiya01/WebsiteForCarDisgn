<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = intval($_POST['car_id']);

    if (isset($_SESSION['cart'])) {
        $index = array_search($car_id, $_SESSION['cart']);
        if ($index !== false) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
            echo "Car removed from cart successfully!";
        } else {
            echo "Car not found in cart!";
        }
    } else {
        echo "Cart is empty!";
    }
} else {
    echo "Invalid request!";
}
?>