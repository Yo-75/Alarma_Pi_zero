<?php

require_once( './../../includes/load.php');
global $db;

if(isset($_POST["user"]) && isset($_POST['id'])) {
    $user=remove_junk($db->escape(trim($_POST["user"])));
    $id=(int) $_POST['id'];

    $sql= "UPDATE Useri SET nUme='$user'  WHERE id=$id";
    $result = $db->query($sql);

    $sql = "SELECT count(*) AS Nr FROM Useri  WHERE nume='$user' AND id=$id";
    $result = $db->query($sql);
    $data = $db->fetch_assoc($result);

    echo $data['Nr'];
}
else echo '0';


