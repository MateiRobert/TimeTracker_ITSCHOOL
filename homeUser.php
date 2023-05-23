<?php
require_once 'inc/db.inc.php'; // Se face conexiunea la baza de date
session_start();
if (!isset($_SESSION['email'])) { // Se verifică dacă există email în sesiune
  header("Location: Index.php"); // Se redirecționează utilizatorul către pagina de autentificare
  exit();
}

$email = $_SESSION['email'];

// Se selectează rolul utilizatorului din tabela users folosind email-ul utilizatorului curent
$query = "SELECT role FROM users WHERE Email = '$email'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $role = $row['role'];
}

// Se selectează department_id din tabela users folosind email-ul utilizatorului curent
$query = "SELECT department_id FROM users WHERE Email = '$email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $department_id = $row['department_id'];

  // Se selectează numele departamentului din tabela departments folosind id-ul departamentului găsit în interogarea anterioară
  $query = "SELECT name FROM departments WHERE id = $department_id";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $department_name = $row['name'];
  } else {
    header("Location: Index.php");
  }
} else {
  header("Location: Index.php");
}



$query = "SELECT name FROM users WHERE Email = '$email'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $nume = $row['name'];
} 

$department = $department_name;
?>









<!DOCTYPE html>
<html>
<head>
	<title>Home (user)</title>
	<link rel="stylesheet" type="text/css" href="style/styleHomeUser.css">
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
        <?php 
        if ($role === 'administrator') {
          echo '<div><a href="HomeAdmin.php" target="_self">Back to administrator</a></div>';
        }
        
        ?>
        <div><a href="inc/logout.php" target="_self">Log Out</a></div>
    </div>




    <br>    
    <br>
	<div class="line"></div>
	
	<div class="card-container">
		<div class="card" onclick="location.href='User/AdaugaureOreLucru.php'">
			<h2>Adăugare a orelor de lucru</h2>
			<p>Nu mai mult de 8 ore pe zi</p>
		</div>
		<div class="card" onclick="location.href='User/LogOreSaptamanal.php'">
			<h2>Vizualizare a orelor logate</h2>
			<p>Orele logate din ultima săptămână cu optiunea de a schimba orele de lucru.</p>
		</div>
		<div class="card" onclick="location.href='User/SchimbaDatePersonale.php'">
			<h2>Modificare date personale</h2>
			<p>Email-ul, numărul de telefon, numele sau parola...</p>
		</div>
    <div class="card" onclick="location.href='/User/LogOreTotal.php'">
      <h2>Sumar al orelor logate </h2>
      <p>Pentru a vizualiza suma orelor logate pe fiecare departament și categorie.</p>
	</div>
  <br>
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