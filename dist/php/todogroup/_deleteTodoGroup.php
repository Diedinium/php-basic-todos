<?php
require __DIR__ . '/../classes/_connect.php';
require __DIR__ . '/../account/_auth.php';
require __DIR__ . '/../classes/_todoManager.php';

if (!$account->getAuthenticated()) {
    echo json_encode(['success' => 0, 'message' => 'You did not provide valid credentials.']);
    die;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!isset($_POST['id'])) {
        echo json_encode(['success' => 0, 'message' => 'Something went wrong, this request could not be processed']);
        die;
    }

    $todoGroupId = $_POST['id'];
    $todoManager = new TodoManager();

    try {
        $todoManager->deleteTodoGroup($todoGroupId, $account->getId());
        echo json_encode(['success' => 1, 'message' => 'Todo group succesfully deleted.']);
        die;
    } catch (Exception $ex) {
        echo json_encode(['success' => 0,'message' => $ex->getMessage()]);
        die;
    }
}
