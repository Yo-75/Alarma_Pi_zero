<!DOCTYPE html>
<html lang="ro">
<?php $user = GetCurrentUser(); ?>
<head>
    <meta charset="UTF-8" />
     <title><?php if (!empty($page_title))
            echo remove_junk($page_title);
        elseif(!empty($user))
            echo ucfirst($user['username']);
        else echo "PiAlarma";?>
    </title>

    <link rel="shortcut icon" href="images/favicon.png">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> 
    <link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="libs/css/main.css?v=1.0" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.css"/>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>  
</head>
<body>

<?php  if ($session->isUserLoggedIn()): ?>
    <header id="header">
        <div class="logo pull-left">PiAlarm</div>
        <div class="rest pull-right">
            <div class="sidebar">
                <?php   include_once('admin_menu.php'); ?>
            </div>

            <div class="pull-right clearfix">
                <ul class="info-menu list-inline list-unstyled">
                    <li class="profile">
                        <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
                            <img src="uploads/users/<?php echo $user['image'];?>" alt="user-image" class="img-circle img-inline">
                            <span><?php echo remove_junk(ucfirst($user['username'])); ?> <i class="caret"> </i></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="edit_account.php" title="edit account">
                                    <i class="glyphicon glyphicon-cog"> </i>
                                    Settings
                                </a>
                            </li>
                            <li class="last">
                                <a href="logout.php">
                                    <i class="glyphicon glyphicon-off"> </i>
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>


            <div class="header-date pull-right">
                <strong><?php echo date("F j, Y");?></strong>
            </div>
        </div>
    </header>


<?php endif;?>

<div class="page">
    <div class="container-fluid">

