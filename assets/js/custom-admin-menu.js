(function( $ ) {
   'use strict';

   $(document).ready( function() {


      // Initialize sortable elements: https://api.jqueryui.com/sortable/
      $('#custom-admin-menu').sortable();

      // Save custom order into a comma-separated string, triggerred after each drag and drop of menu item
      // https://api.jqueryui.com/sortable/#event-update
      // https://api.jqueryui.com/sortable/#method-toArray
      $('#custom-admin-menu').on( 'sortupdate', function( event, ui) {
         let menuOrder = $('#custom-admin-menu').sortable("toArray").toString();
         // console.log( menuOrder );

         // Set hidden input value
         document.getElementById('admin_site_enhancements[custom_menu_order]').value = menuOrder;

         jQuery.ajax({
            url: ajaxurl,
            data: {
               'action': 'save_custom_menu_order',
               'menu_order': menuOrder
            },
            success:function(data) {
               var data = data.slice(0,-1); // remove strange trailing zero in string returned by AJAX call
               var dataObj = JSON.parse(data);
               console.log(dataObj.message )
            },
            error:function(errorThrown) {
               console.log(errorThrown);
            }
         });

      });

   });

})( jQuery );