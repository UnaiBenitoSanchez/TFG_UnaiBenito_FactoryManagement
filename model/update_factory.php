<?php
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $factoryId = $_POST['factoryId'];
        $editedEmployeeCount = $_POST['editedEmployeeCount'];

        $updateSql = "UPDATE factory SET employee_count = :editedEmployeeCount WHERE id_factory = :factoryId";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindParam(':factoryId', $factoryId);
        $updateStmt->bindParam(':editedEmployeeCount', $editedEmployeeCount);
        $updateStmt->execute();

        echo "Factory information updated successfully.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        $conn = null;
    }
}
?>
