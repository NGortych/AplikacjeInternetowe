<?php

session_start();
unset($_SESSION['myThesisStatus']);
session_unset();

header('Location: index.php');

?>

