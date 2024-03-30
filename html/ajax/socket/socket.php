<?php
//error_reporting(E_ALL);

$host = "127.0.0.1";
$port = 5432;

if(!isset($_POST['message']))
	die("Nu am mesaj\n");

$message= $_POST["message"];

if (!($socket = socket_create(AF_INET, SOCK_STREAM, 0))) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    die("Eroare creare socket: [$errorcode] - $errormsg\n");
}

if (!socket_connect($socket, $host, $port)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    socket_close($sock);

    die("Eroare conectare [$errorcode] - $errormsg\n");
}

socket_write($socket, $message, strlen($message));

$result = socket_read($socket, 1024);

if (!$result) {
    die("Could not read server response\n");
}

socket_close($sock);

if ($result=="OK") echo '1';
	else echo '0';
