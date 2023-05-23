<?php
session_start();
session_destroy(); //distruge sesiunea curentă

header("Location: ../index.php"); //redirecționare utilizator la pagina de start
exit();
?>