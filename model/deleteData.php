<?php
require_once '../db_connect.php';

if (isset($_POST['id_product'])) {
    $idProduct = $_POST['id_product'];

    try {
        $conn->beginTransaction();

        $sqlProductH = "DELETE FROM inventory_history WHERE product_id_product = $idProduct";
        $conn->exec($sqlProductH);

        $sqlInventory = "DELETE FROM inventory WHERE product_id_product = $idProduct";
        $conn->exec($sqlInventory);

        $sqlProduct = "DELETE FROM product WHERE id_product = $idProduct";
        $conn->exec($sqlProduct);

        $conn->commit();

        echo "Successfully deleted";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }

    $conn = null;
} else {
    echo "Error: 'id_product' not set or invalid.";
}
