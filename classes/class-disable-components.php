<?php

namespace ASENHA\CLasses;

/**
 * Class related to the Disable Components feature
 *
 * @since 2.2.0
 */
class Disable_Components {

	/**
	 * Disable the XML-RPC component
	 *
	 * @since 2.2.0
	 */
	public function maybe_disable_xmlrpc( $data ) {

		http_response_code(403);
		exit('You don\'t have permission to access this file.');

	}

}