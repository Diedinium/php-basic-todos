<?php
require __DIR__.'/_authTokenOnly.php';
require __DIR__.'/_connect.php';

if (!$validLogon) {
    session_start();
    $_SESSION['loginMessage'] = "You did not provide valid login details.";
    header("Location: ../index.php");
    $connection->close();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $todoGroupHeader = $_POST['todoGroupHeader'];
    $todoGroupUserID = $_POST['userID'];

    if (!empty(trim($todoGroupHeader)) && !empty($todoGroupUserID)) {
        if ($_POST['userID'] == $userID) {

            $insertQuery = $connection->prepare("INSERT INTO t_todogroup (iduser, header) VALUES(?,?)");
            $insertQuery->bind_param("is", $todoGroupUserID, $todoGroupHeader);

            $result == $insertQuery->execute();
        }
    }
}
else {
    header("Location: ../index.php");
    exit;
}

header("Location: ../pages/todos.php");
exit;
