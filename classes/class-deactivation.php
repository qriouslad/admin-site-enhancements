<?php

namespace ASENHA\Classes;

/**
 * Plugin Deactivation
 *
 * @since 1.0.0
 */
class Deactivation {

	/**
	 * Delete failed login log table for Limit Login Attempts feature
	 *
	 * @since 2.5.0
	 */
	public function delete_failed_logins_log_table() {

        global $wpdb;

        // Limit Login Attempts Log Table

        $table_name = $wpdb->prefix . 'asenha_failed_logins';

        // Drop table if already exists
        $wpdb->query("DROP TABLE IF EXISTS `". $table_name ."`");

	}
}