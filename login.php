<?php

session_start();

if ((!isset($_POST['login'])) || (!isset($_POST['password']))) {
    header('Location: index.php');
    exit();
}

require_once "connect.php";

$connect = @new mysqli($host, $db_user, $db_password, $db_name);

if ($connect->connect_errno != 0) {
    echo "Błąd połączenia z bazą. Spróbuj posownie później.";
} else {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $login = htmlentities($login, ENT_QUOTES, "UTF-8");

    if ($result = $connect->query(
            sprintf("SELECT * FROM user WHERE email='%s'", mysqli_real_escape_string($connect, $login)))) {
        $users_count = $result->num_rows;
        if ($users_count > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                if ($row['active'] == 0) {
                    $_SESSION['error'] = '<span style="color:red">Nie potwierdzono aktywacji konta!</span>';
                    $result->free_result();
                    header('Location: index.php');
                } else {

                    $_SESSION['online'] = true;
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['password'] = $row['password'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['surname'] = $row['surname'];
                    $_SESSION['type'] = $row['type'];
                    $_SESSION['indexNM'] = $row['indexNM'];
                    $_SESSION['study_id'] = $row['id_study'];
                    $_SESSION['department_id'] = $row['id_department'];

                    unset($_SESSION['error']);
                    $result->free_result();
                    if ($row['type'] == "Nauczyciel")
                        header('Location: teacher/myThesis.php');
                    elseif($row['type'] == "Student")
                        header('Location: student/myThesis.php');
                }
            } else {
                $_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
                header('Location: index.php');
            }
        } else {

            $_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło.</span>';
            header('Location: index.php');
        }
    }

    $connect->close();
}
?>