<?php
$page_title = 'Schimbare parola';
require_once('includes/load.php');
// Checking What level user has permission to view this page

if (!$session->isUserLoggedIn()){
    die("Trebuie sa fii logat");
}

$user = GetCurrentUser();

if(isset($_POST['update'])){

    $req_fields = array('new-password','old-password','id','check-password' );
    validate_fields($req_fields);

    if(empty($errors)){

        if(md5($_POST['old-password']) !== $user['password'] ){
            $session->msg('d', "Parola veche nu este corecta!");
            redirect('change_password.php',false);
        }


        $id = (int)$_POST['id'];
        $new = remove_junk($db->escape(md5($_POST['new-password'])));
        $check = remove_junk($db->escape(md5($_POST['check-password'])));
        if ($new !== $check){
            $session->msg('d', "Parola noua difera in cele doua campuri!");
            redirect('change_password.php',false);
        }


        $sql = "UPDATE Useri SET password ='{$new}' WHERE id='{$db->escape($id)}'";
        $result = $db->query($sql);
        if($result && $db->affected_rows() === 1):
            $session->logout();
            $session->msg('s',"Parola a fost schimbata. Va rugam sa va deconectati si logati cu noua parola.");
            redirect('index.php', false);
        else:
            $session->msg('d',' Nu se poate modifica parola!');
            redirect('change_password.php', false);
        endif;
    } else {
        $session->msg("d", $errors);
        redirect('change_password.php',false);
    }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="login-page" style="display:flex">
    <div  style="display:flex; align-items:center;justify-content: center;width:250px">
        <img src="images/ChangePassword.png" style="max-width: 150px;max-height: 150px;">
    </div>
    <div  style="width: 100%;">
        <div class="text-center">
            <h2><strong>Schimbare parola</strong></h2>
        </div>

        <form method="post" action="change_password.php" class="clearfix">
            <div class="form-group">
                <label for="oldPassword" class="control-label">Parola veche</label>
                <input type="password" class="form-control" name="old-password" placeholder="Parola veche">
            </div>

            <div class="form-group">
                <label for="newPassword" class="control-label">Parola noua</label>
                <input type="password" class="form-control" name="new-password" placeholder="Parola noua">
            </div>

            <div class="form-group">
                <label for="checkPassword" class="control-label">Verificare parola noua</label>
                <input type="password" class="form-control" name="check-password" placeholder="Verificare parola noua">
            </div>

            <div class="form-group clearfix">
                <input type="hidden" name="id" value="<?php echo (int)$user['id'];?>">
                <button type="submit" name="update" class="btn btn-info  pull-right">Modifica parola</button>
            </div>
        </form>
    </div>
</div>
<?php include_once('layouts/footer.php'); ?>
