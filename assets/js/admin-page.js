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

      // Show all / less toggler for field options | Modified from https://codepen.io/symonsays/pen/rzgEgY
      $('.asenha-field-with-options.field-show-more > .show-more').click(function(e) {

         e.preventDefault();

         var $this = $(this);
         $this.toggleClass('show-more');

         if ($this.hasClass('show-more')) {
            $this.next().removeClass('opened',0);
            $this.html('Expand &#9660;');
         } else {
            $this.next().addClass('opened',0);
            $this.html('Collapse &#9650;');
         }

      });

      // Place fields into the "Content Management" tab
      $('.enable-duplication').appendTo('.fields-content-management tbody');
      $('.enable-media-replacement').appendTo('.fields-content-management tbody');
      $('.enhance-list-tables').appendTo('.fields-content-management tbody');
      $('.show-featured-image-column').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');
      $('.show-excerpt-column').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');
      $('.show-id-column').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');
      $('.hide-comments-column').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');
      $('.hide-post-tags-column').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');
      $('.show-custom-taxonomy-filters').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');

      // Place fields into "Admin Interface" tab
      $('.hide-admin-notices').appendTo('.fields-admin-interface tbody');
      $('.view-admin-as-role').appendTo('.fields-admin-interface tbody');
      $('.customize-admin-menu').appendTo('.fields-admin-interface tbody');
      $('.custom-menu-order').appendTo('.fields-admin-interface .customize-admin-menu .asenha-subfields');
      $('.hide-modify-elements').appendTo('.fields-admin-interface tbody');
      $('.hide-ab-wp-logo-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-customize-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-updates-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-comments-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-new-content-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-howdy').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-admin-bar').appendTo('.fields-admin-interface tbody');
      $('.hide-admin-bar-for').appendTo('.fields-admin-interface .hide-admin-bar .asenha-subfields');

      // Place fields into "Security" tab
      $('.change-login-url').appendTo('.fields-security tbody');
      $('.custom-login-slug').appendTo('.fields-security .change-login-url .asenha-subfields');
      $('.obfuscate-author-slugs').appendTo('.fields-security tbody');
      $('.disable-xmlrpc').appendTo('.fields-security tbody');

      // Place fields into "Utilities" tab
      $('.redirect-after-login').appendTo('.fields-utilities tbody');
      $('.redirect-after-login-to-slug').appendTo('.fields-utilities .redirect-after-login .asenha-subfields');
      $('.redirect-after-login-for').appendTo('.fields-utilities .redirect-after-login .asenha-subfields');
      $('.redirect-after-logout').appendTo('.fields-utilities tbody');
      $('.redirect-after-logout-to-slug').appendTo('.fields-utilities .redirect-after-logout .asenha-subfields');
      $('.redirect-after-logout-for').appendTo('.fields-utilities .redirect-after-logout .asenha-subfields');
      $('.redirect-404-to-homepage').appendTo('.fields-utilities tbody');

      // Place fields into the "Disable Components" tab

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

      $('#tab-utilities + label').click( function() {
         $('.fields-utilities').show();
         $('.asenha-fields:not(.fields-utilities)').hide();
         // window.location.hash = 'utilities';
      });

      // $('#tab-disable-components + label').click( function() {
         // $('.fields-disable-components').show();
         // $('.asenha-fields:not(.fields-disable-components)').hide();
         // window.location.hash = 'utilities';
      // });

      // Open Content Management tab on document ready
      $('#tab-content-management + label').trigger('click');

      // Open tab by URL hash. Defaults to Content Management tab.
      // var hash = decodeURI(window.location.hash).substr(1); // get hash without the # character

      // Enhance List Tables => show/hide subfields on document ready
      if ( document.getElementById('admin_site_enhancements[enhance_list_tables]').checked ) {
         $('.enhance-list-tables .asenha-subfields').show();
         $('.asenha-toggle.enhance-list-tables td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.enhance-list-tables .asenha-subfields').hide();        
      }

      // Enhance List Tables => show/hide subfields on toggle click
      document.getElementById('admin_site_enhancements[enhance_list_tables]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.enhance-list-tables .asenha-subfields').fadeIn();
            $('.enhance-list-tables .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.enhance-list-tables .asenha-subfields').hide();
            $('.enhance-list-tables .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Hide Admin Bar => show/hide roles checkboxes on document ready
      if ( document.getElementById('admin_site_enhancements[hide_admin_bar]').checked ) {
         $('.hide-admin-bar .asenha-subfields').show();
         $('.asenha-toggle.hide-admin-bar td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.hide-admin-bar .asenha-subfields').hide();        
      }

      // Hide Admin Bar => show/hide roles checkboxes on toggle click
      document.getElementById('admin_site_enhancements[hide_admin_bar]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.hide-admin-bar .asenha-subfields').fadeIn();
            $('.hide-admin-bar .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.hide-admin-bar .asenha-subfields').hide();
            $('.hide-admin-bar .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Hide or Modify Elements => show/hide subfields on document ready
      if ( document.getElementById('admin_site_enhancements[hide_modify_elements]').checked ) {
         $('.hide-modify-elements .asenha-subfields').show();
         $('.asenha-toggle.hide-modify-elements td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.hide-modify-elements .asenha-subfields').hide();        
      }

      // Hide or Modify Elements => show/hide subfields on toggle click
      document.getElementById('admin_site_enhancements[hide_modify_elements]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.hide-modify-elements .asenha-subfields').fadeIn();
            $('.hide-modify-elements .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.hide-modify-elements .asenha-subfields').hide();
            $('.hide-modify-elements .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Customize Admin Menu => show/hide subfields on document ready
      if ( document.getElementById('admin_site_enhancements[customize_admin_menu]').checked ) {
         $('.customize-admin-menu .asenha-subfields').show();
         $('.asenha-toggle.customize-admin-menu td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.customize-admin-menu .asenha-subfields').hide();        
      }

      // Customize Admin Menu => show/hide subfields on toggle click
      document.getElementById('admin_site_enhancements[customize_admin_menu]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.customize-admin-menu .asenha-subfields').fadeIn();
            $('.customize-admin-menu .asenha-field-with-options').toggleClass('is-enabled');

            // Initialize sortable elements: https://api.jqueryui.com/sortable/
            $('#custom-admin-menu').sortable();

         } else {
            $('.customize-admin-menu .asenha-subfields').hide();
            $('.customize-admin-menu .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Change Login URL => show/hide custom slug input on document ready
      if ( document.getElementById('admin_site_enhancements[change_login_url]').checked ) {
         $('.change-login-url .asenha-subfields').show();
         $('.asenha-toggle.change-login-url td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.change-login-url .asenha-subfields').hide();        
      }

      // Change Login URL => show/hide custom slug input on toggle click
      document.getElementById('admin_site_enhancements[change_login_url]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.change-login-url .asenha-subfields').fadeIn();
            $('.change-login-url .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.change-login-url .asenha-subfields').hide();
            $('.change-login-url .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Redirect After Login => show/hide roles checkboxes on document ready
      if ( document.getElementById('admin_site_enhancements[redirect_after_login]').checked ) {
         $('.redirect-after-login .asenha-subfields').show();
         $('.asenha-toggle.redirect-after-login td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.redirect-after-login .asenha-subfields').hide();        
      }

      // Redirect After Login => show/hide roles checkboxes on toggle click
      document.getElementById('admin_site_enhancements[redirect_after_login]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.redirect-after-login .asenha-subfields').fadeIn();
            $('.redirect-after-login .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.redirect-after-login .asenha-subfields').hide();
            $('.redirect-after-login .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Redirect After Logout => show/hide roles checkboxes on document ready
      if ( document.getElementById('admin_site_enhancements[redirect_after_logout]').checked ) {
         $('.redirect-after-logout .asenha-subfields').show();
         $('.asenha-toggle.redirect-after-logout td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.redirect-after-logout .asenha-subfields').hide();        
      }

      // Redirect After Logout => show/hide roles checkboxes on toggle click
      document.getElementById('admin_site_enhancements[redirect_after_logout]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.redirect-after-logout .asenha-subfields').fadeIn();
            $('.redirect-after-logout .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.redirect-after-logout .asenha-subfields').hide();
            $('.redirect-after-logout .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

   });

})( jQuery );