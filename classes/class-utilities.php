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

}