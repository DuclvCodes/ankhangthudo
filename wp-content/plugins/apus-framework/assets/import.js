jQuery(document).ready(function($){
    var source = '';
    
    if ( $('.apus-btn-import').data('disabled') ) {
        $(this).attr('disabled', 'disabled');
    }
    
    $('.apus-btn-import').click(function(){
        // all
        source = $('.apus-demo-import-wrapper .source-data').val();
        if ( confirm('Do you want to import demo now ?') ) {
            
            $(this).attr('disabled', 'disabled');
            $('.apus-progress-content').show();
            
            $('.first_settings span').hide();
            $('.first_settings .installing').show();
            $('.steps li').removeClass('active');
            $('.first_settings').addClass('active');

            apus_import_type('first_settings');
        }
    });

    function apus_import_type( type ) {
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'apus_import_sample',
                demo_source: source,
                ajax: 1,
                import_type: type
            },
            dataType: 'json',
            success: function (res) {
                var next = res.next;

                if ( res.status == false ) {
                    apus_import_error( res );
                    return false;
                }

                if ( next == 'done' ) {
                    apus_import_complete( type );
                    return false;
                }
                
                if ( next == 'error' ) {
                    apus_import_error( res );
                    return false;
                }

                apus_import_complete_step( type, res );
                apus_import_type( next );
            },
            error: function (html) {
                $('.apus_progress_error_message .apus-error .content').append('<p>' + html + '</p>');
                $('.apus_progress_error_message').show();
                return false;
            }
        });

        return false;
    }

    function apus_import_complete_step(type, res) {
        $( '.' + type + ' span' ).hide();
        $( '.' + type + ' .installed' ).show();

        var next = res.next;
        if ( next == 'done' ) {
            $('.apus-complete').show();
            $('.steps li').removeClass('active');
        } else {
            $('.' + next + ' span').hide();
            $('.' + next + ' .installing').show();
            $('.steps li').removeClass('active');
            $('.' + next).addClass('active');
        }
    }

    function apus_import_complete(type) {
        $( '.' + type + ' span' ).hide();
        $( '.' + type + ' .installed' ).show();
        $('.apus-complete').show();
    }

    function apus_import_error(res) {
        if ( res.msg !== undefined && res.msg != '' ) {
            $('.apus_progress_error_message .apus-error .content').append('<p>' + res.msg + '</p>');
            $('.apus_progress_error_message').show();
        }
    }

});


