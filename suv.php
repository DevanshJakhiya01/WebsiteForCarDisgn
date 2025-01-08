<html>
<head>
    <title>Suvs</title>
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

        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; 
            width: 100%;
            margin-top: 10px;
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
<img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo">
    <h1>Welcome to Suvs!</h1>
    <p>This is the suv page.</p>

    <?php
    $Suvs = [
        ["name" => "Land Rover Defender", "image" => "Images/2024%20Land%20Rover%20Defender.jpg", "description" => "The iconic Land Rover Defender, blending rugged capability with modern luxury."],
        ["name" => "Mahindra XUV 700: A Luxurious", "image" => "Images/Mahindra%20XUV%20700%20A%20Luxurious.jpg", "description" => "The Mahindra XUV700, offering a premium experience at a competitive price."],
        ["name" => "XT6 Cadillac", "image" => "Images/2023-cadillac-xt6-exterior-001.jpg", "description" => "The Cadillac XT6, a stylish and refined SUV with three rows of seating."],
    ];

    foreach ($Suvs as $Suv): ?>
        <div class="polaroid">
            <img src="<?= $Suv['image'] ?>" alt="<?= $Suv['name'] ?>">
            <div class="container">
                <p><?= $Suv['name'] ?></p>
                <p><?= $Suv['description'] ?></p>
                <select>
                    <option value="stock">Stock Wheels</option>
                    <option value="black">Black Rims</option>
                    <option value="chrome">Chrome Rims</option>
                    <option value="offroad">Off-Road Tires</option>
                </select>
            </div>
        </div>
    <?php endforeach; ?>

</body>
</html>