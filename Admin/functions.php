<?php

function verifyUser($session) {
  if (!isset($session['email'])) {
    header("Location: StartSeite.php");
    exit();
  }
}

function getUserRole($conn, $email) {
  $stmt = $conn->prepare("SELECT role FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    return $user['role'];
  } else {
    throw new Exception('Eroare la interogarea rolului utilizatorului');
  }
}

function getUserDepartment($conn, $email) {
  $stmt = $conn->prepare("SELECT department_id, name FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows == 1) {
    return $result->fetch_assoc();
  } else {
    throw new Exception('Eroare la interogarea informațiilor utilizatorului');
  }
}

function getDepartmentNames($conn) {
  $result = $conn->query("SELECT id, name FROM departments");
  $department_name = array();
  while ($row = $result->fetch_assoc()) {
    $department_name[$row['id']] = $row['name'];
  }
  return $department_name;
}





?>

