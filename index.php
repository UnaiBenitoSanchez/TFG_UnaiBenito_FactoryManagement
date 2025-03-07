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

        <div class="signup-section">
            <header style="margin-bottom: -40px">Signup</header>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="margin-top: 90px;">
                <input type="text" name="fullname" id="fullname" placeholder="Full name">
                <input type="email" name="email" id="email" placeholder="Email address">
                <input type="password" name="password" id="password" placeholder="Password">

                <label for="factory" style="color: white;">Select your factory:</label>
                <select name="factory" id="factory">
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
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {
                function textToBrainfuck($text)
                {
                    $brainfuckCode = "";
                    for ($i = 0; $i < strlen($text); $i++) {
                        $asciiCode = ord($text[$i]);
                        $brainfuckCode .= str_repeat("+", $asciiCode) . ".>";
                    }
                    return $brainfuckCode;
                }

                if (empty($_POST['fullname']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['factory'])) {
                    echo "<p style='color: #ffffff'>Please fill in all fields, including the factory.<p>";
                } else {
                    $fullname = $_POST['fullname'];
                    $email = $_POST['email'];
                    $password = $_POST['password'];
                    $factoryId = $_POST['factory'];
                    $encryptedPassword = textToBrainfuck($password);

                    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
                        echo "<p style='color: #ffffff'>Password must contain at least one letter, one number, and be at least 8 characters long.</p>";
                    } else {
                        if ($factoryId == "1") {
                            try {
                                $stmt = $conn->prepare("INSERT INTO boss (name, email, password) VALUES (:fullname, :email, :password)");
                                $stmt->bindParam(':fullname', $fullname);
                                $stmt->bindParam(':email', $email);
                                $stmt->bindParam(':password', $encryptedPassword);
                                $stmt->execute();

                                $bossId = $conn->lastInsertId(); // Obtén la ID del nuevo jefe

                                if (!$bossId) {
                                    echo "Error: Boss insertion failed.";
                                    exit();
                                }

                                $stmt = $conn->prepare("INSERT INTO factory_boss (factory_id_factory, boss_id_boss_factory) VALUES (:factoryId, :bossId)");
                                $stmt->bindParam(':factoryId', $factoryId);
                                $stmt->bindParam(':bossId', $bossId); // Usa la nueva ID del jefe
                                $stmt->execute();

                                // Crear archivo JavaScript con el código de la plantilla
                                $canvasId = "c_$bossId"; // ID dinámico del canvas
                                $newJsFileName = "./js/boss$bossId.js";
                                $template = file_get_contents("./js/template.js"); // Contiene el código JavaScript de la plantilla

                                // Reemplazar el ID del canvas en la plantilla
                                $scriptContent = str_replace("c_1", $canvasId, $template);

                                // Guardar el contenido actualizado en el archivo JavaScript
                                file_put_contents($newJsFileName, $scriptContent);

                                echo "<p style='color: white;'>User registered successfully, redirecting...</p>";
                                session_start();
                                $_SESSION['user_email'] = $email;
                                echo '<script>
                                            setTimeout(function() {
                                                window.location.href = "./php/landing_page.php";
                                            }, 3000); // 3 segundos de retraso
                                        </script>';
                                exit();
                            } catch (PDOException $e) {
                                echo "<p style='color: #ffffff'>Error: " . $e->getMessage() . "</p>";
                                echo "<script>console.log(" . json_encode($e) . ")</script>";
                            }
                        } else if ($factoryId == "2") {
                            try {
                                $stmt = $conn->prepare("INSERT INTO boss (name, email, password) VALUES (:fullname, :email, :password)");
                                $stmt->bindParam(':fullname', $fullname);
                                $stmt->bindParam(':email', $email);
                                $stmt->bindParam(':password', $encryptedPassword);
                                $stmt->execute();

                                $bossId = $conn->lastInsertId(); // Obtén la ID del nuevo jefe

                                if (!$bossId) {
                                    echo "Error: Boss insertion failed.";
                                    exit();
                                }

                                $stmt = $conn->prepare("INSERT INTO factory_boss (factory_id_factory, boss_id_boss_factory) VALUES (:factoryId, :bossId)");
                                $stmt->bindParam(':factoryId', $factoryId);
                                $stmt->bindParam(':bossId', $bossId);
                                $stmt->execute();

                                // Crear archivo JavaScript con el código de la plantilla
                                $canvasId = "c_$bossId"; // ID dinámico del canvas
                                $newJsFileName = "./js/boss$bossId.js";
                                $template = file_get_contents("./js/template.js"); // Contiene el código JavaScript de la plantilla

                                // Reemplazar el ID del canvas en la plantilla
                                $scriptContent = str_replace("c_1", $canvasId, $template);

                                // Guardar el contenido actualizado en el archivo JavaScript
                                file_put_contents($newJsFileName, $scriptContent);

                                echo "<p style='color: white;'>User registered successfully, redirecting...</p>";
                                session_start();
                                $_SESSION['user_email'] = $email;
                                echo '<script>
                                            setTimeout(function() {
                                                window.location.href = "./php/landing_page.php";
                                            }, 3000); // 3 segundos de retraso
                                        </script>';
                                exit();
                            } catch (PDOException $e) {
                                echo "<p style='color: #ffffff'>Please fill in all fields, including the factory.<p>";
                                echo "<script>console.log($e)</script>";
                            }
                        } else if ($factoryId == "3") {
                            try {

                                $stmt = $conn->prepare("INSERT INTO boss (name, email, password) VALUES (:fullname, :email, :password)");
                                $stmt->bindParam(':fullname', $fullname);
                                $stmt->bindParam(':email', $email);
                                $stmt->bindParam(':password', $encryptedPassword);
                                $stmt->execute();

                                $bossId = $conn->lastInsertId(); // Obtén la ID del nuevo jefe

                                if (!$bossId) {
                                    echo "Error: Boss insertion failed.";
                                    exit();
                                }

                                $stmt = $conn->prepare("INSERT INTO factory_boss (factory_id_factory, boss_id_boss_factory) VALUES (:factoryId, :bossId)");
                                $stmt->bindParam(':factoryId', $factoryId);
                                $stmt->bindParam(':bossId', $bossId);
                                $stmt->execute();

                                // Crear archivo JavaScript con el código de la plantilla
                                $canvasId = "c_$bossId"; // ID dinámico del canvas
                                $newJsFileName = "./js/boss$bossId.js";
                                $template = file_get_contents("./js/template.js"); // Contiene el código JavaScript de la plantilla

                                // Reemplazar el ID del canvas en la plantilla
                                $scriptContent = str_replace("c_1", $canvasId, $template);

                                // Guardar el contenido actualizado en el archivo JavaScript
                                file_put_contents($newJsFileName, $scriptContent);

                                // Guardar el contenido actualizado en el archivo JavaScript
                                file_put_contents($newJsFileName, $scriptContent);

                                echo "<p style='color: white;'>User registered successfully, redirecting...</p>";
                                session_start();
                                $_SESSION['user_email'] = $email;
                                echo '<script>
                                                setTimeout(function() {
                                                    window.location.href = "./php/landing_page.php";
                                                }, 3000); // 3 segundos de retraso
                                            </script>';
                                exit();
                            } catch (PDOException $e) {
                                echo "<p style='color: #ffffff'>Please fill in all fields, including the factory.<p>";
                                echo "<script>console.log($e)</script>";
                            }
                        } else if ($factoryId == "4") {
                            try {

                                $stmt = $conn->prepare("INSERT INTO boss (name, email, password) VALUES (:fullname, :email, :password)");
                                $stmt->bindParam(':fullname', $fullname);
                                $stmt->bindParam(':email', $email);
                                $stmt->bindParam(':password', $encryptedPassword);
                                $stmt->execute();

                                $bossId = $conn->lastInsertId(); // Obtén la ID del nuevo jefe

                                if (!$bossId) {
                                    echo "Error: Boss insertion failed.";
                                    exit();
                                }

                                $stmt = $conn->prepare("INSERT INTO factory_boss (factory_id_factory, boss_id_boss_factory) VALUES (:factoryId, :bossId)");
                                $stmt->bindParam(':factoryId', $factoryId);
                                $stmt->bindParam(':bossId', $bossId);
                                $stmt->execute();

                                // Crear archivo JavaScript con el código de la plantilla
                                $canvasId = "c_$bossId"; // ID dinámico del canvas
                                $newJsFileName = "./js/boss$bossId.js";
                                $template = file_get_contents("./js/template.js"); // Contiene el código JavaScript de la plantilla

                                // Reemplazar el ID del canvas en la plantilla
                                $scriptContent = str_replace("c_1", $canvasId, $template);

                                // Guardar el contenido actualizado en el archivo JavaScript
                                file_put_contents($newJsFileName, $scriptContent);

                                echo "<p style='color: white;'>User registered successfully, redirecting...</p>";
                                session_start();
                                $_SESSION['user_email'] = $email;
                                echo '<script>
                                                setTimeout(function() {
                                                    window.location.href = "./php/landing_page.php";
                                                }, 3000); // 3 segundos de retraso
                                            </script>';
                                exit();
                            } catch (PDOException $e) {
                                echo "<p style='color: #ffffff'>Please fill in all fields, including the factory.<p>";
                                echo "<script>console.log($e)</script>";
                            }
                        }
                    }
                }
            }
            ?>

        </div>

        <div class="login-section">
            <header>Login</header>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="margin-top: 90px;">
                <input type="email" name="email" placeholder="Email address">
                <input type="password" name="password" placeholder="Password">
                <div class="separator">
                    <div class="line"></div>
                </div>
                <button type="submit" class="btn" name="login">Login</button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
                function textToBrainfuck($text)
                {
                    $brainfuckCode = "";
                    for ($i = 0; $i < strlen($text); $i++) {
                        $asciiCode = ord($text[$i]);
                        $brainfuckCode .= str_repeat("+", $asciiCode) . ".>";
                    }
                    return $brainfuckCode;
                }

                if (empty($_POST['email']) || empty($_POST['password'])) {
                    echo "<p style='color: #ffffff'>Please fill in all fields.<p>";
                } else {
                    $email = $_POST['email'];
                    $password = $_POST['password'];

                    $encryptedPasswordInput = textToBrainfuck($password);

                    try {
                        $stmt = $conn->prepare("SELECT * FROM boss WHERE email = :email");
                        $stmt->bindParam(':email', $email);
                        $stmt->execute();
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($user) {
                            if ($encryptedPasswordInput == $user['password']) {
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