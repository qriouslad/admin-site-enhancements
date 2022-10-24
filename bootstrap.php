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

		// Add notification for successful settings update
		add_action( 'admin_notices', 'asenha_success_notice' );

		// Enqueue admin scripts and styles only on the plugin's main page
		add_action( 'admin_enqueue_scripts', 'asenha_admin_scripts' );

		// Add action links in plugins page
		add_filter( 'plugin_action_links_' . ASENHA_SLUG . '/' . ASENHA_SLUG . '.php', 'asenha_plugin_action_links' );

		// Update footer text
		add_filter( 'admin_footer_text', 'asenha_footer_text' );

		// Selectively enable enhancements based on options value

		// Get all WP Enhancements options, default to empty array in case it's not been created yet
		$options = get_option( 'admin_site_enhancements', array() );

		// Instantiate object for Content Admin functionalities
		$content_admin = new ASENHA\Classes\Content_Admin;

		// Content Admin >> Show Featured Image Column
		if ( array_key_exists( 'show_featured_image_column', $options ) && $options['show_featured_image_column'] ) {
			add_action( 'admin_init', [ $content_admin, 'show_featured_image_column' ] );
		}

		// Content Admin >> Show Excerpt Column
		if ( array_key_exists( 'show_excerpt_column', $options ) && $options['show_excerpt_column'] ) {
			add_action( 'admin_init', [ $content_admin, 'show_excerpt_column' ] );
		}

		// Content Admin >> Show ID Column
		if ( array_key_exists( 'show_id_column', $options ) && $options['show_id_column'] ) {
			add_action( 'admin_init', [ $content_admin, 'show_id_column' ] );
		}

		// Content Admin >> Hide Comments Column
		if ( array_key_exists( 'hide_comments_column', $options ) && $options['hide_comments_column'] ) {
			add_action( 'admin_init', [ $content_admin, 'hide_comments_column' ] );
		}

		// Content Admin >> Hide Post Tags Column
		if ( array_key_exists( 'hide_post_tags_column', $options ) && $options['hide_post_tags_column'] ) {
			add_action( 'admin_init', [ $content_admin, 'hide_post_tags_column' ] );
		}

		// Content Admin >> Show Custom Taxonomy Filters
		if ( array_key_exists( 'show_custom_taxonomy_filters', $options ) && $options['show_custom_taxonomy_filters'] ) {
			add_action( 'restrict_manage_posts', [ $content_admin, 'show_custom_taxonomy_filters' ] );
		}

		// Content Admin >> Enable Page and Post Duplication
		if ( array_key_exists( 'enable_duplication', $options ) && $options['enable_duplication'] ) {
			add_action( 'admin_action_asenha_enable_duplication', [ $content_admin, 'asenha_enable_duplication' ] );
			add_filter( 'page_row_actions', [ $content_admin, 'add_duplication_action_link' ], 10, 2 );
			add_filter( 'post_row_actions', [ $content_admin, 'add_duplication_action_link' ], 10, 2 );
		}

		// Content Admin >> Enable Media Replacement
		if ( array_key_exists( 'enable_media_replacement', $options ) && $options['enable_media_replacement'] ) {
			add_filter( 'media_row_actions', [ $content_admin, 'modify_media_list_table_edit_link' ], 10, 2 );
			add_filter( 'attachment_fields_to_edit', [ $content_admin, 'add_media_replacement_button' ] );
			add_action( 'edit_attachment', [ $content_admin, 'replace_media' ] );
			add_filter( 'post_updated_messages', [ $content_admin, 'attachment_updated_custom_message' ] );
		}
		
	}

}

Admin_Site_Enhancements::get_instance();