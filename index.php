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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
        <script src="js/bootstrap.min.js"></script>

        <title>Logowanie</title>

    </head>
    <body>

        <div id="container">

            <header>
                <h1 class="logo">STRONA GŁÓWNA</h1>    

                <nav class="navbar navbar-toggleable-sm navbar-light bg-faded" id="topnav">
                    <button class="navbar-toggler navbar-toggler-right menu_button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto menu">
                            <li class="nav-item">
                                <a href="registration.php">REJESTRACJA</a>
                            </li>                            
                        </ul>
                    </div>
                </nav>

            </header>
            <div id="main_page">
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

