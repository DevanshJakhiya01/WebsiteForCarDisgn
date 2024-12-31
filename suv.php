<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUVS</title>
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
    <h1>Welcome to SUVS!</h1>
    <p>This is the SUV page.</p>

    <?php
    
    $suvs = [
        ["image" => "suv1.jpg", "caption" => "SUV Model 1"], 
        ["image" => "suv2.jpg", "caption" => "SUV Model 2"],
        ["image" => "suv3.jpg", "caption" => "SUV Model 3"],
    ];

    foreach ($suvs as $suv): ?>
        <div class="polaroid">
            <img src="<?php echo $suv['image']; ?>" alt="<?php echo $suv['caption']; ?>">
            <div class="container">
                <p><?php echo $suv['caption']; ?></p>
            </div>
        </div>
    <?php endforeach; ?>

    <input type="button" value="Explore More">

</body>
</html>