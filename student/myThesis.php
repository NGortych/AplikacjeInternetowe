<?php
session_start();
if (!isset($_SESSION['online'])) {
    header('Location: index.php');
    exit();
} elseif (!isset($_SESSION['myThesisStatus'])) {
    require_once '../connect.php';
    $connect = @new mysqli($host, $db_user, $db_password, $db_name);
    $id = $_SESSION['id'];
    if ($result = $connect->query("SELECT * FROM thesis WHERE id_student = '$id'")) {
        $thesis_count = $result->num_rows;
        if ($thesis_count > 0) {
            $_SESSION['myThesisStatus'] = 1;
        } else {
            $_SESSION['myThesisStatus'] = 0;
        }
    }
}


if (isset($_POST['emailM'])) {

    $email = $_POST['emailM'];
    $name = $_POST['nameM'];
    $surname = $_POST['surnameM'];
    $indexNM = $_POST['index_NM'];
    if (isset($_POST['passwordM'])) {
        $password = $_POST['passwordM'];
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $passwordHash = $_SESSION['password'];
    }

    if (isset($_POST['studyM'])) {
        $study = $_POST['studyM'];
    } else {
        $study = $_SESSION['study_id'];
    }

    require_once '../connect.php';
    $connect = @new mysqli($host, $db_user, $db_password, $db_name);

    if ($connect->connect_errno != 0) {
        echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
    } else {
        $id = $_SESSION['id'];
        if (@$connect->query("UPDATE `user` SET `email`='$email', `password`='$passwordHash', `name`='$name',`surname`='$surname',`indexNM`=' $indexNM',`id_study`=' $study' WHERE id ='$id'")) {

            $_SESSION['email'] = $email;
            $_SESSION['name'] = $name;
            $_SESSION['surname'] = $surname;
            $_SESSION['indexNM'] = $indexNM;
            $_SESSION['study_id'] = $study;
        } else {
            echo "BLĄD !!!" . $connect->error;
        }
    }
}






if (filter_input(INPUT_GET, 'reserve')) {
    require_once '../connect.php';
    $connect = @new mysqli($host, $db_user, $db_password, $db_name);
    if ($connect->connect_errno != 0) {
        echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
    } elseif ($_SESSION['myThesisStatus'] == 0) {
        $id_student = $_SESSION['id'];
        if ($connect->query("UPDATE thesis SET status=1, id_student ='$_SESSION[id]' WHERE id_thesis='$_GET[reserve]' ")) {
            $resultTeacherId = $connect->query("SELECT * FROM thesis WHERE id_thesis = '$_GET[reserve]'");
            $rowTeacherId = $resultTeacherId->fetch_assoc();
            $id_teacher = $rowTeacherId['id_teacher'];
            $resultTeacherEmail = $connect->query("SELECT * FROM user WHERE id = '$id_teacher'");
            $rowTeacherEmail = $resultTeacherEmail->fetch_assoc();
            $email = $rowTeacherEmail['email'];
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-2\r\n";
            $content = "Status jednej z twoich prac uległ zmianie.";
            mail($email, "Zmiana statusu pracy.", $content, $headers);
            $_SESSION['myThesisStatus'] = 1;
        } else {
            echo "Błąd połączenia z bazą danych.";
        }
    } else {
        echo "Masz już zarezerwowaną pracę dyplomową.";
    }
    $connect->close();
}
?>

<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8"/> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title>Moja Praca Dyplomowa</title>


        <link href="../css/bootstrap.min.css" rel="stylesheet" >
        <link href="../style.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>


    </head>
    <body>

        <div id="container">

            <header>
                <div class='row'>

                    <div class='col-12 col-md-3 flex-md-last' id="logo_right">
                        <p class="head_banner">
                            <?php
                            echo " <a href='#' data-toggle='modal' data-target='#editProfileModal'>" . $_SESSION['name'] . ' ' . $_SESSION['surname'] . "</a><br/>";
                            echo $_SESSION['type'];
                            ?>
                        </p>

                        <a class="header" href="../logout.php">Wyloguj sie!</a>
                    </div>
                    <div class='col-12 col-md-9 '>
                        <h1 class="logo"> Moja praca</h1>

                    </div>


                </div>

                <nav class="navbar navbar-toggleable-sm navbar-light bg-faded" id="topnav">

                    <button class="navbar-toggler navbar-toggler-right menu_button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto menu">
                            <li class="nav-item"><a href="#">MOJA PRACA</a></li>
                            <li class="nav-item"><a href="thesis.php">DOSTĘPNE PRACE</a></li>
                        </ul>
                    </div>
                </nav>
            </header>
            <div id="teacher_page">
                <?php
                require_once '../connect.php';
                $connect = @new mysqli($host, $db_user, $db_password, $db_name);

                if ($connect->connect_errno != 0) {
                    echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
                } else {

                    $id = $_SESSION['id'];
                    if ($result = $connect->query("SELECT * FROM thesis WHERE id_student = '$id'")) {
                        $thesis_count = $result->num_rows;
                        if ($thesis_count > 0) {
                            $row = $result->fetch_assoc();

                            $id_teacher = $row['id_teacher'];
                            if ($result = $connect->query("SELECT * FROM user WHERE id = '$id_teacher'")) {
                                $row2 = $result->fetch_assoc();
                                echo "<h5>PROMOTOR:     </h5><h3>" . "<a href = '../user/userPage.php?id=" . $row['id_teacher'] . "'>" . $row2['name'] . " " . $row2['surname'] . "</a></h3><br/>";
                            } else {
                                echo "BLAD BAZY DANYCH.";
                            }
                            echo "<h5>TYTUŁ:        </h5><h3>" . $row['title'] . "</h3><br/>";
                            echo "<h5>OPIS:     </h5><p>" . $row['description'] . "</p>";
                        } else
                            echo "NIE WYBRANO JESZCZE ŻADNEJ PRACY DYPLOMOWEJ.";
                    }
                }
                ?>
            </div>
            <div class="modal fade " id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="Login" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content profileModal">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title">Edytuj profil</h5>
                        </div>

                        <div class="modal-body ">
                            <!-- The form is placed inside the body of modal -->
                            <form id="modalForm" method="post" class="form-horizontal">
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    <div>
                                        <input type="text" class="form-control" name="emailM" placeholder="Wprowadź email" require value="<?php echo $_SESSION['email'] ?>">

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Nowe hasło</label>
                                    <div >
                                        <input type="password" class="form-control" id="passwordM" name="passwordM" placeholder="Podaj nowe hasło (opcjonalnie)">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Potwierdź nowe hasło</label>
                                    <div >
                                        <input type="password" class="form-control" name="password_confirmM" placeholder="Potwierdź nowe hasło">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Imie</label>
                                    <div >
                                        <input type="text" class="form-control" name="nameM" placeholder="Wprowadz swoje imie" value="<?php echo $_SESSION['name'] ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Nazwisko</label>
                                    <div >
                                        <input type="text" class="form-control" name="surnameM" placeholder="Wprowadz swoje nazwisko" value="<?php echo $_SESSION['surname'] ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Numer indeksu</label>
                                    <div >
                                        <input type="text" class="form-control" name="index_NM" placeholder="Wprowadz swój numer indeksu" value="<?php echo $_SESSION['indexNM'] ?>">
                                    </div>
                                </div>
                                <?php if ($_SESSION['myThesisStatus'] === 0) { ?>
                                    <div class="form-group">
                                        <label class="control-label">Kierunek studiów</label>
                                        <div>
                                            <?php
                                            require_once '../connect.php';
                                            $connect = @new mysqli($host, $db_user, $db_password, $db_name);
                                            if ($connect->connect_errno != 0) {
                                                echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
                                            } else {
                                                $result = $connect->query("SELECT * FROM study");
                                                ?> <select class="form-control" name="studyM">
                                                <?php
                                                while ($row = $result->fetch_assoc()) {
                                                    if ($row['id_study'] === $_SESSION['study_id']) {
                                                        echo '<option selected value="' . $row['id_study'] . '">' . $row['name'] . '</option>';
                                                    } else {
                                                        echo '<option value="' . $row['id_study'] . '">' . $row['name'] . '</option>';
                                                    }
                                                }
                                                echo '</select>';
                                                $result->free_result();
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="modal-footer">
                                    <div class="form-group">
                                        <div>
                                            <button type="submit"  class="btn btn-primary">Zapisz zmiany</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Anuluj</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="push"></div>
        </div>

        <footer class="footer">

            &copy; 2018 Aplikacje Internetowe 

        </footer>

    </body>


</html>