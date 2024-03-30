<?php
require_once( './../../includes/load.php');
global $db;

if(isset($_POST["nume"]) && isset($_POST["value"])  ) {

    $nume = remove_junk($_POST["nume"]);
    $value = remove_junk($_POST["value"]);

    $query = "UPDATE setari SET value='$value' WHERE parametru='$nume'";
    $result = $db->query($query);

    $query = "SELECT COUNT(*) AS Nr FROM  setari WHERE value='$value' AND parametru='$nume'";
    $result = $db->query($query);
    $data = $db->fetch_assoc($result);

    echo $data['Nr'];
} else echo '0';
