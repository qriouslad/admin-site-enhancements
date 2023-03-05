<?php

namespace ASENHA\Classes;

/**
 * Class related to registration of settings fields
 *
 * @since 2.2.0
 */
class Settings_Sections_Fields {

	/**
	 * Register plugin settings and the corresponding fields
	 *
	 * @link https://wpshout.com/making-an-admin-options-page-with-the-wordpress-settings-api/
	 * @link https://rudrastyh.com/wordpress/creating-options-pages.html
	 * @since 1.0.0
	 */
	function register_sections_fields() {
		
		add_settings_section(
			'main-section', // Section ID
			'', // Section title. Can be blank.
			'', // Callback function to output section intro. Can be blank.
			ASENHA_SLUG // Settings page slug
		);

		// Register main setttings

		// Instantiate object for sanitization of settings fields values
		$sanitization = new Settings_Sanitization;

		// Instantiate object for rendering of settings fields for the admin page
		$render_field = new Settings_Fields_Render;

		register_setting( 
			ASENHA_ID, // Option group or option_page
			ASENHA_SLUG_U, // Option name in wp_options table
			array(
				'type'					=> 'array', // 'string', 'boolean', 'integer', 'number', 'array', or 'object'
				'description'			=> '', // A description of the data attached to this setting.
				'sanitize_callback'		=> [ $sanitization, 'sanitize_for_options' ],
				'show_in_rest'			=> false,
				'default'				=> array(), // When calling get_option()
			)
		);

		// =================================================================
		// Call WordPress globals and set new globals required for the fields
		// =================================================================

		global $wp_roles, $wpdb, $asenha_public_post_types, $asenha_gutenberg_post_types, $asenha_revisions_post_types;

		$roles = $wp_roles->get_names();

		// Get array of slugs and plural labels for public post types, e.g. array( 'post' => 'Posts', 'page' => 'Pages' )
		$asenha_public_post_types = array();
		$public_post_type_names = get_post_types( array( 'public' => true ), 'names' );
		foreach( $public_post_type_names as $post_type_name ) {
			$post_type_object = get_post_type_object( $post_type_name );
			$asenha_public_post_types[$post_type_name] = $post_type_object->label;
		}

		// Get array of slugs and plural labels for post types that can be edited with the Gutenberg block editor, e.g. array( 'post' => 'Posts', 'page' => 'Pages' )
		$asenha_gutenberg_post_types = array();
		$gutenberg_not_applicable_types = array( 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block' );
		$all_post_types = get_post_types( array(), 'objects' );
		foreach ( $all_post_types as $post_type_slug => $post_type_info ) {
			$asenha_gutenberg_post_types[$post_type_slug] = $post_type_info->label;
			if ( in_array( $post_type_slug, $gutenberg_not_applicable_types ) ) {
				unset( $asenha_gutenberg_post_types[$post_type_slug] );
			}
		}

		// Get array of slugs and plural labels for post types supporting revisions, e.g. array( 'post' => 'Posts', 'page' => 'Pages' )
		$asenha_revisions_post_types = array();
		foreach ( get_post_types( array(), 'names' ) as $post_type_slug ) { // post type slug/name
			if ( post_type_supports( $post_type_slug, 'revisions' ) ) {
				$post_type_object = get_post_type_object( $post_type_slug );
				if ( property_exists( $post_type_object, 'label' ) ) {
					$asenha_revisions_post_types[$post_type_slug] = $post_type_object->label;
				}
			}
		}

		// =================================================================
		// CONTENT MANAGEMENT
		// =================================================================

		// Enable Page and Post Duplication

		$field_id = 'enable_duplication';
		$field_slug = 'enable-duplication';

		add_settings_field(
			$field_id, // Field ID
			'Page and Post Duplication', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'			=> $field_id, // Custom argument
				'field_slug'		=> $field_slug, // Custom argument
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
			'Media Replacement', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'			=> $field_id, // Custom argument
				'field_slug'		=> $field_slug, // Custom argument
				'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'	=> 'Easily replace any type of media file with a new one while retaining the existing media ID, publish date and file name. So, no existing links will break.', // Custom argument
				'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable SVG Upload

		$field_id = 'enable_svg_upload';
		$field_slug = 'enable-svg-upload';

		add_settings_field(
			$field_id, // Field ID
			'SVG Upload', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Allow some or all user roles to upload SVG files, which will then be sanitized to keep things secure.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'enable_svg_upload_for';
		$field_slug = 'enable-svg-upload-for';

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator

				add_settings_field(
					$field_id . '_' . $role_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $role_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $role_slug . ']', // Custom argument
						'field_label'			=> $role_label, // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half admin-interface ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
					)
				);

			}
		}

		// Enable External Permalinks

		$field_id = 'enable_external_permalinks';
		$field_slug = 'enable-external-permalinks';

		add_settings_field(
			$field_id, // Field ID
			'External Permalinks', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Enable pages, posts and/or custom post types to have permalinks that point to external URLs. Compatible with links added using <a href="https://wordpress.org/plugins/page-links-to/" target="_blank">Page Links To</a>.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'enable_external_permalinks_for';
		$field_slug = 'enable-external-permalinks-for';

		if ( is_array( $asenha_public_post_types ) ) {
			foreach ( $asenha_public_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				if ( 'attachment' != $post_type_slug ) {
					add_settings_field(
						$field_id . '_' . $post_type_slug, // Field ID
						'', // Field title
						[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
						ASENHA_SLUG, // Settings page slug
						'main-section', // Section ID
						array(
							'parent_field_id'		=> $field_id, // Custom argument
							'field_id'				=> $post_type_slug, // Custom argument
							'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
							'field_label'			=> $post_type_label . ' <span class="faded">('. $post_type_slug .')</span>', // Custom argument
							'class'					=> 'asenha-checkbox asenha-hide-th asenha-half disable-components ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
						)
					);
				}
			}
		}

		// Enable Auto-Publishing of Posts with Missed Schedules

		$field_id = 'enable_missed_schedule_posts_auto_publish';
		$field_slug = 'enable-missed-schedule-posts-auto-publish';

		add_settings_field(
			$field_id, // Field ID
			'Auto-Publish Posts with Missed Schedule', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'			=> $field_id, // Custom argument
				'field_slug'		=> $field_slug, // Custom argument
				'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'	=> 'Trigger publishing of scheduled posts of all types marked with "missed schedule", anytime the site is visited.', // Custom argument
				'class'				=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);


		// Enhance List Tables

		$field_id = 'enhance_list_tables';
		$field_slug = 'enhance-list-tables';

		add_settings_field(
			$field_id, // Field ID
			'Enhance List Tables', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Improve the usefulness of listing pages for various post types and taxonomies, media, comments and users by adding / removing columns and elements.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Show Featured Image Column

		$field_id = 'show_featured_image_column';
		$field_slug = 'show-featured-image-column';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Show featured image column.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Show Excerpt Column

		$field_id = 'show_excerpt_column';
		$field_slug = 'show-excerpt-column';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Show excerpt column.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Show ID Column

		$field_id = 'show_id_column';
		$field_slug = 'show-id-column';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Show ID column.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Show ID in Action Row

		$field_id = 'show_id_in_action_row';
		$field_slug = 'show-id-in-action_row';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Show ID in action row along with links for Edit, View, etc.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Show Custom Taxonomy Filters

		$field_id = 'show_custom_taxonomy_filters';
		$field_slug = 'show-custom-taxonomy-filters';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Show additional filter(s) for hierarchical, custom taxonomies.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Hide Comments Column

		$field_id = 'hide_comments_column';
		$field_slug = 'hide-comments-column';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove comments column.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Hide Post Tags Column

		$field_id = 'hide_post_tags_column';
		$field_slug = 'hide-post-tags-column';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove tags column (for posts).', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th content-management ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// =================================================================
		// ADMIN INTERFACE
		// =================================================================

		// Hide Admin Notices

		$field_id = 'hide_admin_notices';
		$field_slug = 'hide-admin-notices';

		add_settings_field(
			$field_id, // Field ID
			'Hide Admin Notices', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'			=> $field_id, // Custom argument
				'field_slug'		=> $field_slug, // Custom argument
				'field_name'		=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'	=> 'Clean up admin pages by moving notices into a separate panel easily accessible via the admin bar.', // Custom argument
				'class'				=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Disable Dashboard Widgets

		$field_id = 'disable_dashboard_widgets';
		$field_slug = 'disable-dashboard-widgets';

		add_settings_field(
			$field_id, // Field ID
			'Disable Dashboard Widgets', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Clean up and speed up the dashboard by completely disabling some or all widgets. Disabled widgets won\'t load any assets nor show up under Screen Options. ', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disabled_dashboard_widgets';
		$field_slug = 'disabled-dashboard-widgets';

		$extra_options = get_option( 'admin_site_enhancements_extra', array() );
		if ( array_key_exists( 'dashboard_widgets', $extra_options ) ) {
			$dashboard_widgets = $extra_options['dashboard_widgets'];
		} else {
			$admin_interface = new Admin_Interface;
			$dashboard_widgets = $admin_interface->get_dashboard_widgets();
			$extra_options['dashboard_widgets'] = $dashboard_widgets;
			update_option( 'admin_site_enhancements_extra', $extra_options );
		}

		foreach ( $dashboard_widgets as $widget ) {
			add_settings_field(
				$field_id . '_' . $widget['id'], // Field ID
				'', // Field title
				[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
				ASENHA_SLUG, // Settings page slug
				'main-section', // Section ID
				array(
					'parent_field_id'		=> $field_id, // Custom argument
					'field_id'				=> $widget['id'] . '__' . $widget['context'] . '__' . $widget['priority'], // Custom argument
					'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . '][' . $widget['id'] . '__' . $widget['context'] . '__' . $widget['priority'] . ']', // Custom argument
					'field_label'			=> $widget['title'] . ' <span class="faded">(' . $widget['id'] . ')</span>', // Custom argument
					'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug . ' ' . $widget['id'], // Custom class for the <tr> element
				)
			);
		}

		// Hide or Modify Elements

		$field_id = 'hide_modify_elements';
		$field_slug = 'hide-modify-elements';

		add_settings_field(
			$field_id, // Field ID
			'Clean Up Admin Bar', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Remove various elements from the admin bar.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_wp_logo_menu';
		$field_slug = 'hide-ab-wp-logo-menu';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove WordPress logo/menu', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_customize_menu';
		$field_slug = 'hide-ab-customize-menu';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove customize menu', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_updates_menu';
		$field_slug = 'hide-ab-updates-menu';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove updates counter/link', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_comments_menu';
		$field_slug = 'hide-ab-comments-menu';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove comments counter/link', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_new_content_menu';
		$field_slug = 'hide-ab-new-content-menu';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove new content menu', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_ab_howdy';
		$field_slug = 'hide-ab-howdy';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove \'Howdy\'', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_help_drawer';
		$field_slug = 'hide-help-drawer';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Remove the Help tab and drawer', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Hide Admin Bar

		$field_id = 'hide_admin_bar';
		$field_slug = 'hide-admin-bar';

		add_settings_field(
			$field_id, // Field ID
			'Hide Admin Bar', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Hide admin bar on the front end for all or some user roles.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'hide_admin_bar_for';
		$field_slug = 'hide-admin-bar-for';

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator

				add_settings_field(
					$field_id . '_' . $role_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $role_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $role_slug . ']', // Custom argument
						'field_label'			=> $role_label, // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half admin-interface ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
					)
				);

			}
		}

		// Customize Admin Menu

		$field_id = 'customize_admin_menu';
		$field_slug = 'customize-admin-menu';

		add_settings_field(
			$field_id, // Field ID
			'Admin Menu Organizer', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Customize the order of the admin menu and optionally change menu item title or hide some items.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'custom_menu_order';
		$field_slug = 'custom-menu-order';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_sortable_menu' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'sortable-menu', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-sortable asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// =================================================================
		// LOG IN | LOG OUT
		// =================================================================

		// Change Login URL

		$field_id = 'change_login_url';
		$field_slug = 'change-login-url';

		add_settings_field(
			$field_id, // Field ID
			'Change Login URL', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Default is ' . get_site_url() . '/wp-admin/', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'custom_login_slug';
		$field_slug = 'custom-login-slug';

		add_settings_field(
			$field_id, // Field ID
			'New URL:', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> get_site_url() . '/', // Custom argument
				'field_suffix'			=> '/', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable Log In/Out Menu

		$field_id = 'enable_login_logout_menu';
		$field_slug = 'enable-login-logout-menu';

		add_settings_field(
			$field_id, // Field ID
			'Log In/Out Menu', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Enable log in, log out and dynamic log in/out menu item for addition to any menu.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable Last Login Column

		$field_id = 'enable_last_login_column';
		$field_slug = 'enable-last-login-column';

		add_settings_field(
			$field_id, // Field ID
			'Last Login Column', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Log when users on the site last logged in and display the date and time in the users list table.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Redirect After Login

		$field_id = 'redirect_after_login';
		$field_slug = 'redirect-after-login';

		add_settings_field(
			$field_id, // Field ID
			'Redirect After Login', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Set custom redirect URL for all or some user roles after login.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'redirect_after_login_to_slug';
		$field_slug = 'redirect-after-login-to-slug';

		add_settings_field(
			$field_id, // Field ID
			'Redirect to:', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> get_site_url() . '/', // Custom argument
				'field_suffix'			=> '/ for:', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'redirect_after_login_for';
		$field_slug = 'redirect-after-login-for';

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator

				add_settings_field(
					$field_id . '_' . $role_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $role_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $role_slug . ']', // Custom argument
						'field_label'			=> $role_label, // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half login-logout ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
					)
				);

			}
		}

		// Redirect After Logout

		$field_id = 'redirect_after_logout';
		$field_slug = 'redirect-after-logout';

		add_settings_field(
			$field_id, // Field ID
			'Redirect After Logout', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Set custom redirect URL for all or some user roles after logout.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'redirect_after_logout_to_slug';
		$field_slug = 'redirect-after-logout-to-slug';

		add_settings_field(
			$field_id, // Field ID
			'Redirect to:', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> get_site_url() . '/', // Custom argument
				'field_suffix'			=> '/ for:', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix login-logout ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'redirect_after_logout_for';
		$field_slug = 'redirect-after-logout-for';

		if ( is_array( $roles ) ) {
			foreach ( $roles as $role_slug => $role_label ) { // e.g. $role_slug is administrator, $role_label is Administrator

				add_settings_field(
					$field_id . '_' . $role_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $role_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $role_slug . ']', // Custom argument
						'field_label'			=> $role_label, // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half login-logout ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
					)
				);

			}
		}

		// =================================================================
		// CUSTOM CODE
		// =================================================================

		// Enable Custom Admin CSS

		$field_id = 'enable_custom_admin_css';
		$field_slug = 'enable-custom-admin-css';

		add_settings_field(
			$field_id, // Field ID
			'Custom Admin CSS', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Add custom CSS on all admin pages for all user roles.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'custom_admin_css';
		$field_slug = 'custom-admin-css';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 30,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable Custom Frontend CSS

		$field_id = 'enable_custom_frontend_css';
		$field_slug = 'enable-custom-frontend-css';

		add_settings_field(
			$field_id, // Field ID
			'Custom Frontend CSS', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Add custom CSS on all frontend pages for all user roles.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'custom_frontend_css';
		$field_slug = 'custom-frontend-css';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 30,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Custom Body Class

		$field_id = 'enable_custom_body_class';
		$field_slug = 'enable-custom-body-class';

		add_settings_field(
			$field_id, // Field ID
			'Custom Body Class', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Add custom &lt;body&gt; class(es) on the singular view of some or all public post types. Compatible with classes already added using <a href="https://wordpress.org/plugins/wp-custom-body-class" target="_blank">Custom Body Class plugin</a>.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'enable_custom_body_class_for';
		$field_slug = 'enable-custom-body-class-for';

		if ( is_array( $asenha_public_post_types ) ) {
			foreach ( $asenha_public_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				if ( 'attachment' != $post_type_slug ) {
					add_settings_field(
						$field_id . '_' . $post_type_slug, // Field ID
						'', // Field title
						[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
						ASENHA_SLUG, // Settings page slug
						'main-section', // Section ID
						array(
							'parent_field_id'		=> $field_id, // Custom argument
							'field_id'				=> $post_type_slug, // Custom argument
							'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
							'field_label'			=> $post_type_label . ' <span class="faded">('. $post_type_slug .')</span>', // Custom argument
							'class'					=> 'asenha-checkbox asenha-hide-th asenha-half custom-code ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
						)
					);
				}
			}
		}

		// Manage ads.txt and app-ads.txt

		$field_id = 'manage_ads_appads_txt';
		$field_slug = 'manage-ads-appads-txt';

		add_settings_field(
			$field_id, // Field ID
			'Manage ads.txt and app-ads.txt', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Easily edit and validate your <a href="/ads.txt" target="_blank">ads.txt</a> and <a href="/app-ads.txt" target="_blank">app-ads.txt</a> content.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'ads_txt_content';
		$field_slug = 'ads-txt-content';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 15,
				'field_intro'			=> '<strong>Your ads.txt content:</strong>', // Custom argument
				'field_description'		=> 'Validate with: <a href="https://adstxt.guru/validator/url/?url=' . urlencode( get_site_url( null, 'ads.txt' ) ) . '" target="_blank">adstxt.guru</a> | <a href="https://www.adstxtvalidator.com/ads_txt/' . esc_attr( str_replace( '.', '-', $_SERVER['SERVER_NAME'] ) ) . '" target="_blank">adstxtvalidator.com</a><div class="vspacer"></div>', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'app_ads_txt_content';
		$field_slug = 'app-ads-txt-content';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 15,
				'field_intro'			=> '<strong>Your app-ads.txt content:</strong>', // Custom argument
				'field_description'		=> 'Validate with: <a href="https://adstxt.guru/validator/url/?url=' . urlencode( get_site_url( null, 'app-ads.txt' ) ) . '" target="_blank">adstxt.guru</a>', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Manage robots.txt

		$field_id = 'manage_robots_txt';
		$field_slug = 'manage-robots-txt';

		add_settings_field(
			$field_id, // Field ID
			'Manage robots.txt', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Easily edit and validate your <a href="/robots.txt" target="_blank">robots.txt</a> content.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'robots_txt_content';
		$field_slug = 'robots-txt-content';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 20,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> 'Validate with: <a href="https://en.ryte.com/free-tools/robots-txt/?refresh=1&url=' . urlencode( get_site_url( null, 'robots.txt' ) ) . '&useragent=Googlebot&submit=Evaluate" target="_blank">ryte.com</a> | <a href="https://serp.tools/tools/robots-txt" target="_blank">serp.tools</a><div class="vspacer"></div>', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Insert <head>, <body> and <footer> code

		$field_id = 'insert_head_body_footer_code';
		$field_slug = 'insert-head-body-footer-code';

		add_settings_field(
			$field_id, // Field ID
			'Insert &lt;head&gt;, &lt;body&gt; and &lt;footer&gt; Code', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Easily insert &lt;meta&gt;, &lt;link&gt;, &lt;script&gt; and &lt;style&gt; tags, Google Analytics, Tag Manager, AdSense, Ads Conversion and Optimize code, Facebook, TikTok and Twitter pixels, etc.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options.
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'head_code_priority';
		$field_slug = 'head-code-priority';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '<strong>Code to insert before &lt;/head&gt; with the priority of</strong>', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> 'Default is 10. Larger number insert code closer to &lt;/head&gt;', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th narrow custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'head_code';
		$field_slug = 'head-code';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 15,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'body_code_priority';
		$field_slug = 'body-code-priority';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '<strong>Code to insert after &lt;body&gt; with the priority of</strong>', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> 'Default is 10. Smaller number insert code closer to &lt;body&gt;', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th narrow custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'body_code';
		$field_slug = 'body-code';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 15,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'footer_code_priority';
		$field_slug = 'footer-code-priority';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '<strong>Code to insert in footer section before &lt;/body&gt;: with the priority of</strong>', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> 'Default is 10. Larger number insert code closer to &lt;/body&gt;', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th narrow custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'footer_code';
		$field_slug = 'footer-code';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 15,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted custom-code ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// =================================================================
		// DISABLE COMPONENTS
		// =================================================================

		// Disable Gutenberg

		$field_id = 'disable_gutenberg';
		$field_slug = 'disable-gutenberg';

		add_settings_field(
			$field_id, // Field ID
			'Disable Gutenberg', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Disable the Gutenberg block editor for some or all applicable post types.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_gutenberg_for';
		$field_slug = 'disable-gutenberg-for';

		if ( is_array( $asenha_gutenberg_post_types ) ) {
			foreach ( $asenha_gutenberg_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				add_settings_field(
					$field_id . '_' . $post_type_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $post_type_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
						'field_label'			=> $post_type_label . ' <span class="faded">('. $post_type_slug .')</span>', // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half disable-components ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
					)
				);
			}
		}

		$field_id = 'disable_gutenberg_frontend_styles';
		$field_slug = 'disable-gutenberg-frontend-styles';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Also disable frontend block styles / CSS files for the selected post types.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th asenha-th-border-top disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Disable Comments

		$field_id = 'disable_comments';
		$field_slug = 'disable-comments';

		add_settings_field(
			$field_id, // Field ID
			'Disable Comments', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Disable comments for some or all public post types. When disabled, existing comments will also be hidden on the frontend.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_comments_for';
		$field_slug = 'disable-comments-for';

		if ( is_array( $asenha_public_post_types ) ) {
			foreach ( $asenha_public_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				add_settings_field(
					$field_id . '_' . $post_type_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $post_type_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
						'field_label'			=> $post_type_label . ' <span class="faded">('. $post_type_slug .')</span>', // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half disable-components ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
					)
				);
			}
		}

		// Disable REST API

		$field_id = 'disable_rest_api';
		$field_slug = 'disable-rest-api';

		add_settings_field(
			$field_id, // Field ID
			'Disable REST API', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Disable REST API access for non-authenticated users and remove URL traces from &lt;head&gt;, HTTP headers and WP RSD endpoint.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Disable Feeds

		$field_id = 'disable_feeds';
		$field_slug = 'disable-feeds';

		add_settings_field(
			$field_id, // Field ID
			'Disable Feeds', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Disable all RSS, Atom and RDF feeds. This includes feeds for posts, categories, tags, comments, authors and search. Also removes traces of feed URLs from &lt;head&gt;.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Disable Auto Updates

		$field_id = 'disable_all_updates';
		$field_slug = 'disable-all-updates';

		add_settings_field(
			$field_id, // Field ID
			'Disable All Updates', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Completely disable core, theme and plugin updates and auto-updates. Will also disable update checks, notices and emails.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle disable-components ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Disable Smaller Components

		$field_id = 'disable_smaller_components';
		$field_slug = 'disable-smaller-components';

		add_settings_field(
			$field_id, // Field ID
			'Disable Smaller Components', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Prevent smaller components from running or loading. Make the site more secure and load slightly faster.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'					=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_head_generator_tag';
		$field_slug = 'disable-head-generator-tag';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable the <strong>generator &lt;meta&gt; tag</strong> in &lt;head&gt;, which discloses the WordPress version number. Older versions(s) might contain unpatched security loophole(s).', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_head_wlwmanifest_tag';
		$field_slug = 'disable-head-wlwmanifest-tag';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable the <strong>Windows Live Writer (WLW) manifest &lt;link&gt; tag</strong> in &lt;head&gt;. The WLW app was discontinued in 2017.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_head_rsd_tag';
		$field_slug = 'disable-head-rsd-tag';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable the <strong>Really Simple Discovery (RSD) &lt;link&gt; tag</strong> in &lt;head&gt;. It\'s not needed if your site is not using pingback or remote (XML-RPC) client to manage posts.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_head_shortlink_tag';
		$field_slug = 'disable-head-shortlink-tag';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable the default <strong>WordPress shortlink &lt;link&gt; tag</strong> in &lt;head&gt;. Ignored by search engines and has minimal practical use case. Usually, a dedicated shortlink plugin or service is preferred that allows for nice names in the short links and tracking of clicks when sharing the link on social media.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_frontend_dashicons';
		$field_slug = 'disable-frontend-dashicons';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable loading of <strong>dashicons CSS and JS files</strong> on the front-end for public site visitors.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'disable_emoji_support';
		$field_slug = 'disable-emoji-support';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Disable <strong>emoji support for pages, posts and custom post types</strong> on the admin and frontend. The support is primarily useful for older browsers that do not have native support for it. Most modern browsers across different OSes and devices now have native support for it.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// =================================================================
		// SECURITY
		// =================================================================

		// Limit Login Attempts

		$field_id = 'limit_login_attempts';
		$field_slug = 'limit-login-attempts';

		add_settings_field(
			$field_id, // Field ID
			'Limit Login Attempts', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Prevent brute force attacks by limiting the number of failed login attempts allowed per IP address.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle security ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'login_fails_allowed';
		$field_slug = 'login-fails-allowed';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> 'failed login attempts allowed before 15 minutes lockout', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix narrow no-margin security ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'login_lockout_maxcount';
		$field_slug = 'login-lockout-maxcount';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> 'lockout(s) will block further login attempts for 24 hours', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix narrow no-margin security ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'login_attempts_log_table';
		$field_slug = 'login-attempts-log-table';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_datatable' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'datatable', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text datatable asenha-hide-th security ' . $field_slug, // Custom class for the <tr> element
				'table_title'			=> 'Failed Login Attempts Log',
				'table_name'			=> $wpdb->prefix . 'asenha_failed_logins',
			)
		);

		// Obfuscate Author Slugs

		$field_id = 'obfuscate_author_slugs';
		$field_slug = 'obfuscate-author-slugs';

		add_settings_field(
			$field_id, // Field ID
			'Obfuscate Author Slugs', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Obfuscate publicly exposed author page URLs that shows the user slugs / usernames, e.g. <em>sitename.com/author/username1/</em> into <em>sitename.com/author/a6r5b8ytu9gp34bv/</em>, and output 404 errors for the original URLs. Also obfuscates in /wp-json/wp/v2/users/ REST API endpoint.', // Custom argument
				'field_options_wrapper'	=> false, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle security ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Disable XML-RPC

		$field_id = 'disable_xmlrpc';
		$field_slug = 'disable-xmlrpc';

		add_settings_field(
			$field_id, // Field ID
			'Disable XML-RPC', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Protect your site from brute force, DOS and DDOS attacks via <a href="https://kinsta.com/blog/xmlrpc-php/#what-is-xmlrpcphp" target="_blank">XML-RPC</a>. Also disables trackbacks and pingbacks. ', // Custom argument
				'field_options_wrapper'	=> false, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle security ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// =================================================================
		// OPTIMIZATIONS
		// =================================================================

		// Image Upload Control

		$field_id = 'image_upload_control';
		$field_slug = 'image-upload-control';

		add_settings_field(
			$field_id, // Field ID
			'Image Upload Control', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Resize newly uploaded, large images to a smaller dimension and delete originally uploaded files. BMPs and non-transparent PNGs will be converted to JPGs and resized.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'image_max_width';
		$field_slug = 'image-max-width';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Max width:', // Custom argument
				'field_suffix'			=> 'pixels. <span class="faded">(Default is 1920 pixels)</span>', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th narrow optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'image_max_height';
		$field_slug = 'image-max-height';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Max height:', // Custom argument
				'field_suffix'			=> 'pixels <span class="faded">(Default is 1920 pixels)</span>', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> 'To exclude an image from conversion and resizing, append \'-nr\' suffix to the file name, e.g. bird-photo-4k-nr.jpg', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th narrow optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable Revisions Control

		$field_id = 'enable_revisions_control';
		$field_slug = 'enable-revisions-control';

		add_settings_field(
			$field_id, // Field ID
			'Revisions Control', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Prevent bloating the database by limiting the number of revisions to keep for some or all post types supporting revisions.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'revisions_max_number';
		$field_slug = 'revisions-max-number';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Limit to', // Custom argument
				'field_suffix'			=> 'revisions for:', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th extra-narrow optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'enable_revisions_control_for';
		$field_slug = 'enable-revisions-control-for';

		if ( is_array( $asenha_revisions_post_types ) ) {
			foreach ( $asenha_revisions_post_types as $post_type_slug => $post_type_label ) { // e.g. $post_type_slug is post, $post_type_label is Posts
				add_settings_field(
					$field_id . '_' . $post_type_slug, // Field ID
					'', // Field title
					[ $render_field, 'render_checkbox_subfield' ], // Callback to render field with custom arguments in the array below
					ASENHA_SLUG, // Settings page slug
					'main-section', // Section ID
					array(
						'parent_field_id'		=> $field_id, // Custom argument
						'field_id'				=> $post_type_slug, // Custom argument
						'field_name'			=> ASENHA_SLUG_U . '['. $field_id .'][' . $post_type_slug . ']', // Custom argument
						'field_label'			=> $post_type_label . ' <span class="faded">('. $post_type_slug .')</span>', // Custom argument
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half optimizations ' . $field_slug . ' ' . $post_type_slug, // Custom class for the <tr> element
					)
				);
			}
		}

		// Enable Heartbeat Control

		$field_id = 'enable_heartbeat_control';
		$field_slug = 'enable-heartbeat-control';

		add_settings_field(
			$field_id, // Field ID
			'Heartbeat Control', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Modify the interval of the WordPress heartbeat API or disable it on admin pages, post creation/edit screens and/or the frontend. This will help reduce CPU load on the server.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle optimizations ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'heartbeat_control_for_admin_pages';
		$field_slug = 'heartbeat-control-for-admin-pages';

		add_settings_field(
			$field_id, // Field ID
			'On admin pages', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> array(
					'Keep as is'	=> 'default',
					'Modify'		=> 'modify',
					'Disable'		=> 'disable',
				),
				'field_default'			=> 'default',
				'class'					=> 'asenha-radio-buttons optimizations ' . $field_slug, // Custom class for the <tr> element
			),
		);

		$field_id = 'heartbeat_interval_for_admin_pages';
		$field_slug = 'heartbeat-interval-for-admin-pages';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_select_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Set interval to once every', // Custom argument
				'field_suffix'			=> '<span class="faded">(Default is 1 minute)</span>', // Custom argument
				'field_select_options'	=> array(
					'15 seconds'	=> 15,
					'30 seconds'	=> 30,
					'1 minute'		=> 60,
					'2 minutes'		=> 120,
					'3 minutes'		=> 180,
					'5 minutes'		=> 300,
					'10 minutes'	=> 600,
				),
				'field_select_default'	=> 60,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th extra-narrow shift-up optimizations ' . $field_slug, // Custom class for the <tr> element
				'display_none_on_load'	=> true,
			)
		);

		$field_id = 'heartbeat_control_for_post_edit';
		$field_slug = 'heartbeat-control-for-post-edit';

		add_settings_field(
			$field_id, // Field ID
			'On post creation and edit screens', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> array(
					'Keep as is'	=> 'default',
					'Modify'		=> 'modify',
					'Disable'		=> 'disable',
				),
				'field_default'			=> 'default',
				'class'					=> 'asenha-radio-buttons optimizations top-border ' . $field_slug, // Custom class for the <tr> element
			),
		);

		$field_id = 'heartbeat_interval_for_post_edit';
		$field_slug = 'heartbeat-interval-for-post-edit';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_select_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Set interval to once every', // Custom argument
				'field_suffix'			=> '<span class="faded">(Default is 15 seconds)</span>', // Custom argument
				'field_select_options'	=> array( 
					'15 seconds'	=> 15,
					'30 seconds'	=> 30, 
					'45 seconds'	=> 45, 
					'60 seconds'	=> 60, 
					'90 seconds'	=> 90, 
					'120 seconds'	=> 120 
				),
				'field_select_default'	=> 15,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th extra-narrow shift-up optimizations ' . $field_slug, // Custom class for the <tr> element
				'display_none_on_load'	=> true,
			)
		);

		$field_id = 'heartbeat_control_for_frontend';
		$field_slug = 'heartbeat-control-for-frontend';

		add_settings_field(
			$field_id, // Field ID
			'On the frontend', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> array(
					'Keep as is'	=> 'default',
					'Modify'		=> 'modify',
					'Disable'		=> 'disable',
				),
				'field_default'			=> 'default',
				'class'					=> 'asenha-radio-buttons optimizations top-border ' . $field_slug, // Custom class for the <tr> element
			),
		);

		$field_id = 'heartbeat_interval_for_frontend';
		$field_slug = 'heartbeat-interval-for-frontend';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_select_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> 'Set interval to once every', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_select_options'	=> array( 
					'15 seconds'	=> 15,
					'30 seconds'	=> 30,
					'1 minute'		=> 60,
					'2 minutes'		=> 120,
					'3 minutes'		=> 180,
					'5 minutes'		=> 300,
					'10 minutes'	=> 600,
				),
				'field_select_default'	=> 60,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-number asenha-hide-th extra-narrow shift-up optimizations ' . $field_slug, // Custom class for the <tr> element
				'display_none_on_load'	=> true,
			)
		);

		// =================================================================
		// UTILITIES
		// =================================================================

		// SMTP Email Delivery

		$field_id = 'smtp_email_delivery';
		$field_slug = 'smtp-email-delivery';

		add_settings_field(
			$field_id, // Field ID
			'SMTP Email Delivery', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Use external SMTP service to ensure notification and transactional emails from your site are being delivered to inboxes.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_host';
		$field_slug = 'smtp-host';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Host</span>', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix wide utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_port';
		$field_slug = 'smtp-port';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Port</span>', // Field title
			[ $render_field, 'render_number_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix narrow utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_security';
		$field_slug = 'smtp-security';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Security</span>', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> array(
					'None'		=> 'none',
					'SSL'		=> 'ssl',
					'TLS'		=> 'tls',
				),
				'field_default'			=> 'default',
				'class'					=> 'asenha-radio-buttons with-prefix-suffix utilities ' . $field_slug, // Custom class for the <tr> element
			),
		);

		$field_id = 'smtp_username';
		$field_slug = 'smtp-username';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Username</span>', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix wide utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_password';
		$field_slug = 'smtp-password';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">Password</span>', // Field title
			[ $render_field, 'render_password_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix wide utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_default_from_name';
		$field_slug = 'smtp-default-from-name';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">FROM name</span>', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix wide utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_default_from_email';
		$field_slug = 'smtp-default-from-email';

		add_settings_field(
			$field_id, // Field ID
			'<span class="field-sublabel sublabel-wide">FROM email</span>', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix wide utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_default_from_description';
		$field_slug = 'smtp-default-from-description';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_description'		=> 'If set, the FROM name/email overrides WordPress core defaults but can still be overridden by plugins that enables custom FROM name/email, e.g. form plugins.', // Custom argument
				'class'					=> 'asenha-description utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'smtp_debug';
		$field_slug = 'smtp-debug';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_checkbox_plain' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				'field_label'			=> 'Enable debug mode and output the debug info into WordPress debug.log file.', // Custom argument
				'class'					=> 'asenha-checkbox asenha-hide-th top-border utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// View Admin as Role

		$field_id = 'view_admin_as_role';
		$field_slug = 'view-admin-as-role';

		add_settings_field(
			$field_id, // Field ID
			'View Admin as Role', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'View admin pages and the site (logged-in) as one of the non-administrator user roles.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Enable Password Protection

		$field_id = 'enable_password_protection';
		$field_slug = 'enable-password-protection';

		add_settings_field(
			$field_id, // Field ID
			'Password Protection', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Password-protect the entire site to hide the content from public view and search engine bots / crawlers. Logged-in administrators can still access the site as usual.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'password_protection_password';
		$field_slug = 'password-protection-password';

		add_settings_field(
			$field_id, // Field ID
			'Set the password:', // Field title
			[ $render_field, 'render_password_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'with-prefix-suffix', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '<span class="faded">(Default is \'secret\')</span>', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-text with-prefix-suffix utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Maintenance Mode

		$field_id = 'maintenance_mode';
		$field_slug = 'maintenance-mode';

		add_settings_field(
			$field_id, // Field ID
			'Maintenance Mode', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'					=> $field_id, // Custom argument
				'field_slug'				=> $field_slug, // Custom argument
				'field_name'				=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'			=> 'Show a customizable maintenance page on the frontend while performing a brief maintenance to your site. Logged-in administrators can still view the site as usual.', // Custom argument
				'field_options_wrapper'		=> true, // Custom argument. Add container for additional options
				'field_options_moreless'	=> true,  // Custom argument. Add show more/less toggler.
				'class'						=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'maintenance_page_heading';
		$field_slug = 'maintenance-page-heading';

		add_settings_field(
			$field_id, // Field ID
			'Heading', // Field title
			[ $render_field, 'render_text_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> '', // Custom argument
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'field_placeholder'		=> 'We\'ll be back soon.',
				'class'					=> 'asenha-text utilities full-width ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'maintenance_page_description';
		$field_slug = 'maintenance-page-description';

		add_settings_field(
			$field_id, // Field ID
			'Description', // Field title
			[ $render_field, 'render_textarea_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_type'			=> 'textarea', // Custom argument
				'field_rows'			=> 5,
				'field_intro'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'field_placeholder'		=> 'This site is undergoing maintenance for an extended period today. Thanks for your patience.',
				'class'					=> 'asenha-textarea utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		$field_id = 'maintenance_page_background';
		$field_slug = 'maintenance-page-background';

		add_settings_field(
			$field_id, // Field ID
			'Background', // Field title
			[ $render_field, 'render_radio_buttons_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '[' . $field_id . ']', // Custom argument
				// 'field_label'			=> 'Temporary label', // Custom argument
				'field_radios'			=> array(
					'Stripes'	=> 'stripes',
					'Curves'	=> 'curves',
					'Lines'		=> 'lines',
				),
				'field_default'			=> 'default',
				'class'					=> 'asenha-radio-buttons utilities ' . $field_slug, // Custom class for the <tr> element
			),
		);

		$field_id = 'maintenance_mode_description';
		$field_slug = 'maintenance-mode-description';

		add_settings_field(
			$field_id, // Field ID
			'', // Field title
			[ $render_field, 'render_description_subfield' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_description'		=> '<div class="asenha-warning"><strong>Please clear your cache</strong> after enabling or disabling maintenance mode. This ensures site visitors see either the maintenance page or the actual content of each page.</div>', // Custom argument
				'class'					=> 'asenha-description utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// Redirect 404 to Homepage

		$field_id = 'redirect_404_to_homepage';
		$field_slug = 'redirect-404-to-homepage';

		add_settings_field(
			$field_id, // Field ID
			'Redirect 404 to Homepage', // Field title
			[ $render_field, 'render_checkbox_toggle' ], // Callback to render field with custom arguments in the array below
			ASENHA_SLUG, // Settings page slug
			'main-section', // Section ID
			array(
				'field_id'				=> $field_id, // Custom argument
				'field_slug'			=> $field_slug, // Custom argument
				'field_name'			=> ASENHA_SLUG_U . '['. $field_id .']', // Custom argument
				'field_description'		=> 'Perform 301 (permanent) redirect to the homepage for all 404 (not found) pages.', // Custom argument
				'field_options_wrapper'	=> true, // Custom argument. Add container for additional options
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

	}

}