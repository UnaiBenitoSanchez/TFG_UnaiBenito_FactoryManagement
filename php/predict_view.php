<?php
$output = shell_exec("python3 ../python/ml_predictor.py");
$data = json_decode($output, true);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../controller/head.php'; ?>
    <?php include '../controller/navbar.php'; ?>
    <meta charset="UTF-8">
    <title>Demand Prediction</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(45deg, #F7F9F9, #BED8D4, #78D5D7, #63D2FF, #2081C3);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }
            25% {
                background-position: 100% 50%;
            }
            50% {
                background-position: 0% 100%;
            }
            75% {
                background-position: 100% 100%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container-fluid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px;
        }

        .page-title {
            text-align: center;
            color: #303f9f;
            margin-top: 40px;
            margin-bottom: 20px;
            font-size: 32px;
            font-weight: 700;
        }

        .card,
        .prediction-card {
            background-color: rgba(48, 63, 159, 0.9);
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
            padding: 20px;
            text-align: center;
            color: #fff;
            border: 2px solid #2081C3;
            overflow-y: auto;
            animation: fadeIn 0.5s ease-in-out;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover,
        .prediction-card:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.5);
        }

        .card {
            width: 300px;
            height: 80px;
            max-height: 400px;
        }

        .prediction-card {
            margin-left: 10px;
            margin-top: 10px;
            width: 350px;
            height: 200px;
        }

        .card::-webkit-scrollbar,
        .prediction-card::-webkit-scrollbar {
            width: 8px;
        }

        .card::-webkit-scrollbar-track,
        .prediction-card::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .card::-webkit-scrollbar-thumb,
        .prediction-card::-webkit-scrollbar-thumb {
            background: #2081C3;
            border-radius: 8px;
        }

        .card::-webkit-scrollbar-thumb:hover,
        .prediction-card::-webkit-scrollbar-thumb:hover {
            background: #63D2FF;
        }

        .card h2,
        .prediction-card h2 {
            font-size: 15px;
            font-weight: 600;
            color: #BED8D4;
            margin-bottom: 10px;
        }

        .prediction-title {
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            color: #F0F8FF;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .prediction-value {
            font-size: 18px;
            font-weight: 700;
            color: #FFD700;
        }

        .prediction-card h1 {
            font-size: 20px; 
            font-weight: 600;
            color: #F0F8FF; 
            margin-bottom: 10px;
        }

        .card p {
            font-size: 16px;
            color: #fff;
            opacity: 0.95;
        }

        @media (max-width: 768px) {
            .card,
            .prediction-card {
                width: 90%;
                height: auto;
            }
        }
    </style>
</head>

<body>
    <h1 class="page-title">Demand Prediction Results</h1>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <?php if ($data && isset($data['predictions'])): ?>
                <?php foreach ($data['predictions'] as $id => $prediction): ?>
                    <div class="card prediction-card">
                        <h1>Estimated demand of the product:</h1>
                        <h2 class="prediction-title"><?php echo htmlspecialchars($prediction['name']); ?></h2>
                        <p class="prediction-value" style="color: #FFD700;"><?php echo htmlspecialchars($prediction['prediction']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card prediction-card">
                    <h2>Error</h2>
                    <p>Could not retrieve prediction data.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 