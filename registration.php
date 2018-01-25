<?php
session_start();

if (isset($_POST['email_reg'])) {
    $successful_validation = true;

    //email
    $email = $_POST['email_reg'];
    $email_val = filter_var($email, FILTER_SANITIZE_EMAIL);

    if ((filter_var($email_val, FILTER_VALIDATE_EMAIL) == false)) {
        $successful_validation = false;
        $_SESSION['error_email'] = "Niepoprawny adres email.";
    }

    //hasło
    $password = $_POST['password1_registration'];
    $password2 = $_POST['password2_registration'];

    if ((strlen($password) < 8) || (strlen($password) > 20)) {
        $successful_validation = false;
        $_SESSION['error_password'] = "Hasło musi zawierać od 8 do 20 znaków";
    }

    if (($password != $password2)) {
        $successful_validation = false;
        $_SESSION['error_password'] = "Wprowadzone hasła nie są takie same.";
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    //Imie
    $name = $_POST['name_registration'];
    if ((strlen($name) < 3) || (strlen($name) > 20) || !preg_match('/^[A-ZŁŚ]{1}[a-ząęśżźćń]+$/', $name)) {
        $successful_validation = false;
        $_SESSION['error_name'] = "Wprowadzone imie nie jest prawidłowe.";
    }

    //Nazwisko
    $surname = $_POST['surname_registration'];
    if ((strlen($surname) < 3) || (strlen($surname) > 20) || !preg_match('/^[A-ZŁŚ]{1}[a-ząęśżźćń]+$/', $surname)) {
        $successful_validation = false;
        $_SESSION['error_surname'] = "Wprowadzone nazwisko nie jest prawidłowe.";
    }

    //typ
    $type = $_POST['type_registration'];

    //indeks
    $indexNM = $_POST['indexNM_registration'];
    if ($type == "Student" && (strlen($indexNM) != 6 || !preg_match('/^[0-9]+$/', $indexNM))) {
        $successful_validation = false;
        $_SESSION['error_indexNM'] = "Podany index jest nieprawidłowy.";
    }

    $study = $_POST['study_db'];
    if ($type == "Student" && $study == "") {
        $successful_validation = false;
        $_SESSION['error_study'] = "Wybierz swój kierunek studiów.";
    }

    $department = $_POST['department_db'];
    if ($type == "Nauczyciel" && $department == "") {
        $successful_validation = false;
        $_SESSION['error_department'] = "Wybierz swój wydział.";
    }


    //checkbox
    if (!isset($_POST['reulations'])) {
        $successful_validation = false;
        $_SESSION['error_reulations'] = "Potwierdź akceptację regulaminu.";
    }

    //CAPTCHA
    $captcha = $_POST['g-recaptcha-response'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $secretKey = "6Ld3YjoUAAAAAB_YAqJM2OP794f18mYiiE3F7szh";

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'secret' => $secretKey,
            'response' => $captcha,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);

    $output = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($output);

    if (!$json->success) {
        $successful_validation = false;
        $_SESSION['error_captcha'] = "Potwierdź, że nie jesteś botem.";
    }

    $imgFile = $_FILES['user_image']['name'];
    $tmp_dir = $_FILES['user_image']['tmp_name'];
    $imgSize = $_FILES['user_image']['size'];
    $userpic = "123456.jpg";


    if (!empty($imgFile)) {
        $upload_dir = 'user_images/';

        $imgExt = strtolower(pathinfo($imgFile, PATHINFO_EXTENSION));
        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif');

        $userpic = rand(1000, 1000000) . "." . $imgExt;


        if (in_array($imgExt, $valid_extensions)) {
            if ($imgSize < 5000000) {
                move_uploaded_file($tmp_dir, $upload_dir . $userpic);
            } else {
                $successful_validation = false;
                $_SESSION['error_image'] = "Plik jest za duży!";
            }
        } else {
            $successful_validation = false;
            $_SESSION['error_image'] = "Niedozwolony format pliku!";
        }
    }

    require_once 'connect.php';
    $connect = @new mysqli($host, $db_user, $db_password, $db_name);

    if ($connect->connect_errno != 0) {
        echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
    } else {
        $result = $connect->query("SELECT id FROM user WHERE email='$email'");
        $email_check = $result->num_rows;
        if ($email_check > 0) {
            $successful_validation = false;
            $_SESSION['error_email'] = "Konto już istnieje.";
        }
        if ($successful_validation == true) {
            if ($type === "Student") {
                $actCode = str_shuffle("qwertyuiopasdfghjklzxcvbnm1234567890");
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-2\r\n";
                $content = "Witaj " . $name . ". Aby aktywowac swoje konto kliknij ponizszy link aktywujacy:<br>
                                           <a href=\"http://localhost/test/index.php?active=" . $actCode . "\"> http://localhost/test/index.php?active=" . $actCode . " </a><br>
                                                ";
                mail($email, "Link Aktywacyjny", $content, $headers);
                if ($connect->query("INSERT INTO `user`(`id`, `email`, `password`, `name`, `surname`, `type`, `indexNM`, `id_study`, `id_department`,`activation_key`, `active`, `image`) VALUES(NULL,'$email','$passwordHash','$name','$surname','$type','$indexNM','$study',NULL,'$actCode',0, '$userpic')"))
                    echo "Zostałeś zarejestrowany. Na twój e-mail została wysłana wiadomość z kodem aktywayjnym.";
                else
                    echo "REJESTRACJA NIE POWIODŁĄ SIĘ." . $connect->error;
            } else {
                $actCode = str_shuffle("qwertyuiopasdfghjklzxcvbnm1234567890");
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-2\r\n";
                $content = "Witaj " . $name . ". Aby aktywowac swoje konto kliknij ponizszy link aktywujacy:<br>
                                           <a href=\"http://localhost/test/index.php?active=" . $actCode . "\"> http://localhost/test/index.php?active=" . $actCode . " </a><br>
                                                ";
                mail($email, "Link Aktywacyjny", $content, $headers);
                if ($connect->query("INSERT INTO `user`(`id`, `email`, `password`, `name`, `surname`, `type`, `indexNM`, `id_study`, `id_department`,`activation_key`, `active`, `image`) VALUES(NULL,'$email','$passwordHash','$name','$surname','$type',NULL,NULL,$department,'$actCode',0,'$userpic')"))
                    echo "Zostałeś zarejestrowany. Na twój e-mail została wysłana wiadomość z kodem aktywayjnym.";
                else
                    echo "REJESTRACJA NIE POWIODŁĄ SIĘ." . $connect->error;
            }
        }
        $connect->close();
    }
}
?>

<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8"/> 
        <meta http-equiv="X-UA-Compatible" content="IE = edge, chrome = 1"/>
        <title>Rejestracja</title>
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <link href="css/bootstrap.min.css" rel="stylesheet" >
        <script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="js.js"></script>
        <link href="style.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
        <script src="js/bootstrap.min.js"></script>

    </head>
    <body>

        <div id="container">

            <header>

                <h1 class="logo">REJESTRACJA</h1>    

                <nav class="navbar navbar-toggleable-sm navbar-light bg-faded" id="topnav">

                    <button class="navbar-toggler navbar-toggler-right menu_button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto menu">
                            <li class="nav-item">
                                <a href="index.php">STRONA GŁÓWNA</a>
                            </li>                            
                        </ul>
                    </div>
                </nav>

            </header>

            <div>
                <form method="post" enctype="multipart/form-data" >
                    <div id="reg_form">

                        <div class=" reg_fields">E-mail: <br/> <input type="text" name="email_reg" />  </div>

                        <?php
                        if (isset($_SESSION['error_email'])) {
                            echo '<div class=" error">' . $_SESSION['error_email'] . '</div>';
                            unset($_SESSION['error_email']);
                        }
                        ?>

                        <div class="reg_fields">Hasło: </br> <input type="password" name="password1_registration"/></div>

                        <?php
                        if (isset($_SESSION['error_password'])) {
                            echo '<div class="error">' . $_SESSION['error_password'] . '</div>';
                            unset($_SESSION['error_password']);
                        }
                        ?>

                        <div class="reg_fields">Powtórz hasło: <br/> <input type="password" name="password2_registration"/></div>

                        <div class="reg_fields">Imię: <br/> <input type="text" name="name_registration" /> </div>
                        <?php
                        if (isset($_SESSION['error_name'])) {
                            echo '<div class="error">' . $_SESSION['error_name'] . '</div>';
                            unset($_SESSION['error_name']);
                        }
                        ?>

                        <div class="reg_fields">Nazwisko: <br/> <input type="text" name="surname_registration" /> </div>
                        <?php
                        if (isset($_SESSION['error_surname'])) {
                            echo '<div class="error">' . $_SESSION['error_surname'] . '</div>';
                            unset($_SESSION['error_surname']);
                        }
                        ?>

                        <div class="reg_fields">Typ: <br/>

                            <select id="type_of_user"  class="form-control" name="type_registration" onchange="
                                    displayFields()">
                                <option>Student</option>
                                <option>Nauczyciel</option>
                            </select>
                        </div>
                        <div id="indexNM" class="reg_fields">Numer indeksu:<br/> <input type="text" name="indexNM_registration" /> </div>
                        <?php
                        if (isset($_SESSION['error_indexNM'])) {
                            echo '<div class="error">' . $_SESSION['error_indexNM'] . '</div>';
                            unset($_SESSION['error_indexNM']);
                        }
                        ?>

                        <div id="study" class="reg_fields">Kierunek studiów:
                            <br/>
                            <?php
                            require_once 'connect.php';
                            $connect = @new mysqli($host, $db_user, $db_password, $db_name);

                            if ($connect->connect_errno != 0) {
                                echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
                            } else {
                                $result = $connect->query("SELECT * FROM study");
                                echo '<select class="form-control" name="study_db">';
                                echo '<option value=""> Kierunek studiów </option>';
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['id_study'] . '">' . $row['name'] . '</option>';
                                }
                                echo '</select></div>';
                                $result->free_result();
                                if (isset($_SESSION['error_study'])) {
                                    echo '<div class="error">' . $_SESSION['error_study'] . '</div>';
                                    unset($_SESSION['error_study']);
                                }
                            }
                            ?>

                            <?php
                            if ($connect->connect_errno != 0) {
                                echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
                            } else {
                                $result = $connect->query("SELECT * FROM department");
                                echo '<div id="department" class="reg_fields" style="display:none;">Wydział: <br/>';
                                echo '<select class="form-control" name="department_db">';
                                echo '<option value=""> Wydział </option>';
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['id_department'] . '">' . $row['name'] . '</option>';
                                }
                                echo '</select></div>';
                                $result->free_result();
                                if (isset($_SESSION['error_department'])) {
                                    echo '<div class="error">' . $_SESSION['error_department'] . '</div>';
                                    unset($_SESSION['error_department']);
                                }
                            }
                            ?>

                            <div class="reg_fields">
                                <label>
                                    <input type="checkbox" name="reulations"/> Akceptuje regulamin
                                </label>
                            </div>

                            <?php
                            if (isset($_SESSION['error_reulations'])) {
                                echo '<div class="error">' . $_SESSION['error_reulations'] . '</div>';
                                unset($_SESSION['error_reulations']);
                            }
                            ?>


                            <div class="g-recaptcha" data-sitekey="6Ld3YjoUAAAAAMlA7-JvXqp2ulkBPAucq5oMvvE5"></div>

                            <?php
                            if (isset($_SESSION['error_captcha'])) {
                                echo '<div class="error">' . $_SESSION['error_captcha'] . '</div>';
                                unset($_SESSION['error_captcha']);
                            }
                            ?>
                            </br>
                            <input type="submit" value="Zarejestruj się" class="button"/>



                        </div>
                    </div>
                    <div id="reg_image">
                        <p>Zdjęcie profilowe (opcjonalnie)</p>
                        <img id="blah" src="user_images/123456.jpg" width="300" height="400" />
                        <br/>
                        <br/>
                        <label class="btn upload_button">
                            Wybierz plik... <input type="file" name="user_image" accept="image/*" onchange="readURL(this);" hidden>
                        </label>
                        <?php
                        if (isset($_SESSION['error_image'])) {
                            echo '<div class="error">' . $_SESSION['error_image'] . '</div>';
                            unset($_SESSION['error_image']);
                        }
                        ?>

                    </div>

                    <div style="clear:both;"></div>
                </form>
                <div class="push"></div>
            </div>
        </div>
        <footer class="footer">

            &copy; 2018 Aplikacje Internetowe 

        </footer>
    </body>
</html>

