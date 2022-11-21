<?php

namespace ASENHA\Classes;

/**
 * Class that provides common methods used throughout the plugin
 *
 * @since 2.5.0
 */
class Common_Methods {

	/**
	 * Get IP of the current visitor/user. In use by at least the Limit Login Attempts feature.
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
	 * Convert number of seconds into hours, minutes, seconds. In use by at least the Limit Login Attempts feature.
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