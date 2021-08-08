<?php
    session_start();

    include_once '../redirects/db-management.php';
    $connection=mysqli_connect($host, $db_user, $db_password, $db_name);
    if(!$connection) die('Nie można połączyć się z bazą!');

    $login = $_SESSION['login'];
    $currentRole = mysqli_query($connection, "SELECT role FROM users WHERE login='$login'");

    if ($currentRole->num_rows > 0) {
        while($row = $currentRole->fetch_assoc()) {
             $myRole = $row["role"];
        }
    }

    if(!isset($_SESSION['logged']) || $myRole != "admin") {
        header('Location: ../');
        exit();
    }

    $hours = array("06:00 - 14:00", "14:00 - 22:00", "22:00 - 06:00");

    if(isset($_POST['month']) && isset($_POST['year'])) {
        $everything_OK=true;

        $days = $_POST['days'];
        $month = $_POST['month'];
        $year = $_POST['year'];

        $x = 0;
        for($d = 1; $d <= $days; $d++) {
            for($s = 1; $s <= sizeof($hours); $s++) {
                $carriers[$x] = $_POST[$d.'shift'.$s];
                $x++;
            }
        }

        require_once "../redirects/db-management.php";
        mysqli_report(MYSQLI_REPORT_STRICT);
        try {
            $connection = new mysqli($host, $db_user, $db_password, $db_name);
            if($connection->connect_errno!=0) {
                throw new Exception(mysqli_connect_errno());
            } else {

                //sprawdzanie istnienia zakazu
                $mktime = mktime(0, 0, 0, $month, 1, $year);
                $checkDate = date("Y-m", $mktime);

                $result = $connection->query("SELECT * FROM `prohibition` WHERE date LIKE '%$checkDate%'");
                if(!$result) throw new Exception($connection->error);

                $how_many_rows = $result->num_rows;
                if($how_many_rows>0){
                    $everything_OK=false;
                    $_SESSION['e_create']="Zakaz z wybranego miesiąca już istnieje!";
                }

                if($everything_OK==true) {
                    // Dodaj wiersze
                    $z = 0;
                    for($x=1; $x <= $days; $x++) {
                        $values = '';
                        $mktime = mktime(0, 0, 0, $month, $x, $year);
                        $date = date("Y-m-d", $mktime);

                        for($y=0; $y < 3; $y++){
                            if($y==2) {
                                $values .= "'".$carriers[$z]."')";
                            } else {
                                $values .= "'".$carriers[$z]."', ";
                            }
                            $z++;
                        }

                        $query = "INSERT INTO
                                    prohibition (date, shift1, shift2, shift3)
                                VALUES
                                    ('$date', ".$values;

                        if(mysqli_query($connection, $query)) {
                            $_SESSION['sent'] = true;
                            header('Location: ../zakaz');
                            if(isset($_SESSION['e_create'])) unset($_SESSION['e_create']);
                        }
                        else throw new Exception($connection -> error);
                    }
                }
            }
        }
        catch(Exception $e) {
            echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
            echo '<br>Informacja developerska: '.$e;
        }
    }
    include '../redirects/db-management.php';
    $connection=mysqli_connect($host, $db_user, $db_password, $db_name);
    if(!$connection) die('Nie można połączyć się z bazą!');
    $tkid = mysqli_query($connection, "SELECT tkid FROM users WHERE role = 'user'");
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="stylesheet" href="/styles/panel.css">
</head>
<body onload="showCalendar()">
<h2><a href="../zakaz">POWRÓT</a></h2>
<?php
    if(isset($_SESSION['e_create'])) {
        echo '<div class="error">'.$_SESSION['e_create'].'</div>';
        unset($_SESSION['e_create']);
    }
?>
    <form method="post" enctype="multipart/form-data">

        <table border = "1px, solid, black">
            <thead>
                <th>
                    <select name="month" id="month" onchange="showCalendar()">
                        <option <?php if(date("m") == '12') echo 'selected'?> value="1">01</option>
                        <option <?php if(date("m") == '01') echo 'selected'?> value="2">02</option>
                        <option <?php if(date("m") == '02') echo 'selected'?> value="3">03</option>
                        <option <?php if(date("m") == '03') echo 'selected'?> value="4">04</option>
                        <option <?php if(date("m") == '04') echo 'selected'?> value="5">05</option>
                        <option <?php if(date("m") == '05') echo 'selected'?> value="6">06</option>
                        <option <?php if(date("m") == '06') echo 'selected'?> value="7">07</option>
                        <option <?php if(date("m") == '07') echo 'selected'?> value="8">08</option>
                        <option <?php if(date("m") == '08') echo 'selected'?> value="9">09</option>
                        <option <?php if(date("m") == '09') echo 'selected'?> value="10">10</option>
                        <option <?php if(date("m") == '10') echo 'selected'?> value="11">11</option>
                        <option <?php if(date("m") == '11') echo 'selected'?> value="12">12</option>
                    </select>
                    <select name="year" id="year" onchange="showCalendar()">
                        <option><?php echo date("Y"); ?></option>
                        <option <?php if(date("m") == '12') echo 'selected'?>><?php echo date("Y")+1; ?></option>
                    </select>
                </th>

                <?php for($x = 0; $x < sizeof($hours); $x++) {?>
                    <th><?php echo $hours[$x];?></th>
                <?php } ?>
            </thead>
            <tbody id="days"></tbody>
        </table>
        <p id="daysSum"></p>
        <input type="submit" value="DODAJ">
    </form>
    <?php $connection->close(); ?>
    <script src="/administracja/js/calendar.js"></script>
</body>
