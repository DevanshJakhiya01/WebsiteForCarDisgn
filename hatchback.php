<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hatchbacks</title>
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

        .wheel-image {
            margin-top: 15px;
            display: none;
            max-width: 100%;
            height: auto;
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
    <img src="Images/Devansh%20Car%20Customization%20logo%201.jpg" alt="Devansh Car Customization Logo" class="logo">
    <h1>Welcome to Hatchbacks!</h1>
    <p>This is the hatchback page.</p>

    <?php
    $hatchbacks = [
        ["name" => "Suzuki Swift", "image" => "Images/Suzuki_Swift_04.jpg", "description" => "A popular and fuel-efficient hatchback."],
        ["name" => "Honda Civic", "image" => "Images/2023-honda-civic-sdn_100861363_h.jpg", "description" => "Known for its reliability and sporty handling."],
        ["name" => "Volkswagen Golf", "image" => "Images/volkswagen-golf-2020-specs-01.jpg", "description" => "A classic hatchback with a premium feel."],
    ];

    foreach ($hatchbacks as $index => $hatchback): ?>
        <div class="polaroid">
            <img src="<?= htmlspecialchars($hatchback['image']) ?>" alt="<?= htmlspecialchars($hatchback['name']) ?>">
            <div class="container">
                <p><?= htmlspecialchars($hatchback['name']) ?></p>
                <p><?= htmlspecialchars($hatchback['description']) ?></p>
                <select id="wheel-select-<?= $index ?>" name="wheel_type" onchange="updateImage(<?= $index ?>)">
                    <option value="">Select Wheel Type</option>
                    <option value="alloy">Alloy Wheels</option>
                    <option value="steel">Steel Wheels</option>
                    <option value="aftermarket">Aftermarket Wheels</option>
                </select>
                <img id="wheel-image-<?= $index ?>" class="wheel-image" alt="Wheel Type">
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        function updateImage(index) {
            const selectElement = document.getElementById(`wheel-select-${index}`);
            const wheelImage = document.getElementById(`wheel-image-${index}`);
            const selectedValue = selectElement.value;

            if (selectedValue) {
                const wheelImages = {
                    alloy: [
                        'Images/Firefly%20imagine%20red%20color%20Maruti%20Suzuki%20Swift%202024%20with%20Alloy%20Wheels%2048339.jpg',
                        'Images/Firefly%20honda%20civic%20red%20color%20with%20Alloy%20whells%2085734.jpg',
                        'Images/Firefly%20Volkswagen%20Golf%20Green%20color%20with%20Alloy%20wheel%2069604.jpg'
                    ],
                    steel: [
                        'Images/Firefly%20red%20color%20Maruti%20Suzuki%20Swift%202024%20with%C2%A0Steel%20Wheels%2059797.jpg',
                        'Images/Firefly%20Honda%20civic%20red%20color%20with%20Steel%20wheels%2085734.jpg',
                        'Images/Firefly%20Volkswagen%20Golf%20Green%20color%20with%20Steel%20wheel%2069604.jpg'
                    ],
                    aftermarket: [
                        'Images/Firefly%20red%20color%20Maruti%20Suzuki%20Swift%202024%20with%20Aftermarket%20Wheels%2086150.jpg',
                        'Images/Firefly%20Honda%20civic%20red%20color%20with%20Aftermarket%20wheels%2085734.jpg',
                        'Images/Firefly%20Volkswagen%20Golf%20Green%20color%20with%20Aftermarket%20wheel%2069604.jpg'
                    ]
                };

                wheelImage.src = wheelImages[selectedValue][index] || '';
                wheelImage.style.display = 'block';
            } else {
                wheelImage.style.display = 'none';
            }
        }
    </script>
</body>
</html>
