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

         // Get current tab's URL hash and save it in cookie
         var hash = decodeURI(window.location.hash).substr(1); // get hash without the # character
         Cookies.set('asenha_tab', hash, { expires: 1 }); // expires in 1 day

         // Submit the settings form
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

      // Initialize data tables
      var table = $("#login-attempts-log").DataTable({
         pageLength: 10
      });

      // Place fields into the "Content Management" tab
      $('.enable-duplication').appendTo('.fields-content-management > table > tbody');
      $('.enable-media-replacement').appendTo('.fields-content-management > table > tbody');
      $('.enable-svg-upload').appendTo('.fields-content-management > table > tbody');
      $('.enable-svg-upload-for').appendTo('.fields-content-management .enable-svg-upload .asenha-subfields');
      $('.enable-missed-schedule-posts-auto-publish').appendTo('.fields-content-management > table > tbody');
      $('.enhance-list-tables').appendTo('.fields-content-management > table > tbody');
      $('.show-featured-image-column').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');
      $('.show-excerpt-column').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');
      $('.show-id-column').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');
      $('.hide-comments-column').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');
      $('.hide-post-tags-column').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');
      $('.show-custom-taxonomy-filters').appendTo('.fields-content-management .enhance-list-tables .asenha-subfields');

      // Place fields into "Admin Interface" tab
      $('.hide-admin-notices').appendTo('.fields-admin-interface > table > tbody');
      $('.view-admin-as-role').appendTo('.fields-admin-interface > table > tbody');
      $('.customize-admin-menu').appendTo('.fields-admin-interface > table > tbody');
      $('.custom-menu-order').appendTo('.fields-admin-interface .customize-admin-menu .asenha-subfields');
      $('.hide-modify-elements').appendTo('.fields-admin-interface > table > tbody');
      $('.hide-ab-wp-logo-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-customize-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-updates-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-comments-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-new-content-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-howdy').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-admin-bar').appendTo('.fields-admin-interface > table > tbody');
      $('.hide-admin-bar-for').appendTo('.fields-admin-interface .hide-admin-bar .asenha-subfields');

      // Place fields into the "Disable Components" tab
      $('.disable-gutenberg').appendTo('.fields-disable-components > table > tbody');
      $('.disable-gutenberg-for').appendTo('.fields-disable-components .disable-gutenberg .asenha-subfields');
      $('.disable-gutenberg-frontend-styles').appendTo('.fields-disable-components .disable-gutenberg .asenha-subfields');
      $('.disable-comments').appendTo('.fields-disable-components > table > tbody');
      $('.disable-comments-for').appendTo('.fields-disable-components .disable-comments .asenha-subfields');
      $('.disable-rest-api').appendTo('.fields-disable-components > table > tbody');
      $('.disable-feeds').appendTo('.fields-disable-components > table > tbody');

      // Place fields into "Security" tab
      $('.change-login-url').appendTo('.fields-security > table > tbody');
      $('.custom-login-slug').appendTo('.fields-security .change-login-url .asenha-subfields');
      $('.limit-login-attempts').appendTo('.fields-security > table > tbody');
      $('.login-fails-allowed').appendTo('.fields-security .limit-login-attempts .asenha-subfields');
      $('.login-lockout-maxcount').appendTo('.fields-security .limit-login-attempts .asenha-subfields');
      $('.login-attempts-log-table').appendTo('.fields-security .limit-login-attempts .asenha-subfields');
      $('.obfuscate-author-slugs').appendTo('.fields-security > table > tbody');
      $('.disable-xmlrpc').appendTo('.fields-security > table > tbody');

      // Place fields into "Utilities" tab
      $('.enable-custom-admin-css').appendTo('.fields-utilities > table > tbody');
      $('.custom-admin-css').appendTo('.fields-utilities .enable-custom-admin-css .asenha-subfields');
      $('.enable-custom-frontend-css').appendTo('.fields-utilities > table > tbody');
      $('.custom-frontend-css').appendTo('.fields-utilities .enable-custom-frontend-css .asenha-subfields');
      $('.redirect-after-login').appendTo('.fields-utilities > table > tbody');
      $('.redirect-after-login-to-slug').appendTo('.fields-utilities .redirect-after-login .asenha-subfields');
      $('.redirect-after-login-for').appendTo('.fields-utilities .redirect-after-login .asenha-subfields');
      $('.redirect-after-logout').appendTo('.fields-utilities > table > tbody');
      $('.redirect-after-logout-to-slug').appendTo('.fields-utilities .redirect-after-logout .asenha-subfields');
      $('.redirect-after-logout-for').appendTo('.fields-utilities .redirect-after-logout .asenha-subfields');
      $('.redirect-404-to-homepage').appendTo('.fields-utilities > table > tbody');

      // Remove empty .form-table that originally holds the fields
      const formTableCount = $('.form-table').length;
      // $('.form-table')[formTableCount-1].remove();

      // Enable Custom Admin CSS => Initialize CodeMirror
      var adminCssTextarea = document.getElementById("admin_site_enhancements[custom_admin_css]");
      var adminCssEditor = CodeMirror.fromTextArea(adminCssTextarea, {
         mode: "css",
         lineNumbers: true,
         lineWrapping: true
      });

      adminCssEditor.setSize("100%",600);

      // Enable Custom Frontend CSS => Initialize CodeMirror
      var frontendCssTextarea = document.getElementById("admin_site_enhancements[custom_frontend_css]");
      var frontendCssEditor = CodeMirror.fromTextArea(frontendCssTextarea, {
         mode: "css",
         lineNumbers: true,
         lineWrapping: true
      });

      frontendCssEditor.setSize("100%",600);

      // Show and hide corresponding fields on tab clicks

      $('#tab-content-management + label').click( function() {
         $('.fields-content-management').show();
         $('.asenha-fields:not(.fields-content-management)').hide();
         window.location.hash = 'content-management';
         Cookies.set('asenha_tab', 'content-management', { expires: 1 }); // expires in 1 day
      });

      $('#tab-admin-interface + label').click( function() {
         $('.fields-admin-interface').show();
         $('.asenha-fields:not(.fields-admin-interface)').hide();
         window.location.hash = 'admin-interface';
         Cookies.set('asenha_tab', 'admin-interface', { expires: 1 }); // expires in 1 day
      });

      $('#tab-disable-components + label').click( function() {
         $('.fields-disable-components').show();
         $('.asenha-fields:not(.fields-disable-components)').hide();
         window.location.hash = 'disable-components';
         Cookies.set('asenha_tab', 'disable-components', { expires: 1 }); // expires in 1 day
      });

      $('#tab-security + label').click( function() {
         $('.fields-security').show();
         $('.asenha-fields:not(.fields-security)').hide();
         window.location.hash = 'security';
         Cookies.set('asenha_tab', 'security', { expires: 1 }); // expires in 1 day
      });

      $('#tab-utilities + label').click( function() {
         $('.fields-utilities').show();
         $('.asenha-fields:not(.fields-utilities)').hide();
         window.location.hash = 'utilities';
         Cookies.set('asenha_tab', 'utilities', { expires: 1 }); // expires in 1 day
         adminCssEditor.refresh(); // Custom Admin CSS >> CodeMirror
         frontendCssEditor.refresh(); // Custom Fronend CSS >> CodeMirror
      });

      // Open tab set in 'asenha_tab' cookie set on saving changes. Defaults to content-management tab when cookie is empty
      var asenhaTabHash = Cookies.get('asenha_tab');

      if (typeof asenhaTabHash === 'undefined') {
         $('#tab-content-management + label').trigger('click');         
      } else {
         $('#tab-' + asenhaTabHash + ' + label').trigger('click');         
      }

      // Enable SVG Upload => show/hide roles checkboxes on document ready
      if ( document.getElementById('admin_site_enhancements[enable_svg_upload]').checked ) {
         $('.enable-svg-upload .asenha-subfields').show();
         $('.asenha-toggle.enable-svg-upload td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.enable-svg-upload .asenha-subfields').hide();        
      }

      // Enable SVG Upload => show/hide roles checkboxes on toggle click
      document.getElementById('admin_site_enhancements[enable_svg_upload]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.enable-svg-upload .asenha-subfields').fadeIn();
            $('.enable-svg-upload .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.enable-svg-upload .asenha-subfields').hide();
            $('.enable-svg-upload .asenha-field-with-options').toggleClass('is-enabled');
         }
      });


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

      // Disable Gutenberg => show/hide post type checkboxes on document ready
      if ( document.getElementById('admin_site_enhancements[disable_gutenberg]').checked ) {
         $('.disable-gutenberg .asenha-subfields').show();
         $('.asenha-toggle.disable-gutenberg td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.disable-gutenberg .asenha-subfields').hide();        
      }

      // Disable Gutenberg => show/hide post type checkboxes on toggle click
      document.getElementById('admin_site_enhancements[disable_gutenberg]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.disable-gutenberg .asenha-subfields').fadeIn();
            $('.disable-gutenberg .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.disable-gutenberg .asenha-subfields').hide();
            $('.disable-gutenberg .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Disable Comments => show/hide post type checkboxes on document ready
      if ( document.getElementById('admin_site_enhancements[disable_comments]').checked ) {
         $('.disable-comments .asenha-subfields').show();
         $('.asenha-toggle.disable-comments td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.disable-comments .asenha-subfields').hide();        
      }

      // Disable Comments => show/hide post type checkboxes on toggle click
      document.getElementById('admin_site_enhancements[disable_comments]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.disable-comments .asenha-subfields').fadeIn();
            $('.disable-comments .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.disable-comments .asenha-subfields').hide();
            $('.disable-comments .asenha-field-with-options').toggleClass('is-enabled');
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

      // Limit Login Attempts => show/hide custom slug input on document ready
      if ( document.getElementById('admin_site_enhancements[limit_login_attempts]').checked ) {
         $('.limit-login-attempts .asenha-subfields').show();
         $('.asenha-toggle.limit-login-attempts td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.limit-login-attempts .asenha-subfields').hide();        
      }

      // Limit Login Attempts => show/hide custom slug input on toggle click
      document.getElementById('admin_site_enhancements[limit_login_attempts]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.limit-login-attempts .asenha-subfields').fadeIn();
            $('.limit-login-attempts .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.limit-login-attempts .asenha-subfields').hide();
            $('.limit-login-attempts .asenha-field-with-options').toggleClass('is-enabled');
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

      // Enable Custom Admin CSS => show/hide CSS textarea on document ready
      if ( document.getElementById('admin_site_enhancements[enable_custom_admin_css]').checked ) {
         $('.enable-custom-admin-css .asenha-subfields').show();
         $('.asenha-toggle.enable-custom-admin-css td .asenha-field-with-options').addClass('is-enabled');
         adminCssEditor.refresh();
      } else {
         $('.enable-custom-admin-css .asenha-subfields').hide();        
      }

      // Enable Custom Admin CSS => show/hide CSS textarea on toggle click
      document.getElementById('admin_site_enhancements[enable_custom_admin_css]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.enable-custom-admin-css .asenha-subfields').fadeIn();
            $('.enable-custom-admin-css .asenha-field-with-options').toggleClass('is-enabled');
            adminCssEditor.refresh();
         } else {
            $('.enable-custom-admin-css .asenha-subfields').hide();
            $('.enable-custom-admin-css .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Enable Custom Frontend CSS => show/hide CSS textarea on document ready
      if ( document.getElementById('admin_site_enhancements[enable_custom_admin_css]').checked ) {
         $('.enable-custom-frontend-css .asenha-subfields').show();
         $('.asenha-toggle.enable-custom-frontend-css td .asenha-field-with-options').addClass('is-enabled');
         frontendCssEditor.refresh();
      } else {
         $('.enable-custom-frontend-css .asenha-subfields').hide();        
      }

      // Enable Custom Frontend CSS => show/hide CSS textarea on toggle click
      document.getElementById('admin_site_enhancements[enable_custom_admin_css]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.enable-custom-frontend-css .asenha-subfields').fadeIn();
            $('.enable-custom-frontend-css .asenha-field-with-options').toggleClass('is-enabled');
            frontendCssEditor.refresh();
         } else {
            $('.enable-custom-frontend-css .asenha-subfields').hide();
            $('.enable-custom-frontend-css .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

   });

})( jQuery );