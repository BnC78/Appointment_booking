<?php
require_once('dataaccess.php');

session_start();

if (isset($_SESSION['email'])) header('Location: index.php');

$hibak = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    if (isset($_POST['email']) && trim($_POST['email']) != '') $email = $_POST['email'];
    else $hibak[] = 'Nincs megadva email!';
    if (isset($_POST['pw']) && trim($_POST['pw']) != '') $pw = $_POST['pw'];
    else $hibak[] = 'Nincs megadva jelszo!';

    if (count($hibak) == 0) {
        if (userExists($email)) {
            if (!passwordMatches($email, $pw)) $hibak[] = 'Az jelszó nem megfelelő!';
            else {
                $_SESSION['email'] = $email;
                if (!isset($_SESSION['application']))
                    header('Location: index.php');
                else
                    if ($_SESSION['email'] == 'admin@nemkovid.hu')
                        header('Location: apply.php?id=' . $_SESSION['application'] . '&details=true');
                    else
                        header('Location: apply.php?id=' . $_SESSION['application']);
            }
        }
        else $hibak[] = 'Ezzel az email címmel még nem regisztráltak!';
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
    <title>NemKoViD - Bejelentkezés</title>
</head>
<body>
    <a class="titlepage" href="index.php">Vissza a főoldalra</a>
    <h1>Bejelentkezés</h1>
    <div id="container">
        <?php if (count($hibak) != 0): ?>
            <ul class="hibak">
                <?php foreach ($hibak as $hiba): ?>
                    <li><?=$hiba?></li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
        <form method="post" novalidate>
            <table id="form">
                <tr>
                    <td>Email:</td>
                    <td><input name="email" value="<?=isset($email) ? $email : ''?>"></input></td>
                </tr>
                <tr>
                    <td>Jelszó:</td>
                    <td><input type="password" name="pw" value="<?=isset($pw) ? $pw : ''?>"></input></td>
                </tr>
            </table>
            <input type="submit" value="Bejelenkezek"></input>
        </form>
    <div>
</body>
</html>