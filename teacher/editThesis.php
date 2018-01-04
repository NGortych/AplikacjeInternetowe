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
            if ($connect->query("UPDATE `thesis` SET `title`='$title',`description`='$description', `id_study`='$study' WHERE id_thesis='$_GET[edit]'")) {
                echo "Praca dyplomowa została edytowana.";
                header('Location: myThesis.php');
                exit();
            } else
                echo "BLĄD PODCZAS EDYTOWANIA PRACY DO BAZY DANYCH !!!" . $connect->error;
        }
    }
}

if (filter_input(INPUT_GET, 'edit')) {
    require_once '../connect.php';
    $connect = @new mysqli($host, $db_user, $db_password, $db_name);
    if ($connect->connect_errno != 0) {
        echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
    } elseif (1) {
        if ($result = $connect->query("SELECT * FROM `thesis` WHERE id_thesis='$_GET[edit]' ")) {
            $row = $result->fetch_assoc();
            $title = $row['title'];
            $description = $row['description'];
            $id_study_edit = $row['id_study'];
        } else {
            echo "Błąd połączenia z bazą danych.";
        }
    } else {
        echo "Nie można wykonać tej czynności.";
    }
    $connect->close();
}
?>
<!DOCTYPE HTML>
<html lang="pl">
    <head>
        <meta charset="utf-8"/> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title>Dodaj prace dyplomową.</title>

        <link href="../css/bootstrap.min.css" rel="stylesheet" >
        <link href="../style.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
    </head>
    <body>
        <div id="container">
            <header>
                <div id="logo_left">
                    <h1 class="logo">Nowa praca</h1>
                </div>
                <div id="logo_right">
                    <?php
                    echo $_SESSION['name'] . ' ' . $_SESSION['surname'] . "<br/>";
                    echo $_SESSION['type'];
                    ?>
                    <br/>
                    <a class="header" href="../logout.php">Wyloguj sie!</a>

                </div>
                <div style="clear:both;"></div>
                <nav class="navbar navbar-toggleable-sm navbar-light bg-faded" id="topnav">

                    <button class="navbar-toggler navbar-toggler-right menu_button" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto menu">
                            <li class="nav-item"><a href="myThesis.php">STRONA GŁÓWNA</a></li>
                            <li class="nav-item"><a href="#">DODAJ PRACE</a></li>
                            <li class="nav-item"><a href="../department/departmentPage.php?id=<?php echo $_SESSION['department_id'] ?>">WYDZIAŁ</a></li>
                        </ul>
                    </div>
                </nav>
            </header>
            <div>
                <form method="post" > 
                    <div id="teacher_page">
                        <div class=" reg_fields">Tytuł pracy: <br/> <textarea name="title" class="form-control half-screen" cols="10" rows="2" ><?php echo $title; ?></textarea> </div>
                        <?php
                        if (isset($_SESSION['error_title'])) {
                            echo '<div class="error">' . $_SESSION['error_title'] . '</div>';
                            unset($_SESSION['error_title']);
                        }
                        ?>
                        <div class=" reg_fields">Opis: <br/> <textarea name="description" class="form-control" rows="5" maxlength="500"><?php echo $description; ?></textarea>
                        </div>
                        <?php
                        if (isset($_SESSION['error_description'])) {
                            echo '<div class="error">' . $_SESSION['error_description'] . '</div>';
                            unset($_SESSION['error_description']);
                        }
                        ?>
                        <div id="study" class="reg_fields">Kierunek :
                            <?php
                            require_once '../connect.php';
                            $connect = @new mysqli($host, $db_user, $db_password, $db_name);

                            if ($connect->connect_errno != 0) {
                                echo "Błąd połączeniea z bazą. Spróbuj ponownie później";
                            } else {
                                $id_department = $_SESSION['department_id'];
                                //   $result = $connect->query("SELECT * FROM study WHERE id_study='$id_study'");
                                //  $row = $result->fetch_assoc();
                                $result = $connect->query("SELECT * FROM department_study WHERE id_department = '$id_department'");
                                echo '<select class="form-control" name="study">';
                                while ($row = $result->fetch_assoc()) {
                                    $id_study = $row['id_study'];
                                    $result2 = $connect->query("SELECT * FROM study WHERE id_study = '$id_study'");
                                    $row2 = $result2->fetch_assoc();
                                    if ($row2['id_study'] === $id_study_edit) {
                                        echo '<option selected value="' . $row2['id_study'] . '">' . $row2['name'] . '</option>';
                                    } else {
                                        echo '<option value="' . $row2['id_study'] . '">' . $row2['name'] . '</option>';
                                    }
                                }
                                echo '</select>';
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
                        </div>
                        <br/>
                        <input type="submit" value="Zapisz zmiany" class="button"/>
                    </div>
                </form>
            </div>
            <div class="push"></div>
        </div>
        <footer class="footer">

            &copy; 2018 Aplikacje Internetowe 

        </footer>
    </body>
</html>

