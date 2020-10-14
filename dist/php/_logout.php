<?php
require __DIR__."/_authTokenOnly.php";
require __DIR__."/_connect.php";

if (isset($_COOKIE[$cookieName])) {
    session_start();
    $cookieValue = $_COOKIE[$cookieName];

    $deleteQuery = $connection->prepare("DELETE FROM t_persist WHERE token = ?");
    $deleteQuery->bind_param("s", $cookieValue);
    $exec = $deleteQuery->execute();

    if ($exec) {
        setcookie($cookieName, "", time() - 3600);
        $_SESSION['logoutSuccess'] = true;
        $_SESSION['logoutMessage'] = "You have been logged out.";
        header("Location: ../index.php");
        $connection->close();
        $deleteQuery->close();
        exit;
    }
    else {
        $_SESSION['logoutFailed'] = true;
        $_SESSION['logoutMessage'] = "Logout failed, could not delete authentication token.";
    }
}
else {
    $connection->close();
}

