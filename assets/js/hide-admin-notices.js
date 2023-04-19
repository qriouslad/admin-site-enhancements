(function( $ ) {
   'use strict';

   $(document).ready( function() {

		// Move admin notices at an interval after document is ready and WP core moves the position of notices under the page heading.
	   	// https://plugins.trac.wordpress.org/browser/hide-admin-notices/tags/1.2.2/assets/js/hide-admin-notices.js
	   	// https://plugins.trac.wordpress.org/browser/admin-notices-manager/tags/1.3.1/assets/js/admin/notices.js

		var noticesPanel = $('.asenha-admin-notices-drawer');
		var startTime = new Date().getTime();

		var interval = setInterval(function () {

			// Stop monitoring after 5 seconds
			if (new Date().getTime() - startTime > 1000) {
				clearInterval(interval);

		   		// Count hidden notices and append into admin bar counter
		   		var noticesCount = $('.asenha-admin-notices-drawer > div').length;

		   		if ( noticesCount > 0 ) {
		   			$('.asenha-admin-notices-menu').show(); // show admin bar menu
		   			// $('.asenha-admin-notices-counter').show(); // show counter
			   		$('.asenha-admin-notices-counter').html(noticesCount); // insert count
		   			$('.asenha-admin-notices-counter').css("opacity", "1"); // show counter
		   		} else {
		   			$('.asenha-admin-notices-menu').hide(); // hide admin bar menu
		   		}

				return;
			}

			// Plugins that outputs notices. For testing.
			// Ajax Press - https://wordpress.org/plugins/ajax-press/
			// Atlas Search - https://wordpress.org/plugins/atlas-search/
			// ExactMetrics Analytics - https://wordpress.org/plugins/google-analytics-dashboard-for-wp/
			// JetPack - https://wordpress.org/plugins/jetpack/
			// ManageWP Worker - https://wordpress.org/plugins/worker/
			// WP Smushit - https://wordpress.org/plugins/wp-smushit/
			// FluentSMTP - https://wordpress.org/plugins/fluent-smtp/
			// WP Backend File Search - https://wordpress.org/plugins/wp-backend-file-search-editor-tweaks/
			// TotalPress Custom post types - https://wordpress.org/plugins/custom-post-types/

			// Reposition notices with the following selectors. Excluding 'notice-system'.
			$('#wpbody-content > .wrap > .notice:not(.system-notice),'
			+ '#wpbody-content > .wrap > .notice-error,'
			+ '#wpbody-content > .wrap > .error,'
			+ '#wpbody-content > .wrap > .notice-info,'
			+ '#wpbody-content > .wrap > .notice-information,'
			+ '#wpbody-content > .wrap > #message,'
			+ '#wpbody-content > .wrap > .notice-warning'
			+ '#wpbody-content > .wrap > .notice-success,'
			+ '#wpbody-content > .wrap > .notice-updated,'
			+ '#wpbody-content > .wrap > .updated,'
			+ '#wpbody-content > .wrap > .update-nag,'
			+ '#wpbody-content > .wrap > div > .notice:not(.system-notice),'
			+ '#wpbody-content > .wrap > div > .notice-error,'
			+ '#wpbody-content > .wrap > div > .error,'
			+ '#wpbody-content > .wrap > div > .notice-info,'
			+ '#wpbody-content > .wrap > div > .notice-information,'
			+ '#wpbody-content > .wrap > div > #message,'
			+ '#wpbody-content > .wrap > div > .notice-warning'
			+ '#wpbody-content > .wrap > div > .notice-success,'
			+ '#wpbody-content > .wrap > div > .notice-updated,'
			+ '#wpbody-content > .wrap > div > .updated,'
			+ '#wpbody-content > .wrap > div > .update-nag,'
			+ '#wpbody-content > .update-nag,' // LearnDash
			+ '#wpbody-content > .notice,' // LearnDash
			+ '#wpbody-content > .jp-connection-banner' // Jetpack
			).detach()
			.appendTo(noticesPanel)
			.show();

		}, 250);

   		// Set up the side drawer that holds the hidden admin notices: https://stephanwagner.me/jBox

   		var noticesModal = new jBox('Modal', {
   			attach: '.asenha-admin-notices-menu',
   			trigger: 'click', // or 'mouseenter'
   			// content: 'Test'
   			content: $('.asenha-admin-notices-drawer'),
   			width: 1118, // pixels
   			closeButton: 'box',
   			addClass: 'admin-notices-modal',
   			overlayClass: 'admin-notices-modal-overlay',
   			target: '#wpwrap', // where to anchor the modal
   			position: {
   				x: 'right',
   				y: 'top'
   			},
   			// fade: 1000,
   			animation: {
   				open: 'slide:bottom',
   				close: 'slide:bottom'
   			}
   		});

   });

})( jQuery );