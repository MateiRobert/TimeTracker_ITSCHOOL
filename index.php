<?php
session_start();
include_once 'inc/db.inc.php';

$errors = []; // Array pentru a stoca erorile

// Verific dacă sunt deja autentificat
if (isset($_SESSION['email'])) {
  $email = $_SESSION['email'];

  // Obține rolul și starea de activitate a utilizatorului din baza de date
  $stmt = $conn->prepare("SELECT role, is_active FROM users WHERE email=?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $role = $user['role'];
    $is_active = $user['is_active'];

    // Verific starea de activitate a utilizatorului
    if ($is_active == 1) {
      // Dacă sunt un admin, redirecționez către HomeAdmin.php
      if ($role == 'administrator') {
        header("Location: homeAdmin.php");
        exit();
      }
      // Dacă sunt un user, redirecționez către HomeUser.php
      elseif ($role == 'user') {
        header("Location: homeUser.php");
        exit();
      }
    } else {
      $errors[] = "Contul tău este inactiv.";
    }
  }
}

// Autentificare
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['login'])) {
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
      $user = $result->fetch_assoc();
      if (password_verify($password, $user['password'])) {
        $is_active = $user['is_active'];

        if ($is_active == 1) {
          $_SESSION['email'] = $email;
          // Redirecționez către pagina potrivită în funcție de rol
          if ($user['role'] == 'administrator') {
            header("Location: homeAdmin.php");
            exit();
          } elseif ($user['role'] == 'user') {
            header("Location: homeUser.php");
            exit();
          }
        } else {
          $errors[] = "Contul tău este inactiv.";
        }
      } else {
        $errors[] = "Parolă incorectă.";
      }
    } else {
      $errors[] = "Email incorect.";
    }
  }
  else if (isset($_POST['register'])) {
    $name = trim(htmlspecialchars($_POST['name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));
    $repeat_password = trim(htmlspecialchars($_POST['repeat_password']));
    $department = trim(htmlspecialchars($_POST['department']));

    // Verificare dacă toate câmpurile au fost completate
    if (empty($name) || empty($email) || empty($password) || empty($repeat_password) || empty($department)) {
      $errors[] = "Vă rugăm să completați toate câmpurile.";
    } else {
      // Verificare dacă parolele coincid
      if ($password !== $repeat_password) {
        $errors[] = "Parolele nu coincid.";
      } else {
        // Verificare dacă parola îndeplinește cerințele
        if (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$&*]).{6,24}$/', $password)) {
          $errors[] = "Parola trebuie să conțină între 6 și 24 de caractere, cel puțin o literă mare și un caracter special.";
        } else {
        // Verificare dacă adresa de email este validă
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $errors[] = "Adresa de email nu este validă.";
        } else {
          // Verificare dacă adresa de email este deja înregistrată
          $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
            $errors[] = "Această adresă de email este deja înregistrată.";
          } else {
            // Hash parola
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Înregistrare noul utilizator cu starea de activitate setată la 0
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, department_id, is_active) VALUES (?, ?, ?, ?, 0)");
            $stmt->bind_param("sssi", $name, $email, $hashed_password, $department);
            $stmt->execute();

            if ($stmt->affected_rows == 1) {
              $errors[] = "Înregistrare cu succes.";
            } else {
              $errors[] = "A apărut o eroare în timpul înregistrării.";
            }
          }
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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title> Logare / Inregistrare </title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="style/styleStart.css">
</head>
<body>
<div class="error-messages">
  <?php foreach ($errors as $error): ?>
    <p><?php echo $error; ?></p>
  <?php endforeach; ?>
</div>
  <div class="body">
    <div class="veen">
      <div class="login-btn splits">
        <p>Deja utilizator?</p>
        <button class="active">Autentificare</button>
      </div>
      <div class="rgstr-btn splits">
        <p>Nu aveți un cont?</p>
        <button>Înregistrare</button>
      </div>
      <div class="wrapper">
        <form id="login" tabindex="500" method="post">
          <h3>Autentificare</h3>
          <div class="mail">
              <input type="mail" name="email">
              <label>E-mail</label>
          </div>
          <div class="passwd">
              <input type="password" name="password">
              <label>Parolă</label>
          </div>
          <div class="submit">
              <button class="dark" name="login">Autentificare</button>
          </div>
        </form>

        <form id="register" tabindex="502" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
  <h3>Înregistrare</h3>
  <div class="name">
    <input type="text" name="name" required>
    <label>Nume complet</label>
  </div>
  <div class="mail">
    <input type="email" name="email" required>
    <label>E-mail</label>
  </div>
  <div class="passwd">
    <input type="password" name="password" required>
    <label>Parolă</label>
  </div>
  <div class="repasswd">
    <input type="password" name="repeat_password" required>
    <label>Repetare parolă</label>
  </div>
  <div class="department">
    <label>Departament:</label>
  </br>
    <select name="department" required>
      <option value="">Alegeți departamentul</option>
      <option value="2">Marketing</option>
      <option value="3">Productie</option>
      <option value="4">Financiar</option>
    </select>
  </div>
  
  <div class="submit">
    <button class="dark" type="submit" name="register">Înregistrare</button>
  </div>
</form>

      </div>
    </div>  
  </div>
  <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" ></script>
  <script src="script/scriptStart.js"></script>
</body>
</html>