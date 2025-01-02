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

        #wheel-type-select {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 20px;
            background-color: white; 
            color: black; 
        }
        #wheel-type-select:focus{
            outline: none;
            border: 1px solid blue;
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
        ["image" => "Images/El nuevo Volkswagen Polo llegará a México en 2023.jpeg", "caption" => "Volkswagen Polo"],
        ["image" => "Images/audi%20A3.jpg", "caption" => "Audi A3"],
        ["image" => "Images/2024%20Lexus%20ES.jpg", "caption" => "Lexus ES"],
    ];

    foreach ($sedans as $sedan): ?>
        <div class="polaroid">
            <img src="<?php echo $sedan['image']; ?>" alt="<?php echo $sedan['caption']; ?>">
            <div class="container">
                <p><?php echo $sedan['caption']; ?></p>
            </div>
        </div>
    <?php endforeach; ?>

    <select id="wheel-type-select">
        <option value="">Select Wheel Type</option>
        <option value="alloy">Alloy Wheels</option>
        <option value="steel">Steel Wheels</option>
        <option value="custom">Custom Wheels</option>
        </select>
    <script>
        const selectElement = document.getElementById('wheel-type-select');
        selectElement.addEventListener('change', (event) => {
            const selectedValue = event.target.value;
            console.log(`Selected wheel type: ${selectedValue}`);
        });
    </script>

</body>
</html>