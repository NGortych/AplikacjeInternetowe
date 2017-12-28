<?php
session_start();


if ((isset($_SESSION['online'])) && ($_SESSION['online'] == true)) {
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
        <link href="css/bootstrap.min.css" rel="stylesheet" >
        <link href="style.css" rel="stylesheet">

        <title>Logowanie</title>

    </head>
    <body>

        <div id="container">
            <div id="main_page">
                <div id="logo">

                    <h1>STRONA GŁÓWNA</h1>

                </div>
                <div id="menu">
                    <a href="registration.php"><div class="option"> REJESTRACJA </div></a>
                    <div style="clear:both;"></div>
                </div>


                <form action="login.php" method="post">
                    <div class="reg_fields">Login: <br/> <input type="text" name="login"/><br/></div>
                    <div class="reg_fields">Hasło: <br/> <input type="password" name="password" /><br/> </div> <br/>
                    <input type="submit" value="Zaloguj się" class="btn button " />

                </form>
                <?php
                if (isset($_SESSION['error']))
                    echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>   
            </div>
        </div>
    </body>
</html>

