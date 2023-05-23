<?php
require_once '../inc/db.inc.php'; //conexiunea la baza de date
session_start();
if(!isset($_SESSION['email'])) { //verificăm dacă există email în sesiune
  header("Location: StartSeite.php"); //redirecționăm utilizatorul la pagina de login
  exit();
}

$email = $_SESSION['email'];
$errors = []; // Initializam un array pentru erori
$successMessage = ""; // Initializam un string pentru mesajul de succes

//selecteaza department_id din tabela users folosind email-ul utilizatorului curent
$query = "SELECT role FROM users WHERE Email = '$email'";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $role = $row['role'];
}

//selecteaza department_id din tabela users folosind email-ul utilizatorului curent
$query = "SELECT department_id FROM users WHERE Email = '$email'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $department_id = $row['department_id'];

  //selecteaza numele departamentului din tabela departments folosind id-ul departamentului gasit in interogarea anterioara
  $query = "SELECT name FROM departments WHERE id = $department_id";
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $department_name = $row['name'];
  } else {
    header("Location: StartSeite.php");
    exit();
  }
} else {
  header("Location: StartSeite.php");
  exit();
}

$department = $department_name;

$query = "SELECT name FROM users WHERE Email = '$email'";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $nume = $row['name'];
} else {
  $nume = "Nedefinit";
}

$query = "SELECT id FROM users WHERE Email = '$email'";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $user_id = $row['id'];
}

$start_date = date("Y-m-d", strtotime("-1 week"));
$end_date = date("Y-m-d");

$query = "SELECT hoursWorked.id, hoursWorked.data, hoursWorked.numar_ore, departments.name as department_name, Categories.nume_activitate as category_name FROM hoursWorked 
          INNER JOIN departments ON hoursWorked.departament_id = departments.id
          INNER JOIN Categories ON hoursWorked.categorie_id = Categories.id
          WHERE user_id = $user_id AND data BETWEEN '$start_date' AND '$end_date'";
$result = mysqli_query($conn, $query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['numar_ore']) && isset($_POST['id'])) {
    $numar_ore = $_POST['numar_ore'];
    $id = $_POST['id'];

    $query = "SELECT data, numar_ore FROM hoursWorked WHERE id = $id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) == 1) {
      $row = mysqli_fetch_assoc($result);
      $record_date = new DateTime($row['data']);
      $current_date = new DateTime(date("Y-m-d"));

      $interval = $record_date->diff($current_date);
      $diff_days = $interval->d;

      if ($diff_days > 2) {
        $errors[] = "Nu puteți modifica orele după mai mult de 2 zile!";
      } else {
        $query = "SELECT SUM(numar_ore) as total_hours FROM hoursWorked WHERE data = '".$row['data']."' AND user_id = $user_id";
        $hours_result = mysqli_query($conn, $query);
        if ($hours_result) {
          $hours_row = mysqli_fetch_assoc($hours_result);
          $total_hours = $hours_row['total_hours'];

          if (($total_hours - $row['numar_ore'] + $numar_ore) > 8) {
            $errors[] = "Suma orelor nu poate depăși 8 ore într-o zi!";
          } else {
            if (empty($errors)) {
              $query = "UPDATE hoursWorked SET numar_ore = $numar_ore WHERE id = $id";
              $result = mysqli_query($conn, $query);
              if ($result) {
                $successMessage = "Orele au fost actualizate cu succes!";
              } else {
                $errors[] = "Eroare la actualizarea orelor!";
              }
            }
          }
        } else {
          $errors[] = "Eroare la calcularea orelor totale!";
        }
      }
    } else {
      $errors[] = "Inregistrare invalidă!";
    }
  }
}

?>


<!DOCTYPE html>
<html>
<head>
	<title>Log / Modificare a orelor</title>
	<link rel="stylesheet" type="text/css" href="../style/styleHomeUser.css">
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
  
  <div><a href="../HomeUser.php" target="_self">Acasa</a></div>
  <div><a href="../inc/logout.php" target="_self">Log Out</a></div>
</div>
<br>    
<br>
<div class="line"></div>
<div class="form-container">
  <!-- Afiseaza erorile aici -->
  <?php if (!empty($errors)) {
    foreach ($errors as $error) {
      echo "<p style='color: red;'>$error</p>";
    }
  }?>

  <!-- Afiseaza mesajul de succes aici -->
  <?php if (isset($successMessage)) {
    echo "<p style='color: green;'>$successMessage</p>";
  }?>
  
  <table>
    <tr>
      <th>Data</th>
      <th>Numărul de ore</th>
      <th>Departament</th>
      <th>Categorie</th>
      <th>Acțiune</th>
    </tr>
    <?php
    $query = "SELECT hoursWorked.id, hoursWorked.data, hoursWorked.numar_ore, departments.name as department_name, Categories.nume_activitate as category_name FROM hoursWorked 
    INNER JOIN departments ON hoursWorked.departament_id = departments.id
    INNER JOIN Categories ON hoursWorked.categorie_id = Categories.id
    WHERE user_id = $user_id AND data BETWEEN '$start_date' AND '$end_date'";
$result = mysqli_query($conn, $query);

if($result) {
while($row = mysqli_fetch_assoc($result)) {
echo '<tr>';
echo '<form action="LogOreSaptamanal.php" method="POST">';
echo '<td>' . $row['data'] . '</td>';
echo '<td><input type="number" id="numar_ore" name="numar_ore" min="1" max="8" value="' . $row['numar_ore'] . '" required></td>';
echo '<td>' . $row['department_name'] . '</td>';
echo '<td>' . $row['category_name'] . '</td>';
echo '<td><input type="hidden" id="id" name="id" value="' . $row['id'] . '"><input type="submit" value="Actualizează" name="submit"></td>';
echo '</form>';
echo '</tr>';
}
} else {
echo "Error: " . $query . "<br>" . mysqli_error($conn);
}

    ?>
  </table>
</div>
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
