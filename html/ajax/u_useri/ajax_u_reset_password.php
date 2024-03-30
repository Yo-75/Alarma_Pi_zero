<?php
require_once( './../../includes/load.php');
global $db;

if(!empty($_POST["id"]) ) {
    $id = (int)$_POST["id"];
    $pass = md5('initial');

    $sql = "UPDATE Useri SET password='$pass' WHERE id=$id";
    $result = $db->query($sql);

    $sql = "SELECT count(*) AS Nr FROM Useri  WHERE id=$id AND password='$pass'";
    $result = $db->query($sql);
    $data = $db->fetch_assoc($result);

    echo $data['Nr'] ;
} else echo '0';


