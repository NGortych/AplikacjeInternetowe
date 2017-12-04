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

    </head>
    <body>

        <?php
        require_once '../connect.php';
        $connect = @new mysqli($host, $db_user, $db_password, $db_name);

        if ($connect->connect_errno != 0) {
            echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
        } else {
            echo "Dostępne prace dyplomowe: </br>";
            $id_study = $_SESSION['study_id'];
            if ($result = $connect->query("SELECT * FROM thesis WHERE id_study = '$id_study' AND status = 0")) {
                $thesis_count = $result->num_rows;
                if ($thesis_count > 0) {
                    echo "<table cellpadding=\"2\" border=1>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['title'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        $id_teacher = $row['id_teacher'];
                        if ($result_teacher = $connect->query("SELECT * FROM user WHERE id = '$id_teacher'")) {
                            $row_teacher = $result_teacher->fetch_assoc();
                            echo "<td>" . $row_teacher['name'] . " " . $row_teacher['surname'] . "</td>";
                        } else {
                            echo "BLAD BAZY DANYCH.";
                        }
                        echo "<td>       <a href=\"http://localhost/test/student/myThesis.php?reserve=" . $row['id_thesis'] . "\">REZERWUJ</a>  </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "Nie dodano jeszcze żadnej pracy dyplomowej.";
                }
            } else {
                echo "Błąd połączenie z bazą danych.";
            }
            echo " Prace typlomowe które zostały już zarezerwowane: </br>";
            if ($result = $connect->query("SELECT * FROM thesis WHERE id_study = '$id_study' AND status = 1")) {
                $thesis_count = $result->num_rows;
                if ($thesis_count > 0) {
                    echo "<table cellpadding=\"2\" border=1>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['title'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        $id_teacher = $row['id_teacher'];
                        if ($result_teacher = $connect->query("SELECT * FROM user WHERE id = '$id_teacher'")) {
                            $row_teacher = $result_teacher->fetch_assoc();
                            echo "<td>" . $row_teacher['name'] . " " . $row_teacher['surname'] . "</td>";
                        } else {
                            echo "BLAD BAZY DANYCH.";
                        }

                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "Nie dodano jeszcze żadnej pracy dyplomowej.";
                }
            } else {
                echo "Błąd połączenie z bazą danych.";
            }
        }
        ?>


    </body>


</html>