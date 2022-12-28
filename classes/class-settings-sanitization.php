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
		global $wp_roles, $asenha_public_post_types, $asenha_gutenberg_post_types, $asenha_revisions_post_types;
		$roles = $wp_roles->get_names();

		// =================================================================
		// CONTENT MANAGEMENT
		// =================================================================

		// Enable Page and Post Duplication
		if ( ! isset( $options['enable_duplication'] ) ) $options['enable_duplication'] = false;
		$options['enable_duplication'] = ( 'on' == $options['enable_duplication'] ? true : false );

		// Enable Media Replacement
		if ( ! isset( $options['enable_media_replacement'] ) ) $options['enable_media_replacement'] = false;
		$options['enable_media_replacement'] = ( 'on' == $options['enable_media_replacement'] ? true : false );

		// Enable SVG Upload
		if ( ! isset( $options['enable_svg_upload'] ) ) $options['enable_svg_upload'] = false;
		$options['enable_svg_upload'] = ( 'on' == $options['enable_svg_upload'] ? true : false );

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator
				if ( ! isset( $options['enable_svg_upload_for'][$role_slug] ) ) $options['enable_svg_upload_for'][$role_slug] = false;
				$options['enable_svg_upload_for'][$role_slug] = ( 'on' == $options['enable_svg_upload_for'][$role_slug] ? true : false );
			}
		}

		// Enable External Permalinks
		if ( ! isset( $options['enable_external_permalinks'] ) ) $options['enable_external_permalinks'] = false;
		$options['enable_external_permalinks'] = ( 'on' == $options['enable_external_permalinks'] ? true : false );

		if ( is_array( $asenha_public_post_types ) ) {
			foreach ( $asenha_public_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				if ( ! isset( $options['enable_external_permalinks_for'][$post_type_slug] ) ) $options['enable_external_permalinks_for'][$post_type_slug] = false;
				$options['enable_external_permalinks_for'][$post_type_slug] = ( 'on' == $options['enable_external_permalinks_for'][$post_type_slug] ? true : false );
			}
		}

		// Enable Revisions Control
		if ( ! isset( $options['enable_revisions_control'] ) ) $options['enable_revisions_control'] = false;
		$options['enable_revisions_control'] = ( 'on' == $options['enable_revisions_control'] ? true : false );

		if ( ! isset( $options['revisions_max_number'] ) ) $options['revisions_max_number'] = 10;
		$options['revisions_max_number'] = ( ! empty( $options['revisions_max_number'] ) ) ? sanitize_text_field( $options['revisions_max_number'] ) : 10;

		if ( is_array( $asenha_revisions_post_types ) ) {
			foreach ( $asenha_revisions_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, 
				if ( ! isset( $options['enable_revisions_control_for'][$post_type_slug] ) ) $options['enable_revisions_control_for'][$post_type_slug] = false;
				$options['enable_revisions_control_for'][$post_type_slug] = ( 'on' == $options['enable_revisions_control_for'][$post_type_slug] ? true : false );
			}
		}

		// Enable Auto-Publishing of Posts with Missed Schedules
		if ( ! isset( $options['enable_missed_schedule_posts_auto_publish'] ) ) $options['enable_missed_schedule_posts_auto_publish'] = false;
		$options['enable_missed_schedule_posts_auto_publish'] = ( 'on' == $options['enable_missed_schedule_posts_auto_publish'] ? true : false );
		
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

		// =================================================================
		// ADMIN INTERFACE
		// =================================================================

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
		// The following fields are added on rendering of custom_menu_order field
		if ( ! isset( $options['custom_menu_titles'] ) ) $options['custom_menu_titles'] = ''; 
		if ( ! isset( $options['custom_menu_hidden'] ) ) $options['custom_menu_hidden'] = '';

		// =================================================================
		// LOG IN | LOG OUT
		// =================================================================

		// Change Login URL
		if ( ! isset( $options['change_login_url'] ) ) $options['change_login_url'] = false;
		$options['change_login_url'] = ( 'on' == $options['change_login_url'] ? true : false );

		if ( ! isset( $options['custom_login_slug'] ) ) $options['custom_login_slug'] = 'backend';
		$options['custom_login_slug'] = ( ! empty( $options['custom_login_slug'] ) ) ? sanitize_text_field( $options['custom_login_slug'] ) : 'backend';

		// Enable Login Logout Menu
		if ( ! isset( $options['enable_login_logout_menu'] ) ) $options['enable_login_logout_menu'] = false;
		$options['enable_login_logout_menu'] = ( 'on' == $options['enable_login_logout_menu'] ? true : false );

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

		// Enable Last Login Column
		if ( ! isset( $options['enable_last_login_column'] ) ) $options['enable_last_login_column'] = false;
		$options['enable_last_login_column'] = ( 'on' == $options['enable_last_login_column'] ? true : false );

		// =================================================================
		// CUSTOM CODE
		// =================================================================

		// Enable Custom Admin CSS
		if ( ! isset( $options['enable_custom_admin_css'] ) ) $options['enable_custom_admin_css'] = false;
		$options['enable_custom_admin_css'] = ( 'on' == $options['enable_custom_admin_css'] ? true : false );

		if ( ! isset( $options['custom_admin_css'] ) ) $options['custom_admin_css'] = '';
		$options['custom_admin_css'] = ( ! empty( $options['custom_admin_css'] ) ) ? $options['custom_admin_css'] : '';

		// Enable Custom Frontend CSS
		if ( ! isset( $options['enable_custom_frontend_css'] ) ) $options['enable_custom_frontend_css'] = false;
		$options['enable_custom_frontend_css'] = ( 'on' == $options['enable_custom_frontend_css'] ? true : false );

		if ( ! isset( $options['custom_frontend_css'] ) ) $options['custom_frontend_css'] = '';
		$options['custom_frontend_css'] = ( ! empty( $options['custom_frontend_css'] ) ) ? $options['custom_frontend_css'] : '';

		// Manage ads.txt and app-ads.txt
		if ( ! isset( $options['manage_ads_appads_txt'] ) ) $options['manage_ads_appads_txt'] = false;
		$options['manage_ads_appads_txt'] = ( 'on' == $options['manage_ads_appads_txt'] ? true : false );

		if ( ! isset( $options['ads_txt_content'] ) ) $options['ads_txt_content'] = '';
		$options['ads_txt_content'] = ( ! empty( $options['ads_txt_content'] ) ) ? $options['ads_txt_content'] : '';

		if ( ! isset( $options['app_ads_txt_content'] ) ) $options['app_ads_txt_content'] = '';
		$options['app_ads_txt_content'] = ( ! empty( $options['app_ads_txt_content'] ) ) ? $options['app_ads_txt_content'] : '';

		// Manage robots.txt
		if ( ! isset( $options['manage_robots_txt'] ) ) $options['manage_robots_txt'] = false;
		$options['manage_robots_txt'] = ( 'on' == $options['manage_robots_txt'] ? true : false );

		if ( ! isset( $options['robots_txt_content'] ) ) $options['robots_txt_content'] = '';
		$options['robots_txt_content'] = ( ! empty( $options['robots_txt_content'] ) ) ? $options['robots_txt_content'] : '';

		// Insert <head>, <body> and <footer> code
		if ( ! isset( $options['insert_head_body_footer_code'] ) ) $options['insert_head_body_footer_code'] = false;
		$options['insert_head_body_footer_code'] = ( 'on' == $options['insert_head_body_footer_code'] ? true : false );

		if ( ! isset( $options['head_code_priority'] ) ) $options['head_code_priority'] = '';
		$options['head_code_priority'] = ( isset( $options['head_code_priority'] ) ) ? $options['head_code_priority'] : 10;

		if ( ! isset( $options['head_code'] ) ) $options['head_code'] = '';
		$options['head_code'] = ( ! empty( $options['head_code'] ) ) ? $options['head_code'] : '';

		if ( ! isset( $options['body_code_priority'] ) ) $options['body_code_priority'] = '';
		$options['body_code_priority'] = ( isset( $options['body_code_priority'] ) ) ? $options['body_code_priority'] : 10;

		if ( ! isset( $options['body_code'] ) ) $options['body_code'] = '';
		$options['body_code'] = ( ! empty( $options['body_code'] ) ) ? $options['body_code'] : '';

		if ( ! isset( $options['footer_code_priority'] ) ) $options['footer_code_priority'] = '';
		$options['footer_code_priority'] = ( isset( $options['footer_code_priority'] ) ) ? $options['footer_code_priority'] : 10;

		if ( ! isset( $options['footer_code'] ) ) $options['footer_code'] = '';
		$options['footer_code'] = ( ! empty( $options['footer_code'] ) ) ? $options['footer_code'] : '';

		// =================================================================
		// DISABLE COMPONENTS
		// =================================================================

		// Disable Gutenberg
		if ( ! isset( $options['disable_gutenberg'] ) ) $options['disable_gutenberg'] = false;
		$options['disable_gutenberg'] = ( 'on' == $options['disable_gutenberg'] ? true : false );

		if ( is_array( $asenha_gutenberg_post_types ) ) {
			foreach ( $asenha_gutenberg_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, 
				if ( ! isset( $options['disable_gutenberg_for'][$post_type_slug] ) ) $options['disable_gutenberg_for'][$post_type_slug] = false;
				$options['disable_gutenberg_for'][$post_type_slug] = ( 'on' == $options['disable_gutenberg_for'][$post_type_slug] ? true : false );
			}
		}

		if ( ! isset( $options['disable_gutenberg_frontend_styles'] ) ) $options['disable_gutenberg_frontend_styles'] = false;
		$options['disable_gutenberg_frontend_styles'] = ( 'on' == $options['disable_gutenberg_frontend_styles'] ? true : false );

		// Disable Comments
		if ( ! isset( $options['disable_comments'] ) ) $options['disable_comments'] = false;
		$options['disable_comments'] = ( 'on' == $options['disable_comments'] ? true : false );

		if ( is_array( $asenha_public_post_types ) ) {
			foreach ( $asenha_public_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				if ( ! isset( $options['disable_comments_for'][$post_type_slug] ) ) $options['disable_comments_for'][$post_type_slug] = false;
				$options['disable_comments_for'][$post_type_slug] = ( 'on' == $options['disable_comments_for'][$post_type_slug] ? true : false );
			}
		}

		// Disable REST API
		if ( ! isset( $options['disable_rest_api'] ) ) $options['disable_rest_api'] = false;
		$options['disable_rest_api'] = ( 'on' == $options['disable_rest_api'] ? true : false );

		// Disable Feeds
		if ( ! isset( $options['disable_feeds'] ) ) $options['disable_feeds'] = false;
		$options['disable_feeds'] = ( 'on' == $options['disable_feeds'] ? true : false );

		// =================================================================
		// SECURITY
		// =================================================================

		// Limit Login Attempts
		if ( ! isset( $options['limit_login_attempts'] ) ) $options['limit_login_attempts'] = false;
		$options['limit_login_attempts'] = ( 'on' == $options['limit_login_attempts'] ? true : false );

		if ( ! isset( $options['login_fails_allowed'] ) ) $options['login_fails_allowed'] = 3;
		$options['login_fails_allowed'] = ( ! empty( $options['login_fails_allowed'] ) ) ? sanitize_text_field( $options['login_fails_allowed'] ) : 3;

		if ( ! isset( $options['login_lockout_maxcount'] ) ) $options['login_lockout_maxcount'] = 3;
		$options['login_lockout_maxcount'] = ( ! empty( $options['login_lockout_maxcount'] ) ) ? sanitize_text_field( $options['login_lockout_maxcount'] ) : 3;

		if ( ! isset( $options['login_attempts_log_table'] ) ) $options['login_attempts_log_table'] = '';
		$options['login_attempts_log_table'] = '';
		
		// Obfuscate Author Slugs
		if ( ! isset( $options['obfuscate_author_slugs'] ) ) $options['obfuscate_author_slugs'] = false;
		$options['obfuscate_author_slugs'] = ( 'on' == $options['obfuscate_author_slugs'] ? true : false );

		// Disable XML-RPC
		if ( ! isset( $options['disable_xmlrpc'] ) ) $options['disable_xmlrpc'] = false;
		$options['disable_xmlrpc'] = ( 'on' == $options['disable_xmlrpc'] ? true : false );

		// =================================================================
		// OPTIMIZATIONS
		// =================================================================

		// Enable Heartbeat Control
		if ( ! isset( $options['enable_heartbeat_control'] ) ) $options['enable_heartbeat_control'] = false;
		$options['enable_heartbeat_control'] = ( 'on' == $options['enable_heartbeat_control'] ? true : false );

		if ( ! isset( $options['heartbeat_control_for_admin_pages'] ) ) $options['enable_heartbeat_control'] = 'default';
		if ( ! isset( $options['heartbeat_control_for_post_edit'] ) ) $options['heartbeat_control_for_post_edit'] = 'default';
		if ( ! isset( $options['heartbeat_control_for_frontend'] ) ) $options['heartbeat_control_for_frontend'] = 'default';

		if ( ! isset( $options['heartbeat_interval_for_admin_pages'] ) ) $options['heartbeat_interval_for_admin_pages'] = 60;
		$options['heartbeat_interval_for_admin_pages'] = ( ! empty( $options['heartbeat_interval_for_admin_pages'] ) ) ? sanitize_text_field( $options['heartbeat_interval_for_admin_pages'] ) : 60;

		if ( ! isset( $options['heartbeat_interval_for_post_edit'] ) ) $options['heartbeat_interval_for_post_edit'] = 15;
		$options['heartbeat_interval_for_post_edit'] = ( ! empty( $options['heartbeat_interval_for_post_edit'] ) ) ? sanitize_text_field( $options['heartbeat_interval_for_post_edit'] ) : 15;

		if ( ! isset( $options['heartbeat_interval_for_frontend'] ) ) $options['heartbeat_interval_for_frontend'] = 60;
		$options['heartbeat_interval_for_frontend'] = ( ! empty( $options['heartbeat_interval_for_frontend'] ) ) ? sanitize_text_field( $options['heartbeat_interval_for_frontend'] ) : 60;

		// =================================================================
		// UTILITIES
		// =================================================================

		// Redirect 404 to Homepage
		if ( ! isset( $options['redirect_404_to_homepage'] ) ) $options['redirect_404_to_homepage'] = false;
		$options['redirect_404_to_homepage'] = ( 'on' == $options['redirect_404_to_homepage'] ? true : false );

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