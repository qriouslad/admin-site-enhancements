<?php

namespace ASENHA\CLasses;

/**
 * Class related to the Disable Components feature
 *
 * @since 2.2.0
 */
class Disable_Components {

	/**
	 * Disable the XML-RPC component
	 *
	 * @since 2.2.0
	 */
	public function maybe_disable_xmlrpc( $data ) {

		http_response_code(403);
		exit('You don\'t have permission to access this file.');

	}

	/**
	 * Disable comments for post types
	 *
	 * @since 2.7.0
	 */
	public function disable_comments_for_post_types_edit() {

		$options = get_option( ASENHA_SLUG_U );
		$disable_comments_for = $options['disable_comments_for'];

		foreach ( $disable_comments_for as $post_type_slug => $is_commenting_disabled ) {
			if ( $is_commenting_disabled ) {
				remove_post_type_support( $post_type_slug, 'comments' );
				remove_post_type_support( $post_type_slug, 'trackbacks' );
				remove_meta_box( 'commentstatusdiv', $post_type_slug, 'normal' );
				remove_meta_box( 'commentstatusdiv', $post_type_slug, 'side' );
				remove_meta_box( 'commentsdiv', $post_type_slug, 'normal' );
				remove_meta_box( 'commentsdiv', $post_type_slug, 'side' );
			}
		}

	}

	/**
	 * Hide existing comments from the frontend post
	 *
	 * @since 2.7.0
	 */
	public function hide_existing_comments_on_frontend( $comments, $post_id ) {
		$options = get_option( ASENHA_SLUG_U );
		$disable_comments_for = $options['disable_comments_for'];
		$current_post_type = get_post_type();

		foreach ( $disable_comments_for as $post_type_slug => $is_commenting_disabled ) {
			if ( ( $current_post_type === $post_type_slug ) && $is_commenting_disabled ) {
				return array(); // return an empty array instead of the existing comments array
			} else {
				return $comments;
			}
		}
	}

	/**
	 * Close commenting on the frontend
	 *
	 * @since 2.7.0
	 */
	public function close_commenting_on_frontend() {
		$options = get_option( ASENHA_SLUG_U );
		$disable_comments_for = $options['disable_comments_for'];
		$current_post_type = get_post_type();

		foreach ( $disable_comments_for as $post_type_slug => $is_commenting_disabled ) {
			if ( ( $current_post_type === $post_type_slug ) && $is_commenting_disabled ) {
				return false; // close commenting
			} else {
				return true; // keep commenting open
			}
		}
	}

}