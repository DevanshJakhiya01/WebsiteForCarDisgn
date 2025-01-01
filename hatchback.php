<html>
<head>
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

        input[type="button"] {
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

        input[type="button"]:hover {
            background-color: #e9967a;
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
    <h1>Welcome to Hatchbacks!</h1>
    <p>This is the hatchback page.</p>

    <?php
    
     
    $hatchbacks = [
        ["name" => "Suzuki Swift", "image" => "Images/Suzuki_Swift_04.jpg", "description" => "A popular and fuel-efficient hatchback."],
        ["name" => "Honda Civic", "image" => "Images/2023-honda-civic-sdn_100861363_h.jpg", "description" => "Known for its reliability and sporty handling."],
        ["name" => "Volkswagen Golf", "image" => "Images/volkswagen-golf-2020-specs-01.jpg", "description" => "A classic hatchback with a premium feel."],
    ];

    foreach ($hatchbacks as $hatchback): ?>
        <div class="polaroid">
            <img src="<?= $hatchback['image'] ?>" alt="<?= $hatchback['name'] ?>">
            <div class="container">
                <p><?= $hatchback['name'] ?></p>
                <p><?= $hatchback['description'] ?></p>
                <input type="button" value="Learn More">
            </div>
        </div>
    <?php endforeach; ?>

</body>
</html>
