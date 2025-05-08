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

    // Pagination configuration
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 5;
    $offset = ($page - 1) * $perPage;

    $stmt = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM employee e
    JOIN factory_employee fe ON e.id_employee = fe.employee_id_employee
    WHERE fe.factory_id_factory = :factory_id
");
    $stmt->bindParam(':factory_id', $factoryId);
    $stmt->execute();
    $totalEmployees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalEmployees / $perPage);

    $stmt = $conn->prepare("
    SELECT e.id_employee, e.name, e.email, e.role, e.is_logged_in 
    FROM employee e
    JOIN factory_employee fe ON e.id_employee = fe.employee_id_employee
    WHERE fe.factory_id_factory = :factory_id
    ORDER BY e.name
    LIMIT :limit OFFSET :offset
");
    $stmt->bindParam(':factory_id', $factoryId);
    $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$currentEmployeeId = $_SESSION['user_role'] === 'employee' ? $_SESSION['employee_id'] : null;

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees from <?php echo htmlspecialchars($factory['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function toggleNavbar() {
                var navbar = document.querySelector('.navbar');
                var navbarNav = document.getElementById('navbarNav');

                navbarNav.classList.toggle('show');
                navbar.classList.toggle('expanded');

                if (navbarNav.classList.contains('show')) {
                    const navbarExpandedHeight = navbarNav.offsetHeight;
                    document.documentElement.style.setProperty('--navbar-expanded-height', navbarExpandedHeight + 'px');
                }
            }

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

    <!-- css -->
    <link rel="stylesheet" href="../css/session.css">
    <link rel="stylesheet" href="../css/employees_table.css">
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

        /* Pagination controls */
        .pagination-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            gap: 20px;
        }

        .pagination-arrow {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination-arrow:hover:not(.disabled) {
            background-color: #45a049;
        }

        .pagination-arrow.disabled {
            background-color: #cccccc;
            color: #666666;
            cursor: not-allowed;
        }

        .page-info {
            font-weight: 500;
            color: #333;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 90%;
            max-width: 400px;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-container {
            transform: translateY(0);
        }

        .modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .modal-body {
            padding: 20px;
            color: #555;
            line-height: 1.5;
        }

        .modal-footer {
            padding: 12px 20px;
            border-top: 1px solid #eaeaea;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-btn {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .modal-btn-secondary {
            background-color: #f0f0f0;
            color: #333;
        }

        .modal-btn-secondary:hover {
            background-color: #e0e0e0;
        }

        .modal-btn-primary {
            background-color: #4CAF50;
            color: white;
        }

        .modal-btn-primary:hover {
            background-color: #45a049;
        }

        .modal-btn-danger {
            background-color: #f44336;
            color: white;
        }

        .modal-btn-danger:hover {
            background-color: #d32f2f;
        }

        .modal-success .modal-header {
            background-color:rgb(104, 175, 76);
            color: white;
            border-bottom: none;
        }

        .modal-success .modal-title {
            color: white;
        }

        .modal-success .modal-close {
            color: white;
        }

        .modal-error .modal-header {
            background-color: #f44336;
            color: white;
            border-bottom: none;
        }

        .modal-error .modal-title {
            color: red;
        }

        .modal-error .modal-close {
            color: red;
        }

        .modal-container .modal-header{
            background-color: #f44336;
        }

        .modal-container .modal-header h3{
            color: white;
        }
    </style>

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
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr <?php echo ($employee['is_logged_in']) ? 'class="active-session"' : ''; ?> data-employee-id="<?php echo $employee['id_employee']; ?>">
                                <td contenteditable="false">
                                    <?php echo htmlspecialchars($employee['name']); ?>
                                    <?php if (isset($_SESSION['employee_id']) && $_SESSION['employee_id'] == $employee['id_employee']): ?>
                                        <span class="badge bg-success">You</span>
                                    <?php endif; ?>
                                </td>
                                <td contenteditable="false"><?php echo htmlspecialchars($employee['email']); ?></td>
                                <td>
                                    <?php if ($employee['is_logged_in']): ?>
                                        <span class="status-active">Online</span>
                                    <?php else: ?>
                                        <span class="status-inactive">Offline</span>
                                    <?php endif; ?>
                                </td>
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
                <div class="pagination-controls">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="pagination-arrow">← Previous</a>
                    <?php else: ?>
                        <span class="pagination-arrow disabled">← Previous</span>
                    <?php endif; ?>

                    <span class="page-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="pagination-arrow">Next →</a>
                    <?php else: ?>
                        <span class="pagination-arrow disabled">Next →</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="no-employees">
                <h2>There are no employees on this factory.</h2>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Confirm delete</h3>
                <button class="modal-close" onclick="hideModal('deleteModal')">&times;</button>
            </div>
            <div class="modal-body">
                Are you sure that you want to delete that employee?
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-secondary" onclick="hideModal('deleteModal')">Cancel</button>
                <button class="modal-btn modal-btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="successModal">
        <div class="modal-container modal-success">
            <div class="modal-header" style="
            background-color:rgb(104, 175, 76);">
                <h3 class="modal-title">Updated!</h3>
                <button class="modal-close" onclick="hideModal('successModal')">&times;</button>
            </div>
            <div class="modal-body">
                Employee updated succesfully.
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-primary" onclick="hideModal('successModal')">Confirm</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="errorModal">
        <div class="modal-container modal-error">
            <div class="modal-header">
                <h3 class="modal-title">Error</h3>
                <button class="modal-close" onclick="hideModal('errorModal')">&times;</button>
            </div>
            <div class="modal-body" id="errorModalBody">
                Error processing the solicitude.
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-primary" onclick="hideModal('errorModal')">Confirm</button>
            </div>
        </div>
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

        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function hideModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('active');
            document.body.style.overflow = '';
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
                        showModal('successModal');
                        cells.forEach(cell => cell.setAttribute('contenteditable', 'false'));
                        button.style.display = 'none';
                        row.querySelector('.edit-btn').style.display = 'inline-block';
                    } else {
                        document.getElementById('errorModalBody').textContent = data.message || 'Error updating employee.';
                        showModal('errorModal');
                    }
                })
                .catch(error => {
                    document.getElementById('errorModalBody').textContent = 'Connection error.';
                    showModal('errorModal');
                });
        }

        let currentEmployeeToDelete = null;

        function deleteEmployee(id, button) {
            currentEmployeeToDelete = {
                id,
                button
            };
            showModal('deleteModal');
        }

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (!currentEmployeeToDelete) return;

            const {
                id,
                button
            } = currentEmployeeToDelete;

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
                        document.getElementById('errorModalBody').textContent = data.message || 'Error deleting the employee';
                        showModal('errorModal');
                    }
                })
                .catch(error => {
                    document.getElementById('errorModalBody').textContent = 'Connection error.';
                    showModal('errorModal');
                })
                .finally(() => {
                    hideModal('deleteModal');
                    currentEmployeeToDelete = null;
                });
        });

        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideModal(this.id);
                }
            });
        });
    </script>

    <script>
        function updateConnectionStatus() {
            fetch('get_connection_status.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }

                    data.forEach(employee => {
                        const rows = document.querySelectorAll('tbody tr');
                        rows.forEach(row => {
                            const saveBtn = row.querySelector('.save-btn');
                            if (saveBtn) {
                                const onclickAttr = saveBtn.getAttribute('onclick');
                                const match = onclickAttr.match(/saveEdit\(this, (\d+)\)/);
                                if (match && match[1] == employee.id) {
                                    const statusCell = row.querySelector('td:nth-child(3)');
                                    if (employee.is_logged_in) {
                                        statusCell.innerHTML = '<span class="status-active">Online</span>';
                                        row.classList.add('active-session');
                                    } else {
                                        statusCell.innerHTML = '<span class="status-inactive">Offline</span>';
                                        row.classList.remove('active-session');
                                    }
                                }
                            }
                        });
                    });
                })
                .catch(error => {
                    console.error('Error fetching connection status:', error);
                })
                .finally(() => {
                    setTimeout(updateConnectionStatus, 1000);
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateConnectionStatus();
        });
    </script>

    <?php include '../controller/session.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sessionPopup = document.querySelector('.session-popup');

            if (sessionPopup) {
                setTimeout(function() {
                    sessionPopup.style.transition = 'opacity 0.5s ease-out';
                    sessionPopup.style.opacity = '0';

                    setTimeout(function() {
                        sessionPopup.remove();
                    }, 500);
                }, 5000);
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.pagination-arrow:not(.disabled)').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = new URL(this.href).searchParams.get('page');
                    fetchEmployees(page);
                });
            });
        });

        function fetchEmployees(page) {
            fetch(`employees_table.php?page=${page}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('.table-container');
                    document.querySelector('.table-container').innerHTML = newTable.innerHTML;

                    document.querySelectorAll('.edit-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            enableEdit(this);
                        });
                    });

                    document.querySelectorAll('.save-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const id = this.closest('tr').dataset.employeeId;
                            saveEdit(this, id);
                        });
                    });

                    document.querySelectorAll('.delete-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const id = this.closest('tr').dataset.employeeId;
                            deleteEmployee(id, this);
                        });
                    });
                });
        }
    </script>

</body>

</html>