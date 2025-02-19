<html>
<head>
    <title>Sedans</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-image: url("Images/doddles of car in whole page in pink and red color for website background.jpg");
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
        <img src="Images/Devansh Car Customization logo 1.jpg" alt="Devansh Car Customization Logo">
    </div>
    <h1>Welcome to Sedans!</h1>
    <p>This is the sedan page.</p>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "car_customization_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sedans = [
        [
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

    foreach ($sedans as $index => $sedan): ?>
        <div class="polaroid">
            <img src="<?= htmlspecialchars($sedan['image']) ?>" alt="<?= htmlspecialchars($sedan['name']) ?>" id="sedan-image-<?= $index ?>">
            <div class="container">
                <p><?= htmlspecialchars($sedan['name']) ?></p>
                <p><?= htmlspecialchars($sedan['description']) ?></p>
                <div class="select-container">
                    <select onchange="changeImage(<?= $index ?>, this.value, 'wheels')">
                        <option value="stock">Stock Wheels</option>
                        <option value="sport">Sport Wheels</option>
                        <option value="alloy">Alloy Wheels</option>
                        <option value="black">Black Rims</option>
                    </select>
                    <select onchange="changeImage(<?= $index ?>, this.value, 'paint')">
                        <option value="default">Default</option>
                        <option value="candyred">Candy Red</option>
                        <option value="perlblue">Perl Blue</option>
                        <option value="detonagreen">Detona Green</option>
                        <option value="blacksparidematte">Black Sparide Matte</option>
                    </select>
                    <button onclick="submitCustomization(<?= $index ?>)">Submit</button>
                </div>
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

        function submitCustomization(index) {
            const selectedWheels = document.querySelector(`#sedan-image-${index}`).previousElementSibling.querySelector('select').value;
            const selectedPaint = document.querySelector(`#sedan-image-${index}`).nextElementSibling.querySelector('select').value;
            console.log(`Customization submitted for Sedan ${index}: Wheels - ${selectedWheels}, Paint - ${selectedPaint}`);
            alert(`Customization submitted for Sedan ${index + 1}: Wheels - ${selectedWheels}, Paint - ${selectedPaint}`);
        }
    </script>
</body>
</html>