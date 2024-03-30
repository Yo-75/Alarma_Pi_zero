<?php

require_once( './../../includes/load.php');
global $db;

if(isset($_POST["activ"]) && isset($_POST['id'])) {
    $activ =(int)$_POST["activ"];
    $id=(int) $_POST['id'];

    $sql= "UPDATE Useri SET activ=$activ  WHERE id=$id";
    $result = $db->query($sql);

    $sql = "SELECT count(*) AS Nr FROM e_u_useri  WHERE activ=$activ AND id=$id";
    $result = $db->query($sql);
    $data = $db->fetch_assoc($result);

    echo $data['Nr'];
}
else echo '0';


