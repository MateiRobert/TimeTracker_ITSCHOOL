<?php
session_start();
include_once '../inc/db.inc.php';
include_once 'functions.php'; // includem fișierul cu funcții comune

$email = $_SESSION['email'];
$department = '';

verifyUser($_SESSION);
$role = getUserRole($conn, $email);

// Redirectionare dacă rolul este 'user'
if ($role == 'user') {
  header("Location: ../HomeUser.php");
  exit();
}

$userInfo = getUserDepartment($conn, $email);
$department_id = $userInfo['department_id'];
$nume = $userInfo['name'];
$department_name = getDepartmentNames($conn);
$department = isset($department_name[$department_id]) ? $department_name[$department_id] : '';

// Setare utilizator inactiv
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['inactive_user_select'])) {
  $selected_inactive_user = mysqli_real_escape_string($conn, $_POST['inactive_user_select']);
  $stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
  $stmt->bind_param("i", $selected_inactive_user);

  if ($stmt->execute()) {
    echo "<p>Utilizatorul a fost setat ca inactiv.</p>";
  } else {
    echo "<p>A apărut o eroare la setarea utilizatorului ca inactiv.</p>";
    error_log("[".date('Y-m-d H:i:s')."]"." Eroare la setarea utilizatorului cu ID-ul $selected_inactive_user ca inactiv\n", 3, "log.txt");
  }
}

// Activare utilizator inactiv
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['active_user_select'])) {
  $selected_active_user = mysqli_real_escape_string($conn, $_POST['active_user_select']);
  $stmt = $conn->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
  $stmt->bind_param("i", $selected_active_user);

  if ($stmt->execute()) {
    echo "<p>Utilizatorul a fost activat cu succes.</p>";
  } else {
    echo "<p>A apărut o eroare la activarea utilizatorului.</p>";
    error_log("[".date('Y-m-d H:i:s')."]"." Eroare la activarea utilizatorului cu ID-ul $selected_active_user\n", 3, "log.txt");
  }
}

$result_active_users = $conn->query("SELECT id, name FROM users WHERE is_active = 1");
$active_user_name = array();

while ($row = $result_active_users->fetch_assoc()) {
  $active_user_name[$row['id']] = $row['name'];
}

$result_inactive_users = $conn->query("SELECT id, name FROM users WHERE is_active = 0");
$inactive_user_name = array();

while ($row = $result_inactive_users->fetch_assoc()) {
  $inactive_user_name[$row['id']] = $row['name'];
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Activare/Blocare User</title>
  <link rel="stylesheet" type="text/css" href="../style/styleHomeAdmin.css">
  <style>
    table {
      border-collapse: collapse;
    }

    table, th, td {
      border: 1px solid black;
      padding: 5px;
    }
  </style>
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
    <div><a href="../HomeAdmin.php" target="_self">Home</a></div>
    <div><a href="../inc/logout.php" target="_self">Log Out</a></div>
  </div>

  <br>
  <div class="line"></div>

  <h2>Utilizatori activi</h2>
  <form method="post">
    <label for="inactive_user_select">Selectează Utilizatorul Inactiv:</label>
    <select name="inactive_user_select" id="inactive_user_select">
    <?php
      foreach ($active_user_name as $id => $name) {
        echo "<option value='$id'>$name</option>";
      }
      ?>
    </select>
    <input type="submit" value="Dezactivează Utilizator">
  </form>

  <h2>Utilizatori inactivi</h2>
  <form method="post">
    <label for="active_user_select">Selectează Utilizatorul Activ:</label>
    <select name="active_user_select" id="active_user_select">
    <?php
      foreach ($inactive_user_name as $id => $name) {
        echo "<option value='$id'>$name</option>";
      }
      ?>
    </select>
    <input type="submit" value="Activează Utilizator">
  </form>

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
