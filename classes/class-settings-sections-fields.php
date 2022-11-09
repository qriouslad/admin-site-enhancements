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
		
		// Add "Content Management" section

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

		// Call WordPress globals required for the fields

		global $wp_roles;
		$roles = $wp_roles->get_names();

		// ===== CONTENT MANAGEMENT =====

		// Enable Page and Post Duplication

		$field_id = 'enable_duplication';
		$field_slug = 'enable-duplication';

		add_settings_field(
			$field_id, // Field ID
			'Enable Page and Post Duplication', // Field title
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
			'Enable Media Replacement', // Field title
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
				'field_description'		=> 'Improve the usefulness of listing pages of various post types by adding / removing columns and elements.', // Custom argument
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

		// ===== ADMIN INTERFACE =====

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
				'class'					=> 'asenha-toggle admin-interface ' . $field_slug, // Custom class for the <tr> element
			)
		);

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
				'field_description'			=> 'Customize the order of the admin menu and optionally hide some items.', // Custom argument
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

		// ===== SECURITY =====

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
				'class'					=> 'asenha-toggle security ' . $field_slug, // Custom class for the <tr> element
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
				'class'					=> 'asenha-text with-prefix-suffix security ' . $field_slug, // Custom class for the <tr> element
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

		// ===== UTILITIES ======

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
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
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
				'class'					=> 'asenha-text with-prefix-suffix utilities ' . $field_slug, // Custom class for the <tr> element
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
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half utilities ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
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
				'class'					=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
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
				'class'					=> 'asenha-text with-prefix-suffix utilities ' . $field_slug, // Custom class for the <tr> element
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
						'class'					=> 'asenha-checkbox asenha-hide-th asenha-half utilities ' . $field_slug . ' ' . $role_slug, // Custom class for the <tr> element
					)
				);

			}
		}

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

		// Custom Admin CSS

		$field_id = 'enable_custom_admin_css';
		$field_slug = 'enable-custom-admin-css';

		add_settings_field(
			$field_id, // Field ID
			'Enable Custom Admin CSS', // Field title
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
				'class'						=> 'asenha-toggle utilities ' . $field_slug, // Custom class for the <tr> element
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
				'field_prefix'			=> '', // Custom argument
				'field_suffix'			=> '', // Custom argument
				'field_description'		=> '', // Custom argument
				'class'					=> 'asenha-textarea asenha-hide-th syntax-highlighted utilities ' . $field_slug, // Custom class for the <tr> element
			)
		);

		// ===== DISABLE COMPONENTS =====

	}

}