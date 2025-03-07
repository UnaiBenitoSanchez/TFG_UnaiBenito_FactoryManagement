<?php
// Include the database connection file
require_once '../db_connect.php';

// Check if the 'id_product' parameter is set in the POST request
if (isset($_POST['id_product'])) {
    $idProduct = $_POST['id_product'];

    try {
        // Begin a transaction
        $conn->beginTransaction();

        // Delete records from the 'inventory_history' table associated with the product
        $sqlProductH = "DELETE FROM inventory_history WHERE product_id_product = $idProduct";
        $conn->exec($sqlProductH);

        // Delete records from the 'inventory' table associated with the product
        $sqlInventory = "DELETE FROM inventory WHERE product_id_product = $idProduct";
        $conn->exec($sqlInventory);

        // Delete the product record from the 'product' table
        $sqlProduct = "DELETE FROM product WHERE id_product = $idProduct";
        $conn->exec($sqlProduct);

        // Commit the transaction if all queries are successful
        $conn->commit();

        echo "Successfully deleted";
    } catch (PDOException $e) {
        // Roll back the transaction and display an error message if any exception occurs
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }

    // Close the database connection
    $conn = null;
} else {
    // If 'id_product' is not set or invalid, display an error message
    echo "Error: 'id_product' not set or invalid.";
}
