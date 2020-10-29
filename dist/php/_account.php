<?php

class Account
{

    private $id;
    private $email;
    private $isAuthenticated;

    public function __construct()
    {
        $this->id = null;
        $this->email = null;
        $this->isAuthenticated = false;
    }

    public function __destruct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getAuthenticated()
    {
        return $this->isAuthenticated;
    }

    public function addAccount(string $email, string $password, string $firstName, string $lastName)
    {
        global $connection;

        $name = trim($email);
        $password = trim($password);

        if (!is_null($this->getIdFromName($email))) {
            throw new Exception("User name is already taken.");
        }

        if (is_null($firstName) || is_null($lastName)) {
            throw new Exception("First name and last name must be provided.");
        }

        $addAccountQuery = $connection->prepare("INSERT INTO t_users (email, password, firstName, lastName) VALUES (?, ?, ?, ?)");
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $addAccountQuery->bind_param("ssss", $email, $hash, $firstName, $lastName);
        $addAccountQuery->execute();
        $addAccountQuery->store_result();

        if (!empty($addAccountQuery->error)) {
            throw new Exception("Failed to add user");
        }

        $getAccountQuery = $connection->query("SELECT id, email FROM t_users WHERE id = $addAccountQuery->insert_id");
        while ($row = $getAccountQuery->fetch_assoc()) {
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->isAuthenticated = true;
        }
    }

    public function getIdFromName(string $email)
    {
        global $connection;
        $userQuery = $connection->prepare("SELECT id FROM t_users WHERE email = ?");

        $userQuery->bind_param("s", $email);
        $userQuery->execute();

        $userQuery->bind_result($userID);
        $userQuery->store_result();

        return $userQuery->num_rows() > 0 ? $userID : null;
    }
}
