<?php

namespace ASENHA\Classes;
use WP_Error;

/**
 * Class related to the Disable Components feature
 *
 * @since 2.2.0
 */
class Disable_Components {

	/**
	 * Disable comments for post types
	 *
	 * @since 2.7.0
	 */
	public function disable_comments_for_post_types_edit() {

		$options = get_option( ASENHA_SLUG_U );
		$disable_comments_for = $options['disable_comments_for'];

		foreach ( $disable_comments_for as $post_type_slug => $is_commenting_disabled ) {
			if ( $is_commenting_disabled ) {
				remove_post_type_support( $post_type_slug, 'comments' );
				remove_post_type_support( $post_type_slug, 'trackbacks' );
				remove_meta_box( 'commentstatusdiv', $post_type_slug, 'normal' );
				remove_meta_box( 'commentstatusdiv', $post_type_slug, 'side' );
				remove_meta_box( 'commentsdiv', $post_type_slug, 'normal' );
				remove_meta_box( 'commentsdiv', $post_type_slug, 'side' );
				remove_meta_box( 'trackbacksdiv', $post_type_slug, 'normal' );
				remove_meta_box( 'trackbacksdiv', $post_type_slug, 'side' );
			}
		}

	}

	/**
	 * Hide existing comments from the frontend post
	 *
	 * @since 2.7.0
	 */
	public function hide_existing_comments_on_frontend( $comments, $post_id ) {
		$options = get_option( ASENHA_SLUG_U );
		$disable_comments_for = $options['disable_comments_for'];
		$current_post_type = get_post_type();

		foreach ( $disable_comments_for as $post_type_slug => $is_commenting_disabled ) {
			if ( ( $current_post_type === $post_type_slug ) && $is_commenting_disabled ) {
				return array(); // return an empty array instead of the existing comments array
			} else {
				return $comments;
			}
		}
	}

	/**
	 * Close commenting on the frontend
	 *
	 * @since 2.7.0
	 */
	public function close_commenting_on_frontend() {
		$options = get_option( ASENHA_SLUG_U );
		$disable_comments_for = $options['disable_comments_for'];
		$current_post_type = get_post_type();

		foreach ( $disable_comments_for as $post_type_slug => $is_commenting_disabled ) {
			if ( ( $current_post_type === $post_type_slug ) && $is_commenting_disabled ) {
				return false; // close commenting
			} else {
				return true; // keep commenting open
			}
		}
	}
	
	/**
	 * Show blank template on singular views when comment is disabled
	 * 
	 * @since 4.9.2
	 */
	public function show_blank_comment_template() {

		$options = get_option( ASENHA_SLUG_U );
		$disable_comments_for = $options['disable_comments_for'];
		$current_post_type = get_post_type();

		foreach ( $disable_comments_for as $post_type_slug => $is_commenting_disabled ) {
			if ( ( $current_post_type === $post_type_slug ) && $is_commenting_disabled ) {

				if ( is_singular() ) {
					add_filter( 'comments_template', [ $this, 'load_blank_comment_template' ], 20 );
				}

			}
		}

	}
	
	/**
	 * Load the actual blank comment template
	 * 
	 * @since 4.9.2
	 */
	public function load_blank_comment_template() {
		return ASENHA_PATH . 'includes/blank-comment-template.php';
	}

	/**
	 * Disable Gutenberg in wp-admin for some or all post types
	 *
	 * @since 2.8.0
	 */
	public function disable_gutenberg_for_post_types_admin() {

		// Get current page's post type from WP core globals and query parameters

		global $pagenow, $typenow;

		$post_type = null;

		if ( 'edit.php' === $pagenow ) { // on the list table screen, $typenow returns correct post type

			$post_type = $typenow;

		} elseif ( 'post.php' === $pagenow ) { // on the edit screen, $typenow is empty, so we detect it

			$post_type = get_post_type( $_GET['post'] );

		} elseif ( 'post-new.php' === $pagenow ) { // on the add new screen, best to get post type from GET parameter

			$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : 'post';

		} else {}

		// Check if Gutenberg feature is enabled for the site

		// Before/after WP v5.0.0 via feature plugin
		$gutenberg = function_exists( 'gutenberg_register_scripts_and_styles' );

		// Since WP v5.0.0, gutenberg is in core
		$block_editor = has_action( 'enqueue_block_assets' );

		// Gutenberg feature is not enabled for the site
		if ( ! $gutenberg && ( false === $block_editor ) ) {
			return; // do nothing
		}

		// Assemble single-dimensional array of post types for which Gutenberg should be disabled
		$options = get_option( ASENHA_SLUG_U );
		$disable_gutenberg_for = $options['disable_gutenberg_for'];
		$post_types_for_disable_gutenberg = array();

		foreach( $disable_gutenberg_for as $post_type_slug => $is_gutenberg_disabled ) {
			if ( $is_gutenberg_disabled ) {
				$post_types_for_disable_gutenberg[] = $post_type_slug;
			}
		}

		// Selectively disable Gutenberg
		if ( in_array( $post_type, $post_types_for_disable_gutenberg ) ) {

			// For WP v5.0.0 upwards
			add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );

			// If Gutenberg feature plugin is activated
			if ( $gutenberg ) {
				add_filter( 'gutenberg_can_edit_post_type', '__return_false', 100 );
				$this->remove_all_gutenberg_hook();
			}

		}

	}

	/**
	 * Remove Gutenberg hooks added via feature plugin.
	 *
	 * @link https://plugins.trac.wordpress.org/browser/classic-editor/tags/1.6.2/classic-editor.php#L138
	 * @since 2.8.0
	 */
	public function remove_all_gutenberg_hooks() {

		remove_action( 'admin_menu', 'gutenberg_menu' );
		remove_action( 'admin_init', 'gutenberg_redirect_demo' );

		// Gutenberg 5.3+
		remove_action( 'wp_enqueue_scripts', 'gutenberg_register_scripts_and_styles' );
		remove_action( 'admin_enqueue_scripts', 'gutenberg_register_scripts_and_styles' );
		remove_action( 'admin_notices', 'gutenberg_wordpress_version_notice' );
		remove_action( 'rest_api_init', 'gutenberg_register_rest_widget_updater_routes' );
		remove_action( 'admin_print_styles', 'gutenberg_block_editor_admin_print_styles' );
		remove_action( 'admin_print_scripts', 'gutenberg_block_editor_admin_print_scripts' );
		remove_action( 'admin_print_footer_scripts', 'gutenberg_block_editor_admin_print_footer_scripts' );
		remove_action( 'admin_footer', 'gutenberg_block_editor_admin_footer' );
		remove_action( 'admin_enqueue_scripts', 'gutenberg_widgets_init' );
		remove_action( 'admin_notices', 'gutenberg_build_files_notice' );

		remove_filter( 'load_script_translation_file', 'gutenberg_override_translation_file' );
		remove_filter( 'block_editor_settings', 'gutenberg_extend_block_editor_styles' );
		remove_filter( 'default_content', 'gutenberg_default_demo_content' );
		remove_filter( 'default_title', 'gutenberg_default_demo_title' );
		remove_filter( 'block_editor_settings', 'gutenberg_legacy_widget_settings' );
		remove_filter( 'rest_request_after_callbacks', 'gutenberg_filter_oembed_result' );

		// Previously used, compat for older Gutenberg versions.
		remove_filter( 'wp_refresh_nonces', 'gutenberg_add_rest_nonce_to_heartbeat_response_headers' );
		remove_filter( 'get_edit_post_link', 'gutenberg_revisions_link_to_editor' );
		remove_filter( 'wp_prepare_revision_for_js', 'gutenberg_revisions_restore' );

		remove_action( 'rest_api_init', 'gutenberg_register_rest_routes' );
		remove_action( 'rest_api_init', 'gutenberg_add_taxonomy_visibility_field' );
		remove_filter( 'registered_post_type', 'gutenberg_register_post_prepare_functions' );

		remove_action( 'do_meta_boxes', 'gutenberg_meta_box_save' );
		remove_action( 'submitpost_box', 'gutenberg_intercept_meta_box_render' );
		remove_action( 'submitpage_box', 'gutenberg_intercept_meta_box_render' );
		remove_action( 'edit_page_form', 'gutenberg_intercept_meta_box_render' );
		remove_action( 'edit_form_advanced', 'gutenberg_intercept_meta_box_render' );
		remove_filter( 'redirect_post_location', 'gutenberg_meta_box_save_redirect' );
		remove_filter( 'filter_gutenberg_meta_boxes', 'gutenberg_filter_meta_boxes' );

		remove_filter( 'body_class', 'gutenberg_add_responsive_body_class' );
		remove_filter( 'admin_url', 'gutenberg_modify_add_new_button_url' ); // old
		remove_action( 'admin_enqueue_scripts', 'gutenberg_check_if_classic_needs_warning_about_blocks' );
		remove_filter( 'register_post_type_args', 'gutenberg_filter_post_type_labels' );		
	}

	/**
	 * Disable Gutenberg styles and scripts on the front end for all or some post types
	 *
	 * @since 2.8.0
	 */
	public function disable_gutenberg_for_post_types_frontend() {

		$post = get_queried_object();
		$post_type = $post->post_type;

		// Assemble single-dimensional array of post types for which Gutenberg should be disabled
		$options = get_option( ASENHA_SLUG_U );
		$disable_gutenberg_for = $options['disable_gutenberg_for'];
		$post_types_for_disable_gutenberg = array();

		foreach( $disable_gutenberg_for as $post_type_slug => $is_gutenberg_disabled ) {
			if ( $is_gutenberg_disabled ) {
				$post_types_for_disable_gutenberg[] = $post_type_slug;
			}
		}

		// Selectively disable for the selected post types
		if ( in_array( $post_type, $post_types_for_disable_gutenberg ) ) {

			global $wp_styles;

			// As needed, exclude some block styles from dequeuing
			$keep_enqueued = array(); // e.g. array( 'wp-block-navigation' );

			foreach ( $wp_styles->queue as $handle ) {

				// For all stye handles that starts with 'wp-block'
				if ( false !== strpos( $handle, 'wp-block' ) ) {

					if ( ! in_array( $handle, $keep_enqueued ) ) {
						wp_dequeue_style( $handle );
					}

				}

			}

			// Additional dequeuing
			wp_dequeue_style( 'core-block-supports' );
			wp_dequeue_style( 'global-styles' ); // theme.json
			wp_dequeue_style( 'classic-theme-styles' ); // classic theme

		}

	}

	/**
	 * Disable REST API for non-authenticated users. This is for WP v4.7 or later.
	 *
	 * @since 2.9.0
	 */
	public function disable_rest_api() {
		
		if ( ! is_user_logged_in() ) {
		
			return new WP_Error(
				'rest_api_authentication_required', 
				'The REST API has been restricted to authenticated users.', 
				array( 
					'status' => rest_authorization_required_code() 
				) 
			);

		}

	}

	/**
	 * Disable updates and related functionalities
	 *
	 * @since 4.0.0
	 */
	public function disable_update_notices_version_checks() {

		// Remove nags
		remove_action( 'admin_notices', 'update_nag', 3 );
		remove_action( 'admin_notices', 'maintenance_nag' );

		// Disable WP version check
		remove_action( 'wp_version_check', 'wp_version_check' );
		remove_action( 'admin_init', 'wp_version_check' );
		wp_clear_scheduled_hook( 'wp_version_check' );

		add_filter( 'pre_option_update_core', '__return_null' );

		// Disable theme version checks
		remove_action( 'wp_update_themes', 'wp_update_themes' );
		remove_action( 'admin_init', '_maybe_update_themes' );
		wp_clear_scheduled_hook( 'wp_update_themes' );

		remove_action( 'load-themes.php', 'wp_update_themes' );
		remove_action( 'load-update.php', 'wp_update_themes' );
		remove_action( 'load-update-core.php', 'wp_update_themes' );

		// Disable plugin version checks
		remove_action( 'wp_update_plugins', 'wp_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		wp_clear_scheduled_hook( 'wp_update_plugins' );

		remove_action( 'load-plugins.php', 'wp_update_plugins' );
		remove_action( 'load-update.php', 'wp_update_plugins' );
		remove_action( 'load-update-core.php', 'wp_update_plugins' );

		// Disable auto updates
		wp_clear_scheduled_hook( 'wp_maybe_auto_update' );

		remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
		remove_action( 'admin_init', 'wp_maybe_auto_update' );
		remove_action( 'admin_init', 'wp_auto_update_core' );

		// Disable Site Health checks
		add_filter( 'site_status_tests', [ $this, 'disable_update_checks_in_site_health' ] );

	}

	/**
	 * Override version check info stored in transients named update_core, update_plugins, update_themes.
	 *
	 * @since 4.0.0
	 */
	public function override_version_check_info() {

		include( ABSPATH . WPINC . '/version.php' ); // get $wp_version from here

		$current = (object)array(); // create empty object
		$current->updates = array();
		$current->response = array();
		$current->version_checked = $wp_version;
		$current->last_checked = time();

		return $current;

	}

	/**
	 * Disable Background Updates and Auto-Updates tests in Site Health tests
	 *
	 * @since 4.0.0
	 */
	public function disable_update_checks_in_site_health( $tests ) {

		unset( $tests['async']['background_updates'] );
		unset( $tests['direct']['plugin_theme_auto_updates'] );

		return $tests;

	}

	/**
	 * Remove Dashboard >> Updates menu item
	 *
	 * @since 4.0.0
	 */
	public function remove_updates_menu() {
		global $submenu;
		remove_submenu_page( 'index.php', 'update-core.php' );
	}

	/**
	 * Disable loading of frontend public assets of dashicons
	 *
	 * @since 4.5.0
	 */
	public function disable_dashicons_public_assets() {
		if ( ! is_user_logged_in() ) {
			// Exclude the login page, where dashicon assets are requred to properly style the page
			$current_url = sanitize_text_field( $_SERVER['REQUEST_URI'] );
			if ( false !== strpos( 'wp-login.php', $current_url ) ) {
				wp_dequeue_style( 'dashicons' );
				wp_deregister_style( 'dashicons' );				
			}
		}
	}

	/**
	 * Disable emoji support
	 *
	 * @since 4.5.0
	 */
	public function disable_emoji_support() {

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'embed_head', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_action( 'admin_init', [ $this, 'disable_admin_emojis' ] );
		add_filter( 'emoji_svg_url', '__return_false' );
		add_filter( 'tiny_mce_plugins', [ $this, 'disable_emoji_for_tinymce' ] );
		add_filter( 'wp_resource_hints', [ $this, 'disable_emoji_remove_dns_prefetch' ], 10, 2 );	
		
	}

	/**
	 * Remove the tinymce emoji plugin
	 * 
	 * @since 4.5.0
	 */
	public function disable_emoji_for_tinymce( $plugins ) {

		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		}

		return array();

	}

	/**
	 * Remove emoji CDN hostname from DNS prefetching hints.
	 *
	 * @since 4.5.0
	 */
	public function disable_emoji_remove_dns_prefetch( $urls, $relation_type ) {

		if ( 'dns-prefetch' == $relation_type ) {

			// Strip out any URLs referencing the WordPress.org emoji location
			$emoji_svg_url_base = 'https://s.w.org/images/core/emoji/';
			foreach ( $urls as $key => $url ) {
				if ( false !== strpos( $url, $emoji_svg_url_base ) ) {
					unset( $urls[$key] );
				}
			}

		}

		return $urls;

	}

	/** 
	 * Disable emojis in wp-admin
	 *
	 * @since 4.7.2
	 */
	public function disable_admin_emojis() {
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
	}

}