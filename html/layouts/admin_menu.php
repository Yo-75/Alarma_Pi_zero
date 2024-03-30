<?php
require_once('includes/load.php');

$userID = $_SESSION['user_id'];
if(!$userID) { redirect('home.php', false);}
?>

<ul class="drop_menu" id="AdminMenu">

    <li><a style="color: #fefefe;text-decoration: none" href="home.php">Home</a></li>

    <?php  if(IsAdmin($userID)) : ?>
        <li>
            <a href="setari.php">Setari alarma(admin)</a>
        </li>
    <?php endif ?>

        <li>
            <a class="toggle" href="javascript:void(0);">Date presiune/temperatura</a>
            <ul class="dropdown-content">
                <li><a href="pressure.php" >Valori presiune</a></li>
                <li><a href="temperatures.php">Temperaturi</a> </li>
            </ul>
        </li>

     <?php  if(IsAdmin($userID)) : ?>
        <li>
            <a class="toggle" href="javascript:void(0);">Utilizatori</a>
            <ul class="dropdown-content">
                <li><a href="u_useri.php">Useri site</a> </li>
            </ul>
        </li>

    <?php endif ?>

</ul>

