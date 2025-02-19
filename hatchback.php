<html>
<head>
    <title>Hatchbacks</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-image: url("Images/doddles of car in whole page in pink and red color for website background.jpg");
            background-size: cover;
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
    <h1>Welcome to Hatchbacks!</h1>
    <p>This is the hatchback page.</p>

    <?php
    // Database connection
    

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "car_customization_db";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $hatchbacks = [
        ["name" => "Suzuki Swift", "image" => "Images/Suzuki_Swift_04.jpg", "description" => "A popular and fuel-efficient hatchback.",
        "custom_images" => [
            "stock" => "Images/Suzuki_Swift_04.jpg",
            "alloy" => "Images/Firefly imagine red color Maruti Suzuki Swift 2024 with Alloy Wheels 48339.jpg",
            "steel" => "Images/Firefly red color Maruti Suzuki Swift 2024 with Steel Wheels 59797.jpg",
            "aftermarket" => "Images/Firefly red color Maruti Suzuki Swift 2024 with Aftermarket Wheels 86150.jpg",
            "candyred" => "Images/Firefly Maruti Suzuki Swift Cany Red color 65968.jpg",
            "perlblue" => "Images/Firefly Suzuki Swift Car Perl Blue Color 67714.jpg",
            "detonagreen" => "Images/Firefly Suzuki Swift Car Detona green Color 62240.jpg",
            "blacksparidematte" => "Images/Firefly Suzuki Swift Car Black Sparide Matte Color 67714.jpg",
        ]],
        ["name" => "Honda Civic", "image" => "Images/2023-honda-civic-sdn_100861363_h.jpg", "description" => "Known for its reliability and sporty handling.",
        "custom_images" => [
            "stock" => "Images/2023-honda-civic-sdn_100861363_h.jpg",
            "alloy" => "Images/Firefly honda civic red color with Alloy whells 85734.jpg",
            "steel" => "Images/Firefly Honda civic red color with Steel wheels 85734.jpg",
            "aftermarket" => "Images/Firefly Honda civic red color with Aftermarket wheels 85734.jpg",
            "candyred" => "Images/Firefly Honda civic car in Candy red color 23588.jpg",
            "perlblue" => "Images/Firefly Honda Civic car in Perl Blue color 23588.jpg",
            "detonagreen" => "Images/Firefly Honda Civic car in Detona Green color 23588.jpg",
            "blacksparidematte" => "Images/Firefly Honda Civic car in Black Sparide Matte color 15388.jpg",
        ]],
        ["name" => "Volkswagen Golf", "image" => "Images/volkswagen-golf-2020-specs-01.jpg", "description" => "A classic hatchback with a premium feel.",
        "custom_images" => [
            "stock" => "Images/volkswagen-golf-2020-specs-01.jpg",
            "alloy" => "Images/Firefly Volkswagen Golf Green color with Alloy wheel 69604.jpg",
            "steel" => "Images/Firefly Volkswagen Golf Green color with Steel wheel 69604.jpg",
            "aftermarket" => "Images/Firefly Volkswagen Golf Green color with Aftermarket wheel 69604.jpg",
            "candyred" => "Images/Firefly Volkswagen golf car in Candy Red color 84012.jpg",
            "perlblue" => "Images/Firefly Volkswagen golf car in Perl Blue color 10246.jpg",
            "detonagreen" => "Images/Firefly Volkswagen golf car in Detona Green color 10246.jpg",
            "blacksparidematte" => "Images/Firefly Volkswagen golf car in Black Sparide Matte color 84012.jpg",
        ]],
    ];

    foreach ($hatchbacks as $index => $hatchback): ?>
        <div class="polaroid">
            <img src="<?= htmlspecialchars($hatchback['image']) ?>" alt="<?= htmlspecialchars($hatchback['name']) ?>" id="hatchback-image-<?= $index ?>">
            <div class="container">
                <p><?= htmlspecialchars($hatchback['name']) ?></p>
                <p><?= htmlspecialchars($hatchback['description']) ?></p>
                <div class="select-container">
                    <select onchange="changeImage(<?= $index ?>, this.value, 'wheels')">
                        <option value="stock">Stock Wheels</option>
                        <option value="alloy">Alloy Wheels</option>
                        <option value="steel">Steel Wheels</option>
                        <option value="aftermarket">Aftermarket Wheels</option>
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
        const hatchbackImages = <?= json_encode(array_column($hatchbacks, 'custom_images')) ?>;
        function changeImage(index, value, type) {
            let imageValue;
            if (type === "wheels") {
                imageValue = value;
            } else if (type === "paint") {
                imageValue = value !== 'default' ? value : 'stock';
            } else {
                imageValue = "stock"; 
            }
            const imageElement = document.getElementById(`hatchback-image-${index}`);
            if (hatchbackImages[index] && hatchbackImages[index][imageValue]) {
                imageElement.src = hatchbackImages[index][imageValue];
            } else {
                console.error("Invalid image value:", value, "for hatchback", index);
            }
        }

        function submitCustomization(index) {
            const selectedWheels = document.querySelector(`select[onchange='changeImage(${index}, this.value, "wheels")']`).value;
            const selectedPaint = document.querySelector(`select[onchange='changeImage(${index}, this.value, "paint")']`).value;
            const hatchbackName = <?= json_encode(array_column($hatchbacks, 'name')) ?>[index];
            

            fetch('submit_customization.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name: hatchbackName, wheels: selectedWheels, paint: selectedPaint })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Your car customization request has been submitted!');
                } else {
                    alert('Failed to submit customization request.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>