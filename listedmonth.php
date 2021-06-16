<?php
require_once('dataaccess.php');

session_start();

if (!isset($_SESSION['listedMonth']))
    $_SESSION['listedMonth'] = date('Y-m');

if (isset($_GET['month'])) {
    $currListed = explode('-', $_SESSION['listedMonth']);
    if ($_GET['month'] == 'previous') {
        if (intval($currListed[1]) == 1) {
            $_SESSION['listedMonth'] = (strval($currListed[0]-1) . '-12');
        } else {
            $_SESSION['listedMonth'] = strval($currListed[0]) . '-';
            if ($currListed[1] < 11) $_SESSION['listedMonth'] .= '0';
            $_SESSION['listedMonth'] .= strval($currListed[1]-1);
        }
    } else if ($_GET['month'] == 'next') {
        if (intval($currListed[1]) == 12) {
            $_SESSION['listedMonth'] = strval($currListed[0]+1) . '-01';
        } else {
            $_SESSION['listedMonth'] = strval($currListed[0]) . '-';
            if ($currListed[1] < 9) $_SESSION['listedMonth'] .= '0';
            $_SESSION['listedMonth'] .= strval($currListed[1]+1);
        }
    }
}

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
<?php echo $_SESSION['listedMonth'] ?>|separator|

<tr id="daysHeader">
    <th>Hétfő</th>
    <th>Kedd</th>
    <th>Szerda</th>
    <th>Csütörtök</th>
    <th>Péntek</th>
    <th>Szombat</th>
    <th>Vasárnap</th>
</tr>
<?php $start = false; $dayofmonth = 1; $weeks = 0 ?>
<?php while ($weeks < 6 && $dayofmonth <= intval(date('t', strtotime($_SESSION['listedMonth'])))): ?>
    <tr class="days">
        <?php for ($days = 1; $days <= 7; ++$days): ?>
            <?php if (!$start && $weeks == 0 && date('N', strtotime($_SESSION['listedMonth']) . "-01") == $days) {
                $start = true;
                $dayofmonth = 1;
            } ?>
            <td>
                <?php if($start && $dayofmonth <= intval(date('t', strtotime($_SESSION['listedMonth'])))): ?>
                    <?= $dayofmonth ?>
                <?php endif ?>
                <?php ++$dayofmonth ?>
            </td>
        <?php endfor ?>
    </tr>
    <?php 
    if ($weeks == 0) {
        $start = false;
        $dayofmonth = 1;
    } else {
        $dayofmonth -= 7;
    }
    ?>
    <tr class="apps">
        <?php for ($days = 1; $days <= 7; ++$days): ?>
            <?php if (!$start && $weeks == 0 && date('N', strtotime($_SESSION['listedMonth']) . "-01") == $days) {
                $start = true;
            } ?>
            <td>
                <?php if($start): ?>
                    <?php for ($i = 0; $i < count($appointments); ++$i): ?>
                        <?php $appointment = $appointments[$i] ?>
                        <?php if ($appointment->date == $_SESSION['listedMonth'] . "-" . ($dayofmonth < 10 ? "0" : "") . $dayofmonth): ?>
                            <?php if (!$admin): ?>
                                <a
                                    <?php if($appointment->places != count($appointment->users) && !$hasAppointment):?>
                                        href="apply.php?id=<?= $i ?>"       
                                    <?php endif ?>
                                        class="<?= (count($appointment->users) == $appointment->places) ? 'full' : 'notfull' ?>">
                                    <?= $appointment->time ?> || <?= count($appointment->users) ?> / <?= $appointment->places ?>
                                </a>
                            <?php else: ?>
                                <a href="apply.php?id=<?= $i ?>&details=true" class="<?= (count($appointment->users) == $appointment->places) ? 'full' : 'notfull'?>">
                                    <?= $appointment->time ?> || <?= count($appointment->users) ?> / <?= $appointment->places ?>
                                </a>
                            <?php endif ?>
                        <?php endif ?>
                    <?php endfor ?>
                    <?php $dayofmonth++ ?>
                <?php endif ?>
            </td>
        <?php endfor ?>
        <?php if ($dayofmonth > intval(date('t', strtotime($_SESSION['listedMonth'])))) {break;} ?>
    </tr>
    <?php ++$weeks ?>
<?php endwhile ?>




<!-- Ellenőrzés céljából meghagytam a listás kiiratást is. -->
<!--
<tr>
    <th>Nap</th>
    <th>Időpont</th>
    <th>Szabad hely / Összes hely</th>
    <th></th>
</tr>
<?php for($i = 0; $i < count($appointments); ++$i): ?>
    <?php $appointment = $appointments[$i] ?>
    <?php if (strpos($appointment->date, $_SESSION['listedMonth']) !== false): ?>
        <tr class="<?= (count($appointment->users) == $appointment->places) ? 'full' : 'notfull' ?>">
            <td><?= $appointment->date ?></td>
            <td><?= $appointment->time ?></td>
            <td><?= count($appointment->users) ?> / <?= $appointment->places ?></td>
            <td>
                <?php if ((!$logged_in || !$hasAppointment) && $appointment->places != count($appointment->users) && !$admin): ?>
                    <a href="apply.php?id=<?= $i ?>">Jelentkezés</a>
                <?php endif ?>
                <?php if ($admin): ?>
                    <a href="apply.php?id=<?= $i ?>&details=true">Részletek</a>
                <?php endif ?>
            </td>
        </tr>
    <?php endif ?>
<?php endfor ?>
-->