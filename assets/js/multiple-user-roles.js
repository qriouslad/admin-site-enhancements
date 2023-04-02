(function( $ ) {
   'use strict';

   $(document).ready( function() {

      // Replace roles dropdown with checkboxes in user editing / creation screens
      var rolesRow = $('select#role').closest('tr');
      rolesRow.html($('.asenha-roles-temporary-container tr').html());
      $('.asenha-roles-temporary-container').remove();

   });

})( jQuery );