<?php

namespace ASENHA\Classes;

/**
 * Class related to Security features
 *
 * @since 1.4.0
 */
class Security {

	/**
	 * Redirect to /not_found when login URL does not contain the custom login slug
	 *
	 * @link https://plugins.trac.wordpress.org/browser/admin-login-url-change/trunk/admin-login-url-change.php#L121
	 * @since 1.4.0
	 */
	public function redirect_on_default_login_urls() {

		$options = get_option( ASENHA_SLUG_U );
		$custom_login_slug = $options['custom_login_slug'];
		$url_input = sanitize_text_field( $_SERVER['REQUEST_URI'] );

		// Custom login slug is not part of the login URL typed into the browser
		// e.g. https://www.example.com/wp-admin/ or https://www.example.com/wp-login.php
		if ( false === strpos( $url_input, $custom_login_slug ) ) {

			wp_safe_redirect( home_url( 'not_found/' ), 302 );
			exit();

		}

	}

	/**
	 * Redirect to valid login URL when custom login slug is part of the request URL
	 *
	 * @link https://plugins.trac.wordpress.org/browser/admin-login-url-change/trunk/admin-login-url-change.php#L134
	 * @since 1.4.0
	 */
	public function redirect_on_custom_login_url() {

		$options = get_option( ASENHA_SLUG_U );
		$custom_login_slug = $options['custom_login_slug'];
		$url_input = parse_url( sanitize_text_field( $_SERVER['REQUEST_URI'] ) ); // an array

		if ( ( $url_input['path'] == '/' . $custom_login_slug ) || ( $url_input['path'] == '/' . $custom_login_slug . '/' ) ) {

			wp_safe_redirect( home_url( 'wp-login.php?' . $custom_login_slug . '&redirect=false' ) );
			exit();

		}

	}

	/**
	 * Redirect on successful logout
	 *
	 * @link https://plugins.trac.wordpress.org/browser/admin-login-url-change/trunk/admin-login-url-change.php#L148
	 * @since 1.4.0
	 */
	public function redirect_to_custom_login_url() {

		$options = get_option( ASENHA_SLUG_U );
		$custom_login_slug = $options['custom_login_slug'];

		wp_safe_redirect( home_url( 'wp-login.php?' . $custom_login_slug . '&redirect=false' ) );
		exit();

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