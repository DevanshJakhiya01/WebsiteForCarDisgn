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
    <script>
        function updateImage(index) {
            const selectElement = document.getElementById(`wheel-select-${index}`);
            const wheelImage = document.getElementById(`wheel-image-${index}`);
            const selectedValue = selectElement.value;

            const wheelImages = {
                "Standard Wheels": [
                    'Images/El nuevo Volkswagen Polo llegará a México en 2023.jpeg',
                    'Images/audi%20A3.jpg',
                    'Images/2024%20Lexus%20ES.jpg'
                ],
                "Sport Wheels": [
                    'Images/Firefly%20Volkswagen%20Polo%20in%20purple%20color%20with%20Sport%20Wheel%2038905.jpg',
                    'Images/Firefly%20Audi%20A3%20Blue%20in%20color%20with%20Sport%20Wheel%2010329.jpg',
                    'Images/Firefly%20Lexus%20ES%20in%20Golden%20color%20with%20Sports%20Wheel%2034255.jpg'
                ],
                "Alloy Wheels": [
                    'Images/Firefly%20Volkswagen%20Polo%20in%20purple%20color%20with%20Alloy%20Wheel%2084813.jpg',
                    'Images/Firefly%20Audi%20A3%20Blue%20in%20color%20with%20Alloy%20Wheel%2010329.jpg',
                    'Images/Firefly%20Lexus%20ES%20in%20Golden%20color%20with%20Alloy%20Wheel%2034255.jpg'
                ],
                "Black Rims": [
                    'Images/Firefly%20Volkswagen%20Polo%20in%20purple%20color%20with%20Black%20Rims%2088237.jpg',
                    'Images/Firefly%20Audi%20A3%20Blue%20in%20color%20with%20Black%20Rims%2052221.jpg',
                    'Images/Firefly%20Lexus%20ES%20in%20Golden%20color%20with%20Black%20Rims%2034255.jpg'
                ]
            };

            wheelImage.src = wheelImages[selectedValue][index] || '';
            wheelImage.style.display = selectedValue ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <h1>Welcome to Sedan</h1>
    <p>This is the sedan page.</p>

    <?php
    $sedans = [
        ["name" => "Volkswagen Polo", "image" => "Images/El nuevo Volkswagen Polo llegará a México en 2023.jpeg", "description" => "A stylish and efficient compact car."],
        ["name" => "Audi A3", "image" => "Images/audi%20A3.jpg", "description" => "A premium compact sedan with advanced technology."],
        ["name" => "Lexus ES", "image" => "Images/2024%20Lexus%20ES.jpg", "description" => "A luxurious and comfortable mid-size sedan."]
    ];

    $wheelOptions = ["Standard Wheels", "Sport Wheels", "Alloy Wheels", "Black Rims"];

    foreach ($sedans as $index => $sedan): ?>
        <div class="polaroid">
            <img id="wheel-image-<?= $index ?>" src="<?= $sedan['image'] ?>" alt="<?= $sedan['name'] ?>">
            <div class="container">
                <p><?= $sedan['name'] ?></p>
                <p><?= $sedan['description'] ?></p>
                <select id="wheel-select-<?= $index ?>" onchange="updateImage(<?= $index ?>)">
                    
                    <?php foreach ($wheelOptions as $wheel): ?>
                        <option value="<?= $wheel ?>"><?= $wheel ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endforeach; ?>

</body>
</html>
