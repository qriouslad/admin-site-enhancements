(function( $ ) {
   'use strict';

   $(document).ready( function() {

      $('#toplevel_page_asenha_hide_hidden_menu').hide();

      // Show hidden menu items

      $('#toplevel_page_asenha_show_hidden_menu a').on( 'click', function(e) {
         e.preventDefault();
         $('#toplevel_page_asenha_show_hidden_menu').hide();
         $('#toplevel_page_asenha_hide_hidden_menu').show();
         $('.menu-top.asenha_hidden_menu').toggleClass('hidden');
         $('.wp-menu-separator.asenha_hidden_menu').toggleClass('hidden');
         $(document).trigger('wp-window-resized');         
      });

      // Hide menu items set for hiding

      $('#toplevel_page_asenha_hide_hidden_menu a').on( 'click', function(e) {
         e.preventDefault();
         $('#toplevel_page_asenha_show_hidden_menu').show();
         $('#toplevel_page_asenha_hide_hidden_menu').hide();
         $('.menu-top.asenha_hidden_menu').toggleClass('hidden');
         $('.wp-menu-separator.asenha_hidden_menu').toggleClass('hidden');
         $(document).trigger('wp-window-resized');         
      });

   });

})( jQuery );