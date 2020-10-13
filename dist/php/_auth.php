<?php
require __DIR__.'/../php/_connect.php';
require __DIR__.'/../php/_utilities.php';

$cookieName = "UserAuthToken";
$userID;
$verifiedEmail;
$validLogon = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['loginMessage'] = "You somehow did not provide a valid email address or password... having fun messing around with the HTML, hmm?";
        header("Location: ../index.php");
        $connection->close();
        exit;
    } else {
        $query = "SELECT id, email FROM t_users WHERE email = ? AND password = ?";
        $userQuery = $connection->prepare($query);

        $userQuery->bind_param("ss", $email, $password);
        $userQuery->execute();

        $userQuery->bind_result($userID, $verifyiedEmail);
        $userQuery->store_result();

        if ($userQuery->num_rows < 1) {;
            $_SESSION['loginMessage'] = "You did not provide valid login details.";
            header("Location: ../index.php");
            $connection->close();
            $userQuery->close();
            exit;
        } else {
            $userQuery->fetch();

            $GUID = getGUID();
            $cookieValue = "{$verifyiedEmail}:{$GUID}";

            $query = "INSERT INTO t_persist (iduser, token) VALUES ($userID, '$cookieValue')";
            $connection->query($query);

            setcookie($cookieName, $cookieValue, null, "/", null, false, true);

            header("Location: ../pages/todos.php");
            $connection->close();
            exit;
        }
    }
}
else {
    if (isset($_COOKIE[$cookieName])) {
        $authToken = $_COOKIE[$cookieName];
        $query = "SELECT iduser, token FROM t_persist WHERE token = ?";
    
        $userQuery = $connection->prepare($query);
    
        $userQuery->bind_param("s", $authToken);
        $userQuery->execute();
    
        $userQuery->bind_result($userID, $token);
        $userQuery->store_result();

        if ($userQuery->num_rows > 0) {
            while ($userQuery->fetch()) 
            {
                if ($token === $authToken) {
                    $validLogon = true;

                    $query = "SELECT id, email FROM t_users WHERE id = {$userID}";
                    $userQueryResults = $connection->query($query);

                    $row = $userQueryResults->fetch_assoc();

                    $verifiedEmail = $row['email'];
                }
            }
        }
    }
}
?>