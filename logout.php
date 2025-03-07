<?php
// Start or resume the session
session_start();

// Clear and destroy the session
session_unset();
session_destroy();

// Redirect to the home page
echo '<script>window.location.href = "index.php";</script>';
?>
