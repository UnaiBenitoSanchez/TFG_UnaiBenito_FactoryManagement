<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <title>Secure Inventory Management - Login/Register</title>
</head>

<body>
    <div class="container">
        <div class="signup-section">
            <header>Signup</header>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <input type="text" name="fullname" placeholder="Full name" required>
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="password" placeholder="Password" required>
                <label>Select your factory:</label>
                <select name="factory" required>
                    <?php
                    $stmt = $conn->query("SELECT id_factory, name FROM factory");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id_factory']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="signup">Signup</button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {
                if (!empty($_POST['fullname']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['factory'])) {
                    $fullname = $_POST['fullname'];
                    $email = $_POST['email'];
                    $password = $_POST['password'];
                    $factoryId = $_POST['factory'];

                    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
                        echo "<p>Password must contain at least one letter, one number, and be at least 8 characters long.</p>";
                    } else {
                        $encryptedPassword = password_hash($password, PASSWORD_BCRYPT);

                        try {
                            $stmt = $conn->prepare("INSERT INTO boss (name, email, password) VALUES (:fullname, :email, :password)");
                            $stmt->bindParam(':fullname', $fullname);
                            $stmt->bindParam(':email', $email);
                            $stmt->bindParam(':password', $encryptedPassword);
                            $stmt->execute();

                            $bossId = $conn->lastInsertId();

                            $stmt = $conn->prepare("INSERT INTO factory_boss (factory_id_factory, boss_id_boss_factory) VALUES (:factoryId, :bossId)");
                            $stmt->bindParam(':factoryId', $factoryId);
                            $stmt->bindParam(':bossId', $bossId);
                            $stmt->execute();

                            echo "<p>User registered successfully, redirecting...</p>";
                            session_start();
                            $_SESSION['user_email'] = $email;
                            echo '<script>setTimeout(() => window.location.href = "./php/landing_page.php", 3000);</script>';
                            exit();
                        } catch (PDOException $e) {
                            echo "<p>Error: " . $e->getMessage() . "</p>";
                        }
                    }
                } else {
                    echo "<p>Please fill in all fields.<p>";
                }
            }
            ?>
        </div>

        <div class="login-section">
            <header>Login</header>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
                if (!empty($_POST['email']) && !empty($_POST['password'])) {
                    $email = $_POST['email'];
                    $password = $_POST['password'];

                    try {
                        $stmt = $conn->prepare("SELECT * FROM boss WHERE email = :email");
                        $stmt->bindParam(':email', $email);
                        $stmt->execute();
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($user && password_verify($password, $user['password'])) {
                            session_start();
                            $_SESSION['user_email'] = $email;
                            echo '<script>window.location.href = "./php/landing_page.php";</script>';
                            exit();
                        } else {
                            echo "Incorrect email or password.";
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                } else {
                    echo "<p>Please fill in all fields.<p>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
