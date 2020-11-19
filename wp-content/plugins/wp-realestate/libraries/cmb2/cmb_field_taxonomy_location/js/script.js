(function ($) {
	'use strict';

	$('.wpre_taxonomy_location').each(function () {
		var allowclear = $(this).data('allowclear');
		var width = $(this).data('width') ? $(this).data('width') : '100%';
		$(this).select2({
			allowClear: allowclear,
			width: width
		});
	});

	$.fn.extend({
		select2_sortable: function () {
			var select = $(this);
			$(select).select2();
			var ul = $(select).next('.select2-container').first('ul.select2-selection__rendered');
			ul.sortable({
				containment: 'parent',
				items      : 'li:not(.select2-search--inline)',
				tolerance  : 'pointer',
				stop       : function () {
					$($(ul).find('.select2-selection__choice').get().reverse()).each(function () {
						var id = $(this).data('data').id;
						var option = select.find('option[value="' + id + '"]')[0];
						$(select).prepend(option);
					});
				}
			});
		}
	});

	// Before a new group row is added, destroy Select2. We'll reinitialise after the row is added
	$('.cmb-repeatable-group').on('cmb2_add_group_row_start', function (event, instance) {
		var $table = $(document.getElementById($(instance).data('selector')));
		var $oldRow = $table.find('.cmb-repeatable-grouping').last();

		$oldRow.find('.wpre_taxonomy_location').each(function () {
			$(this).select2('destroy');
		});
	});

	// When a new group row is added, clear selection and initialise Select2
	$('.cmb-repeatable-group').on('cmb2_add_row', function (event, newRow) {
		$(newRow).find('.wpre_taxonomy_location').each(function () {
			$('option:selected', this).removeAttr("selected");
			var allowclear = $(this).data('allowclear');
			var width = $(this).data('width') ? $(this).data('width') : '100%';
			$(this).select2({
				allowClear: allowclear,
				width: width
			});
		});

		// Reinitialise the field we previously destroyed
		$(newRow).prev().find('.wpre_taxonomy_location').each(function () {
			var allowclear = $(this).data('allowclear');
			var width = $(this).data('width') ? $(this).data('width') : '100%';
			$(this).select2({
				allowClear: allowclear,
				width: width
			});
		});

	});

	// Before a group row is shifted, destroy Select2. We'll reinitialise after the row shift
	$('.cmb-repeatable-group').on('cmb2_shift_rows_start', function (event, instance) {
		var groupWrap = $(instance).closest('.cmb-repeatable-group');
		groupWrap.find('.wpre_taxonomy_location').each(function () {
			$(this).select2('destroy');
		});

	});

	// When a group row is shifted, reinitialise Select2
	$('.cmb-repeatable-group').on('cmb2_shift_rows_complete', function (event, instance) {
		var groupWrap = $(instance).closest('.cmb-repeatable-group');
		groupWrap.find('.wpre_taxonomy_location').each(function () {
			var allowclear = $(this).data('allowclear');
			var width = $(this).data('width') ? $(this).data('width') : '100%';
			$(this).select2({
				allowClear: allowclear,
				width: width
			});
		});

	});

	// Location Change
    $('body').on('change', 'select.wpre_taxonomy_location', function(){
        var val = $(this).val();
        var next = $(this).data('next');
        var main_select = 'select.wpre_taxonomy_location' + next;
        if ( $(main_select).length > 0 ) {
            var width = $(main_select).data('width') ? $(main_select).data('width') : '100%';
            var allowclear = $(main_select).data('allowclear');
            
            $(main_select).prop('disabled', true);
            $(main_select).val('').trigger('change');

            if ( val ) {
            	$(main_select).parent().addClass('loading');
                $.ajax({
                    url: location_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wpre_process_change_location' ),
                    type:'POST',
                    dataType: 'json',
                    data:{
                        'action': 'wpre_process_change_location',
                        'parent': val,
                        'taxonomy': $(main_select).data('taxonomy'),
                        'security': location_opts.ajax_nonce,
                    }
                }).done(function(data) {
                    $(main_select).parent().removeClass('loading');
                    
                    $(main_select).find('option').remove();
                    if ( data ) {
                    	$.each(data, function(i, item) {
                    		var option = new Option(item.name, item.id, true, true);
    						$(main_select).append(option);
						});
                    }
                    $(main_select).prop("disabled", false);
                    $(main_select).val(null).select2("destroy").select2({
						allowClear: allowclear,
						width: width
					});
                });
            } else {
                $(main_select).find('option').remove();
                $(main_select).prop("disabled", false);
                $(main_select).val(null).select2("destroy").select2({
					allowClear: allowclear,
					width: width
				});
            }
        }
    });


})(jQuery);