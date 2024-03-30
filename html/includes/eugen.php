<?php
// Load Composer's autoloader
require 'vendor/autoload.php';

function GetUserName($id_user)
{
    global $db;
    $sql  = "SELECT nume FROM  Useri WHERE id=$id_user";

    $result = $db->query($sql);
    $data=$db->fetch_assoc($result);
    return ucfirst(trim($data['name']));
}

function GetSetariValue($parametru)
{
    global $db;
    $sql  = "SELECT value FROM setari WHERE parametru='$parametru'";
    $result = $db->query($sql);
    $data=$db->fetch_assoc($result);
    return ucfirst(trim($data['value']));
}


function GetSetariDescriere($parametru)
{
    global $db;
    $sql  = "SELECT descriere FROM setari WHERE parametru='$parametru'";
    $result = $db->query($sql);
    $data=$db->fetch_assoc($result);
    return ucfirst(trim($data['descriere']));
}


function GetUserAvatar($id_user){
    global $db;
    $sql  = "SELECT image FROM Useri WHERE id=$id_user";
    $result = $db->query($sql);
    $data=$db->fetch_assoc($result);
    return $data['image'];
}

function GetAllUsers(){

    global $db;

    $sql = "SELECT nume,username,activ,id,last_login  FROM Useri WHERE username <> 'admin' ORDER BY nume, username ASC";    $result = $db->query($sql);
    $result_set = $db->while_loop($result);
    return $result_set;
}

function GetUserDetails($id_user){
    global $db;

    $sql = "SELECT * FROM Useri WHERE id =$id_user";
    $result = $db->query($sql);
    $data=$db->fetch_assoc($result);
    return $data;
}

//SIGLA
function GetSigla() {
?>
    <div class="panel">
        <div class="titlu-div">
            <div><img class="sigla" style="max-height:50px" src= "images/Sigla.png"></div>
            <div  class="titlu">Pi Alarm System</div>
        </div>
    </div>
    <?php
}


//SEND MAIL

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function SendMail($Message) {

try {
   $mail=new PHPMailer(true);

//   $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
   $mail->CharSet = 'UTF-8';
   $mail->IsSMTP();
   $mail->Host       = 'smtp.gmail.com';

//   $mail->SMTPSecure = 'tls';
   $mail->Port       = 587;
   $mail->SMTPAuth   = true;
   $mail->Username   = 'alarmadeacasa1';
   $mail->Password   = '1ntrax0m';

   $mail->SetFrom('alarmadeacasa1@gmail.com', 'Alarma');
   $mail->AddReplyTo('no-reply@mycomp.com','no-reply');
   $mail->Subject    = 'Alarma - Eveniment';
   $mail->MsgHTML($Message);

   $mail->AddAddress('eugens75@gmail.com', 'title1');
   //$mail->AddAddress('abc2@gmail.com', 'title2'); /* ... */

   $mail->send();
   echo "all OK"; 	   
   return 1;
   }
catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    echo "Eroare";
    return 0;
    }
}
