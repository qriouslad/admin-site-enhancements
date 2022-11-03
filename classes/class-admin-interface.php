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
<html <?php echo $dir_attr; ?>>
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
		if ( array_key_exists( 'hide_default_wp_logo_menu', $options ) && $options['hide_default_wp_logo_menu'] ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 ); // priority needs to match default value. Use QM to reference.
		}

		// Hide Comments Counter/Link
		if ( array_key_exists( 'hide_ab_comments_menu', $options ) && $options['hide_ab_comments_menu'] ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 ); // priority needs to match default value. Use QM to reference.
		}

	}

}