<?php

require_once( './../../includes/load.php');
global $db;

if (isset($_POST['id']) &&  (isset($_POST['field']) || isset($_POST['checked']) )) {

    $id = (int) $_POST['id'];
    $field = remove_junk($_POST['field']);
    $checked = $_POST['checked'];

    $sql = "UPDATE Useri SET $field=$checked WHERE id=$id";
    $result = $db->query($sql);

    $sql = "SELECT $field FROM Useri WHERE id=$id";

    $result = $db->query($sql);
    $data=$db->fetch_assoc($result);

    echo $data[$field];
}
else echo '-1';


