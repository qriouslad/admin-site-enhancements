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
class WP_Enhancements {

	// Refers to a single instance of this class
	private static $instance = null;

	/**
	 * Creates or returns a single instance of this class
	 *
	 * @return WP_Enhancements a single instance of this class
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

		// Register admin menu and subsequently the main admin page. This is using CodeStar Framework (CSF).
		add_action( 'wpenha_csf_loaded', 'wpenha_admin_menu_page' );

		// Enqueue admin scripts and styles only on the plugin's main page
		add_action( 'admin_enqueue_scripts', 'wpenha_admin_scripts' );

		// Add action links in plugins page
		add_filter( 'plugin_action_links_' . WPENHA_SLUG . '/' . WPENHA_SLUG . '.php', 'wpenha_plugin_action_links' );

		// Remove CodeStar Framework submenu under tools
		add_action( 'admin_menu', 'wpenha_remove_codestar_submenu' );

		// Selectively enable enhancements based on options value

		// Get all WP Enhancements options, default to empty array in case it's not been created yet
		$wpenha_options = get_option( 'wp-enhancements', array() );

		// Instantiate object for Content Admin functionalities
		$content_admin = new WPENHA\Classes\Content_Admin;

		// Content Admin >> Show Featured Image Column
		if ( array_key_exists( 'show-featured-image-column', $wpenha_options ) && $wpenha_options['show-featured-image-column'] ) {
			add_action( 'admin_init', [ $content_admin, 'show_featured_image_column' ] );
		}

		// Content Admin >> Show Excerpt Column
		if ( array_key_exists( 'show-excerpt-column', $wpenha_options ) && $wpenha_options['show-excerpt-column'] ) {
			add_action( 'admin_init', [ $content_admin, 'show_excerpt_column' ] );
		}

		// Content Admin >> Show ID Column
		if ( array_key_exists( 'show-id-column', $wpenha_options ) && $wpenha_options['show-id-column'] ) {
			add_action( 'admin_init', [ $content_admin, 'show_id_column' ] );
		}

		// Content Admin >> Hide Comments Column
		if ( array_key_exists( 'hide-comments-column', $wpenha_options ) && $wpenha_options['hide-comments-column'] ) {
			add_action( 'admin_init', [ $content_admin, 'hide_comments_column' ] );
		}

		// Content Admin >> Hide Post Tags Column
		if ( array_key_exists( 'hide-post-tags-column', $wpenha_options ) && $wpenha_options['hide-post-tags-column'] ) {
			add_action( 'admin_init', [ $content_admin, 'hide_post_tags_column' ] );
		}

		// Content Admin >> Show Custom Taxonomy Filters
		if ( array_key_exists( 'show-custom-taxonomy-filters', $wpenha_options ) && $wpenha_options['show-custom-taxonomy-filters'] ) {
			add_action( 'restrict_manage_posts', [ $content_admin, 'show_custom_taxonomy_filters' ] );
		}

		// Content Admin >> Enable Page and Post Duplication
		if ( array_key_exists( 'enable-duplication', $wpenha_options ) && $wpenha_options['enable-duplication'] ) {
			add_action( 'admin_action_wpenha_enable_duplication', [ $content_admin, 'wpenha_enable_duplication' ] );
			add_filter( 'page_row_actions', [ $content_admin, 'add_duplication_action_link' ], 10, 2 );
			add_filter( 'post_row_actions', [ $content_admin, 'add_duplication_action_link' ], 10, 2 );
		}
		
	}

}

WP_Enhancements::get_instance();