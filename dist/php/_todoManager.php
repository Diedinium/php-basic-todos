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
        return $idUser === $foundUserId;
    }
}
