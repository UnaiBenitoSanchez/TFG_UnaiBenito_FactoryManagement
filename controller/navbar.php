<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="../css/navbar.css"> 
</head>
<body>
    <nav class="navbar">
        <a class="navbar-brand" href="landing_page.php">TFG_UnaiBenitoSánchez</a>
        <button class="navbar-toggler" onclick="toggleNavbar()">☰</button>
        <ul class="navbar-nav" id="navbarNav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Products from your factory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="graphics.php">Production graphics</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="factory.php">Your factory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">Logout</a>
            </li>
        </ul>
    </nav>

    <script>
        function toggleNavbar() {
            var navbarNav = document.getElementById('navbarNav');
            navbarNav.classList.toggle('show');
        }
    </script> 
</body>
</html>      
