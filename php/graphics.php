<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../controller/head.php'; ?>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <?php
    include '../db_connect.php';
    session_start();

    $user_email = $_SESSION['user_email'];

    $factoryIdStmt = $conn->prepare("SELECT fb.factory_id_factory
                               FROM factory_boss fb
                               INNER JOIN boss b ON fb.boss_id_boss_factory = b.id_boss_factory
                               WHERE b.email = :user_email");
    $factoryIdStmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);
    $factoryIdStmt->execute();
    $factoryId = $factoryIdStmt->fetch(PDO::FETCH_ASSOC)['factory_id_factory'];

    $productsStmt = $conn->prepare("SELECT p.id_product, p.name
                              FROM product p
                              INNER JOIN inventory i ON p.id_product = i.product_id_product
                              WHERE i.factory_id_factory = :factory_id");
    $productsStmt->bindParam(':factory_id', $factoryId, PDO::PARAM_INT);
    $productsStmt->execute();

    $productsData = array();

    while ($product = $productsStmt->fetch(PDO::FETCH_ASSOC)) {
        $productId = $product['id_product'];
        $productName = $product['name'];

        $historyStmt = $conn->prepare("SELECT ih.change_timestamp, ih.change_quantity 
                            FROM inventory_history ih 
                            INNER JOIN inventory i ON ih.product_id_product = i.product_id_product
                            WHERE i.product_id_product = :product_id");
        $historyStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $historyStmt->execute();

        $chartData = array();
        $chartData[] = array('Timestamp', 'Quantity');

        while ($row = $historyStmt->fetch(PDO::FETCH_ASSOC)) {
            $timestamp = date("Y-m-d H:i:s", strtotime($row['change_timestamp']));
            $chartData[] = array($timestamp, (int)$row['change_quantity']);
        }

        $productsData[$productId] = array(
            'name' => $productName,
            'chartData' => $chartData
        );
    }
    ?>

    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });

        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            <?php
            foreach ($productsData as $productId => $productInfo) {
                $productName = $productInfo['name'];
                $chartData = $productInfo['chartData'];
            ?>

                var options_<?php echo $productId; ?> = {
                    title: 'Product: <?php echo $productName; ?>',
                    curveType: 'function',
                    legend: {
                        position: 'bottom'
                    },
                    backgroundColor: '#303f9f',
                    titleTextStyle: {
                        color: '#fff',
                    },
                    legendTextStyle: {
                        color: '#fff',
                    },
                    hAxis: {
                        textStyle: {
                            color: '#fff'
                        }
                    },
                    vAxis: {
                        textStyle: {
                            color: '#fff'
                        }
                    },
                    colors: ['#2081C3']
                };


                var chart_<?php echo $productId; ?> = new google.visualization.LineChart(document.getElementById('curve_chart_<?php echo $productId; ?>'));
                var data_<?php echo $productId; ?> = google.visualization.arrayToDataTable(<?php echo json_encode($chartData); ?>);
                chart_<?php echo $productId; ?>.draw(data_<?php echo $productId; ?>, options_<?php echo $productId; ?>);

            <?php
            }
            ?>
        }
    </script>

    <!-- title -->
    <title>Inventory management dashboard - Graphics</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/graphics.css">
    <link rel="stylesheet" href="../css/session.css">

</head>

<body>
    <?php include '../controller/navbar.php'; ?>

    <div class="container-fluid mt-4 col-lg-10">
        <div class="row">
            <?php
            $productsStmt->execute();
            while ($product = $productsStmt->fetch(PDO::FETCH_ASSOC)) {
                $productId = $product['id_product'];
            ?>
                <div class="col-lg-4">
                    <div id="curve_chart_<?php echo $productId; ?>" style="width: 100%; height: 400px; margin-bottom: 20px;"></div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <?php
    $conn = null;
    ?>

    <?php include '../controller/session.php'; ?>

</body>

</html>