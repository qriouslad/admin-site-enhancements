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

		// Register admin menu and subsequently the main admin page. This is using CodeStar Framework (CSF).
		add_action( 'wpenha_csf_loaded', [ $this, 'register_admin_menu_page' ] );

		// Remove CodeStar Framework submenu under tools
		add_action( 'admin_menu', [ $this, 'remove_codestar_submenu' ] );

		// Add action links in plugins page
		add_filter( 'plugin_action_links_' . WPENHA_SLUG . '/' . WPENHA_SLUG . '.php', [ $this, 'add_plugin_action_links' ] );

		// Enqueue admin scripts and styles only on the plugin's main page
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

		// Get all WP Enhancements options, default to empty array in case it's not been created yet
		$wpenha_options = get_option( 'wp-enhancements', array() );

		// Instantiate object for Content Admin functionalities
		$content_admin = new WPENHA\Classes\Content_Admin;

		// Content Admin >> Show IDs
		if ( array_key_exists( 'show-ids', $wpenha_options ) && $wpenha_options['show-ids'] ) {
			add_action( 'admin_init', [ $content_admin, 'show_ids' ], 10, 1 );
		}
		
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since 1.0.0
	 */
	public function admin_scripts() {

		// For main page of this plugin

		if ( $this->is_wpenha() ) {

			wp_enqueue_style( 'wpenha-admin', WPENHA_URL . 'assets/css/admin.css', array(), WPENHA_VERSION );
			wp_enqueue_script( 'wpenha-admin', WPENHA_URL . 'assets/js/admin.js', array(), WPENHA_VERSION, false );

		}

		// CSS for Content Admin >> Show IDs, for list tables in wp-admin, e.g. All Posts page

		$current_screen = get_current_screen();

		if ( 
			( false !== strpos( $current_screen->base, 'edit' ) ) || 
			( false !== strpos( $current_screen->base, 'users' ) ) || 
			( false !== strpos( $current_screen->base, 'upload' ) ) ) {
			wp_enqueue_style( 'wpenha-edit', WPENHA_URL . 'assets/css/edit.css', array(), WPENHA_VERSION );
		}

	}

	/**
	 * Register admin menu and page via Codestar framework
	 *
	 * @since 1.0.0
	 */
	public function register_admin_menu_page() {

		if ( class_exists( 'WPENHA_CSF' ) ) {

			// Set a unique slug-like ID

			$prefix = 'wp-enhancements';

			// Create options

			WPENHA_CSF::createOptions ( $prefix, array(

			    // framework title
				'framework_title' 		=> 'WP Enhancements <small>by <a href="https://bowo.io" target="_blank">bowo.io</a></small>',
				'framework_class' 		=> 'wpenha',

				// menu settings
				'menu_title' 			=> 'WP Enhancements',
				'menu_slug' 			=> 'wp-enhancements',
				'menu_type'				=> 'submenu',
				'menu_capability'		=> 'manage_options',
				// 'menu_icon'			=> 'dashicons-arrow-up-alt2',
				// 'menu_position'		=> 8,
				'menu_hidden'			=> false,
				'menu_parent'			=> 'tools.php',

				// menu extras
				'show_bar_menu' 		=> false,
				'show_sub_menu' 		=> false,
				'show_in_network' 		=> false,
				'show_in_customizer' 	=> false,
				'show_search' 			=> true,
				'show_reset_all'		=> false,
				'show_reset_section'	=> false,
				'show_footer' 			=> true,
				'show_all_options' 		=> true,
				'show_form_warning' 	=> false,
				'sticky_header'			=> true,
				'save_defaults'			=> false,
				'ajax_save'				=> true,

				// admin bar menu settings
				// 'admin_bar_menu_icon'     => '',
				// 'admin_bar_menu_priority' => 80,

				// footer
				'footer_text'			=> '',
				// 'footer_after'			=> 'Footer after',
				'footer_credit'			=> '<a href="https://wordpress.org/plugins/wp-enhancements/" target="_blank">WP Enhancements</a> is on <a href="https://github.com/qriouslad/wp-enhancements" target="_blank">github</a>.',

				// database model
				// 'database'                => 'options', // options, transient, theme_mod, network
				// 'transient_time'          => 0,

				// contextual help
				// 'contextual_help'         => array(),
				// 'contextual_help_sidebar' => '',

				// typography options
				// 'enqueue_webfont'         => true,
				// 'async_webfont'           => false,

				// others
				// 'output_css'              => true,

				// theme and wrapper classname
				'nav'                     => 'normal',
				'theme'                   => 'light',
				'class'                   => '',

				// external default values
				// 'defaults'                => array(),

			) );

			WPENHA_CSF::createSection( $prefix, array(
				'title'  => 'Content',
				'icon'   => 'fas fa-rocket',
				'fields' => array(

					array(
					  'id'    => 'show-ids',
					  'type'  => 'switcher',
					  'title' => 'Show IDs',
					  'label' => 'Show ID column in posts, taxonomies, media and user listings.',
					),

				)
			) );

			// WPENHA_CSF::createSection( $prefix, array(
			//   'id'    => 'basic_fields',
			//   'title' => 'Basic Fields',
			//   'icon'  => 'fas fa-plus-circle',
			// ) );

			// WPENHA_CSF::createSection( $prefix, array(
			// 	'parent'      => 'basic_fields',
			// 	'title'       => 'Text',
			// 	'icon'        => 'far fa-square',
			// 	'description' => 'Visit documentation for more details on this field: <a href="http://codestarframework.com/documentation/#/fields?id=text" target="_blank">Field: text</a>',
			// 	'fields'      => array(

			// 		array(
			// 		'id'    => 'opt-text-1',
			// 		'type'  => 'text',
			// 		'title' => 'Text',
			// 		),

			// 	)
			// ) );

			// WPENHA_CSF::createSection( $prefix, array(
			// 	'parent'      => 'basic_fields',
			// 	'title'       => 'Textarea',
			// 	'icon'        => 'far fa-square',
			// 	'description' => 'Visit documentation for more details on this field: <a href="http://codestarframework.com/documentation/#/fields?id=textarea" target="_blank">Field: textrea</a>',
			// 	'fields'      => array(

			// 		array(
			// 		'id'    => 'opt-textarea-1',
			// 		'type'  => 'textarea',
			// 		'title' => 'Textarea',
			// 		),

			// 	)
			// ) );

		}

	}

	/**
	 * Remove CodeStar framework welcome / ads page
	 *
	 * @since 1.0.0
	 */
	public function remove_codestar_submenu() {

		remove_submenu_page( 'tools.php', 'csf-welcome' );

	}

	/**
	 * Add 'Access now' plugin action link.
	 *
	 * @since    1.0.0
	 */
	
	public function add_plugin_action_links( $links ) {

		$settings_link = '<a href="tools.php?page=' . WPENHA_SLUG . '">Access now</a>';

		array_unshift($links, $settings_link); 

		return $links; 

	}


	/**
	 * Check if current screen is this plugin's main page
	 *
	 * @since 1.0.0
	 */
	public function is_wpenha() {

		$request_uri = sanitize_text_field( $_SERVER['REQUEST_URI'] ); // e.g. /wp-admin/index.php?page=page-slug

		if ( strpos( $request_uri, 'page=' . WPENHA_SLUG ) !== false ) {
			return true; // Yes, this is the plugin's main page
		} else {
			return false; // Nope, this is NOT the plugin's page
		}

	}

}

WP_Enhancements::get_instance();