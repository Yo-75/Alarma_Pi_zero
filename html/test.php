<?php

$name = $_GET['name'];

if ($name == null) {
    $name = 'guest';
}

$message = $_GET['message'];

if ($message == null) {
    $message = 'hello there';
}

echo "$name says: $message";
