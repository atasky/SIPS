<?php

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/dbconnect.php");
include(ROOT . "sips-admin/functions.php");
####################################################################### 
### BEGIN  - Created by kant <-- fag
#######################################################################
$lead_id = (isset($_GET['lead_id'])) ? $_GET['lead_id'] : $_POST['lead_id'];

### Dados da Lead
$query = "SELECT 
			vdlf.campaign_id,
			vdc.campaign_name, 
			vdlf.list_id, 
			vdlf.list_name, 
			vu.full_name, 
			vdl.called_count, 
			DATE_FORMAT(vdl.last_local_call_time,'%H:%i:%s') AS hora_last,
			DATE_FORMAT(vdl.last_local_call_time,'%d-%m-%Y') AS data_last,
			vdl.status, 
			vds.status_name as status_name_one,
			vdcs.status_name as status_name_two,
			DATE_FORMAT(vdl.entry_date,'%H:%i:%s') AS hora_load,
			DATE_FORMAT(vdl.entry_date,'%d-%m-%Y') AS data_load,
			vdl.phone_number
			
		FROM 
			vicidial_lists vdlf 
		INNER JOIN 
			vicidial_list vdl 
		ON 
			vdl.list_id=vdlf.list_id 						
		INNER JOIN 
			vicidial_campaigns vdc 
		ON 
			vdc.campaign_id=vdlf.campaign_id
		LEFT JOIN 
			vicidial_users vu 
		ON 
			vu.user=vdl.user
		LEFT JOIN 
			vicidial_statuses vds 
		ON 
			vds.status=vdl.status
		LEFT JOIN 
			vicidial_campaign_statuses vdcs 
		ON 
			vdcs.status=vdl.status
		WHERE 
			lead_id='$lead_id'
		LIMIT 1";

$query = mysql_query($query, $link) or die(mysql_error());
$lead_info = mysql_fetch_assoc($query);

if ($lead_info['status_name_one'] == NULL) {
    $status_name = $lead_info['status_name_two'];
} else {
    $status_name = $lead_info['status_name_one'];
}

### Dados do Contacto
$query = "SELECT 
			Name,
			Display_name
		FROM 
			vicidial_list_ref
		WHERE
			campaign_id='$lead_info[campaign_id]'
		AND
			active=1 Order by field_order ASC";
$query = mysql_query($query, $link) or die(mysql_error());
$fields_count = mysql_num_rows($query);
for ($i = 0; $i < $fields_count; $i++) {
    $row = mysql_fetch_row($query);
    if ($fields_count == 1) {
        $fields_NAME[$i] = strtolower($row[0]);
        $fields_SELECT = $row[0];
        $fields_LABEL[$i] = $row[1];
    } elseif ($fields_count - 1 == $i) {
        $fields_NAME[$i] = strtolower($row[0]);
        $fields_SELECT .= $row[0];
        $fields_LABEL[$i] = $row[1];
    } else {
        $fields_NAME[$i] = strtolower($row[0]);
        $fields_SELECT .= $row[0] . ",";
        $fields_LABEL[$i] = $row[1];
    }
}
$query = "SELECT 
			$fields_SELECT 
		FROM 
			vicidial_list
		WHERE
			lead_id='$lead_id' 
		LIMIT 1";
$query = mysql_query($query, $link) or die(mysql_error());
$fields = mysql_fetch_row($query);

### Construção da Lista de Feedbacks
$query = "SELECT status,status_name FROM vicidial_campaign_statuses WHERE campaign_id='$lead_info[campaign_id]' AND scheduled_callback!=1";
$query = mysql_query($query, $link) or die(mysql_error());
$is_campaign_feedback = 0;

for ($i = 0; $i < mysql_num_rows($query); $i++) {
    $row = mysql_fetch_assoc($query) or die(mysql_query());

    if ($row['status'] == $lead_info['status']) { # feedback actual (selected)
        $status_options .= "<option selected value='$row[status]'>$row[status_name]</option>\n";
        $is_campaign_feedback = 1;
    } else { # outros feedbacks da campanha
        $status_options .= "<option value='$row[status]'>$row[status_name]</option\n>";
    }
}

if (!$is_campaign_feedback) { # caso se o feedback actual seja de sistema
    $query = "SELECT status,status_name FROM vicidial_statuses WHERE status='$lead_info[status]'";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_assoc($query);
    $status_options .= "<option selected value='$row[status]'>$row[status_name]</option>";
}


### Chamadas Feitas
$query = "SELECT 									
			vl.uniqueid, 
			vl.lead_id, 
			vl.list_id,
			vl.campaign_id,
			DATE_FORMAT(vl.call_date,'%d-%m-%Y') AS data,
			DATE_FORMAT(vl.call_date,'%H:%i:%s') AS hora,
			vl.start_epoch,
			vl.end_epoch,
			vl.length_in_sec,
			vl.status,
			vl.phone_code,
			vl.phone_number,
			vl.user,
			vl.comments,
			vl.processed,
			vl.user_group,
			vl.term_reason,
			vl.alt_dial,
			vu.full_name,
			(select status_name from vicidial_campaign_statuses where status=vl.status limit 1) as status_name1,
			(select status_name from vicidial_statuses where status=vl.status limit 1) as status_name2,
			vc.campaign_name,
			vls.list_name
		FROM 
			vicidial_log vl
		INNER JOIN vicidial_users vu ON vl.user=vu.user
		INNER JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
		INNER JOIN vicidial_lists vls ON vl.list_id=vls.list_id
			
		WHERE 
			vl.lead_id='$lead_id' 
		ORDER BY
			uniqueid 
		DESC LIMIT 500;";
$chamadas_feitas = mysql_query($query, $link) or die(mysql_error());

#Gravações da lead
$query = "SELECT 
				DATE_FORMAT(start_time,'%d-%m-%Y') AS data,
				DATE_FORMAT(start_time,'%H:%i:%s') AS hora_inicio,
				DATE_FORMAT(end_time,'%H:%i:%s') AS hora_fim,
				length_in_sec,
				filename,
				location,
				lead_id,
				rl.user,
				full_name
			FROM 
				recording_log rl
			INNER JOIN vicidial_users vu ON rl.user=vu.user
			WHERE 
				lead_id='$lead_id' 
			ORDER BY 
				recording_id 
			DESC LIMIT 500;";
$gravacoes = mysql_query($query, $link) or die(mysql_error());
?>

<!-- Cabeçalho -->


    <table class='table table-mod table-bordered'>
        <thead> 
            <tr>
                <th>ID do Contacto</th>
                <th>Número de Telefone</th>
                <th>Base de Dados</th>
                <th>Campanha</th>
                <th>Operador</th>
                <th>Feedback</th>
                <th>Nº de Chamadas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $lead_id ?></td>
                <td><?= $lead_info[phone_number] ?></td>
                <td><?= $lead_info[list_name] ?></td>
                <td><?= $lead_info[campaign_name] ?></td>
                <td><?= $lead_info[full_name] ?></td>
                <td><span id='lead_info_status'><?= $status_name ?></span></td>
                <td><?= $lead_info[called_count] ?></td>
            </tr>
        </tbody>
    </table>
<br>
    <table class='table table-mod table-bordered'>
        <thead> 
        <tr>
            <th>Data de Carregamento</th>
            <th>Hora de Carregamento</th>
            <th>Data da Última Chamada</th>
            <th>Hora da Última Chamada</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $lead_info[data_load] ?></td>
                <td><?= $lead_info[hora_load] ?></td>
                <td><?= $lead_info[data_last] ?></td>
                <td><?= $lead_info[hora_last] ?></td>
            </tr>
        </tbody>
    </table>

<legend>Dados da Lead</legend>
        <form class="form-horizontal" id='inputcontainer' >
            <?php for ($i = 0; $i < count($fields); $i++) { ?>
                <div class="control-group">
                    <label class="control-label"><?= $fields_LABEL[$i] ?>:</label>
                    <div class="controls" >
                        <?php if($fields_NAME[$i]!="comments"){ ?>
                        <input type=text name='<?= $fields_NAME[$i] ?>' id='<?= $fields_NAME[$i] ?>' class='span10' value='<?= $fields[$i] ?>'>
                        <?php } else { ?>
                        <textarea name='<?= $fields_NAME[$i] ?>' id='<?= $fields_NAME[$i] ?>' class='span10' ><?= $fields[$i] ?></textarea>
                        <?php } ?>
                        <span id='td_<?= $fields_NAME[$i] ?>'></span>
                    </div>
                </div>
            <?php } ?>
        </form>

<legend>Alteração do Feedback</legend>
      <div class="control-group">
            <label class="control-label">Feedback Actual:</label>
            <div class="controls" >
                <select style='width:200px' name=feedback_list id=feedback_list><?= $status_options ?></select>
                <span id=modify_feedback_status class='help-inline'><i>(O feedback deste contacto pode ser alterado neste menu.)</i></Span>
            </div>
        </div>



<legend>Chamadas realizadas para este Contacto</legend>
    <table class='table table-mod table-bordered'>
        <thead>
            <tr>
                <th>Data</th>
                <th>Hora</th>
                <th>Duração</th>
                <th>Número</th>
                <th>Operador</th>
                <th>Feedback</th>
                <th>Campanha</th>
                <th>Base de Dados</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysql_fetch_assoc($chamadas_feitas)) {

                $duracao = sec_convert($row['length_in_sec'], "H");
                if ($row['status_name1']) {
                    $status_name = $row['status_name1'];
                } else {
                    $status_name = $row['status_name2'];
                }
                ?>

                <tr>
                    <td><?= $row[data] ?></td>
                    <td><?= $row[hora] ?></td>
                    <td><?= $duracao ?></td>
                    <td><?= $row[phone_number] ?></td>
                    <td><?= $row[full_name] ?></td>
                    <td><?= $status_name ?></td>
                    <td><?= $row[campaign_name] ?></td>
                    <td><?= $row[list_name] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


<legend>Gravações deste Contacto</legend>
    <table class='table table-mod table-bordered'>
        <thead>
            <tr>
                <th>Data</th>
                <th>Inicio da Gravação</th>
                <th>Fim da Gravação</th>
                <th>Duração</th>
                <th>Operador</th>
        </thead>
        <tbody>
            <?php while ($row = mysql_fetch_assoc($gravacoes)) { ?>
                <tr>
                    <td><?= $row[data] ?></td>
                    <td><?= $row[hora_inicio] ?></td>
                    <td><?= $row[hora_fim] ?></td>
                    <td><?= sec_convert($row['length_in_sec'], "H") ?></td>
                    <td><?= $row[full_name] ?>
                                    <div class="view-button"><a href='<?= $row[location] ?>' target='_self' class="btn  btn-mini"><i class="icon-play"></i>Ouvir</a></div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

<script type="text/javascript">

    /* VARS/DIALOGS */
    var lead_id = <?= $lead_id; ?>;
    var $error = $('<div></div>')
            .html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ')
            .dialog({
        autoOpen: false,
        title: "<span style='float:left; margin-right: 4px;' class='ui-icon ui-icon-alert'></span> Erro",
        width: "550",
        height: "250",
        show: "fade",
        hide: "fade",
        buttons: {"OK": function() {
                $(this).dialog("close");
            }}
    });


    /* FUNCTIONS */
    $("#inputcontainer input").focus(function()
    {
        $(this).css({"border": "1px solid green"});

    });
    $("#inputcontainer input").blur(function()
    {

        var field = this.name;
        var field_value = $(this).val();
        $(this).css({"border": "1px solid #c0c0c0"});

        $.ajax({
            type: "POST",
            url: "/sips-agente/crm-agent/_requests.php",
            data: {action: "update_contact_field", send_field: field, send_field_value: field_value, send_lead_id: lead_id},
            error: function(jqXHR, textStatus, errorThrown) {
                $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)
            },
            success: function(data, textStatus, jqXHR)
            {
                if ((textStatus == 'success') && (data == 1))
                {
                    $("#td_" + field).html("<span id='img_fade" + field + "'><table><tr><td style='width:18px'><td style='text-align:left;'>A Gravar</span></tr></table></span>");
                    $("#img_fade" + field).fadeOut(2500);
                }
                else
                {
                    $("#td_" + field).html("<span id='img_fade" + field + "'><table><tr><td style='width:18px'><td style='text-align:left;'>Erro a Gravar</tr></table></span>");
                    $("#img_fade" + field).fadeOut(2500);
                }
            }
        });

    });
    $("#feedback_list").change(function()
    {
        var feedback_id = $("#feedback_list").val();
        var feedback_name = $("#feedback_list option:selected").text();
        $.ajax({
            type: "POST",
            url: "/sips-agente/crm-agent/_requests.php",
            data: {action: "update_feedback", send_lead_id: lead_id, send_feedback: feedback_id},
            error: function(jqXHR, textStatus, errorThrown) {
                $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown);
            },
            success: function(data, textStatus, jqXHR)
            {

                if ((textStatus == 'success') && (data == 1)) {
                    $("#modify_feedback_status").html("<span id='feedback_fade'><table><tr><td style='width:18px'><td style='text-align:left;'>A Gravar</span></tr></table></span>");
                    $("#feedback_fade").fadeOut(2000, function() {
                        $("#modify_feedback_status").html("<i>(O feedback deste contacto pode ser alterado neste menu.)</i>");
                    });
                    $("#lead_info_status").html(feedback_name);
                } else {
                    $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + data);
                }

            }
        });

    });

</script>