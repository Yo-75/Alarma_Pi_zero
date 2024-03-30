<?php

/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table){
    global $db;
    $table_exit = $db->query('SHOW TABLES FROM '.DB_NAME.' LIKE "'.$db->escape($table).'"');
    if($table_exit) {
        if($db->num_rows($table_exit) > 0)
            return true;
        else
            return false;
    }
    return false;
}

/*--------------------------------------------------------------*/
/* Determin daca userul are sau nu drepturi de admin
/*--------------------------------------------------------------*/
// ok 17.10
function IsAdmin($userid) {
    global $db;
    $username = $db->escape($userid);

    $sql  = "SELECT Is_admin FROM Useri WHERE id =$username LIMIT 1";
    $result = $db->query($sql);
    if($db->num_rows($result)){
        $user = $db->fetch_assoc($result);
        return $user['Is_admin'];
    }
    return false;
}


/*--------------------------------------------------------------*/
/* Find current log in user by session id
/*--------------------------------------------------------------*/
// ok 17.10
function GetCurrentUser(){
    static $current_user;
    global $db;
    if(!$current_user){
        if(isset($_SESSION['user_id'])):
            $user_id = intval($_SESSION['user_id']);

            $sql="SELECT * FROM Useri WHERE id= $user_id";
            $result = $db->query($sql);
            if($db->num_rows($result)){
                $user = $db->fetch_assoc($result);
                return $user;
            }
        endif;
    }
    return $current_user;
}

//try to get visitor IP
function getUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

/*--------------------------------------------------------------*/
/* Function to update the last log in of a user
/*--------------------------------------------------------------*/
// ok 17.10
function updateLastLogIn($user_id)
{
    global $db;

    $userIP=getUserIP();

    $sql = "UPDATE Useri SET last_login=NOW() WHERE id =$user_id";
    $db->query($sql);

    $sql = "INSERT INTO Logins (user_id,data,IP) VALUES ($user_id, NOW(),'$userIP')";
    $db->query($sql);

}

