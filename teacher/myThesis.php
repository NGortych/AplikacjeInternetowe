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
        <title>Moje Prace Dyplomowe</title>

    </head>
    <body>
        <?php
        require_once '../connect.php';
        $connect = @new mysqli($host, $db_user, $db_password, $db_name);

        if ($connect->connect_errno != 0) {
            echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
        } else {
            echo " Zarezerwowane prace dyplomowe: </br>";
            $id = $_SESSION['id'];
            if ($result = $connect->query("SELECT * FROM thesis WHERE id_teacher = '$id' AND status = 1")) {
                $thesis_count = $result->num_rows;
                if ($thesis_count > 0) {

                    echo "<table cellpadding=\"2\" border=1>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['title'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        $id_study = $row['id_study'];
                        if ($result_study = $connect->query("SELECT * FROM study WHERE id_study = '$id_study'")) {
                            $row_study = $result_study->fetch_assoc();
                            echo "<td>" . $row_study['name'] . "</td>";
                        } else {
                            echo "BLAD BAZY DANYCH.";
                        }
                        $id_student = $row['id_student'];
                        if ($result = $connect->query("SELECT * FROM user WHERE id = '$id_student'")) {
                            $row = $result->fetch_assoc();
                            echo "<td>" . $row['name'] . " " . $row['surname'] . "</td>";
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
            echo " Niezarezerwowane prace dyplomowe: </br>";
            if ($result = $connect->query("SELECT * FROM thesis WHERE id_teacher = '$id' AND status = 0")) {
                $thesis_count = $result->num_rows;
                if ($thesis_count > 0) {

                    echo "<table cellpadding=\"2\" border=1>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['title'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        $id_study = $row['id_study'];
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
                    echo "Nie dodano jeszcze żadnej pracy dyplomowej.";
                }
            } else {
                echo "Błąd połączenie z bazą danych.";
            }
        }
        ?>
        <a href="addThesis.php"> Dodaj nową pracę dyplomową. </a><br/>
        <a href="../logout.php">Wyloguj sie!</a>


    </body>
</html>