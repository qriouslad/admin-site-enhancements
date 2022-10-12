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

		// Register admin menu and subsequently the main admin page
		add_action( 'wpenha_csf_loaded', [ $this, 'register_admin_menu_page' ] );

	}

	/**
	 * Register admin menu and page via Codestar framework
	 *
	 * @since 1.0.0
	 */
	public function register_admin_menu_page() {

		if ( class_exists( 'WPENHA_CSF' ) ) {

			// Set a unique slug-like ID

			$prefix = 'wpenha';

			// Create options

			WPENHA_CSF::createOptions ( $prefix, array(
				'menu_title' 		=> 'Enhancements',
				'menu_slug' 		=> 'wpenha',
				'menu_type'			=> 'submenu',
				// 'menu_parent'		=> 'options-general.php',
				'menu_parent'		=> 'tools.php',
				// 'menu_position'		=> 8,
				// 'menu_icon'			=> 'dashicons-arrow-up-alt2',
				'framework_title' 	=> 'WP Enhancements <small>by <a href="https://bowo.io" target="_blank">bowo.io</a></small>',
				'framework_class' 	=> 'wpenha',
				'show_bar_menu' 	=> false,
				'show_search' 		=> false,
				'show_reset_all' 	=> false,
				'show_reset_section' => false,
				'show_form_warning' => false,
				'save_defaults'		=> true,
				'show_footer' 		=> false,
				'footer_credit'		=> 'WP Enhancements.',
			) );

			WPENHA_CSF::createSection( $prefix, array(
				'title'  => 'Tab Title 1',
				'fields' => array(

					// A text field
					array(
						'id'    => 'opt-text',
						'type'  => 'text',
						'title' => 'Simple Text',
					),

				)
			) );

		}

	}

}

WP_Enhancements::get_instance();