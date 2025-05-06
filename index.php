<?php
include 'db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS-Imported -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.18.0/font/bootstrap-icons.css">

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="./css/index.css">
    <style>
        .error-message {
            color: #ff6b6b;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
    </style>

    <!-- Title -->
    <title>Inventory management dashboard - Login/Register</title>

</head>

<body>

    <div class="container">

        <!-- Signup Section -->
        <div class="signup-section">

            <?php
            // Signup Logic
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {
                $fullname = $_POST['fullname'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $factoryId = $_POST['factory'];
                $role = $_POST['role'];

                $valid = true;

                if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                    echo "<script>document.getElementById('email').classList.add('invalid'); document.getElementById('emailError').style.display = 'block';</script>";
                    $valid = false;
                }

                if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/', $password)) {
                    echo "<script>document.getElementById('password').classList.add('invalid'); document.getElementById('passwordError').style.display = 'block';</script>";
                    $valid = false;
                }

                if (empty($fullname) || empty($factoryId)) {
                    $valid = false;
                }

                if ($valid) {
                    $encryptedPassword = password_hash($password, PASSWORD_BCRYPT);

                    try {
                        if ($role === "boss") {
                            $stmt = $conn->prepare("SELECT id_boss_factory FROM boss WHERE email = :email");
                        } else {
                            $stmt = $conn->prepare("SELECT id_employee FROM employee WHERE email = :email");
                        }

                        $stmt->bindParam(':email', $email);
                        $stmt->execute();
                        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($existingUser) {
                            echo "<div class='alert alert-danger text-center mt-3' role='alert'>Error: This email is already registered as a $role.</div>";
                        } else {
                            if ($role === "boss") {
                                $stmt = $conn->prepare("INSERT INTO boss (name, email, password) VALUES (:fullname, :email, :password)");
                                $stmt->bindParam(':fullname', $fullname);
                                $stmt->bindParam(':email', $email);
                                $stmt->bindParam(':password', $encryptedPassword);
                                $stmt->execute();

                                $bossId = $conn->lastInsertId();

                                if (!$bossId) {
                                    echo "<div class='alert alert-danger text-center mt-3' role='alert'>Error: Couldn't register the boss.</div>";
                                    exit();
                                }

                                $stmt = $conn->prepare("INSERT INTO factory_boss (factory_id_factory, boss_id_boss_factory) VALUES (:factoryId, :bossId)");
                                $stmt->bindParam(':factoryId', $factoryId);
                                $stmt->bindParam(':bossId', $bossId);
                                $stmt->execute();

                                $canvasId = "c_$bossId";
                                $newJsFileName = "./js/boss$bossId.js";
                                $template = file_get_contents("./js/template.js");
                                $scriptContent = str_replace("c_1", $canvasId, $template);
                                file_put_contents($newJsFileName, $scriptContent);

                                echo "<div class='alert alert-success text-center mt-3' role='alert'>Boss registered successfully, redirecting...</div>";
                                $_SESSION['user_email'] = $email;
                                $_SESSION['user_role'] = $role;
                                $_SESSION['boss_user'] = $fullname;
                                $_SESSION['boss_id'] = $bossId;

                                echo '<script>
                                        setTimeout(function() {
                                            window.location.href = "./php/landing_page.php";
                                        }, 3000);
                                    </script>';
                            } else {
                                $roleE = "worker";
                                $stmt = $conn->prepare("INSERT INTO employee (name, email, password, role) 
                                            VALUES (:fullname, :email, :password, :role)");
                                $stmt->bindParam(':fullname', $fullname);
                                $stmt->bindParam(':email', $email);
                                $stmt->bindParam(':password', $encryptedPassword);
                                $stmt->bindParam(':role', $roleE);
                                $stmt->execute();

                                $employeeId = $conn->lastInsertId();

                                $stmt = $conn->prepare("
                                INSERT INTO GestionDeFabricas.factory_employee (factory_id_factory, employee_id_employee)
                                VALUES (:factoryId, :employeeId)
                            ");

                                $stmt->bindParam(':factoryId', $factoryId);
                                $stmt->bindParam(':employeeId', $employeeId);

                                $stmt->execute();

                                echo "<div class='alert alert-success text-center mt-3' role='alert'>Employee registered successfully, redirecting...</div>";

                                $_SESSION['user_email'] = $email;
                                $_SESSION['user_role'] = $role;
                                $_SESSION['employee_user'] = $fullname;
                                $_SESSION['employee_id'] = $employeeId;

                                echo '<script>
                            setTimeout(function() {
                                window.location.href = "./php/employee_dashboard.php";
                            }, 3000);
                          </script>';
                            }

                            exit();
                        }
                    } catch (PDOException $e) {
                        echo "<div class='alert alert-danger text-center mt-3' role='alert'>Error: " . $e->getMessage() . "</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger text-center mt-3' role='alert'>Please correct the errors in the form.</div>";
                }
            }
            ?>

            <header style="margin-bottom: -70px">Signup</header>

            <form id="signupForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="margin-top: 90px;">
                <input type="text" name="fullname" id="fullname" placeholder="Full name" required>

                <input type="email" name="email" id="email" placeholder="Email address" required>
                <div id="emailError" class="error-message">Please enter a valid email address (example: x@x.xx)</div>

                <input type="password" name="password" id="password" placeholder="Password" required>
                <div id="passwordError" class="error-message">Password must be at least 8 characters long and contain at least 1 letter, 1 number, and 1 special character</div>

                <label for="role" style="color: white;">Register as:</label>
                <select name="role" id="role" required>
                    <option value="boss">Boss</option>
                    <option value="employee">Employee</option>
                </select>

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



        </div>

        <!-- Login Section -->
        <div class="login-section" id="login-section">
            <header>Login</header>
            <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="margin-top: 90px;">
                <input type="email" name="email" id="loginEmail" placeholder="Email address" required>
                <div id="loginEmailError" class="error-message">Please enter a valid email address (example: x@x.xx)</div>

                <input type="password" name="password" id="loginPassword" placeholder="Password" required>

                <label for="role" style="color: white; margin-top: 10px;">Login as:</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="boss">Boss</option>
                    <option value="employee">Employee</option>
                </select>

                <div id="loginError" class="error-message" style="display: <?php echo isset($loginError) && $loginError ? 'block' : 'none'; ?>">Invalid email or password</div>

                <div class="separator">
                    <div class="line"></div>
                </div>
                <button type="submit" class="btn" name="login">Login</button>
            </form>

            <?php
            // Login Logic
            $loginError = false;
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
                $email = $_POST['email'];
                $password = $_POST['password'];
                $role = $_POST['role'];

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "<script>document.getElementById('loginEmail').classList.add('invalid'); document.getElementById('loginEmailError').style.display = 'block';</script>";
                    $loginError = true;
                } else {
                    try {
                        if ($role === 'boss') {
                            $stmt = $conn->prepare("SELECT * FROM boss WHERE email = :email");
                        } else {
                            $stmt = $conn->prepare("SELECT * FROM employee WHERE email = :email");
                        }

                        $stmt->bindParam(':email', $email);
                        $stmt->execute();
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($user && password_verify($password, $user['password'])) {
                            session_start();
                            $_SESSION['user_email'] = $email;
                            $_SESSION['user_role'] = $role;
                            $_SESSION['user_id'] = $user['id_boss_factory'];

                            if ($role === 'employee') {
                                $_SESSION['employee_user'] = $user['name'];
                            } else {
                                $_SESSION['boss_user'] = $user['name'];
                            }

                            if ($role === 'boss') {
                                echo '<script>window.location.href = "./php/landing_page.php";</script>';
                            } else if ($role === 'employee') {
                                $stmt = $conn->prepare("UPDATE employee SET is_logged_in = TRUE WHERE id_employee = :id");

                                $stmt->bindParam(':id', $user['id_employee']);
                                $stmt->execute();
                                $_SESSION['employee_user'] = $user['name'];
                                $_SESSION['employee_id'] = $user['id_employee'];

                                echo '<script>window.location.href = "./php/employee_dashboard.php";</script>';
                                exit();
                            }

                            exit();
                        } else {
                            $loginError = true;
                        }
                    } catch (PDOException $e) {
                        $loginError = true;
                    }
                }

                if ($loginError) {
                    echo "<script>
                            document.getElementById('loginError').style.display = 'block';
                            document.getElementById('loginPassword').classList.add('invalid');
                          </script>";
                }
            }
            ?>
        </div>

    </div>

    <script src="./js/index.js"></script>
    <script>
        function validateEmail(email) {
            const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return re.test(String(email).toLowerCase());
        }

        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailError = document.getElementById('emailError');

            if (!validateEmail(email)) {
                this.classList.add('invalid');
                emailError.style.display = 'block';
            } else {
                this.classList.remove('invalid');
                emailError.style.display = 'none';
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const passwordError = document.getElementById('passwordError');

            if (!/(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}/.test(password)) {
                this.classList.add('invalid');
                passwordError.style.display = 'block';
            } else {
                this.classList.remove('invalid');
                passwordError.style.display = 'none';
            }
        });

        document.getElementById('loginEmail').addEventListener('input', function() {
            const email = this.value;
            const emailError = document.getElementById('loginEmailError');

            if (!validateEmail(email)) {
                this.classList.add('invalid');
                emailError.style.display = 'block';
            } else {
                this.classList.remove('invalid');
                emailError.style.display = 'none';
            }
        });

        document.getElementById('signupForm').addEventListener('submit', function(e) {
            let valid = true;

            const email = document.getElementById('email').value;
            if (!validateEmail(email)) {
                document.getElementById('email').classList.add('invalid');
                document.getElementById('emailError').style.display = 'block';
                valid = false;
            }

            const password = document.getElementById('password').value;
            if (!/(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}/.test(password)) {
                document.getElementById('password').classList.add('invalid');
                document.getElementById('passwordError').style.display = 'block';
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('loginEmail').value;
            if (!validateEmail(email)) {
                document.getElementById('loginEmail').classList.add('invalid');
                document.getElementById('loginEmailError').style.display = 'block';
                e.preventDefault();
            }
        });
    </script>

</body>

</html>