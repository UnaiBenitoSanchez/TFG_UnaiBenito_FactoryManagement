<?php
ob_start();
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .session-popup {
            position: fixed;
            bottom: 60px;
            left: 15px;
            background-color: rgba(83, 226, 70, 0.9);
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
            border: 1px solid #ddd;
        }

        .session-popup .close-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 12px;
        }
    </style>

</head>

<body>

    <?php include '../controller/navbar.php'; ?>

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addProd"])) {
        $productName = $_POST["product_name"];
        $productDescription = $_POST["product_description"];
        $productPrice = $_POST["product_price"];
        $initialQuantity = $_POST["product_quantity"];

        $bossEmail = $_SESSION['user_email'];

        if (isset($_FILES["product_image"]) && $_FILES["product_image"]["error"] == 0) {
            $nameFile = basename($_FILES["product_image"]["name"]);
        }

        $nameFile1 = "img/" . $nameFile;

        $stmt = $conn->prepare("SELECT COUNT(*) FROM product WHERE name = ?");
        $stmt->execute([$productName]);
        $productExists = $stmt->fetchColumn();

        if ($productExists > 0) {
            echo "<div class='alert alert-danger text-center'>Error: There is already a product with this name on the factory.</div>";
        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO product (name, description, price, image, category_id_category) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([$productName, $productDescription, $productPrice, $nameFile1]);
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

                    SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = '$productName'));
                    
                    UPDATE GestionDeFabricas.inventory
                    SET available_quantity = GREATEST(available_quantity - RAND()*(100-50)+50, 0)
                    WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = '$productName');
                    
                    INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
                    VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = '$productName'),(SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = '$productName')), 'Subtract');
                    END;
                    ";
                $stmt = $conn->prepare($subtractEventSQL);
                $stmt->execute();

                $addEventSQL = "
                    CREATE EVENT IF NOT EXISTS add_quantity_event_$productName
                    ON SCHEDULE EVERY 1 HOUR
                    DO
                    BEGIN
            
                    SET @current_quantity := (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = '$productName'));
                    
                    UPDATE GestionDeFabricas.inventory
                    SET available_quantity = available_quantity + RAND()*(100-50)+50
                    WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = '$productName');
                    
                    INSERT INTO GestionDeFabricas.inventory_history (product_id_product, change_quantity, change_type)
                    VALUES ((SELECT id_product FROM GestionDeFabricas.product WHERE name = '$productName'), (SELECT available_quantity FROM GestionDeFabricas.inventory WHERE product_id_product = (SELECT id_product FROM GestionDeFabricas.product WHERE name = '$productName')), 'Add');
                    
                    END;
                    ";

                $stmt = $conn->prepare($addEventSQL);
                $stmt->execute();
                $conn = null;
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } catch (PDOException $e) {
                echo "Error inserting data: " . $e->getMessage();
            }
            exit();
        }
    }
    $conn = null;
    ?>

    <div class="container mt-4 mb-5 col-lg-10 z">
        <div class="row" id="products-container">

        </div>
    </div>

    <footer class="text-center text-lg-start fixed-bottom" id="addProductFooter">
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

    <?php include '../controller/session.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sessionPopup = document.querySelector('.session-popup');

            if (sessionPopup) {
                setTimeout(function() {
                    sessionPopup.style.transition = 'opacity 0.5s ease-out';
                    sessionPopup.style.opacity = '0';

                    setTimeout(function() {
                        sessionPopup.remove();
                    }, 500);
                }, 5000);
            }
        });
    </script>

</body>

<?php ob_end_flush(); ?>

</html>