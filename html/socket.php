<?php
//error_reporting(E_ALL);

$host = "127.0.0.1";
$port = 5432;

if(!isset($_GET['message']))
	die("Nu am mesaj\n");

$message= $_GET["message"];

if (!($socket = socket_create(AF_INET, SOCK_STREAM, 0))) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    die("Eroare creare socket: [$errorcode] - $errormsg\n");
}

echo "Socket created\n";

if (!socket_connect($socket, $host, $port)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    die("Eroare conectare [$errorcode] - $errormsg\n");
}

echo "Connection established \n";

$message = "p:DelayPornireAlarma:33:OK:\n";


socket_write($socket, $message, strlen($message));

$result = socket_read($socket, 1024);

if (!$result) {
    die("Could not read server response\n");
}

socket_close($sock);

echo $result;
