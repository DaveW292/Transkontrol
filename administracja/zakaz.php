<?php include "validation/zakaz.php"; ?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="stylesheet" href="../styles/panel.css">
</head>
<body id="schedule">
    <nav>
        <a href="kontakty"><h2>Kontakty</h2></a>
        <a href="aktualnosci"><h2>Aktualności</h2></a>
        <a href="grafik"><h2>Grafik</h2></a>
        <a href="dyspozycyjnosc"><h2>Dyspozycyjność</h2></a>
        <h2>Zakaz ZTM</h2>
    </nav>
    <?php
    echo $deleteDate;
        echo "<p>Witaj ".$_SESSION['login'].'!</p>';
        echo "<a href='redirects/logout.php'>Wyloguj się!</a><br><br>";
        if ($currentRole->num_rows > 0) {
            while($row = $currentRole->fetch_assoc()) {
                $myRole = $row["role"];
            }
        }
        if ($currentTkid->num_rows > 0) {
            while($row = $currentTkid->fetch_assoc()) {
                $myTkid = $row["tkid"];
            }
        }
        if($myRole == "admin") {
            error_reporting(0);
    ?>
    <div style="display: flex;">
        <a href="crud/create-prohibition">NOWY ZAKAZ</a>
<!-- usuwanie tabeli -->
        <form action="zakaz" method="post" style="margin-left: 50px;">
            <input type="hidden" value=<?php echo $date; ?> name="deleteDate">
            <input type="submit" value="USUŃ TABELĘ">
        </form>
        <?php
            if(isset($_SESSION['e_delete'])) {
                echo '<div class="error">'.$_SESSION['e_delete'].'</div>';
                unset($_SESSION['e_delete']);
            }
        ?>
    </div>

    <br>
<!-- aktualizacja tabeli -->
    <fieldset>
        <legend>Edycja</legend>
        <form id="editTable" action="zakaz" method="post">
            <table>
                <tr>
                    <td>
                        <select name="date">
                            <?php
                                $i=0;
                                while($row = mysqli_fetch_array($resultSelect)) {
                            ?>
                            <option><?php echo $row["date"]; ?></option>
                            <?php
                                    $i++;
                                }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select name="shift">
                                <option value="shift1">06:00 - 14:00</option>
                                <option value="shift2">14:00 - 22:00</option>
                                <option value="shift3">22:00 - 06:00</option>
                        </select>
                    </td>
                    <td>
                        <select name="carrier">
                            <?php for($x = 0; $x < sizeof($carriers); $x++) echo '<option>'.$carriers[$x].'</option>'; ?>
                        </select>
                    </td>
                    <td>
                        <input type="submit" value="AKTUALIZUJ">
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
    <?php } ?>
<!-- wyświetlanie tabeli -->
    <table border = "2px, solid, black">
        <tr>
            <th>
                <form id="displayTable" action="zakaz" method="post">
                    <select name="month" id="month" onchange="submitDisplayTable()">
                        <?php
                            $tmpYearMonth = substr_replace($dateUpdate, "", 7);
                            $tmpMonth = substr($tmpYearMonth, 5);
                            for($x = 1; $x <= 12; $x++) {
                                if(strlen($x)==1) $x = '0'.$x;

                                if($tmpMonth == $x) {
                                    echo '<option selected>'.$x.'</option>';
                                }
                                else if($_POST['month'] == $x) {
                                    echo '<option selected>'.$x.'</option>';
                                } 
                                else { 
                                    echo '<option>'.$x.'</option>';
                                }
                            }
                        ?>
                    </select>
                    <select name="year" id="year" onchange="submitDisplayTable()">
                    <?php
                        $nextYear = date("Y") + 1;
                        $currentYear = date("Y");
                        $previousYear = date("Y") - 1;
                        $updateYear = substr_replace($dateUpdate, "", 4);

                        if($tmpYear == $nextYear) {
                            echo '<option selected>'.$nextYear.'</option>';
                        } else if($_POST['year'] == $nextYear) {
                            echo '<option selected>'.$nextYear.'</option>';
                        } else {
                            echo '<option>'.$nextYear.'</option>';
                        }

                        if($tmpYear == $currentYear) {
                            echo '<option selected>'.$currentYear.'</option>';
                        } else if($_POST['year'] == $currentYear) { 
                            echo '<option selected>'.$currentYear.'</option>';
                        } else {
                            echo '<option>'.$currentYear.'</option>';
                        }

                        if($tmpYear == $previousYear) {
                            echo '<option selected>'.$previousYear.'</option>';
                        } else if($_POST['year'] == $previousYear) {
                            echo '<option selected>'.$previousYear.'</option>';
                        } else {
                            echo '<option>'.$previousYear.'</option>';
                        }
                    ?>
                    </select>
                </form>
            </th>
            <?php for($x = 0; $x < sizeof($hours); $x++) {?>
                <th><?php echo $hours[$x];?></th>
            <?php } ?>
        </tr>
        <?php
            $i=0;
            while($row = mysqli_fetch_array($result)) {
        ?>
        <tr class="teams">
            <td><?php echo $row["date"]; ?></td>
            <td><?php echo $row["shift1"]; ?></td>
            <td><?php echo $row["shift2"]; ?></td>
            <td><?php echo $row["shift3"]; ?></td>
        </tr>
        <?php
                $i++;
            }
            $connection->close();
        ?>
    </table>
    <?php
        if(isset($_SESSION['e_read'])) {
            echo '<div class="error">'.$_SESSION['e_read'].'</div>';
            unset($_SESSION['e_read']);
        }
    ?>
    <script src="/administracja/js/submit.js"></script>
</body>
