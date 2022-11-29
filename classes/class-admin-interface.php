<?php

namespace ASENHA\Classes;
use WP_Admin_Bar;

/**
 * Class related to Admin Interface features
 *
 * @since 1.2.0
 */
class Admin_Interface {

	/**
	 * Wrapper for admin notices being output on admin screens
	 *
	 * @since 1.2.0
	 */
	public function admin_notices_wrapper() {

		echo '<div class="asenha-admin-notices-drawer" style="display:none;"><h2>Admin Notices</h2></div>';

	}

	/**
	 * Admin bar menu item for the hidden admin notices
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_admin_bar/add_menu/
	 * @link https://developer.wordpress.org/reference/classes/wp_admin_bar/add_node/
	 * @since 1.2.0
	 */
	public function admin_notices_menu( WP_Admin_Bar $wp_admin_bar ) {

		$wp_admin_bar->add_menu( array(
			'id'		=> 'asenha-hide-admin-notices',
			'parent'	=> 'top-secondary',
			'grou'		=> null,
			'title'		=> 'Notices<span class="asenha-admin-notices-counter" style="opacity:0;">0</span>',
			// 'href'		=> '',
			'meta'		=> array(
				'class'		=> 'asenha-admin-notices-menu',
				'title'		=> 'Click to view hidden admin notices',
			),
		) );

	}

	/**
	 * Inline CSS for the admin bar notices menu
	 *
	 * @since 1.2.0
	 */
	public function admin_notices_menu_inline_css() {

		wp_add_inline_style( 'admin-bar', '

			#wpadminbar .asenha-admin-notices-counter {
				box-sizing: border-box;
				margin: 1px 0 -1px 6px ;
				padding: 2px 6px 3px 5px;
				min-width: 18px;
				height: 18px;
				border-radius: 50%;
				background-color: #ca4a1f;
				color: #fff;
				font-size: 11px;
				line-height: 1.6;
				text-align: center;
			}

		' );

	}

	/**
	 * Hide admin bar on the frontend for the user roles selected
	 *
	 * @since 1.3.0
	 */
	public function hide_admin_bar_for_roles() {

		$options = get_option( ASENHA_SLUG_U );
		$hide_admin_bar = $options['hide_admin_bar'];
		$for_roles = $options['hide_admin_bar_for'];

		$current_user = wp_get_current_user();
		$current_user_roles = (array) $current_user->roles; // single dimensional array of role slugs

		// User has no role, i.e. logged-out

		if ( count( $current_user_roles ) == 0 ) {
			return false; // hide admin bar
		}

		// User has role(s). Do further checks.

		if ( isset( $for_roles ) && ( count( $for_roles ) > 0 ) ) {

			// Assemble single-dimensional array of roles for which admin bar would be hidden

			$roles_admin_bar_hidden = array();
	
			foreach( $for_roles as $role_slug => $admin_bar_hidden ) {
				if ( $admin_bar_hidden ) {
					$roles_admin_bar_hidden[] = $role_slug;
				}
			}

			// Check if any of the current user roles is one for which admin bar should be hidden

			foreach ( $current_user_roles as $role ) {
				if ( in_array( $role, $roles_admin_bar_hidden ) ) {
					return false; // hide admin bar
				}
			}

		}

		return true; // show admin bar

	}

	/**
	 * Add menu bar item to view admin as one of the user roles
	 *
	 * @param $wp_admin_bar The WP_Admin_Bar instance
	 * @link https://developer.wordpress.org/reference/hooks/admin_bar_menu/
	 * @link https://developer.wordpress.org/reference/classes/wp_admin_bar/
	 * @since 1.8.0
	 */
	public function view_admin_as_admin_bar_menu( $wp_admin_bar ) {

		// Get which role slug is currently set to "View as"
		$viewing_admin_as = get_user_meta( get_current_user_id(), '_asenha_viewing_admin_as', true );

		if ( empty( $viewing_admin_as ) ) {
			update_user_meta( get_current_user_id(), '_asenha_viewing_admin_as', 'administrator' );
		}

		// Get the role name, translated if available, from the role slug
		$wp_roles = wp_roles()->roles;

		foreach ( $wp_roles as $wp_role_slug => $wp_role_info ) {

			if ( $wp_role_slug == $viewing_admin_as ) {

				$viewing_admin_as_role_name = $wp_role_info['name'];

			}

		}

		if ( ! isset( $viewing_admin_as_role_name ) ) {

			$viewing_admin_as_role_name = $viewing_admin_as;

		}

		$translated_name_for_viewing_admin_as = ucfirst( $viewing_admin_as_role_name );

		// Add parent menu basd on the role being set to "View as"

		if ( 'administrator' == $viewing_admin_as ) {

			// Add parent menu
			$wp_admin_bar->add_menu( array(
				'id'		=> 'asenha-view-admin-as-role',
				'parent'	=> 'top-secondary',
				'title'		=> 'View as <span style="font-size:0.8125em;">&#9660;</span>',
				'href'		=> '#',
				'meta'		=> array(
					'title'	=> 'View admin pages and the site (logged-in) as one of the following user roles.'
				),
			) );

		} else {

			// Add parent menu
			$wp_admin_bar->add_menu( array(
				'id'		=> 'asenha-view-admin-as-role',
				'parent'	=> 'top-secondary',
				'title'		=> 'Viewing as ' . $translated_name_for_viewing_admin_as . ' <span style="font-size:0.8125em;">&#9660;</span>',
				'href'		=> '#',
			) );

		}

		// Get available role(s) to switch to
		$roles_to_switch_to = $this->get_roles_to_switch_to();

		// Add role(s) to switch to as sub-menu

		if ( 'administrator' == $viewing_admin_as ) {

			// Add submenu for each role other than Administrator

			$i = 1;

			foreach ( $roles_to_switch_to as $role_slug => $data ) {

				$wp_admin_bar->add_menu( array(
					'id'		=> 'role' . $i . '_' . $role_slug, // id based on role slug, e.g. role1_editor, role5_shop_manager
					'parent'	=> 'asenha-view-admin-as-role',
					'title'		=> $data['role_name'], // role name, e.g. Editor, Shop Manager
					'href'		=> $data['nonce_url'], // nonce URL for each role
				) );

				$i++;

			}

		} else {

			// Add submenu to switch back to Administrator role

			foreach ( $roles_to_switch_to as $role_slug => $data ) {

				$wp_admin_bar->add_menu( array(
					'id'		=> 'role_' . $role_slug, // id based on role slug, e.g. role1_editor, role5_shop_manager
					'parent'	=> 'asenha-view-admin-as-role',
					'title'		=> 'Switch back to ' . $data['role_name'], // role name, e.g. Editor, Shop Manager
					'href'		=> $data['nonce_url'], // nonce URL for each role

				) );
			
			}

		}

	}

	/** 
	 * Get roles availble to switch to
	 *
	 * @since 1.8.0
	 */
	private function get_roles_to_switch_to() {

		$current_user = wp_get_current_user();
		$current_user_role_slugs = $current_user->roles; // indexed array of current user role slug(s)

		// Get full list of roles defined in WordPress
		$wp_roles = wp_roles()->roles;

		$roles_to_switch_to = array();

		// Get which role slug is currently active for viewing
		$viewing_admin_as = get_user_meta( get_current_user_id(), '_asenha_viewing_admin_as', true );

		if ( 'administrator' == $viewing_admin_as ) {

			 // Exclude 'Administrator' from the "View as" menu

			foreach ( $wp_roles as $wp_role_slug => $wp_role_info ) {

				if ( ! in_array( $wp_role_slug,$current_user_role_slugs ) ) {

					$roles_to_switch_to[$wp_role_slug] = array( 
						'role_name'	=> $wp_role_info['name'], // role name, e.g. Editor, Shop Manager
						'nonce_url'	=> wp_nonce_url(
											add_query_arg( array(
												'action'	=> 'switch_role_to',
												'role'		=> $wp_role_slug,
											) ), // add query parameters to current URl, this is the $actionurl that will be appended with the nonce action
											'asenha_view_admin_as_' . $wp_role_slug, // the nonce $action name
											'nonce', // the nonce url parameter name
										), // will result in a URL that looks like https://www.example.com/wp-admin/index.php?action=switch_role_to&role=editor&nonce=2ced3a40df
					);

				}

			}

		} else {

			// Only show switch back to Administrator in the "View as" menu

			$roles_to_switch_to['administrator'] = array( 
				'role_name'	=> 'Administrator', // role name, e.g. Editor, Shop Manager
				'nonce_url'	=> wp_nonce_url(
									add_query_arg( array(
										'action'	=> 'switch_back_to_administrator',
										'role'		=> 'administrator',
									) ), // add query parameters to current URl, this is the $actionurl that will be appended with the nonce action
									'asenha_view_admin_as_administrator', // the nonce $action name
									'nonce', // the nonce url parameter name
								), // will result in a URL that looks like https://www.example.com/wp-admin/index.php?action=switch_role_to&role=editor&nonce=2ced3a40df
			);
		}

		return $roles_to_switch_to; // array of $role_slug => $nonce_url

	}

	/**
	 * Switch user role to view admin and site
	 *
	 * @since 1.8.0
	 */
	public function role_switcher_to_view_admin_as() {

		$current_user = wp_get_current_user();
		$current_user_role_slugs = $current_user->roles; // indexed array of current user role slug(s)

		if ( isset( $_REQUEST['action'] ) && isset( $_REQUEST['role'] ) && isset( $_REQUEST['nonce'] ) ) {

			$action = sanitize_text_field( $_REQUEST['action'] );
			$new_role = sanitize_text_field( $_REQUEST['role'] );
			$nonce = sanitize_text_field( $_REQUEST['nonce'] );

			if ( 'switch_role_to' === $action ) {

				// Check nonce validity and role existence

				$wp_roles = array_keys( wp_roles()->roles ); // indexed array of all WP roles

				if ( ! wp_verify_nonce( $nonce, 'asenha_view_admin_as_' . $new_role ) || ! in_array( $new_role, $wp_roles ) ) {
					return; // cancel role switching
				}

				// Get original roles (before role switching) of the current user
				$original_role_slugs = get_user_meta( get_current_user_id(), '_asenha_view_admin_as_original_roles', true );

				// Store original user role(s) before switching it to another role
				
				if ( empty( $original_role_slugs ) ) {

					update_user_meta( get_current_user_id(), '_asenha_view_admin_as_original_roles', $current_user_role_slugs );

				}

				// Remove all current roles from current user.
				foreach ( $current_user_role_slugs as $current_user_role_slug ) {

					$current_user->remove_role( $current_user_role_slug );

				}

				// Add new role to current user
				$current_user->add_role( $new_role );

				// Mark that the user has switched to a non-administrator role
				update_user_meta( get_current_user_id(), '_asenha_viewing_admin_as', $new_role );

				// Redirect to admin dashboard
				wp_safe_redirect( get_admin_url() );
				exit;

			}

			if ( 'switch_back_to_administrator' === $action ) {

				// Check nonce validity

				if ( ! wp_verify_nonce( $nonce, 'asenha_view_admin_as_administrator' ) || ( $new_role != 'administrator' ) ) {
					return; // cancel role switching
				}

				// Remove all current roles from current user.
				foreach ( $current_user_role_slugs as $current_role_slug ) {

					$current_user->remove_role( $current_role_slug );

				}

				// Get original roles (before role switching) of the current user
				$original_role_slugs = get_user_meta( get_current_user_id(), '_asenha_view_admin_as_original_roles', true );
				
				// Add the original roles to the current user
				foreach ( $original_role_slugs as $original_role_slug ) {

					$current_user->add_role( $original_role_slug );

				}

				// Mark that the user has switched back to an administrator role
				update_user_meta( get_current_user_id(), '_asenha_viewing_admin_as', 'administrator' );

			}

		} elseif ( isset( $_REQUEST['reset-view-as'] ) ) {

			$reset_view_as = sanitize_text_field( $_REQUEST['reset-view-as'] );

			if ( $reset_view_as == 'yes' ) {

				// Remove all current roles from current user.
				foreach ( $current_user_role_slugs as $current_role_slug ) {

					$current_user->remove_role( $current_role_slug );

				}

				// Get original roles (before role switching) of the current user
				$original_role_slugs = get_user_meta( get_current_user_id(), '_asenha_view_admin_as_original_roles', true );
				
				// Add the original roles to the current user
				foreach ( $original_role_slugs as $original_role_slug ) {

					$current_user->add_role( $original_role_slug );

				}

				// Mark that the user has switched back to an administrator role
				update_user_meta( get_current_user_id(), '_asenha_viewing_admin_as', 'administrator' );

				// Redirect to admin dashboard
				wp_safe_redirect( get_admin_url() );
				exit;

			}

		}

	}

	/**
	 * Show custom error page on switch failure, which causes inability to view admin dashboard/pages
	 *
	 * @since 1.8.0
	 */
	public function custom_error_page_on_switch_failure( $callback ) {

		?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8; ?>" />
	<meta name="viewport" content="width=device-width">
	<title>WordPress Error</title>
	<style type="text/css">
		html {
			background: #f1f1f1;
		}
		body {
			background: #fff;
			border: 1px solid #ccd0d4;
			color: #444;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
			margin: 2em auto;
			padding: 1em 2em;
			max-width: 700px;
			-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
			box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
		}
		h1 {
			border-bottom: 1px solid #dadada;
			clear: both;
			color: #666;
			font-size: 24px;
			margin: 30px 0 0 0;
			padding: 0;
			padding-bottom: 7px;
		}
		#error-page {
			margin-top: 50px;
		}
		#error-page p,
		#error-page .wp-die-message {
			font-size: 14px;
			line-height: 1.5;
			margin: 20px 0;
		}
		#error-page .wp-die-message:last-of-type {
			display: none;
		}
		#error-page code {
			font-family: Consolas, Monaco, monospace;
		}
		a {
			color: #0073aa;
		}
		a:hover,
		a:active {
			color: #006799;
		}
		a:focus {
			color: #124964;
			-webkit-box-shadow:
				0 0 0 1px #5b9dd9,
				0 0 2px 1px rgba(30, 140, 190, 0.8);
			box-shadow:
				0 0 0 1px #5b9dd9,
				0 0 2px 1px rgba(30, 140, 190, 0.8);
			outline: none;
		}
	</style>
</head>
<body id="error-page">
	<div class="wp-die-message">Something went wrong. <a href="<?php echo home_url( '?reset-view-as=yes' ); ?>">Click here</a> to go back to the admin dashboard.</div>
</body>
</html>
		<?php

	}

	/**
	 * Modify admin bar menu for Admin Interface >> Hide or Modify Elements feature
	 *
	 * @param $wp_admin_bar object The admin bar.
	 * @since 1.9.0
	 */
	public function modify_admin_bar_menu( $wp_admin_bar ) {

		$options = get_option( ASENHA_SLUG_U, array() );

		// Hide WP Logo Menu
		if ( array_key_exists( 'hide_ab_wp_logo_menu', $options ) && $options['hide_ab_wp_logo_menu'] ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 ); // priority needs to match default value. Use QM to reference.
		}

		// Hide Customize Menu
		if ( array_key_exists( 'hide_ab_customize_menu', $options ) && $options['hide_ab_customize_menu'] ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_customize_menu', 40 ); // priority needs to match default value. Use QM to reference.
		}

		// Hide Updates Counter/Link
		if ( array_key_exists( 'hide_ab_updates_menu', $options ) && $options['hide_ab_updates_menu'] ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 50 ); // priority needs to match default value. Use QM to reference.
		}

		// Hide Comments Counter/Link
		if ( array_key_exists( 'hide_ab_comments_menu', $options ) && $options['hide_ab_comments_menu'] ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 ); // priority needs to match default value. Use QM to reference.
		}

		// Hide New Content Menu
		if ( array_key_exists( 'hide_ab_new_content_menu', $options ) && $options['hide_ab_new_content_menu'] ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 70 ); // priority needs to match default value. Use QM to reference.
		}

		// Hide 'Howdy' text
		if ( array_key_exists( 'hide_ab_howdy', $options ) && $options['hide_ab_howdy'] ) {

			// Remove the whole my account sectino and later rebuild it
			remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_item', 7 );

			$current_user = wp_get_current_user();
			$user_id = get_current_user_id();
			$profile_url  = get_edit_profile_url( $user_id );

			$avatar = get_avatar( $user_id, 26 ); // size 26x26 pixels
			$display_name = $current_user->display_name;
			$class = 'with-avatar';

			$wp_admin_bar->add_menu( array(
				'id'		=> 'my-account',
				'parent'	=> 'top-secondary',
				'title'		=> $display_name . $avatar,
				'href'		=> $profile_url,
				'meta'		=> array(
					'class'		=> $class,
				),
			) );

		}

	}

	/**
	 * Save custom menu order into an option
	 *
	 * @since 2.0.0
	 */
	public function save_custom_menu_order() {

		if ( isset( $_REQUEST ) && ( 'save_custom_menu_order' == $_REQUEST['action'] ) ) {

			$options = get_option( ASENHA_SLUG_U );

			// Empty option value first
			// $options['custom_menu_order'] = '';
			// update_option( ASENHA_SLUG_U, $options );

			// Save new value
			// $options = get_option( ASENHA_SLUG_U );
			$options['custom_menu_order'] = sanitize_text_field( $_REQUEST['menu_order'] );
			$success = update_option( ASENHA_SLUG_U, $options ); // true or false

			if ( $success ) {

				$data = array(
					'status'	=> 'success',
					'message'	=> 'Custom menu order was successfully saved.',
				);

			} else {

				$data = array(
					'status'	=> 'error',
					'message'	=> 'Custom menu order was not saved.',
				);

			}

			echo json_encode( $data );

		}

	}

	/**
	 * Render custom menu order
	 *
	 * @param $menu_order array an ordered array of menu items
	 * @link https://developer.wordpress.org/reference/hooks/menu_order/
	 * @since 2.0.0
	 */
	public function render_custom_menu_order( $menu_order ) {

		global $menu;

		$options = get_option( ASENHA_SLUG_U );

		// Get current menu order. We're not using the default $menu_order which uses index.php, edit.php as array values.

		$current_menu_order = array();

		foreach ( $menu as $menu_key => $menu_info ) {

			if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
				$menu_item_id = $menu_info[2];
			} else {
				$menu_item_id = $menu_info[5];
			}

			$current_menu_order[] = array( $menu_item_id, $menu_info[2] );

		}

		// Get custom menu order

		if ( array_key_exists( 'custom_menu_order', $options ) ) {
			$custom_menu_order = $options['custom_menu_order']; // comma separated
		} else {
			$custom_menu_order = '';
		}

		$custom_menu_order = explode( ",", $custom_menu_order ); // array of menu ID, e.g. menu-dashboard

		// Return menu order for rendering

		$rendered_menu_order = array();

		foreach ( $custom_menu_order as $custom_menu_item_id ) {

			foreach ( $current_menu_order as $current_menu_item_id => $current_menu_item ) {

				if ( $custom_menu_item_id == $current_menu_item[0] ) {

					$rendered_menu_order[] = $current_menu_item[1];

				}

			}

		}

		return $rendered_menu_order;

	}

	/**
	 * Save menu items that was chosen to be hidden
	 *
	 * @since 2.0.0
	 */
	public function save_hidden_menu_items() {

		if ( isset( $_REQUEST ) && ( 'save_hidden_menu_items' == $_REQUEST['action'] ) ) {

			$options = get_option( ASENHA_SLUG_U );

			// Empty option value first
			// $options['custom_menu_order'] = '';
			// update_option( ASENHA_SLUG_U, $options );

			// Save new value
			// $options = get_option( ASENHA_SLUG_U );
			$options['custom_menu_hidden'] = sanitize_text_field( $_REQUEST['hidden_menu_items'] );
			$success = update_option( ASENHA_SLUG_U, $options ); // true or false

			if ( $success ) {

				$data = array(
					'status'	=> 'success',
					'message'	=> 'Hidden menu items was successfully saved.',
				);

			} else {

				$data = array(
					'status'	=> 'error',
					'message'	=> 'Hidden menu items was not saved.',
				);

			}

			echo json_encode( $data );

		}

	}

	/**
	 * Apply custom menu item titles
	 *
	 * @since 2.9.0
	 */
	public function apply_custom_menu_item_titles() {

		global $menu;

		$options = get_option( ASENHA_SLUG_U );

		// Get custom menu item titles
		if ( array_key_exists( 'custom_menu_titles', $options ) ) {
			$custom_menu_titles = $options['custom_menu_titles'];
			$custom_menu_titles = explode( ',', $custom_menu_titles );
		} else {
			$custom_menu_titles = array();
		}	

		$i = 1;

		foreach ( $menu as $menu_key => $menu_info ) {

			do_action( 'inspect', [ 'menu_key_' . $i, $menu_key ] );
			do_action( 'inspect', [ 'menu_info_' . $i, $menu_info ] );

			if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
				$menu_item_id = $menu_info[2];
			} else {
				$menu_item_id = $menu_info[5];
			}

			// Get defaul/custom menu item title
			foreach ( $custom_menu_titles as $custom_menu_title ) {

				// At this point, $custom_menu_title value looks like toplevel_page_snippets__Code Snippets

				$custom_menu_title = explode( '__', $custom_menu_title );

				if ( $custom_menu_title[0] == $menu_item_id ) {
					$menu_item_title = $custom_menu_title[1]; // e.g. Code Snippets
					break; // stop foreach loop so $menu_item_title is not overwritten in the next iteration
				} else {
					$menu_item_title = $menu_info[0];
				}

			}

			$menu[$menu_key][0] = $menu_item_title;

			$i++;

		}
	}

	/**
	 * Hide menu items by adding 'hidden' class (part of WP Core's common.css)
	 *
	 * @since 2.0.0
	 */
	public function hide_menu_items() {

		global $menu;

		$options = get_option( ASENHA_SLUG_U );

		// Get hidden menu items

		if ( array_key_exists( 'custom_menu_hidden', $options ) ) {
			$hidden_menu = $options['custom_menu_hidden'];
			$hidden_menu = explode( ',', $hidden_menu );
		} else {
			$hidden_menu = array();
		}

		foreach ( $menu as $menu_key => $menu_info ) {

			if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
				$menu_item_id = $menu_info[2];
			} else {
				$menu_item_id = $menu_info[5];
			}

			if ( in_array( $menu_item_id, $hidden_menu ) ) {

				$menu[$menu_key][4] = $menu_info[4] . ' hidden asenha_hidden_menu';

			}

		}

	}

	/**
	 * Add toggle to show hidden menu items
	 *
	 * @since 2.0.0
	 */
	public function add_hidden_menu_toggle() {

		$options = get_option( ASENHA_SLUG_U );

		// Get hidden menu items

		if ( array_key_exists( 'custom_menu_hidden', $options ) ) {
			$hidden_menu = $options['custom_menu_hidden'];
		} else {
			$hidden_menu = '';
		}

		if ( ! empty( $hidden_menu ) ) {

			add_menu_page(
				'Show All',
				'Show All',
				'manage_options',
				'asenha_show_hidden_menu',
				function () {  return false;  },
				"dashicons-arrow-down-alt2",
				300 // position
			);

			add_menu_page(
				'Show Less',
				'Show Less',
				'manage_options',
				'asenha_hide_hidden_menu',
				function () {  return false;  },
				"dashicons-arrow-up-alt2",
				301 // position
			);
		}

	}

	/**
	 * Script to toggle hidden menu itesm
	 *
	 * @since 2.0.0
	 */
	public function enqueue_toggle_hidden_menu_script() {

		$options = get_option( ASENHA_SLUG_U );

		// Get hidden menu items

		if ( array_key_exists( 'custom_menu_hidden', $options ) ) {
			$hidden_menu = $options['custom_menu_hidden'];
		} else {
			$hidden_menu = '';
		}

		if ( ! empty( $hidden_menu ) ) {

			// Script to set behaviour and actions of the sortable menu
			wp_enqueue_script( 'asenha-toggle-hidden-menu', ASENHA_URL . 'assets/js/toggle-hidden-menu.js', array(), ASENHA_VERSION, false );

		}

	}

}