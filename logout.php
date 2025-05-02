<?php
// Start or resume the session
session_start();

if (isset($_SESSION['user_role'])) {
    require_once 'db_connect.php';
    
    if ($_SESSION['user_role'] === 'employee' && isset($_SESSION['employee_id'])) {
        $stmt = $conn->prepare("UPDATE employee SET is_logged_in = FALSE WHERE id_employee = :id");
        $stmt->bindParam(':id', $_SESSION['employee_id']);
        $stmt->execute();
    }
}

session_unset();
session_destroy();

header("Location: index.php");
exit;