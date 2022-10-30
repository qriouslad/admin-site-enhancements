(function( $ ) {
   'use strict';

   $(document).ready( function() {

      // Make page header sticky on scroll. Using https://github.com/AndrewHenderson/jSticky
      $('#asenha-header').sticky({
         topSpacing: 0, // Space between element and top of the viewport (in pixels)
         zIndex: 100, // z-index
         stopper: '', // Id, class, or number value
         stickyClass: 'asenha-sticky' // Class applied to element when it's stuck. Class name or false.
      })

      // Clicking on header save button triggers click of the hidden form submit button
      $('.asenha-save-button').click( function(e) {
         e.preventDefault();
         $('input[type="submit"]').click();
      });

      // Place fields into the "Content Management" tab
      $('.enable-duplication').appendTo('.fields-content-management tbody');
      $('.enable-media-replacement').appendTo('.fields-content-management tbody');
      $('.show-featured-image-column').appendTo('.fields-content-management tbody');
      $('.show-excerpt-column').appendTo('.fields-content-management tbody');
      $('.show-id-column').appendTo('.fields-content-management tbody');
      $('.hide-comments-column').appendTo('.fields-content-management tbody');
      $('.hide-post-tags-column').appendTo('.fields-content-management tbody');
      $('.show-custom-taxonomy-filters').appendTo('.fields-content-management tbody');

      // Place fields into "Admin Interface" tab
      $('.hide-admin-notices').appendTo('.fields-admin-interface tbody');
      $('.hide-admin-bar').appendTo('.fields-admin-interface tbody');
      $('.hide-admin-bar-for').appendTo('.fields-admin-interface .hide-admin-bar .asenha-subfields');

      // Place fields into "Security" tab
      $('.change-login-url').appendTo('.fields-security tbody');
      $('.custom-login-slug').appendTo('.fields-security .change-login-url .asenha-subfields');

      // Remove empty .form-table that originally holds the fields
      const formTableCount = $('.form-table').length;
      // $('.form-table')[formTableCount-1].remove();

      // Show and hide corresponding fields on tab clicks

      $('#tab-content-management + label').click( function() {
         $('.fields-content-management').show();
         $('.asenha-fields:not(.fields-content-management)').hide();
         // window.location.hash = 'content-management';
      });

      $('#tab-admin-interface + label').click( function() {
         $('.fields-admin-interface').show();
         $('.asenha-fields:not(.fields-admin-interface)').hide();
         // window.location.hash = 'admin-interface';
      });

      $('#tab-security + label').click( function() {
         $('.fields-security').show();
         $('.asenha-fields:not(.fields-security)').hide();
         // window.location.hash = 'security';
      });

      // Open Content Management tab on document ready
      $('#tab-content-management + label').trigger('click');

      // Open tab by URL hash. Defaults to Content Management tab.
      // var hash = decodeURI(window.location.hash).substr(1); // get hash without the # character

      // Hide Admin Bar => show/hide roles checkboxes on document ready
      if ( document.getElementById('admin_site_enhancements[hide_admin_bar]').checked ) {
         $('.hide-admin-bar .asenha-subfields').show();
      } else {
         $('.hide-admin-bar .asenha-subfields').hide();        
      }

      // Hide Admin Bar => show/hide roles checkboxes on toggle click
      document.getElementById('admin_site_enhancements[hide_admin_bar]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.hide-admin-bar .asenha-subfields').fadeIn();
         } else {
            $('.hide-admin-bar .asenha-subfields').fadeOut();
         }
      });

      // Change Login URL => show/hide custom slug input on document ready
      if ( document.getElementById('admin_site_enhancements[change_login_url]').checked ) {
         $('.change-login-url .asenha-subfields').show();
      } else {
         $('.change-login-url .asenha-subfields').hide();        
      }

      // Change Login URL => show/hide custom slug input on toggle click
      document.getElementById('admin_site_enhancements[change_login_url]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.change-login-url .asenha-subfields').fadeIn();
         } else {
            $('.change-login-url .asenha-subfields').fadeOut();
         }
      });

   });

})( jQuery );