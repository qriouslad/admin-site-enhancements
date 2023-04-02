(function( $ ) {
	'use strict';

	$(document).ready( function() {

		// Bypass browser cache by appending query parameter to preview image src
		var imgSrcUrl = $('.wp_attachment_image .thumbnail').attr('src');
		$('.wp_attachment_image .thumbnail').attr('src', imgSrcUrl + `?v=${new Date().getTime()}`);

		// Place the Replace Media section after the side metaboxes
		$('#media-replace-div').appendTo('#postbox-container-1');

		// For 'Replace Media' buttom on the edit screen of each media/attachment
		$('#asenha-media-replace').click( function() {
			asenha_replace_media();
		});

		// https://stephanwagner.me/jBox
		// var mediaReplaceModal = new jBox('Modal', {
		// 	content: 'Content here...'
		// });

		// Open jBox modal
		// $('.asenha-media-replace').click(function(e) {
		// 	e.preventDefault();
		// 	mediaReplaceModal.open();
		// });

	});

})( jQuery );

// let asenhaMRVars = asenhaMR;

function asenha_replace_media() {

	// https://codex.wordpress.org/Javascript_Reference/wp.media
	// https://github.com/ericandrewlewis/wp-media-javascript-guide

	// Instantiate the media frame
	mediaFrame = wp.media({
		title: 'Select New Media File',
		button: {
			text: 'Perform Replacement'
		},
		multiple: false // Enable/disable multiple select
	});

	// When an image is selected in the media frame...
	mediaFrame.on('select', function() {

		// Get media attachment details from the frame state
		var attachment = mediaFrame.state().get('selection').first().toJSON();
		console.log( attachment );

		// Send the attachment id to our hidden input
		jQuery('#new-attachment-id').val(attachment.id);

		if (jQuery("#new-attachment-id").closest('.media-modal').length) {
			// Do nothing. Media frame is still open.
		} else {
			// "Perform Replacement" button has been clicked. Submit the edit form, which includes 'new-attachment-id'
			jQuery("#new-attachment-id").closest("form").submit();
		}
	});

	// Open the media dialog and store it in a variable
	var mediaFrameEl = jQuery(mediaFrame.open().el);

	// Open the "Upload files" tab on load
	mediaFrameEl.find('#menu-item-upload').click();

}