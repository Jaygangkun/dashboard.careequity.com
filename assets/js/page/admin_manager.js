var linkedin_list = null;
var biorxiv_list = null;
var pubmed_list = null;
var clinical_list = null;

var user_count = 3;

var reports = {};
var reports_tmp = {};
(function($) {
    function load_reports() {
        $('body').addClass('loading');
        $.ajax({
            url: base_url + '/backend/reports/load',
            type: 'get',
            dataType: 'json',
            success: function(resp) {
                $('#linkedin_list tbody').html(resp.linkedin);
                $('#biorxiv_list tbody').html(resp.biorxiv);
                $('#pubmed_list tbody').html(resp.pubmed);
                $('#clinical_list tbody').html(resp.clinical);

                linkedin_list = $('#linkedin_list').DataTable({
                    responsive: true,
                    "columnDefs": [
                        { "width": 50, "targets": 1 }
                    ],
                    fixedColumns: true,
                    "ordering": false,
                    // ajax: base_url + "/backend/linkedin/profiles"
                });

                // linkedin_list.on('page.dt', function() {
                //     var linkedin_checkboxs = $('#modal_select #linkedin_list tbody [type="checkbox"]');
                //     for(var index = 0; index < linkedin_checkboxs.length; index ++) {
                //         var linkedin_checkbox_data_id = $(linkedin_checkboxs[index]).attr('data-id');
                        
                //         $(linkedin_checkboxs[index]).prop('checked', true);
                        
                //     }
                // })

                linkedin_list.on('draw', function() {
                    console.log("draw");
                    var linkedin_checkboxs = $('#modal_select #linkedin_list tbody [type="checkbox"]');
                    for(var index = 0; index < linkedin_checkboxs.length; index ++) {
                        var linkedin_checkbox_data_id = $(linkedin_checkboxs[index]).attr('data-id');
                        
                        if(reports_tmp.hasOwnProperty('linkedin')) {
                            if(reports_tmp["linkedin"].hasOwnProperty(linkedin_checkbox_data_id)) {
                                if(reports_tmp["linkedin"][linkedin_checkbox_data_id]) {
                                    $(linkedin_checkboxs[index]).prop('checked', true);
                                }
                                else {
                                    $(linkedin_checkboxs[index]).prop('checked', false);
                                }
                            }
                            else {
                                $(linkedin_checkboxs[index]).prop('checked', false);
                            }
                        }
                        else {
                            $(linkedin_checkboxs[index]).prop('checked', false);
                        }
                       
                    }
                })

                biorxiv_list = $('#biorxiv_list').DataTable({
                    responsive: true,
                    "columnDefs": [
                        { "width": 50, "targets": 1 }
                    ],
                    fixedColumns: true,
                    "ordering": false,
                });

                pubmed_list = $('#pubmed_list').DataTable({
                    responsive: true,
                    "columnDefs": [
                        { "width": 50, "targets": 1 }
                    ],
                    fixedColumns: true,
                    "ordering": false,
                });

                clinical_list = $('#clinical_list').DataTable({
                    responsive: true,
                    "columnDefs": [
                        { "width": 50, "targets": 1 }
                    ],
                    fixedColumns: true,
                    "ordering": false,
                });

                $('body').removeClass('loading');
            }
        })

    }

    load_reports();

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
        console.log(linkedin_list.rows().data());

        if(data_type == "linkedin" && data_id == "all") {

            var linkedin_list_data = linkedin_list.rows().data();
            for(var index = 0; index < linkedin_list_data.length; index ++) {
                var checkbox_dom = $($.parseHTML(linkedin_list_data[index][0])).find('[type="checkbox"]');
                linkedin_checkbox_data_id = $(checkbox_dom).attr('data-id');

                if($(this).is(':checked')) {
                    // $(linkedin_checkboxs[index]).prop('checked', true);
                    reports_tmp[data_type][linkedin_checkbox_data_id] = $(this).is(':checked');
                }
                else {
                    // $(linkedin_checkboxs[index]).prop('checked', false);
                    delete reports_tmp[data_type][linkedin_checkbox_data_id];
                }

            }

            linkedin_list.draw(false);
        }
        else {
            if($(this).is(':checked')) {
                reports_tmp[data_type][data_id] = $(this).is(':checked');
            }
            else if(reports_tmp[data_type].hasOwnProperty(data_id)){
                delete reports_tmp[data_type][data_id];
            }
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

        var html = `
            <div class="admin-manager-form-input-controls">
                <input type="text" class="admin-manager-form-input-control" placeholder="Email" name="email"></input>
                <input type="text" class="admin-manager-form-input-control" placeholder="Password" name="password"></input>
            </div>
        `;

        $('#user_list').html(html);

        $('.admin-manager-form-input-result').text("");

        $('#modal_select [type="checkbox"]').prop('checked', false);

        reports = {};

        $('#btn_dashboard_publish').attr('dashboard-id', '');
    }

    $(document).on('click', '#btn_dashboard_publish', function() {
        if($('[name="dashboard_name"]').val() == '') {
            alert('Please Input Dashboard Name');
            $('[name="dashboard_name"]').focus();
            return;
        }

        var users_wrap = $('#user_list .admin-manager-form-input-controls');

        // if($('[name="email1"]').val() == '') {
        //     alert('Please Input Email');
        //     $('[name="email1"]').focus();
        //     return;
        // }

        // if($('[name="password1"]').val() == '') {
        //     alert('Please Input User Password');
        //     $('[name="password1"]').focus();
        //     return;
        // }

        var publish_data = {
            reports: reports,
            dashboard_name: $('[name="dashboard_name"]').val(),
            users: []
        };
        
        for(var index = 0; index < users_wrap.length; index++) {
            // var user_wrap = $(users_wrap[index]);

            var email = $(users_wrap[index]).find('[name="email"]').val();
            var password = $(users_wrap[index]).find('[name="password"]').val();

            if(email != '' && password != '') {
                publish_data['users'].push({
                    email: email,
                    password: password
                })
            }
        }

        if(publish_data['users'].length == 0) {
            alert('Please Input Email/Password');
            return;
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
                    if(!resp.success) {
                        $('body').removeClass('loading');
                        alert(resp.message);
                        return;
                    }
                    alert('Published Successfully!');
                    $('#dashboard_list_table').append(resp.dashboard_tr);
    
                    clearAdminManagerForm();
                    $('body').removeClass('loading');
                }
            })
        }
        
    })

    $(document).on('click', '#btn_dashboard_new', function() {
        clearAdminManagerForm();
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

                    if(resp.dashboard.reports != null) {
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
                }

                if(resp.users) {
                    $('#user_list').html('');
                    for(var index = 1; index <= resp.users.length; index++) {
                        var html = `
                            <div class="admin-manager-form-input-controls">
                                <input type="text" class="admin-manager-form-input-control" placeholder="Email" name="email" value="` + resp.users[index - 1].email + `"></input>
                                <input type="text" class="admin-manager-form-input-control" placeholder="Password" name="password"></input>
                            </div>
                        `;
                        $('#user_list').append(html);
                        
                        // $('[name="email' + index + '"]').val(resp.users[index - 1].email);
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

    $(document).on('click', '#btn_add_user', function() {
        var html = `
            <div class="admin-manager-form-input-controls">
                <input type="text" class="admin-manager-form-input-control" placeholder="Email" name="email"></input>
                <input type="text" class="admin-manager-form-input-control" placeholder="Password" name="password"></input>
            </div>
        `;
        $('#user_list').append(html);
    })

    $(document).on('keyup', '[name="dashboard_name"]', function() {
        var name = $(this).val();
        var slug = name.toLowerCase().trim().replace(/[^\w\s-]/g, '').replace(/[\s_-]+/g, '-').replace(/^-+|-+$/g, '');
        $('#create_dashboard_url').html(base_url + slug);
    })
})(jQuery)