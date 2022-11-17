<?php

namespace ASENHA\Classes;

/**
 * Class that provides common methods used throughout the plugin
 *
 * @since 2.5.0
 */
class Common_Methods {

	/**
	 * Re-enqueue CodeMirror syntax highlighter from WordPress core
	 *
	 * @since 2.3.0
	 */
	public function enqueue_codemirror_assets() {

		// Only enqueue scripts on the plugin's settings page
		if ( is_asenha() ) {

			// CodeMirror JS
			wp_enqueue_script( 'asenha-codemirror', ASENHA_URL . 'assets/js/codemirror/codemirror.min.js', array(), ASENHA_VERSION, true );			

			// CodeMirror CSS
			wp_enqueue_style( 'asenha-codemirror', ASENHA_URL . 'assets/css/codemirror/codemirror.min.css', array(), ASENHA_VERSION );

			// CSS mode for CodeMirror
			wp_enqueue_script( 'asenha-codemirror-css-mode', ASENHA_URL . 'assets/js/codemirror/css.js', array( 'asenha-codemirror' ), ASENHA_VERSION, true );			

		}

	}

	/**
	 * Re-enqueue CodeMirror syntax highlighter from WordPress core
	 *
	 * @since 2.3.0
	 */
	public function enqueue_datatables_assets() {

		// Only enqueue scripts on the plugin's settings page
		if ( is_asenha() ) {

			wp_enqueue_style( 'asenha-datatables', ASENHA_URL . 'assets/css/datatables/datatables.min.css', array(), ASENHA_VERSION );
			wp_enqueue_script( 'asenha-datatables', ASENHA_URL . 'assets/js/datatables/datatables.min.js', array( 'jquery' ), ASENHA_VERSION, false );

		}

	}
	/**
	 * Get IP of the current visitor/user
	 *
	 * @since 2.5.0
	 */
	public function get_user_ip_address() {

		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {

			$ip_address = sanitize_text_field( $_SERVER['REMOTE_ADDR'] );

		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

			$ip_address = sanitize_text_field( $_SERVER['HTTP_X_FORWARDED_FOR'] );

		} elseif ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {

			$ip_address = sanitize_text_field( $_SERVER['HTTP_CLIENT_IP'] );

		} else {

			$ip_address = '0.0.0.0';

		}

		return $ip_address;

	}

	/**
	 * Convert number of seconds into hours, minutes, seconds
	 *
	 * @since 2.5.0
	 */
	public function seconds_to_period( $seconds, $conversion_type ) {

	    $period_start = new \DateTime('@0');
	    $period_end = new \DateTime("@$seconds");

	    if ( $conversion_type == 'to-days-hours-minutes-seconds' ) {

		    return $period_start->diff($period_end)->format('%a days, %h hours, %i minutes and %s seconds');

	    } elseif ( $conversion_type == 'to-hours-minutes-seconds' ) {

		    return $period_start->diff($period_end)->format('%h hours, %i minutes and %s seconds');

	    } elseif ( $conversion_type == 'to-minutes-seconds' ) {

		    return $period_start->diff($period_end)->format('%i minutes and %s seconds');

	    } else {

		    return $period_start->diff($period_end)->format('%a days, %h hours, %i minutes and %s seconds');

	    }

	}
}