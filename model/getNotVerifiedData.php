<?php
require_once '../db_connect.php';
session_start();

try {
    $bossEmail = $_SESSION['user_email'];

    $sql = "SELECT p.id_product, p.name, p.description, p.price, p.image, i.available_quantity
            FROM product p
            INNER JOIN inventory i ON p.id_product = i.product_id_product
            INNER JOIN factory_boss fb ON fb.factory_id_factory = i.factory_id_factory
            INNER JOIN boss b ON b.id_boss_factory = fb.boss_id_boss_factory
            WHERE b.email = :email AND p.verified = FALSE";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $bossEmail);
    $stmt->execute();

    $data = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    echo json_encode($data);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conn = null;
}
