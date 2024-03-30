<?php
$page_title = 'Editare Drepturi Utilizator';
require_once('includes/load.php');

if (!$session->isUserLoggedIn()) {
    redirect('index.php', false);
    die();
}

$user_ID=$_SESSION['user_id'];
if(!$user_ID) {
    redirect('home.php', false);
    die();
}

include_once('layouts/header.php');

// Only Admins are allowed
if (!IsAdmin($user_ID)) Iesire_Nu_Am_Drepturi();

if (!isset($_GET['id'])) ShowErrorMessage();

$id=(int)$_GET['id'];

$user = GetUserDetails($id);
?>

    <h2><strong>Modificare user</strong></h2>

    <div class="col-md-12">
        <div class="panel panel-default user">
            <div class="panel-body" >
                <table id="myTable">
                    <tr>
                        <td style="width: 150px"><strong>Username</strong></td>
                        <td style="width: 500px">
                            <input id="username" class="component-details" style="width: 200px" value="<?php echo $user['username'];?>" title="Noul username">
                            <button id="save_username" style="display:none"
                                    onclick="SaveNewUserName('<?php echo $user['username'];?>')">
                                Salveaza noul username</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Nume utilizator</strong></td>
                        <td>
                            <input id="name" class="component-details"
                                   style="width: 200px"  value="<?php echo ucfirst($user['nume']);?>"
                                   title="Noul nume">
                            <button id="save_name" style="display:none"
                                    onclick="SaveNewNume('<?php echo ucfirst($user['nume']);?>')">
                                Salveaza noul nume</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Activ</strong></td>
                        <td>
                            <?php if($user['activ'] === '1'): ?>
                                <span class="label label-success"><?php echo "Activ"; ?></span>
                                <button onclick="SetActiv(<?php echo $id ?>,0)">Dezactivare</button>
                            <?php else: ?>
                                <span class="label label-danger"><?php echo "Inactiv"; ?></span>
                                <button onclick="SetActiv(<?php echo $id ?>,1)">Activare</button>
                            <?php endif;?>
                        </td>
                    </tr>


                    <?php if ($user['activ'] && !$user['Is_admin']) : ?>
                        <tr id="permisiuni-1">
                            <td rowspan="5"><strong>Permisiuni</strong></td>
                            <td><br>
                                <input type="checkbox" id="Can_View_Records" class="check_user"
                                       onclick="Toggle('Can_View_Records')"
                                    <?php if ($user['Can_View_Records']) echo 'checked' ;?>
                                       data-toggle="tooltip"
                                       title="Dreptul de a vizualiza inregistrarile sistemului de alarma">
                                Vizualizare Inregistrari<br>

                                <input type="checkbox" id="Can_Delete_Records" class="check_user"
                                       onclick="Toggle('Can_Delete_Records')"
                                    <?php if ($user['Can_Edit_Component']) echo 'checked' ;?>
                                       data-toggle="tooltip"
                                       title="Dreptul de a edita sterge inregistrari ale sistemului de alarma">
                                Stergere Inregistrari<br>

                                <input type="checkbox" id="Can_Modify_Times" class="check_user"
                                       onclick="Toggle('Can_Modify_Times')"
                                    <?php if ($user['Can_Modify_Times']) echo 'checked' ;?>
                                       data-toggle="tooltip"
                                       title="Dreptul de a modifica temporizarile siste,ului de alarma">
                                Modificare temporizari<br>
                            </td>
                        </tr>
                    <?php endif ?>

                </table>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function(){

            $('#username').on('input propertychange paste', function() {
                // do your stuff
                $("#save_username").show();
            });

            $('#name').on('input propertychange paste', function() {
                // do your stuff
                $("#save_name").show();
            })
        });

        function Toggle(field) {
            let check=$("#"+field);
            let is_checked=check.prop('checked');
            $.ajax({
                type: "POST",
                url: "ajax/u_useri/ajax_set_rights.php",
                async: true,
                data: {
                    field: field,
                    id: <?php echo $id ?>,
                    checked:is_checked
                },
                success: function (dat) {
                    if (dat==='-1') {
                        DisplayError("Eroare modificare permisiuni",
                            "A aparut o eroare la modificarea acestei permisiuni. Va rog reincercati.")
                    }
                    else {
                        check.prop("checked", (dat === '1'));
                    }
                }
            });
        }

        function ToggleAdmin() {
            let check=$("#Admin");
            let is_checked=check.prop('checked');
            $.ajax({
                type: "POST",
                url: "ajax/u_useri/ajax_set_rights.php",
                async: true,
                data: {
                    field: 'Is_admin',
                    id: <?php echo $id ?>,
                    checked:is_checked
                },
                success: function (data) {
                    if (data==='-1') {
                        DisplayError("Eroare modificare permisiuni",
                            "A aparut o eroare la modificarea acestei permisiuni. Va rog reincercati.")
                    }
                    else {
                        if (data === '1') {
                            $("#permisiuni-1").remove();
                            $("#permisiuni-2").remove();
                            $("#permisiuni-3").remove();
                            $("#permisiuni-4").remove();
                            $("#permisiuni-5").remove();
                        }
                        else
                        {
                            location.reload();
                        }
                    }
                }
            });
        }

        function SaveNewUserName(old_user){
            const new_user=$("#username").val();

            $.ajax({
                url: 'ajax/u_useri/ajax_check_exist_username.php',
                type: 'POST',
                data: {
                    username: new_user
                },
                success: function (data) {
                    data = data.trim();
                    if (data !== "0") {
                        DisplayError("Eroare schimbare username","Acest username exista deja.<br>Va rog sa verificati!")
                    }
                    else {

                        $('<div id="tmp3"></div>').appendTo('body')
                            .html('<div><p>Doriti modificarea username ?<br>' +
                                'Valoare initiala :<strong> ' + old_user + '</strong><br>' +
                                'Valoare noua     :<strong> ' + new_user + '</strong></p></div>')
                            .dialog({
                                modal: true,
                                title: 'Confirmare schimbare username',
                                zIndex: 10000,
                                autoOpen: true,
                                width: 'auto',
                                resizable: false,
                                buttons: {
                                    "Da": function () {
                                        $(this).dialog("close");
                                        $("#tmp3").remove();
                                        $.ajax({
                                            url: 'ajax/u_useri/ajax_change_username.php',
                                            type: 'POST',
                                            data: {
                                                id: <?php echo $id ?>,
                                                username: new_user
                                            },
                                            async: true,
                                            success: function (data) {
                                                if (data === '1') {
                                                    $("#save_username").hide().attr('onclick', "SaveNewUserName('" + new_user + "')");
                                                }
                                                else {
                                                    $("#save_username").hide();
                                                    $("#username").val(old_user);
                                                    DisplayError("Eroare modificare username",
                                                        "A aparut o eroare la modificarea username pentru acest user. Va rog reincercati.")
                                                }
                                            }
                                        });
                                    },
                                    "Nu": function () {
                                        $(this).dialog("close");
                                        $("#tmp3").remove();
                                    }
                                },
                                close: function () {
                                    $(this).remove();
                                }
                            });
                    }
                }
            });
        }

        function SaveNewNume(oldname) {
            let newname=$("#name").val();
            let id=<?php echo $id ?>;

            $('<div id="tmp3"></div>').appendTo('body')
                .html('<div><p>Doriti modificarea numelui utilizatorului ?<br>' +
                    'Nume vechi :<strong> ' + oldname + '</strong><br>' +
                    'Nume nou   :<strong> ' + newname + '</strong></p></div>')
                .dialog({
                    modal: true,
                    title: 'Confirmare schimbare nume utilizator',
                    zIndex: 10000,
                    autoOpen: true,
                    width: 'auto',
                    resizable: false,
                    buttons: {
                        Da: function () {
                            $(this).dialog("close");
                            $("#tmp3").remove();
                            $.ajax({
                                url: 'ajax/u_useri/ajax_change_user.php',
                                type: 'POST',
                                data: {
                                    id: id ,
                                    user: newname
                                },
                                async: true,
                                success: function (data) {
                                    if (data === '1') {
                                        $("#save_name").hide().attr('onclick', "SaveNewNume('" + newname + "')");
                                    }
                                    else {
                                        $("#save_name").hide();
                                        $("#name").val(oldname);
                                        DisplayError("Eroare modificare nume utilizator",
                                            "A aparut o eroare la modificarea numelui utilizatorului. Va rog reincercati.")
                                    }
                                }
                            });
                        },
                        Nu: function () {
                            $(this).dialog("close");
                            $("#tmp3").remove();
                        }
                    },
                    close: function () {
                        $(this).remove();
                    }
                });
        }

        function SetActiv(id,activ) {
            $.ajax({
                type: "POST",
                url: "ajax/u_useri/ajax_change_activ.php",
                async: true,
                data: {
                    id: id,
                    activ: activ
                },
                success: function (data) {
                    if (data==='0') {
                        DisplayError("Eroare modificare user","Nu s-a putut modifica statusul activ al userului. Va rog reincercati")
                    }
                    else {
                        location.reload();
                    }
                }
            })
        }

    </script>

<?php include_once('layouts/footer.php'); ?>
