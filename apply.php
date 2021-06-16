<?php
require_once('dataaccess.php');

session_start();

$appointment = getAppointment($_GET['id']);

if (getUser($_SESSION['email'])->appointment !== false || ($appointment->places == count($appointment->users) && !isset($_GET['details'])))
    header('Location: index.php');

$admin = false;
if ($_SESSION['email'] === 'admin@nemkovid.hu')
    $admin = true;

if ((isset($_GET['details']) && !$admin) || (!isset($_GET['details']) && $admin))
    header('Location: index.php');

unset($_SESSION['application']);

if (!isset($_SESSION['email'])) {
    $_SESSION['application'] = $_GET['id'];
    header('Location: login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hiba = false;
    
    if (!isset($_POST['accept']))
        $hiba = true;
    else {
        applyAppointment($_GET['id'], $_SESSION['email']);
        header('Location: success.php');
    }
}

$user = getUser($_SESSION['email']);

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" href="covid.ico">
    <title>NemKoViD - <?= $admin ? 'Időpont részletei' : 'Időpont jelentkezés' ?></title>
</head>
<body>
    <a class="titlepage" href="index.php">Vissza a főoldalra</a>
    <h1><?= $admin ? 'Időpont részletei' : 'Időpont jelentkezés' ?></h1>
    <div id="container">
        <table id="form">
            <tr>
                <td>Dátum:</td>
                <td><?= $appointment->date ?></td>
            </tr>
            <tr>
                <td>Időpont:</td>
                <td><?= $appointment->time ?></td>
            </tr>
            <?php if (!$admin): ?>
            <tr>
                <td>Név:</td>
                <td><?= $user->name ?></td>
            </tr>
            <tr>
                <td>Lakcím:</td>
                <td><?= $user->address ?></td>
            </tr>
            <tr>
                <td>TAJ szám:</td>
                <td><?= $user->taj ?></td>
            </tr>
            <?php endif ?>
        </table>
        <?php if (!isset($_GET['details'])): ?>
            <form method="post" novalidate>
                <input type="checkbox" name="accept">
                <span id="check">Elfogadom, hogy az általam választott időpontban megjelenek.
                Továbbá megértettem, hogy az oltásnak lehetnek mellékhatásai.</span> </br>
                <input type="submit" name="submit" value="Jelentkezés megerősítése"></input>
            </form>
        <?php endif ?>
        <?php if ($hiba): ?>
            <ul class="hibak">
                <li>El kell fogadnod a jeletkezés feltételeit.</li>
            </ul>
        <?php endif ?>
        <?php if($admin && isset($_GET['details'])): ?>
            <p>Jelentkezett felhasználók:</p>
            <table id="applied">
                <tr>
                    <td>Név</td>
                    <td>TAJ szám</td>
                    <td>Értesítési cím</td>
                    <td>Email cím</td>
                </tr>
                <?php for ($i = 0; $i < $appointment->places; ++$i): ?>
                <tr>
                    <?php $user = getUser($appointment->users[$i]); ?>
                    <td><?= $user->name ?></td>
                    <td><?= $user->taj ?></td>
                    <td><?= $user->address ?></td>
                    <td><?= $user->email ?></td>
                </tr>
                <?php endfor ?>
            </table>
        <?php endif ?>
    </div>
</body>
</html>