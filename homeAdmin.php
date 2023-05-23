<?php
session_start(); //start sesiune
include_once 'inc/db.inc.php'; //conexiunea la baza de date facuta ini fisier extern

if(!isset($_SESSION['email'])) { //verificare dacă nu există email în sesiune
  header("Location: Index.php");  // trimite utilizatorul la pagina de login dacă nu este autentificat
  exit(); //oprește executarea scriptului
}

if (isset($_SESSION['email'])) { //verificare dacă există email în sesiune
  $email = $_SESSION['email']; //atribuirea email-ului din sesiune variabilei $email

  // Obține rolul utilizatorului din baza de date
  $query = "SELECT role FROM users WHERE email='$email'"; //interogare pentru a obține rolul utilizatorului
  $result = mysqli_query($conn, $query); //executarea interogării

  
  if (mysqli_num_rows($result) == 1) { //verificare dacă interogarea a returnat un singur rând
    $user = mysqli_fetch_assoc($result); //atribuirea rândului returnat variabilei $user
    $role = $user['role']; //atribuirea rolului din rândul returnat variabilei $role

    // Dacă utilizatorul este un user, redirecționează către HomeUser.php
    if ($role != 'administrator') { //verificare dacă rolul utilizatorului nu este administrator
      header("Location: HomeUser.php"); //redirecționare către pagina HomeUser.php
      exit(); //oprește executarea scriptului
    } 
  }
}




//selectare department_id din tabela users folosind email-ul utilizatorului curent
$query = "SELECT department_id FROM users WHERE Email = '$email'"; // interogare pentru a obține id-ul departamentului
$result = mysqli_query($conn, $query); //executarea interogării

if(mysqli_num_rows($result) == 1) { //verificare dacă interogarea a returnat un singur rând
  $row = mysqli_fetch_assoc($result); //atribuirea rândului returnat variabilei $row
  $department_id = $row['department_id']; //atribuirea id-ului departamentului din rândul returnat variabilei $department_id

  //selectare numele departamentului din tabela departments folosind id-ul departamentului gasit in interogarea anterioara
  $query = "SELECT name FROM departments WHERE id = $department_id"; //interogare pentru a obține numele departamentului
  $result = mysqli_query($conn, $query); //executarea interogării

  if(mysqli_num_rows($result) == 1) { 
    $row = mysqli_fetch_assoc($result); 
    $department_name = $row['name']; 
  } 
}  


//selectare nume utilizator din tabela users folosind email-ul utilizatorului curent
$query = "SELECT name FROM users WHERE Email = '$email'";
$result = mysqli_query($conn, $query);
if(mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $nume = $row['name'];
}


//afisare nume departament in locul [role]
$department = $department_name; 

?>





<!DOCTYPE html>
<html>
<head>
	<title>Home (admin)</title>
	<link rel="stylesheet" type="text/css" href="style/styleHomeAdmin.css">
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
        <div><a href="HomeUser.php" target="_self">View as USER</a></div>
        <div><a href="inc/logout.php" target="_self">Log Out</a></div>
    </div>



    <br>
	<div class="line"></div>
	
	<div class="card-container">
		<div class="card" onclick="location.href='Admin/AdaugaDepartament.php'">
			<h2>Adaugă departament</h2>
			<p>Adăugare departamente în baza de date</p>
		</div>
		<div class="card" onclick="location.href='Admin/AdaugaCategorie.php'">
			<h2>Adaugă categorie</h2>
			<p>Adăugarea de noi categorii pentru departamente</p>
		</div>
		<div class="card" onclick="location.href='Admin/MutaUtilizatori.php'">
			<h2>Mută utilizatori</h2>
			<p>Mutarea utilizatorilor dintr-un departament în altul</p>
		</div>
		<div class="card" onclick="location.href='Admin/LogOreDepartament.php'">
			<h2>Vizualizează orele</h2>
			<p>Vizualizarea orelor logate de utilizatorii dintr-un anumit departament</p>
		</div>
		<div class="card" onclick="location.href='Admin/SetUserInactive.php'">
			<h2>Dezactivează utilizator / Dezactiveaza utilizator</h2>
      <P>Dezactivarea/Activarea utilizatorilor</P>
		</div>
		<div class="card" onclick="location.href='Admin/ModificareUtilizator.php'">
			<h2>Modifică date utilizator</h2>
      <P>Modificarea datelor utilizatorilor</P>
		</div>
    <div class="card" onclick="location.href='Admin/LogIstoricPersoana.php'">
			<h2>Istoric activități</h2>
			<p>Istoric activitate per utilizator</p>
		</div>
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

