<?php
    session_start();
    if(!isset($_SESSION['logged'])) {
        header('Location: zaloguj');
        exit();
    }

    $carriers = array("", "Rokbus (Rokietnica)", "ZKP Suchy Las", "Transkom (Murowana Goślina, Czerwonak)", "PUK Komorniki",
                      "PUK Dopiewo", "Translub (Luboń)", "Marco Polo", "PKS Poznań");

    $hours = array("06:00 - 14:00", "14:00 - 22:00", "22:00 - 06:00");

    $_SESSION['fr_day'] = $fr_day;
    $_SESSION['fr_team'] = $fr_day;

    if(!isset($_POST['month'])) $_POST['month'] = date("m");
    if(!isset($_POST['year'])) $_POST['year'] = date("Y");
    // usuwanie tabeli
    if(isset($_POST['deleteDate'])) {
        $everything_OK=true;
        require_once "redirects/db-management.php";
        mysqli_report(MYSQLI_REPORT_STRICT);
        try {
            $connection = new mysqli($host, $db_user, $db_password, $db_name);
            if($connection->connect_errno!=0) {
                throw new Exception(mysqli_connect_errno());
            } else {
                $deleteDate = $_POST['deleteDate'];
                //sprawdzanie istnienia grafiku
                // $result = $connection->query("SELECT date from prohibition WHERE date LIKE '%$deleteDate%'");
                // if(!$result) throw new Exception($connection->error);

                // $how_many_tables = $result->num_rows;
                // if($how_many_tables==0) {
                //     $everything_OK=false;
                //     $_SESSION['e_delete']="Grafik z wybranego przedziału nie istnieje!";
                // }
                if($everything_OK==true) {
                    if(mysqli_query($connection, "DELETE FROM prohibition WHERE date LIKE '%$deleteDate%'")) {
                        $_SESSION['sent']=true;
                        header('Location: zakaz');
                    } else {
                        throw new Exception($connection->error);
                    }
                }
            }
        }
        catch(Exception $e)
        {
            // echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
            // echo '<br>Informacja developerska: '.$e;
        }
    }
    // aktualizacja tabeli
    if(isset($_POST['date'])) {
        $everything_OK=true;
        require_once "redirects/db-management.php";
        mysqli_report(MYSQLI_REPORT_STRICT);

        try {
            $connection = new mysqli($host, $db_user, $db_password, $db_name);
            if($connection->connect_errno!=0) {
                throw new Exception(mysqli_connect_errno());
            } else {
                if($everything_OK==true) {
                    $dateUpdate = $_POST['date'];
                    $shift = $_POST['shift'];
                    $carrier = $_POST['carrier'];

                    $updateQuery = "UPDATE prohibition SET $shift = '$carrier' WHERE date = '$dateUpdate'";
                    if(mysqli_query($connection, $updateQuery)) {
                        $_SESSION['sent']=true;
                        $query = "SELECT * FROM prohibition WHERE date LIKE '%$date%'";
                        $result = mysqli_query($connection, $query);    
                    }
                    else throw new Exception($connection->error);
                }
            }
        }
        catch(Exception $e) {
            echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
            echo '<br>Informacja developerska: '.$e;
        }
    }
    // wyświetlanie tabeli
    if(isset($_POST['month']) && isset($_POST['year'])) {
        $everything_OK=true;
        require_once "redirects/db-management.php";
        mysqli_report(MYSQLI_REPORT_STRICT);

        try {
            $connection = new mysqli($host, $db_user, $db_password, $db_name);

            if($connection->connect_errno!=0) {
                throw new Exception(mysqli_connect_errno());
            } else {
                $month = $_POST['month'];
                $year = $_POST['year'];
                if(isset($dateUpdate)) {
                    $date = substr_replace($dateUpdate,"",7);
                } else {
                    $date = $year."-".$month;
                }
                //sprawdzanie istnienia grafiku
                $result = $connection->query("SELECT * FROM prohibition WHERE date LIKE '%$date%'");
                if(!$result) throw new Exception($connection->error);

                $how_many_tables = $result->num_rows;
                if($how_many_tables==0) {
                    $everything_OK=false;
                    $_SESSION['e_read']="Zakaz z wybranego miesiąca nie istnieje!";
                }

                if($everything_OK == true) {
                    $query = "SELECT * FROM prohibition WHERE date LIKE '%$date%'";
                    $result = mysqli_query($connection, $query);
                    $querySelect = "SELECT date FROM prohibition WHERE date LIKE '%$date%'";
                    $resultSelect = mysqli_query($connection, $querySelect);
                }
            }
        }
        catch(Exception $e) {
            echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
            echo '<br>Informacja developerska: '.$e;
        }
    }
    // pobieranie uprawnienia oraz id zalogowanego użytkownika
    include_once 'redirects/db-management.php';
    $connection=mysqli_connect($host, $db_user, $db_password, $db_name);
    if(!$connection) die('Could not Connect My Sql:');

    $login = $_SESSION['login'];
    $currentRole = mysqli_query($connection, "SELECT role FROM users WHERE login='$login'");
    $currentTkid = mysqli_query($connection, "SELECT tkid FROM users WHERE login='$login'");
?>
