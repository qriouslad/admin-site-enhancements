(function( $ ) {
   'use strict';

   $(document).ready( function() {

      // console.log( phpVars );

      // When External Permalinks is enabled, remove #new_tab from external permalinks and set target to _blank
      if ( phpVars.externalPermalinksEnabled ) {

         $("a").each(function() {
            var url = $(this).attr("href");
            var target = $(this).attr("target");

            if ( url != null ) {
               if ( url.indexOf("#new_tab") >= 0 ) {
                  url = url.replace("#new_tab", "");
                  target = "_blank";
                  $(this).attr("href", url);
                  $(this).attr("target", target);
                  $(this).attr("rel", "noopener noreferrer nofollow")
               }
            }

         });

      }

   });

})( jQuery );