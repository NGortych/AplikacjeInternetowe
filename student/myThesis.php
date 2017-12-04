<?php
session_start();
if (!isset($_SESSION['online'])) {
    header('Location: index.php');
    exit();
}

if (filter_input(INPUT_GET, 'reserve')) {
    require_once '../connect.php';
    $connect = @new mysqli($host, $db_user, $db_password, $db_name);

    if ($connect->connect_errno != 0) {
        echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
    } else {
        $id_student = $_SESSION['id'];
        if ($connect->query("UPDATE thesis SET status=1, id_student ='$_SESSION[id]' WHERE id_thesis='$_GET[reserve]' ")) {
            
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
        <title>Moja Praca Dyplomowa</title>

    </head>
    <body>
        <?php
        require_once '../connect.php';
        $connect = @new mysqli($host, $db_user, $db_password, $db_name);

        if ($connect->connect_errno != 0) {
            echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
        } else {
            echo "Moja praca dyplomowa: </br>";
            $id = $_SESSION['id'];
            if ($result = $connect->query("SELECT * FROM thesis WHERE id_student = '$id'")) {
                $thesis_count = $result->num_rows;
                if ($thesis_count > 0) {
                    $row = $result->fetch_assoc();
                    echo "<table cellpadding=\"2\" border=1>";
                    echo "<tr>";
                    echo "<td>" . $row['title'] . "</td>";
                    echo "<td>" . $row['description'] . "</td>";
                    $id_teacher = $row['id_teacher'];
                    if ($result = $connect->query("SELECT * FROM user WHERE id = '$id_teacher'")) {
                        $row = $result->fetch_assoc();
                        echo "<td>" . $row['name'] . " " . $row['surname'] . "</td>";
                    } else {
                        echo "BLAD BAZY DANYCH.";
                    }
                    echo "</tr>";
                    echo "</table>";
                } else
                    echo "NIE WYBRANO JESZCZE ŻADNEJ PRACY DYPLOMOWEJ.";
            }
        }
        ?>

        <a href="thesis.php"> Pokaz dostepne prace </a>;
    </body>


</html>