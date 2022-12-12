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
				<!-- <a href="https://wordpress.org/plugins/admin-site-enhancements/" target="_blank" class="asenha-header-action"><span>&#8505;</span> <?php // esc_html_e( 'Info', 'admin-site-enhancements' ); ?></a> -->
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
					    <input id="tab-disable-components" type="radio" name="tabs"><label for="tab-disable-components">Disable Components</label>
					    <input id="tab-security" type="radio" name="tabs"><label for="tab-security">Security</label>
					    <input id="tab-utilities" type="radio" name="tabs"><label for="tab-utilities">Utilities</label>
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
					    <section class="asenha-fields fields-disable-components"> 
					    	<table class="form-table" role="presentation">
					    		<tbody></tbody>
					    	</table>
					    </section>
					    <section class="asenha-fields fields-security"> 
					    	<table class="form-table" role="presentation">
					    		<tbody></tbody>
					    	</table>
					    </section>
					    <section class="asenha-fields fields-utilities"> 
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
 * Suppress all notices, then add notice for successful settings update
 *
 * @since 1.1.0
 */
function asenha_suppress_notices() {

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
 * Suppress all generic notices on the plugin settings page
 *
 * @since 2.7.0
 */
function asenha_suppress_generic_notices() {

	global $plugin_page;

	// Suppress all notices

	if ( ASENHA_SLUG === $plugin_page ) {

		remove_all_actions( 'all_admin_notices' );

	}

}

/**
 * Enqueue admin scripts
 *
 * @since 1.0.0
 */
function asenha_admin_scripts( $hook_suffix ) {

	global $wp_version;

	$current_screen = get_current_screen();

	// Get all WP Enhancements options, default to empty array in case it's not been created yet
	$options = get_option( 'admin_site_enhancements', array() );

	// For main page of this plugin

	if ( is_asenha() ) {
		wp_enqueue_style( 'asenha-jbox', ASENHA_URL . 'assets/css/jBox.all.min.css', array(), ASENHA_VERSION );
		wp_enqueue_script( 'asenha-jbox', ASENHA_URL . 'assets/js/jBox.all.min.js', array(), ASENHA_VERSION, false );
		wp_enqueue_script( 'asenha-jsticky', ASENHA_URL . 'assets/js/jquery.jsticky.mod.min.js', array( 'jquery' ), ASENHA_VERSION, false );
		wp_enqueue_script( 'asenha-js-cookie', ASENHA_URL . 'assets/js/js.cookie.min.js', array(), ASENHA_VERSION, false );

		// jQuery UI Sortables. In use, e.g. for Admin Interface >> Admin Menu Organizer	
		// Re-register and re-enqueue jQuery UI Core and plugins required for sortable, draggable and droppable when ordering menu items
		wp_deregister_script( 'jquery-ui-core' );
		wp_register_script( 'jquery-ui-core', get_site_url() . '/wp-includes/js/jquery/ui/core.min.js', array( 'jquery' ), ASENHA_VERSION, false );
		wp_enqueue_script( 'jquery-ui-core' );

		if ( version_compare( $wp_version, '5.6.0', '>=' ) ) {

			wp_deregister_script( 'jquery-ui-mouse' );
			wp_register_script( 'jquery-ui-mouse', get_site_url() . '/wp-includes/js/jquery/ui/mouse.min.js', array( 'jquery-ui-core' ), ASENHA_VERSION, false );
			wp_enqueue_script( 'jquery-ui-mouse' );

		} else {

			wp_deregister_script( 'jquery-ui-widget' );
			wp_register_script( 'jquery-ui-widget', get_site_url() . '/wp-includes/js/jquery/ui/widget.min.js', array( 'jquery' ), ASENHA_VERSION, false );
			wp_enqueue_script( 'jquery-ui-widget' );

			wp_deregister_script( 'jquery-ui-mouse' );
			wp_register_script( 'jquery-ui-mouse', get_site_url() . '/wp-includes/js/jquery/ui/mouse.min.js', array( 'jquery-ui-core', 'jquery-ui-widget' ), ASENHA_VERSION, false );
			wp_enqueue_script( 'jquery-ui-mouse' );

		}

		wp_deregister_script( 'jquery-ui-sortable' );
		wp_register_script( 'jquery-ui-sortable', get_site_url() . '/wp-includes/js/jquery/ui/sortable.min.js', array( 'jquery-ui-mouse' ), ASENHA_VERSION, false );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_deregister_script( 'jquery-ui-draggable' );
		wp_register_script( 'jquery-ui-draggable', get_site_url() . '/wp-includes/js/jquery/ui/draggable.min.js', array( 'jquery-ui-mouse' ), ASENHA_VERSION, false );
		wp_enqueue_script( 'jquery-ui-draggable' );

		wp_deregister_script( 'jquery-ui-droppable' );
		wp_register_script( 'jquery-ui-droppable', get_site_url() . '/wp-includes/js/jquery/ui/droppable.min.js', array( 'jquery-ui-draggable' ), ASENHA_VERSION, false );
		wp_enqueue_script( 'jquery-ui-droppable' );

		// Script to set behaviour and actions of the sortable menu
		wp_enqueue_script( 'asenha-custom-admin-menu', ASENHA_URL . 'assets/js/custom-admin-menu.js', array( 'jquery-ui-draggable' ), ASENHA_VERSION, false );

		// CodeMirror. In use, e.g. for Utilities >> Enable Custom Admin / Frontend CSS / ads.txt / app-ads.txt
		wp_enqueue_style( 'asenha-codemirror', ASENHA_URL . 'assets/css/codemirror/codemirror.min.css', array(), ASENHA_VERSION );
		wp_enqueue_script( 'asenha-codemirror', ASENHA_URL . 'assets/js/codemirror/codemirror.min.js', array(), ASENHA_VERSION, true );			
		wp_enqueue_script( 'asenha-codemirror-css-mode', ASENHA_URL . 'assets/js/codemirror/css.js', array( 'asenha-codemirror' ), ASENHA_VERSION, true ); // CSS mode
		wp_enqueue_script( 'asenha-codemirror-markdown-mode', ASENHA_URL . 'assets/js/codemirror/markdown.js', array( 'asenha-codemirror' ), ASENHA_VERSION, true ); // Markdown mode

		// DataTables. In use, e.g. for Security >> Limit Login Attempts
		wp_enqueue_style( 'asenha-datatables', ASENHA_URL . 'assets/css/datatables/datatables.min.css', array(), ASENHA_VERSION );
		wp_enqueue_script( 'asenha-datatables', ASENHA_URL . 'assets/js/datatables/datatables.min.js', array( 'jquery' ), ASENHA_VERSION, false );

		// Main style and script for the admin page
		wp_enqueue_style( 'asenha-admin-page', ASENHA_URL . 'assets/css/admin-page.css', array( 'asenha-jbox', 'asenha-codemirror', 'asenha-datatables' ), ASENHA_VERSION );
		wp_enqueue_script( 'asenha-admin-page', ASENHA_URL . 'assets/js/admin-page.js', array( 'asenha-jsticky', 'asenha-jbox', 'asenha-js-cookie', 'asenha-codemirror-css-mode', 'asenha-datatables', 'asenha-custom-admin-menu' ), ASENHA_VERSION, false );
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

	$settings_link = '<a href="tools.php?page=' . ASENHA_SLUG . '">Configure now</a>';

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