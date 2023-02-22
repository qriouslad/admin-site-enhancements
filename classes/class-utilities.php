<?php

namespace ASENHA\Classes;
use WP_Error;

/**
 * Class related to Utilities features
 *
 * @since 1.5.0
 */
class Utilities {

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
	 * Show admin bar status icon
	 *
	 * @since 4.1.0
	 */
	public function show_admin_bar_icon() {
		add_action( 'wp_before_admin_bar_render', [ $this, 'add_wp_admin_bar_item' ] );
		add_action( 'admin_head', [ $this, 'add_wp_admin_bar_item_styles' ] );
		add_action( 'wp_head', [ $this, 'add_wp_admin_bar_item_styles' ] );
	}

	/**
	 * Add WP Admin Bar item
	 *
	 * @since 4.1.0
	 */
	public function add_wp_admin_bar_item() {
		global $wp_admin_bar;

		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {
				$wp_admin_bar->add_menu( array(
					'id'	=> 'password_protection',
					'title'	=> '',
					'href'	=> admin_url( 'tools.php?page=admin-site-enhancements#utilities' ),
					'meta'	=> array(
						'title'	=> 'Password protection is enabled for this site.',
					),
				) );
			}
		}

	}

	/**
	 * Add icon and CSS for admin bar item
	 *
	 * @since 4.1.0
	 */
	public function add_wp_admin_bar_item_styles() {

		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {

				?>
				<style>
					#wp-admin-bar-password_protection { 
						background-color: green !important;
						transition: .25s;
					}
					#wp-admin-bar-password_protection > .ab-item { 
						color: #fff !important;  
					}
					#wp-admin-bar-password_protection > .ab-item:before { 
						content: "\f160"; 
						top: 2px; 
						color: #fff !important; 
						margin-right: 0px; 
					}
					#wp-admin-bar-password_protection:hover > .ab-item { 
						background-color: #006600 !important; 
						color: #fff; 
					}
				</style>
				<?php

			}
		}

	}

	/**
	 * Disable page caching
	 *
	 * @since 4.1.0
	 */
	public function maybe_disable_page_caching() {

		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

	}

	/**
	 * Maybe show login form
	 *
	 * @since 4.1.0
	 */
	public function maybe_show_login_form() {

		// When user is logged-in as in an administrator
		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {
				return; // Do not load login form or perform redirection to the login form
			}
		}

		// When site visitor has entered correct password
		$auth_cookie = $_COOKIE['asenha_password_protection'];

		if ( 'authenticated' == $auth_cookie ) {
			return; // Do not load login form or perform redirection to the login form
		}

		if ( isset( $_REQUEST['protected-page'] ) && 'view' == $_REQUEST['protected-page'] ) {// Show login form

			$password_protected_login_page_template = ASENHA_PATH . 'includes/password-protected-login.php';

			load_template( $password_protected_login_page_template );
			exit();

		} else { // Redirect to login form

			$current_url = ( is_ssl() ? 'https://' : 'http://' ) . sanitize_text_field( $_SERVER['HTTP_HOST'] ) . sanitize_text_field( $_SERVER['REQUEST_URI'] );

			$args = array(
				'protected-page' => 'view',
				'source'		=> urlencode( $current_url ),
			);

			$pwd_protect_login_url = add_query_arg( $args, home_url() );

			nocache_headers();
			wp_safe_redirect( $pwd_protect_login_url );
			exit();

		}

	}

	/**
	 * Maybe process login to access protected page content
	 *
	 * @since 4.1.0
	 */
	public function maybe_process_login() {

		global $password_protected_errors;
		$password_protected_errors = new WP_Error();

		if ( isset( $_REQUEST['protected_page_pwd'] ) ) {

			$password_input = $_REQUEST['protected_page_pwd'];

			$options = get_option( ASENHA_SLUG_U, array() );
			$stored_password = $options['password_protection_password'];

			if ( ! empty( $password_input ) ) {

				if ( $password_input == base64_decode( $stored_password ) ) { // Password is correct

					// Set auth cookie
					// $expiration = time() + DAY_IN_SECONDS; // in 24 hours
					$expiration = 0; // by the end of browsing session
					setcookie( 'asenha_password_protection', 'authenticated', $expiration, COOKIEPATH, COOKIE_DOMAIN, true, true );

					// Redirect
					$redirect_to_url = isset( $_REQUEST['source'] ) ? $_REQUEST['source'] : '';
					wp_safe_redirect( $redirect_to_url );
					exit();

				} else { // Password is incorrect

					// Add error message
					$password_protected_errors->add( 'incorrect_password', 'Incorrect password.' );

				}

			} else { // Password input is empty

				// Add error message
				$password_protected_errors->add( 'empty_password', 'Password can not be empty.' );

			}

		}
	}

	/**
	 * Add custom login error messages
	 *
	 * @since 4.1.0
	 */
	public function add_login_error_messages() {

		global $password_protected_errors;

		if ( $password_protected_errors->get_error_code() ) {

			$messages = '';
			$errors = '';

			// Extract the error message

			foreach( $password_protected_errors->get_error_codes() as $code ) {

				$severity = $password_protected_errors->get_error_data( $code );

				foreach( $password_protected_errors->get_error_messages( $code ) as $error ) {
					if ( 'message' == $severity ) {
						$messages .= $error . '<br />';
					} else {
						$errors .= $error . '<br />';
					}
				}

			}

			// Output the error message

			if ( ! empty( $messages ) ) {
				echo '<p class="message">' . $messages . '</p>';
			}

			if ( ! empty( $errors ) ) {
				echo '<div id="login_error">' . $errors . '</div>';
			}

		}

	}

	/**
	 * Redirect 404 to homepage
	 *
	 * @since 1.7.0
	 */
	public function redirect_404_to_homepage() {

		if ( ! is_404() || is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {

			return;

		} else {

			// wp_safe_redirect( home_url(), 301 );

			header( 'HTTP/1.1 301 Moved Permanently');
			header( 'Location: ' . home_url() );
			exit();

		}

	}

	/**
	 * Send emails using external SMTP service
	 *
	 * @since 4.5.0
	 */
	public function deliver_email_via_smtp( $phpmailer ) {

		$options 					= get_option( ASENHA_SLUG_U, array() );
		$smtp_host					= $options['smtp_host'];
		$smtp_port 					= $options['smtp_port'];
		$smtp_security 				= $options['smtp_security'];
		$smtp_username 				= $options['smtp_username'];
		$smtp_password 				= base64_decode( $options['smtp_password'] );
		$smtp_default_from_name 	= $options['smtp_default_from_name'];
		$smtp_default_from_email 	= $options['smtp_default_from_email'];
		$smtp_debug 					= $options['smtp_debug'];

		// Do nothing if host or password is empty
		if ( empty( $smtp_host ) || empty( $smtp_password ) ) {
			return;
		}

		// Send using SMTP
		$phpmailer->isSMTP(); // phpcs:ignore

		// Enanble SMTP authentication
		$phpmailer->SMTPAuth 	= true; // phpcs:ignore

		// Set some other defaults
		// $phpmailer->CharSet 	= 'utf-8'; // phpcs:ignore
		$phpmailer->XMailer 	= 'Admin and Site Enhancements v' . ASENHA_VERSION . ' - a WordPress plugin'; // phpcs:ignore

		$phpmailer->Host 		= $smtp_host; 		// phpcs:ignore
		$phpmailer->Port 		= $smtp_port; 		// phpcs:ignore
		$phpmailer->SMTPSecure 	= $smtp_security; 	// phpcs:ignore
		$phpmailer->Username 	= $smtp_username; 	// phpcs:ignore
		$phpmailer->Password 	= $smtp_password; 	// phpcs:ignore

		// Maybe override FROM email and/or name if the sender is "WordPress <wordpress@sitedomain.com>", the default from WordPress core and not yet overridden by another plugin.

		$from_name = $phpmailer->FromName;

		if ( ( 'WordPress' === $from_name ) && ! empty( $smtp_default_from_name ) ) {
			$phpmailer->FromName = $smtp_default_from_name;
		}

		$from_email_beginning = substr( $phpmailer->From, 0, 0 ); // Get the first 9 characters of the current FROM email

		if ( ( 'wordpress' === $from_email_beginning ) && ! empty( $smtp_default_from_email ) ) {
			$phpmailer->From = $smtp_default_from_email;
		}

		// If debug mode is enabled, send debug info (SMTP::DEBUG_CONNECTION) to WordPress debug.log file set in wp-config.php
		// Reference: https://github.com/PHPMailer/PHPMailer/wiki/SMTP-Debugging

		if ( $smtp_debug ) {
			$phpmailer->SMTPDebug 	= 3; 	//phpcs:ignore
			$phpmailer->Debugoutput = 'error_log'; 				//phpcs:ignore
		}

	}

}