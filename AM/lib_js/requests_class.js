var requests_class = function(basic_path, options_ext)
{
    var me = this;
    this.basic_path = basic_path;
    this.config = new Object();
    $.extend(true, this.config, options_ext);


    this.init_apoio_marketing = function()
    {
        $.get("/AM/view/requests/apoio_marketing_modal.html", function(data) {
            me.basic_path.append(data);
        });
    };

    this.init_relatorio_correio = function()
    {
        /*   $.get("/AM/view/requests/relatorio_correio_modal.html", function(data) {
         me.basic_path.append(data);
         });*/
    };

    this.init_relatorio_frota = function()
    {
        $.get("/AM/view/requests/relatorio_frota_modal.html", function(data) {
            me.basic_path.append(data);
        });
    };

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------APOIO MARKETING--------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    this.new_apoio_marketing = function(am_zone)
    {
        var ldpinput_count = 2;
        am_zone.empty().off();
        $.get("/AM/view/requests/apoio_marketing.html", function(data) {
            am_zone.append(data);
            $('#apoio_am_form').stepy({backLabel: "Anterior", nextLabel: "Seguinte", next: function() {
                    if (am_zone.find("#apoio_am_form").validationEngine("validate"))
                        return true;
                    else
                        return false;
                }, finishButton: false});
            am_zone.find(".form_datetime_day").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2}).on('changeDate', function(ev) {
                am_zone.find(".form_datetime_hour").datetimepicker('update', $(this).val());
            });
            am_zone.find(".form_datetime_hour").datetimepicker({format: 'yyyy-mm-dd hh:ii', autoclose: true, language: "pt", startView: 1, maxView: 1});
            //Adiciona Linhas
            am_zone.on("click", "#button_ldptable_add_line", function(e)
            {
                e.preventDefault();
                am_zone.find("#table_tbody_ldp").append("<tr><td><input name='ldp_cp" + ldpinput_count + "' class='linha_cp validate[required]'></td><td><input name='ldp_freg" + ldpinput_count + "' class='linha_freg validate[required]'></td><td> <button class='btn btn-danger button_ldptable_remove_line' ><span class='icon-minus'></span></button></td></tr>");
                ldpinput_count++;
            });
            //Remove Linhas
            am_zone.on("click", ".button_ldptable_remove_line", function(e)
            {
                e.preventDefault();
                $(this).parent().parent().remove();
            });

            //SUBMIT
            am_zone.on("click", "#submit_am", function(e)
            {
                e.preventDefault();
                if (am_zone.find("#apoio_am_form").validationEngine("validate"))
                {
                    var local_publicidade_array = [];
                    $.each(am_zone.find("#table_tbody_ldp").find("tr"), function(data)
                    {
                        local_publicidade_array.push(
                                {cp: $(this).find(".linha_cp").val(),
                                    freguesia: $(this).find(".linha_freg").val()});
                    });
                    $.post("/AM/ajax/requests.php", {action: "criar_apoio_marketing",
                        data: am_zone.find("#data_rastreio").val(),
                        horario: {inicio1: am_zone.find("#data_inicio1").val(), inicio2: am_zone.find("#data_inicio2").val(), fim1: am_zone.find("#data_fim1").val(), fim2: am_zone.find("#data_fim2").val()},
                        localidade: am_zone.find("#input_localidade").val(),
                        local: am_zone.find("#input_local_rastreio").val(),
                        morada: am_zone.find("#input_morada_rastreio").val(),
                        comments: am_zone.find("#input_observaçoes").val(),
                        local_publicidade: local_publicidade_array},
                    function(data1)
                    {
                        $('#apoio_am_form').stepy('step', 1);
                        am_zone.find(":input").val("");
                        $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                    }, "json");
                }
            });
        });
    };
    this.get_apoio_marketing_to_datatable = function(am_zone)
    {
        var Table_pedido_apoio_marketing = am_zone.dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers",
            "sAjaxSource": '/AM/ajax/requests.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "get_apoio_marketing_to_datatable"});
            },
            "aoColumns": [{"sTitle": "id"}, {"sTitle": "Agente"}, {"sTitle": "Data pedido"}, {"sTitle": "Data rastreio"}, {"sTitle": "Horario"}, {"sTitle": "Localidade"}, {"sTitle": "Local"}, {"sTitle": "Morada"}, {"sTitle": "Observações"}, {"sTitle": "Local publicidade"}, {"sTitle": "Status"}],
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
        });


        am_zone.on("click", ".ver_horario", function()
        {
            var id = $(this).data().apoio_marketing_id;
            $.post("ajax/requests.php", {action: "get_horario_from_apoio_marketing", id: id}, function(data) {

                me.basic_path.find("#ver_horario_modal #inicio1").text(data.inicio1);
                me.basic_path.find("#ver_horario_modal #inicio2").text(data.inicio2);
                me.basic_path.find("#ver_horario_modal #fim1").text(data.fim1);
                me.basic_path.find("#ver_horario_modal #fim2").text(data.fim2);
                me.basic_path.find("#ver_horario_modal").modal("show");
            }, "json");


        });

        am_zone.on("click", ".ver_local_publicidade", function()
        {
            var id = $(this).data().apoio_marketing_id;
            $.post("/AM/ajax/requests.php", {action: "get_locais_publicidade_from_apoio_marketing", id: id}, function(data) {
                me.basic_path.find("#ver_local_publicidade_modal #tbody_ver_local_publicidade").empty();
                $.each(data, function()
                {
                    me.basic_path.find("#ver_local_publicidade_modal #tbody_ver_local_publicidade").append("<tr><td>" + this.cp + "</td><td>" + this.freguesia + "</td></tr>");
                });
                me.basic_path.find("#ver_local_publicidade_modal").modal("show");
            }, "json");


        });



        am_zone.on("click", ".accept_apoio_marketing", function()
        {
            var this_button = $(this);
            $.post('/AM/ajax/requests.php', {action: "accept_apoio_marketing", id: $(this).val()}, function() {
                this_button.parent("td").prev().text("Aprovado");
            }, "json");
        });

        am_zone.on("click", ".decline_apoio_marketing", function()
        {
            var this_button = $(this);
            $.post('/AM/ajax/requests.php', {action: "decline_apoio_marketing", id: $(this).val()}, function() {
                this_button.parent("td").prev().text("Rejeitado");
            }, "json");
        });

    };





    this.new_relatorio_correio = function(rc_zone)
    {
        rc_zone.empty().off();
        $.get("/AM/view/requests/relatorio_correio.html", function(data) {
            rc_zone.append(data);
            rc_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", minView: 2});
            //SUBMIT
            rc_zone.on("click", "#submit_rc", function(e)
            {
                e.preventDefault();
                if (rc_zone.find("#relatorio_correio_form").validationEngine("validate"))
                {
                    $.post("ajax/requests.php", {action: "criar_relatorio_correio",
                        carta_porte: rc_zone.find("#input_carta_porte").val(),
                        data: rc_zone.find("#data_envio_datetime").val(),
                        doc: rc_zone.find("#input_doc").val(),
                        lead_id: rc_zone.find("#input_lead_id").val(),
                        client_name: rc_zone.find("#input_client_name").val(),
                        input_doc_obj_assoc: rc_zone.find("#input_doc_obj_assoc").val().length ? rc_zone.find("#input_doc_obj_assoc").val() : "",
                        comments: rc_zone.find("#input_comments").val().length ? rc_zone.find("#input_comments").val() : "Sem observações"},
                    function(data1)
                    {
                        rc_zone.find(":input").val("");
                        $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                    }, "json");
                }
            });
        });
    };
    this.get_relatorio_correio_to_datatable = function(rc_zone)
    {
        var Table_relatorio_correio = rc_zone.dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers",
            "sAjaxSource": '/AM/ajax/requests.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "get_relatorio_correio_to_datatable"});
            },
            "aoColumns": [{"sTitle": "id"}, {"sTitle": "Agente"}, {"sTitle": "Carta Porte"}, {"sTitle": "Data Envio"}, {"sTitle": "Documento"}, {"sTitle": "Lead id"}, {"sTitle": "anexo"}, {"sTitle": "Observações"}, {"sTitle": "Status"}],
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
        });

        rc_zone.on("click", ".accept_report_correio", function()
        {
            var this_button = $(this);
            $.post('/AM/ajax/requests.php', {action: "accept_report_correio", id: $(this).val()}, function() {
                this_button.parent("td").prev().text("Aprovado");
            }, "json");
        });

        rc_zone.on("click", ".decline_report_correio", function()
        {
            var this_button = $(this);
            $.post('/AM/ajax/requests.php', {action: "decline_report_correio", id: $(this).val()}, function() {
                this_button.parent("td").prev().text("Rejeitado");
            }, "json");
        });
    };



    this.new_relatorio_frota = function(rf_zone)
    {
        var rfinput_count = 2;
        rf_zone.empty().off();
        $.get("/AM/view/requests/relatorio_frota.html", function(data) {
            rf_zone.append(data);
            rf_zone.find(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
            rf_zone.find(".rf_datetime").datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
            //Adiciona Linhas
            rf_zone.on("click", "#button_rf_table_add_line", function(e)
            {
                e.preventDefault();
                rf_zone.find("#table_tbody_rf").append("<tr><td> <input size='16' type='text' name='rf_data" + rfinput_count + "' class='rf_datetime validate[required] linha_data' readonly id='rf_datetime" + rfinput_count + "' placeholder='Data'></td><td><input class='validate[required] linha_ocorrencia' type='text' name='rf_ocorr" + rfinput_count + "'></td><td>  <input class='validate[required] linha_km' type='number' value='1' name='rf_km" + rfinput_count + "' min='1'></td><td>     <button class='btn btn-danger button_rf_table_remove_line'><span class='icon-minus'></span></button></td></tr>");
                rf_zone.find("#rf_datetime" + rfinput_count).datetimepicker({format: 'yyyy-mm-dd', autoclose: true, language: "pt", startView: 2, minView: 2});
                rfinput_count++;
            });
            //Remove Linhas
            rf_zone.on("click", ".button_rf_table_remove_line", function(e)
            {
                e.preventDefault();
                $(this).parent().parent().remove();
            });
            //SUBMIT
            rf_zone.on("click", "#submit_rf", function(e)
            {
                e.preventDefault();
                if (rf_zone.find("#relatorio_frota_form").validationEngine("validate"))
                {
                    var ocorrencias_array = [];
                    $.each(rf_zone.find("#table_tbody_rf").find("tr"), function(data)
                    {
                        ocorrencias_array.push(
                                {data: $(this).find(".linha_data").val(),
                                    ocorrencia: $(this).find(".linha_ocorrencia").val(),
                                    km: $(this).find(".linha_km").val()});
                    });
                    $.post("/AM/ajax/requests.php", {action: "criar_relatorio_frota",
                        data: rf_zone.find("#input_data").val(),
                        matricula: rf_zone.find("#input_matricula").val(),
                        km: rf_zone.find("#input_km").val(),
                        viatura: rf_zone.find(":radio[name='rrf']:checked").val(),
                        ocorrencias: ocorrencias_array,
                        comments: rf_zone.find("#input_comments").val().length ? rf_zone.find("#input_comments").val() : ""},
                    function(data1)
                    {
                        rf_zone.find(":input").val("");
                        $.jGrowl('Pedido Efectuado com sucesso', {life: 5000});
                    }, "json");




                }
            });
        });
    };
    this.get_relatorio_frota_to_datatable = function(rf_zone)
    {
        var Table_relatorio_frota = rf_zone.dataTable({
            "bSortClasses": false,
            "bProcessing": true,
            "bDestroy": true,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers",
            "sAjaxSource": '/AM/ajax/requests.php',
            "fnServerParams": function(aoData) {
                aoData.push({"name": "action", "value": "get_relatorio_frota_to_datatable"});
            },
            "aoColumns": [{"sTitle": "id"}, {"sTitle": "user"}, {"sTitle": "data"}, {"sTitle": "Matricula"}, {"sTitle": "Km"}, {"sTitle": "Viatura"}, {"sTitle": "Observações"}, {"sTitle": "Ocorrencias"}, {"sTitle": "Status"}],
            "oLanguage": {"sUrl": "../../../jquery/jsdatatable/language/pt-pt.txt"}
        });



    };


};