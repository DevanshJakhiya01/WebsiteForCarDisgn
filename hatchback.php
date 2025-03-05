<html>
<head>
    <title>Hatchbacks</title>
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
    <h1>Welcome to Hatchbacks!</h1>
    <p>This is the hatchback page.</p>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "car_customization";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $hatchbacks = [
        ["name" => "Suzuki Swift", "image" => "Images/Suzuki_Swift_04.jpg", "description" => "A popular and fuel-efficient hatchback.",
        "custom_images" => [
            "stock" => "Images/Suzuki_Swift_04.jpg",
            "alloy" => "Images/Firefly%20imagine%20red%20color%20Maruti%20Suzuki%20Swift%202024%20with%20Alloy%20Wheels%2048339.jpg",
            "steel" => "Images/Firefly%20red%20color%20Maruti%20Suzuki%20Swift%202024%20with%C2%A0Steel%20Wheels%2059797.jpg",
            "aftermarket" => "Images/Firefly%20red%20color%20Maruti%20Suzuki%20Swift%202024%20with%20Aftermarket%20Wheels%2086150.jpg",
            "candyred" => "Images/Firefly%20Maruti%20Suzuki%20Swift%20Cany%20Red%20color%2065968.jpg",
            "perlblue" => "Images/Firefly%20Suzuki%20Swift%20Car%20Perl%20Blue%20Color%2067714.jpg",
            "detonagreen" => "Images/Firefly%20Suzuki%20Swift%20Car%20Detona%20green%20Color%2062240.jpg",
            "blacksparidematte" => "Images/Firefly%20Suzuki%20Swift%20Car%20Black%20Sparide%20Matte%20Color%2067714.jpg",
        ]],
        ["name" => "Honda Civic", "image" => "Images/2023-honda-civic-sdn_100861363_h.jpg", "description" => "Known for its reliability and sporty handling.",
        "custom_images" => [
            "stock" => "Images/2023-honda-civic-sdn_100861363_h.jpg",
            "alloy" => "Images/Firefly%20honda%20civic%20red%20color%20with%20Alloy%20whells%2085734.jpg",
            "steel" => "Images/Firefly%20Honda%20civic%20red%20color%20with%20Steel%20wheels%2085734.jpg",
            "aftermarket" => "Images/Firefly%20Honda%20civic%20red%20color%20with%20Aftermarket%20wheels%2085734.jpg",
            "candyred" => "Images/Firefly%20Honda%20civic%20car%20in%20Candy%20red%20color%2023588.jpg",
            "perlblue" => "Images/Firefly%20Honda%20Civic%20car%20in%20Perl%20Blue%20color%2023588.jpg",
            "detonagreen" => "Images/Firefly%20Honda%20Civic%20car%20in%20Detona%20Green%20color%2023588.jpg",
            "blacksparidematte" => "Images/Firefly%20Honda%20Civic%20car%20in%20Black%20Sparide%20Matte%20color%2015388.jpg",
        ]],
        ["name" => "Volkswagen Golf", "image" => "Images/volkswagen-golf-2020-specs-01.jpg", "description" => "A classic hatchback with a premium feel.",
        "custom_images" => [
            "stock" => "Images/volkswagen-golf-2020-specs-01.jpg",
            "alloy" => "Images/Firefly%20Volkswagen%20Golf%20Green%20color%20with%20Alloy%20wheel%2069604.jpg",
            "steel" => "Images/Firefly%20Volkswagen%20Golf%20Green%20color%20with%20Steel%20wheel%2069604.jpg",
            "aftermarket" => "Images/Firefly%20Volkswagen%20Golf%20Green%20color%20with%20Aftermarket%20wheel%2069604.jpg",
            "candyred" => "Images/Firefly%20Volkswagen%20golf%20car%20in%20Candy%20Red%20color%2084012.jpg",
            "perlblue" => "Images/Firefly%20Volkswagen%20golf%20car%20in%20Perl%20Blue%20color%2010246.jpg",
            "detonagreen" => "Images/Firefly%20Volkswagen%20golf%20car%20in%20Detona%20Green%20color%2010246.jpg",
            "blacksparidematte" => "Images/Firefly%20Volkswagen%20golf%20car%20in%20Black%20Sparide%20Matte%20color%2084012.jpg",
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