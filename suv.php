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
    <h1>Welcome to Suvs!</h1>
    <p>This is the suv page.</p>

    <?php
    $Suvs = [
        ["name" => "Land Rover Defender", "image" => "Images/2024%20Land%20Rover%20Defender.jpg", "description" => "The iconic Land Rover Defender, blending rugged capability with modern luxury.",
        "custom_images" => [
            "stock" => "Images/2024%20Land%20Rover%20Defender.jpg",
            "black" => "Images/Firefly%20golden%20Land%20Rover%20Defender%20with%20Black%20rims%2013603.jpg",
            "chrome" => "Images/Firefly%20golden%20Land%20Rover%20Defender%20with%20Chrome%20rims%2013603.jpg",
            "offroad" => "Images/Firefly%20golden%20Land%20Rover%20Defender%20with%20Off%20road%20tires%2054774.jpg"
        ]],
        ["name" => "Mahindra XUV 700: A Luxurious", "image" => "Images/Mahindra%20XUV%20700%20A%20Luxurious.jpg", "description" => "The Mahindra XUV700, offering a premium experience at a competitive price.",
        "custom_images" => [
            "stock" => "Images/Mahindra%20XUV%20700%20A%20Luxurious.jpg",
            "black" => "Images/Firefly%20Red%20Mahindra%20XUV%20700-%20A%20Luxurious%20with%20Black%20Rims%204514.jpg",
            "chrome" => "Images/Firefly%20Red%20Mahindra%20XUV%20700-%20A%20Luxurious%20with%20Chrome%20Rims%204514.jpg",
            "offroad" => "Images/Firefly%20Red%20Mahindra%20XUV%20700-%20A%20Luxurious%20with%20Off%20road%20tires%204514.jpg"
        ]],
        ["name" => "XT6 Cadillac", "image" => "Images/2023-cadillac-xt6-exterior-001.jpg", "description" => "The Cadillac XT6, a stylish and refined SUV with three rows of seating.",
        "custom_images" => [
            "stock" => "Images/2023-cadillac-xt6-exterior-001.jpg",
            "black" => "Images/Firefly%20Golden%20XT6%20Cadillac%20with%20black%20Rims%2089686.jpg",
            "chrome" => "Images/Firefly%20Golden%20XT6%20Cadillac%20with%20Chrome%20Rims%2089686.jpg",
            "offroad" => "Images/Firefly%20Golden%20XT6%20Cadillac%20with%20Off%20road%20tires%2089686.jpg"
        ]],
    ];

    foreach ($Suvs as $index => $Suv): ?>
        <div class="polaroid">
            <img src="<?= $Suv['image'] ?>" alt="<?= $Suv['name'] ?>" id="suv-image-<?= $index ?>">
            <div class="container">
                <p><?= $Suv['name'] ?></p>
                <p><?= $Suv['description'] ?></p>
 <select onchange="changeImage(<?= $index ?>, this.value)">
                    <option value="stock">Stock Wheels</option>
                    <option value="black">Black Rims</option>
                    <option value="chrome">Chrome Rims</option>
                    <option value="offroad">Off-Road Tires</option>
                </select>
                <br><br>
                <select onchange="changeImage(<?= $index ?>, this.value)">
                <option value="Defult">Defult</option>
                    <option value="Candy Red">Candy Red</option>
                    <option value="Perl Blue">Perl Blue</option>
                    <option value="Detona Green">Detona green</option>
                    <option value="Black Sparide Matte">Black Sparide Matte</option>
                </select>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        function changeImage(index, value) {
            const suvImages = <?= json_encode(array_column($Suvs, 'custom_images')) ?>;
            const imageElement = document.getElementById(`suv-image-${index}`);
            imageElement.src = suvImages[index][value];
        }
    </script>

</body>
</html>