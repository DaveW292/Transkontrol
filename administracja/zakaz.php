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
        echo "<p>Witaj ".$_SESSION['login'].'!</p>';
        echo "<a href='redirects/logout.php'>Wyloguj się!</a><br><br>";
        if ($currentRole->num_rows > 0) 
        {
            while($row = $currentRole->fetch_assoc()) 
            {
                $myRole = $row["role"];
            }
        }
        if ($currentTkid->num_rows > 0)
        {
            while($row = $currentTkid->fetch_assoc()) 
            {
                $myTkid = $row["tkid"];
            }
        }
        if($myRole == "admin")
        {
            error_reporting(0);
            echo '<a href="crud/create-prohibition">NOWY ZAKAZ</a>';
        }
    ?>
    <br><br>
    <!-- wybranie tabeli -->
    <fieldset>
        <legend>Wyświetl grafik</legend>
        <form action="grafik" method="post">
            Wybierz zakres jednego tygodnia od poniedziałku do niedzieli <br>
            <input type="date" value=<?php
                $dates = split ("\_", $newestTable); 
                $newestDateStart = substr($dates[0],0,4).'-'.substr($dates[0],4,2).'-'.substr($dates[0],6,2);
                if(isset($dateStartUpdate)) echo $dateStartUpdate;
                else if(!isset($dateStart) || $dateStart=='') echo $newestDateStart;
                else echo $dateStart;
            ?> name="dateStart">
            <input type="date" value=<?php
                $dates = split ("\_", $newestTable); 
                $newestDateEnd = substr($dates[1],0,4).'-'.substr($dates[1],4,2).'-'.substr($dates[1],6,2);
                if(isset($dateEndUpdate)) echo $dateEndUpdate;
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
        <!-- usuwanie tabeli -->
        <?php if($myRole == "admin") { ?>
        <form action="grafik" method="post" style="display: flex; justify-content: end;">
            <input type="hidden" value=<?php
            $dates = split ("\_", $newestTable); 
            $newestDateStart = substr($dates[0],0,4).'-'.substr($dates[0],4,2).'-'.substr($dates[0],6,2);
            if(!isset($dateStart) || $dateStart=='') echo $newestDateStart;
            else echo $dateStart;
            ?> name="dateStartDelete">
            <input type="hidden" value=<?php
            $dates = split ("\_", $newestTable); 
            $newestDateEnd = substr($dates[1],0,4).'-'.substr($dates[1],4,2).'-'.substr($dates[1],6,2);
            if(!isset($dateEnd) || $dateEnd=='') echo $newestDateEnd;
            else echo $dateEnd;
            ?> name="dateEndDelete">
            <input type="submit" value="USUŃ TABELĘ">
        </form>
        <?php
            if(isset($_SESSION['e_delete']))
            {
                echo '<div class="error">'.$_SESSION['e_delete'].'</div>';
                unset($_SESSION['e_delete']);
            }
        }
        ?>
        <!-- aktualizacja tabeli -->
        <?php if($myRole == "admin") { ?>
        <fieldset>
            <legend>Aktualizuj grafik</legend>
            <form action="grafik" method="post">
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

                <select name="day"><?php for($x = 0; $x < sizeof($days); $x++) echo '<option value="'.$daysEn[$x].'">'.$days[$x].'</option>'; ?></select>
                <br><br>
                <input type="radio" id="1" name="hour" value="1" required>
                <label for="1">06:00 - 14:00</label>
                <input type="radio" id="2" name="hour" value="2" required>
                <label for="2">14:00 - 22:00</label>
                <br><br>
                <select name="carrier"><?php for($x = 0; $x < sizeof($carriers); $x++) echo '<option>'.$carriers[$x].'</option>'; ?></select>
                <br><br>
                <textarea name="team" cols="8" rows="2" placeholder="np. 123 - 456, 321 - 654"><?php if(isset($_SESSION['fr_team'])) { echo $_SESSION['fr_team']; unset($_SESSION['fr_team']);} ?></textarea>
                <br><br>
                <input type="submit" value="AKTUALIZUJ">
            </form>
            <?php
                if(isset($_SESSION['e_update']))
                {
                    echo '<div class="error">'.$_SESSION['e_update'].'</div>';
                    unset($_SESSION['e_update']);
                }
            ?>

            <?php } ?>
            <!-- wyświetlanie tabeli -->
            <table border = "2px, solid, black">

                <tr>
                    <th rowspan = "2">Przewoźnik</th>
                    <?php for($x = 0; $x < sizeof($days); $x++) {?>
                        <th colspan = "2"><?php echo $days[$x]; ?></th>
                    <?php } ?>
                </tr>

                <tr class="hours">
                    <?php for($x = 0; $x < 7; $x++) {?>
                        <th>06:00 - 14:00</th>
                        <th>14:00 - 22:00</th>
                    <?php } ?>
                </tr>

                <?php
                    $i=0;
                    while($row = mysqli_fetch_array($result))
                    {
                ?>
                <tr class="teams">
                    <td><?php echo $row["carrier"]; ?></td>
                    <?php
                        if($myRole == "admin") {
                            for($x = 0; $x < sizeof($shifts); $x++) {
                                echo '<td>'.$row[$shifts[$x]].'</td>';
                            }
                        } else {
                            for($x = 0; $x < sizeof($shifts); $x++) {
                                if(strpos($row[$shifts[$x]], $myTkid) !== false)
                                {
                                    $text = "";
                                    $re = '/([0-9]+ ?- ?[0-9]+)/m';
                                    $str = $row[$shifts[$x]];
                                    preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
                                    if(count($matches) > 0) {
                                        foreach( $matches as $match ) {
                                            $text .= substr_count($match[1], $myTkid) >= 1 ? $match[1] : "";
                                        }
                                    }
                                    echo '<td>'.$text.'</td>';


                                    // if(substr_count($row[$shifts[$x]], $myTkid." -") == 1) {
                                    //     $record = preg_filter('/'.$myTkid.' - [0-9]{3}/', '($0)', $row[$shifts[$x]]);
                                    //     $record = preg_replace('/.*[(]/', '', $record);
                                    //     $record = preg_replace('/[)].*/', '', $record);
                                    //     echo '<td>'.$record.'</td>';
                                    // }

                                    // if(substr_count($row[$shifts[$x]], "- ".$myTkid) == 1) {
                                    //     $record = preg_filter('/[0-9]{3} - '.$myTkid.'/', '($0)', $row[$shifts[$x]]);
                                    //     $record = preg_replace('/.*[(]/', '', $record);
                                    //     $record = preg_replace('/[)].*/', '', $record);
                                    //     echo '<td>'.$record.'</td>';
                                    // }
                                } else if(stripos($row[$shifts[$x]], 'ZAKAZ') !== false)  {
                                    echo '<td>'.$row[$shifts[$x]].'</td>';
                                } else {
                                    echo '<td></td>';
                                }
                            }
                        }
                    ?>
                </tr>
                
                <?php
                        $i++;
                    }
                    $connection->close();
                ?>
            </table>
        </fieldset>
    </fieldset>
</body>