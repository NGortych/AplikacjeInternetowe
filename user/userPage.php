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
        if ($result = $connect->query("SELECT * FROM user WHERE id = '$_GET[id]'")) {
            $user_count = $result->num_rows;
            if ($user_count > 0) {
                $row = $result->fetch_assoc();
                $id = $_GET['id'];
            } else {
                echo "Nie ma użytkownika o podanym identyfikatorze.";
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
        <title>
            <?php
            echo $row['name'] . " " . $row['surname'];
            ?></title>

    </head>
    <body>

        <?php
        if (isset($id)) {
            echo "Imie: " . $row['name'] . '<br/>';
            echo "Nazwisko: " . $row['surname'] . '<br/>';
            echo "Email: " . $row['email'] . '<br/>';
            echo $row['type'] . '<br/>';

            if ($row['type'] == "Student") {

                if ($result = $connect->query("SELECT * FROM thesis WHERE id_student = '$id'")) {
                    $thesis_count = $result->num_rows;
                    if ($thesis_count > 0) {
                        echo "<table cellpadding=\"2\" border=1>";
                        while ($row_thesis = $result->fetch_assoc()) {
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
                            $id_teacher = $row_thesis['id_teacher'];
                            if ($result = $connect->query("SELECT * FROM user WHERE id = '$id_teacher'")) {
                                $row = $result->fetch_assoc();
                                echo "<td>" . $row['name'] . " " . $row['surname'] . "</td>";
                            } else {
                                echo "BLAD BAZY DANYCH.";
                            }

                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "Uzytkownik nie zarezerwowal jeszcze zadenj pracy dyplomowej";
                    }
                    $connect->close();
                } else {
                    echo "Błąd bazy danych.";
                }
            } elseif ($row['type'] == "Nauczyciel") {

                if ($result = $connect->query("SELECT * FROM thesis WHERE id_teacher = '$id'")) {
                    $thesis_count = $result->num_rows;
                    if ($thesis_count > 0) {
                        echo "<table cellpadding=\"2\" border=1>";
                        while ($row_thesis = $result->fetch_assoc()) {
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
                        echo "Uzytkownik nie zarezerwowal jeszcze zadenj pracy dyplomowej";
                    }
                    $connect->close();
                } else {
                    echo "Błąd bazy danych.";
                }
            }
        }

        echo"<p>Witaj " . $_SESSION['email'] . '[<a href="logout.php">Wyloguj sie!</a>]</p>';
        ?>
    </body>
</html>
