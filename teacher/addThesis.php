<?php
session_start();

if (!isset($_SESSION['online'])) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['title'])) {
    $successful_validation = true;

    //tytuł
    $title = $_POST['title'];
    if (strlen($title) < 10 || strlen($title > 100)) {
        $successful_validation = false;
        $_SESSION['error_title'] = "Tytuł musi zawierać od 10 do 100 znaków!!!";
    }

    //opis
    $description = $_POST['description'];
    if (strlen($description > 500)) {
        $successful_validation = false;
        $_SESSION['error_description'] = "Opis zbyt długi. Maksymalnie 500 znaków!!!";
    }

    //kierunek
    $study = $_POST['study'];
    if ($study == "") {
        $successful_validation = false;
        $_SESSION['error_study'] = "Musisz wybrać kierunek dla którego realizowana ma być dana praca.";
    }

    if ($successful_validation == true) {
        require_once '../connect.php';
        $connect = @new mysqli($host, $db_user, $db_password, $db_name);

        if ($connect->connect_errno != 0) {
            echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
        } else {
            $id = $_SESSION['id'];
            if ($connect->query("INSERT INTO `thesis`(`id_thesis`, `title`, `description`, `status`, `id_student`, `id_teacher`, `id_study`) VALUES (NULL,'$title','$description',0,NULL,'$id',$study)")) {
                echo "Praca dyplomowa została dodana.";
                header('Location: myThesis.php');
                exit();
            } else
                echo "BLĄD PODCZAS DODAWANIA PRACY DO BAZY DANYCH !!!" . $connect->error;
        }
    }
}
?>
<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8"/> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title>Dodaj prace dyplomową.</title>
        <style>
            .error
            {
                color:red;
                margin-top: 10px;           
                margin-bottom: 10px;                    
            }
        </style>
    </head>
    <body>
        <form method="post">


            <br/>

            Tytuł pracy: <br/> <input type="text" name="title" size ="75" maxlength="100"/> <br/>
<?php
if (isset($_SESSION['error_title'])) {
    echo '<div class="error">' . $_SESSION['error_title'] . '</div>';
    unset($_SESSION['error_title']);
}
?>
            <br/>

            Opis: <br/> <input type="text" name="description"size="150" maxlength="500" /> <br/>
<?php
if (isset($_SESSION['error_description'])) {
    echo '<div class="error">' . $_SESSION['error_description'] . '</div>';
    unset($_SESSION['error_description']);
}
?>
            <?php
            require_once '../connect.php';
            $connect = @new mysqli($host, $db_user, $db_password, $db_name);

            if ($connect->connect_errno != 0) {
                echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
            } else {
                $id_department = $_SESSION['department_id'];
                $result = $connect->query("SELECT * FROM deparment_study WHERE id_department = '$id_department'");
                echo "<br/><br/>Kierunek: <br/>";
                echo '<select name="study">';
                echo '<option value=""> Kierunek </option>';
                while ($row = $result->fetch_assoc()) {
                    $id_study = $row['id_study'];
                    $result2 = $connect->query("SELECT * FROM study WHERE id_study = '$id_study'");
                    $row2 = $result2->fetch_assoc();
                    echo '<option value="' . $row2['id_study'] . '">' . $row2['name'] . '</option>';
                }
                echo '</select><br/>';
                $result->free_result();
                $result2->free_result();
            }
            ?>
            <?php
            if (isset($_SESSION['error_study'])) {
                echo '<div class="error">' . $_SESSION['error_study'] . '</div>';
                unset($_SESSION['error_study']);
            }
            ?>
            <input type="submit" value="Dodaj pracę."/>  
        </form>
        <br/>

        <br/>
        <a href="../logout.php">Wyloguj sie!</a>

    </body>
</html>

