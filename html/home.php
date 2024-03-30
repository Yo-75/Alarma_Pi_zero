<?php
$page_title = 'Home Page';
require_once('includes/load.php');

if (!$session->isUserLoggedIn()) {
    redirect('index.php', false);
    die();
}

include_once('layouts/header.php');
$user_ID=(int) $_SESSION['user_id'];

?>

<!--suppress ALL -->
<div class="row">
    <div class="col-md-12">
        <?php echo GetSigla() ?>
        <div class="panel panel-default">
            <div class="panel-body">
             
	

           </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>

