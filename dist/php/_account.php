<?php

class Account
{

    private $id;
    private $email;
    private $password;
    private $isAuthenticated;
    private $firstName;
    private $lastName;
    private $dateCreated;

    public function __construct()
    {
        $this->id = null;
        $this->email = null;
        $this->password = null;
        $this->isAuthenticated = false;
        $this->firstName = null;
        $this->lastName = null;
        $this->dateCreated = null;
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

    public function getPassword()
    {
        return $this->password;
    }

    public function getAuthenticated()
    {
        return $this->isAuthenticated;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getFullName()
    {
        return $this->firstName . " " . $this->lastName;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function addAccount(string $email, string $password, string $firstName, string $lastName)
    {
        global $connection;

        if (session_status() == PHP_SESSION_ACTIVE) {
            $sessionId = session_id();
            $email = trim($email);
            $password = trim($password);

            if (!is_null($this->getIdFromName($email))) {
                throw new Exception("User name is already taken.");
            }

            $addAccountQuery = $connection->prepare("INSERT INTO t_users (email, password, firstName, lastName) VALUES (?, ?, ?, ?)");
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $addAccountQuery->bind_param("ssss", $email, $hash, $firstName, $lastName);
            $addAccountQuery->execute();

            if (!empty($addAccountQuery->error)) {
                throw new Exception("Failed to add user.");
            }

            $getAccountQuery = $connection->query("SELECT id, email, password, firstName, lastName, dateCreated FROM t_users WHERE id = $addAccountQuery->insert_id");
            while ($row = $getAccountQuery->fetch_assoc()) {
                $this->id = $row['id'];
                $this->email = $row['email'];
                $this->password = $row['password'];
                $this->isAuthenticated = true;
                $this->firstName = $row['firstName'];
                $this->lastName = $row['lastName'];
                $this->dateCreated = $row['dateCreated'];
            }

            $connection->query("INSERT INTO t_persist (iduser, token) VALUES ($this->id, '$sessionId')");
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
        $userQuery->fetch();

        return $userQuery->num_rows() > 0 ? $userID : null;
    }

    public function login(string $email = '', string $password = '')
    {
        global $connection;

        if (session_status() == PHP_SESSION_ACTIVE) {
            $sessionId = session_id();
            $tokenQuery = $connection->prepare("SELECT iduser FROM t_persist WHERE token = ?");

            $tokenQuery->bind_param("s", $sessionId);
            $tokenQuery->execute();

            $tokenQuery->bind_result($userID);
            $tokenQuery->store_result();

            if ($tokenQuery->num_rows > 0) {
                $tokenQuery->fetch();

                $userQueryResults = $connection->query("SELECT id, email, password, firstName, lastName, dateCreated FROM t_users WHERE id = {$userID}");
                $row = $userQueryResults->fetch_assoc();

                $this->id = $row['id'];
                $this->email = $row['email'];
                $this->password = $row['password'];
                $this->isAuthenticated = true;
                $this->firstName = $row['firstName'];
                $this->lastName = $row['lastName'];
                $this->dateCreated = $row['dateCreated'];
                return;
            }

            if (!empty($email) && !empty($password)) {
                $userQuery = $connection->prepare("SELECT id, email, password, firstName, lastName, dateCreated FROM t_users WHERE email = ?");

                $userQuery->bind_param("s", $email);
                $userQuery->execute();

                $userQuery->bind_result($resultId, $resultEmail, $resultPassword, $resultFirstName, $resultLastName, $resultDateCreated);
                $userQuery->store_result();

                if ($userQuery->num_rows > 0) {
                    $row = $userQuery->fetch();

                    if (password_verify($password, $resultPassword)) {
                        $this->id = $resultId;
                        $this->email = $resultEmail;
                        $this->password = $resultPassword;
                        $this->isAuthenticated = true;
                        $this->firstName = $resultFirstName;
                        $this->lastName = $resultLastName;
                        $this->dateCreated = $resultDateCreated;

                        $connection->query("INSERT INTO t_persist (iduser, token) VALUES ($this->id, '$sessionId')");
                        return;
                    } else {
                        throw new Exception('Password was incorrect');
                    }
                } else {
                    throw new Exception('User not found.');
                }
            }
        }
    }

    public function logout()
    {
        global $connection;

        if (session_status() == PHP_SESSION_ACTIVE) {
            $sessionId = session_id();
            $delete = $connection->query("DELETE FROM t_persist WHERE token = '$sessionId'");

            if (!$delete) {
                throw new Exception("Logout failed");
            }

            return;
        }

        return;
    }

    public function updateNames(string $firstName, string $lastName)
    {
        global $connection;

        $updateNames = $connection->prepare("UPDATE t_users SET firstName=?, lastName=? WHERE id = {$this->id}");
        $updateNames->bind_param("ss", $firstName, $lastName);
        $success = $updateNames->execute();

        if (!$success) {
            throw new Exception("Updating names failed.");
        }
    }

    public function changePassword($newPassword)
    {
        global $connection;

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updatePasswordQuery = $connection->prepare("UPDATE t_users SET password=? WHERE id = {$this->id}");
        $updatePasswordQuery->bind_param("s", $passwordHash);
        $success = $updatePasswordQuery->execute();

        if (!$success) {
            throw new Exception("Updating password failed.");
        }
    }

    public function deleteAllTodos()
    {
        global $connection;

        if (is_null($this->id)) {
            throw new Exception("Cannot delete, no user id.");
        }

        $success = $connection->query("DELETE FROM t_todogroup WHERE iduser = $this->id");

        if (!$success) {
            throw new Exception("Deleting todos failed.");
        }
    }

    public function deleteAccount()
    {
        global $connection;

        if (is_null($this->id)) {
            throw new Exception("Cannot delete, no user id.");
        }

        $success = $connection->query("DELETE FROM t_users WHERE id = $this->id");

        if (!$success) {
            throw new Exception("Deleting account failed.");
        }
    }
}
