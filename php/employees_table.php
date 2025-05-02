<?php
session_start();
require_once '../db_connect.php';

if (!isset($_SESSION['user_role'])) {
    header("Location: ../index.php");
    exit();
}

$bossEmail = $_SESSION['user_email'];

try {
    $stmt = $conn->prepare("SELECT id_boss_factory FROM boss WHERE email = :email");
    $stmt->bindParam(':email', $bossEmail);
    $stmt->execute();
    $boss = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$boss) {
        throw new Exception("Boss not found");
    }

    $bossId = $boss['id_boss_factory'];

    $stmt = $conn->prepare("SELECT factory_id_factory FROM factory_boss WHERE boss_id_boss_factory = :boss_id");
    $stmt->bindParam(':boss_id', $bossId);
    $stmt->execute();
    $factoryBoss = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$factoryBoss) {
        throw new Exception("Factory not found for this boss.");
    }

    $factoryId = $factoryBoss['factory_id_factory'];

    $stmt = $conn->prepare("SELECT name FROM factory WHERE id_factory = :factory_id");
    $stmt->bindParam(':factory_id', $factoryId);
    $stmt->execute();
    $factory = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("
        SELECT e.id_employee, e.name, e.email, e.role 
        FROM employee e
        JOIN factory_employee fe ON e.id_employee = fe.employee_id_employee
        WHERE fe.factory_id_factory = :factory_id
        ORDER BY e.name
    ");
    $stmt->bindParam(':factory_id', $factoryId);
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados de <?php echo htmlspecialchars($factory['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/employees_table.css">

    <!-- Navbar -->
    <link rel="stylesheet" href="../css/navbar.css">

    <style>
        /* Navbar */
        .nav-logout-inline {
            color: white;
            background-color: rgb(203, 35, 35);
            text-decoration: none;
            transition: color 0.3s, background-color 0.3s;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .nav-logout-inline:hover {
            background-color: rgb(255, 90, 90);
            color: #fff;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth navbar toggle
            function toggleNavbar() {
                var navbar = document.querySelector('.navbar');
                var navbarNav = document.getElementById('navbarNav');

                navbarNav.classList.toggle('show');
                navbar.classList.toggle('expanded');

                // Calcular altura del menú expandido y establecerla como variable CSS
                if (navbarNav.classList.contains('show')) {
                    const navbarExpandedHeight = navbarNav.offsetHeight;
                    document.documentElement.style.setProperty('--navbar-expanded-height', navbarExpandedHeight + 'px');
                }
            }

            // Set up the event listener
            const navbarToggler = document.querySelector('.navbar-toggler');
            if (navbarToggler) {
                navbarToggler.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleNavbar();
                });
            }

            // Close navbar when clicking on links
            document.querySelectorAll('.navbar-nav a').forEach(link => {
                link.addEventListener('click', () => {
                    document.getElementById('navbarNav').classList.remove('show');
                    document.querySelector('.navbar').classList.remove('expanded');
                });
            });

            // Close navbar when clicking outside
            document.addEventListener('click', function(event) {
                const navbar = document.querySelector('.navbar');
                const navbarNav = document.getElementById('navbarNav');
                const navbarToggler = document.querySelector('.navbar-toggler');

                if (navbarNav && navbarNav.classList.contains('show') &&
                    !navbarNav.contains(event.target) &&
                    event.target !== navbarToggler) {
                    navbarNav.classList.remove('show');
                    navbar.classList.remove('expanded');
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <nav class="navbar" style="height: 46px;">
        <a class="navbar-brand" href="landing_page.php" style="font-size: 20px">TFG_UnaiBenitoSánchez</a>
        <button class="navbar-toggler" onclick="toggleNavbar()" style="color: black;">☰</button>
        <ul class="navbar-nav" id="navbarNav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Products from your factory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="graphics.php">Production graphics</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./predict_view.php">Demand prediction</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="employees_table.php">Employees table</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="factory.php">Your factory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="chat.php">Chat</a>
            </li>
            <li class="nav-item">
                <a class="nav-logout-inline" href="../logout.php">Logout</a>
            </li>
        </ul>
    </nav>

    <div class="main-container" style="margin-top: 60px;">
        <?php if (count($employees) > 0): ?>
            <h2 class="table-title">Employees of your factory</h2>
            <div class="table-container">
                <table class="employees-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td contenteditable="false"><?php echo htmlspecialchars($employee['name']); ?></td>
                                <td contenteditable="false"><?php echo htmlspecialchars($employee['email']); ?></td>

                                <td>
                                    <button class="edit-btn" onclick="enableEdit(this)">Edit</button>
                                    <button class="save-btn" style="display:none;" onclick="saveEdit(this, <?php echo $employee['id_employee']; ?>)">
                                        💾 Save
                                    </button>

                                    <button class="delete-btn" onclick="deleteEmployee(<?php echo $employee['id_employee']; ?>, this)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-employees">
                <h2>There are no employees on this factory.</h2>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function enableEdit(button) {
            const row = button.closest('tr');
            const cells = row.querySelectorAll('td');

            cells[0].setAttribute('contenteditable', 'true');
            cells[1].setAttribute('contenteditable', 'true');

            button.style.display = 'none';
            row.querySelector('.save-btn').style.display = 'inline-block';
        }

        function saveEdit(button, id) {
            const row = button.closest('tr');
            const cells = row.querySelectorAll('td');

            const name = cells[0].innerText.trim();
            const email = cells[1].innerText.trim();

            fetch('update_employee.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id,
                        name,
                        email
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Employee updated!');
                        cells.forEach(cell => cell.setAttribute('contenteditable', 'false'));
                        button.style.display = 'none';
                        row.querySelector('.edit-btn').style.display = 'inline-block';
                    } else {
                        alert('Update failed.');
                    }
                });
        }

        function deleteEmployee(id, button) {
            if (!confirm('Are you sure you want to delete this employee?')) return;

            fetch('delete_employee.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const row = button.closest('tr');
                        row.remove();
                    } else {
                        alert('Delete failed.');
                    }
                });
        }
    </script>

</body>

</html>