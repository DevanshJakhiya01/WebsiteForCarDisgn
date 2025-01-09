<html>
<head>
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

    <?php
    $sedans = [
        ["name" => "Volkswagen Polo", "image" => "Images/El nuevo Volkswagen Polo llegará a México en 2023.jpeg", "description" => "A stylish and efficient compact car.",
        "custom_images" => [
            "stock" => "Images/El nuevo Volkswagen Polo llegará a México en 2023.jpeg",
            "sport" => "Images/Firefly%20Volkswagen%20Polo%20in%20purple%20color%20with%20Sport%20Wheel%2038905.jpg",
            "alloy" => "Images/Firefly%20Volkswagen%20Polo%20in%20purple%20color%20with%20Alloy%20Wheel%2084813.jpg",
            "black" => "Images/Firefly%20Volkswagen%20Polo%20in%20purple%20color%20with%20Black%20Rims%2088237.jpg"
        ]],
        ["name" => "Audi A3", "image" => "Images/audi%20A3.jpg", "description" => "A premium compact sedan with advanced technology.",
        "custom_images" => [
            "stock" => "Images/audi%20A3.jpg",
            "sport" => "Images/Firefly%20Audi%20A3%20Blue%20in%20color%20with%20Sport%20Wheel%2010329.jpg",
            "alloy" => "Images/Firefly%20Audi%20A3%20Blue%20in%20color%20with%20Alloy%20Wheel%2010329.jpg",
            "black" => "Images/Firefly%20Audi%20A3%20Blue%20in%20color%20with%20Black%20Rims%2052221.jpg"
        ]],
        ["name" => "Lexus ES", "image" => "Images/2024%20Lexus%20ES.jpg", "description" => "A luxurious and comfortable mid-size sedan.",
        "custom_images" => [
            "stock" => "Images/2024%20Lexus%20ES.jpg",
            "sport" => "Images/Firefly%20Lexus%20ES%20in%20Golden%20color%20with%20Sports%20Wheel%2034255.jpg",
            "alloy" => "Images/Firefly%20Lexus%20ES%20in%20Golden%20color%20with%20Alloy%20Wheel%2034255.jpg",
            "black" => "Images/Firefly%20Lexus%20ES%20in%20Golden%20color%20with%20Black%20Rims%2034255.jpg"
        ]]
    ];

    foreach ($sedans as $index => $sedan): ?>
        <div class="polaroid">
            <img src="<?= htmlspecialchars($sedan['image']) ?>" alt="<?= htmlspecialchars($sedan['name']) ?>" id="sedan-image-<?= $index ?>">
            <div class="container">
                <p><?= htmlspecialchars($sedan['name']) ?></p>
                <p><?= htmlspecialchars($sedan['description']) ?></p>
                <select onchange="changeImage(<?= $index ?>, this.value)">
                    <option value="stock">Stock Wheels</option>
                    <option value="sport">Sport Wheels</option>
                    <option value="alloy">Alloy Wheels</option>
                    <option value="black">Black Rims</option>
                </select>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        function changeImage(index, value) {
            const sedanImages = <?= json_encode(array_column($sedans, 'custom_images')) ?>;
            const imageElement = document.getElementById(`sedan-image-${index}`);
            imageElement.src = sedanImages[index][value];
        }
    </script>

</body>
</html>
