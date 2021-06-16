<?php

function userExists($email) {
    $users = json_decode(file_get_contents('users.json'));
    return isset($users->$email);
}

function passwordMatches($email, $password) {
    $user = json_decode(file_get_contents('users.json'))->$email;
    return password_verify($password, $user->password);
}

function register($name, $taj, $address, $email, $pw) {
    $users = json_decode(file_get_contents('users.json'));
    $users->$email = (object)[
        'name' => $name,
        'taj' => $taj,
        'address' => $address,
        'email' => $email,
        'password' => password_hash($pw, PASSWORD_DEFAULT),
        'appointment' => false
    ];
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
    header('Location: login.php');
}

function getUser($email) {
    $users = json_decode(file_get_contents('users.json'));
    return $users->$email;
}



function getAppointments() {
    return json_decode(file_get_contents('appointments.json'));
}

function addAppointment($date, $time, $places) {
    $appointments = json_decode(file_get_contents('appointments.json'));
    $appointments[] = (object)[
        'date' => $date,
        'time' => $time,
        'places' => $places,
        'users' => []
    ];
    file_put_contents('appointments.json', json_encode($appointments, JSON_PRETTY_PRINT));
}

function applyAppointment($id, $user) {
    $appointments = json_decode(file_get_contents('appointments.json'));
    $appointments[$id]->users[] = $user;
    file_put_contents('appointments.json', json_encode($appointments, JSON_PRETTY_PRINT));
    $users = json_decode(file_get_contents('users.json'));
    $users->$user->appointment = $id;
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
}

function deleteAppointment($user) {
    $users = json_decode(file_get_contents('users.json'));
    $id = $users->$user->appointment;
    $users->$user->appointment = false;
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
    $appointments = json_decode(file_get_contents('appointments.json'));
    $index = array_search($user, $appointments[$id]->users);
    array_splice($appointments[$id]->users, $index, 1);
    file_put_contents('appointments.json', json_encode($appointments, JSON_PRETTY_PRINT));
}

function getAppointment($id) {
    $appointments = json_decode(file_get_contents('appointments.json'));
    return $appointments[$id];
}
?>