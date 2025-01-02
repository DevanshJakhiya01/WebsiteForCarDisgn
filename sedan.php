<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedans</title>
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

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .polaroid {
            width: 90%;
            max-width: 600px;
            background-color: white;
            margin-bottom: 20px;
            text-align: center;
            overflow: hidden;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        .polaroid img {
            width: 100%;
            height: auto;
            display: block;
        }

        .container {
            padding: 15px;
            display: flex;
            flex-direction: column; 
            align-items: flex-start; 
        }
        .container p{
            text-align: center;
            margin: 5px 0; 
        }

        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            margin-top: 10px; 
            width: 100%; 
            box-sizing: border-box; 
        }

        @media (min-width: 768px) {
            body {
                margin: 50px;
            }
            .polaroid {
                width: 80%;
            }
        }
    </style>
</head>
<body>
    <h1>Welcome to Sedans!</h1>
    <p>This is the Sedan page.</p>

    <?php
    $sedans = [
        ["image" => "Images/El nuevo Volkswagen Polo llegará a México en 2023.jpeg", "name" => "Volkswagen Polo", "description" => "A stylish and efficient compact car."],
        ["image" => "Images/audi%20A3.jpg", "name" => "Audi A3", "description" => "A premium compact sedan with advanced technology."],
        ["image" => "Images/2024%20Lexus%20ES.jpg", "name" => "Lexus ES", "description" => "A luxurious and comfortable mid-size sedan."],
    ];

    foreach ($sedans as $sedan): ?>
        <div class="polaroid">
            <img src="<?php echo $sedan['image']; ?>" alt="<?php echo $sedan['name']; ?>">
            <div class="container">
                <p><?= $sedan['name'] ?></p>
                <p><?= $sedan['description'] ?></p>
                <select name="wheel_type">
                  <option value="">Select Wheel Type</option>
                  <option value="alloy">Alloy Wheels</option>
                  <option value="steel">Steel Wheels</option>
                  <option value="aftermarket">Aftermarket Wheels</option>
                </select>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>