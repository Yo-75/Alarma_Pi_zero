<?php

require_once( './../../includes/load.php');
global $db;

if(isset($_POST["username"]) && isset($_POST['id'])) {
    $username=remove_junk($db->escape(trim($_POST["username"])));
    $id=(int) $_POST['id'];

    //verific sa nu existe username
    $sql= "SELECT count(*) AS Nr FROM Useri  WHERE username='$username'";
    $result = $db->query($sql);
    $data=$db->fetch_assoc($result);

    if ($data['Nr']=='0') {

        $sql = "UPDATE Useri SET username='$username' WHERE id=$id";
        $result = $db->query($sql);

        $sql = "SELECT count(*) AS Nr FROM Useri  WHERE username='$username' AND id=$id";
        $result = $db->query($sql);
        $data = $db->fetch_assoc($result);

        echo $data['Nr'];
    } else echo '0';
} else echo '0';


