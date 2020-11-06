<?php
require __DIR__ . '/../classes/_connect.php';
require __DIR__ . '/../account/_auth.php';
require __DIR__ . '/../classes/_todoManager.php';

if (!$account->getAuthenticated()) {
    echo json_encode(['success' => 0, 'message' => 'You did not provide valid credentials.']);
    die;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!isset($_POST['id']) || !isset($_POST['status'])) {
        echo json_encode(['success' => 0, 'message' => 'Something went wrong, this request could not be processed']);
        die;
    }

    $todoId = $_POST['id'];
    $status = boolval($_POST['status']); 
    $todoManager = new TodoManager();

    try {
        $newStatus = $todoManager->toggleTodoComplete($todoId, $account->getId(), $status);
        echo json_encode(['success' => 1, 'message' => 'Todo status updated.', 'status' => intval($newStatus)]);
        die;
    } catch (Exception $ex) {
        echo json_encode(['success' => 0,'message' => $ex->getMessage()]);
        die;
    }
}