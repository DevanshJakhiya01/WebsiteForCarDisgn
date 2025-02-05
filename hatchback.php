<!DOCTYPE html>
<html lang="fr">
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
    $hatchbacks = [
        ["name" => "Suzuki Swift", "image" => "Images/Suzuki_Swift_04.jpg", "description" => "A popular and fuel-efficient hatchback.",
        "custom_images" => [
            "wheels" => [
                "stock" => "Images/Suzuki_Swift_04.jpg",
                "alloy" => "Images/Firefly%20imagine%20red%20color%20Maruti%20Suzuki%20Swift%202024%20with%20Alloy%20Wheels%2048339.jpg",
                "steel" => "Images/Firefly%20red%20color%20Maruti%20Suzuki%20Swift%202024%20with%C2%A0Steel%20Wheels%2059797.jpg",
                "aftermarket" => "Images/Firefly%20red%20color%20Maruti%20Suzuki%20Swift%202024%20with%20Aftermarket%20Wheels%2086150.jpg"
            ],
            "paint" => [
                "default" => "Images/Suzuki_Swift_04.jpg",
                "candyred" => "Images/7180c0aa-efa5-42d4-8f79-6db970074ee0_0.jpeg",
                "perlblue" => "Images/Firefly%20Suzuki%20Swift%20Car%20Perl%20Blue%20Color%2067714.jpg",
                "detonagreen" => "Images/Firefly%20Suzuki%20Swift%20Car%20Detona%20green%20Color%2062240.jpg",
                "blacksparidematte" => "Images/Firefly%20Suzuki%20Swift%20Car%20Black%20Sparide%20Matte%20Color%2067714.jpg"
            ]
        ]],
        ["name" => "Honda Civic", "image" => "Images/2023-honda-civic-sdn_100861363_h.jpg", "description" => "Known for its reliability and sporty handling.",
        "custom_images" => [
            "wheels" => [
                "stock" => "Images/2023-honda-civic-sdn_100861363_h.jpg",
                "alloy" => "Images/Firefly%20honda%20civic%20red%20color%20with%20Alloy%20whells%2085734.jpg",
                "steel" => "Images/Firefly%20Honda%20civic%20red%20color%20with%20Steel%20wheels%2085734.jpg",
                "aftermarket" => "Images/Firefly%20Honda%20civic%20red%20color%20with%20Aftermarket%20wheels%2085734.jpg"
            ],
            "paint" => [
                "default" => "Images/2023-honda-civic-sdn_100861363_h.jpg",
                "candyred" => "Images/CandyRed_Civic.jpg",
                "perlblue" => "Images/PerlBlue_Civic.jpg",
                "detonagreen" => "Images/DetonaGreen_Civic.jpg",
                "blacksparidematte" => "Images/BlackSparideMatte_Civic.jpg"
            ]
        ]]
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
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        const hatchbackImages = <?= json_encode(array_column($hatchbacks, 'custom_images')) ?>;
        let selections = Array(hatchbackImages.length).fill({ wheels: "stock", paint: "default" });

        function changeImage(index, value, type) {
            selections[index] = { ...selections[index], [type]: value };
            const selectedWheels = selections[index].wheels;
            const selectedPaint = selections[index].paint;

            let newImage = hatchbackImages[index].wheels[selectedWheels];
            if (hatchbackImages[index].paint[selectedPaint]) {
                newImage = hatchbackImages[index].paint[selectedPaint];
            }

            document.getElementById(`hatchback-image-${index}`).src = newImage;
        }
    </script>
</body>
</html>
