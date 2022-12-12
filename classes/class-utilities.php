<?php

namespace ASENHA\Classes;

/**
 * Class related to Utilities features
 *
 * @since 1.5.0
 */
class Utilities {

	/**
	 * Redirect to custom internal URL after login for user roles
	 *
	 * @param string $redirect_to_url URL to redirect to. Default is admin dashboard URL.
	 * @param string $origin_url URL the user is coming from.
	 * @param object $user logged-in user's data.
	 * @since 1.5.0
	 */
	public function redirect_for_roles_after_login( $username, $user ) {

		$options = get_option( ASENHA_SLUG_U );
		$redirect_after_login_to_slug = $options['redirect_after_login_to_slug'];
		$redirect_after_login_for = $options['redirect_after_login_for'];

		if ( isset( $redirect_after_login_for ) && ( count( $redirect_after_login_for ) > 0 ) ) {

			// Assemble single-dimensional array of roles for which custom URL redirection should happen
			$roles_for_custom_redirect = array();

			foreach( $redirect_after_login_for as $role_slug => $custom_redirect ) {
				if ( $custom_redirect ) {
					$roles_for_custom_redirect[] = $role_slug;
				}
			}

			// Does the user have roles data in array form?
			if ( isset( $user->roles ) && is_array( $user->roles ) ) {

				$current_user_roles = $user->roles;

			}

			// Set custom redirect URL for roles set in the settings. Otherwise, leave redirect URL to the default, i.e. admin dashboard.
			foreach ( $current_user_roles as $role ) {
				if ( in_array( $role, $roles_for_custom_redirect ) ) {
					
					wp_safe_redirect( home_url( $redirect_after_login_to_slug . '/' ) );
					exit();

				}
			}

		}

	}

	/**
	 * Redirect to custom internal URL after login for user roles
	 *
	 * @param string $redirect_to_url URL to redirect to. Default is admin dashboard URL.
	 * @param string $origin_url URL the user is coming from.
	 * @param object $user logged-in user's data.
	 * @since 1.6.0
	 */
	public function redirect_after_logout( $user_id ) {

		$options = get_option( ASENHA_SLUG_U );
		$redirect_after_logout_to_slug = $options['redirect_after_logout_to_slug'];
		$redirect_after_logout_for = $options['redirect_after_logout_for'];

		$user = get_userdata( $user_id );

		if ( isset( $redirect_after_logout_for ) && ( count( $redirect_after_logout_for ) > 0 ) ) {

			// Assemble single-dimensional array of roles for which custom URL redirection should happen
			$roles_for_custom_redirect = array();

			foreach( $redirect_after_logout_for as $role_slug => $custom_redirect ) {
				if ( $custom_redirect ) {
					$roles_for_custom_redirect[] = $role_slug;
				}
			}

			// Does the user have roles data in array form?
			if ( isset( $user->roles ) && is_array( $user->roles ) ) {

				$current_user_roles = $user->roles;

			}

			// Redirect for roles set in the settings. Otherwise, leave redirect URL to the default, i.e. admin dashboard.
			foreach ( $current_user_roles as $role ) {
				if ( in_array( $role, $roles_for_custom_redirect ) ) {

					wp_safe_redirect( home_url( $redirect_after_logout_to_slug . '/' ) );
					exit();

				}
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
	 * Enqueue custom admin CSS
	 *
	 * @since 2.3.0
	 */
	public function custom_admin_css() {

		$options = get_option( ASENHA_SLUG_U );
		$custom_admin_css = $options['custom_admin_css'];

		?>
		<style type="text/css">
			<?php echo wp_kses_post( $custom_admin_css ); ?>
		</style>
		<?php

	}

	/**
	 * Enqueue custom frontend CSS
	 *
	 * @since 2.3.0
	 */
	public function custom_frontend_css() {

		$options = get_option( ASENHA_SLUG_U );
		$custom_frontend_css = $options['custom_frontend_css'];

		?>
		<style type="text/css">
			<?php echo wp_kses_post( $custom_frontend_css ); ?>
		</style>
		<?php

	}

	/** 
	 * Show content of ads.txt saved to options
	 *
	 * @since 3.2.0
	 */
	public function show_ads_appads_txt_content() {

		$options = get_option( ASENHA_SLUG_U, array() );

		$request = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : false;

		if ( '/ads.txt' === $request ) {

			$ads_txt_content = array_key_exists( 'ads_txt_content', $options ) ? $options['ads_txt_content'] : '';

			header( 'Content-Type: text/plain' );
			echo esc_textarea( $ads_txt_content );
			die();

		}

		if ( '/app-ads.txt' === $request ) {

			$app_ads_txt_content = array_key_exists( 'app_ads_txt_content', $options ) ? $options['app_ads_txt_content'] : '';

			header( 'Content-Type: text/plain' );
			echo esc_textarea( $app_ads_txt_content );
			die();

		}

	}

}