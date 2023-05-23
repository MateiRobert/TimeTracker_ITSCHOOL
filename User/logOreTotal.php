<?php
require_once '../inc/db.inc.php';
session_start();

if(!isset($_SESSION['email'])) {
  header("Location: StartSeite.php");
  exit();
}

$email = $_SESSION['email'];

$query = "SELECT role, id, name, department_id FROM users WHERE Email = '$email'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $role = $row['role'];
  $nume = $row['name'];
  $user_id = $row['id'];
  $department_id = $row['department_id'];
} else {
  header("Location: StartSeite.php");
  exit();
}

$query = "SELECT name FROM departments WHERE id = $department_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $department_name = $row['name'];
} else {
  header("Location: StartSeite.php");
  exit();
}

$department = $department_name;

$query = "SELECT id, name FROM departments";
$departments = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Formular de vizualizare a orelor</title>
	<link rel="stylesheet" type="text/css" href="../style/styleHomeUser.css">
</head>
<title id="title">Vizualizare a orelor</title>
<body>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #4CAF50;
            color: black;
        }
        tr:nth-child(even) {
            background-color: white;
        }
        h2 {
            text-align: center;
            color: #black;
        }
    </style>

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
  
        <div><a href="../HomeUser.php" target="_self">Acasa</a></div>
        <div><a href="../inc/logout.php" target="_self">Log Out</a></div>
    </div>

    <?php
    while($department = mysqli_fetch_assoc($departments)) {
        $department_id = $department['id'];
        $department_name = $department['name'];

        echo "<h2>$department_name</h2>";

        $query = "SELECT hoursWorked.id, hoursWorked.data, hoursWorked.numar_ore, departments.name as department_name, Categories.nume_activitate as category_name FROM hoursWorked 
            INNER JOIN departments ON hoursWorked.departament_id = departments.id
            INNER JOIN Categories ON hoursWorked.categorie_id = Categories.id
            WHERE hoursWorked.user_id = $user_id AND hoursWorked.departament_id = $department_id";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            
            echo '<table>
                <tr>
                  <th>Data</th>
                  <th>Numărul de ore</th>
                  <th>Departament</th>
                  <th>Categorie</th>
                </tr>';
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['data'] . '</td>';
                echo '<td>' . $row['numar_ore'] . '</td>';
                echo '<td>' . $row['department_name'] . '</td>';
                echo '<td>' . $row['category_name'] . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            echo '<div class="line"></div>';
        } else {
            echo '<p style="text-align: center; color: red;">Nu sunt date înregistrate</p>';
            echo '<div class="line"></div>';
        }
    }
    ?>
</body>

<script>
function updateClock() {
  const now = new Date();
  const clock = document.getElementById("clock");
  clock.innerHTML = now.toLocaleString();
}
setInterval(updateClock, 1000);
</script>
</html>
