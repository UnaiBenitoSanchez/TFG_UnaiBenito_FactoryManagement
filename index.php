<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- meta --> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.18.0/font/bootstrap-icons.css">

    <!-- js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

    <!-- css -->
    <link rel="stylesheet" href="css/index.css">

    <!-- title -->
    <title>Inventory management dashboard - Login/Register</title>

</head>

<body>

    <div class="container">

        <!-- Signup Section -->
        <div class="signup-section">
            <header style="margin-bottom: -40px">Signup</header>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="margin-top: 90px;">
                <input type="text" name="fullname" id="fullname" placeholder="Full name" required>
                <input type="email" name="email" id="email" placeholder="Email address" required>
                <input type="password" name="password" id="password" placeholder="Password" required>

                <label for="factory" style="color: white;">Select your factory:</label>
                <select name="factory" id="factory" required>
                    <?php
                    $stmt = $conn->query("SELECT id_factory, name FROM factory");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id_factory']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>

                <div class="separator">
                    <div class="line"></div>
                </div>
                <button type="submit" class="btn" name="signup" style="background-color: white;">Signup</button>
            </form>

            <?php
            // Signup Logic
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {

                if (empty($_POST['fullname']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['factory'])) {
                    echo "<p style='color: #ffffff'>Please fill in all fields, including the factory.<p>";
                } else {
                    $fullname = $_POST['fullname'];
                    $email = $_POST['email'];
                    $password = $_POST['password'];
                    $factoryId = $_POST['factory'];

                    // Encrypt the password
                    $encryptedPassword = password_hash($password, PASSWORD_BCRYPT);

                    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
                        echo "<p style='color: #ffffff'>Password must contain at least one letter, one number, and be at least 8 characters long.</p>";
                    } else {
                        try {
                            $stmt = $conn->prepare("INSERT INTO boss (name, email, password) VALUES (:fullname, :email, :password)");
                            $stmt->bindParam(':fullname', $fullname);
                            $stmt->bindParam(':email', $email);
                            $stmt->bindParam(':password', $encryptedPassword);
                            $stmt->execute();

                            $bossId = $conn->lastInsertId(); // Get the new boss ID

                            if (!$bossId) {
                                echo "Error: Boss insertion failed.";
                                exit();
                            }

                            $stmt = $conn->prepare("INSERT INTO factory_boss (factory_id_factory, boss_id_boss_factory) VALUES (:factoryId, :bossId)");
                            $stmt->bindParam(':factoryId', $factoryId);
                            $stmt->bindParam(':bossId', $bossId); // Use the new boss ID
                            $stmt->execute();

                            // Create JavaScript file with template code
                            $canvasId = "c_$bossId"; // Dynamic canvas ID
                            $newJsFileName = "./js/boss$bossId.js";
                            $template = file_get_contents("./js/template.js"); // Contains the JavaScript template code

                            // Replace the canvas ID in the template
                            $scriptContent = str_replace("c_1", $canvasId, $template);

                            // Save the updated content in the JavaScript file
                            file_put_contents($newJsFileName, $scriptContent);

                            echo "<p style='color: white;'>User registered successfully, redirecting...</p>";
                            session_start();
                            $_SESSION['user_email'] = $email;
                            echo '<script>
                                        setTimeout(function() {
                                            window.location.href = "./php/landing_page.php";
                                        }, 3000); // 3-second delay
                                    </script>';
                            exit();
                        } catch (PDOException $e) {
                            echo "<p style='color: #ffffff'>Error: " . $e->getMessage() . "</p>";
                            echo "<script>console.log(" . json_encode($e) . ")</script>";
                        }
                    }
                }
            }
            ?>
        </div>

        <!-- Login Section -->
        <div class="login-section">
            <header>Login</header>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="margin-top: 90px;">
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="password" placeholder="Password" required>
                <div class="separator">
                    <div class="line"></div>
                </div>
                <button type="submit" class="btn" name="login">Login</button>
            </form>

            <?php
            // Login Logic
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {

                if (empty($_POST['email']) || empty($_POST['password'])) {
                    echo "<p style='color: #ffffff'>Please fill in all fields.<p>";
                } else {
                    $email = $_POST['email'];
                    $password = $_POST['password'];

                    try {
                        $stmt = $conn->prepare("SELECT * FROM boss WHERE email = :email");
                        $stmt->bindParam(':email', $email);
                        $stmt->execute();
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($user) {
                            // Verify if the password is correct
                            if (password_verify($password, $user['password'])) {
                                session_start();
                                $_SESSION['user_email'] = $email;
                                echo '<script>window.location.href = "./php/landing_page.php";</script>';
                                exit();
                            } else {
                                echo "Incorrect password";
                            }
                        } else {
                            echo "User not found";
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
            }
            ?>
        </div>

    </div>

    <script src="js/index.js"></script>

</body>

</html>
