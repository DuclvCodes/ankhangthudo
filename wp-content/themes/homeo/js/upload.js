jQuery(document).ready(function($){
	"use strict";
	var homeo_upload;
	var homeo_selector;

	function homeo_add_file(event, selector) {

		var upload = $(".uploaded-file"), frame;
		var $el = $(this);
		homeo_selector = selector;

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( homeo_upload ) {
			homeo_upload.open();
			return;
		} else {
			// Create the media frame.
			homeo_upload = wp.media.frames.homeo_upload =  wp.media({
				// Set the title of the modal.
				title: "Select Image",

				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: "Selected",
					// Tell the button not to close the modal, since we're
					// going to refresh the page when the image is selected.
					close: false
				}
			});

			// When an image is selected, run a callback.
			homeo_upload.on( 'select', function() {
				// Grab the selected attachment.
				var attachment = homeo_upload.state().get('selection').first();

				homeo_upload.close();
				homeo_selector.find('.upload_image').val(attachment.attributes.url).change();
				if ( attachment.attributes.type == 'image' ) {
					homeo_selector.find('.homeo_screenshot').empty().hide().prepend('<img src="' + attachment.attributes.url + '">').slideDown('fast');
				}
			});

		}
		// Finally, open the modal.
		homeo_upload.open();
	}

	function homeo_remove_file(selector) {
		selector.find('.homeo_screenshot').slideUp('fast').next().val('').trigger('change');
	}
	
	$('body').on('click', '.homeo_upload_image_action .remove-image', function(event) {
		homeo_remove_file( $(this).parent().parent() );
	});

	$('body').on('click', '.homeo_upload_image_action .add-image', function(event) {
		homeo_add_file(event, $(this).parent().parent());
	});

});