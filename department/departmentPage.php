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
        <title>Logowanie</title>

    </head>
    <body>

        <?php
        if (isset($id)) {
            echo $row['name'] . '<br/>';
            echo "Adres: " . $row['address'] . '<br/>';
            echo "Kierunki realizowane na wydziale: <br/>";
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
            echo "Skład wydziału: <br/>";
            if ($result = $connect->query("SELECT * FROM user WHERE id_department = '$id'")) {
                $user_count = $result->num_rows;
                if ($user_count > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo " - " . $row['name'] . " " . $row['surname'] . " <br/>";
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
                                echo "<table cellpadding=\"2\" border=1>";
                                while ($row_thesis = $result_thesis->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row_thesis['title'] . "</td>";
                                    echo "<td>" . $row_thesis['description'] . "</td>";
                                    $id_study = $row_thesis['id_study'];
                                    if ($result_study = $connect->query("SELECT * FROM study WHERE id_study = '$id_study'")) {
                                        $row_study = $result_study->fetch_assoc();
                                        echo "<td>" . $row_study['name'] . "</td>";
                                    } else {
                                        echo "BLAD BAZY DANYCH.";
                                    }
                                    echo "</tr>";
                                }
                                echo "</table>";
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
                                echo "<table cellpadding=\"2\" border=1>";
                                while ($row_thesis = $result_thesis->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row_thesis['title'] . "</td>";
                                    echo "<td>" . $row_thesis['description'] . "</td>";
                                    $id_study = $row_thesis['id_study'];
                                    if ($result_study = $connect->query("SELECT * FROM study WHERE id_study = '$id_study'")) {
                                        $row_study = $result_study->fetch_assoc();
                                        echo "<td>" . $row_study['name'] . "</td>";
                                    } else {
                                        echo "BLAD BAZY DANYCH.";
                                    }
                                    $id_student = $row_thesis['id_student'];
                                    if ($result_student = $connect->query("SELECT * FROM user WHERE id = '$id_student'")) {
                                        $student_count = $result_student->num_rows;
                                        if ($student_count > 0) {
                                            $row = $result_student->fetch_assoc();
                                            echo "<td>" . $row['name'] . " " . $row['surname'] . "</td>";
                                        } else {
                                            echo "<td>Praca nie zostaøa jeszcze zarezerwowana.</td>";
                                        }
                                    } else {
                                        echo "BLAD BAZY DANYCH.";
                                    }
                                    echo "</tr>";
                                }
                                echo "</table>";
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

        echo"<p>Witaj " . $_SESSION['email'] . '[<a href="logout.php">Wyloguj sie!</a>]</p>';
        ?>
    </body>
</html>