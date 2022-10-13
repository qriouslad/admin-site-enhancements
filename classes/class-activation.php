<?php

namespace WPENHA\Classes;

/**
 * Plugin Activation
 *
 * @since 1.0.0
 */
class Activation {

	/**
	 * Code that runs on plugin activation
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		add_option( WPENHA_SLUG, array(), '', 'yes' );

	}

}