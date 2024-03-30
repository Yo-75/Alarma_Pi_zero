<?php
$page_title = 'Lista Utilizatori';
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

$all_users = GetAllUsers();

?>

<!--suppress ALL -->
    <h2><strong>Useri în baza de date</strong></h2>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-body">
            <p class="my_h3"><strong>Angajati</strong></p>
            <table id="myTable" class="table table-bordered table-striped" style="width:auto">
                <thead>
                <tr>
                    <th class="text-center no-sort" style="width: 50px">#</th>
                    <th class="text-center" style="min-width:150px">Username</th>
                    <th class="text-center" style="min-width:170px">Nume utilizator</th>
                    <th class="text-center no-sort" style="width: 50px;">Activ</th>
                    <th class="text-center" style="min-width: 200px;">Ultima conectare</th>
                    <th class="text-center no-sort" style="width: 90px;">Actiuni</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($all_users as $a_user): ?>
                    <tr id="row-<?php echo $a_user['id']; ?>">
                        <td class="text-center"><?php echo count_id();?></td>
                        <td style="text-align: center" id="col-<?php echo $a_user['id']; ?>">
                            <a href="u_user.php?id=<?php echo $a_user['id']; ?>" data-toggle="tooltip" title="Click pentru editare permisiuni user">
                                <?php echo $a_user['username'];?>
                            </a>
                        </td>
                        <td style="text-align: center"><?php echo ucfirst($a_user['nume']);?></td>
                        <td class="text-center">
                            <?php if($a_user['activ'] === '1'): ?>
                                <span class="label label-success"><?php echo "Activ"; ?></span>
                            <?php else: ?>
                                <span class="label label-danger"><?php echo "Inactiv"; ?></span>
                            <?php endif;?>
                        </td>
                        <td style="text-align: center"><?php echo read_date_format($a_user['last_login']);?></td>
                        <td class="text-center">
                            <div class="btn-group">

                                <button onclick="ResetPassword(<?php echo (int)$a_user['id'];?>,'<?php echo $a_user['username']; ?>')"
                                        class="btn btn-xs btn-warning" style="margin-right:10px;border-radius:5px;"
                                        data-toggle="tooltip" title="Resetare parola user">
                                    <span class="glyphicon glyphicon-refresh"> </span>
                                </button>

                                <button  onclick="DeleteUser(<?php echo (int)$a_user['id'];?>,'<?php echo $a_user['username']; ?>')"
                                         class="btn btn-xs btn-danger" style="border-radius:5px;" data-toggle="tooltip"
                                         title="Stergere user">
                                    <span class="glyphicon glyphicon-trash" > </span>
                                </button>

                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
        <div class="panel-body" style="display:flex">
            <input id="newUserName" class="form-control" style="width:200px;margin-right: 25px;" title="Nume utilizator">
            <button onclick="AddUser()" type="submit" name="add_cat" class="btn btn-primary">Adaugare user</button>
        </div>
    </div>


    </div>
</div>


<script>

    $(document).ready(function(){
        const t = $('#myTable').DataTable({
            searching: false,
            "order": [1, 'asc'],
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
            "lengthChange": false,
            "pageLength": 25,
            "info": false,
            drawCallback: function (settings) {
                const pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                pagination.toggle(this.api().page.info().pages > 1);
            }
        });

        t.on( 'order.dt search.dt', function () {
            t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();

        const t2 = $('#myTable2').DataTable({
            searching: false,
            "order": [1, 'asc'],
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
            "lengthChange": false,
            "pageLength": 25,
            "info": false,
            drawCallback: function (settings) {
                const pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                pagination.toggle(this.api().page.info().pages > 1);
            }
        });

        t2.on( 'order.dt search.dt', function () {
            t2.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
    });

    function AddUser () {
        const username= $("#newUserName").val();
        if (username!=='') {
            $.ajax({
                url: 'ajax/u_useri/ajax_check_exist_username.php',
                type: 'POST',
                data: {username: username},
                success: function (data) {
                    data = data.trim();
                    if (data === '0') {
                        AdaugareUser(username);
                    }
                    else {
                        DisplayError("Eroare adaugare user",
                            "Există deja definit in baza de date un utilizator cu acest username.<br>Va rog sa verificati!")
                    }
                }
            })
        }
    }

    function AdaugareUser(username){
        $.ajax({
            url: 'ajax/u_useri/ajax_add_u_useri.php',
            type: 'POST',
            data: {
                username: username,
                nume:username
            },
            success: function (data) {
                if (data === '0') {
                        DisplayError("Eroare adaugare user",
                            "A aparut o eroare la adaugarea noului user. Va rog reincercati.")
                }
                else {
                    const Table = $("#myTable");
                    const index = Table.find("tr").length;

                    let a= '<tr id="row-' + data + '">' +
                        '<td class="text-center">' + index + ' </td>' +
                        '<td id="col-' + data + '" class="text-center">' +
                        '<a href="u_user.php?id=' + data + '">' + username + ' </a></td>' +
                        '<td class="text-center">' + username + ' </td>' +
                        '<td class="text-center"><div class="label label-success" >Activ</div></td>' +
                        '<td class="text-center">-</td>' +
                        '<td class="text-center">' +
                        '<div class="btn-group">' +
                        '<button onclick="ResetPassword(' + data + ',' + "'" + username + "'" + ')" ' +
                        'class="btn btn-xs btn-warning" style="margin-right:10px;border-radius:5px;" ' +
                        'data-toggle="tooltip" title="Editare">' +
                        '<span class="glyphicon glyphicon-edit"> </span>' +
                        '</button>' +
                        '<button  onclick="DeleteUser(' + data + ',' + "'" + username + "'" + ')" ' +
                        'class="btn btn-xs btn-danger" style="border-radius:5px;" data-toggle="tooltip"  title="Stergere">' +
                        '<span class="glyphicon glyphicon-trash" > </span>' +
                        '</button>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                    Table.DataTable().row.add($(a)).draw();
                    $("#newUserName").val('');
                    DisplayError("Creare user nou", "A fost creat userul <strong>" + username + " </strong> cu parola <strong>initial<strong>");
                }

            }
        })
    }

    function DeleteUser(id,user) {
        $('<div id="tmp1"></div>').appendTo('body').html('<div><p>' + 'Doriti stergerea acestui user: <br><br><strong> ' + user + '</strong> ?' + '</p></div>')
            .dialog({
                modal: true,
                title: 'Confirmare stergere',
                zIndex: 10000,
                autoOpen: true,
                width: 'auto',
                resizable: false,
                buttons: {
                    Da: function() {
                        $(this).dialog("close");
                        $("#tmp1").remove();
                        $.ajax({
                            url: 'ajax/u_useri/ajax_delete_u_user.php',
                            type: 'POST',
                            data: {id: id},
                            success: function (data) {
                                if (data==='1') {
                                    $('#row-' + id).remove();

                                    //re-generate index pe prima coloana
                                    $("#myTable").find("tr").each(function () {
                                        $(this).find("td").first().html($(this).index() + 1);
                                    });
                                }
                                else {
                                    DisplayError("Eroare stergere user",
                                        "A aparut o eroare la stergerea acestui user. Va rog reincercati.")
                                }
                            }
                        });
                    },
                    Nu: function () {
                        $(this).dialog("close");
                        $("#tmp1").remove();
                    }
                },
                close: function () {
                    $(this).remove();
                }
            });
    }

    function ResetPassword(id,user) {
        $('<div id="tmp5"></div>').appendTo('body').html('<div><p>' + 'Doriti resetarea parolei userului: <strong>' + user + ' </strong> ?' + '</p></div>')
            .dialog({
                modal: true,
                title: 'Confirmare resetare parola',
                zIndex: 10000,
                autoOpen: true,
                width: 'auto',
                resizable: false,
                buttons: {
                    Da: function() {
                        $(this).dialog("close");
                        $("#tmp5").remove();
                        $.ajax({
                            url: 'ajax/u_useri/ajax_u_reset_password.php',
                            type: 'POST',
                            data: {id: id},
                            success: function (data) {
                                if (data ==="1") {
                                    $('<div id="tmp6"></div>').appendTo('body').html('<div><p>' + 'Noua parola a userului <strong> ' + user + '</strong> este acum <strong>initial</strong></p></div>')
                                        .dialog({
                                            modal: true,
                                            title: 'Confirmare resetare parola',
                                            zIndex: 10000,
                                            autoOpen: true,
                                            width: 'auto',
                                            resizable: false,
                                            buttons: {
                                                Da: function () {
                                                    $(this).dialog("close");
                                                    $("#tmp6").remove();
                                                }
                                            }
                                        });
                                }
                                else {
                                    DisplayError("Eroare resetare parola",
                                        "A aparut o eroare la resetarea parolei user. Va rog reincercati.")
                                }
                            }
                        });
                    },
                    Nu: function () {
                        $(this).dialog("close");
                        $("#tmp5").remove();
                    }
                },
                close: function () {
                    $(this).remove();
                }
            });
    }

</script>

<?php include_once('layouts/footer.php'); ?>
