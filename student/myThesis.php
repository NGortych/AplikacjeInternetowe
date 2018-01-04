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
        <br/>
        <a href="thesis.php"> Pokaz dostepne prace </a> <br/>

        <a href="../logout.php">Wyloguj sie!</a>
        <footer class="footer">

            &copy; 2018 Aplikacje Internetowe 

        </footer>
    </body>


</html>