<?php include "validation/dyspozycyjnosc.php"; ?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <link rel="stylesheet" href="../styles/panel.css">
</head>
<body>
    <nav>
        <a href="kontakty"><h2>Kontakty</h2></a>
        <a href="aktualnosci"><h2>Aktualności</h2></a>
        <a href="grafik"><h2>Grafik</h2></a>
        <h2>Dyspozycyjność</h2>
        <a href="zakaz"><h2>Zakaz ZTM</h2></a>
    </nav>
    <?php
        echo "<p>Witaj ".$_SESSION['login'].'!</p>';
        echo "<a href='redirects/logout.php'>Wyloguj się!</a><br><br>";
        error_reporting(0);
        if($myRole == "user") {
    ?>
    <!-- dodaj wiersz -->
    <fieldset>
        <legend>Dodaj dyspozycyjność</legend>
        <form action="dyspozycyjnosc" method="post">
            <input type="hidden" name="tkid" value="<?php echo $myTkid; ?>">
            <input type="hidden" name="date" value="<?php echo date("Y-m-d H:i"); ?>">
            <input type="hidden" value=<?php
                    $dates = split ("\_", $newestTable); 
                    $newestDateStart = substr($dates[0],0,4).'-'.substr($dates[0],4,2).'-'.substr($dates[0],6,2);
                    if(!isset($dateStart) || $dateStart=='') echo $newestDateStart;
                    else echo $dateStart;
                ?> name="dateStartCreate">

                <input type="hidden" value=<?php
                    $dates = split ("\_", $newestTable); 
                    $newestDateEnd = substr($dates[1],0,4).'-'.substr($dates[1],4,2).'-'.substr($dates[1],6,2);
                    if(!isset($dateEnd) || $dateEnd=='') echo $newestDateEnd;
                    else echo $dateEnd;
                ?> name="dateEndCreate">
            <table border = "1px, solid, black">
            <tr>
                <td rowspan = "2">Numer służbowy</td>
                <?php for($x = 0; $x < sizeof($days); $x++) {?>
                    <td colspan = "2"><?php echo $days[$x]; ?></td>
                <?php } ?>
            </tr>
            <tr>
                <?php for($x = 0; $x < 7; $x++) {?>
                    <td>06:00 - 14:00</td>
                    <td>14:00 - 22:00</td>
                    <?php } ?>
            </tr>
            <tr>
                <td><input type="text" value="<?php echo $myTkid; ?>" disabled></td>
                <?php for($y = 0; $y < sizeof($shifts); $y++) { ?>
                <td>
                    <select name = "<?php echo $shifts[$y]?>">
                        <option <?php if ($_SESSION['fr_'.$shifts[$y]] == "TAK") echo 'selected="selected" ';?>>TAK</option>
                        <option <?php if ($_SESSION['fr_'.$shifts[$y]] == "NIE") echo 'selected="selected" ';?>>NIE</option>
                    </select>
                </td>
                <?php } ?>
            </tr>
            </table>
            <input type="submit" value="DODAJ">
        </form>
    <?php
        if(isset($_SESSION['e_create']))
        {
            // if(isset($_SESSION['e_team'])) unset($_SESSION['e_team']);
            echo '<div class="error">'.$_SESSION['e_create'].'</div>';
            unset($_SESSION['e_create']);
        }
        if(isset($_SESSION['e_team']))
        {
            // if(isset($_SESSION['e_create'])) unset($_SESSION['e_create']);
            echo '<div class="error">'.$_SESSION['e_team'].'</div>';
            unset($_SESSION['e_team']);
        }
    ?>
    </fieldset>
    <?php } ?>

    <!-- wybranie tabeli -->
    <fieldset>
        <legend>Wyświetl dyspozycyjność</legend>
        <form action="dyspozycyjnosc" method="post">
            Wybierz zakres jednego tygodnia od poniedziałku do niedzieli <br>
            <input type="date" value=<?php
                $dates = split ("\_", $newestTable); 
                $newestDateStart = substr($dates[0],0,4).'-'.substr($dates[0],4,2).'-'.substr($dates[0],6,2);
                if(isset($dateStartUpdate)) echo $dateStartUpdate;
                else if(isset($dateStartCreate)) echo $dateStartCreate;
                else if(!isset($dateStart) || $dateStart=='') echo $newestDateStart;
                else echo $dateStart;
            ?> name="dateStart">
            <input type="date" value=<?php
                $dates = split ("\_", $newestTable); 
                $newestDateEnd = substr($dates[1],0,4).'-'.substr($dates[1],4,2).'-'.substr($dates[1],6,2);
                if(isset($dateEndUpdate)) echo $dateEndUpdate;
                else if(isset($dateEndCreate)) echo $dateEndCreate;
                else if(!isset($dateEnd) || $dateEnd=='') echo $newestDateEnd;
                else echo $dateEnd;
            ?> name="dateEnd">
            <input type="submit" value="WYŚWIETL">
        </form>
        <?php
            if(isset($_SESSION['e_read']))
            {
                echo '<div class="error">'.$_SESSION['e_read'].'</div>';
                unset($_SESSION['e_read']);
            }
        ?>
        <br>
        <!-- aktualizacja tabeli -->
        <?php if($myRole == "user") { ?>
        <fieldset>
            <legend>Aktualizuj dyspozycyjność</legend>
            <form action="dyspozycyjnosc" method="post">
                <input type="hidden" value=<?php
                    $dates = split ("\_", $newestTable); 
                    $newestDateStart = substr($dates[0],0,4).'-'.substr($dates[0],4,2).'-'.substr($dates[0],6,2);
                    if(!isset($dateStart) || $dateStart=='') echo $newestDateStart;
                    else echo $dateStart;
                ?> name="dateStartUpdate">

                <input type="hidden" value=<?php
                    $dates = split ("\_", $newestTable); 
                    $newestDateEnd = substr($dates[1],0,4).'-'.substr($dates[1],4,2).'-'.substr($dates[1],6,2);
                    if(!isset($dateEnd) || $dateEnd=='') echo $newestDateEnd;
                    else echo $dateEnd;
                ?> name="dateEndUpdate">

                <input type="hidden" name="date" value="<?php echo date("Y-m-d H:i"); ?>">

                <select name="day"><?php for($x = 0; $x < sizeof($days); $x++) echo '<option value="'.$daysEn[$x].'">'.$days[$x].'</option>'; ?></select>
                <br><br>
                <input type="radio" id="1" name="hour" value="1" required>
                <label for="1">06:00 - 14:00</label>
                <input type="radio" id="2" name="hour" value="2" required>
                <label for="2">14:00 - 22:00</label>

                <input type="hidden" value="<?php echo $myTkid; ?>" name="tkid">
                <br><br>

                <select name = "availability">
                    <option>TAK</option>
                    <option>NIE</option>
                </select>
                <br><br>
                <input type="submit" value="AKTUALIZUJ">
            </form>
            <?php } $managementCon->close(); ?>
            <!-- wyświetlanie tabeli -->
            <table border = "1px, solid, black">
                <tr>
                    <td rowspan = "2">Numer służbowy</td>
                    <?php for($x = 0; $x < sizeof($days); $x++) {?>
                        <td colspan = "2"><?php echo $days[$x]; ?></td>
                    <?php } ?>
                    <td rowspan = "2">Data dodania</td>
                </tr>
                <tr>
                    <?php for($x = 0; $x < 7; $x++) {?>
                        <td>06:00 - 14:00</td>
                        <td>14:00 - 22:00</td>
                    <?php } ?>
                </tr>
                <?php
                    $i=0;
                    while($row = mysqli_fetch_array($result))
                    {
                ?>
                <tr>
                    <td><?php echo $row["tkid"]; ?></td>
                    <?php
                        for($x = 0; $x < sizeof($shifts); $x++) echo '<td>'.$row[$shifts[$x]].'</td>';
                    ?>
                    <td><?php echo $row["date"];?></td>
                </tr>
                <?php
                        $i++;
                    }
                    $connection->close();
                ?>
            </table>
            <?php                     
                if(isset($_SESSION['e_table'])) {
                    echo '<div class="error"><h1>'.$_SESSION['e_table'].'</h1></div>';
                    unset($_SESSION['e_table']);
                }
            ?>
        </fieldset>
    </fieldset>
</body>