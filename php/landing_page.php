<?php
include '../db_connect.php';
session_start();

if (!isset($_SESSION['user_email'])) {

  header("Location: ../index.php");
  exit();
}
function getFactoryNameByBoss($bossEmail)
{
  global $conn;

  $escapedBossEmail = $conn->quote($bossEmail);

  $query = "SELECT f.name FROM factory_boss fb
              JOIN boss b ON fb.boss_id_boss_factory = b.id_boss_factory
              JOIN factory f ON fb.factory_id_factory = f.id_factory
              WHERE b.email = $escapedBossEmail";

  $result = $conn->query($query);

  if ($result) {
    $factoryName = $result->fetchColumn();
    return $factoryName;
  }

  return false;
}

$bossEmail = $_SESSION['user_email'];
$factoryName = getFactoryNameByBoss($bossEmail);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include '../controller/head.php'; ?>

  <!-- title -->
  <title>Inventory management dashboard Landing Page</title>

  <!-- css -->
  <link rel="stylesheet" href="../css/landing_page.css">
  <link rel="stylesheet" href="../css/session.css">

  <!-- js -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <?php
  // Common Google Charts library loading logic
  echo '<script type="text/javascript">';
  echo 'google.charts.load(\'current\', {\'packages\': [\'bar\']});';
  echo '</script>';
  ?>

</head>

<body>
  <?php include '../controller/navbar.php'; ?>

  <?php
  // Common function to draw charts
  echo '<script type="text/javascript">';
  echo 'function drawChart(data, chartElementId, chartTitle) {';
  echo 'var options = {';
  echo 'chart: {';
  echo 'title: chartTitle,';
  echo 'subtitle: \'per month compared with other companies\',';
  echo '},';
  echo 'backgroundColor: {';
  echo 'fill: \'transparent\'';
  echo '},';
  echo 'chartArea: {';
  echo 'width: \'80%\',';
  echo '},';
  echo 'legend: {';
  echo 'textStyle: {';
  echo 'color: \'white\'';
  echo '}';
  echo '},';
  echo 'hAxis: {';
  echo 'textStyle: {';
  echo 'color: \'white\'';
  echo '}';
  echo '},';
  echo 'vAxis: {';
  echo 'textStyle: {';
  echo 'color: \'white\'';
  echo '}';
  echo '},';
  echo 'titleTextStyle: {';
  echo 'color: \'white\'';
  echo '}';
  echo '};';

  echo 'var chart = new google.charts.Bar(document.getElementById(chartElementId));';
  echo 'var chartData = google.visualization.arrayToDataTable(data);';
  echo 'chart.draw(chartData, google.charts.Bar.convertOptions(options));';
  echo '}';
  echo '</script>';
  ?>

  <?php
  // Individual chart data
  $mattelChartData = [
    ['Company', 'Sales', 'Production', 'Profit'],
    ['Mattel', 63983746, 75240000, 10000000],
    ['Melissa & Doug', 28983746, 33240000, 2000000],
    ['VTech', 50983746, 62240000, 8000000],
    ['Spin Master', 53983746, 66240000, 6000000]
  ];

  $legoChartData = [
    ['Company', 'Sales', 'Production', 'Profit'],
    ['Ravensburger', 38983746, 43240000, 4000000],
    ['Lego', 63983746, 75240000, 10000000],
    ['Fisher-Price', 50983746, 62240000, 8000000],
    ['Playmobil', 43983746, 56240000, 6000000]
  ];

  $nerfChartData = [
    ['Company', 'Sales', 'Production', 'Profit'],
    ['LeapFrog', 38983746, 43240000, 5000000],
    ['Tomy', 40983746, 52240000, 7000000],
    ['Nerf', 63983746, 75240000, 10000000],
    ['WowWee', 23983746, 36240000, 3000000]
  ];

  $playtmobilChartData = [
    ['Company', 'Sales', 'Production', 'Profit'],
    ['Naipes Heraclio Fournier', 38983746, 43240000, 4000000],
    ['Bandai', 50983746, 62240000, 8000000],
    ['MGA Entertainment', 23983746, 36240000, 4000000],
    ['Playmobil', 63983746, 75240000, 10000000]
  ];
  ?>

  <?php if ($factoryName == 'Mattel') : ?>
    <div class="container">
      <div id="columnchart_material1" style="width: 100%; height: 500px;"></div>
    </div>

    <!-- Display Mattel cards -->
    <div class="container">
      Our most sold toys:
      <br>
      <div style="justify-content: center; display: flex; flex-wrap: wrap;">
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/mattel4.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Barbie Cutie Reveal Serie Phantasy Unicorn</h5>
            <p class="card-text">Open the box and you'll see inside a soft plush unicorn and four surprise bags. Remove the rainbow unicorn costume and you'll find a Barbie doll with long hair and sparkly details. Open the surprise bags and discover sparkly clothes, accessories, a sponge-comb and a mini unicorn.</p>
          </div>
        </div>
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/mattel5.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Barbie Cutie Reveal Serie Jungle Friends Tiger</h5>
            <p class="card-text">Barbie Cutie Reveal Jungle Series dolls offer the cutest unboxing experience with 10 surprises! Discover a charming Elephant, lovable Tiger, bright Toucan or cheeky Monkey, then remove the plush costume to reveal a posable Barbie doll with long, colorful hair. Which doll will you reveal?</p>
          </div>
        </div>
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/mattel6.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Disney Frozen Queen Anna & Elsa Snow Queen</h5>
            <p class="card-text">Set of two classic dolls, Queen Anna and Snow Queen Elsa. Finely detailed features; Elsa snow queen costume includes satin dress with shimmering lavender organza cape and sleeves. Queen Anna costume includes layered green satin dress with glitter, lined cape and tiara. Beautifully styled, rooted hair; molded shoes and boots</p>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($factoryName == 'Lego') : ?>
    <div class="container">
      <div id="columnchart_material2" style="width: 100%; height: 500px;"></div>
    </div>

    <!-- Display Lego cards -->
    <div class="container">
      Our most sold toys:
      <br>
      <div style="justify-content: center; display: flex; flex-wrap: wrap;">
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/lego4.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Cherry Blossoms</h5>
            <p class="card-text">As well as being a celebration gift for kids, the brick-built blossoms make a great gift for grown-ups, who will be delighted to receive these unique flowers onValentine’s Day or Mother’s Day. Once complete, the set makes a beautiful piece of floral decor that will add a touch of spring joy to any space. It can also be combined with other LEGO flowers (sold separately) to create a vibrant bouquet.</p>
          </div>
        </div>
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/lego5.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Disney Ariel Mini Castle</h5>
            <p class="card-text">Fans of Disney Princess buildable toys and The Little Mermaid movie aged 12 and up will enjoy endless imaginative role play with this mini model of Ariel’s enchanting palace. Mini Disney Ariel’s Castle (40708) is covered in golden details, incorporates various underwater features and includes an Ariel mini-doll figure. This portable buildable playset is part of the Mini Disney range of companion construction toys, sold separately.</p>
          </div>
        </div>
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/lego6.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Ideas International Space Station</h5>
            <p class="card-text">Challenge your construction skills and evoke nostalgia with this LEGO Ideas International Space Station. A set developed in collaboration with NASA, this space station building kit includes a toy shuttle, three mini cargo spacecraft, and two astronaut figures to create a spectacular display and rekindle nostalgic memories of childhood LEGO projects.</p>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($factoryName == 'Nerf') : ?>
    <div class="container">
      <div id="columnchart_material3" style="width: 100%; height: 500px;"></div>
    </div>

    <!-- Display Nerf cards -->
    <div class="container">
      Our most sold toys:
      <br>
      <div style="justify-content: center; display: flex; flex-wrap: wrap;">
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/nerf4.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Nerf N-Strike Elite Disruptor Blaster</h5>
            <p class="card-text">This quick-draw blaster has a rotating drum that holds up to 6 Elite darts. Choose your target and fire 1 dart at a time, or unleash all 6 darts in rapid succession with slam-fire action. To prime the blaster, pull the slide back and release. Check the indicator; if it’s orange, the blaster is primed and ready to fire. The The Nerf N-Strike Elite Disruptor fires darts up to 90 feet (27 meters).</p>
          </div>
        </div>
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/nerf5.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Nerf Rival Phantom Corps Kronos XVIII-500</h5>
            <p class="card-text">Phantom Corps is a group of rogue specialists who may join Team Red or Team Blue today, then challenge them tomorrow. Nerf Rival battles will never be the same! Go into battle as a member of the Phantom Corps team with the breech-loading Kronos XVIII-500 blaster that features the team’s identifying color and logo.</p>
          </div>
        </div>
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/nerf6.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Nerf N-Strike Elite Rhino-Fire Blaster</h5>
            <p class="card-text">Dominate any battlefield with the double-barrel assault of the Nerf N-Strike Elite Rhino-Fire blaster! You can launch a blizzard of darts at targets up to 90 feet away from the blaster’s two barrels, and its two drums hold 25 darts each. Remove the tripod when you’re on the move or attach it to steady your shots when you’re firing from a secure location. You'll overwhelm the competition with the motorized, rapid-fire speed of the Rhino-Fire blaster!</p>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($factoryName == 'Playmobil') : ?>
    <div class="container">
      <div id="columnchart_material4" style="width: 100%; height: 500px;"></div>
    </div>

    <!-- Display Playmobil cards -->
    <div class="container">
      Our most sold toys:
      <br>
      <div style="justify-content: center; display: flex; flex-wrap: wrap;">
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/playmobil8.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Playmobil Space Station</h5>
            <p class="card-text">Space base with astronauts, spacecraft and accessories for space exploration.</p>
          </div>
        </div>
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/playmobil4.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Playmobil Hospital</h5>
            <p class="card-text">Complete hospital with emergency room, medical equipment and doctor/patient figures.</p>
          </div>
        </div>
        <div class="card" style="width: 18rem; margin-bottom: 10px; margin-left: 10px;">
          <img src="../img/playmobil1.jpg" class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Playmobil City Life Family</h5>
            <p class="card-text">Modern family figure set with accessories for playing everyday life scenes.</p>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <script>
    <?php
    // Individual chart drawing calls
    echo 'google.charts.setOnLoadCallback(function() { drawChart(' . json_encode($mattelChartData) . ', "columnchart_material1", "Mattel Sales, Production, and Profit"); });';
    echo 'google.charts.setOnLoadCallback(function() { drawChart(' . json_encode($legoChartData) . ', "columnchart_material2", "Lego Sales, Production, and Profit"); });';
    echo 'google.charts.setOnLoadCallback(function() { drawChart(' . json_encode($nerfChartData) . ', "columnchart_material3", "Nerf Sales, Production, and Profit"); });';
    echo 'google.charts.setOnLoadCallback(function() { drawChart(' . json_encode($playtmobilChartData) . ', "columnchart_material4", "Playmobil. Sales, Production, and Profit"); });';
    ?>
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

</body>

</html>