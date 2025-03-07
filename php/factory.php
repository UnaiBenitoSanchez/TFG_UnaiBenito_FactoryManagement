<?php
include '../db_connect.php';

$sql1 = "SELECT boss_id_boss_factory FROM factory_boss 
        WHERE factory_id_factory IN (
            SELECT id_factory FROM factory 
            INNER JOIN factory_boss ON factory.id_factory = factory_boss.factory_id_factory
            INNER JOIN boss ON factory_boss.boss_id_boss_factory = boss.id_boss_factory
            WHERE boss.email = :userEmail
        )";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../controller/head.php'; ?>

    <!-- title -->
    <title>Inventory Management Dashboard - Factory</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- CSS -->
    <link rel="stylesheet" href="../css/three.css">
    <link rel="stylesheet" href="../css/factory.css">

</head>

<body>
    <?php include '../controller/navbar.php'; ?>

    <?php
    session_start();

    if (isset($_SESSION['user_email'])) {
        $userEmail = $_SESSION['user_email'];

        try {
            $sql = "SELECT factory.id_factory, factory.name AS factory_name, 
                    CONCAT(factory.street_address, ', ', factory.city, ', ', factory.state, ', ', factory.country) AS factory_address,
                    factory.employee_count, boss.name AS boss_name
                    FROM factory
                    INNER JOIN factory_boss ON factory.id_factory = factory_boss.factory_id_factory
                    INNER JOIN boss ON factory_boss.boss_id_boss_factory = boss.id_boss_factory
                    WHERE boss.email = :userEmail";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userEmail', $userEmail);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
    ?>
                <div class="container mt-5">
                    <h2 style="color: white">Factory Information</h2>
                    <?php
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $factoryAddress = $row['factory_address'];
                    ?>
                        <div class="card mt-4">
                            <div class="card-body">
                                <h5 class="card-title">Factory ID: <?php echo $row['id_factory']; ?></h5>
                                <div id="factoryContent">
                                    <p class="card-text">Factory Name: <span id="factoryName"><?php echo $row['factory_name']; ?></span></p>
                                    <p class="card-text">Boss Name: <?php echo $row['boss_name']; ?></p>
                                    <p class="card-text">Factory Address: <span id="factoryAddress"><?php echo $factoryAddress; ?></span></p>
                                    <p class="card-text">Number of Employees: <?php echo $row['employee_count']; ?></p>
                                </div>
                                <div id="factoryEdit" style="display: none;">
                                    <label for="editFactoryName">Factory Name:</label>
                                    <label for="editedEmployeeCount">Number of Employees:</label>
                                    <input type="text" id="editEmployeeCount" value="<?php echo $row['employee_count']; ?>"><br>
                                </div>
                                <div id="map" style="height: 300px;"></div>
                                <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
                                <button class="btn btn-primary" onclick="toggleEdit()">Edit</button>
                                <button class="btn btn-danger" onclick="saveChanges(<?php echo $row['id_factory']; ?>)" style="display: none;">Save</button>
                                <script>
                                    let map = L.map('map').setView([0, 0], 2);
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '© OpenStreetMap contributors'
                                    }).addTo(map);
                                    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent('<?php echo $factoryAddress; ?>'))
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data && data.length > 0) {
                                                let latlng = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                                                map.setView(latlng, 13);
                                                L.marker(latlng).addTo(map)
                                                    .bindPopup('<?php echo $factoryAddress; ?>')
                                                    .openPopup();
                                            } else {
                                                console.error('Error retrieving geocoding data for the address: <?php echo $factoryAddress; ?>');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error retrieving geocoding data:', error);
                                        });

                                    function toggleEdit() {
                                        let factoryContent = document.getElementById('factoryContent');
                                        let factoryEdit = document.getElementById('factoryEdit');
                                        let editButton = document.querySelector('.btn-primary');
                                        let saveButton = document.querySelector('.btn-danger');

                                        if (factoryContent.style.display === 'none') {
                                            factoryContent.style.display = 'block';
                                            factoryEdit.style.display = 'none';
                                            editButton.style.display = 'block';
                                            saveButton.style.display = 'none';
                                        } else {
                                            factoryContent.style.display = 'none';
                                            factoryEdit.style.display = 'block';
                                            editButton.style.display = 'none';
                                            saveButton.style.display = 'block';
                                        }
                                    }

                                    function saveChanges(factoryId) {

                                        let editedEmployeeCount = document.getElementById('editEmployeeCount').value;

                                        let xhr = new XMLHttpRequest();
                                        xhr.open("POST", "update_factory.php", true);
                                        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                        xhr.onreadystatechange = function() {
                                            if (xhr.readyState == 4 && xhr.status == 200) {
                                                console.log(xhr.responseText);
                                                toggleEdit();
                                                updateMap("<?php echo $factoryAddress; ?>");
                                                location.reload();
                                            }
                                        };

                                        xhr.send("factoryId=" + factoryId +
                                            "&editedEmployeeCount=" + editedEmployeeCount);
                                    }

                                    function updateMap(updatedAddress) {
                                        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(updatedAddress))
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data && data.length > 0) {
                                                    let latlng = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                                                    map.setView(latlng, 13);
                                                    L.marker(latlng).addTo(map)
                                                        .bindPopup(updatedAddress)
                                                        .openPopup();
                                                } else {
                                                    console.error('Error retrieving geocoding data for the address: ' + updatedAddress);
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error retrieving geocoding data:', error);
                                            });
                                    }
                                </script>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <center>
                    <h2 style="color: white; margin-top: 20px">Bosses:</h2>
                </center>
                <div class="mt-5 boss-container">

                    <?php
                    $stmt1 = $conn->prepare($sql1);
                    $stmt1->bindParam(':userEmail', $_SESSION['user_email']);
                    $stmt1->execute();
                    while ($bossRow = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="wrapper">';
                        echo '<canvas class="c" id="c_' . $bossRow['boss_id_boss_factory'] . '"></canvas>';
                        echo '</div>';
                    }
                    ?>
                </div>

        <?php
            } else {
                echo "No factory information found for the current boss.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        ?>

        <script src='../js/boss1.js'></script>
        <script src='../js/boss2.js'></script>
        <script src='../js/boss3.js'></script>
        <script src='../js/boss4.js'></script>
        <script>
            <?php
            for ($i = 1; $i <= 100; $i++) {
                echo "<script src='../js/boss$i.js'></script>";
            }
            ?>
        </script>

    <?php

    } else {
        echo "User not logged in.";
    }
    ?>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/three.js/108/three.min.js'></script>
    <script src='https://cdn.jsdelivr.net/gh/mrdoob/Three.js@r92/examples/js/loaders/GLTFLoader.js'></script>

</body>

</html>