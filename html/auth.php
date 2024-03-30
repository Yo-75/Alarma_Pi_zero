<?php include_once('includes/load.php'); ?>
<?php
$req_fields = array('username','password' );
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);

global $db;
$username = $db->escape($username);
$password = $db->escape($password);
$sql  = "SELECT id,username,password,activ FROM Useri WHERE username ='{$username}' LIMIT 1";

$result = $db->query($sql);
if($db->num_rows($result)) {
    $user = $db->fetch_assoc($result);

    if ($user['activ'] == '0') {
        {
            $session->msg("d", "Userul este inactiv. Va rog sa contactati administratorul site-ului.");
            redirect('index.php');
            die();
        }
    } else {
        $password_request = md5($password);
        if ($password_request === $user['password']) {
            //create session with id
            $session->login($user['id']);

            //Update Sign in time
            updateLastLogIn($user['id']);

            $session->msg("s", "Admin - Welcome to Volt Electro Lab.");
            redirect('home.php');

        } else {
            $session->msg("d", "Userul sau parola sunt incorecte.");
            redirect('index.php');
            die();
        }
    }
}
else {
    $session->msg("d", "User inexistent.");
    redirect('index.php');
    die();
}
?>
