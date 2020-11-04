<?php

class TodoManager
{
    public function deleteTodoGroup(int $idTodoGroup, int $idUser) {
        global $connection;

        $userMatches = $this->TodoGroupUserIdMatches($idTodoGroup, $idUser);

        if (!$userMatches) {
            throw new Exception("Trying to be sneeky and delete todo groups that are not yours, huh?");
        }

        $deleteTodoGroup = $connection->prepare("DELETE FROM t_todogroup WHERE id = ?");
        $deleteTodoGroup->bind_param("i", $idTodoGroup);
        $deleteTodoGroup->execute();

        if (!empty($deleteTodoGroup->error)) {
            throw new Exception("Failed to delete todo group");
        }
    }

    public function deleteTodo(int $idTodo, int $idUser) {
        global $connection;

        $userMatches = $this->TodoUserIdMatches($idTodo, $idUser);

        if (!$userMatches) {
            throw new Exception("Trying to be sneeky and delete a todo that is not yours, huh?");
        }

        $deleteTodo = $connection->prepare("DELETE FROM t_todos WHERE id = ?");
        $deleteTodo->bind_param("i", $idTodo);
        $deleteTodo->execute();

        if (!empty($deleteTodo->error)) {
            throw new Exception("Failed to delete todo group");
        }
    }

    private function TodoGroupUserIdMatches(int $idTodoGroup, int $idUser):bool
    {
        global $connection;

        $findUserIdQuery = $connection->prepare("SELECT iduser FROM t_todogroup WHERE id = ?");
        $findUserIdQuery->bind_param("i", $idTodoGroup);
        $findUserIdQuery->execute();

        $findUserIdQuery->bind_result($foundUserId);
        $findUserIdQuery->store_result();

        if ($findUserIdQuery->num_rows < 1) {
            throw new Exception("Todogroup not found");
        }

        if (!empty($findUserIdQuery->error)) {
            throw new Exception("Failed to find todo group");
        }

        $findUserIdQuery->fetch();
        return $idUser == $foundUserId;
    }

    private function TodoUserIdMatches(int $idTodo, int $idUser):bool {
        global $connection;

        $todosQuery = $connection->prepare("SELECT idtodogroup FROM t_todos WHERE id = ?");
        $todosQuery->bind_param("i", $idTodo);
        $todosQuery->execute();
        $todosQuery->bind_result($idTodoGroup);
        $todosQuery->store_result();
        
        if ($todosQuery->num_rows < 1) {
            throw new Exception("No todo found.");
        }
        $todosQuery->fetch();
        
        $todoGroupResult = $connection->query("SELECT iduser FROM t_todogroup WHERE id = $idTodoGroup");

        if ($todoGroupResult->num_rows < 1) {
            throw new Exception("Error while locating todo group this todo is contained within");
        }

        $row = $todoGroupResult->fetch_assoc();
        $foundUserId = $row['iduser'];

        return $idUser == $foundUserId;
    }
}
