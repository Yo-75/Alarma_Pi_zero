<?php
$page_title = 'Editare Cont';
require_once('includes/load.php');

if (!$session->isUserLoggedIn()){
    die("Trebuie sa fii logat");
}

//update user image
if(isset($_POST['submit'])) {
    $photo = new Media();
    $user_id = (int)$_POST['user_id'];
    $photo->upload($_FILES['file_upload']);
    $photo->process_user($user_id);
    }
?>

<?php include_once('layouts/header.php'); ?>


<div class="login-page" style="display:flex; flex-direction:column">
       <div class="panel panel-default">
           <div class="panel-heading clearfix">
                <span>Modificare imagine cont</span>
           </div>
           <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <img class="img-circle img-size-2" src="uploads/users/<?php echo $user['image'];?>" alt="">
                    </div>
                    <div class="col-md-8">
                        <form class="form" action="edit_account.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="file_upload">Selectați o imagine nouă:</label>
                                <input type="file" name="file_upload" multiple="multiple" class="btn btn-default btn-fi>                            </div>
                            <div class="form-group">
                                <input type="hidden" name="user_id" value="<?php echo $user['id'];?>">
                                <button type="submit" name="submit" class="btn btn-warning">Modifica</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <span>Modificare parola cont</span>
        </div>
        <div class="panel-body">
            <div class="col-md-4">
                <a href="change_password.php" title="change password" class="btn btn-danger ">Schimbare parola</a>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
