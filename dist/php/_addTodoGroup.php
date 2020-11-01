<?php
require __DIR__ . '/_connect.php';
require __DIR__ . '/_auth.php';

if (!$account->getAuthenticated()) {
    dieWithError("You did not provide valid login details.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $todoGroupHeader = $_POST['todoGroupHeader'];

    if (!empty(trim($todoGroupHeader))) {
        $insertQuery = $connection->prepare("INSERT INTO t_todogroup (iduser, header) VALUES(?,?)");
        $insertQuery->bind_param("is", $account->getId(), $todoGroupHeader);

        $result == $insertQuery->execute();
    }
} else {
    header("Location: ../index.php");
    die;
}

header("Location: ../pages/todos.php");
die;
