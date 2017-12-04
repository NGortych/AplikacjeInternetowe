<?php
session_start();

if(!isset($_SESSION['online']))
{
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8"/> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title>Logowanie</title>

    </head>
    <body>

        <?php
        echo"<p>Witaj " .$_SESSION['email'].'[<a href="logout.php">Wyloguj sie!</a>]</p>';
        ?>
    </body>
</html>

