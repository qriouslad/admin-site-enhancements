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

		// Setup admin menu, admin page, admin scripts, plugin action links, etc.
	
		// Register admin menu and add the settings page.
		add_action( 'admin_menu', 'asenha_register_admin_menu' );

		// Register plugin settings
		add_action( 'admin_init', 'asenha_register_settings' );

		// Suppress all notices on the plugin's main page. Then add notification for successful settings update.
		add_action( 'admin_notices', 'asenha_notices', 5 );

		// Enqueue admin scripts and styles only on the plugin's main page
		add_action( 'admin_enqueue_scripts', 'asenha_admin_scripts' );

		// Add action links in plugins page
		add_filter( 'plugin_action_links_' . ASENHA_SLUG . '/' . ASENHA_SLUG . '.php', 'asenha_plugin_action_links' );

		// Update footer text
		add_filter( 'admin_footer_text', 'asenha_footer_text' );

		// Selectively enable enhancements based on options value

		// Get all WP Enhancements options, default to empty array in case it's not been created yet
		$options = get_option( 'admin_site_enhancements', array() );

		// Instantiate object for Content Management features
		$content_management = new ASENHA\Classes\Content_Management;

		// Content Management >> Show Featured Image Column
		if ( array_key_exists( 'show_featured_image_column', $options ) && $options['show_featured_image_column'] ) {
			add_action( 'admin_init', [ $content_management, 'show_featured_image_column' ] );
		}

		// Content Management >> Show Excerpt Column
		if ( array_key_exists( 'show_excerpt_column', $options ) && $options['show_excerpt_column'] ) {
			add_action( 'admin_init', [ $content_management, 'show_excerpt_column' ] );
		}

		// Content Management >> Show ID Column
		if ( array_key_exists( 'show_id_column', $options ) && $options['show_id_column'] ) {
			add_action( 'admin_init', [ $content_management, 'show_id_column' ] );
		}

		// Content Management >> Hide Comments Column
		if ( array_key_exists( 'hide_comments_column', $options ) && $options['hide_comments_column'] ) {
			add_action( 'admin_init', [ $content_management, 'hide_comments_column' ] );
		}

		// Content Management >> Hide Post Tags Column
		if ( array_key_exists( 'hide_post_tags_column', $options ) && $options['hide_post_tags_column'] ) {
			add_action( 'admin_init', [ $content_management, 'hide_post_tags_column' ] );
		}

		// Content Management >> Show Custom Taxonomy Filters
		if ( array_key_exists( 'show_custom_taxonomy_filters', $options ) && $options['show_custom_taxonomy_filters'] ) {
			add_action( 'restrict_manage_posts', [ $content_management, 'show_custom_taxonomy_filters' ] );
		}

		// Content Management >> Enable Page and Post Duplication
		if ( array_key_exists( 'enable_duplication', $options ) && $options['enable_duplication'] ) {
			add_action( 'admin_action_asenha_enable_duplication', [ $content_management, 'asenha_enable_duplication' ] );
			add_filter( 'page_row_actions', [ $content_management, 'add_duplication_action_link' ], 10, 2 );
			add_filter( 'post_row_actions', [ $content_management, 'add_duplication_action_link' ], 10, 2 );
		}

		// Content Management >> Enable Media Replacement
		if ( array_key_exists( 'enable_media_replacement', $options ) && $options['enable_media_replacement'] ) {
			add_filter( 'media_row_actions', [ $content_management, 'modify_media_list_table_edit_link' ], 10, 2 );
			add_filter( 'attachment_fields_to_edit', [ $content_management, 'add_media_replacement_button' ] );
			add_action( 'edit_attachment', [ $content_management, 'replace_media' ] );
			add_filter( 'post_updated_messages', [ $content_management, 'attachment_updated_custom_message' ] );
		}

		// Instantiate object for Admin Interface features
		$admin_interface = new ASENHA\Classes\Admin_Interface;

		// Admin Interface >> Hide Admin Notices
		if ( array_key_exists( 'hide_admin_notices', $options ) && $options['hide_admin_notices'] ) {
			add_action( 'all_admin_notices', [ $admin_interface, 'admin_notices_wrapper' ] );
			add_action( 'admin_bar_menu', [ $admin_interface, 'admin_notices_menu' ] );
			add_action( 'admin_enqueue_scripts', [ $admin_interface, 'admin_notices_menu_inline_css' ] ); // wp-admin
		}

		// Admin Interface >> Hide Admin Bar
		if ( array_key_exists( 'hide_admin_bar', $options ) && $options['hide_admin_bar'] && array_key_exists( 'hide_admin_bar_for', $options ) && isset( $options['hide_admin_bar_for'] ) ) {
			add_filter( 'show_admin_bar', [ $admin_interface, 'hide_admin_bar_for_roles' ] );
		}

		// Instantiate object for Security features
		$security = new ASENHA\Classes\Security;

		// Security >> Change Login URL
		if ( array_key_exists( 'change_login_url', $options ) && $options['change_login_url'] ) {
			if ( array_key_exists( 'custom_login_slug', $options ) && ! empty( $options['custom_login_slug'] ) )  {
				add_action( 'login_head', [ $security, 'redirect_on_default_login_urls' ] );
				add_action( 'init', [ $security, 'redirect_on_custom_login_url' ] );
				add_action( 'wp_login_failed', [ $security, 'redirect_to_custom_login_url' ] );
				add_action( 'wp_logout', [ $security, 'redirect_to_custom_login_url' ] );
			}
		}

		// Instantiate object for Utilities features
		$utilities = new ASENHA\Classes\Utilities;

		// Utilities >> Redirect After Login
		if ( array_key_exists( 'redirect_after_login', $options ) && $options['redirect_after_login'] ) {
			if ( array_key_exists( 'redirect_after_login_to_slug', $options ) && ! empty( $options['redirect_after_login_to_slug'] ) )  {
				if ( array_key_exists( 'redirect_after_login_for', $options ) && ! empty( $options['redirect_after_login_for'] ) )  {
					add_filter( 'login_redirect', [ $utilities, 'redirect_for_roles_after_login' ], 10, 3 );
				}

			}
		}

		
	}

}

Admin_Site_Enhancements::get_instance();