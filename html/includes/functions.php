<?php
 $errors = array();

 /*--------------------------------------------------------------*/
 /* Function for Remove escapes special
 /* characters in a string for use in an SQL statement
 /*--------------------------------------------------------------*/
function real_escape($str){
  global $con;
  $escape = mysqli_real_escape_string($con,$str);
  return $escape;
}
/*--------------------------------------------------------------*/
/* Function for Remove html characters
/*--------------------------------------------------------------*/
function remove_junk($str){
  $str = nl2br($str);
  $str = htmlspecialchars(strip_tags($str, ENT_QUOTES));
  return $str;
}
/*--------------------------------------------------------------*/
/* Function for Uppercase first character
/*--------------------------------------------------------------*/
function first_character($str){
  $val = str_replace('-'," ",$str);
  $val = ucfirst($val);
  return $val;
}
/*--------------------------------------------------------------*/
/* Function for Checking input fields not empty
/*--------------------------------------------------------------*/
function validate_fields($var){
  global $errors;
  foreach ($var as $field) {
    $val = remove_junk($_POST[$field]);
    if(isset($val) && $val==''){
      $errors = $field ." can't be blank.";
      return $errors;
    }
  }
  return '';
}

/*--------------------------------------------------------------*/
/* Function for redirect
/*--------------------------------------------------------------*/
function redirect($url, $permanent = false)
{
    if (headers_sent() === false)
    {
      header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    }
    else{
        echo '<script> window.location.href = "' . $url . '"; </script>';
    }

    exit();
}

/*--------------------------------------------------------------*/
/* Function for Readable date time
/*--------------------------------------------------------------*/
function read_date($str){
    if($str){
        $date = date('j.m.Y - G:i:s', strtotime($str));
        if ($date === '1.01.1970 - 1:00:00')
            return '-';
        else
            return $date;
    }
    else
        return null;
}
/*--------------------------------------------------------------*/
/* Function for  Readable Make date time
/*--------------------------------------------------------------*/
function make_date(){
  return strftime("%Y-%m-%d %H:%M:%S", time());
}
/*--------------------------------------------------------------*/
/* Function for  Readable date time
/*--------------------------------------------------------------*/
function count_id(){
  static $count = 1;
  return $count++;
}
function count_id2(){
    static $count2 = 1;
    return $count2++;
}
function count_id3(){
    static $count3 = 1;
    return $count3++;
}
function count_id4(){
    static $count4 = 1;
    return $count4++;
}
function count_id5(){
    static $count5 = 1;
    return $count5++;
}
function count_id6(){
    static $count6 = 1;
    return $count6++;
}
/*--------------------------------------------------------------*/
/* Function for Creating random string
/*--------------------------------------------------------------*/
function randString($length = 5)
{
  $str='';
  $cha = "0123456789abcdefghijklmnopqrstuvwxyz";

  for($x=0; $x<$length; $x++)
   $str .= $cha[mt_rand(0,strlen($cha)-1)];
  return $str;
}

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        return;
    }

    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}


function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz' ;//ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function display_msg($msg =''){
    $output = array();
    if(!empty($msg)) {
        /** @noinspection PhpWrongForeachArgumentTypeInspection */
        foreach ($msg as $key => $value) {
            $output  = "<div class=\"alert alert-{$key}\">";
            $output .= "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
            $output .= remove_junk(first_character($value));
            $output .= "</div>";
        }
        return $output;
    } else {
        return "" ;
    }
}

function read_date_format($str){
    if($str){
        $date = date('j.m.Y - G:i:s', strtotime($str));
        $date_start =  '2016-1-1 - 1:0:0';
        if ($date < $date_start)
            return '-';
        else
            return $date;
    }
    else
        return null;
}

function sitelog($message) {

    $file_name = $_SERVER['DOCUMENT_ROOT'] . '/Volt/logs/log.txt';

    if (!file_exists ($file_name)){
        $fp = fopen($file_name,"wb");
        fwrite($fp,'Log Starting date : ' . date("Y-m-d H:i:s") . "\r\n");
        fclose($fp);
    }

// Write the contents to the file,
// using the FILE_APPEND flag to append the content to the end of the file
// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
    file_put_contents($file_name, $message . "\r\n", FILE_APPEND | LOCK_EX);
}

function ShowAccessMessage() {
    ?>
        <div style="text-align: center">
            <img src="images/access-interzis.png">
        </div>
    <?php
    include_once('layouts/footer.php');
    die();
}

function ShowErrorMessage() {
    ?>
    <div style="text-align: center">
        <img src="images/eroare.png">
    </div>
    <?php
    include_once('layouts/footer.php');
    die();
}

