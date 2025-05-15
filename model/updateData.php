<?php
require_once '../db_connect.php';

$idProduct = $_POST['id_product'];
$newName = $_POST['new_name'];
$newDesc = $_POST['new_description'];
$newQuantity = $_POST['new_quantity'];
$newPrice = $_POST['new_price'];

try {
    $conn->beginTransaction();

    $stmtProduct = $conn->prepare("UPDATE product SET name = :newName, description = :newDescription, price = :newPrice WHERE id_product = :idProduct");
    $stmtProduct->bindParam(':newName', $newName);
    $stmtProduct->bindParam(':newDescription', $newDesc);
    $stmtProduct->bindParam(':newPrice', $newPrice);
    $stmtProduct->bindParam(':idProduct', $idProduct);
    $stmtProduct->execute();

    $stmtInventory = $conn->prepare("UPDATE inventory SET available_quantity = :newQuantity WHERE product_id_product = :idProduct");
    $stmtInventory->bindParam(':newQuantity', $newQuantity);
    $stmtInventory->bindParam(':idProduct', $idProduct);
    $stmtInventory->execute();

    $conn->commit();

    echo "Data updated";
} catch (PDOException $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
} finally {
    $conn = null;
}
