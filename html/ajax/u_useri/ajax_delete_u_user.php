<?php

require_once( './../../includes/load.php');

if (isset($_POST['id']) ) {
    global $db;
    $id = (int)$_POST['id'];

    //sterg
    $sql="DELETE FROM Useri WHERE id=$id LIMIT 1";
    $result = $db->query($sql);

    //verific
    $sql= "SELECT count(*) AS Nr FROM Useri  WHERE id=$id";
    $result = $db->query($sql);
    $data=$db->fetch_assoc($result);

    if ($data['Nr']=='0') echo '1';
        else echo '0';
} else echo '0';
