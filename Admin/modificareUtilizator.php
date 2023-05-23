<?php
session_start();
include_once '../inc/db.inc.php';

$errors = []; 
$successMessage = "";
$users = [];
$user_id = null;
$department = '';

if(!isset($_SESSION['email'])) {
  header("Location: StartSeite.php");
  exit();
}

// Verificăm dacă utilizatorul este autentificat
if (isset($_SESSION['email'])) {
  $email = $_SESSION['email'];

  // Obținem informațiile utilizatorului autentificat din baza de date
  $stmt = $conn->prepare("SELECT role, name, department_id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();

  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $role = $user['role'];
    $name = $user['name'];
    $department_id = $user['department_id'];
    
    // Obținem numele departamentului utilizatorului
    $stmt_dep = $conn->prepare("SELECT name FROM departments WHERE id = ?");
    $stmt_dep->bind_param("i", $department_id);
    $stmt_dep->execute();

    $result_dep = $stmt_dep->get_result();

    if($result_dep->num_rows == 1) {
      $row_dep = $result_dep->fetch_assoc();
      $department = $row_dep['name'];
    }
  }
  
  // Obținem lista de utilizatori
  $stmt_users = $conn->prepare("SELECT * FROM users");
  $stmt_users->execute();

  $result_users = $stmt_users->get_result();

  while ($row = $result_users->fetch_assoc()) {
    $users[] = $row;
  }
}

// Verificăm dacă a fost selectat un utilizator pentru actualizare
if (isset($_GET['user_id'])) {
  $user_id = $_GET['user_id'];

  // Obținem informațiile utilizatorului selectat
  $stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
  $stmt_user->bind_param("i", $user_id);
  $stmt_user->execute();

  $result_user = $stmt_user->get_result();

  if($result_user->num_rows == 1) {
    $row_user = $result_user->fetch_assoc();
    $selected_user_name = $row_user['name'];
    $selected_user_email = $row_user['email'];
    $selected_user_password = $row_user['password'];
    $selected_user_phone_number = $row_user['phone_number'];
  }
}

// Verificăm dacă a fost trimis un formular de actualizare a utilizatorului
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['user_id'])){
  $user_id = $_GET['user_id'];
  $nume_nou = $_POST['nume'];
  $email_nou = $_POST['email'];
  $parola_noua = $_POST['parola'];
  $parola_repetata = $_POST['reparola'];
  $numar_telefon_nou = $_POST['numar_telefon'];

  // Validăm parola
  if ($parola_noua != $parola_repetata) {
    $errors[] = "Parolele nu se potrivesc!";
  } elseif (strlen($parola_noua) < 6 || strlen($parola_noua) > 24) {
    $errors[] = "Parola trebuie să aibă între 6 și 24 de caractere!";
  } elseif (!preg_match("/[a-z]/", $parola_noua) || !preg_match("/[A-Z]/", $parola_noua) || !preg_match("/[^a-zA-Z0-9]/", $parola_noua)) {
    $errors[] = "Parola trebuie să conțină cel puțin o literă mare, o literă mică și un semn special!";
  } else {
    if (!empty($parola_noua)) {
      // Hashăm parola nouă înainte de a o salva în baza de date
      $parola_noua_hash = password_hash($parola_noua, PASSWORD_DEFAULT);
      $stmt_update = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, phone_number = ? WHERE id = ?");
      $stmt_update->bind_param("ssssi", $nume_nou, $email_nou, $parola_noua_hash, $numar_telefon_nou, $user_id);
    } else {
      $stmt_update = $conn->prepare("UPDATE users SET name = ?, email = ?, phone_number = ? WHERE id = ?");
      $stmt_update->bind_param("sssi", $nume_nou, $email_nou, $numar_telefon_nou, $user_id);
    }

    // Executăm interogarea de actualizare
    if($stmt_update->execute()){
      $successMessage = "Actualizare cu succes!";
    }else{
      $errors[] = "Eroare: " . $stmt_update->error;
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../style/styleHomeAdmin.css">
<style> 
.error {
  color: red;
}

.success {
  color: green;
}
</style>
</head>
<title>Modificare Date Personal</title>
<body>
    <div class="header">
        <div id="clock"></div>
        <div>
  <?php
    //informațiile utilizatorului autentificat
    if ($role === 'user') {
      echo $name . '[<span style="color: green;">' . $role . '</span>]';
    } else {
      echo $name . '[<span style="color: red;">' . $role . '</span>]';
    }
  ?>
  </div>

        <div><?php echo "DEPARTAMENT: ".$department ;?></div>
        <div><a href="../HomeAdmin.php" target="_self">Acasa</a></div>
        <div><a href="../inc/logout.php" target="_self">Log Out</a></div>
    </div>
    </div>

    <br>
    <div class="line"></div>

    <!-- Mesaje eroare / succes -->
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
    
    <!-- Afisare formular de act. daca user este ales -->
    <?php if (isset($user_id)): ?>
      <form action="<?php echo $_SERVER['PHP_SELF'];?>?user_id=<?php echo $user_id;?>" method="post">
      <table>
        <tr>
          <td>Nume:</td>
          <td><input type="text" name="nume" placeholder="Nume" value="<?php echo $selected_user_name; ?>"></td>
        </tr>
        <tr>
          <td>Email:</td>
          <td colspan="3"><input type="email" name="email" placeholder="Email" value="<?php echo $selected_user_email; ?>"></td>
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
          <td><input type="text" name="numar_telefon" placeholder="Nr. telefon" value="<?php echo $selected_user_phone_number; ?>"></td>
        </tr>
      </table>
      <div class="buttons">
        <input type="submit" value="Salvare">
      </div>
    </form>

    <?php else: ?>
        <?php foreach ($users as $user): ?>
            <p>
                <?php echo htmlspecialchars($user['name']); ?>
                <a href="?user_id=<?php echo $user['id']; ?>">Modifică</a>
            </p>
        <?php endforeach; ?>
    <?php endif; ?>

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
