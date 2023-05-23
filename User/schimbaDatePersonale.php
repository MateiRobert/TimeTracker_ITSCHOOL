<?php
session_start();
include_once '../inc/db.inc.php';

$errors = []; // Initializare array pentru erori
$successMessage = ""; // Initializare string pentru mesajul de succes

if(!isset($_SESSION['email'])) {
  header("Location: StartSeite.php");
  exit();
}

if (isset($_SESSION['email'])) {
  $email = $_SESSION['email'];

  $query = "SELECT role FROM users WHERE email='$email'";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    $role = $user['role'];
  }
}

$email = $_SESSION['email'];

$query = "SELECT department_id FROM users WHERE Email = '$email'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);
  $department_id = $row['department_id'];

  $query = "SELECT name FROM departments WHERE id = $department_id";
  $result = mysqli_query($conn, $query);

  if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $department_name = $row['name'];
  } 
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

$id_utilizator = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email='$id_utilizator'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$nume = $row['name'];
$email = $row['email'];
$parola = $row['password'];
$numar_telefon = $row['phone_number'];

if($_SERVER["REQUEST_METHOD"] == "POST"){
  $nume_nou = $_POST['nume'];
  $email_nou = $_POST['email'];
  $parola_noua = $_POST['parola'];
  $parola_repetata = $_POST['reparola'];
  $numar_telefon_nou = $_POST['numar_telefon'];

  if($parola_noua == $parola_repetata){
    $sql = "UPDATE users SET name='$nume_nou', email='$email_nou', phone_number='$numar_telefon_nou' WHERE email='$id_utilizator'";
    if (!empty($parola_noua)) { 
      $parola_noua_hash = password_hash($parola_noua, PASSWORD_DEFAULT); 
      $sql = "UPDATE users SET name='$nume_nou', email='$email_nou', password='$parola_noua_hash', phone_number='$numar_telefon_nou' WHERE email='$id_utilizator'";
    }
    if(mysqli_query($conn, $sql)){ 
      $successMessage = "Actualizare cu succes!";
    }else{
      $errors[] = "Eroare: " . mysqli_error($conn);
    }
  }else{
    $errors[] = "Parolele nu se potrivesc!";
  }
}

?>


<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../style/styleHomeUser.css">
<style> 
.error {
  color: red;
  }
  
.success {
  color: green;
  }
</style>
</head>
<title>Schimba date personale</title>
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
        <div><a href="../homeUser.php" target="_self">Acasa</a></div>
        <div><a href="../inc/logout.php" target="_self">Log Out</a></div>
    </div>
    </div>

    <br>
	<div class="line"></div>

    <!-- Afisarre erori si mesajul de succes -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <div class="success">
            <p><?php echo $successMessage; ?></p>
        </div>
    <?php endif; ?>

	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
      <table>
        <tr>
          <td>Nume:</td>
          <td><input type="text" name="nume" placeholder="Nume" value="<?php echo $nume; ?>"></td>
        </tr>
        <tr>
          <td>Email:</td>
          <td colspan="3"><input type="email" name="email" placeholder="Email" value="<?php echo $email; ?>"></td>
        </tr>
        <tr>
          <td>Parola:</td>
          <td><input type="password" name="parola" placeholder="Scrie noua parola"></td>
          <td colspan="2"></td>
          <td>Rescrie parola:</td>
          <td><input type="password" name="reparola" placeholder="Rescrie noua parola"></td>
        </tr>
        <tr>
          <td>Nr. telefon:</td>
          <td><input type="text" name="numar_telefon" placeholder="Nr. telefon" value="<?php echo $numar_telefon; ?>"></td>
        </tr>
      </table>
      <div class="buttons">
        <input type="submit" value="Salvare">
      </div>
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

