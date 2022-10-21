(function($) {
    $(document).ready(function() {
        // alert('test');
    })
    
    $(document).on('click', '.report-header-btn-switch', function() {
        $('.report-header-btn-switch').removeClass('active');
        $(this).addClass('active');

        $('.reports-wrap').hide();
        $($(this).attr('data-target')).show();
    })
})(jQuery)