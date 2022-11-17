<?php

namespace ASENHA\Classes;
use WP_Error;

/**
 * Class related to Security features
 *
 * @since 1.4.0
 */
class Security {

	/**
	 * Redirect to valid login URL when custom login slug is part of the request URL
	 *
	 * @link https://plugins.trac.wordpress.org/browser/admin-login-url-change/trunk/admin-login-url-change.php#L134
	 * @since 1.4.0
	 */
	public function redirect_on_custom_login_url() {

		$options = get_option( ASENHA_SLUG_U );
		$custom_login_slug = $options['custom_login_slug'];

		$url_input = sanitize_text_field( $_SERVER['REQUEST_URI'] );

		// Exclude interim login URL, which is inside modal popup when user is logged out in the background
		// URL looks like https://www.example.com/wp-login.php?interim-login=1&wp_lang=en_US
		if ( false !== strpos( $url_input, 'interim-login=1' ) ) {

			remove_action( 'login_head', [ $this, 'redirect_on_default_login_urls' ] );

		}

		// If URL contains the custom login slug, redirect to the login URL with custom login slug in the query parameters
		if ( 
			( false !== strpos( $url_input, '/' . $custom_login_slug ) ) || 
			( false !== strpos( $url_input, '/' . $custom_login_slug . '/' ) ) ) 
		{
			wp_safe_redirect( home_url( 'wp-login.php?' . $custom_login_slug . '&redirect=false' ) );
			exit();

		}

	}

	/**
	 * Redirect to /not_found when login URL does not contain the custom login slug
	 *
	 * @link https://plugins.trac.wordpress.org/browser/admin-login-url-change/trunk/admin-login-url-change.php#L121
	 * @since 1.4.0
	 */
	public function redirect_on_default_login_urls() {

		global $interim_login;

		$options = get_option( ASENHA_SLUG_U );
		$custom_login_slug = $options['custom_login_slug']; // e.g. manage
		$url_input = sanitize_text_field( $_SERVER['REQUEST_URI'] );

		// Custom login slug is not part of the login URL typed into the browser
		// e.g. https://www.example.com/wp-admin/ or https://www.example.com/wp-login.php
		if ( false === strpos( $url_input, $custom_login_slug ) ) {

			if ( 'success' != $interim_login ) {

				wp_safe_redirect( home_url( 'not_found/' ), 302 );
				exit();

			}

		}

	}

	/**
	 * Redirect to custom login URL on failed login
	 *
	 * @link https://plugins.trac.wordpress.org/browser/admin-login-url-change/trunk/admin-login-url-change.php#L148
	 * @since 1.4.0
	 */
	public function redirect_to_custom_login_url_on_login_fail() {

		$options = get_option( ASENHA_SLUG_U );
		$custom_login_slug = $options['custom_login_slug'];

		// Append 'failed_login=true' so we can output custom error message above the login form
		wp_safe_redirect( home_url( 'wp-login.php?' . $custom_login_slug . '&redirect=false&failed_login=true' ) );
		exit();

	}

	/**
	 * Redirect to custom login URL on successful logout
	 *
	 * @link https://plugins.trac.wordpress.org/browser/admin-login-url-change/trunk/admin-login-url-change.php#L148
	 * @since 1.4.0
	 */
	public function redirect_to_custom_login_url_on_logout_success() {

		$options = get_option( ASENHA_SLUG_U );
		$custom_login_slug = $options['custom_login_slug'];

		// Redirect to the login URL with custom login slug in it
		wp_safe_redirect( home_url( 'wp-login.php?' . $custom_login_slug . '&redirect=false' ) );
		exit();

	}

	/**
	 * Make sure user is redirected to dashboard /wp-admin/ when login is successful
	 *
	 * @since 2.5.0
	 */
	public function redirect_to_dashboard( $username, $user ) {

		wp_safe_redirect( get_admin_url() );
		exit();

	}

	/**
	 * Maybe allow login if not locked out. Should return WP_Error object if not allowed to login.
	 *
	 * @since 2.5.0
	 */
	public function maybe_allow_login( $user_or_error, $username, $password ) {

		global $wpdb, $asenha_limit_login;
		$table_name = $wpdb->prefix . 'asenha_failed_logins';

		// Maybe create table if it does not exist yet, e.g. upgraded from previous version of plugin, so, no activation methods are fired
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

		if ( $wpdb->get_var( $query ) === $table_name ) {
			// Table already exists, do nothing.
		} else {
			$activation = new Activation;
			$activation->create_failed_logins_log_table();
		}

        // Get values from options needed to do various checks
		$options = get_option( ASENHA_SLUG_U );
		$login_fails_allowed = $options['login_fails_allowed'];
		$login_lockout_maxcount = $options['login_lockout_maxcount'];
		$change_login_url = $options['change_login_url'];
		$custom_login_slug = $options['custom_login_slug'];

		// Instantiate object to access common methods
		$common_methods = new Common_Methods;

		// Get user/visitor IP address
		$ip_address = $common_methods->get_user_ip_address();

		// Check if IP address has failed login attempts recorded in the DB log
		$sql = $wpdb->prepare("SELECT * FROM `" . $table_name . "` Where `ip_address` = %s", $ip_address);
		$result = $wpdb->get_results( $sql, ARRAY_A );

		$result_count = count( $result );

		if ( $result_count > 0 ) { // IP address has been recorded in the database.

			// Custom Login URL is enabled
			if ( array_key_exists( 'change_login_url', $options ) && $options['change_login_url'] ) {
				$fail_count = $result[0]['fail_count'];
			} else {
				$fail_count = $result[0]['fail_count'] + 1;
			}

			$lockout_count = $result[0]['lockout_count'];
			$last_fail_on = $result[0]['unixtime'];

		} else {

			$fail_count = 0;
			$lockout_count = 0;
			$last_fail_on = '';

		}

		// Initialize the global variable
		$asenha_limit_login = array (
			'ip_address'				=> $ip_address,
			'request_uri'				=> sanitize_text_field( $_SERVER['REQUEST_URI'] ),
			'ip_address_log'			=> $result,
			'maybe_lockout' 			=> false,
			'extended_lockout' 			=> false,
			'within_lockout_period' 	=> false,
			'lockout_period' 			=> 0,
			'lockout_period_remaining' 	=> 0,
			'login_fails_allowed' 		=> $login_fails_allowed,
			'login_lockout_maxcount' 	=> $login_lockout_maxcount,
			// 'default_lockout_period' 	=> 60, // 1 minutes in seconds
			'default_lockout_period' 	=> 60*15, // 15 minutes in seconds
			// 'extended_lockout_period' 	=> 3*60, // 3 minutes in seconds
			'extended_lockout_period' 	=> 24*60*60, // 24 hours in seconds
			'change_login_url'			=> $change_login_url, // is custom login URL enabled?
			'custom_login_slug'			=> $custom_login_slug,
		);

		if ( $result_count > 0 ) { // IP address has been recorded in the database.

			// Failed attempts have been recorded and fulfills lockout condition
			if ( ! empty( $fail_count ) && ( ( $fail_count ) % $login_fails_allowed == 0 ) ) {

				$asenha_limit_login['maybe_lockout'] = true;

				// Has reached max / gone beyond number of lockouts allowed?
				if ( $lockout_count >= $login_lockout_maxcount ) {
					$asenha_limit_login['extended_lockout'] = true;
					$lockout_period = $asenha_limit_login['extended_lockout_period'];
				} else {
					$asenha_limit_login['extended_lockout'] = false;
					$lockout_period = $asenha_limit_login['default_lockout_period'];
				}

				$asenha_limit_login['lockout_period'] = $lockout_period;

				// User/visitor is still within the lockout period
				if ( ( time() - $last_fail_on ) <= $asenha_limit_login['lockout_period'] ) {

					$asenha_limit_login['within_lockout_period'] = true;
					$asenha_limit_login['lockout_period_remaining'] = $asenha_limit_login['lockout_period'] - ( time() - $last_fail_on );

					if ( $asenha_limit_login['lockout_period_remaining'] <= 60 ) {

						// Get remaining lockout period in minutes and seconds
						$lockout_period_remaining = $asenha_limit_login['lockout_period_remaining'] . ' seconds';

					} elseif ( $asenha_limit_login['lockout_period_remaining'] <= 60*60 ) {

						// Get remaining lockout period in minutes and seconds
						$lockout_period_remaining = $common_methods->seconds_to_period( $asenha_limit_login['lockout_period_remaining'], 'to-minutes-seconds' );

					} elseif ( $asenha_limit_login['lockout_period_remaining'] > 60*60 && $asenha_limit_login['lockout_period_remaining'] <= 24*60*60 ) {

						// Get remaining lockout period in minutes and seconds
						$lockout_period_remaining = $common_methods->seconds_to_period( $asenha_limit_login['lockout_period_remaining'], 'to-hours-minutes-seconds' );

					} elseif ( $asenha_limit_login['lockout_period_remaining'] > 24*60*60 ) {

						// Get remaining lockout period in minutes and seconds
						$lockout_period_remaining = $common_methods->seconds_to_period( $asenha_limit_login['lockout_period_remaining'], 'to-days-hours-minutes-seconds' );

					}

					$error = new WP_Error( 'ip_address_blocked', '<b>WARNING:</b> You\'ve been locked out. You can login again in ' . $lockout_period_remaining . '.' );

					// Prevent redirection loop
					remove_action( 'wp_login_failed', [ $this, 'redirect_to_custom_login_url_on_login_fail' ] );

					return $error;

				} else { // User/visitor is no longer within the lockout period

					$asenha_limit_login['within_lockout_period'] = false;

					if ( $lockout_count == $login_lockout_maxcount ) {

						// Remove the DB log entry for the current IP address. i.e. release from extended lockout

						$where = array( 'ip_address' => $ip_address );
						$where_format = array( '%s' );

						// Delete existing data in the database
						$wpdb->delete(
							$table_name,
							$where,
							$where_format
						);

					}

					return $user_or_error;

				}

			} else {

				$asenha_limit_login['maybe_lockout'] = false;

				return $user_or_error;

			}

		} else { // IP address has not been recorded in the database.

			return $user_or_error;

		}

	}

	/**
	 * Disable login form inputs via javascript
	 * 
	 * @since 2.5.0
	 */
	public function maybe_hide_login_form() {

		global $asenha_limit_login;

		if ( $asenha_limit_login['within_lockout_period'] ) {

			// Hide logo, login form and the links below it
			?>
			<style type="text/css">

				body.login {
					background:#f6d6d7;
				}

				#login h1,
				#loginform,
				#login #nav,
				#backtoblog { 
					display: none; 
				}

				@media screen and (max-height: 550px) {

					#login {
						padding: 80px 0 20px !important;
					}

				}

			</style>
			<?php
		}

	}

	/**
	 * Log failed login attempts
	 *
	 * @since 2.5.0
	 */
	public function log_failed_login( $username ) {

		global $wpdb, $asenha_limit_login;
		$table_name = $wpdb->prefix . 'asenha_failed_logins';

		// Check if the IP address has been used in a failed login attempt before, i.e. has it been recorded in the database?
		$sql = $wpdb->prepare( "SELECT * FROM `" . $table_name . "` WHERE `ip_address` = %s", $asenha_limit_login['ip_address'] );
		$result = $wpdb->get_results( $sql, ARRAY_A );
		$result_count = count( $result );

		// Update logged info for the IP address in the global variable
		$asenha_limit_login['ip_address_log'] = $result;

		if ( $result_count == 0 ) { // IP address has not been recorded in the database.

			$new_fail_count = 1;
			$new_lockout_count = 0;

		} else { // IP address has been recorded in the database.

			$new_fail_count = $result[0]['fail_count'] + 1;
			$new_lockout_count = floor( ( $result[0]['fail_count'] + 1 ) / $asenha_limit_login['login_fails_allowed'] );

		}

		// Get the URL where login failed, i.e. where brute force attack might be happening
		// $login_url = ( ! empty( $_SERVER['HTTPS'] ) ? 'https://' : 'http://') . sanitize_text_field( $_SERVER['HTTP_HOST'] ) . sanitize_text_field( $_SERVER['REQUEST_URI'] );

		// Time stamps
		$unixtime = time();
		if ( function_exists( 'wp_date' ) ) {
			$datetime_wp = wp_date( 'Y-m-d H:i:s', $unixtime );
		} else {
			$datetime_wp = date_i18n( 'Y-m-d H:i:s', $unixtime );
		}

		$data = array(
			'ip_address'	=> $asenha_limit_login['ip_address'],
			'username'		=> $username,
			'fail_count'	=> $new_fail_count,
			'lockout_count'	=> $new_lockout_count,
			'request_uri'	=> $asenha_limit_login['request_uri'],
			'unixtime'		=> $unixtime,
			'datetime_wp'	=> $datetime_wp,
			'info'			=> '',
		);

		$data_format = array(
			'%s', // string
			'%s', // string
			'%d', // integer
			'%d', // integer
			'%s', // string
			'%d', // integer
			'%s', // string
			'%s', // string
		);

		if ( $result_count == 0 ) {

			// Insert into the database
			$result = $wpdb->insert(
				$table_name,
				$data,
				$data_format
			);

		} else {

			// $options = get_option( ASENHA_SLUG_U );
			// $login_fails_allowed = $options['login_fails_allowed'];

			$fail_count = $result[0]['fail_count'];
			$lockout_count = $result[0]['lockout_count'];
			$last_fail_on = $result[0]['unixtime'];

			$where = array( 'ip_address' => $asenha_limit_login['ip_address'] );
			$where_format = array( '%s' );

			// Failed attempts have been recorded and fulfills lockout condition
			if ( ! empty( $fail_count ) && ( $fail_count % $asenha_limit_login['login_fails_allowed'] == 0 ) ) {

				// Has reached max / gone beyond number of lockouts allowed?
				if ( $lockout_count >= $asenha_limit_login['login_lockout_maxcount'] ) {
					$asenha_limit_login['extended_lockout'] = true;
					$lockout_period = $asenha_limit_login['extended_lockout_period'];
				} else {
					$asenha_limit_login['extended_lockout'] = false;
					$lockout_period = $asenha_limit_login['default_lockout_period'];
				}

				$asenha_limit_login['lockout_period'] = $lockout_period;

				// User/visitor is still within the lockout period
				if ( ( time() - $last_fail_on ) <= $asenha_limit_login['lockout_period'] ) {

					// Do nothing

				} else {

					if ( $lockout_count < $asenha_limit_login['login_lockout_maxcount'] ) {

						// Update existing data in the database
						$wpdb->update(
							$table_name,
							$data,
							$where,
							$data_format,
							$where_format
						);

					}

				}

			} else {

				// Update existing data in the database
				$wpdb->update(
					$table_name,
					$data,
					$where,
					$data_format,
					$where_format
				);

			}

		}

	}

	/**
	 * Handle login errors
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_error/#methods
	 * @since 2.5.0
	 */
	public function login_error_handler( $errors, $redirect_to ) {

		global $asenha_limit_login;

		if ( is_wp_error( $errors ) ) {

			$error_codes = $errors->get_error_codes();

			foreach ( $error_codes as $error_code ) {

				if ( $error_code == 'invalid_username' || $error_code == 'incorrect_password' ) {

					// Remove default error messages that may give out valueable info to hackers

					$errors->remove( 'invalid_username' ); // Outputs info that says username does not exist. May encourage login attempt with a different username instead.

					$errors->remove( 'incorrect_password' ); // Outputs info that implies username exist. May encourage login attempt with a different password.

					// Add a new error message that does not provide useful clues to hackers
					$errors->add( 'invalid_username_or_incorrect_password', '<b>Error:</b> Invalid username or incorrect password.' );

					// $errors->add( 'another_error_code', 'The error message.' );

				}

			}

		}

		return $errors;

	}

	/**
	 * Add login error message on top of the login form
	 *
	 * @since 2.5.0
	 */
	public function add_failed_login_message( $message ) {

		global $asenha_limit_login;

		if ( isset( $_REQUEST['failed_login'] ) && $_REQUEST['failed_login'] == 'true' ) {

			if ( ! $asenha_limit_login['within_lockout_period'] ) {

				$message = '<div id="login_error"><b>Error:</b> Invalid username or incorrect password.</div>';

			}

		}

		return $message;

	}

	/** 
	 * Clear failed login attempts log after successful login
	 *
	 * @since 2.5.0
	 */
	public function clear_failed_login_log() {

		global $wpdb, $asenha_limit_login;
		$table_name = $wpdb->prefix . 'asenha_failed_logins';

		// Remove the DB log entry for the current IP address.

		$where = array( 'ip_address' => $asenha_limit_login['ip_address'] );
		$where_format = array( '%s' );

		$wpdb->delete(
			$table_name,
			$where,
			$where_format
		);

	}

	/**
	 * If an author name is queried, decrypt it. Used by pre_get_posts action.
	 * 
	 * @link https://plugins.trac.wordpress.org/browser/smart-user-slug-hider/tags/4.0.2/inc/class-smart-user-slug-hider.php
	 * @since 2.1.0
	 */
	function alter_author_query( $query ) {

		// Check if it's a query for author data, and that 'author_name' is not empty
		if ( $query->is_author() && $query->query_vars['author_name'] != '' ) {

			// Check for character(s) representing a hexadecimal digit
			if ( ctype_xdigit( $query->query_vars['author_name'] ) ) {

			// Get user by the decrypted user ID
			$user = get_user_by( 'id', $this->decrypt( $query->query_vars['author_name'] ) );

				if ( $user ) {

					$query->set( 'author_name', $user->user_nicename );

				} else {

					// No user found
					$query->is_404 = true;
					$query->is_author = false;
					$query->is_archive = false;

				}

			} else {

				// No hexadecimal digit detected in URL, i.e. someone is trying to access URL with original author slug
				$query->is_404 = true;
				$query->is_author = false;
				$query->is_archive = false;

			}

		}
		
		return;
	}

	/**
	 * Replace author slug in author link to encrypted value. Used by author_link filter.
	 * 
	 * @link https://plugins.trac.wordpress.org/browser/smart-user-slug-hider/tags/4.0.2/inc/class-smart-user-slug-hider.php
	 * @since 2.1.0
	 */
	function alter_author_link( $link, $user_id, $author_slug ) {

		$encrypted_author_slug = $this->encrypt( $user_id );

		return str_replace ( '/' . $author_slug, '/' . $encrypted_author_slug, $link );

	}

	/**
	 * Replace author slug in REST API /users/ endpoint to encrypted value. Used by rest_prepare_user filter.
	 *
	 * @link https://plugins.trac.wordpress.org/browser/smart-user-slug-hider/tags/4.0.2/inc/class-smart-user-slug-hider.php
	 * @since 2.1.0
	 */
	function alter_json_users($response, $user, $request) {

		$data = $response->get_data();
        $data['slug'] = $this->encrypt($data['id']);
        $response->set_data($data);

		return $response;

	}

	/**
	 * Helper function to return an encrypted user ID, which will then be used to replace the author slug.
	 * 
	 * @link https://plugins.trac.wordpress.org/browser/smart-user-slug-hider/trunk/inc/class-smart-user-slug-hider.php
	 * @since 2.1.0
	 */
	private function encrypt( $user_id ) {

		// Returns encrypted encrypted author slug from user ID, e.g. encrypt user ID 3 to author slug 4e3062d8c8626a14
		return bin2hex( openssl_encrypt( base_convert( $user_id, 10, 36 ), 'DES-EDE3', md5( sanitize_text_field( $_SERVER['SERVER_ADDR'] ) . ASENHA_URL ), OPENSSL_RAW_DATA ) );

	}

	
	/**
	 * Helper function to decrypt an (encrypted) author slug and returns the user ID
	 * 
	 * @link https://plugins.trac.wordpress.org/browser/smart-user-slug-hider/trunk/inc/class-smart-user-slug-hider.php
	 * @since 2.1.0
	 */
	private function decrypt( $encrypted_author_slug ) {

		// Returns user ID, e.g. decrypts author slug 4e3062d8c8626a14 into user ID 3
		return base_convert( openssl_decrypt( pack('H*', $encrypted_author_slug), 'DES-EDE3', md5( sanitize_text_field( $_SERVER['SERVER_ADDR'] ) . ASENHA_URL ), OPENSSL_RAW_DATA ), 36, 10 );

	}

}