var linkedin_list = null;
var biorxiv_list = null;
var pubmed_list = null;
var clinical_list = null;

var user_count = 3;

var reports = {};
var reports_tmp = {};
(function($) {
    if($('#linkedin_list').length > 0) {
        linkedin_list = $('#linkedin_list').DataTable({
            responsive: true,
            "columnDefs": [
                { "width": 50, "targets": 1 }
            ],
            fixedColumns: true,
            "ordering": false,
            // ajax: base_url + "/backend/linkedin/profiles"
        });
    }

    if($('#biorxiv_list').length > 0) {
        biorxiv_list = $('#biorxiv_list').DataTable({
            responsive: true,
            "columnDefs": [
                { "width": 50, "targets": 1 }
            ],
            fixedColumns: true,
            "ordering": false,
        });
    }

    if($('#pubmed_list').length > 0) {
        pubmed_list = $('#pubmed_list').DataTable({
            responsive: true,
            "columnDefs": [
                { "width": 50, "targets": 1 }
            ],
            fixedColumns: true,
            "ordering": false,
        });
    }

    if($('#clinical_list').length > 0) {
        clinical_list = $('#clinical_list').DataTable({
            responsive: true,
            "columnDefs": [
                { "width": 50, "targets": 1 }
            ],
            fixedColumns: true,
            "ordering": false,
        });
    }


    $(document).on('click', '.trigger-click', function() {
        var type = $(this).attr('type');

        $('.modal-table-content').hide();
        if(type == 'linkedin') {
            $("#modal_select .modal-title").html('Select Linkedin Accounts');
            $('#linkedin.modal-table-content').show();
        }
        else if(type == 'biorxiv') {
            $("#modal_select .modal-title").html('Select Biorxiv reports');
            $('#biorxiv.modal-table-content').show();
        }
        else if(type == 'pubmed') {
            $("#modal_select .modal-title").html('Select Pubmed reports');
            $('#pubmed.modal-table-content').show();
        }
        else if(type == 'clinical') {
            $("#modal_select .modal-title").html('Select Clinical Trials reports');
            $('#clinical.modal-table-content').show();
        }
        else {
            $("#modal_select .modal-title").html('');
        }

        $("#modal_select").modal('show');
    })

    $(document).on('click', '#modal_select [type="checkbox"]', function() {
        var data_type = $(this).attr('data-type');
        var data_id = $(this).attr('data-id');

        if(!reports_tmp.hasOwnProperty(data_type)) {
            reports_tmp[data_type] = {};
        }

        if($(this).is(':checked')) {
            reports_tmp[data_type][data_id] = $(this).is(':checked');
        }
        else if(reports_tmp[data_type].hasOwnProperty(data_id)){
            delete reports_tmp[data_type][data_id];
        }
        
    })

    $(document).on('click', '#btn_modal_select_save', function() {
        reports = JSON.parse(JSON.stringify(reports_tmp));

        $('.admin-manager-form-input-result').text("");
        Object.keys(reports).forEach(report_type => {
            if(Object.keys(reports[report_type]).length > 0) {
                $('.admin-manager-form-input-result[data-type="' + report_type + '"]').text(Object.keys(reports[report_type]).length + " selected");
            }  
        })

        $("#modal_select").modal('hide');
    })


    $("#modal_select").on("hidden.bs.modal", function () {
        reports_tmp = {};
        $('#modal_select [type="checkbox"]').prop('checked', false);
    });

    $("#modal_select").on("shown.bs.modal", function () {
        reports_tmp = JSON.parse(JSON.stringify(reports));

        $('#modal_select [type="checkbox"]').prop('checked', false);

        Object.keys(reports).forEach(report_type => {
            Object.keys(reports[report_type]).forEach(report_id => {
                $('.checkbox-' + report_type + '[data-id="' + report_id + '"]').prop('checked', true);
            })            
        })
    });

    $(document).on('click', '#dasbboard_list_action_tr [type="checkbox"]', function() {
        $('.dashboard-checkbox').prop('checked', $(this).is(':checked'));
    })

    function clearAdminManagerForm() {
        $('[name="dashboard_name"]').val('');

        for(var index = 1; index <= user_count; index++) {
            $('[name="username' + index + '"]').val('');
            $('[name="password' + index + '"]').val('');
        }

        $('.admin-manager-form-input-result').text("");

        $('#modal_select [type="checkbox"]').prop('checked', false);
    }

    $(document).on('click', '#btn_dashboard_publish', function() {
        if($('[name="dashboard_name"]').val() == '') {
            alert('Please Input Dashboard Name');
            $('[name="dashboard_name"]').focus();
            return;
        }

        if($('[name="username1"]').val() == '') {
            alert('Please Input User Name');
            $('[name="username1"]').focus();
            return;
        }

        if($('[name="password1"]').val() == '') {
            alert('Please Input User Password');
            $('[name="password1"]').focus();
            return;
        }

        var publish_data = {
            reports: reports,
            dashboard_name: $('[name="dashboard_name"]').val(),
            users: []
        };
        
        for(var index = 1; index <= user_count; index++) {
            publish_data['users'].push({
                username: $('[name="username' + index + '"]').val(),
                password: $('[name="password' + index + '"]').val()
            })
        }

        $('body').addClass('loading');

        if($(this).attr('dashboard-id') != '') {
            publish_data['dashboard_id'] = $(this).attr('dashboard-id');
            $.ajax({
                url: base_url + '/backend/dashboard/update',
                type: 'post',
                data: publish_data,
                dataType: 'json',
                success: function(resp) {
                    alert('Updated Successfully!');
                    $('#dashboard_list_table').html(resp.dashboard_trs);
    
                    clearAdminManagerForm();
                    $('body').removeClass('loading');
                }
            })
        }
        else {
            $.ajax({
                url: base_url + '/backend/dashboard/publish',
                type: 'post',
                data: publish_data,
                dataType: 'json',
                success: function(resp) {
                    alert('Published Successfully!');
                    $('#dashboard_list_table').append(resp.dashboard_tr);
    
                    clearAdminManagerForm();
                    $('body').removeClass('loading');
                }
            })
        }
        
    })

    $(document).on('click', '#btn_dashboard_edit', function() {
        var dashboard_ids = [];
        var dashboard_checkboxs = $('.dashboard-checkbox');
        for(var index = 0; index < dashboard_checkboxs.length; index ++) {
            if($(dashboard_checkboxs[index]).is(':checked')) {
                dashboard_ids.push($(dashboard_checkboxs[index]).attr('data-id'));
            }
        }

        if(dashboard_ids.length == 0) {
            alert('Please check dashboard to delete');
            return;
        }

        $('body').addClass('loading');
        $.ajax({
            url: base_url + '/backend/dashboard/edit',
            type: 'post',
            data: {
                'id': dashboard_ids[0]
            },
            dataType: 'json',
            success: function(resp) {
                $('#btn_dashboard_publish').attr('dashboard-id', dashboard_ids[0]);
                if(resp.dashboard) {
                    $('[name="dashboard_name"]').val(resp.dashboard.name);

                    $('.admin-manager-form-input-result').text("");

                    var resp_reports = JSON.parse(resp.dashboard.reports);
                    reports = JSON.parse(JSON.stringify(resp_reports));
                    Object.keys(resp_reports).map(report_type => {

                        Object.keys(resp_reports[report_type]).map(data_id => {
                            $('#' + report_type + '_checkbox_' + data_id).prop('checked', resp_reports[report_type][data_id]);
                        })

                        if(Object.keys(resp_reports[report_type]).length > 0) {
                            $('.admin-manager-form-input-result[data-type="' + report_type + '"]').text(Object.keys(resp_reports[report_type]).length + " selected");
                        }  
                    });
                }

                if(resp.users) {
                    for(var index = 1; index <= resp.users.length; index++) {
                        $('[name="username' + index + '"]').val(resp.users[index - 1].username);
                        // $('[name="password' + index + '"]').val(resp.users[index - 1].password);
                    }
                }
                
                $('body').removeClass('loading');
            }
        })
    })

    $(document).on('click', '#btn_dashboard_delete', function() {
        var dashboard_ids = [];
        var dashboard_checkboxs = $('.dashboard-checkbox');
        for(var index = 0; index < dashboard_checkboxs.length; index ++) {
            if($(dashboard_checkboxs[index]).is(':checked')) {
                dashboard_ids.push($(dashboard_checkboxs[index]).attr('data-id'));
            }
        }

        if(dashboard_ids.length == 0) {
            alert('Please check dashboard to delete');
            return;
        }

        $('body').addClass('loading');
        $.ajax({
            url: base_url + '/backend/dashboard/delete',
            type: 'post',
            data: {
                'ids': dashboard_ids
            },
            dataType: 'json',
            success: function(resp) {
                alert('Successfully');
                $('#dashboard_list_table').html(resp.dashboard_trs);

                $('body').removeClass('loading');
            }
        })
    })
})(jQuery)