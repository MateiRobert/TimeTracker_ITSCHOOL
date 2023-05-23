<?php
session_start();
include_once '../inc/db.inc.php';
include_once 'functions.php';



$email = $_SESSION['email'];
$department = '';

verifyUser($_SESSION);
$role = getUserRole($conn, $email);

// Redirectionare daca rolul este 'user'
if ($role == 'user') {
  header("Location: ../HomeUser.php");
  exit();
}

$userInfo = getUserDepartment($conn, $email);
$department_id = $userInfo['department_id'];
$nume = $userInfo['name'];
$department_name = getDepartmentNames($conn);
$department = isset($department_name[$department_id]) ? $department_name[$department_id] : '';

function getUserNamesAndDepartments($conn) {
  $result = $conn->query("SELECT users.id, users.name, departments.name AS department FROM users JOIN departments ON users.department_id = departments.id");
  $user_name_department = array();

  while ($row = $result->fetch_assoc()) {
    $user_name_department[$row['id']] = $row['name'] . " (Departamentul anterior: " . $row['department'] . ")";
  }
  return $user_name_department;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['user_select']) && !empty($_POST['department_select'])) {
  $selected_user = mysqli_real_escape_string($conn, $_POST['user_select']);
  $selected_department = mysqli_real_escape_string($conn, $_POST['department_select']);
  $stmt = $conn->prepare("UPDATE users SET department_id = ? WHERE id = ?");
  $stmt->bind_param("ii", $selected_department, $selected_user);

  $user_name_department = getUserNamesAndDepartments($conn);

  if ($stmt->execute()) {
    $selected_user_name = $user_name_department[$selected_user];
    $selected_department_name = $department_name[$selected_department];
    echo "<p>" . $selected_user_name . " a fost mutat cu succes în " . $selected_department_name . ".</p>";
  } else {
    echo "<p>A apărut o eroare la mutarea utilizatorului.</p>";
    error_log("[".date('Y-m-d H:i:s')."]"." Eroare la mutarea utilizatorului cu ID: $selected_user\n", 3, "log.txt");
  }
}

$department_name = getDepartmentNames($conn);
$user_name_department = getUserNamesAndDepartments($conn);

?>



<!DOCTYPE html>
<html>
<head>
  <title>Schimbare Departament User</title>
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

        <div><?php echo "DEPARTAMENT: ".$department ;?></div>
        <div><a href="../HomeAdmin.php" target="_self">Home</a></div>
        <div><a href="../inc/logout.php" target="_self">Log Out</a></div>
    </div>

    <br>
  <div class="line"></div>
  
    <form method="post">
  <label for="user_select">Selectează Utilizatorul:</label>
  <select name="user_select" id="user_select">
    <?php
      foreach ($user_name_department as $id => $name_department) {
        echo "<option value='$id'>$name_department</option>";
      }
    ?>
  </select>

  <label for="department_select">Selectează Departamentul Nou:</label>
  <select name="department_select" id="department_select">
    <?php
      foreach ($department_name as $id => $name) {
        echo "<option value='$id'>$name</option>";
      }
    ?>
  </select>
  <input type="submit" value="Mută Utilizator">
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
