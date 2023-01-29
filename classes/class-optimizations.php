<?php

namespace ASENHA\Classes;

/**
 * Class related to Optimizations features
 *
 * @since 3.8.0
 */
class Optimizations {

	private $current_url_path;

	/**
	 * Limit the number of revisions for post types
	 *
	 * @since 3.7.0
	 */
	public function limit_revisions_to_max_number( $num, $post ) {

		$options = get_option( ASENHA_SLUG_U, array() );
		$revisions_max_number = $options['revisions_max_number'];
		$for_post_types = $options['enable_revisions_control_for'];

		// Assemble single-dimensional array of post type slugs for which revisinos is being limited
		$limited_post_types = array();
		foreach( $for_post_types as $post_type_slug => $post_type_is_limited ) {
			if ( $post_type_is_limited ) {
				$limited_post_types[] = $post_type_slug;
			}
		}

		// Change revisions number to keep if set for the post type as such
		$post_type = $post->post_type;
		if ( in_array( $post_type, $limited_post_types ) ) {
			$num = $revisions_max_number;
		}

		return $num;

	}

	/**
	 * Maybe modify heartbeat tick frequency based on settings for each location
	 *
	 * @since 3.8.0
	 */
	public function maybe_modify_heartbeat_frequency( $settings ) {

		$this->get_url_path(); // defines $current_url_path

		$options = get_option( ASENHA_SLUG_U, array() );

		// Disable heartbeat autostart
		$settings['autostart'] = false;

		if ( is_admin() ) {

			if ( '/wp-admin/post.php' == $this->current_url_path || '/wp-admin/post-new.php' == $this->current_url_path ) {

				// Maybe modify interval on post edit screens
				if ( 'modify' == $options['heartbeat_control_for_post_edit'] ) {
					$settings['minimalInterval'] = absint( $options['heartbeat_interval_for_post_edit'] );
				}

			} else {

				// Maybe modify interval on admin pages
				if ( 'modify' == $options['heartbeat_control_for_admin_pages'] ) {
					$settings['minimalInterval'] = absint( $options['heartbeat_interval_for_admin_pages'] );
				}

			}

		} else {

			// Maybe modify interval on the frontend
			if ( 'modify' == $options['heartbeat_control_for_frontend'] ) {
				$settings['minimalInterval'] = absint( $options['heartbeat_interval_for_frontend'] );
			}

		}

		return $settings;

	}

	/**
	 * Maybe disable heartbeat ticks based on settings for each location
	 *
	 * @since 3.8.0
	 */
	public function maybe_disable_heartbeat() {

		global $pagenow;

		$options = get_option( ASENHA_SLUG_U, array() );

		if ( is_admin() ) {

			if ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) {

				// Maybe disable on post creation / edit screens
				if ( 'disable' == $options['heartbeat_control_for_post_edit'] ) {
					wp_deregister_script( 'heartbeat' );
					return;
				}

			} else {

				// Maybe disable on the rest of admin pages
				if ( 'disable' == $options['heartbeat_control_for_admin_pages'] ) {
					wp_deregister_script( 'heartbeat' );
					return;
				}

			}

		} else {

			// Maybe disable on the frontend
			if ( 'disable' == $options['heartbeat_control_for_frontend'] ) {
				wp_deregister_script( 'heartbeat' );
				return;
			}

		}

	}

	/**
	 * Set current location
	 * Supported locations [editor,dashboard,frontend]
	 */
	public function get_url_path() {

		$url = ( isset( $_SERVER['HTTPS'] ) ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$request_path = parse_url( $url, PHP_URL_PATH ); // e.g. '/wp-admin/post.php'
		$this->current_url_path = $request_path;

	}


}