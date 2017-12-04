<?php
session_start();



if((isset($_SESSION['online'])) && ($_SESSION['online']==true))
{
    header('Location: test.php');
    exit();
}

if (filter_input(INPUT_GET, 'active')) {
    require_once 'connect.php';
    $connect = @new mysqli($host, $db_user, $db_password, $db_name);

    if ($connect->connect_errno != 0) {
        echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
    } else {

        if ($connect->query("UPDATE user SET active=1 WHERE activation_key='$_GET[active]' ")) {
            print"Aktywacja ukonczona pomyślnie. Możesz już korzystać z naszego serwisu.";
        } else {
            print"Podano nieistniejący kod aktywacyjny.";
        }
    }
    $connect->close();
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
        
        <a href="registration.php"> REJESTRACJA </a>

        <form action="login.php" method="post">
            Login: <br/> <input type="text" name="login"/><br/>
            Hasło: <br/> <input type="password" name="password" /><br/> <br/>
            <input type="submit" value="Zalogujj się" />

        </form>
        <?php
        if(isset($_SESSION['error']))
        echo $_SESSION['error'];
        ?>   

    </body>
</html>

