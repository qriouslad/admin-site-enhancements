<?php

namespace ASENHA\Classes;
use WP_Admin_Bar;

/**
 * Class related to Admin Interface functionalities
 *
 * @since 1.2.0
 */
class Admin_Interface {

	/**
	 * Wrapper for admin notices being output on admin screens
	 *
	 * @since 1.2.0
	 */
	public function admin_notices_wrapper() {

		echo '<div class="asenha-admin-notices-drawer" style="display:none;"><h2>Admin Notices</h2></div>';

	}

	/**
	 * Admin bar menu item for the hidden admin notices
	 *
	 * @link https://developer.wordpress.org/reference/classes/wp_admin_bar/add_menu/
	 * @link https://developer.wordpress.org/reference/classes/wp_admin_bar/add_node/
	 * @since 1.2.0
	 */
	public function admin_notices_menu( WP_Admin_Bar $wp_admin_bar ) {

		$wp_admin_bar->add_menu( array(
			'id'		=> 'asenha-hide-admin-notices',
			'parent'	=> 'top-secondary',
			'grou'		=> null,
			'title'		=> 'Notices<span class="asenha-admin-notices-counter" style="opacity:0;">0</span>',
			// 'href'		=> '',
			'meta'		=> array(
				'class'		=> 'asenha-admin-notices-menu',
				'title'		=> 'Click to view hidden admin notices',
			),
		) );

	}

	/**
	 * Inline CSS for the admin bar notices menu
	 *
	 * @since 1.2.0
	 */
	public function admin_notices_menu_inline_css() {

		wp_add_inline_style( 'admin-bar', '

			#wpadminbar .asenha-admin-notices-counter {
				box-sizing: border-box;
				margin: 1px 0 -1px 6px ;
				padding: 2px 6px 3px 5px;
				min-width: 18px;
				height: 18px;
				border-radius: 50%;
				background-color: #ca4a1f;
				color: #fff;
				font-size: 11px;
				line-height: 1.6;
				text-align: center;
			}

		' );

	}

	/**
	 * Hide admin bar on the frontend for the user roles selected
	 *
	 * @since 1.3.0
	 */
	public function hide_admin_bar_for_roles() {

		$options = get_option( ASENHA_SLUG_U );
		$hide_admin_bar = $options['hide_admin_bar'];
		$for_roles = $options['hide_admin_bar_for'];

		$current_user = wp_get_current_user();
		$current_user_roles = (array) $current_user->roles; // single dimensional array of role slugs

		// User has no role, i.e. logged-out

		if ( count( $current_user_roles ) == 0 ) {
			return false; // hide admin bar
		}

		// User has role(s). Do further checks.

		if ( isset( $for_roles ) && ( count( $for_roles ) > 0 ) ) {

			// Assemble single-dimensional array of roles for which admin bar would be hidden

			$roles_admin_bar_hidden = array();
	
			foreach( $for_roles as $role_slug => $admin_bar_hidden ) {
				if ( $admin_bar_hidden ) {
					$roles_admin_bar_hidden[] = $role_slug;
				}
			}

			// Check if any of the current user roles is one for which admin bar should be hidden

			foreach ( $current_user_roles as $role ) {
				if ( in_array( $role, $roles_admin_bar_hidden ) ) {
					return false; // hide admin bar
				}
			}

		}

		return true; // show admin bar

	}

}