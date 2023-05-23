<?php
session_start();
include_once '../inc/db.inc.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['email'])) {
  header("Location: ../StartSeite.php");
  exit();
}

$email = $_SESSION['email'];
$department = '';

// Interogăm rolul utilizatorului
$stmt = $conn->prepare("SELECT role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
  $user = $result->fetch_assoc();
  $role = $user['role'];
}

// Dacă utilizatorul are rolul de 'user', îl redirecționăm către HomeUser.php
if ($role == 'user') {
  header("Location: ../HomeUser.php");
  exit();
}

// Interogăm id-ul departamentului utilizatorului
$stmt = $conn->prepare("SELECT department_id, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
  $row = $result->fetch_assoc();
  $department_id = $row['department_id'];
  $nume = $row['name'];

  // Interogăm numele tuturor departamentelor
  $result = $conn->query("SELECT id, name FROM departments");
  $department_name = array();

  while ($row = $result->fetch_assoc()) {
    $department_name[$row['id']] = $row['name'];
  }
  $department = isset($department_name[$department_id]) ? $department_name[$department_id] : '';
}

// Pregătirea pentru a obține orele de muncă ale unui utilizator selectat -> folosire GET pentru o vedere mai clara in link care utilizator a fost selectat
if (isset($_GET['user_id'])) {
    $selected_user_id = $_GET['user_id'];
  
    // Interogăm orele de muncă ale utilizatorului selectat
    $stmt = $conn->prepare("
      SELECT hoursWorked.numar_ore, hoursWorked.data, departments.name as department_name, categories.nume_activitate
      FROM hoursWorked 
      JOIN departments ON hoursWorked.departament_id = departments.id
      JOIN categories ON hoursWorked.categorie_id = categories.id
      WHERE hoursWorked.user_id = ?
    ");
    $stmt->bind_param("i", $selected_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
  
    $hoursWorked = array();
    while ($row = $result->fetch_assoc()) {
      $hoursWorked[] = $row;
    }
  }
  

?>

<!DOCTYPE html>
<html>
<head>
  <title>Ochii din umbra</title>
  <link rel="stylesheet" type="text/css" href="../style/styleHomeAdmin.css">
</head>
<body>
  <div class="header">
    <div id="clock"></div>
    <div>
      <?php
      if ($role === 'user') {
        echo $nume . '[<span style="color: green;">' . $role . '</span>]';
      } else {
        echo $nume . '[<span style="color: red;">' . $role . '</span>]';
      }
      ?>
    </div>

    <div><?php echo "DEPARTAMENT: " . $department ;?></div>
    <div><a href="../HomeAdmin.php" target="_self">Acasa</a></div>
    <div><a href="../inc/logout.php" target="_self">Log Out</a></div>
  </div>

  <br>
  <div class="line"></div>

  <!-- Adăugare formular pentru a selecta un utilizator -->
  <form method="get">
    <label for="user_id">Select user:</label>
    <select id="user_id" name="user_id">
      <?php
      // Obținem toți utilizatorii din baza de date
      $result = $conn->query("SELECT id, name FROM users");
      while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
      }
      ?>
    </select>
    <input type="submit" value="Show user activity">
  </form>

 <!-- Afișarea orele de muncă ale utilizatorului selectat -->
<?php
if (isset($hoursWorked)) {
  if (empty($hoursWorked)) {
    // Obținem numele utilizatorului selectat
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $selected_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    echo '<h2>' . $user['name'] . ' nu are ore înregistrate.</h2>';
  } else {
    echo '<h2>Activitate a persoanei selectate:</h2>';
    foreach ($hoursWorked as $hours) {
      echo '<p>Data: ' . $hours['data'] . ', Ore Lucrate: ' . $hours['numar_ore'] . ', Departamanet: ' . $hours['department_name'] . ', Categorie: ' . $hours['nume_activitate'] . '</p>';
    }
  }
}

?>
<div class="line"></div>

  <script>
    function updateClock() {
      const now = new Date();
      const clock = document.getElementById("clock");
      clock.innerHTML = now.toLocaleString();
    }
    setInterval(updateClock, 1000);
  </script>
</body>
</html>
