<?php
ob_start();
include '../db_connect.php';
session_start();

if (!isset($_SESSION['user_email'])) {
    die("Error: No email found for this user. Please, log in.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Title -->
    <title>Inventory Management Dashboard - Products</title>

    <?php include '../controller/head.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS -->
    <script src="../js/dashboardEmployee.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/dashboard.css">

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

    <!-- Navbar -->
    <link rel="stylesheet" href="../css/navbar.css">

    <!-- Style -->
    <style>
        /* Navbar */
        .nav-logout-inline {
            color: white;
            background-color: rgb(203, 35, 35);
            text-decoration: none;
            transition: color 0.3s, background-color 0.3s;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .nav-logout-inline:hover {
            background-color: rgb(255, 90, 90);
            color: #fff;
        }

        /* Modal */
        .modalError {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modalError-content {
            background-color: rgba(48, 63, 159, 0.9);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
            width: 80%;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
            color: #fff;
        }

        .close-btn {
            color: #fff;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: #63D2FF;
            text-decoration: none;
        }

        /* Estilos para el modal de confirmación */
        .custom-confirm-modal .modal-dialog {
            max-width: 400px;
        }

        .custom-confirm-modal .modal-content {
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            border: none;
        }

        .custom-confirm-modal .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            padding: 15px 20px;
        }

        .custom-confirm-modal .modal-title {
            font-weight: 600;
            color: #212529;
        }

        .custom-confirm-modal .modal-body {
            padding: 20px;
            font-size: 16px;
            background-color: #63D2FF;
        }

        .custom-confirm-modal .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #dee2e6;
        }

        /* Botones en el modal */
        .custom-confirm-modal .btn {
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .custom-confirm-modal .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .custom-confirm-modal .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .custom-confirm-modal .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .custom-confirm-modal .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        /* Animación del modal */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: translateY(-50px);
        }

        .modal.show .modal-dialog {
            transform: translateY(0);
        }

        /* Overlay del modal más oscuro */
        .modal-backdrop.show {
            opacity: 0.6;
        }

        /* Estilo para el modal de salir sin guardar */
        #unsavedChangesModal .modal-header {
            background-color:rgb(56, 135, 215);
            color: #721c24;
        }

        /* Estilo para el modal de eliminar producto */
        #deleteProductModal .modal-header {
            background-color:rgb(56, 135, 215);
            color: #721c24;
        }

        #deleteProductModal .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>

    <script>
        function toggleNavbar() {
            var navbarNav = document.getElementById('navbarNav');
            navbarNav.classList.toggle('show');
        }
    </script>

</head>

<body>

    <nav class="navbar">
        <p class="navbar-brand" style="font-size: 20px; margin-top: 12px;">TFG_UnaiBenitoSánchez</p>
        <button class="navbar-toggler" onclick="toggleNavbar()" style="color: black;">☰</button>
        <ul class="navbar-nav" id="navbarNav">
            <li class="nav-item">
                <a class="nav-link" href="employee_dashboard.php">Products from your factory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="chatEmployee.php">Chat</a>
            </li>
            <li class="nav-item" style="margin-top: 8px;">
                <a class="nav-logout-inline" href="../logout.php">Logout</a>
            </li>
        </ul>
    </nav>

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addProd"])) {
        $productName = $_POST["product_name"];
        $productDescription = $_POST["product_description"];
        $productPrice = $_POST["product_price"];
        $initialQuantity = $_POST["product_quantity"];

        $bossEmail = $_SESSION['user_email'];

        if (isset($_FILES["product_image"]) && $_FILES["product_image"]["error"] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES["product_image"]["type"];

            if (!in_array($fileType, $allowedTypes)) {
                die("Error: Solo se permiten imágenes JPEG, PNG o GIF.");
            }

            if ($_FILES["product_image"]["size"] > 2000000) {
                die("Error: La imagen es demasiado grande (máx 2MB).");
            }

            $nameFile = preg_replace("/[^a-zA-Z0-9\.\-]/", "", basename($_FILES["product_image"]["name"]));
            $extension = strtolower(pathinfo($nameFile, PATHINFO_EXTENSION));
            $nameFile = uniqid() . '.' . $extension;

            $targetFile = "../img/" . $nameFile;
            if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile)) {
                die("Error al subir la imagen.");
            }
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

                $stmt = $conn->prepare("SELECT factory_id_factory FROM factory_employee WHERE employee_id_employee = (SELECT id_employee FROM employee WHERE email = :email)");
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