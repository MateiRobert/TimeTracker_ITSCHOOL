<?php
session_start();
include_once '../inc/db.inc.php';
include_once 'functions.php'; // includem fisierul cu functii comune

// Initialize error log
ini_set('error_log', 'log.txt');
ini_set('log_errors', 'On');

$email = $_SESSION['email'];
$department = '';

verifyUser($_SESSION);

$role = getUserRole($conn, $email);

if ($role == 'user') {
  header("Location: ../HomeUser.php");
  error_log("User" . $nume ."nu este administrator: Redirectionat catre HomePage User");
  exit();
}

$userInfo = getUserDepartment($conn, $email);
$department_id = $userInfo['department_id'];
$nume = $userInfo['name'];

$department_name = getDepartmentNames($conn);

$department = isset($department_name[$department_id]) ? $department_name[$department_id] : '';

function getHoursWorked($conn, $selected_department_id) {
  $stmt = $conn->prepare("
    SELECT users.name, hoursWorked.data, categories.nume_activitate, hoursWorked.numar_ore, departments.name as department_name
    FROM hoursWorked
    JOIN users ON hoursWorked.user_id = users.id
    JOIN categories ON hoursWorked.categorie_id = categories.id
    JOIN departments ON hoursWorked.departament_id = departments.id
    WHERE hoursWorked.departament_id = ?
  ");
  $stmt->bind_param("i", $selected_department_id);
  $stmt->execute();
  $result = $stmt->get_result();

  $hoursWorked = array();
  while ($row = $result->fetch_assoc()) {
    $hoursWorked[] = $row;
  }
  return $hoursWorked;
}

$hoursWorked = array();
if (isset($_GET['department_id'])) {
  $selected_department_id = $_GET['department_id'];
  $hoursWorked = getHoursWorked($conn, $selected_department_id);
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Vizualizare Ore Departament</title>
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
    <div><a href="../HomeAdmin.php" target="_self">Home</a></div>
    <div><a href="../inc/logout.php" target="_self">Log Out</a></div>
  </div>

  <br>
  <div class="line"></div>

  <form method="GET" action="">
    <label for="department_id">Select Department:</label>
    <select name="department_id" id="department_id">
      <?php
        foreach ($department_name as $id => $name) {
          echo '<option value="' . $id . '">' . $name . '</option>';
        }
      ?>
    </select>
    <input type="submit" value="Submit">
  </form>

  <?php
  if (isset($hoursWorked)) {
    if (empty($hoursWorked)) {
      echo '<h2>Nu există ore înregistrate în acest departament.</h2>' ;
    } else {
      echo '<h2>Activitati in Departametnul selectat:</h2>';
      echo '<table>
        <tr>
          <th>Name</th>
          <th>Date</th>
          <th>Category</th>
          <th>Hours worked</th>
          <th>Department</th>
        </tr>';
      foreach ($hoursWorked as $hours) {
        echo '<tr>
          <td>' . $hours['name'] . '</td>
          <td>' . $hours['data'] . '</td>
          <td>' . $hours['nume_activitate'] . '</td>
          <td>' . $hours['numar_ore'] . '</td>
          <td>' . $hours['department_name'] . '</td>
        </tr>';
      }
      echo '</table>';
    }
  }
  ?>
  
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
