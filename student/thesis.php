<?php
session_start();
if (!isset($_SESSION['online'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8"/> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title>Dostępne prace</title>

        <link href="../css/bootstrap.min.css" rel="stylesheet" >
        <link href="../style.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>


    </head>
    <body>

        <div id="container">

            <header>
                <div class='row'>

                    <div class='col-12 col-md-3 flex-md-last' id="logo_right">
                        <p class="head_banner">
                            <?php
                            echo $_SESSION['name'] . ' ' . $_SESSION['surname'] . "<br/>";
                            echo $_SESSION['type'];
                            ?>
                        </p>

                        <a class="header" href="../logout.php">Wyloguj sie!</a>
                    </div>
                    <div class='col-12 col-md-9 '>
                        <h1 class="logo"> Dostępne prace</h1>
                    </div>

                </div>

                <nav class="navbar navbar-toggleable-sm navbar-light bg-faded" id="topnav">

                    <button class="navbar-toggler navbar-toggler-right menu_button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto menu">
                            <li class="nav-item"><a href="myThesis.php">MOJA PRACA</a></li>
                            <li class="nav-item"><a href="#">DOSTĘPNE PRACE</a></li>
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

                    if ($result = $connect->query("SELECT * FROM study WHERE id_study = '$_SESSION[study_id]'")) {
                        $row = $result->fetch_assoc();
                        echo "<p>Prace dyplomowe realizowane na kierunku <h3>" . $row['name'] . "</h3></p><br/>";
                    } else {
                        echo "BLAD BAZY DANYCH.";
                    }


                    echo "Dostępne prace dyplomowe: </br>";
                    $id_study = $_SESSION['study_id'];
                    if ($result = $connect->query("SELECT * FROM thesis WHERE id_study = '$id_study' AND status = 0")) {
                        $thesis_count = $result->num_rows;
                        if ($thesis_count > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<div class='table_padding'>";
                                if ($_SESSION['myThesisStatus'] == 0) {
                                    echo "<div class='row'>";
                                    echo "<div class='col-12 col-md-2 cell_first_element'>" . $row['title'] . "</div>";
                                    echo "<div class='col-12 col-md-6 cell'>" . $row['description'] . "</div>";
                                    $id_teacher = $row['id_teacher'];
                                    if ($result_teacher = $connect->query("SELECT * FROM user WHERE id = '$id_teacher'")) {
                                        $row_teacher = $result_teacher->fetch_assoc();
                                        echo "<div class='col-12 col-md-2 cell'>" . "<a href = '../user/userPage.php?id=" . $row['id_teacher'] . "'>" . $row_teacher['name'] . " " . $row_teacher['surname'] . "</a></div>";
                                    } else {
                                        echo "BLAD BAZY DANYCH.";
                                    }
                                    echo "<div class='col-12 col-md-2 cell_last_element'>" . "<a href='../student/myThesis.php?reserve=" . $row['id_thesis'] . "'>REZERWUJ</a>  </div>";
                                    echo "</div></div>";
                                } elseif ($_SESSION['myThesisStatus'] == 1) {
                                    echo "<div class='row'>";
                                    echo "<div class='col-12 col-md-3 cell_first_element'>" . $row['title'] . "</div>";
                                    echo "<div class='col-12 col-md-6 cell'>" . $row['description'] . "</div>";
                                    $id_teacher = $row['id_teacher'];
                                    if ($result_teacher = $connect->query("SELECT * FROM user WHERE id = '$id_teacher'")) {
                                        $row_teacher = $result_teacher->fetch_assoc();
                                        echo "<div class='col-12 col-md-3 cell_last_element'>" . "<a href = '../user/userPage.php?id=" . $row['id_teacher'] . "'>" . $row_teacher['name'] . " " . $row_teacher['surname'] . "</a></div>";
                                    } else {
                                        echo "BLAD BAZY DANYCH.";
                                    }
                                    echo "</div></div>";
                                }
                            }
                        } else {
                            echo "Nie dodano jeszcze żadnej pracy dyplomowej.";
                        }
                    } else {
                        echo "Błąd połączenie z bazą danych.";
                    }
                    echo " <br/>Prace typlomowe które zostały już zarezerwowane: </br>";
                    if ($result = $connect->query("SELECT * FROM thesis WHERE id_study = '$id_study' AND status = 1")) {
                        $thesis_count = $result->num_rows;
                        if ($thesis_count > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<div class='table_padding'>";
                                echo "<div class='row'>";
                                echo "<div class='col-12 col-md-2 cell_first_element'>" . $row['title'] . "</div>";
                                echo "<div class='col-12 col-md-6 cell'>" . $row['description'] . "</div>";
                                $id_teacher = $row['id_teacher'];
                                if ($result_teacher = $connect->query("SELECT * FROM user WHERE id = '$id_teacher'")) {
                                    $row_teacher = $result_teacher->fetch_assoc();
                                    echo "<div class='col-12 col-md-2 cell'>" . "<a href = '../user/userPage.php?id=" . $row['id_teacher'] . "'>" . $row_teacher['name'] . " " . $row_teacher['surname'] . "</a></div>";
                                } else {
                                    echo "BLAD BAZY DANYCH.";
                                }
                                $id_student = $row['id_student'];
                                if ($result_student = $connect->query("SELECT * FROM user WHERE id = '$id_student'")) {
                                    $row_student = $result_student->fetch_assoc();
                                    echo "<div class='col-12 col-md-2 cell_last_element'>" . "<a href = '../user/userPage.php?id=" . $row['id_student'] . "'>" . $row_student['name'] . " " . $row_student['surname'] . "</a></div>";
                                } else {
                                    echo "BLAD BAZY DANYCH.";
                                }
                                echo "</div></div>";
                            }
                        } else {
                            echo "Nie dodano jeszcze żadnej pracy dyplomowej.";
                        }
                    } else {
                        echo "Błąd połączenie z bazą danych.";
                    }
                }
                ?>
                <br/>
            </div>
            <div class="push"> </div>
        </div>

        <footer class="footer">

            &copy; 2018 Aplikacje Internetowe 

        </footer>

    </body>


</html>