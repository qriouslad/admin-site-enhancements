<?php

// We're using the singleton design pattern
// https://code.tutsplus.com/articles/design-patterns-in-wordpress-the-singleton-pattern--wp-31621
// https://carlalexander.ca/singletons-in-wordpress/
// https://torquemag.io/2016/11/singletons-wordpress-good-evil/

/**
 * Main class of the plugin used to add functionalities
 *
 * @since 1.0.0
 */
class Admin_Site_Enhancements {

	// Refers to a single instance of this class
	private static $instance = null;

	/**
	 * Creates or returns a single instance of this class
	 *
	 * @return Admin_Site_Enhancements a single instance of this class
	 * @since 1.0.0
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

	/**
	 * Initialize plugin functionalities
	 */
	private function __construct() {

		// Setup admin menu, admin page, settings, settings sections, sections fields, admin scripts, plugin action links, etc.
	
		// Register admin menu and add the settings page.
		add_action( 'admin_menu', 'asenha_register_admin_menu' );

		// Register plugin settings

		// Instantiate object for registration of settings section and fields
		$settings = new ASENHA\Classes\Settings_Sections_Fields;

		add_action( 'admin_init', [ $settings, 'register_sections_fields' ] );

		// Suppress all notices on the plugin's main page. Then add notification for successful settings update.
		add_action( 'admin_notices', 'asenha_suppress_notices', 5 );
		add_action( 'all_admin_notices', 'asenha_suppress_generic_notices', 5 );

		// Enqueue admin scripts and styles only on the plugin's main page
		add_action( 'admin_enqueue_scripts', 'asenha_admin_scripts' );

		// Enqueue public scripts and styles
		add_action( 'wp_enqueue_scripts', 'asenha_public_scripts' );

		// Add action links in plugins page
		add_filter( 'plugin_action_links_' . ASENHA_SLUG . '/' . ASENHA_SLUG . '.php', 'asenha_plugin_action_links' );

		// Update footer text
		add_filter( 'admin_footer_text', 'asenha_footer_text' );

		// ===== Activate features based on settings ===== 

		// Get all WP Enhancements options, default to empty array in case it's not been created yet
		$options = get_option( ASENHA_SLUG_U, array() );

		// =================================================================
		// CONTENT MANAGEMENT
		// =================================================================

		// Instantiate object for Content Management features
		$content_management = new ASENHA\Classes\Content_Management;

		// Enable Page and Post Duplication
		if ( array_key_exists( 'enable_duplication', $options ) && $options['enable_duplication'] ) {
			add_action( 'admin_action_asenha_enable_duplication', [ $content_management, 'asenha_enable_duplication' ] );
			add_filter( 'page_row_actions', [ $content_management, 'add_duplication_action_link' ], 10, 2 );
			add_filter( 'post_row_actions', [ $content_management, 'add_duplication_action_link' ], 10, 2 );
		}

		// Enable Media Replacement
		if ( array_key_exists( 'enable_media_replacement', $options ) && $options['enable_media_replacement'] ) {
			add_filter( 'media_row_actions', [ $content_management, 'modify_media_list_table_edit_link' ], 10, 2 );
			add_filter( 'attachment_fields_to_edit', [ $content_management, 'add_media_replacement_button' ] );
			add_action( 'edit_attachment', [ $content_management, 'replace_media' ] );
			add_filter( 'post_updated_messages', [ $content_management, 'attachment_updated_custom_message' ] );
		}

		// Enable SVG Upload
		if ( array_key_exists( 'enable_svg_upload', $options ) && $options['enable_svg_upload'] && array_key_exists( 'enable_svg_upload_for', $options ) && isset( $options['enable_svg_upload_for'] ) ) {

			global $roles_svg_upload_enabled;

			$enable_svg_upload = $options['enable_svg_upload'];
			$for_roles = $options['enable_svg_upload_for'];

			// User has role(s). Do further checks.
			if ( isset( $for_roles ) && ( count( $for_roles ) > 0 ) ) {

				// Assemble single-dimensional array of roles for which SVG upload would be enabled
				$roles_svg_upload_enabled = array();
				foreach( $for_roles as $role_slug => $svg_upload_enabled ) {
					if ( $svg_upload_enabled ) {
						$roles_svg_upload_enabled[] = $role_slug;
					}
				}

			}

			add_filter( 'upload_mimes', [ $content_management, 'add_svg_mime' ] );
			add_filter( 'wp_check_filetype_and_ext', [ $content_management, 'confirm_file_type_is_svg' ], 10, 4 );
			add_filter( 'wp_handle_upload_prefilter', [ $content_management, 'sanitize_and_maybe_allow_svg_upload' ] );
			add_filter( 'wp_generate_attachment_metadata', [ $content_management, 'generate_svg_metadata' ], 10, 3 );
			add_action( 'wp_ajax_svg_get_attachment_url', [ $content_management, 'get_svg_attachment_url' ] );
			add_filter( 'wp_prepare_attachment_for_js', [ $content_management, 'get_svg_url_in_media_library' ] );
		}

		// Enable External Permalinks
		if ( array_key_exists( 'enable_external_permalinks', $options ) && $options['enable_external_permalinks'] ) {
			if ( array_key_exists( 'enable_external_permalinks_for', $options ) && ! empty( $options['enable_external_permalinks_for'] ) )  {
				add_action( 'add_meta_boxes', [ $content_management, 'add_external_permalink_meta_box' ], 10, 2 );
				add_action( 'save_post', [ $content_management, 'save_external_permalink' ] );

				// Filter the permalink for use by get_permalink()
				add_filter( 'page_link', [ $content_management, 'use_external_permalink_for_pages' ], 20, 2 );
				add_filter( 'post_link', [ $content_management, 'use_external_permalink_for_posts' ], 20, 2 );
				add_filter( 'post_type_link', [ $content_management, 'use_external_permalink_for_posts' ], 20, 2 );

				// Enable redirection to external permalink when page/post is opened directly via it's WP permalink
				add_action( 'wp', [ $content_management, 'redirect_to_external_permalink' ] );
			}
		}

		// Enable Auto-Publishing of Posts with Missed Schedules
		if ( array_key_exists( 'enable_missed_schedule_posts_auto_publish', $options ) && $options['enable_missed_schedule_posts_auto_publish'] ) {
			add_action( 'wp_head', [ $content_management, 'publish_missed_schedule_posts' ] );
			add_action( 'admin_head', [ $content_management, 'publish_missed_schedule_posts' ] );
		}

		// Enhance List Tables
		if ( array_key_exists( 'enhance_list_tables', $options ) && $options['enhance_list_tables'] ) {

			// Show Featured Image Column
			if ( array_key_exists( 'show_featured_image_column', $options ) && $options['show_featured_image_column'] ) {
				add_action( 'admin_init', [ $content_management, 'show_featured_image_column' ] );
			}

			// Show Excerpt Column
			if ( array_key_exists( 'show_excerpt_column', $options ) && $options['show_excerpt_column'] ) {
				add_action( 'admin_init', [ $content_management, 'show_excerpt_column' ] );
			}

			// Show ID Column
			if ( array_key_exists( 'show_id_column', $options ) && $options['show_id_column'] ) {
				add_action( 'admin_init', [ $content_management, 'show_id_column' ] );
			}

			// Hide Comments Column
			if ( array_key_exists( 'hide_comments_column', $options ) && $options['hide_comments_column'] ) {
				add_action( 'admin_init', [ $content_management, 'hide_comments_column' ] );
			}

			// Hide Post Tags Column
			if ( array_key_exists( 'hide_post_tags_column', $options ) && $options['hide_post_tags_column'] ) {
				add_action( 'admin_init', [ $content_management, 'hide_post_tags_column' ] );
			}

			// Show Custom Taxonomy Filters
			if ( array_key_exists( 'show_custom_taxonomy_filters', $options ) && $options['show_custom_taxonomy_filters'] ) {
				add_action( 'restrict_manage_posts', [ $content_management, 'show_custom_taxonomy_filters' ] );
			}

		}

		// =================================================================
		// ADMIN INTERFACE
		// =================================================================

		// Instantiate object for Admin Interface features
		$admin_interface = new ASENHA\Classes\Admin_Interface;

		// Hide Admin Notices
		if ( array_key_exists( 'hide_admin_notices', $options ) && $options['hide_admin_notices'] ) {
			add_action( 'all_admin_notices', [ $admin_interface, 'admin_notices_wrapper' ] );
			add_action( 'admin_bar_menu', [ $admin_interface, 'admin_notices_menu' ] );
			add_action( 'admin_enqueue_scripts', [ $admin_interface, 'admin_notices_menu_inline_css' ] ); // wp-admin
		}

		// Hide Admin Bar
		if ( array_key_exists( 'hide_admin_bar', $options ) && $options['hide_admin_bar'] && array_key_exists( 'hide_admin_bar_for', $options ) && isset( $options['hide_admin_bar_for'] ) ) {
			add_filter( 'show_admin_bar', [ $admin_interface, 'hide_admin_bar_for_roles' ] );
		}

		// Disable Dashboard Widgets
		if ( array_key_exists( 'disable_dashboard_widgets', $options ) && $options['disable_dashboard_widgets'] ) {
			add_action( 'wp_dashboard_setup', [ $admin_interface, 'disable_dashboard_widgets' ], 99 );
		}

		// Hide or Modify Elements
		if ( array_key_exists( 'hide_modify_elements', $options ) && $options['hide_modify_elements'] ) {
			add_filter( 'admin_bar_menu', [ $admin_interface, 'modify_admin_bar_menu' ], 5 ); // priority 5 to execute earlier than the normal 10
		}

		// Customize Admin Menu

		if ( array_key_exists( 'customize_admin_menu', $options ) && $options['customize_admin_menu'] ) {
			// add_action( 'wp_ajax_save_custom_menu_order', [ $admin_interface, 'save_custom_menu_order' ] );
			// add_action( 'wp_ajax_save_hidden_menu_items', [ $admin_interface, 'save_hidden_menu_items' ] );
			if ( array_key_exists( 'custom_menu_order', $options ) ) {
				add_filter( 'custom_menu_order', '__return_true' );
				add_filter( 'menu_order', [ $admin_interface, 'render_custom_menu_order' ] );				
			}
			if ( array_key_exists( 'custom_menu_titles', $options ) ) {
				add_action( 'admin_menu', [ $admin_interface, 'apply_custom_menu_item_titles' ], 1000 );
			}
			if ( array_key_exists( 'custom_menu_hidden', $options ) ) {
				add_action( 'admin_menu', [ $admin_interface, 'hide_menu_items' ], 1001 );
				add_action( 'admin_menu', [ $admin_interface, 'add_hidden_menu_toggle' ], 1002 );
				add_action( 'admin_enqueue_scripts', [ $admin_interface, 'enqueue_toggle_hidden_menu_script' ] );
			}
		}

		// =================================================================
		// LOG IN | LOG OUT
		// =================================================================

		// Instantiate object for Log In Log Out features
		$login_logout = new ASENHA\Classes\Login_Logout;

		// Change Login URL
		if ( array_key_exists( 'change_login_url', $options ) && $options['change_login_url'] ) {
			if ( array_key_exists( 'custom_login_slug', $options ) && ! empty( $options['custom_login_slug'] ) )  {
				add_action( 'init', [ $login_logout, 'redirect_on_custom_login_url' ] );
				add_action( 'login_head', [ $login_logout, 'redirect_on_default_login_urls' ] );
				add_action( 'wp_login_failed', [ $login_logout, 'redirect_to_custom_login_url_on_login_fail' ] );
				add_action( 'wp_logout', [ $login_logout, 'redirect_to_custom_login_url_on_logout_success' ] );
			}
		}

		// Enable Login Logout Menu

		if ( array_key_exists( 'enable_login_logout_menu', $options ) && $options['enable_login_logout_menu'] ) {
			add_action( 'admin_head-nav-menus.php', [ $login_logout, 'add_login_logout_metabox' ] );
			add_filter( 'wp_setup_nav_menu_item', [ $login_logout, 'set_login_logout_menu_item_dynamic_url' ] );
			add_filter( 'wp_nav_menu_objects', [ $login_logout, 'maybe_remove_login_or_logout_menu_item' ] );
		}

		// Enable Last Login Column

		if ( array_key_exists( 'enable_last_login_column', $options ) && $options['enable_last_login_column'] ) {
			add_action( 'wp_login', [ $login_logout, 'log_login_datetime' ] );
			add_filter( 'manage_users_columns', [ $login_logout, 'add_last_login_column' ] );
			add_filter( 'manage_users_custom_column', [ $login_logout, 'show_last_login_info' ], 10, 3 );
			add_action( 'admin_print_styles-users.php', [ $login_logout, 'add_column_style' ] );			
		}

		// Redirect After Login

		if ( array_key_exists( 'redirect_after_login', $options ) && $options['redirect_after_login'] ) {
			if ( array_key_exists( 'redirect_after_login_to_slug', $options ) && ! empty( $options['redirect_after_login_to_slug'] ) )  {
				if ( array_key_exists( 'redirect_after_login_for', $options ) && ! empty( $options['redirect_after_login_for'] ) )  {
					add_filter( 'wp_login', [ $login_logout, 'redirect_for_roles_after_login' ], 5, 2 );
				}
			}
		}

		// Redirect After Logout

		if ( array_key_exists( 'redirect_after_logout', $options ) && $options['redirect_after_logout'] ) {
			if ( array_key_exists( 'redirect_after_logout_to_slug', $options ) && ! empty( $options['redirect_after_logout_to_slug'] ) )  {
				if ( array_key_exists( 'redirect_after_logout_for', $options ) && ! empty( $options['redirect_after_logout_for'] ) )  {
					add_action( 'wp_logout', [ $login_logout, 'redirect_after_logout' ], 5, 1 ); // load earlier than Change Login URL add_action
				}
			}
		}

		// =================================================================
		// CUSTOM CODE
		// =================================================================

		// Instantiate object for Custom Code features
		$custom_code = new ASENHA\Classes\Custom_Code;

		// Enable Custom Admin / Frontend CSS

		if ( array_key_exists( 'enable_custom_admin_css', $options ) && $options['enable_custom_admin_css'] ) {
			add_filter( 'admin_enqueue_scripts', [ $custom_code, 'custom_admin_css' ] );
		}

		if ( array_key_exists( 'enable_custom_frontend_css', $options ) && $options['enable_custom_frontend_css'] ) {
			add_filter( 'wp_enqueue_scripts', [ $custom_code, 'custom_frontend_css' ] );
		}

		// Manage ads.txt and app-ads.txt
		
		if ( array_key_exists( 'manage_ads_appads_txt', $options ) && $options['manage_ads_appads_txt'] ) {
			add_action( 'init', [ $custom_code, 'show_ads_appads_txt_content' ] );
		}

		// Manage robots.txt
		
		if ( array_key_exists( 'manage_robots_txt', $options ) && $options['manage_robots_txt'] ) {
			add_filter( 'robots_txt', [ $custom_code, 'maybe_show_custom_robots_txt_content' ], 10, 2 );
		}

		// Insert <head>, <body> and <footer> code
		
		if ( array_key_exists( 'insert_head_body_footer_code', $options ) && $options['insert_head_body_footer_code'] ) {

			if ( isset( $options['head_code_priority'] ) ) {
				add_action( 'wp_head', [ $custom_code, 'insert_head_code' ], $options['head_code_priority'] );
			} else {
				add_action( 'wp_head', [ $custom_code, 'insert_head_code' ], 10 );
			}

			if ( function_exists( 'wp_body_open' ) && version_compare( get_bloginfo( 'version' ), '5.2', '>=' ) ) {

				if ( isset( $options['body_code_priority'] ) ) {
					add_action( 'wp_body_open', [ $custom_code, 'insert_body_code' ], $options['body_code_priority'] );
				} else {
					add_action( 'wp_body_open', [ $custom_code, 'insert_body_code' ], 10 );
				}
			}

			if ( isset( $options['footer_code_priority'] ) ) {
				add_action( 'wp_footer', [ $custom_code, 'insert_footer_code' ], $options['footer_code_priority'] );
			} else {
				add_action( 'wp_footer', [ $custom_code, 'insert_footer_code' ], 10 );
			}

		}

		// =================================================================
		// DISABLE COMPONENTS
		// =================================================================

		// Instantiate object for Disable Components features
		$disable_components = new ASENHA\Classes\Disable_Components;

		// Disable Gutenberg
		if ( array_key_exists( 'disable_gutenberg', $options ) && $options['disable_gutenberg'] ) {
			if ( array_key_exists( 'disable_gutenberg_for', $options ) && ! empty( $options['disable_gutenberg_for'] ) )  {
				add_action( 'admin_init', [ $disable_components, 'disable_gutenberg_for_post_types_admin' ] );
				if ( array_key_exists( 'disable_gutenberg_frontend_styles', $options ) && $options['disable_gutenberg_frontend_styles'] ) {
					add_action( 'wp_enqueue_scripts', [ $disable_components, 'disable_gutenberg_for_post_types_frontend' ], 100 );
				}
			}
		}

		// Disable Comments
		if ( array_key_exists( 'disable_comments', $options ) && $options['disable_comments'] ) {
			if ( array_key_exists( 'disable_comments_for', $options ) && ! empty( $options['disable_comments_for'] ) )  {
				add_action( 'do_meta_boxes', [ $disable_components, 'disable_comments_for_post_types_edit' ] ); // also work with 'init', 'admin_init', 'wp_loaded' hooks
				add_filter( 'comments_array', [ $disable_components, 'hide_existing_comments_on_frontend' ], 10, 2 ); // hide comments
				add_filter( 'comments_open', [ $disable_components, 'close_commenting_on_frontend' ] ); // close commenting
			}
		}

		// Disable REST API
		if ( array_key_exists( 'disable_rest_api', $options ) && $options['disable_rest_api'] ) {
			if ( version_compare( get_bloginfo('version'), '4.7', '>=' ) ) {
				add_filter( 'rest_authentication_errors', [ $disable_components, 'disable_rest_api' ] );
			} else {
				// REST API 1.x
				add_filter( 'json_enabled', '__return_false' );
				add_filter( 'json_jsonp_enabled', '__return_false' );
				// REST API 2.x
				add_filter( 'rest_enabled', '__return_false' );
				add_filter( 'rest_jsonp_enabled', '__return_false' );
			}
			remove_action('wp_head', 'rest_output_link_wp_head', 10 ); // Disable REST API links in HTML <head>
			remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 ); // Disable REST API link in HTTP headers
			remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' ); // Remove REST API URL from the WP RSD endpoint.
		}

		// Disable Feeds
		if ( array_key_exists( 'disable_feeds', $options ) && $options['disable_feeds'] ) {
			remove_action( 'wp_head', 'feed_links', 2 ); // Remove feed links in <head>
			remove_action( 'wp_head', 'feed_links_extra', 3 ); // Remove feed links in <head>
			remove_action( 'do_feed_rdf', 'do_feed_rdf', 10, 0 );
			remove_action( 'do_feed_rss', 'do_feed_rss', 10, 0 );
			remove_action( 'do_feed_rss2', 'do_feed_rss2', 10, 1 );
			remove_action( 'do_feed_atom', 'do_feed_atom', 10, 1 );
		}

		// Disable All Updates
		if ( array_key_exists( 'disable_all_updates', $options ) && $options['disable_all_updates'] ) {
			add_action( 'admin_init', [ $disable_components, 'disable_update_notices_version_checks' ] );

			// Disable core update
			add_filter( 'pre_transient_update_core', [ $disable_components, 'override_version_check_info' ] );
			add_filter( 'pre_site_transient_update_core', [ $disable_components, 'override_version_check_info' ] );

			// Disable theme updates
			add_filter( 'pre_transient_update_themes', [ $disable_components, 'override_version_check_info' ] );
			add_filter( 'pre_site_transient_update_themes', [ $disable_components, 'override_version_check_info' ] );
			add_action( 'pre_set_site_transient_update_themes', [ $disable_components, 'override_version_check_info' ], 20 );

			// Disable plugin updates
			add_filter( 'pre_transient_update_plugins', [ $disable_components, 'override_version_check_info' ] );
			add_filter( 'pre_site_transient_update_plugins', [ $disable_components, 'override_version_check_info' ] );
			add_action( 'pre_set_site_transient_update_plugins', [ $disable_components, 'override_version_check_info' ], 20 );

			// Disable auto updates
			add_filter( 'automatic_updater_disabled', '__return_true' );
			if ( ! defined( 'AUTOMATIC_UPDATER_DISABLED' ) ) { 
				define( 'AUTOMATIC_UPDATER_DISABLED', true );
			}
			if ( ! defined( 'WP_AUTO_UPDATE_CORE') ) { 
				define( 'WP_AUTO_UPDATE_CORE', false );
			}

			add_filter( 'auto_update_core', '__return_false' );
			add_filter( 'wp_auto_update_core', '__return_false' );
			add_filter( 'allow_minor_auto_core_updates', '__return_false' );
			add_filter( 'allow_major_auto_core_updates', '__return_false' );
			add_filter( 'allow_dev_auto_core_updates', '__return_false' );

			add_filter( 'auto_update_plugin', '__return_false' );
			add_filter( 'auto_update_theme', '__return_false' );
			add_filter( 'auto_update_translation', '__return_false' );

			remove_action( 'init', 'wp_schedule_update_checks' );

			// Disable update emails
			add_filter( 'auto_core_update_send_email', '__return_false' );
			add_filter( 'send_core_update_notification_email', '__return_false' );
			add_filter( 'automatic_updates_send_debug_email', '__return_false' );

			// Remove Dashboard >> Updates menu
			add_action( 'admin_menu', [ $disable_components, 'remove_updates_menu' ] );

		}

		// =================================================================
		// SECURITY
		// =================================================================

		// Instantiate object for Security features
		$security = new ASENHA\Classes\Security;

		// Limit Login Attempts
		if ( array_key_exists( 'limit_login_attempts', $options ) && $options['limit_login_attempts'] ) {
			add_filter( 'authenticate', [ $security, 'maybe_allow_login' ], 999, 3 ); // Very low priority so it is processed last
			add_action( 'login_enqueue_scripts', [ $security, 'maybe_hide_login_form' ] );
			add_action( 'wp_login_failed', [ $security, 'log_failed_login' ], 5 ); // Higher priority than one in Change Login URL
			add_action( 'wp_login_errors', [ $security, 'login_error_handler' ], 999, 2 );
			add_filter( 'login_message', [ $security, 'add_failed_login_message' ] );
			add_action( 'wp_login', [ $security, 'clear_failed_login_log' ] );
		}

		// Obfuscate Author Slugs
		if ( array_key_exists( 'obfuscate_author_slugs', $options ) && $options['obfuscate_author_slugs'] ) {
			add_action( 'pre_get_posts', [ $security, 'alter_author_query' ], 10 );
			add_filter( 'author_link', [ $security, 'alter_author_link' ], 10, 3 );
			add_filter( 'rest_prepare_user', [ $security, 'alter_json_users' ], 10, 3 );
		}

		// Disable XML-RPC
		if ( array_key_exists( 'disable_xmlrpc', $options ) && $options['disable_xmlrpc'] ) {
			add_filter( 'xmlrpc_enabled', '__return_false' );
			add_filter( 'wp_xmlrpc_server_class', [ $security, 'maybe_disable_xmlrpc' ] );
		}

		// =================================================================
		// OPTIMIZATIONS
		// =================================================================

		// Instantiate object for Optimizations features
		$optimizations = new ASENHA\Classes\Optimizations;

		// Image Upload Control
		if ( array_key_exists( 'image_upload_control', $options ) && $options['image_upload_control'] ) {
			add_filter( 'wp_handle_upload', [ $optimizations, 'image_upload_handler' ] );
		}

		// Revisions Control
		if ( array_key_exists( 'enable_revisions_control', $options ) && $options['enable_revisions_control'] ) {
			add_filter( 'wp_revisions_to_keep', [ $optimizations, 'limit_revisions_to_max_number' ], 10, 2 );
		}

		// Heartbeat Control
		if ( array_key_exists( 'enable_heartbeat_control', $options ) && $options['enable_heartbeat_control'] ) {
			add_filter( 'heartbeat_settings', [ $optimizations, 'maybe_modify_heartbeat_frequency' ], 99, 2 );
			add_action( 'admin_enqueue_scripts', [ $optimizations, 'maybe_disable_heartbeat' ], 99 );
			add_action( 'wp_enqueue_scripts', [ $optimizations, 'maybe_disable_heartbeat' ], 99 );
		}

		// =================================================================
		// UTILITIES
		// =================================================================

		// Instantiate object for Utilities features
		$utilities = new ASENHA\Classes\Utilities;

		// View Admin as Role
		if ( array_key_exists( 'view_admin_as_role', $options ) && $options['view_admin_as_role'] ) {
			add_action( 'admin_bar_menu', [ $utilities, 'view_admin_as_admin_bar_menu' ], 8 ); // Priority 8 so it is next to username section
			add_action( 'init', [ $utilities, 'role_switcher_to_view_admin_as' ] );
			add_action( 'wp_die_handler', [ $utilities, 'custom_error_page_on_switch_failure' ] );
		}

		// Enable Password Protection
		if ( array_key_exists( 'enable_password_protection', $options ) && $options['enable_password_protection'] ) {
			add_action( 'plugins_loaded', [ $utilities, 'show_admin_bar_icon' ] );
			add_action( 'init', [ $utilities, 'maybe_disable_page_caching' ], 1 );
			add_action( 'template_redirect', [ $utilities, 'maybe_show_login_form' ], 0 ); // load early
			add_action( 'init', [ $utilities, 'maybe_process_login' ], 1 );
			add_action( 'asenha_password_protection_error_messages', [ $utilities, 'add_login_error_messages' ] );
			if ( function_exists( 'wp_site_icon' ) ) { // WP v4.3+
				add_action( 'asenha_password_protection_login_head', 'wp_site_icon' );
			}
		}

		// Redirect 404 to Homepage

		if ( array_key_exists( 'redirect_404_to_homepage', $options ) && $options['redirect_404_to_homepage'] ) {
			add_filter( 'wp', [ $utilities, 'redirect_404_to_homepage' ] );
		}

	}

}

Admin_Site_Enhancements::get_instance();