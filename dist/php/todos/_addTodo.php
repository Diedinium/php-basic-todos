<?php
require __DIR__ . '/../classes/_connect.php';
require __DIR__ . '/../account/_auth.php';

if (!$account->getAuthenticated()) {
    dieWithError("You did not provide valid login details.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['addTodoHeader']) || !isset($_POST['todoGroupID'])) {
        dieWithError("Unable to add todo. Header must be set.", "pages/todos.php");
    } else {
        $todoGroupID = $_POST['todoGroupID'];

        $todoGroupQuery = $connection->prepare("SELECT iduser FROM t_todogroup WHERE id = ?");

        $todoGroupQuery->bind_param("i", $todoGroupID);
        $todoGroupQuery->execute();

        $todoGroupQuery->bind_result($todoGroupuserID);
        $todoGroupQuery->store_result();
        $todoGroupQuery->fetch();

        if ($todoGroupuserID != $account->getId()) {
            dieWithError("Unable to add todo", "pages/todos.php");
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
                    $_SESSION['errorMessage'] = "Unable to add todo.";
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
                        $_SESSION['errorMessage'] = "Unable to add todo.";
                        exit;
                    }
                } else {
                    header("Location: {$_SERVER['HTTP_REFERER']}");
                    $_SESSION['errorMessage'] = "Unable to add todo. When adding due date, date and time must have a value";
                    exit;
                }
            }
        }
    }
} else {
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;
}
