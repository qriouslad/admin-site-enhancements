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

      // Content Management fields tab
      $('.enable-duplication').appendTo('.fields-content-management tbody');
      $('.enable-media-replacement').appendTo('.fields-content-management tbody');
      $('.show-featured-image-column').appendTo('.fields-content-management tbody');
      $('.show-excerpt-column').appendTo('.fields-content-management tbody');
      $('.show-id-column').appendTo('.fields-content-management tbody');
      $('.hide-comments-column').appendTo('.fields-content-management tbody');
      $('.hide-post-tags-column').appendTo('.fields-content-management tbody');
      $('.show-custom-taxonomy-filters').appendTo('.fields-content-management tbody');

      // Admin Interface fields tab
      $('.hide-admin-notices').appendTo('.fields-admin-interface tbody');

      // Remove empty .form-table that originally holds the fields
      const formTableCount = $('.form-table').length;
      $('.form-table')[formTableCount-1].remove();

      // Show fields on tab clicks
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

      // Open Content Management tab
      $('#tab-content-management + label').trigger('click');

      // Open tab by URL hash. Defaults to Content Management tab.
      // var hash = decodeURI(window.location.hash).substr(1); // get hash without the # character

   });

})( jQuery );