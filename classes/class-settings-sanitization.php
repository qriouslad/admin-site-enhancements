<?php

namespace ASENHA\Classes;

/**
 * Class related to sanitization of settings fields for saving as options
 *
 * @since 2.2.0
 */
class Settings_Sanitization {

	/**
	 * Sanitize options
	 *
	 * @since 1.0.0
	 */
	function sanitize_for_options( $options ) {

		// Call WordPress globals required for validating the fields	
		global $wp_roles;
		$roles = $wp_roles->get_names();

		// Content Management features

		// Enable Page and Post Duplication
		if ( ! isset( $options['enable_duplication'] ) ) $options['enable_duplication'] = false;
		$options['enable_duplication'] = ( 'on' == $options['enable_duplication'] ? true : false );

		// Enable Media Replacement
		if ( ! isset( $options['enable_media_replacement'] ) ) $options['enable_media_replacement'] = false;
		$options['enable_media_replacement'] = ( 'on' == $options['enable_media_replacement'] ? true : false );

		// Enhance List Tables
		if ( ! isset( $options['enhance_list_tables'] ) ) $options['enhance_list_tables'] = false;
		$options['enhance_list_tables'] = ( 'on' == $options['enhance_list_tables'] ? true : false );

		// Show Featured Image Column
		if ( ! isset( $options['show_featured_image_column'] ) ) $options['show_featured_image_column'] = false;
		$options['show_featured_image_column'] = ( 'on' == $options['show_featured_image_column'] ? true : false );

		// Show Excerpt Column
		if ( ! isset( $options['show_excerpt_column'] ) ) $options['show_excerpt_column'] = false;
		$options['show_excerpt_column'] = ( 'on' == $options['show_excerpt_column'] ? true : false );

		// Show ID Column
		if ( ! isset( $options['show_id_column'] ) ) $options['show_id_column'] = false;
		$options['show_id_column'] = ( 'on' == $options['show_id_column'] ? true : false );

		// Hide Comments Column
		if ( ! isset( $options['hide_comments_column'] ) ) $options['hide_comments_column'] = false;
		$options['hide_comments_column'] = ( 'on' == $options['hide_comments_column'] ? true : false );

		// Hide Post Tags Column
		if ( ! isset( $options['hide_post_tags_column'] ) ) $options['hide_post_tags_column'] = false;
		$options['hide_post_tags_column'] = ( 'on' == $options['hide_post_tags_column'] ? true : false );

		// Show Custom Taxonomy Filters
		if ( ! isset( $options['show_custom_taxonomy_filters'] ) ) $options['show_custom_taxonomy_filters'] = false;
		$options['show_custom_taxonomy_filters'] = ( 'on' == $options['show_custom_taxonomy_filters'] ? true : false );

		// Admin Interface features

		// Hide Admin Notices
		if ( ! isset( $options['hide_admin_notices'] ) ) $options['hide_admin_notices'] = false;
		$options['hide_admin_notices'] = ( 'on' == $options['hide_admin_notices'] ? true : false );

		// Hide Admin Bar
		if ( ! isset( $options['hide_admin_bar'] ) ) $options['hide_admin_bar'] = false;
		$options['hide_admin_bar'] = ( 'on' == $options['hide_admin_bar'] ? true : false );

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator
				if ( ! isset( $options['hide_admin_bar_for'][$role_slug] ) ) $options['hide_admin_bar_for'][$role_slug] = false;
				$options['hide_admin_bar_for'][$role_slug] = ( 'on' == $options['hide_admin_bar_for'][$role_slug] ? true : false );
			}
		}

		// View Admin as Role
		if ( ! isset( $options['view_admin_as_role'] ) ) $options['view_admin_as_role'] = false;
		$options['view_admin_as_role'] = ( 'on' == $options['view_admin_as_role'] ? true : false );

		// Hide or Modify Elements

		if ( ! isset( $options['hide_modify_elements'] ) ) $options['hide_modify_elements'] = false;
		$options['hide_modify_elements'] = ( 'on' == $options['hide_modify_elements'] ? true : false );

		if ( ! isset( $options['hide_ab_wp_logo_menu'] ) ) $options['hide_ab_wp_logo_menu'] = false;
		$options['hide_ab_wp_logo_menu'] = ( 'on' == $options['hide_ab_wp_logo_menu'] ? true : false );

		if ( ! isset( $options['hide_ab_customize_menu'] ) ) $options['hide_ab_customize_menu'] = false;
		$options['hide_ab_customize_menu'] = ( 'on' == $options['hide_ab_customize_menu'] ? true : false );

		if ( ! isset( $options['hide_ab_comments_menu'] ) ) $options['hide_ab_comments_menu'] = false;
		$options['hide_ab_comments_menu'] = ( 'on' == $options['hide_ab_comments_menu'] ? true : false );

		if ( ! isset( $options['hide_ab_updates_menu'] ) ) $options['hide_ab_updates_menu'] = false;
		$options['hide_ab_updates_menu'] = ( 'on' == $options['hide_ab_updates_menu'] ? true : false );

		if ( ! isset( $options['hide_ab_new_content_menu'] ) ) $options['hide_ab_new_content_menu'] = false;
		$options['hide_ab_new_content_menu'] = ( 'on' == $options['hide_ab_new_content_menu'] ? true : false );

		if ( ! isset( $options['hide_ab_howdy'] ) ) $options['hide_ab_howdy'] = false;
		$options['hide_ab_howdy'] = ( 'on' == $options['hide_ab_howdy'] ? true : false );

		// Customize Admin Menu

		if ( ! isset( $options['customize_admin_menu'] ) ) $options['customize_admin_menu'] = false;
		$options['customize_admin_menu'] = ( 'on' == $options['customize_admin_menu'] ? true : false );

		if ( ! isset( $options['custom_menu_order'] ) ) $options['custom_menu_order'] = '';
		if ( ! isset( $options['custom_menu_hidden'] ) ) $options['custom_menu_hidden'] = '';

		// Security features

		// Change Login URL
		if ( ! isset( $options['change_login_url'] ) ) $options['change_login_url'] = false;
		$options['change_login_url'] = ( 'on' == $options['change_login_url'] ? true : false );

		if ( ! isset( $options['custom_login_slug'] ) ) $options['custom_login_slug'] = 'backend';
		$options['custom_login_slug'] = ( ! empty( $options['custom_login_slug'] ) ) ? sanitize_text_field( $options['custom_login_slug'] ) : 'backend';
		
		// Obfuscate Author Slugs
		if ( ! isset( $options['obfuscate_author_slugs'] ) ) $options['obfuscate_author_slugs'] = false;
		$options['obfuscate_author_slugs'] = ( 'on' == $options['obfuscate_author_slugs'] ? true : false );

		// Utilities features

		// Redirect After Login
		if ( ! isset( $options['redirect_after_login'] ) ) $options['redirect_after_login'] = false;
		$options['redirect_after_login'] = ( 'on' == $options['redirect_after_login'] ? true : false );

		if ( ! isset( $options['redirect_after_login_to_slug'] ) ) $options['redirect_after_login_to_slug'] = '';
		$options['redirect_after_login_to_slug'] = ( ! empty( $options['redirect_after_login_to_slug'] ) ) ? sanitize_text_field( $options['redirect_after_login_to_slug'] ) : '';

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator
				if ( ! isset( $options['redirect_after_login_for'][$role_slug] ) ) $options['redirect_after_login_for'][$role_slug] = false;
				$options['redirect_after_login_for'][$role_slug] = ( 'on' == $options['redirect_after_login_for'][$role_slug] ? true : false );
			}
		}

		// Redirect After Logout
		if ( ! isset( $options['redirect_after_logout'] ) ) $options['redirect_after_logout'] = false;
		$options['redirect_after_logout'] = ( 'on' == $options['redirect_after_logout'] ? true : false );

		if ( ! isset( $options['redirect_after_logout_to_slug'] ) ) $options['redirect_after_logout_to_slug'] = '';
		$options['redirect_after_logout_to_slug'] = ( ! empty( $options['redirect_after_logout_to_slug'] ) ) ? sanitize_text_field( $options['redirect_after_logout_to_slug'] ) : '';

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator
				if ( ! isset( $options['redirect_after_logout_for'][$role_slug] ) ) $options['redirect_after_logout_for'][$role_slug] = false;
				$options['redirect_after_logout_for'][$role_slug] = ( 'on' == $options['redirect_after_logout_for'][$role_slug] ? true : false );
			}
		}

		// Redirect 404 to Homepage
		if ( ! isset( $options['redirect_404_to_homepage'] ) ) $options['redirect_404_to_homepage'] = false;
		$options['redirect_404_to_homepage'] = ( 'on' == $options['redirect_404_to_homepage'] ? true : false );

		// Disable XML-RPC
		if ( ! isset( $options['disable_xmlrpc'] ) ) $options['disable_xmlrpc'] = false;
		$options['disable_xmlrpc'] = ( 'on' == $options['disable_xmlrpc'] ? true : false );

		return $options;

	}

	/**
	 * Sanitize checkbox field. For reference purpose. Not currently in use.
	 *
	 * @since 1.0.0
	 */
	function asenha_sanitize_checkbox_field( $value ) {

		// A checked checkbox field will originally be saved as an 'on' value in the option. We transform that into true (shown as 1) or false (shown as empty value)
		return 'on' === $value ? true : false;

	}

}