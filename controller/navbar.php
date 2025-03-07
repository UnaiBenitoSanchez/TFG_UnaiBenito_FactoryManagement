<style>
    .navbar {
        background-color: #2c3e50;
        opacity: 0.9;
        z-index: 100;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        flex-wrap: wrap;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        border-bottom: 2px solid #e74c3c;
    }

    .navbar-brand {
        color: #ecf0f1;
        text-decoration: none;
        font-size: 24px;
        font-weight: bold;
    }

    .navbar-toggler {
        display: none;
        background-color: #ecf0f1;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 24px;
    }

    .navbar-nav {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        flex-direction: row;
    }

    .nav-item {
        margin-left: 15px;
    }

    .nav-link {
        color: #ecf0f1;
        text-decoration: none;
        transition: color 0.3s, background-color 0.3s;
        padding: 8px 15px;
        border-radius: 5px;
    }

    .nav-link:hover {
        color: #2c3e50;
        background-color: #e74c3c;
    }

    @media (max-width: 768px) {
        .navbar-toggler {
            display: block;
        }

        .navbar-nav {
            display: none;
            flex-direction: column;
            width: 100%;
            background-color: #2c3e50;
        }

        .navbar-nav.show {
            display: flex;
        }

        .nav-item {
            margin: 10px 0;
            text-align: center;
        }
    }
</style>

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
