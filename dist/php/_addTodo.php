<?php
require __DIR__ . '/_authTokenOnly.php';
require __DIR__ . '/_connect.php';

if (!$validLogon) {
    session_start();
    $_SESSION['loginMessage'] = "You did not provide valid login details.";
    header("Location: ../index.php");
    $connection->close();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    if (!isset($_POST['addTodoHeader']) || !isset($_POST['todoGroupID'])) {
        header("Location: {$_SERVER['HTTP_REFERER']}");
        $_SESSION['todoAddError'] = "Unable to add todo. Header must be set.";
        exit;
    } else {
        $query = "";
        $todoGroupID = $_POST['todoGroupID'];

        $todoGroupQuery = $connection->prepare("SELECT iduser FROM t_todogroup WHERE id = ?");

        $todoGroupQuery->bind_param("i", $todoGroupID);
        $todoGroupQuery->execute();

        $todoGroupQuery->bind_result($todoGroupuserID);
        $todoGroupQuery->store_result();
        $todoGroupQuery->fetch();

        if ($todoGroupuserID !== $userID) {
            header("Location: {$_SERVER['HTTP_REFERER']}");
            $_SESSION['todoAddError'] = "Unable to add todo.";
            exit;
        } else {
            $insertTodoQuery;
            $todoHeader = $_POST['addTodoHeader'];
            $todoDescription = $_POST['addTodoDescription'];
            $todoDate = $_POST['addTodoDate'];
            $todoTime = $_POST['addTodoTime'];

            if (empty($todoDate)) {
                $query = "INSERT INTO t_todos (idtodogroup, header, description, dueDate) VALUES (?, ?, ?, NULL)";
                $insertTodoQuery = $connection->prepare($query);

                $insertTodoQuery->bind_param("iss", $todoGroupID, $todoHeader, $todoDescription);
                $result = $insertTodoQuery->execute();

                if ($result) {
                    header("Location: {$_SERVER['HTTP_REFERER']}");
                    exit;
                }
                else {
                    header("Location: {$_SERVER['HTTP_REFERER']}");
                    $_SESSION['todoAddError'] = "Unable to add todo.";
                    exit;
                }
            } else {
                $query = "INSERT INTO t_todos (idtodogroup, header, description, dueDate) VALUES (?, ?, ?, ?)";
                $insertTodoQuery = $connection->prepare($query);

                if (!empty($todoDate) && !empty($todoTime)) {
                    $combinedTime = $todoDate . " " . $todoTime;
                    $insertTodoQuery->bind_param("isss", $todoGroupID, $todoHeader, $todoDescription, $combinedTime);
                    $result = $insertTodoQuery->execute();

                    if ($result) {
                        header("Location: {$_SERVER['HTTP_REFERER']}");
                        exit;
                    }
                    else {
                        header("Location: {$_SERVER['HTTP_REFERER']}");
                        $_SESSION['todoAddError'] = "Unable to add todo.";
                        exit;
                    }
                } else {
                    header("Location: {$_SERVER['HTTP_REFERER']}");
                    $_SESSION['todoAddError'] = "Unable to add todo. When adding due date, date and time must have a value";
                    exit;
                }
            }
        }
    }
} else {
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;
}
