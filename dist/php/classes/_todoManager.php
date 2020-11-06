<?php

class TodoManager
{
    public function deleteTodoGroup(int $idTodoGroup, int $idUser) {
        global $connection;

        $deleteTodoGroup = $connection->prepare("DELETE FROM t_todogroup WHERE id = ? AND iduser = ?");
        $deleteTodoGroup->bind_param("ii", $idTodoGroup, $idUser);
        $deleteTodoGroup->execute();

        if (!empty($deleteTodoGroup->error)) {
            throw new Exception("Failed to delete todo group");
        }
    }

    public function deleteTodo(int $idTodo, int $idUser) {
        global $connection;

        $deleteTodo = $connection->prepare("DELETE t_todos FROM t_todos LEFT JOIN t_todogroup ON t_todos.idtodogroup = t_todogroup.id WHERE t_todos.id = ? AND t_todogroup.iduser = ?");
        $deleteTodo->bind_param("ii", $idTodo, $idUser);
        $deleteTodo->execute();

        if (!empty($deleteTodo->error)) {
            throw new Exception("Failed to delete todo.");
        }
    }

    public function toggleTodoComplete(int $idTodo, int $idUser, bool $currentStatus):bool {
        global $connection;

        $newStatus = !$currentStatus;
        $updateStatus = $connection->prepare("UPDATE t_todos LEFT JOIN t_todogroup ON t_todos.idtodogroup = t_todogroup.id SET t_todos.complete = ? WHERE t_todos.id = ? AND t_todogroup.iduser = ?");
        $updateStatus->bind_param("iii", $newStatus, $idTodo, $idUser);
        $updateStatus->execute();

        if (!empty($updateStatus->error)) {
            throw new Exception("Failed to change todo status.");
        }

        return $newStatus;
    }
}
