<?php

require_once('dataaccess.php');

session_start();

if ($_SESSION['email'] != 'admin@nemkovid.hu') header('Location: index.php');

$hibak = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['date']) && trim($_POST['date']) != '') $date = $_POST['date'];
    else $hibak[] = 'date-missing';
    if (isset($_POST['time']) && trim($_POST['time']) != '') $time = $_POST['time'];
    else $hibak[] = 'time-missing';
    if (isset($_POST['places']) && trim($_POST['places']) != '') $places = $_POST['places'];
    else $hibak[] = 'places-missing';

    if (count($hibak) == 0) {
        $dateParts = explode('-', $date);
        if (isset($date) && (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date) ||
            !checkdate($dateParts[1], $dateParts[2], $dateParts[0])))
                $hibak[] = 'date-form';
        
        $timeParts = explode(':', $time);
        if (isset($time) && (!preg_match('/^[0-9]{2}:[0-9]{2}$/', $time) ||
            intval($timeParts[0]) < 0 || intval($timeParts[0]) > 23 ||
            intval($timeParts[1]) < 0 || intval($timeParts[1]) > 59))
                $hibak[] = 'time-form';
        
        if (isset($places) && (!preg_match('/^[0-9]+$/', $places) || intval($places) != $places || intval($places) <= 0))
            $hibak[] = 'places-form';
    }

    if (count($hibak) == 0) {
        addAppointment($date, $time, $places);
        header('Location: index.php');
    }

}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" href="covid.ico">
    <title>NemKoViD</title>
</head>
<body>
<a class="titlepage" href="index.php">Vissza a főoldalra</a>
    <h1>Új időpont meghírdetése</h1>
    <div id="container">
        <form method="post" novalidate>
            <table id="form">
                <tr>
                    <td>Dátum:</td>
                    <td><input name="date" placeholder="yyyy-mm-dd" value="<?=isset($date) ? $date : ''?>"></input></td>
                </tr>
                <?php if (in_array('date-missing', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">Nincs megadva dátum!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <?php if (in_array('date-form', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">A dátum formátuma nem megfelelő!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td>Időpont:</td>
                    <td><input name="time" placeholder="hh:mm" value="<?=isset($time) ? $time : ''?>"></input></td>
                </tr>
                <?php if (in_array('time-missing', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">Nincs megadva időpont!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <?php if (in_array('time-form', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">Az időpont formátuma nem megfelelő!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td>Helyek száma:</td>
                    <td><input name="places" placeholder="0" value="<?=isset($places) ? $places : ''?>"></input></td>
                </tr>
                <?php if (in_array('places-missing', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">Nincs megadva a helyek száma!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <?php if (in_array('places-form', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">A helyek számának egésznek kell lennie és nagyobbnak, mint 0!</span>
                        </td>
                    </tr>
                <?php endif ?>
            </table>
            <input type="submit" value="Meghirdetés"></input>
        </form>
    <div>
</body>
</html>