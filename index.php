<?php
require_once('dataaccess.php');

session_start();

$appointments = getAppointments();

$logged_in = isset($_SESSION['email']);
$admin = false;
$hasAppointment = false;

if ($logged_in) {
    $appointmentID = getUser($_SESSION['email'])->appointment;
    if ($appointmentID !== false)
        $hasAppointment = true;
    
    if ($hasAppointment === true) {
        $yourAppointment = $appointments[$appointmentID]->date . ' ' . $appointments[$appointmentID]->time;
    }
    if ($_SESSION['email'] === 'admin@nemkovid.hu')
        $admin = true;
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
<header>
    <h1>NemKoViD</h1>
    <?php if (!$logged_in): ?>
        <a href="register.php">Regisztáció</a>
        <a href="login.php">Bejelentkezés</a>
    <?php else: ?>
        <a href="logout.php">Kijelentkezés</a>
    <?php endif ?>
</header>
<main>
    <p>
        Az alábbi oldalon lehet időpontot foglalni a NemKoVid központi épületében lévő koronavírus oltásra!
    </p>
    <?php if ($logged_in): ?>
        <span id="user">
            Belépve, mint <a id="name"><?=getUser($_SESSION['email'])->name?></a>
        </span></br>
        <?php if ($hasAppointment): ?>
        <p id="appointment">
            Lefoglalt időpontod: <?= $yourAppointment ?> </br>
            <form method="post" action="resign.php"><input type="submit" value="Időpont lemondása"></form>
        </p>
        <?php endif ?>
        <?php if ($admin): ?>
        <a href="newdate.php">Új időpont meghirdetése</a>
        <?php endif ?>
    <?php endif ?>
    <p id="months">
        <button id="previous">Előző hónap</button>
        | <span id="listedMonth"></span> |
        <button id="next">Következő hónap</button>
    </p>
    <table id="list">
    </table>
</main>
</body>
<script>

let previousMonth = document.querySelector('#previous');
let nextMonth = document.querySelector('#next');
let listedMonth = document.querySelector('#listedMonth');

let list = document.querySelector('#list');

function getMonth(event) {
    let xhr = new XMLHttpRequest();

    xhr.addEventListener('readystatechange', ()=>{
        if (xhr.readyState == 4){
            listedMonth.innerHTML = xhr.responseText.split('|separator|')[0];
            list.innerHTML = xhr.responseText.split('|separator|')[1];
        }
    })

    xhr.open('GET', `listedmonth.php?month=${event.target.id}`, true);
    xhr.send();
}

previousMonth.addEventListener('click', getMonth);
nextMonth.addEventListener('click', getMonth);

let xhr = new XMLHttpRequest();
xhr.addEventListener('readystatechange', ()=>{
    if (xhr.readyState == 4){
            listedMonth.innerHTML = xhr.responseText.split('|separator|')[0];
            list.innerHTML = xhr.responseText.split('|separator|')[1];
    }
})
xhr.open('GET', 'listedmonth.php', true);
xhr.send();
</script>
</html>