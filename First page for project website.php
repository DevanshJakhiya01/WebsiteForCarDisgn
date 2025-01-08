<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devansh Car Customization</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-color: lightskyblue;
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

        .button {
            padding: 12px 24px;
            background-color: darksalmon;
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #e9967a;
        }

        @media (min-width: 768px) {
            body {
                margin: 50px;
            }
            .polaroid {
                width: 80%;
            }
            .logo{
                width: 400px;
            }
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
    </div>

    <?php
    $cars = [
        [
            "image" => "images/tatatiagopxe_373379_daytonagrey_base.jpeg", 
            "alt" => "Tata Tiago",
            "type" => "Hatchback",
            "link" => "hatchback.php"
        ],
        [
            "image" => "images/dzire-2024-exterior-right-front-three-quarter-3.jpeg", 
            "alt" => "Dzire",
            "type" => "Sedan",
            "link" => "sedan.php"
        ],
        [
            "image" => "images/syrosintensered.jpeg", 
            "alt" => "Kia Syros",
            "type" => "SUV",
            "link" => "suv.php"
        ],
    ];

    foreach ($cars as $car): ?>
        <div class="polaroid">
            <img src="<?php echo $car['image']; ?>" alt="<?php echo $car['alt']; ?>">
            <div class="container">
                <a href="<?php echo $car['link']; ?>" class="button"><?php echo $car['type']; ?></a>
            </div>
        </div>
    <?php endforeach; ?>

</body>
</html>