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
	 * replace user slug in wp-json users to encrypted value
     * used by rest_prepare_user filter
	 */
	function alter_json_users($response, $user, $request) {
		$data = $response->get_data();
        $data['slug'] = $this->encrypt($data['id']);
        $response->set_data($data);

		return $response;
	}

	/**
	 * replace author name in author link to encrypted value
     * used by author_link filter
	 * 
	 * @link https://plugins.trac.wordpress.org/browser/smart-user-slug-hider/trunk/inc/class-smart-user-slug-hider.php
	 */
	function alter_author_link( $link, $author_id, $author_nicename ) {
		return str_replace ( '/' . $author_nicename, '/' . $this->encrypt( $author_id ), $link );
	}

	/**
	 * if a author name is queried we have to decrypt it
     * used by pre_get_posts action
	 * 
	 * @link https://plugins.trac.wordpress.org/browser/smart-user-slug-hider/trunk/inc/class-smart-user-slug-hider.php
	 */
	function alter_author_query( $query ) {
		if ( $query->is_author() && $query->query_vars['author_name'] != '' ) {
		  if ( ctype_xdigit( $query->query_vars['author_name'] ) ) {
			$user = get_user_by( 'id', $this->decrypt( $query->query_vars['author_name'] ) );
			if ( $user ) {
			  $query->set( 'author_name', $user->user_nicename );
			} else {
			  $query->is_404 = true;
			  $query->is_author = false;
			  $query->is_archive = false;
			}
		  } else {
			$query->is_404 = true;
			$query->is_author = false;
			$query->is_archive = false;
		  }
		}
		
		return;
	}

	/**
	 * helper function to encrypt author name
	 * 
	 * @link https://plugins.trac.wordpress.org/browser/smart-user-slug-hider/trunk/inc/class-smart-user-slug-hider.php
	 */
	private function encrypt( $id ) {
      return bin2hex( openssl_encrypt( base_convert( $id, 10, 36 ), 'DES-EDE3', md5( $_SERVER['SERVER_ADDR'] . ASENHA_URL ), OPENSSL_RAW_DATA ) );
	}

	
	/**
	 * helper function to decrypt author name
	 * 
	 * @link https://plugins.trac.wordpress.org/browser/smart-user-slug-hider/trunk/inc/class-smart-user-slug-hider.php
	 */
	private function decrypt( $encid ) {
      return base_convert( openssl_decrypt( pack('H*', $encid), 'DES-EDE3', md5( $_SERVER['SERVER_ADDR'] . ASENHA_URL ), OPENSSL_RAW_DATA ), 36, 10 );
	}

}