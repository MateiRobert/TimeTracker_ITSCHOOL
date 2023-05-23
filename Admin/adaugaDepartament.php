<?php
session_start();
include_once '../inc/db.inc.php';
include_once 'functions.php';


// Functie care adauga un nou departament
function addNewDepartment($conn, $new_department) {
  if (preg_match("/^[a-zA-Z0-9 ]{3,50}$/", $new_department)) {
    $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
    $stmt->bind_param("s", $new_department);
    if ($stmt->execute()) {
      echo "<p>Departamentul a fost adăugat cu succes.</p>";
    } else {
      echo "<p>A apărut o eroare la adăugarea departamentului.</p>";
    }
  }
  else {
    echo "<p>Formatul departamentului nu este valid.</p>";
  }
}

verifyUser($_SESSION);
$email = $_SESSION['email'];
$department = '';
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
$department = isset($department_name[$department_id - 1]) ? $department_name[$department_id - 1] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['new_department'])) {
  $new_department = mysqli_real_escape_string($conn, $_POST['new_department']);
  addNewDepartment($conn, $new_department);
  $department_name = getDepartmentNames($conn); // actualizam lista de departamente dupa adaugarea unui nou departament
}
?>


<!DOCTYPE html>
<html>
<head>
	<title>Adaugare Departament</title>
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
        <div><a href="../HomeAdmin.php" target="_self">Acasa</a></div>
        <div><a href="../inc/logout.php" target="_self">Log Out</a></div>
    </div>

    <br>
	<div class="line"></div>
	
    <table>
    <thead>
      <tr>
        <th>Departament</th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($department_name as $department) {
        echo "<tr><td>$department</td></tr>";
      }
      ?>
    </tbody>
  </table>
    </br>
  <form method="post">
  <label for="new_department">Noul Departament:</label>
  <input type="text" id="new_department" name="new_department">
  <input type="submit" value="Adaugă Departament">
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
