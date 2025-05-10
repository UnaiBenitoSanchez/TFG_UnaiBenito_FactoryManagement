<?php
session_start();
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['employee_id'], $data['status'])) {
        $stmt = $conn->prepare("UPDATE employee SET is_logged_in = :status WHERE id_employee = :id");
        $stmt->bindParam(':id', $data['employee_id']);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_BOOL);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false]);