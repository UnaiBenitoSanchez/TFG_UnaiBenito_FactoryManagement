<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_role'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$bossEmail = $_SESSION['user_email'];

try {
    $stmt = $conn->prepare("SELECT id_boss_factory FROM boss WHERE email = :email");
    $stmt->bindParam(':email', $bossEmail);
    $stmt->execute();
    $boss = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$boss) {
        throw new Exception("Boss not found");
    }

    $bossId = $boss['id_boss_factory'];

    $stmt = $conn->prepare("SELECT factory_id_factory FROM factory_boss WHERE boss_id_boss_factory = :boss_id");
    $stmt->bindParam(':boss_id', $bossId);
    $stmt->execute();
    $factoryBoss = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$factoryBoss) {
        throw new Exception("Factory not found for this boss.");
    }

    $factoryId = $factoryBoss['factory_id_factory'];

    $stmt = $conn->prepare("
        SELECT e.id_employee as id, e.is_logged_in 
        FROM employee e
        JOIN factory_employee fe ON e.id_employee = fe.employee_id_employee
        WHERE fe.factory_id_factory = :factory_id
    ");
    $stmt->bindParam(':factory_id', $factoryId);
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($employees);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
