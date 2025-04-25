<?php

require_once '../db_connect.php';
session_start();

try {
    $employeeEmail = $_SESSION['user_email'];

    $sqlFactory = "SELECT fe.factory_id_factory
                   FROM factory_employee fe
                   JOIN employee e ON fe.employee_id_employee = e.id_employee
                   WHERE e.email = :email";
    $stmt = $conn->prepare($sqlFactory);
    $stmt->bindParam(':email', $employeeEmail);
    $stmt->execute();
    $factoryId = $stmt->fetchColumn();

    if (!$factoryId) {
        throw new Exception('Factory associated to the employee not found.');
    }

    $sql = "SELECT p.id_product, p.name, p.description, p.price, p.image, i.available_quantity
            FROM product p
            INNER JOIN inventory i ON p.id_product = i.product_id_product
            WHERE i.factory_id_factory = :factory_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':factory_id', $factoryId);
    $stmt->execute();

    $data = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    echo json_encode($data);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conn = null;
}
?>
