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
      $('.enable-external-permalinks').appendTo('.fields-content-management > table > tbody');
      $('.enable-external-permalinks-for').appendTo('.fields-content-management .enable-external-permalinks .asenha-subfields');
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
      $('.customize-admin-menu').appendTo('.fields-admin-interface > table > tbody');
      $('.custom-menu-order').appendTo('.fields-admin-interface .customize-admin-menu .asenha-subfields');
      $('.disable-dashboard-widgets').appendTo('.fields-admin-interface > table > tbody');
      $('.disabled-dashboard-widgets').appendTo('.fields-admin-interface .disable-dashboard-widgets .asenha-subfields');
      $('.hide-modify-elements').appendTo('.fields-admin-interface > table > tbody');
      $('.hide-ab-wp-logo-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-customize-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-updates-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-comments-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-new-content-menu').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-ab-howdy').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-help-drawer').appendTo('.fields-admin-interface .hide-modify-elements .asenha-subfields');
      $('.hide-admin-bar').appendTo('.fields-admin-interface > table > tbody');
      $('.hide-admin-bar-for').appendTo('.fields-admin-interface .hide-admin-bar .asenha-subfields');

      // Place fields into "Log In | Log Out" tab
      $('.change-login-url').appendTo('.fields-login-logout > table > tbody');
      $('.custom-login-slug').appendTo('.fields-login-logout .change-login-url .asenha-subfields');
      $('.enable-login-logout-menu').appendTo('.fields-login-logout > table > tbody');
      $('.enable-last-login-column').appendTo('.fields-login-logout > table > tbody');
      $('.redirect-after-login').appendTo('.fields-login-logout > table > tbody');
      $('.redirect-after-login-to-slug').appendTo('.fields-login-logout .redirect-after-login .asenha-subfields');
      $('.redirect-after-login-for').appendTo('.fields-login-logout .redirect-after-login .asenha-subfields');
      $('.redirect-after-logout').appendTo('.fields-login-logout > table > tbody');
      $('.redirect-after-logout-to-slug').appendTo('.fields-login-logout .redirect-after-logout .asenha-subfields');
      $('.redirect-after-logout-for').appendTo('.fields-login-logout .redirect-after-logout .asenha-subfields');

      // Place fields into "Custom Code" tab
      $('.enable-custom-admin-css').appendTo('.fields-custom-code > table > tbody');
      $('.custom-admin-css').appendTo('.fields-custom-code .enable-custom-admin-css .asenha-subfields');
      $('.enable-custom-frontend-css').appendTo('.fields-custom-code > table > tbody');
      $('.custom-frontend-css').appendTo('.fields-custom-code .enable-custom-frontend-css .asenha-subfields');
      $('.insert-head-body-footer-code').appendTo('.fields-custom-code > table > tbody');
      $('.head-code-priority').appendTo('.fields-custom-code .insert-head-body-footer-code .asenha-subfields');
      $('.head-code').appendTo('.fields-custom-code .insert-head-body-footer-code .asenha-subfields');
      $('.body-code-priority').appendTo('.fields-custom-code .insert-head-body-footer-code .asenha-subfields');
      $('.body-code').appendTo('.fields-custom-code .insert-head-body-footer-code .asenha-subfields');
      $('.footer-code-priority').appendTo('.fields-custom-code .insert-head-body-footer-code .asenha-subfields');
      $('.footer-code').appendTo('.fields-custom-code .insert-head-body-footer-code .asenha-subfields');
      $('.enable-custom-body-class').appendTo('.fields-custom-code > table > tbody');
      $('.enable-custom-body-class-for').appendTo('.fields-custom-code .enable-custom-body-class .asenha-subfields');
      $('.manage-ads-appads-txt').appendTo('.fields-custom-code > table > tbody');
      $('.ads-txt-content').appendTo('.fields-custom-code .manage-ads-appads-txt .asenha-subfields');
      $('.app-ads-txt-content').appendTo('.fields-custom-code .manage-ads-appads-txt .asenha-subfields');
      $('.manage-robots-txt').appendTo('.fields-custom-code > table > tbody');
      $('.robots-txt-content').appendTo('.fields-custom-code .manage-robots-txt .asenha-subfields');

      // Place fields into the "Disable Components" tab
      $('.disable-gutenberg').appendTo('.fields-disable-components > table > tbody');
      $('.disable-gutenberg-for').appendTo('.fields-disable-components .disable-gutenberg .asenha-subfields');
      $('.disable-gutenberg-frontend-styles').appendTo('.fields-disable-components .disable-gutenberg .asenha-subfields');
      $('.disable-comments').appendTo('.fields-disable-components > table > tbody');
      $('.disable-comments-for').appendTo('.fields-disable-components .disable-comments .asenha-subfields');
      $('.disable-rest-api').appendTo('.fields-disable-components > table > tbody');
      $('.disable-feeds').appendTo('.fields-disable-components > table > tbody');
      $('.disable-all-updates').appendTo('.fields-disable-components > table > tbody');
      $('.disable-smaller-components').appendTo('.fields-disable-components > table > tbody');
      $('.disable-head-generator-tag').appendTo('.fields-disable-components .disable-smaller-components .asenha-subfields');
      $('.disable-head-wlwmanifest-tag').appendTo('.fields-disable-components .disable-smaller-components .asenha-subfields');
      $('.disable-head-rsd-tag').appendTo('.fields-disable-components .disable-smaller-components .asenha-subfields');
      $('.disable-head-shortlink-tag').appendTo('.fields-disable-components .disable-smaller-components .asenha-subfields');

      // Place fields into "Security" tab
      $('.limit-login-attempts').appendTo('.fields-security > table > tbody');
      $('.login-fails-allowed').appendTo('.fields-security .limit-login-attempts .asenha-subfields');
      $('.login-lockout-maxcount').appendTo('.fields-security .limit-login-attempts .asenha-subfields');
      $('.login-attempts-log-table').appendTo('.fields-security .limit-login-attempts .asenha-subfields');
      $('.obfuscate-author-slugs').appendTo('.fields-security > table > tbody');
      $('.disable-xmlrpc').appendTo('.fields-security > table > tbody');

      // Place fields into "Optimizations" tab
      $('.image-upload-control').appendTo('.fields-optimizations > table > tbody');
      $('.image-max-width').appendTo('.fields-optimizations .image-upload-control .asenha-subfields');
      $('.image-max-height').appendTo('.fields-optimizations .image-upload-control .asenha-subfields');
      $('.enable-revisions-control').appendTo('.fields-optimizations > table > tbody');
      $('.revisions-max-number').appendTo('.fields-optimizations .enable-revisions-control .asenha-subfields');
      $('.enable-revisions-control-for').appendTo('.fields-optimizations .enable-revisions-control .asenha-subfields');
      $('.enable-heartbeat-control').appendTo('.fields-optimizations > table > tbody');
      $('.heartbeat-control-for-admin-pages').appendTo('.fields-optimizations .enable-heartbeat-control .asenha-subfields');
      $('.heartbeat-interval-for-admin-pages').appendTo('.fields-optimizations .enable-heartbeat-control .asenha-subfields');
      $('.heartbeat-control-for-post-edit').appendTo('.fields-optimizations .enable-heartbeat-control .asenha-subfields');
      $('.heartbeat-interval-for-post-edit').appendTo('.fields-optimizations .enable-heartbeat-control .asenha-subfields');
      $('.heartbeat-control-for-frontend').appendTo('.fields-optimizations .enable-heartbeat-control .asenha-subfields');
      $('.heartbeat-interval-for-frontend').appendTo('.fields-optimizations .enable-heartbeat-control .asenha-subfields');

      // Place fields into "Utilities" tab
      $('.view-admin-as-role').appendTo('.fields-utilities > table > tbody');
      $('.enable-password-protection').appendTo('.fields-utilities > table > tbody');
      $('.password-protection-password').appendTo('.fields-utilities .enable-password-protection .asenha-subfields');
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

      // Manage ads.txt and app-ads.txt=> Initialize CodeMirror
      var adsTxtTextarea = document.getElementById("admin_site_enhancements[ads_txt_content]");
      var adsTxtEditor = CodeMirror.fromTextArea(adsTxtTextarea, {
         mode: "markdown",
         lineNumbers: true,
         lineWrapping: true
      });

      adsTxtEditor.setSize("100%",300);

      var appAdsTxtTextarea = document.getElementById("admin_site_enhancements[app_ads_txt_content]");
      var appAdsTxtEditor = CodeMirror.fromTextArea(appAdsTxtTextarea, {
         mode: "markdown",
         lineNumbers: true,
         lineWrapping: true
      });

      appAdsTxtEditor.setSize("100%",300);

      // Manage robots.txt => Initialize CodeMirror
      var robotsTxtTextarea = document.getElementById("admin_site_enhancements[robots_txt_content]");
      var robotsTxtEditor = CodeMirror.fromTextArea(robotsTxtTextarea, {
         mode: "markdown",
         lineNumbers: true,
         lineWrapping: true
      });

      robotsTxtEditor.setSize("100%",400);

      // Insert <head>, <body> and <footer> code => Initialize CodeMirror
      var headCodeTextarea = document.getElementById("admin_site_enhancements[head_code]");
      var headCodeEditor = CodeMirror.fromTextArea(headCodeTextarea, {
         mode: "htmlmixed",
         lineNumbers: true,
         lineWrapping: true
      });
      headCodeEditor.setSize("100%",300);

      var bodyCodeTextarea = document.getElementById("admin_site_enhancements[body_code]");
      var bodyCodeEditor = CodeMirror.fromTextArea(bodyCodeTextarea, {
         mode: "htmlmixed",
         lineNumbers: true,
         lineWrapping: true
      });
      bodyCodeEditor.setSize("100%",300);

      var footerCodeTextarea = document.getElementById("admin_site_enhancements[footer_code]");
      var footerCodeEditor = CodeMirror.fromTextArea(footerCodeTextarea, {
         mode: "htmlmixed",
         lineNumbers: true,
         lineWrapping: true
      });
      footerCodeEditor.setSize("100%",300);

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

      $('#tab-login-logout + label').click( function() {
         $('.fields-login-logout').show();
         $('.asenha-fields:not(.fields-login-logout)').hide();
         window.location.hash = 'login-logout';
         Cookies.set('asenha_tab', 'login-logout', { expires: 1 }); // expires in 1 day
      });

      $('#tab-custom-code + label').click( function() {
         $('.fields-custom-code').show();
         $('.asenha-fields:not(.fields-custom-code)').hide();
         window.location.hash = 'custom-code';
         Cookies.set('asenha_tab', 'custom-code', { expires: 1 }); // expires in 1 day
         adminCssEditor.refresh(); // Custom Admin CSS >> CodeMirror
         frontendCssEditor.refresh(); // Custom Fronend CSS >> CodeMirror
         adsTxtEditor.refresh(); // Manage ads.txt >> CodeMirror
         appAdsTxtEditor.refresh(); // Manage app-ads.txt >> CodeMirror
         headCodeEditor.refresh(); // Insert <head>, <body> and <footer> code >> CodeMirror
         bodyCodeEditor.refresh(); // Insert <head>, <body> and <footer> code >> CodeMirror
         footerCodeEditor.refresh(); // Insert <head>, <body> and <footer> code >> CodeMirror
         robotsTxtEditor.refresh(); // Manage robots.txt >> CodeMirror
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

      $('#tab-optimizations + label').click( function() {
         $('.fields-optimizations').show();
         $('.asenha-fields:not(.fields-optimizations)').hide();
         window.location.hash = 'optimizations';
         Cookies.set('asenha_tab', 'optimizations', { expires: 1 }); // expires in 1 day
      });

      $('#tab-utilities + label').click( function() {
         $('.fields-utilities').show();
         $('.asenha-fields:not(.fields-utilities)').hide();
         window.location.hash = 'utilities';
         Cookies.set('asenha_tab', 'utilities', { expires: 1 }); // expires in 1 day
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

      // Enable External Permalinks => show/hide post types checkboxes on document ready
      if ( document.getElementById('admin_site_enhancements[enable_external_permalinks]').checked ) {
         $('.enable-external-permalinks .asenha-subfields').show();
         $('.asenha-toggle.enable-external-permalinks td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.enable-external-permalinks .asenha-subfields').hide();        
      }

      // Enable External Permalinks => show/hide post types checkboxes on toggle click
      document.getElementById('admin_site_enhancements[enable_external_permalinks]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.enable-external-permalinks .asenha-subfields').fadeIn();
            $('.enable-external-permalinks .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.enable-external-permalinks .asenha-subfields').hide();
            $('.enable-external-permalinks .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Enable SVG Upload => show/hide roles checkboxes on toggle click
      document.getElementById('admin_site_enhancements[enable_revisions_control]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.enable-revisions-control .asenha-subfields').fadeIn();
            $('.enable-revisions-control .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.enable-revisions-control .asenha-subfields').hide();
            $('.enable-revisions-control .asenha-field-with-options').toggleClass('is-enabled');
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

      // Disable Dashboard Widgets => show/hide subfields on document ready
      if ( document.getElementById('admin_site_enhancements[disable_dashboard_widgets]').checked ) {
         $('.disable-dashboard-widgets .asenha-subfields').show();
         $('.asenha-toggle.disable-dashboard-widgets td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.disable-dashboard-widgets .asenha-subfields').hide();        
      }

      // Disable Dashboard Widgets => show/hide subfields on toggle click
      document.getElementById('admin_site_enhancements[disable_dashboard_widgets]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.disable-dashboard-widgets .asenha-subfields').fadeIn();
            $('.disable-dashboard-widgets .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.disable-dashboard-widgets .asenha-subfields').hide();
            $('.disable-dashboard-widgets .asenha-field-with-options').toggleClass('is-enabled');
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

      // Disable Smaller Components => show/hide components checkboxes on document ready
      if ( document.getElementById('admin_site_enhancements[disable_smaller_components]').checked ) {
         $('.disable-smaller-components .asenha-subfields').show();
         $('.asenha-toggle.disable-smaller-components td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.disable-smaller-components .asenha-subfields').hide();        
      }

      // Smaller Components => show/hide post components checkboxes on toggle click
      document.getElementById('admin_site_enhancements[disable_smaller_components]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.disable-smaller-components .asenha-subfields').fadeIn();
            $('.disable-smaller-components .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.disable-smaller-components .asenha-subfields').hide();
            $('.disable-smaller-components .asenha-field-with-options').toggleClass('is-enabled');
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
      if ( document.getElementById('admin_site_enhancements[enable_custom_frontend_css]').checked ) {
         $('.enable-custom-frontend-css .asenha-subfields').show();
         $('.asenha-toggle.enable-custom-frontend-css td .asenha-field-with-options').addClass('is-enabled');
         frontendCssEditor.refresh();
      } else {
         $('.enable-custom-frontend-css .asenha-subfields').hide();        
      }

      // Enable Custom Frontend CSS => show/hide CSS textarea on toggle click
      document.getElementById('admin_site_enhancements[enable_custom_frontend_css]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.enable-custom-frontend-css .asenha-subfields').fadeIn();
            $('.enable-custom-frontend-css .asenha-field-with-options').toggleClass('is-enabled');
            frontendCssEditor.refresh();
         } else {
            $('.enable-custom-frontend-css .asenha-subfields').hide();
            $('.enable-custom-frontend-css .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Enable Custom Body Class => show/hide post types checkboxes on document ready
      if ( document.getElementById('admin_site_enhancements[enable_custom_body_class]').checked ) {
         $('.enable-custom-body-class .asenha-subfields').show();
         $('.asenha-toggle.enable-custom-body-class td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.enable-custom-body-class .asenha-subfields').hide();        
      }

      // Enable Custom Body Class => show/hide post types checkboxes on toggle click
      document.getElementById('admin_site_enhancements[enable_custom_body_class]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.enable-custom-body-class .asenha-subfields').fadeIn();
            $('.enable-custom-body-class .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.enable-custom-body-class .asenha-subfields').hide();
            $('.enable-custom-body-class .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Manage ads.txt and app-ads.txt => show/hide CodeMirror textarea on document ready
      if ( document.getElementById('admin_site_enhancements[manage_ads_appads_txt]').checked ) {
         $('.manage-ads-appads-txt .asenha-subfields').show();
         $('.asenha-toggle.manage-ads-appads-txt td .asenha-field-with-options').addClass('is-enabled');
         adsTxtEditor.refresh();
         appAdsTxtEditor.refresh();
      } else {
         $('.manage-ads-appads-txt .asenha-subfields').hide();        
      }

      // Manage ads.txt and app-ads.txt => show/hide CodeMirror textarea on toggle click
      document.getElementById('admin_site_enhancements[manage_ads_appads_txt]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.manage-ads-appads-txt .asenha-subfields').fadeIn();
            $('.manage-ads-appads-txt .asenha-field-with-options').toggleClass('is-enabled');
            adsTxtEditor.refresh();
            appAdsTxtEditor.refresh();
         } else {
            $('.manage-ads-appads-txt .asenha-subfields').hide();
            $('.manage-ads-appads-txt .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Manage ads.txt and app-ads.txt => show/hide CodeMirror textarea on document ready
      if ( document.getElementById('admin_site_enhancements[manage_robots_txt]').checked ) {
         $('.manage-robots-txt .asenha-subfields').show();
         $('.asenha-toggle.manage-robots-txt td .asenha-field-with-options').addClass('is-enabled');
         robotsTxtEditor.refresh();
      } else {
         $('.manage-robots-txt .asenha-subfields').hide();        
      }

      // Manage ads.txt and app-ads.txt => show/hide CodeMirror textarea on toggle click
      document.getElementById('admin_site_enhancements[manage_robots_txt]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.manage-robots-txt .asenha-subfields').fadeIn();
            $('.manage-robots-txt .asenha-field-with-options').toggleClass('is-enabled');
            robotsTxtEditor.refresh();
         } else {
            $('.manage-robots-txt .asenha-subfields').hide();
            $('.manage-robots-txt .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Insert <head>, <body> and <footer> code => show/hide CSS textarea on document ready
      if ( document.getElementById('admin_site_enhancements[insert_head_body_footer_code]').checked ) {
         $('.insert-head-body-footer-code .asenha-subfields').show();
         $('.asenha-toggle.insert-head-body-footer-code td .asenha-field-with-options').addClass('is-enabled');
         headCodeEditor.refresh();
         bodyCodeEditor.refresh();
         footerCodeEditor.refresh();
      } else {
         $('.manage-app-ads-txt .asenha-subfields').hide();        
      }

      // Insert <head>, <body> and <footer> code => show/hide CSS textarea on toggle click
      document.getElementById('admin_site_enhancements[insert_head_body_footer_code]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.insert-head-body-footer-code .asenha-subfields').fadeIn();
            $('.insert-head-body-footer-code .asenha-field-with-options').toggleClass('is-enabled');
            headCodeEditor.refresh();
            bodyCodeEditor.refresh();
            footerCodeEditor.refresh();
         } else {
            $('.insert-head-body-footer-code .asenha-subfields').hide();
            $('.insert-head-body-footer-code .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Image Upload Control => show/hide sub-fields on document ready
      if ( document.getElementById('admin_site_enhancements[image_upload_control]').checked ) {
         $('.image-upload-control .asenha-subfields').show();
         $('.asenha-toggle.image-upload-control td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.image-upload-control .asenha-subfields').hide();        
      }

      // Image Upload Control => show/hide sub-fields on toggle click
      document.getElementById('admin_site_enhancements[image_upload_control]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.image-upload-control .asenha-subfields').fadeIn();
            $('.image-upload-control .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.image-upload-control .asenha-subfields').hide();
            $('.image-upload-control .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Enable Revisions Control => show/hide roles checkboxes on document ready
      if ( document.getElementById('admin_site_enhancements[enable_revisions_control]').checked ) {
         $('.enable-revisions-control .asenha-subfields').show();
         $('.asenha-toggle.enable-revisions-control td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.enable-revisions-control .asenha-subfields').hide();        
      }

      // Enable Revisions Control => show/hide sub-fields on toggle click
      document.getElementById('admin_site_enhancements[enable_revisions_control]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.enable-revisions-control .asenha-subfields').fadeIn();
            $('.enable-revisions-control .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.enable-revisions-control .asenha-subfields').hide();
            $('.enable-revisions-control .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Enable Heartbeat Control => show/hide subfields on document ready
      if ( document.getElementById('admin_site_enhancements[enable_heartbeat_control]').checked ) {
         $('.enable-heartbeat-control .asenha-subfields').show();
         $('.asenha-toggle.enable-heartbeat-control td .asenha-field-with-options').addClass('is-enabled');
      } else {
         $('.enable-heartbeat-control .asenha-subfields').hide();        
      }

      // Enable Heartbeat Control => show/hide subfields on toggle click
      document.getElementById('admin_site_enhancements[enable_heartbeat_control]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.enable-heartbeat-control .asenha-subfields').fadeIn();
            $('.enable-heartbeat-control .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.enable-heartbeat-control .asenha-subfields').hide();
            $('.enable-heartbeat-control .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Enable Heartbeat Control => Check if "Modify interval" is chosen/clicked and show/hide the corresponding select field
      if ( $('input[name="admin_site_enhancements[heartbeat_control_for_admin_pages]"]:checked').val() == 'modify' ) {
         $('.heartbeat-interval-for-admin-pages .asenha-subfield-select-inner').show();
      }
      $('input[name="admin_site_enhancements[heartbeat_control_for_admin_pages]"]').click(function() {
         var radioValue = $(this).attr('value');
         if ( radioValue == 'modify' ) {
            $('.heartbeat-interval-for-admin-pages .asenha-subfield-select-inner').show();
         } else {
            $('.heartbeat-interval-for-admin-pages .asenha-subfield-select-inner').hide();            
         }
      });

      if ( $('input[name="admin_site_enhancements[heartbeat_control_for_post_edit]"]:checked').val() == 'modify' ) {
         $('.heartbeat-interval-for-post-edit .asenha-subfield-select-inner').show();
      }
      $('input[name="admin_site_enhancements[heartbeat_control_for_post_edit]"]').click(function() {
         var radioValue = $(this).attr('value');
         if ( radioValue == 'modify' ) {
            $('.heartbeat-interval-for-post-edit .asenha-subfield-select-inner').show();
         } else {
            $('.heartbeat-interval-for-post-edit .asenha-subfield-select-inner').hide();            
         }
      });

      if ( $('input[name="admin_site_enhancements[heartbeat_control_for_frontend]"]:checked').val() == 'modify' ) {
         $('.heartbeat-interval-for-frontend .asenha-subfield-select-inner').show();
      }
      $('input[name="admin_site_enhancements[heartbeat_control_for_frontend]"]').click(function() {
         var radioValue = $(this).attr('value');
         if ( radioValue == 'modify' ) {
            $('.heartbeat-interval-for-frontend .asenha-subfield-select-inner').show();
         } else {
            $('.heartbeat-interval-for-frontend .asenha-subfield-select-inner').hide();            
         }
      });

      // Enable Password Protection => show/hide password input on document ready
      if ( document.getElementById('admin_site_enhancements[enable_password_protection]').checked ) {
         $('.enable-password-protection .asenha-subfields').show();
         $('.asenha-toggle.enable-password-protection td .asenha-field-with-options').addClass('is-enabled');  
      } else {
         $('.enable-password-protection .asenha-subfields').hide();        
      }

      // Enable Password Protection => show/hide password input input on toggle click
      document.getElementById('admin_site_enhancements[enable_password_protection]').addEventListener('click', event => {
         if (event.target.checked) {
            $('.enable-password-protection .asenha-subfields').fadeIn();
            $('.enable-password-protection .asenha-field-with-options').toggleClass('is-enabled');
         } else {
            $('.enable-password-protection .asenha-subfields').hide();
            $('.enable-password-protection .asenha-field-with-options').toggleClass('is-enabled');
         }
      });

      // Enable Password protection => Empty field value on click, so new password can be easily entered
      var oldValue = '';
      $('input[name="admin_site_enhancements[password_protection_password]"]').focusin(function() {
         oldValue = $(this).val();
         $(this).val('');
      });

      $('input[name="admin_site_enhancements[password_protection_password]"]').focusout(function() {
         if ( $(this).val() == '' ) {
            $(this).val(oldValue);
         }
      });

   });

})( jQuery );