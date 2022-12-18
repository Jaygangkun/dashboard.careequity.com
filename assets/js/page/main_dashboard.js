(function($) {
    $(document).ready(function() {
        // alert('test');
    })
    
    $(document).on('click', '.report-header-btn-switch', function() {
        var parent_wrap = $(this).parents('.report-header-wrap');
        if($(parent_wrap).hasClass('disabled')) {
            return;
        }

        $('.report-header-btn-switch').removeClass('active');
        $(this).addClass('active');

        $('.reports-wrap').hide();
        $($(this).attr('data-target')).show();
    })

    function loadUpdates() {
        if(typeof linkedin_reports_ids != 'undefined') {
            $.ajax({
                url: base_url + '/PublicApi/GetGroupProfileUpdates.ashx',
                type: 'post',
                data: {
                    'profile_ids': linkedin_reports_ids
                },
                dataType: 'json',
                success: function(resp) {
                    if(resp.success) {
                        $('#report_count_linkedin').text(resp.count + ' Updates');
                        if(resp.count == 0) {
                            $('#report_count_linkedin').addClass('disable');
                        }
                        else {
                            $('#report_count_linkedin').removeClass('disable');
                        }
                    }
                    else {
                        $('#report_count_linkedin').text('No Updates');
                        $('#report_count_linkedin').addClass('disable');
                    }
                }
            })
        }
        else {
            $('#report_count_linkedin').text('No Accounts');
            $('#report_count_linkedin').addClass('disable');
        }

        if(typeof biorxiv_reports_ids != 'undefined') {
            $.ajax({
                url: base_url + '/biorxiv/admin_api/report_updates',
                type: 'post',
                data: {
                    report_ids: biorxiv_reports_ids
                },
                dataType: 'json',
                success: function(resp) {
                    if(resp.success) {
                        $('#report_count_biorxiv').text(resp.count + ' Updates');
                        if(resp.count == 0) {
                            $('#report_count_biorxiv').addClass('disable');
                        }
                        else {
                            $('#report_count_biorxiv').removeClass('disable');
                        }

                        
                    }
                    else {
                        $('#report_count_biorxiv').text('No Updates');
                        $('#report_count_biorxiv').addClass('disable');
                    }
                }
            })
        }
        else {
            $('#report_count_biorxiv').text('No Reports');
            $('#report_count_biorxiv').addClass('disable');
        }

        if(typeof pubmed_reports_ids != 'undefined') {
            $.ajax({
                url: base_url + '/pubmed/admin_api/report_updates',
                type: 'post',
                data: {
                    report_ids: pubmed_reports_ids
                },
                dataType: 'json',
                success: function(resp) {
                    if(resp.success) {
                        $('#report_count_pubmed').text(resp.count + ' Updates');
                        if(resp.count == 0) {
                            $('#report_count_pubmed').addClass('disable');
                        }
                        else {
                            $('#report_count_pubmed').removeClass('disable');
                        }

                        
                    }
                    else {
                        $('#report_count_pubmed').text('No Updates');
                        $('#report_count_pubmed').addClass('disable');
                    }
                }
            })
        }
        else {
            $('#report_count_pubmed').text('No Reports');
            $('#report_count_pubmed').addClass('disable');
        }

        if(typeof clinical_reports_ids != 'undefined') {
            $.ajax({
                url: base_url + '/clinical/admin_api/report_updates',
                type: 'post',
                data: {
                    report_ids: clinical_reports_ids
                },
                dataType: 'json',
                success: function(resp) {
                    if(resp.success) {
                        $('#report_count_clinical').text(resp.count + ' Updates');
                        if(resp.count == 0) {
                            $('#report_count_clinical').addClass('disable');
                        }
                        else {
                            $('#report_count_clinical').removeClass('disable');
                        }

                        
                    }
                    else {
                        $('#report_count_clinical').text('No Updates');
                        $('#report_count_clinical').addClass('disable');
                    }
                }
            })
        }
        else {
            $('#report_count_clinical').text('No Reports');
            $('#report_count_clinical').addClass('disable');
        }

    }

    loadUpdates();
})(jQuery)