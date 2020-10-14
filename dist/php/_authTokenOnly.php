<?php
require __DIR__.'/../php/_connect.php';
require __DIR__.'/../php/_utilities.php';

$cookieName = "UserAuthToken";
$userID;
$verifiedEmail;
$validLogon = false;

if (isset($_COOKIE[$cookieName])) {
    $authToken = $_COOKIE[$cookieName];

    $userQuery = $connection->prepare("SELECT iduser, token FROM t_persist WHERE token = ?");

    $userQuery->bind_param("s", $authToken);
    $userQuery->execute();

    $userQuery->bind_result($userID, $token);
    $userQuery->store_result();

    if ($userQuery->num_rows > 0) {
        while ($userQuery->fetch()) {
            if ($token === $authToken) {
                $validLogon = true;

                $query = "SELECT id, email FROM t_users WHERE id = {$userID}";
                $userQueryResults = $connection->query($query);

                $row = $userQueryResults->fetch_assoc();

                $verifiedEmail = $row['email'];
            }
        }
    }

    $userQuery->close();
    $connection->close();
}
