<?php
require_once('dataaccess.php');

session_start();

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    return;
}

deleteAppointment($_SESSION['email']);

header('Location: index.php');

?>