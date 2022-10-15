<?php

/**
 * Register admin menu and page via Codestar framework
 *
 * @since 1.0.0
 */
function wpenha_admin_menu_page() {

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
			'show_search' 			=> false,
			'show_reset_all'		=> false,
			'show_reset_section'	=> false,
			'show_footer' 			=> false,
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
			// 'icon'   => 'fas fa-rocket',
			'fields' => array(

				array(
				  'id'    => 'show-id-column',
				  'type'  => 'switcher',
				  'title' => 'Show ID Column',
				  'label' => 'Show ID column in list tables for pages, all post types, all taxonomies, media, users and comments.',
				),

				array(
				  'id'    => 'show-featured-image-column',
				  'type'  => 'switcher',
				  'title' => 'Show Featured Image Column',
				  'label' => 'Show Featured Image column in list tables for pages and post types that support featured images.',
				),

				array(
				  'id'    => 'hide-comments-column',
				  'type'  => 'switcher',
				  'title' => 'Hide Comments Column',
				  'label' => 'Hide comments column in list tables for pages and post types that support comments.',
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
 * Enqueue admin scripts
 *
 * @since 1.0.0
 */
function wpenha_admin_scripts() {

	// For main page of this plugin

	if ( is_wpenha() ) {

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
 * Remove CodeStar framework welcome / ads page
 *
 * @since 1.0.0
 */
function wpenha_remove_codestar_submenu() {

	remove_submenu_page( 'tools.php', 'csf-welcome' );

}

/**
 * Add 'Access now' plugin action link.
 *
 * @since    1.0.0
 */

function wpenha_add_plugin_action_links( $links ) {

	$settings_link = '<a href="tools.php?page=' . WPENHA_SLUG . '">Access now</a>';

	array_unshift($links, $settings_link); 

	return $links; 

}

/**
 * Check if current screen is this plugin's main page
 *
 * @since 1.0.0
 */
function is_wpenha() {

	$request_uri = sanitize_text_field( $_SERVER['REQUEST_URI'] ); // e.g. /wp-admin/index.php?page=page-slug

	if ( strpos( $request_uri, 'page=' . WPENHA_SLUG ) !== false ) {
		return true; // Yes, this is the plugin's main page
	} else {
		return false; // Nope, this is NOT the plugin's page
	}

}