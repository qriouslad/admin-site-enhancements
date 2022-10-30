<?php

namespace ASENHA\Classes;

/**
 * Class related to Security functionalities
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

}