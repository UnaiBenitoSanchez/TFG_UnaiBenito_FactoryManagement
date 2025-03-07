<?php
include '../db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Title -->
    <title>Inventory Management Dashboard - Products</title>

    <?php include '../controller/head.php'; ?>

    <!-- JS -->
    <script src="../js/dashboard.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/dashboard.css">

</head>

<body>

    <?php include '../controller/navbar.php'; ?>

    <div class="container mt-4 mb-5 col-lg-10 z">
        <div class="row" id="products-container">

        </div>
    </div>

    <footer class="bg-body-tertiary text-center text-lg-start fixed-bottom" id="addProductFooter" style=" box-shadow: inset 0px 10px 4px 0 rgba(20, 20, 20, 0.2);">
        <div class="container mt-3">
            <div id="footerIndicator" class="footer-indicator"></div>
            <center>
                <h5>Add New Product</h5>
            </center>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="product_name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name">
                </div>
                <div class="mb-3">
                    <label for="product_description" class="form-label">Product Description</label>
                    <textarea class="form-control" id="product_description" name="product_description"></textarea>
                </div>
                <div class="mb-3">
                    <label for="product_price" class="form-label">Product Price</label>
                    <input type="number" step="0.01" class="form-control" id="product_price" name="product_price">
                </div>
                <div class="mb-3">
                    <label for="product_image" class="form-label">Product Image</label>
                    <input type="file" class="form-control" id="product_image" name="product_image">
                </div>
                <div class="mb-3">
                    <label for="product_quantity" class="form-label">Initial Quantity</label>
                    <input type="number" class="form-control" id="product_quantity" name="product_quantity">
                </div>
                <button type="submit" class="btn btn-primary" name="addProd">Add Product</button>
            </form>
        </div>
    </footer>

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addProd"])) {
        $productName = $_POST["product_name"];
        $productDescription = $_POST["product_description"];
        $productPrice = $_POST["product_price"];
        $initialQuantity = $_POST["product_quantity"];

        $bossEmail = $_SESSION['user_email'];

        if (isset($_FILES["product_image"]) && $_FILES["product_image"]["error"] == 0) {
            $nombreArchivo = basename($_FILES["product_image"]["name"]);
        }

        $nombreArchivo1 = "img/" . $nombreArchivo;

        try {
            $stmt = $conn->prepare("INSERT INTO product (name, description, price, image, category_id_category) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$productName, $productDescription, $productPrice, $nombreArchivo1]);

            $productId = $conn->lastInsertId();

            $stmt = $conn->prepare("SELECT factory_id_factory FROM factory_boss WHERE boss_id_boss_factory = (SELECT id_boss_factory FROM boss WHERE email = :email)");
            $stmt->bindParam(':email', $bossEmail);
            $stmt->execute();
            $factoryId = $stmt->fetchColumn();

            $stmt = $conn->prepare("INSERT INTO inventory (available_quantity, update_date, product_id_product, factory_id_factory) VALUES (?, CURRENT_DATE, ?, ?)");
            $stmt->execute([$initialQuantity, $productId, $factoryId]);

            $stmt = $conn->prepare("INSERT INTO inventory_history (product_id_product, change_quantity, change_type) VALUES (?, ?, 'Add')");
            $stmt->execute([$productId, $initialQuantity]);

            $subtractEventSQL = "
                CREATE EVENT IF NOT EXISTS subtract_quantity_event_$productName
                ON SCHEDULE EVERY 1 HOUR
                DO
                BEGIN

                  SET @current_quantity := (SELECT available_quantity FROM BootstrapWebsite.inventory WHERE product_id_product = (SELECT id_product FROM BootstrapWebsite.product WHERE name = '$productName'));
                
                  UPDATE BootstrapWebsite.inventory
                  SET available_quantity = GREATEST(available_quantity - RAND()*(100-50)+50, 0)
                  WHERE product_id_product = (SELECT id_product FROM BootstrapWebsite.product WHERE name = '$productName');
                
                  INSERT INTO BootstrapWebsite.inventory_history (product_id_product, change_quantity, change_type)
                  VALUES ((SELECT id_product FROM BootstrapWebsite.product WHERE name = '$productName'),(SELECT available_quantity FROM BootstrapWebsite.inventory WHERE product_id_product = (SELECT id_product FROM BootstrapWebsite.product WHERE name = '$productName')), 'Subtract');
                END;
                ";
            $stmt = $conn->prepare($subtractEventSQL);
            $stmt->execute();

            $addEventSQL = "
                CREATE EVENT IF NOT EXISTS add_quantity_event_$productName
                ON SCHEDULE EVERY 1 HOUR
                DO
                BEGIN
           
                  SET @current_quantity := (SELECT available_quantity FROM BootstrapWebsite.inventory WHERE product_id_product = (SELECT id_product FROM BootstrapWebsite.product WHERE name = '$productName'));
                
                  UPDATE BootstrapWebsite.inventory
                  SET available_quantity = available_quantity + RAND()*(100-50)+50
                  WHERE product_id_product = (SELECT id_product FROM BootstrapWebsite.product WHERE name = '$productName');
                
                  INSERT INTO BootstrapWebsite.inventory_history (product_id_product, change_quantity, change_type)
                  VALUES ((SELECT id_product FROM BootstrapWebsite.product WHERE name = '$productName'), (SELECT available_quantity FROM BootstrapWebsite.inventory WHERE product_id_product = (SELECT id_product FROM BootstrapWebsite.product WHERE name = '$productName')), 'Add');
                
                  END;
                ";

            $stmt = $conn->prepare($addEventSQL);
            $stmt->execute();
            $conn = null;
        } catch (PDOException $e) {
            echo "Error inserting data: " . $e->getMessage();
        }
        exit();
    }
    $conn = null;
    ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var addProductFooter = document.getElementById("addProductFooter");

            addProductFooter.addEventListener("click", function(event) {
                if (event.target.tagName.toLowerCase() === 'input' || event.target.tagName.toLowerCase() === 'textarea') {
                    event.stopPropagation();
                } else {
                    this.classList.toggle("expanded");
                }
            });

        });
    </script>

</body>

</html>