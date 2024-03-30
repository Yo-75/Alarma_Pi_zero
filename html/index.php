<?php
ob_start();
require_once('includes/load.php');

if($session->isUserLoggedIn()) {
    redirect('home.php', false);
    die();
}

include_once('layouts/header.php');
?>

<div style="margin-top:-30px" >
     <?php echo GetSigla() ?>
</div>


<div class="login-page" style="display:flex">
    <div  style="display:flex; align-items:center;justify-content: space-between;;">
        <div  style="display:flex; flex-direction: column;margin-right:30px">
            <div>
                <img src="images/Login.png" style="max-width: 150px;max-height: 150px;" alt="Poza login">
            </div>
        </div>
        <div>
        <div class="text-center">
            <p class="my_h3"><strong>Autentificare in sistem</strong></p>
            <br>
        </div>

       <form method="post" action="auth.php" class="clearfix">
            <div class="form-group">
                <label for="username" class="control-label">Utilizator</label>
                <input type="text" class="form-control" name="username" placeholder="Username">
            </div>
            <div class="form-group">
                <label for="Password" class="control-label">Parola</label>
                <input type="password" name= "password" class="form-control" placeholder="Password">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-info pull-right" style="margin-bottom:10px;">Autentificare</button>
            </div>

        </form>
        <div>
            <?php  echo display_msg($msg);  ?>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
