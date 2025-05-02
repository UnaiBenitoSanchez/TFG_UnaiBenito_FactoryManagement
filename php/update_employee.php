<?php
require_once '../db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'], $data['name'], $data['email'])) {
    echo json_encode(['success' => false, 'error' => 'Missing data']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE employee SET name = :name, email = :email WHERE id_employee = :id");
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
