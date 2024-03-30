 <?php
/*
 * Folosita la adaugarea unui nou user
 * Date in : POST['username'] - denumirea noii categorii
 *           POST['password']
 *           POST['group']
 * Return: Id-ul userului creat sau 0 in caz eroare
 */

require_once( './../../includes/load.php');
global $db;

if(!empty($_POST['username']) && $_POST['nume']) {

    $username   = remove_junk($db->escape($_POST['username']));
    $name   = remove_junk($db->escape($_POST['nume']));

    $password = md5('initial');

    //verific sa nu existe
    $sql= "SELECT count(*) AS Nr FROM Useri  WHERE username='{$username}'";
    $result = $db->query($sql);
    $data=$db->fetch_assoc($result);

    if ($data['Nr']=='0') {

        $query = "INSERT INTO Useri (username, nume, password, activ, image) 
                  VALUES ( '{$username}', '{$name}', '{$password}', 1,'user.png')";
        $result = $db->query($query);

        $query = "SELECT id FROM  Useri WHERE username = '{$username}' AND password='{$password}' AND name='{$name}'";
        $result = $db->query($query);
        $data = $db->fetch_assoc($result);

        echo $data['id'];

    } else echo '0';
} else  echo '0';
