<?php
session_start();

if (!isset($_SESSION['online'])) {
    header('Location: index.php');
    exit();
}


if (filter_input(INPUT_GET, 'id')) {
    require_once '../connect.php';
    $connect = @new mysqli($host, $db_user, $db_password, $db_name);

    if ($connect->connect_errno != 0) {
        echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
    } else {
        if ($result = $connect->query("SELECT * FROM department WHERE id_department = '$_GET[id]'")) {
            $dep_count = $result->num_rows;
            if ($dep_count > 0) {
                $row = $result->fetch_assoc();
                $id = $_GET['id'];
            } else {
                echo "Nie ma takiego wydzialu.";
            }
        }
    }
}
?>

<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8"/> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title>Strona wydziału</title>

        <link href="../css/bootstrap.min.css" rel="stylesheet" >
        <link href="../style.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>

    </head>
    <body>

        <div id="container">

            <header>
                <div class='row' >

                    <div class='col-12 col-md-3 flex-md-last' id="logo_right">
                        <p class="head_banner">
                            <?php
                            echo $_SESSION['name'] . ' ' . $_SESSION['surname'] . "<br/>";
                            echo $_SESSION['type'];
                            ?>
                            <br/>
                        </p>

                        <a class="header" href="../logout.php">Wyloguj sie!</a>
                    </div>
                    <div class='col-12 col-md-9 '>
                        <h1 class="logo"><?php
                            echo $row['name'];
                            ?></h1>
                    </div>

                </div>

                <nav class="navbar navbar-toggleable-sm navbar-light bg-faded" id="topnav">

                    <button class="navbar-toggler navbar-toggler-right menu_button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto menu">
                            <?php if ($_SESSION['type'] === "Nauczyciel") { ?>
                                <li class="nav-item"><a href="../teacher/myThesis.php">STRONA GŁÓWNA</a></li>
                                <li class="nav-item"><a href="../teacher/addThesis.php">DODAJ PRACE</a></li>
                                <li class="nav-item"><a href="../department/departmentPage.php?id=<?php echo $_SESSION['department_id'] ?>">WYDZIAŁ</a></li>
                                <?php
                            } elseif ($_SESSION['type'] === "Student") {
                                ?>
                                <li class="nav-item"><a href="../student/myThesis.php">MOJA PRACA</a></li>
                                <li class="nav-item"><a href="../student/thesis.php">DOSTĘPNE PRACE</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </nav>
            </header>
            <div id="teacher_page">
                <?php
                if (isset($id)) {
                    echo "<strong>Adres:</strong> " . $row['address'] . '<br/>';
                    echo "<strong>Kierunki realizowane na wydziale: </strong> <br/>";
                    if ($result = $connect->query("SELECT * FROM department_study WHERE id_department = '$id'")) {
                        $dep_count = $result->num_rows;
                        if ($dep_count > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $id_study = $row['id_study'];
                                $result_study = $connect->query("SELECT * FROM study WHERE id_study = '$id_study'");
                                $row_study = $result_study->fetch_assoc();
                                echo " - " . $row_study['name'] . " <br/>";
                            }
                        } else {
                            echo "Do wydziału nie maprzyporządkowanych żadnych kierunków nauczania.";
                        }
                    }
                    echo "<strong>Skład wydziału: </strong><br/>";
                    if ($result = $connect->query("SELECT * FROM user WHERE id_department = '$id'")) {
                        $user_count = $result->num_rows;
                        if ($user_count > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo " - " . "<a href = '../user/userPage.php?id=" . $row['id'] . "'>" . $row['name'] . " " . $row['surname'] . "</a> <br/>";
                            }
                        } else {
                            echo "W skład wydziału nie wchodzi żaden nauczyciel.";
                        }
                    }
                    echo "<br/>Prace dyplmowe realizowane na wydziale (niezarezerwowane): </br>";
                    if ($result = $connect->query("SELECT * FROM user WHERE id_department = '$id'")) {
                        if ($user_count > 0) {
                            while ($row = $result->fetch_assoc()) {

                                $id_teacher = $row['id'];
                                if ($result_thesis = $connect->query("SELECT * FROM thesis WHERE id_teacher = '$id_teacher' AND status = 0 ")) {
                                    $thesis_count = $result->num_rows;
                                    if ($thesis_count > 0) {

                                        while ($row_thesis = $result_thesis->fetch_assoc()) {
                                            echo "<div class='table_padding'>";
                                            echo "<div class='row'>";
                                            echo "<div class='col-12 col-md-2 cell_first_element'>" . $row_thesis['title'] . "</div>";
                                            echo "<div class='col-12 col-md-4 cell' style='max-height: 250px; overflow: auto'>" . $row_thesis['description'] . "</div>";

                                            $id_study = $row_thesis['id_study'];
                                            if ($result_study = $connect->query("SELECT * FROM study WHERE id_study = '$id_study'")) {
                                                $row_study = $result_study->fetch_assoc();
                                                echo "<div class='col-12 col-md-2 cell'>" . $row_study['name'] . "</div>";
                                            } else {
                                                echo "BLAD BAZY DANYCH.";
                                            }
                                            $id_teacher = $row_thesis['id_teacher'];
                                            if ($result_teacher = $connect->query("SELECT * FROM user WHERE id = '$id_teacher'")) {
                                                $row_teacher = $result_teacher->fetch_assoc();
                                                echo "<div class='col-12 col-md-2 cell'>" . "<a href = '../user/userPage.php?id=" . $row_teacher['id'] . "'>" . $row_teacher['name'] . " " . $row_teacher['surname'] . "</a></div>";
                                            } else {
                                                echo "BLAD BAZY DANYCH.";
                                            }
                                            if ($_SESSION['type'] == "Student" && $row_study['id_study'] == $_SESSION['study_id'] && $_SESSION['myThesisStatus'] == 0) {
                                                echo "<div class='col-12 col-md-2 cell_last_element'>" . "<a href='../student/myThesis.php?reserve=" . $row_thesis['id_thesis'] . "'>REZERWUJ</a>  </div>";
                                            } else {
                                                echo "<div class='col-12 col-md-2 cell_last_element'>Praca nie została jeszcze zarezerwowana.</div>";
                                            }

                                            echo "</div></div>";
                                        }
                                    } else {
                                        echo "Brak prac dyplomowych";
                                    }
                                }
                            }
                        } else {
                            echo "Brak prac dyplmowych.";
                        }
                    }

                    echo "<br/>Prace dyplmowe realizowane na wydziale (zarezerwowane): </br>";
                    if ($result = $connect->query("SELECT * FROM user WHERE id_department = '$id'")) {
                        if ($user_count > 0) {
                            while ($row = $result->fetch_assoc()) {

                                $id_teacher = $row['id'];
                                if ($result_thesis = $connect->query("SELECT * FROM thesis WHERE id_teacher = '$id_teacher' AND status = 1 ")) {
                                    $thesis_count = $result->num_rows;
                                    if ($thesis_count > 0) {
                                        while ($row_thesis = $result_thesis->fetch_assoc()) {
                                            echo "<div class='row'>";
                                            echo "<div class='col-12 col-md-2 cell_first_element'>" . $row_thesis['title'] . "</div>";
                                            echo "<div class='col-12 col-md-4 cell' style='max-height: 250px; overflow: auto;'> " . $row_thesis['description'] . "</div>";
                                            $id_study = $row_thesis['id_study'];
                                            if ($result_study = $connect->query("SELECT * FROM study WHERE id_study = '$id_study'")) {
                                                $row_study = $result_study->fetch_assoc();
                                                echo "<div class='col-12 col-md-2 cell'>" . $row_study['name'] . "</div>";
                                            } else {
                                                echo "BLAD BAZY DANYCH.";
                                            }
                                            $id_teacher = $row_thesis['id_teacher'];
                                            if ($result_teacher = $connect->query("SELECT * FROM user WHERE id = '$id_teacher'")) {
                                                $row_teacher = $result_teacher->fetch_assoc();
                                                echo "<div class='col-12 col-md-2 cell'>" . "<a href = '../user/userPage.php?id=" . $row_teacher['id'] . "'>" . $row_teacher['name'] . " " . $row_teacher['surname'] . "</a></div>";
                                            } else {
                                                echo "BLAD BAZY DANYCH.";
                                            }
                                            $id_student = $row_thesis['id_student'];
                                            if ($result_student = $connect->query("SELECT * FROM user WHERE id = '$id_student'")) {
                                                $student_count = $result_student->num_rows;
                                                if ($student_count > 0) {
                                                    $row = $result_student->fetch_assoc();
                                                    echo "<div class='col-12 col-md-2 cell_last_element'>" . "<a href = '../user/userPage.php?id=" . $row['id'] . "'>" . $row['name'] . " " . $row['surname'] . "</a></div>";
                                                } else {
                                                    echo "<td>Praca nie zostaøa jeszcze zarezerwowana.</td>";
                                                }
                                            } else {
                                                echo "BLAD BAZY DANYCH.";
                                            }
                                            echo "</div></div>";
                                        }
                                    } else {
                                        echo "Brak prac dyplomowych";
                                    }
                                }
                            }
                        } else {
                            echo "Brak prac dyplmowych.";
                        }
                    }
                }
                ?>

                <div class="push"></div>
            </div>
        </div>
        <footer class="footer">

            &copy; 2018 Aplikacje Internetowe 

        </footer>
    </body>

</html>