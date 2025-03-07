<?php
// Include the database connection file
include '../db_connect.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Retrieve data from the POST request
        $factoryId = $_POST['factoryId'];
        $editedEmployeeCount = $_POST['editedEmployeeCount'];

        // Prepare and execute the database update query
        $updateSql = "UPDATE factory SET employee_count = :editedEmployeeCount WHERE id_factory = :factoryId";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindParam(':factoryId', $factoryId);
        $updateStmt->bindParam(':editedEmployeeCount', $editedEmployeeCount);
        $updateStmt->execute();

        // Display success message
        echo "Factory information updated successfully.";
    } catch (PDOException $e) {
        // Display error message in case of exception
        echo "Error: " . $e->getMessage();
    } finally {
        // Close the database connection
        $conn = null;
    }
}
?>
