<?php
require_once('dataaccess.php');

session_start();

if (isset($_SESSION['email'])) header('Location: index.php');

$hibak = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['name']) && trim($_POST['name']) != '')
        $name = trim($_POST['name']);
    else $hibak[] = 'name-missing';
    if (isset($_POST['taj']) && trim($_POST['taj']) != ''){
        $taj = trim($_POST['taj']);
        if (strlen($taj) != 9 || !is_numeric($taj)) $hibak[] = "taj-form";
    }
    else $hibak[] = 'taj-missing';
    if (isset($_POST['address']) && trim($_POST['address']) != '')
        $address = trim($_POST['address']);
    else $hibak[] = 'address-missing';
    if (isset($_POST['email']) && trim($_POST['email']) != ''){
        $email = trim($_POST['email']);
        $parts = explode('@', $email);
        if (!strpos($email, '@') || count($parts) != 2 || !strpos($parts[1], '.')) {
            $hibak[] = 'email-form';
        }
        if (userExists($email)) {
            $hibak[] = 'email-exists';
        }
    }
    else $hibak[] = 'email-missing';
    if (isset($_POST['pw']) && trim($_POST['pw']) != '')
        $pw = trim($_POST['pw']);
    else $hibak[] = 'password-missing';
    if (isset($_POST['pw2']) && trim($_POST['pw2']) != '') {
        $pw2 = trim($_POST['pw2']);
        if (isset($_POST['pw']) && $pw != $pw2) $hibak[] = "password-different";
    }
    else $hibak[] = 'password2-missing';

    if (count($hibak) == 0)
        register($name, $taj, $address, $email, $pw);

}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" href="covid.ico">
    <title>NemKoViD - Regisztráció</title>
</head>
<body>
    <a class="titlepage" href="index.php">Vissza a főoldalra</a>
    <h1>Regisztáció</h1>
    <div id="container">
        <form method="post" novalidate>
            <table id="form">
                <tr>
                    <td>Teljes név:</td>
                    <td><input name="name" value="<?=isset($name) ? $name : ''?>"></input></td>
                </tr>
                <?php if (in_array('name-missing', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">A teljes név megadása kötelező!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td>TAJ szám:</td>
                    <td><input name="taj" value="<?=isset($taj) ? $taj : ''?>"></input></td>
                </tr>
                <?php if (in_array('taj-missing', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">A TAJ szám megadása kötelező!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <?php if (in_array('taj-form', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">A TAJ számnak kilenc számból kell állnia!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td>Értesítési cím:</td>
                    <td><input name="address" value="<?=isset($address) ? $address : ''?>"></input></td>
                </tr>
                <?php if (in_array('address-missing', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">Az értesítési cím megadása kötelező!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td>Email:</td>
                    <td><input name="email" value="<?=isset($email) ? $email : ''?>"></input></td>
                </tr>
                <?php if (in_array('email-missing', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">Az email cím megadása kötelező!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <?php if (in_array('email-form', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">Az email cím formátuma nem megfelelő!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <?php if (in_array('email-exists', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">A megadott email címmel már regisztráltak!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td>Jelszó:</td>
                    <td><input type="password" name="pw" value="<?=isset($pw) ? $pw : ''?>"></input></td>
                </tr>
                <?php if (in_array('password-missing', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">A jelszó megadása kötelező!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td>Jelszó megerősítése:</td>
                    <td><input type="password" name="pw2" value="<?=isset($pw2) ? $pw2 : ''?>"></input></td>
                </tr>
                <?php if (in_array('password2-missing', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">A jelszó megerősítése kötelező!</span>
                        </td>
                    </tr>
                <?php endif ?>
                <?php if (in_array('password-different', $hibak)): ?>
                    <tr>
                        <td></td>
                        <td>
                            <span class="hiba">A jelszavak nem egyeznek!</span>
                        </td>
                    </tr>
                <?php endif ?>
            </table>
            <input type="submit" value="Regisztrálok"></input>
        </form>
    <div>
</body>
</html>