<?php
$page_title = 'Setare variabile configurare';
require_once('includes/load.php');

if (!$session->isUserLoggedIn()) {
    redirect('index.php', false);
    die();
}

$user_ID = $_SESSION['user_id'];
if (!$user_ID) {
    redirect('home.php', false);
    die();
}
include_once('layouts/header.php');

if (!IsAdmin($user_ID)) Iesire_Nu_Am_Drepturi();

?>

<h3><strong>Setari Alarma</strong></h3><br>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-body">
            <p class="my_h3"><strong>Valori intarziere alarma</strong> </p>
            <table>
                <tr>
                    <td style="width:150px;height: 40px;">Delay Armare Alarma</td>
                    <td width="250px"><input type="number" id="delay_armare" title="<?php echo GetSetariDescriere('DelayArmare'); ?>" size="30" value="<?php echo GetSetariValue('DelayArmare'); ?>" /> </td>
                </tr>
                <tr>
                    <td style="height: 40px;">Delay Usa Deschisa</td>
                    <td><input id="delay_usa_deschisa" type="number" title="<?php echo GetSetariDescriere('DelayUsaDeschisa'); ?>" size="30" value="<?php echo GetSetariValue('DelayUsaDeschisa'); ?>" /> </td>
                </tr>
                <tr>
                    <td style="height: 40px;">Timp autoreset alarma</td>
                    <td><input type="number" title="<?php echo GetSetariDescriere('DelayAutoreset'); ?>" size="30" id="delay_autoreset" value="<?php echo GetSetariValue('DelayAutoreset'); ?>"></td>
                </tr>
            </table>
            <button id="save_delays" class="btn btn-primary" style="display:none" onclick="SalvareDelays()">Salvare
                temporizari</button>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <p class="my_h3"><strong>Setari server mail</strong> </p>
            <table>
                <tr>
                    <td style="width:150px;height: 40px;">Server mail</td>
                    <td width="250px"><input id="server_mail" title="<?php echo GetSetariDescriere('ServerMail'); ?>" type="text" size="30" value="<?php echo GetSetariValue('ServerMail'); ?>" /> </td>
                </tr>
                <tr>
                    <td style="width:150px;height: 40px;">Port server mail</td>
                    <td width="250px"><input id="port_server_mail" title="<?php echo GetSetariDescriere('PortServerMail'); ?>" type="number" size="30" value="<?php echo GetSetariValue('PortServerMail'); ?>" /> </td>
                </tr>
                <tr>
                    <td style="height: 40px;">User server mail</td>
                    <td><input id="user_server_mail" title="<?php echo GetSetariDescriere('UserServerMail'); ?>" type="text" size="30" value="<?php echo GetSetariValue('UserServerMail'); ?>" /> </td>
                </tr>
                <tr>
                    <td style="height: 40px;">Parola server mail</td>
                    <td><input id="parola_server_mail" type="text" title="<?php echo GetSetariDescriere('ParolaServerMail'); ?>" size="30" value="<?php echo GetSetariValue('ParolaServerMail'); ?>" /> </td>
                </tr>
            </table>
            <button id="save_mail" class="btn btn-primary" style="display:none" onclick="SalvareMailData()">Salvare date
                server mail</button>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <p class="my_h3"><strong>Setari mail</strong> </p>
            <table>
                <tr>
                    <td style="width:150px;height: 40px;">De la cine</td>
                    <td width="250px"><input id="mail_from" title="<?php echo GetSetariDescriere('MailSender'); ?>" type="text" size="30" value="<?php echo GetSetariValue('MailSender'); ?>" /> </td>
                </tr>
                <tr>
                    <td style="width:150px;height: 40px;">Catre cine</td>
                    <td width="250px"><textarea id="mail_to" title="<?php echo GetSetariDescriere('MailRecipients'); ?>"  cols="50" rows="5" ><?php echo str_replace(';','&#13;&#10;',GetSetariValue('MailRecipients')); ?> </textarea> </td>
                </tr>
                <tr>
                    <td style="height: 40px;">Subiect mail</td>
                    <td><textarea id="mail_subject" title="<?php echo GetSetariDescriere('MailSubject'); ?>" type="textarea" cols="100" rows="3"><?php echo GetSetariValue('MailSubject'); ?> </textarea> </td>
                </tr>

            </table>
            <button id="save_mail_data" class="btn btn-primary" style="display:none" onclick="SalvareMailData2()">Salvare date mail</button>
        </div>
    </div>

	<button id="test" class="btn btn_primary"  onclick="SendTestMail()">Send Test Message</button>

    <script>
        $(function() {
            $("#delay_armare").on("keyup", function() {
                $("#save_delays").show();
            });

            $("#delay_usa_deschisa").on("keyup", function() {
                $("#save_delays").show();
            });

            $("#delay_autoreset").on("keyup", function() {
                $("#save_delays").show();
            });

            $("#server_mail").on("keyup", function() {
                $("#save_mail").show();
            });

            $("#port_server_mail").on("keyup", function() {
                $("#save_mail").show();
            });

            $("#user_server_mail").on("keyup", function() {
                $("#save_mail").show();
            });

            $("#parola_server_mail").on("keyup", function() {
                $("#save_mail").show();
            });

            $("#mail_from").on("keyup", function() {
                $("#save_mail_data").show();
            });
            $("#mail_to").on("keyup", function() {
                $("#save_mail_data").show();
            });
            $("#mail_subject").on("keyup", function() {
                $("#save_mail_data").show();
            });
        });

	function SendTestMail() {
	var message1="t: : :OK:";
	$.ajax ({
		url: 'ajax/socket/socket.php',
		type: 'POST',
		data: {
			message:message1
		      },
		success: function (data) {
		   if (data ==='0'){
			DisplayError(eroare1,"Eroare trimitere mail test");
			} else {
				DisplayError("Mesaj trimis","Mesajul de test a fost trimis cu succes. Verificati");
				}
			}
		})
	}

        var eroare1 = "Eroare modificare setare";
        var eroare2 = "A aparut o eroare la modificarea setarii : ";
        var eroare3 = "Eroare la modificare run a parametrului : ";

        function SalvareDelays() {
            var message1 = "p:DelayPornireAlarma:";
            var message2 = "p:DelayUsa:";
            var message3 = "p:TimpAutoreset:";
            message1 = message1.concat($("#delay_armare").val(), ":OK:");
            message2 = message2.concat($("#delay_usa_deschisa").val(), ":OK:");
            message3 = message3.concat($("#delay_autoreset").val(), ":OK:");


            $.ajax({
                url: 'ajax/setari/ajax_change_setare.php',
                type: 'POST',
                data: {
                    nume: 'DelayArmare',
                    value: $("#delay_armare").val()
                },
                success: function(data) {
                    if (data === '0') {
                        DisplayError(eroare1, eroare2.concat("DelayArmare"))
                    } else {
                        $.ajax({
                            url: 'ajax/setari/ajax_change_setare.php',
                            type: 'POST',
                            data: {
                                nume: 'DelayUsaDeschisa',
                                value: $("#delay_usa_deschisa").val()
                            },
                            success: function(data) {
                                if (data === '0') {
                                    DisplayError(eroare1, eroare2.concat("DelayUsaDeschisa"))
                                } else {
                                    $.ajax({
                                        url: 'ajax/setari/ajax_change_setare.php',
                                        type: 'POST',
                                        data: {
                                            nume: 'DelayAutoreset',
                                            value: $("#delay_autoreset").val()
                                        },
                                        success: function(data) {
                                            if (data === '0') {
                                                DisplayError(eroare1, eroare2.concat("TimpAutoreset"))
                                            } else {
                                                $.ajax({
                                                    url: 'ajax/socket/socket.php',
                                                    type: 'POST',
                                                    data: {
                                                        message: message1
                                                    },
                                                    success: function(data) {
                                                        if (data === '0') {
                                                            DisplayError(eroare1, eroare3.concat("DelayArmare"));
                                                        } else {
                                                            $.ajax({
                                                                url: 'ajax/socket/socket.php',
                                                                type: 'POST',
                                                                data: {
                                                                    message: message2
                                                                },
                                                                success: function(data) {
                                                                    if (data === '0') {
                                                                        DisplayError(eroare1, eroare3.concat("DelayUsaDeschisa"));
                                                                    } else {
                                                                        $.ajax({
                                                                            url: 'ajax/socket/socket.php',
                                                                            type: 'POST',
                                                                            data: {
                                                                                message: message3
                                                                            },
                                                                            success: function(data) {
                                                                                if (data ==='0') {
                                                                                    DisplayError(eroare1,eroare3.concat("TimpAutoreset"));
                                                                                } else {
                                                                                    $("#save_delays").hide();
                                                                                }
                                                                            }
                                                                        })
                                                                    }
                                                                }
                                                            })
                                                        }
                                                    }
                                                })
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            });
        }

        function SalvareMailData() {
            var message1 = "p:ServerMail:";
            var message2 = "p:PortServerMail:";
            var message3 = "p:UserServerMail:";
            var message4 = "p:ParolaServerMail:";

            var newServer=$("#server_mail").val().trim().replace(/:/g,"");
            var newUser=$("#user_server_mail").val().trim().replace(/:/g,"");
            var newParola=$("#parola_server_mail").val().trim().replace(/:/g,"");


            message1 = message1.concat(newServer, ":OK:");
            message2 = message2.concat($("#port_server_mail").val(), ":OK:");
            message3 = message3.concat(newUser, ":OK:");
            message4 = message4.concat(newParola, ":OK:");
            $.ajax({
                url: 'ajax/setari/ajax_change_setare.php',
                type: 'POST',
                data: {
                    nume: 'ServerMail',
                    value: newServer
                },
                success: function(data) {
                    if (data === '0') {
                        DisplayError(eroare1, eroare2.concat("ServerMail"))
                    } else {
                        $.ajax({
                            url: 'ajax/setari/ajax_change_setare.php',
                            type: 'POST',
                            data: {
                                nume: 'PortServerMail',
                                value: $("#port_server_mail").val()
                            },
                            success: function(data) {
                                if (data === '0') {
                                    DisplayError(eroare1, eroare2.concat("PortServerMail"))
                                } else {
                                    $.ajax({
                                        url: 'ajax/setari/ajax_change_setare.php',
                                        type: 'POST',
                                        data: {
                                            nume: 'UserServerMail',
                                            value: newUser
                                        },
                                        success: function(data) {
                                            if (data === '0') {
                                                DisplayError(eroare1, eroare2.concat("UserServerMail"))
                                            } else {
                                                $.ajax({
                                                    url: 'ajax/setari/ajax_change_setare.php',
                                                    type: 'POST',
                                                    data: {
                                                        nume: 'ParolaServerMail',
                                                        value: newParola
                                                    },
                                                    success: function(data) {
                                                        if (data === '0') {
                                                            DisplayError(eroare1,eroare2.concat("ParolaServerMail"))
                                                        } else {
                                                            $.ajax({
                                                                url: 'ajax/socket/socket.php',
                                                                type: 'POST',
                                                                data: {
                                                                    message: message1
                                                                },
                                                                success: function(data) {
                                                                    if (data === '0') {
                                                                        DisplayError (eroare1,eroare3.concat("ServerMail"));
                                                                    } else {
                                                                        $.ajax({
                                                                            url: 'ajax/socket/socket.php',
                                                                            type: 'POST',
                                                                            data: {
                                                                                message: message2
                                                                            },
                                                                            success: function(data) {
                                                                                if (data ==='0' ) {
                                                                                    DisplayError (eroare1,eroare3.concat("PortServerMail" ) );
                                                                                } else {
                                                                                    $.ajax({
                                                                                        url: 'ajax/socket/socket.php',
                                                                                        type: 'POST',
                                                                                        data: {
                                                                                            message: message3
                                                                                        },
                                                                                        success: function( data) {
                                                                                            if (data ==='0') {
                                                                                                DisplayError (eroare1,eroare3.concat("UserServerMail"));
                                                                                            } else {
                                                                                                $.ajax({
                                                                                                    url: 'ajax/socket/socket.php',
                                                                                                    type: 'POST',
                                                                                                    data: {
                                                                                                        message: message4
                                                                                                    },
                                                                                                    success: function(data) {
                                                                                                        if (data ==='0') {
                                                                                                            DisplayError(eroare1,eroare3.concat("ParolaServerMail" ));
                                                                                                        } else {
                                                                                                            $("#save_mail").hide();
                                                                                                        }
                                                                                                    }
                                                                                                })
                                                                                            }
                                                                                        }
                                                                                    })
                                                                                }
                                                                            }
                                                                        })
                                                                    }
                                                                }
                                                            })
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            });
        }

        function SalvareMailData2() {
            var message1 = "p:MailSender:";
            var message2 = "p:MailRecipients:";
            var message3 = "p:MailSubject:";

            var newSender=$("#mail_from").val().trim().replace(/:/g,"");
            var newRecipients=$("#mail_to").val().replace(/\n/g,";").replace(/\s/g,"").replace(/:/g,"");
            var newSubject=$("#mail_subject").val().trim().replace(/:/g,"");
            
            message1 = message1.concat(newSender, ":OK:");
            message2 = message2.concat(newRecipients, ":OK:");
            message3 = message3.concat(newSubject, ":OK:");
            $.ajax({
                url: 'ajax/setari/ajax_change_setare.php',
                type: 'POST',
                data: {
                    nume: 'MailSender',
                    value: newSender
                },
                success: function(data) {
                    if (data === '0') {
                        DisplayError(eroare1, eroare2.concat("MailSender"))
                    } else {
                        $.ajax({
                            url: 'ajax/setari/ajax_change_setare.php',
                            type: 'POST',
                            data: {
                                nume: 'MailRecipients',
                                value: newRecipients
                            },
                            success: function(data) {
                                if (data === '0') {
                                    DisplayError(eroare1, eroare2.concat("MailRecipients"))
                                } else {
                                    $.ajax({
                                        url: 'ajax/setari/ajax_change_setare.php',
                                        type: 'POST',
                                        data: {
                                            nume: 'MailSubject',
                                            value: newSubject
                                        },
                                        success: function(data) {
                                            if (data === '0') {
                                                DisplayError(eroare1, eroare2.concat("UserServerMail"))
                                            } else {

                                                $.ajax({
                                                    url: 'ajax/socket/socket.php',
                                                    type: 'POST',
                                                    data: {
                                                        message: message1
                                                    },
                                                    success: function(data) {
                                                        if (data === '0') {
                                                            DisplayError(eroare1, eroare3.concat("MailSender"));
                                                        } else {
                                                            $.ajax({
                                                                url: 'ajax/socket/socket.php',
                                                                type: 'POST',
                                                                data: {
                                                                    message: message2
                                                                },
                                                                success: function( data) {
                                                                    if (data ==='0') {
                                                                        DisplayError(eroare1,eroare3.concat("MailRecipients"));
                                                                    } else {
                                                                        $.ajax({
                                                                            url: 'ajax/socket/socket.php',
                                                                            type: 'POST',
                                                                            data: {
                                                                                message: message3
                                                                            },
                                                                            success: function(data) {
                                                                                if (data ==='0') {
                                                                                    DisplayError(eroare1,eroare3.concat( "MailSubject" ));
                                                                                } else {
                                                                                    $("#save_mail_data").hide();
                                                                                }
                                                                            }
                                                                        })
                                                                    }
                                                                }
                                                            })
                                                        }
                                                    }
                                                })
                                            }
                                        }
                                    })
                                }
                            }
                        });
                    }
                }
            });
        }
    </script>

    <?php include_once('layouts/footer.php'); ?>
