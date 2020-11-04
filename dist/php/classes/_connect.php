<?php

$dbPassword = "MEe566JIR6MLTkU9";
$dbUserName = "PHPdatabase";
$dbServer = "localhost";
$dbName = "todos";

$connection = new mysqli($dbServer, $dbUserName, $dbPassword, $dbName);

if ($connection->connect_errno) {
    exit("Database connection failed. Reason:".$connection->connect_error);
}