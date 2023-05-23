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
if(mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $role = $row['role'];
}

//selecteaza department_id din tabela users folosind email-ul utilizatorului curent
$query = "SELECT department_id FROM users WHERE Email = '$email'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $department_id = $row['department_id'];

  //selecteaza numele departamentului din tabela departments folosind id-ul departamentului gasit in interogarea anterioara
  $query = "SELECT name FROM departments WHERE id = $department_id";
  $result = mysqli_query($conn, $query);

  if(mysqli_num_rows($result) == 1) {
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
if(mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $nume = $row['name'];
} else {
  $nume = "Nedefinit";
}

$query = "SELECT id, nume_activitate FROM Categories WHERE departament_id = $department_id";
$result_categories = mysqli_query($conn, $query);
if(!$result_categories) {
  die("Query failed: " . mysqli_error($conn));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // verificăm dacă toate datele necesare sunt setate și nu sunt goale
    if(isset($_POST['categorie']) && isset($_POST['numar_ore']) && isset($_POST['data'])){
      $categorie = $_POST['categorie'];
      $numar_ore = $_POST['numar_ore'];
      $data = $_POST['data'];
      $numeDepartament = $department_id;
  
      // Verificăm dacă data introdusă este în viitor
      $current_date = date("Y-m-d"); // Data curentă
      if ($data > $current_date) {
        $errors[] = "Nu puteti adauga ore pentru o data din viitor!";
      }
  
      // Verificăm dacă data introdusă este mai veche de două săptămâni
      $two_weeks_ago = date("Y-m-d", strtotime("-2 weeks")); 
      if ($data < $two_weeks_ago) {
        $errors[] = "Nu puteti adauga ore pentru o data mai veche de două săptămâni!";
      }
  
      if(empty($errors)) {
        // selectează user_id din tabelul users folosind email-ul utilizatorului curent
        $query = "SELECT id FROM users WHERE Email = '$email'";
        $result = mysqli_query($conn, $query);
        if(mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['id'];
  
        $query = "SELECT SUM(numar_ore) as total_ore FROM hoursWorked WHERE user_id = $user_id AND data = '$data'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);

        if ($row['total_ore'] + $numar_ore > 8) {
          $errors[] = "Numarul total de ore nu poate depasi 8 intr-o zi!";
        } else {
          // inserare date în baza de date
          $query = "INSERT INTO hoursWorked (user_id, departament_id, categorie_id, numar_ore, data) VALUES ($user_id, $numeDepartament,  $categorie, $numar_ore, '$data')";
          $result = mysqli_query($conn, $query);
          if($result) {
            $successMessage = "Orele au fost înregistrate cu succes!";
          } else {
            $errors[] = "Eroare la înregistrarea orelor!";
          }
        }

        }
      }
    } 
}
?>


<!DOCTYPE html>
<html>
<head>
	<title>Adaugare ore de lucru</title>
	<link rel="stylesheet" type="text/css" href="../style/styleHomeUser.css">
</head>
<body>
    <div class="header">
        <div id="clock"></div>
        <div >
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
        <!-- Afisez erorile  -->
        <?php if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<p style='color: red;'>$error</p>";
            }
        }?>

        <!-- Afisez mesajul de succes -->
        <?php if (isset($successMessage)) {
            echo "<p style='color: green;'>$successMessage</p>";
        }?>
        
        <form action="AdaugaureOreLucru.php" method="POST">
            <label for="categorie">Categorie:</label>
            <select id="categorie" name="categorie">
                <?php 
                    while($row = mysqli_fetch_assoc($result_categories)) {
                        echo "<option value='" . $row['id'] . "'>" . $row['nume_activitate'] . "</option>";
                    }
                ?>
            </select>
            <label for="numar_ore">Numărul de ore:</label>
            <input type="number" id="numar_ore" name="numar_ore" min="1" max="8" required>
            <label for="data">Data:</label>
            <input type="date" id="data" name="data" required>
            <input type="submit" value="Înregistrează" name="submit">
        </form>
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
