<?php
require __DIR__ . '/_connect.php';
require __DIR__ . '/_account.php';

session_start();

$account = new Account();

try {
    $account->logout();
    $_SESSION['successMessage'] = 'Logout successful';
    header("Location: ../");
    die;
} catch (Exception $ex) {
    $_SESSION['errorMessage'] = 'Logout failed, are you sure you haven\'t already logged out?';
    header("Location: {$_SERVER['HTTP_REFERER']}");
    $connection->close();
    die;
}
