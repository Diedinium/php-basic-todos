<?php

// $headers = 'From: webmaster@localhost.com' . "\r\n" .
//     'Reply-To: webmaster@localhost.com' . "\r\n" .
//     'X-Mailer: PHP/' . phpversion();

// mail("jakethomashall@gmail.com", "Test email", "Test message", $headers);

require __DIR__ . '/../php/_connect.php';
require __DIR__.'/../php/_account.php';

$account = new Account();

try {
    $account->addAccount('testemail4@test.com', 'somepassword', 'Jake', 'Hall');
}
catch (Exception $e) {
    echo $e->getMessage();
    die();
}

echo 'New account id is: '.$account->getId().' with email: '.$account->getEmail().' and is authenticated? '.$account->getAuthenticated();