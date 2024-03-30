<?php
require_once( './../../includes/load.php');
global $db;

if(!empty($_POST["username"]) ) {
    $username=remove_junk($db->escape(trim($_POST["username"])));

    $sql= "SELECT count(*) AS Nr FROM Useri  WHERE username='{$username}'";
    $result = $db->query($sql);
    $data=$db->fetch_assoc($result);

    echo $data['Nr'];
}
else echo '-1';


