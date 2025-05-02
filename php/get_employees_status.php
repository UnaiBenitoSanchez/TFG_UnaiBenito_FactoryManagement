<?php
session_start();
require_once '../db_connect.php';

if (!isset($_GET['factory_id'])) {
    die(json_encode([]));
}

$factoryId = $_GET['factory_id'];

try {
    $stmt = $conn->prepare("
        SELECT e.id_employee, e.is_logged_in 
        FROM employee e
        JOIN factory_employee fe ON e.id_employee = fe.employee_id_employee
        WHERE fe.factory_id_factory = :factory_id
    ");
    $stmt->bindParam(':factory_id', $factoryId);
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($employees);
} catch (PDOException $e) {
    die(json_encode([]));
}