<?php
require_once(LIB_PATH_INC . DS . "config.php");

class MySqli_DB {

    private $con;
    public $query_id;

    function __construct() {
      $this->db_connect();
    }

/*--------------------------------------------------------------*/
/* Function for Open database connection
/*--------------------------------------------------------------*/
public function db_connect()
{
  $this->con = mysqli_connect(DB_HOST,DB_USER,DB_PASS);
  if(!$this->con)
         {
           die(" Database connection failed:". mysqli_connect_error());
         } else {
           $select_db = $this->con->select_db(DB_NAME);
             if(!$select_db)
             {
               die("Failed to Select Database". mysqli_connect_error());
             }
         }
    mysqli_set_charset( $this->con, 'utf8');
}
/*--------------------------------------------------------------*/
/* Function for Close database connection
/*--------------------------------------------------------------*/

public function db_disconnect()
{
  if(isset($this->con))
  {
    mysqli_close($this->con);
    unset($this->con);
  }
}
/*--------------------------------------------------------------*/
/* Function for mysqli query
/*--------------------------------------------------------------*/
public function query($sql)
   {

      if (trim($sql != "")) {
          /** @noinspection PhpUndefinedMethodInspection */
          $this->query_id = $this->con->query($sql);
      }
      if (!$this->query_id)
        // only for Developer mode


             die("Error on this Query :<pre> " . $sql ."</pre>");
       // For production mode
        //  die("Error on Query");

       return $this->query_id;

   }

/*--------------------------------------------------------------*/
/* Function for Query Helper
/*--------------------------------------------------------------*/
public function fetch_array($statement)
{
  return mysqli_fetch_array($statement);
}
public function fetch_object($statement)
{
  return mysqli_fetch_object($statement);
}
public function fetch_assoc($statement)
{
  return mysqli_fetch_assoc($statement);
}
public function num_rows($statement)
{
  return mysqli_num_rows($statement);
}
public function insert_id()
{
  return mysqli_insert_id($this->con);
}
public function affected_rows()
{
  return mysqli_affected_rows($this->con);
}
/*--------------------------------------------------------------*/
 /* Function for Remove escapes special
 /* characters in a string for use in an SQL statement
 /*--------------------------------------------------------------*/
 public function escape($str){
     /** @noinspection PhpUndefinedMethodInspection */
     return $this->con->real_escape_string($str);
 }
/*--------------------------------------------------------------*/
/* Function for while loop
/*--------------------------------------------------------------*/
public function while_loop($loop){
   $results = array();
   while ($result = $this->fetch_array($loop)) {
      $results[] = $result;
   }
 return $results;
}

public function beginTransaction() {
    mysqli_begin_transaction($this->con);
}

public function commit() {
    mysqli_commit($this->con);
}

public function rollback() {
    mysqli_rollback($this->con);
}

}

$db = new MySqli_DB();

