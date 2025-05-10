<?php
require_once '../db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['id_product'];
$verified = $data['verified'];

try {
    $sql = "UPDATE product SET verified = :verified WHERE id_product = :id_product";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':verified', $verified, PDO::PARAM_BOOL);
    $stmt->bindParam(':id_product', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
