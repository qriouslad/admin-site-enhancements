(function( $ ) {
   'use strict';

   $(document).ready( function() {
      var itemList = $('#item-list'), // Container of item list
          maxLevel = 6,
          sort_started = {}, // For data related to the dragged element when dragging started
          sort_finished = {}; // For data related to the dragged element when dragging has finished

      // console.log(contentOrderSort); // Data passed from PHP via wp_localize_script
      if (contentOrderSort.hierarchical == 'false') {
         maxLevel = 1;
      }

      // Make item list into nested sortable
      // Ref: https://api.jqueryui.com/sortable/
      // Ref: https://github.com/ilikenwf/nestedSortable
      itemList.nestedSortable({
         // Disable nesting if set to true
         protectRoot: true,
         // Forces the placeholder to have a size.
         forcePlaceholderSize: true,
         // Restricts sort start click to the specified element.
         // Allows for a helper element to be used for dragging display.
         // If set to "clone", then the element will be cloned and the clone will be dragged.
         helper: 'clone',
         listType: 'ul',
         items: 'li',
         toleranceElement: '> div', // Direct children of the li element
         handle: 'div', // The same <div> for toleranceElement is set as the drag handle
         // Specifies which mode to use for testing whether the item being moved is hovering over another item.
         // If set to 'pointr', is when the mouse pointer overlaps the other item.
         tolerance: 'pointer',
         // The maximum depth of nested items the list can accept.
         maxLevels: maxLevel,
         // Defines the opacity of the helper while sorting.
         opacity: 0.6,
         placeholder: 'ui-sortable-placeholder',
         // Whether the sortable items should revert to their new positions using a smooth animation.
         // If set as a number, it's in miliseconds
         revert: 250,
         // How far right or left (in pixels) the item has to travel 
         // in order to be nested or to be sent outside its current list. Default: 20
         tabSize: 25, 
         // This event is triggered when sorting starts.
         start: function (event, ui) {
            sort_started.item = ui.item; // The jQuery object representing the current dragged element.
            sort_started.prev = ui.item.prev(':not(".ui-sortable-placeholder")');
            sort_started.next = ui.item.next(':not(".ui-sortable-placeholder")');
         },
         // This event is triggered when the user stopped sorting and the DOM position has changed.
         update: function (event, ui) {
            // Elements of the "Updating order..." notice
            var updateNotice = $('#updating-order-notice'), // Wrapper
                spinner = $('#spinner-img'), // Spinner
                updateSuccess = $('.updating-order-notice .dashicons.dashicons-saved'); // Check mark

            ui.item.find('div.row-content:first').append(updateNotice);
            
            // Reset the state of the "Updating order..." indicator
            $(spinner).show();
            $(updateSuccess).hide();
            $(updateNotice).css('background-color','#eee').fadeIn();
            
            // Get the end items where the item was placed
            sort_finished.item = ui.item; // The jQuery object representing the current dragged element.
            sort_finished.prev = ui.item.prev(':not(".ui-sortable-placeholder")');
            sort_finished.next = ui.item.next(':not(".ui-sortable-placeholder")');

            var list_offset = parseInt(sort_finished.item.index());
            sort_finished.item.attr('data-menu-order', list_offset);
            
            // Get attributes
            var attributes = {};
            $.each(sort_finished.item[0].attributes, function () {
               attributes[this.name] = this.value;
            });
            // console.log('attributes: ' + cleanStringify(attributes));
            
            // Data for ajax call
            var dataArgs = {
               action: contentOrderSort.action, // from wp_localize_script
               item_parent: 0, // We only deal with top-level items, not child items
               start: 0, // Start processing menu_order update in DB from item with menu_order defined here
               nonce: contentOrderSort.nonce,
               post_id: sort_finished.item.attr('data-id'),
               menu_order: sort_finished.item.attr('data-menu-order'),
               excluded_items: {},
               post_type: sort_started.item.attr('data-post-type'),
               attributes: attributes,
            };
            // console.log('dataArgs: ' + cleanStringify(dataArgs));
            
            // AJAX call to update menu_order for items in the list
            $.ajax({
               type: "POST",
               url: ajaxurl,
               data: dataArgs,
               success: function(response) {
                  // console.log(response);
                  // Update the state of the "Updating order..." indicator
                  $(spinner).hide();
                  $(updateSuccess).show();
                  $(updateNotice).css('background-color','#cce5cc').delay(1000).fadeOut();
               },
               error: function(errorThrown) {
                  console.log(errorThrown);
               }
            });
         }
      });
   });

   // Convert object to simpler string for console.log. Ref: https://stackoverflow.com/a/48845206
   // function cleanStringify(object) {
   //     if (object && typeof object === 'object') {
   //         object = copyWithoutCircularReferences([object], object);
   //     }
   //     return JSON.stringify(object);

   //     function copyWithoutCircularReferences(references, object) {
   //         var cleanObject = {};
   //         Object.keys(object).forEach(function(key) {
   //             var value = object[key];
   //             if (value && typeof value === 'object') {
   //                 if (references.indexOf(value) < 0) {
   //                     references.push(value);
   //                     cleanObject[key] = copyWithoutCircularReferences(references, value);
   //                     references.pop();
   //                 } else {
   //                     cleanObject[key] = '###_Circular_###';
   //                 }
   //             } else if (typeof value !== 'function') {
   //                 cleanObject[key] = value;
   //             }
   //         });
   //         return cleanObject;
   //     }
   // }

})( jQuery );