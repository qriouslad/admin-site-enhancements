(function( $ ) {
	'use strict';

     $(document).ready( function() {

        var addReview = '<a href="https://wordpress.org/plugins/admin-site-enhancements/#reviews" target="_blank" class="header-action"><span>&starf;</span> Review</a>';
        var giveFeedback = '<a href="https://wordpress.org/support/plugin/admin-site-enhancements/" target="_blank" class="header-action">&#10010; Feedback</a>';
        var donate = '<a href="https://paypal.me/qriouslad" target="_blank" class="header-action">&#9829; Donate</a>';

        $(addReview).appendTo('.csf-header-left');
        $(giveFeedback).appendTo('.csf-header-left');
        $(donate).appendTo('.csf-header-left');

     });

})( jQuery );