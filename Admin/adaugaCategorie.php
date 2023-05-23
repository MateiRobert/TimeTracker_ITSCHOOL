<?php
session_start();
include_once '../inc/db.inc.php';
include_once 'functions.php'; // includem fisierul cu functii comune

$email = $_SESSION['email'];
$department = '';
$category = '';

verifyUser($_SESSION);
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
$department = isset($department_name[$department_id]) ? $department_name[$department_id] : '';

function getCategories($conn, $selected_department) {
  $stmt = $conn->prepare("SELECT nume_activitate FROM categories WHERE departament_id = ?");
  $stmt->bind_param("i", $selected_department);
  $stmt->execute();
  $result = $stmt->get_result();
  $categories_result = array();

  while ($row = $result->fetch_assoc()) {
    $categories_result[] = $row['nume_activitate'];
  }
  return $categories_result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['department_select']) && !empty($_POST['new_category'])) {
  $new_category = $_POST['new_category'];
  $selected_department = mysqli_real_escape_string($conn, $_POST['department_select']);

  // validare nume categorie
  if (!preg_match("/^[a-zA-Z0-9 _-]*$/", $new_category)) {
    echo "<p>Numele categoriei conține caractere nepermise.</p>";
  } else {
    $new_category = mysqli_real_escape_string($conn, $new_category);
    $stmt = $conn->prepare("INSERT INTO categories (nume_activitate, departament_id) VALUES (?, ?)");
    $stmt->bind_param("si", $new_category, $selected_department);

    if ($stmt->execute()) {
      echo "<p>Categoria a fost adăugată cu succes.</p>";
    } else {
      echo "<p>A apărut o eroare la adăugarea categoriei.</p>";
    }
  }
}


$categories_result = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['department_select'])) {
  $selected_department = mysqli_real_escape_string($conn, $_POST['department_select']);
  $categories_result = getCategories($conn, $selected_department);
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Adaugare Categorie</title>
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
        <div><a href="../HomeAdmin.php" target="_self">Home</a></div>
        <div><a href="../inc/logout.php" target="_self">Log Out</a></div>
    </div>

    <br>
	<div class="line"></div>
	
    <form method="post">
  <label for="department_select">Selectează Departamentul:</label>
  <select name="department_select" id="department_select">
    <?php
      foreach ($department_name as $id => $name) {
        echo "<option value='$id'>$name</option>";
      }
    ?>
  </select>
  <input type="submit" value="Selectează">
</form>
<?php
  $selected_department_name = '';
  if (isset($_POST['department_select'])) {
    $selected_department_name = $department_name[$_POST['department_select']];
  }
?>
</br>
<table>
<thead>
  <tr>
    <th>Categorie pentru Departamentul <?php echo $selected_department_name; ?></th>
  </tr>
</thead>
<tbody>
  <?php
  foreach ($categories_result as $category) {
    echo "<tr><td>$category</td></tr>";
  }
  ?>
</tbody>
</table>

<form method="post">
  <?php if(isset($_POST['department_select'])): ?>
    <input type="hidden" name="department_select" value="<?php echo $_POST['department_select']; ?>">
  <?php endif; ?>
  </br>
  <label for="new_category">Noua categorie:</label>
  <input type="text" id="new_category" name="new_category">
  <input type="submit" value="Adaugă Categorie">
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
