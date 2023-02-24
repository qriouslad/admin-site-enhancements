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
	 * Show Password Protection admin bar status icon
	 *
	 * @since 4.1.0
	 */
	public function show_password_protection_admin_bar_icon() {
		add_action( 'wp_before_admin_bar_render', [ $this, 'add_password_protection_admin_bar_item' ] );
		add_action( 'admin_head', [ $this, 'add_password_protection_admin_bar_item_styles' ] );
		add_action( 'wp_head', [ $this, 'add_password_protection_admin_bar_item_styles' ] );
	}

	/**
	 * Add WP Admin Bar item
	 *
	 * @since 4.1.0
	 */
	public function add_password_protection_admin_bar_item() {
		global $wp_admin_bar;

		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {
				$wp_admin_bar->add_menu( array(
					'id'	=> 'password_protection',
					'title'	=> '',
					'href'	=> admin_url( 'tools.php?page=admin-site-enhancements#utilities' ),
					'meta'	=> array(
						'title'	=> 'Password protection is currently enabled for this site.',
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
	public function add_password_protection_admin_bar_item_styles() {

		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {

				?>
				<style>
					#wp-admin-bar-password_protection { 
						background-color: #c32121 !important;
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
						background-color: #af1d1d !important; 
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
	 * @since 4.6.0
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

	/**
	 * Redirect for when maintenance mode is enabled
	 *
	 * @since 4.7.0
	 */
	public function maintenance_mode_redirect() {

		$current_url = sanitize_text_field( $_SERVER['REQUEST_URI'] );
		$current_url_parts = explode( '/', $current_url );
		
		// Bypass wp-admin pages and logged-in administrator on the frontend
		if ( ! in_array( 'wp-admin', $current_url_parts ) || ( false !== strpos( 'wp-login.php', $current_url ) ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {

				header( 'HTTP/1.1 503 Service Unavailable', true, 503 );
				header( 'Status: 503 Service Unavailable' );
				header( 'Retry-After: 3600' ); // Tell search engine bots to return after 3600 seconds, i.e. 1 hour

				$options 			= get_option( ASENHA_SLUG_U, array() );
				$heading			= $options['maintenance_page_heading'];
				$description		= $options['maintenance_page_description'];
				$background			= $options['maintenance_page_background'];

				if ( 'lines' === $background ) { // https://bgjar.com/curve-line
					$background_image = "url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' version='1.1' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns:svgjs='http://svgjs.com/svgjs' width='1920' height='1280' preserveAspectRatio='none' viewBox='0 0 1920 1280'%3e%3cg mask='url(%26quot%3b%23SvgjsMask1804%26quot%3b)' fill='none'%3e%3crect width='1920' height='1280' x='0' y='0' fill='url(%23SvgjsLinearGradient1805)'%3e%3c/rect%3e%3cpath d='M2294.46 927.36C2128.65 934.22 2078.52 1270.56 1693.36 1208.96 1308.19 1147.36 1373.24 145.96 1092.25-67.11' stroke='rgba(158%2c 160%2c 161%2c 0.57)' stroke-width='2'%3e%3c/path%3e%3cpath d='M2225.25 303.97C1963.34 332.56 1808.36 909.76 1359.97 905.57 911.59 901.38 820.47-55.06 494.7-167.42' stroke='rgba(158%2c 160%2c 161%2c 0.57)' stroke-width='2'%3e%3c/path%3e%3cpath d='M2247.58 281.19C2070.08 293.95 1967.68 651 1632.53 639.59 1297.39 628.18 1265.17-143.39 1017.49-253.69' stroke='rgba(158%2c 160%2c 161%2c 0.57)' stroke-width='2'%3e%3c/path%3e%3cpath d='M1924.29 917.21C1696.21 904.78 1584.63 530.74 1114.13 494.81 643.63 458.88 546.92-26.2 303.97-50.85' stroke='rgba(158%2c 160%2c 161%2c 0.57)' stroke-width='2'%3e%3c/path%3e%3cpath d='M2009.59 400.31C1847.79 399.06 1696.02 240.31 1382.45 240.31 1068.87 240.31 1083.3 404.62 755.3 400.31 427.31 396 332.72-108.61 128.16-144.89' stroke='rgba(158%2c 160%2c 161%2c 0.57)' stroke-width='2'%3e%3c/path%3e%3c/g%3e%3cdefs%3e%3cmask id='SvgjsMask1804'%3e%3crect width='1920' height='1280' fill='white'%3e%3c/rect%3e%3c/mask%3e%3clinearGradient x1='8.33%25' y1='-12.5%25' x2='91.67%25' y2='112.5%25' gradientUnits='userSpaceOnUse' id='SvgjsLinearGradient1805'%3e%3cstop stop-color='rgba(255%2c 255%2c 255%2c 1)' offset='0'%3e%3c/stop%3e%3cstop stop-color='rgba(193%2c 192%2c 192%2c 1)' offset='1'%3e%3c/stop%3e%3c/linearGradient%3e%3c/defs%3e%3c/svg%3e\")";
				} elseif ( 'stripes' === $background ) { // https://bgjar.com/shiny-overlay
					$background_image = "url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' version='1.1' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns:svgjs='http://svgjs.com/svgjs' width='2560' height='2560' preserveAspectRatio='none' viewBox='0 0 2560 2560'%3e%3cg mask='url(%26quot%3b%23SvgjsMask1276%26quot%3b)' fill='none'%3e%3crect width='2560' height='2560' x='0' y='0' fill='url(%23SvgjsLinearGradient1277)'%3e%3c/rect%3e%3cpath d='M0 0L524.59 0L0 986.23z' fill='rgba(255%2c 255%2c 255%2c .1)'%3e%3c/path%3e%3cpath d='M0 986.23L524.59 0L684.6500000000001 0L0 1251.4z' fill='rgba(255%2c 255%2c 255%2c .075)'%3e%3c/path%3e%3cpath d='M0 1251.4L684.6500000000001 0L1140.02 0L0 1816.94z' fill='rgba(255%2c 255%2c 255%2c .05)'%3e%3c/path%3e%3cpath d='M0 1816.94L1140.02 0L1666.1399999999999 0L0 1973.71z' fill='rgba(255%2c 255%2c 255%2c .025)'%3e%3c/path%3e%3cpath d='M2560 2560L1477.86 2560L2560 2129.39z' fill='rgba(0%2c 0%2c 0%2c .1)'%3e%3c/path%3e%3cpath d='M2560 2129.39L1477.86 2560L669.0099999999999 2560L2560 1244.5099999999998z' fill='rgba(0%2c 0%2c 0%2c .075)'%3e%3c/path%3e%3cpath d='M2560 1244.51L669.0099999999998 2560L531.5999999999998 2560L2560 928.88z' fill='rgba(0%2c 0%2c 0%2c .05)'%3e%3c/path%3e%3cpath d='M2560 928.8800000000001L531.5999999999997 2560L354.62999999999965 2560L2560 697.8700000000001z' fill='rgba(0%2c 0%2c 0%2c .025)'%3e%3c/path%3e%3c/g%3e%3cdefs%3e%3cmask id='SvgjsMask1276'%3e%3crect width='2560' height='2560' fill='white'%3e%3c/rect%3e%3c/mask%3e%3clinearGradient x1='0%25' y1='0%25' x2='100%25' y2='100%25' gradientUnits='userSpaceOnUse' id='SvgjsLinearGradient1277'%3e%3cstop stop-color='rgba(255%2c 255%2c 255%2c 1)' offset='0'%3e%3c/stop%3e%3cstop stop-color='rgba(172%2c 172%2c 172%2c 1)' offset='1'%3e%3c/stop%3e%3c/linearGradient%3e%3c/defs%3e%3c/svg%3e\")";
				} elseif ( 'curves' === $background ) { // https://www.svgbackgrounds.com/
					$background_image = "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 1600 800'%3E%3Cg %3E%3Cpath fill='%23e0e0e0' d='M486 705.8c-109.3-21.8-223.4-32.2-335.3-19.4C99.5 692.1 49 703 0 719.8V800h843.8c-115.9-33.2-230.8-68.1-347.6-92.2C492.8 707.1 489.4 706.5 486 705.8z'/%3E%3Cpath fill='%23e2e2e2' d='M1600 0H0v719.8c49-16.8 99.5-27.8 150.7-33.5c111.9-12.7 226-2.4 335.3 19.4c3.4 0.7 6.8 1.4 10.2 2c116.8 24 231.7 59 347.6 92.2H1600V0z'/%3E%3Cpath fill='%23e5e5e5' d='M478.4 581c3.2 0.8 6.4 1.7 9.5 2.5c196.2 52.5 388.7 133.5 593.5 176.6c174.2 36.6 349.5 29.2 518.6-10.2V0H0v574.9c52.3-17.6 106.5-27.7 161.1-30.9C268.4 537.4 375.7 554.2 478.4 581z'/%3E%3Cpath fill='%23e7e7e7' d='M0 0v429.4c55.6-18.4 113.5-27.3 171.4-27.7c102.8-0.8 203.2 22.7 299.3 54.5c3 1 5.9 2 8.9 3c183.6 62 365.7 146.1 562.4 192.1c186.7 43.7 376.3 34.4 557.9-12.6V0H0z'/%3E%3Cpath fill='%23EAEAEA' d='M181.8 259.4c98.2 6 191.9 35.2 281.3 72.1c2.8 1.1 5.5 2.3 8.3 3.4c171 71.6 342.7 158.5 531.3 207.7c198.8 51.8 403.4 40.8 597.3-14.8V0H0v283.2C59 263.6 120.6 255.7 181.8 259.4z'/%3E%3Cpath fill='%23ededed' d='M1600 0H0v136.3c62.3-20.9 127.7-27.5 192.2-19.2c93.6 12.1 180.5 47.7 263.3 89.6c2.6 1.3 5.1 2.6 7.7 3.9c158.4 81.1 319.7 170.9 500.3 223.2c210.5 61 430.8 49 636.6-16.6V0z'/%3E%3Cpath fill='%23f0f0f0' d='M454.9 86.3C600.7 177 751.6 269.3 924.1 325c208.6 67.4 431.3 60.8 637.9-5.3c12.8-4.1 25.4-8.4 38.1-12.9V0H288.1c56 21.3 108.7 50.6 159.7 82C450.2 83.4 452.5 84.9 454.9 86.3z'/%3E%3Cpath fill='%23f2f2f2' d='M1600 0H498c118.1 85.8 243.5 164.5 386.8 216.2c191.8 69.2 400 74.7 595 21.1c40.8-11.2 81.1-25.2 120.3-41.7V0z'/%3E%3Cpath fill='%23f5f5f5' d='M1397.5 154.8c47.2-10.6 93.6-25.3 138.6-43.8c21.7-8.9 43-18.8 63.9-29.5V0H643.4c62.9 41.7 129.7 78.2 202.1 107.4C1020.4 178.1 1214.2 196.1 1397.5 154.8z'/%3E%3Cpath fill='%23F8F8F8' d='M1315.3 72.4c75.3-12.6 148.9-37.1 216.8-72.4h-723C966.8 71 1144.7 101 1315.3 72.4z'/%3E%3C/g%3E%3C/svg%3E\")";
				} else {}

				?>
				<html>
					<head>
						<title>Under maintenance</title>
						<link rel="stylesheet" id="asenha-maintenance" href="<?php echo ASENHA_URL . 'assets/css/maintenance.css' ?>" media="all">
						<meta name="viewport" content="width=device-width">
						<style>
							body {
								background-image: <?php echo $background_image; ?>;								
							}
						</style>
					</head>
					<body>
						<div class="page-wrapper">
							<div class="message-box">
								<h1><?php echo $heading; ?></h1>
								<div class="description"><?php echo $description; ?></div>
							</div>
						</div>
					</body>
				</html>
				<?php
				exit();

			}
		}

	}

	/**
	 * Show Password Protection admin bar status icon
	 *
	 * @since 4.1.0
	 */
	public function show_maintenance_mode_admin_bar_icon() {
		add_action( 'wp_before_admin_bar_render', [ $this, 'add_maintenance_mode_admin_bar_item' ] );
		add_action( 'admin_head', [ $this, 'add_maintenance_mode_admin_bar_item_styles' ] );
		add_action( 'wp_head', [ $this, 'add_maintenance_mode_admin_bar_item_styles' ] );
	}

	/**
	 * Add WP Admin Bar item
	 *
	 * @since 4.1.0
	 */
	public function add_maintenance_mode_admin_bar_item() {
		global $wp_admin_bar;

		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {
				$wp_admin_bar->add_menu( array(
					'id'	=> 'maintenance_mode',
					'title'	=> '',
					'href'	=> admin_url( 'tools.php?page=admin-site-enhancements#utilities' ),
					'meta'	=> array(
						'title'	=> 'Maintenance mode is currently enabled for this site.',
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
	public function add_maintenance_mode_admin_bar_item_styles() {

		if ( is_user_logged_in() ) {
			if ( current_user_can( 'manage_options' ) ) {

				?>
				<style>
					#wp-admin-bar-maintenance_mode { 
						background-color: #ff800c !important;
						transition: .25s;
					}
					#wp-admin-bar-maintenance_mode > .ab-item { 
						color: #fff !important;  
					}
					#wp-admin-bar-maintenance_mode > .ab-item:before { 
						content: "\f308"; 
						top: 2px; 
						color: #fff !important; 
						margin-right: 0px; 
					}
					#wp-admin-bar-maintenance_mode:hover > .ab-item { 
						background-color: #e5730a !important; 
						color: #fff; 
					}
				</style>
				<?php

			}
		}

	}

}