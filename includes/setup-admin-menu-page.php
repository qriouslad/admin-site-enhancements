<?php

/**
 * Register admin menu
 *
 * @since 1.0.0
 */
function asenha_register_admin_menu() {

	add_submenu_page(
		'tools.php', // Parent page/menu
		'Admin and Site Enhancements', // Browser tab/window title
		'Enhancements', // Sube menu title
		'manage_options', // Minimal user capabililty
		ASENHA_SLUG, // Page slug. Shows up in URL.
		'asenha_add_settings_page'
	);

}

/**
 * Create the settings page of the plugin
 *
 * @since 1.0.0
 */
function asenha_add_settings_page() {
	?>
	<div class="wrap asenha">

		<div id="asenha-header" class="asenha-header">
			<div class="asenha-header-left">
				<h1 class="asenha-heading"><?php echo get_admin_page_title(); ?> <small><?php esc_html_e( 'by', 'admin-site-enhancements' ); ?> <a href="https://bowo.io" target="_blank">bowo.io</a></small></h1>
				<a href="https://wordpress.org/plugins/admin-site-enhancements/" target="_blank" class="asenha-header-action"><span>&#8505;</span> <?php esc_html_e( 'Info', 'admin-site-enhancements' ); ?></a>
				<a href="https://wordpress.org/plugins/admin-site-enhancements/#reviews" target="_blank" class="asenha-header-action"><span>&starf;</span> <?php esc_html_e( 'Review', 'admin-site-enhancements' ); ?></a>
				<a href="https://wordpress.org/support/plugin/admin-site-enhancements/" target="_blank" class="asenha-header-action">&#10010; <?php esc_html_e( 'Feedback', 'admin-site-enhancements' ); ?></a>
				<a href="https://paypal.me/qriouslad" target="_blank" class="asenha-header-action">&#9829; <?php esc_html_e( 'Donate', 'admin-site-enhancements' ); ?></a>
			</div>
			<div class="asenha-header-right">
				<a class="button button-primary asenha-save-button">Save Changes</a>
				<div class="asenha-changes-saved" style="display:none;">Changes have been saved.</div>
			</div>
		</div>

		<div class="asenha-body">
			<form action="options.php" method="post">
				<div class="asenha-vertical-tabs">
					<div class="asenha-tab-buttons">
					    <input id="tab-content-management" type="radio" name="tabs" checked><label for="tab-content-management">Content Management</label>
					    <input id="tab-admin-interface" type="radio" name="tabs"><label for="tab-admin-interface">Admin Interface</label>
					</div>
					<div class="asenha-tab-contents">
					    <section class="asenha-fields fields-content-management"> 
					    	<table class="form-table" role="presentation">
					    		<tbody></tbody>
					    	</table>
					    </section>
					    <section class="asenha-fields fields-admin-interface"> 
					    	<table class="form-table" role="presentation">
					    		<tbody></tbody>
					    	</table>
					    </section>
					</div>
				</div>
				<div style="display:none;"><!-- Hide to prevent flash of fields appearing at the bottom of the page -->
					<?php settings_fields( ASENHA_ID ); ?>
					<?php do_settings_sections( ASENHA_SLUG ); ?>
					<?php submit_button(
						'Save Changes', // Button copy
						'primary', // Type: 'primary', 'small', or 'large'
						'submit', // The 'name' attribute
						true, // Whether to wrap in <p> tag
						array( 'id' => 'asenha-submit' ), // additional attributes
					); ?>
				</div>
			</form>
		</div>

		<div class="asenha-footer">
		</div>

	</div>
	<?php

}

/**
 * Register plugin settings and the corresponding fields
 *
 * @link https://wpshout.com/making-an-admin-options-page-with-the-wordpress-settings-api/
 * @link https://rudrastyh.com/wordpress/creating-options-pages.html
 * @since 1.0.0
 */
function asenha_register_settings() {
	
	// Add "Content Management" section

	add_settings_section(
		'content-management', // Section ID
		'', // Section title. Can be blank.
		'', // Callback function to output section intro. Can be blank.
		ASENHA_SLUG // Settings page slug
	);

	// Register main setttings

	register_setting( 
		ASENHA_ID, // Option group or option_page
		ASENHA_SLUG_U, // Option name in wp_options table
		array(
			'type'					=> 'array', // 'string', 'boolean', 'integer', 'number', 'array', or 'object'
			'description'			=> '', // A description of the data attached to this setting.
			'sanitize_callback'		=> 'asenha_sanitize_options',
			'show_in_rest'			=> false,
			'default'				=> array(), // When calling get_option()
		)
	);

	// Register fields for "Content Management" section

	// Enable Page and Post Duplication

	$field_id = 'enable_duplication';
	$field_slug = 'enable-duplication';

	add_settings_field(
		$field_id, // Field ID
		'Enable Page and Post Duplication', // Field title
		'asenha_render_field_checkbox', // Callback to render field with custom arguments in the array below
		ASENHA_SLUG, // Settings page slug
		'content-management', // Section ID
		array(
			'field_id'			=> $field_id, // Custom argument
			'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
			'field_description'	=> 'Enable one-click duplication of pages, posts and custom posts. The corresponding taxonomy terms and post meta will also be duplicated.', // Custom argument
			'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
		)
	);

	// Enable Media Replacement

	$field_id = 'enable_media_replacement';
	$field_slug = 'enable-media-replacement';

	add_settings_field(
		$field_id, // Field ID
		'Enable Media Replacement', // Field title
		'asenha_render_field_checkbox', // Callback to render field with custom arguments in the array below
		ASENHA_SLUG, // Settings page slug
		'content-management', // Section ID
		array(
			'field_id'			=> $field_id, // Custom argument
			'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
			'field_description'	=> 'Easily replace any type of media file with a new one while retaining the existing media ID and file name.', // Custom argument
			'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
		)
	);

	// Show Featured Image Column

	$field_id = 'show_featured_image_column';
	$field_slug = 'show-featured-image-column';

	add_settings_field(
		$field_id, // Field ID
		'Show Featured Image Column', // Field title
		'asenha_render_field_checkbox', // Callback to render field with custom arguments in the array below
		ASENHA_SLUG, // Settings page slug
		'content-management', // Section ID
		array(
			'field_id'			=> $field_id, // Custom argument
			'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
			'field_description'	=> 'Show featured image column in list tables for pages and post types that support featured images.', // Custom argument
			'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
		)
	);

	// Show Excerpt Column

	$field_id = 'show_excerpt_column';
	$field_slug = 'show-excerpt-column';

	add_settings_field(
		$field_id, // Field ID
		'Show Excerpt Column', // Field title
		'asenha_render_field_checkbox', // Callback to render field with custom arguments in the array below
		ASENHA_SLUG, // Settings page slug
		'content-management', // Section ID
		array(
			'field_id'			=> $field_id, // Custom argument
			'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
			'field_description'	=> 'Show excerpt column in list tables for pages and post types that support excerpt.', // Custom argument
			'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
		)
	);

	// Show ID Column

	$field_id = 'show_id_column';
	$field_slug = 'show-id-column';

	add_settings_field(
		$field_id, // Field ID
		'Show ID Column', // Field title
		'asenha_render_field_checkbox', // Callback to render field with custom arguments in the array below
		ASENHA_SLUG, // Settings page slug
		'content-management', // Section ID
		array(
			'field_id'			=> $field_id, // Custom argument
			'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
			'field_description'	=> 'Show ID column in list tables for pages, all post types, all taxonomies, media, users and comments.', // Custom argument
			'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
		)
	);

	// Hide Comments Column

	$field_id = 'hide_comments_column';
	$field_slug = 'hide-comments-column';

	add_settings_field(
		$field_id, // Field ID
		'Hide Comments Column', // Field title
		'asenha_render_field_checkbox', // Callback to render field with custom arguments in the array below
		ASENHA_SLUG, // Settings page slug
		'content-management', // Section ID
		array(
			'field_id'			=> $field_id, // Custom argument
			'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
			'field_description'	=> 'Hide comments column in list tables for pages, post types that support comments, and alse media/attachments.', // Custom argument
			'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
		)
	);

	// Hide Post Tags Column

	$field_id = 'hide_post_tags_column';
	$field_slug = 'hide-post-tags-column';

	add_settings_field(
		$field_id, // Field ID
		'Hide Post Tags Column', // Field title
		'asenha_render_field_checkbox', // Callback to render field with custom arguments in the array below
		ASENHA_SLUG, // Settings page slug
		'content-management', // Section ID
		array(
			'field_id'			=> $field_id, // Custom argument
			'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
			'field_description'	=> 'Hide tags column in list tables for posts.', // Custom argument
			'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
		)
	);

	// Show Custom Taxonomy Filters

	$field_id = 'show_custom_taxonomy_filters';
	$field_slug = 'show-custom-taxonomy-filters';

	add_settings_field(
		$field_id, // Field ID
		'Show Custom Taxonomy Filters', // Field title
		'asenha_render_field_checkbox', // Callback to render field with custom arguments in the array below
		ASENHA_SLUG, // Settings page slug
		'content-management', // Section ID
		array(
			'field_id'			=> $field_id, // Custom argument
			'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
			'field_description'	=> 'Show additional filter(s) for hierarchical, custom taxonomies on list tables of all post types. This will work similarly with the post categories filter.', // Custom argument
			'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
		)
	);

	// Hide Admin Notices

	$field_id = 'hide_admin_notices';
	$field_slug = 'hide-admin-notices';

	add_settings_field(
		$field_id, // Field ID
		'Hide Admin Notices', // Field title
		'asenha_render_field_checkbox', // Callback to render field with custom arguments in the array below
		ASENHA_SLUG, // Settings page slug
		'content-management', // Section ID
		array(
			'field_id'			=> $field_id, // Custom argument
			'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
			'field_description'	=> 'Clean up admin pages by moving notices into a separate panel easily accessible via the admin bar.', // Custom argument
			'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
		)
	);

}

/**
 * Sanitize options
 *
 * @since 1.0.0
 */
function asenha_sanitize_options( $options ) {

	// Enable Page and Post Duplication
	if ( ! isset( $options['enable_duplication'] ) ) $options['enable_duplication'] = false;
	$options['enable_duplication'] = ( 'on' == $options['enable_duplication'] ? true : false );

	// Enable Media Replacement
	if ( ! isset( $options['enable_media_replacement'] ) ) $options['enable_media_replacement'] = false;
	$options['enable_media_replacement'] = ( 'on' == $options['enable_media_replacement'] ? true : false );

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

	// Hide Admin Notices
	if ( ! isset( $options['hide_admin_notices'] ) ) $options['hide_admin_notices'] = false;
	$options['hide_admin_notices'] = ( 'on' == $options['hide_admin_notices'] ? true : false );

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

/**
 * Render checkbox fields
 *
 * @since 1.0.0
 */
function asenha_render_field_checkbox( $args ) {

	$options = get_option( ASENHA_SLUG_U );

	$field_name = $args['field_name'];
	$field_description = $args['field_description'];
	$field_option_value = ( array_key_exists( $args['field_id'], $options ) ) ? $options[$args['field_id']] : false;

	echo '<input type="checkbox" id="' . esc_attr( $field_name ) . '" class="asenha-field-checkbox" name="' . esc_attr( $field_name ) . '" ' . checked( $field_option_value, true, false ) . '>';
	echo '<label for="' . esc_attr( $field_name ) . '"></label>';
	echo '<div class="asenha-field-description">' . esc_html( $field_description ) . '</div>';

}

/**
 * Suppress all notices, then add notice for successful settings update
 *
 * @since 1.1.0
 */
function asenha_notices() {

	global $plugin_page;

	// Suppress all notices

	if ( ASENHA_SLUG === $plugin_page ) {

		remove_all_actions( 'admin_notices' );

	}

	// Add notice for successful settings update

	if (
		isset( $_GET[ 'page' ] ) 
		&& ASENHA_SLUG == $_GET[ 'page' ]
		&& isset( $_GET[ 'settings-updated' ] ) 
		&& true == $_GET[ 'settings-updated' ]
	) {
		?>
			<script>
				jQuery(document).ready( function() {
					jQuery('.asenha-changes-saved').fadeIn(400).delay(2500).fadeOut(400);
				});
			</script>

		<?php
	}
}

/**
 * Enqueue admin scripts
 *
 * @since 1.0.0
 */
function asenha_admin_scripts( $hook_suffix ) {

	$current_screen = get_current_screen();

	// Get all WP Enhancements options, default to empty array in case it's not been created yet
	$options = get_option( 'admin_site_enhancements', array() );

	// For main page of this plugin

	if ( is_asenha() ) {
		wp_enqueue_style( 'asenha-jbox', ASENHA_URL . 'assets/css/jBox.all.min.css', array(), ASENHA_VERSION );
		wp_enqueue_script( 'asenha-jbox', ASENHA_URL . 'assets/js/jBox.all.min.js', array(), ASENHA_VERSION, false );
		wp_enqueue_script( 'asenha-jsticky', DLM_URL . 'assets/js/jquery.jsticky.mod.min.js', array( 'jquery' ), DLM_VERSION, false );
		wp_enqueue_style( 'asenha-admin-page', ASENHA_URL . 'assets/css/admin-page.css', array( 'asenha-jbox' ), ASENHA_VERSION );
		wp_enqueue_script( 'asenha-admin-page', ASENHA_URL . 'assets/js/admin-page.js', array( 'asenha-jsticky', 'asenha-jbox' ), ASENHA_VERSION, false );
	}

	// Enqueue on all wp-admin

	wp_enqueue_style( 'asenha-wp-admin', ASENHA_URL . 'assets/css/wp-admin.css', array(), ASENHA_VERSION );

	// Content Management >> Show IDs, for list tables in wp-admin, e.g. All Posts page

	if ( ( false !== strpos( $current_screen->base, 'edit' ) ) // List tables for pages, posts, taxonomies
		|| ( false !== strpos( $current_screen->base, 'users' ) ) // Users list table
		|| ( false !== strpos( $current_screen->base, 'upload' ) ) // Media list table
	) {
		wp_enqueue_style( 'asenha-list-table', ASENHA_URL . 'assets/css/list-table.css', array(), ASENHA_VERSION );
	}

	// Content Management >> Enable Media Replacement
	
	if ( ( $current_screen->base == 'upload' ) // Media list table
		|| ( $current_screen->id == 'attachment' ) // Media edit page
	) {
		// wp_enqueue_style( 'asenha-jbox', ASENHA_URL . 'assets/css/jBox.all.min.css', array(), ASENHA_VERSION );
		// wp_enqueue_script( 'asenha-jbox', ASENHA_URL . 'assets/js/jBox.all.min.js', array(), ASENHA_VERSION, false );
		wp_enqueue_style( 'asenha-media-replace', ASENHA_URL . 'assets/css/media-replace.css', array(), ASENHA_VERSION );
		wp_enqueue_script( 'asenha-media-replace', ASENHA_URL . 'assets/js/media-replace.js', array(), ASENHA_VERSION, false );
	}

	// Content Management >> Hide Admin Notices
	if ( array_key_exists( 'hide_admin_notices', $options ) && $options['hide_admin_notices'] ) {
		wp_enqueue_style( 'asenha-jbox', ASENHA_URL . 'assets/css/jBox.all.min.css', array(), ASENHA_VERSION );
		wp_enqueue_script( 'asenha-jbox', ASENHA_URL . 'assets/js/jBox.all.min.js', array(), ASENHA_VERSION, false );
		wp_enqueue_style( 'asenha-hide-admin-notices', ASENHA_URL . 'assets/css/hide-admin-notices.css', array(), ASENHA_VERSION );
		wp_enqueue_script( 'asenha-hide-admin-notices', ASENHA_URL . 'assets/js/hide-admin-notices.js', array( 'asenha-jbox' ), ASENHA_VERSION, false );
	}

}

/**
 * Add 'Access now' plugin action link.
 *
 * @since    1.0.0
 */

function asenha_plugin_action_links( $links ) {

	$settings_link = '<a href="tools.php?page=' . ASENHA_SLUG . '">Access now</a>';

	array_unshift($links, $settings_link); 

	return $links; 

}

/**
 * Modify footer text
 *
 * @since 1.0.0
 */
function asenha_footer_text() {

	if ( is_asenha() ) {
		?>
		<a href="https://wordpress.org/plugins/admin-site-enhancements/" target="_blank">Admin Site Enhancements</a> is on <a href="https://github.com/qriouslad/admin-site-enhancements" target="_blank">github</a>.
		<?php
	}

}

/**
 * Check if current screen is this plugin's main page
 *
 * @since 1.0.0
 */
function is_asenha() {

	$request_uri = sanitize_text_field( $_SERVER['REQUEST_URI'] ); // e.g. /wp-admin/index.php?page=page-slug

	if ( strpos( $request_uri, 'page=' . ASENHA_SLUG ) !== false ) {
		return true; // Yes, this is the plugin's main page
	} else {
		return false; // Nope, this is NOT the plugin's page
	}

}