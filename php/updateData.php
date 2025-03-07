<?php
require_once '../db_connect.php';

// Retrieve data from the POST request
$idProduct = $_POST['id_product'];
$newName = $_POST['new_name'];
$newDesc = $_POST['new_description'];
$newQuantity = $_POST['new_quantity'];
$newPrice = $_POST['new_price'];

try {
    // Begin a database transaction
    $conn->beginTransaction();

    // Update product information
    $stmtProduct = $conn->prepare("UPDATE BootstrapWebsite.product SET name = :newName, description = :newDescription, price = :newPrice WHERE id_product = :idProduct");
    $stmtProduct->bindParam(':newName', $newName);
    $stmtProduct->bindParam(':newDescription', $newDesc);
    $stmtProduct->bindParam(':newPrice', $newPrice);
    $stmtProduct->bindParam(':idProduct', $idProduct);
    $stmtProduct->execute();

    // Update inventory information
    $stmtInventory = $conn->prepare("UPDATE BootstrapWebsite.inventory SET available_quantity = :newQuantity WHERE product_id_product = :idProduct");
    $stmtInventory->bindParam(':newQuantity', $newQuantity);
    $stmtInventory->bindParam(':idProduct', $idProduct);
    $stmtInventory->execute();

    // Commit the transaction if all queries are successful
    $conn->commit();

    // Display success message
    echo "Data updated";
} catch (PDOException $e) {
    // Rollback the transaction in case of an error
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
} finally {
    // Close the database connection
    $conn = null;
}
