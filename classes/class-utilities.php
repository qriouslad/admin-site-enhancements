<?php

namespace ASENHA\Classes;
use Walker_Nav_Menu_Checklist;

/**
 * Class related to Utilities features
 *
 * @since 1.5.0
 */
class Utilities {

	/**
	 * Add metabox to Appearance >> Menus page for the login logout menu items
	 *
	 * @since 3.4.0
	 */
	public function add_login_logout_metabox() {
		add_meta_box( 
			'add-login-logout', 
			'Login Logout', 
			array( $this, 'add_login_logout_menu_items' ), 
			'nav-menus', 
			'side', 
			'default' 
		);
	}

	/**
	 * Add menu items for the login logout metabox
	 *
	 * @since 3.4.0
	 */
	public function add_login_logout_menu_items() {

		// The ID of the currently selected menu
		global $nav_menu_selected_id;

		$menu_items = array(
			'asenha-login'		=> array( 
				'title' 	=> 'Log In',
				'url'		=> '#asenha-login',
				'classes'	=> array( 'asenha-login-menu-item' ),
			),
			'asenha-logout'		=> array( 
				'title'		=> 'Log Out',
				'url'		=> '#asenha-logout',
				'classes'	=> array( 'asenha-logout-menu-item' ),
			),
			'asenha-login-logout'	=> array( 
				'title'		=> 'Log In / Log Out',
				'url'		=> '#asenha-login-logout',
				'classes'	=> array( 'asenha-login-logout-menu-item' ),
			),
		);

		$item_details = array(
			'db_id'				=> 0,
			'object'			=> 'asenha',
			'object_id'			=> '',
			'menu_item_parent'	=> 0,
			'type'				=> 'custom',
			'title'				=> '',
			'url'				=> '',
			'target'			=> '',
			'attr_title'		=> '',
			'classes'			=> array(),
			'xfn'				=> '',
		);

		$menu_items_object = array();

		foreach ( $menu_items as $item_id => $details ) {
			$menu_items_object[ $details['title'] ]            = (object) $item_details;
			$menu_items_object[ $details['title'] ]->object_id = $item_id;
			$menu_items_object[ $details['title'] ]->title     = $details['title'];
			$menu_items_object[ $details['title'] ]->classes   = $details['classes'];
			$menu_items_object[ $details['title'] ]->url       = $details['url'];
		}

		$walker = new Walker_Nav_Menu_Checklist( array() );

		?>
		<div id="login-logout-links" class="loginlinksdiv">
			<div id="tabs-panel-login-logout-links-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
			<ul id="login-logout-links-checklist" class="list:login-logout-links categorychecklist form-no-clear">
				<?php echo walk_nav_menu_tree( 
					array_map( 'wp_setup_nav_menu_item', $menu_items_object ), 
					0, 
					(object) array( 'walker' => $walker) 
				); ?>
			</ul>
			</div>
			<p class="button-controls">
				<span class="add-to-menu">
					<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php echo esc_attr( 'Add to Menu' ); ?>" name="add-login-logout-links-menu-item" id="submit-login-logout-links" />
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php

	}

	/** 
	 * Setup login logout URL based on login state
	 * 
	 * @since 3.4.0
	 */
	public function set_login_logout_menu_item_dynamic_url( $menu_item ) {

			global $pagenow;
			$options = get_option( ASENHA_SLUG_U, array() );

			if ( $pagenow != 'nav-menus.php' && !defined('DOING_AJAX') && isset( $menu_item->url ) && false !== strpos( $menu_item->url, 'asenha' ) ) {

				// Define login URL based on whether 
				if ( array_key_exists( 'change_login_url', $options ) && $options['change_login_url'] ) {
					if ( array_key_exists( 'custom_login_slug', $options ) && ! empty( $options['custom_login_slug'] ) )  {
						$login_page_url = get_site_url() . '/' . $options['custom_login_slug'];
					}
				} else {
					$login_page_url = wp_login_url();
				}

				$logout_redirect_url = home_url();

				switch( $menu_item->url ) {
					case '#asenha-login';
						$menu_item->url = $login_page_url;
						break;
					case '#asenha-logout';
						$menu_item->url = wp_logout_url();
						break;
					case '#asenha-login-logout';
						$menu_item->url = ( is_user_logged_in() ) ? wp_logout_url() : $login_page_url;
						$menu_item->title = ( is_user_logged_in() ) ? 'Log Out' : 'Log In';
						break;
				}

			}

		return $menu_item;

	}

	/**
	 * Conditionally remove login or logout menu item based on is_user_logged_in()
	 *
	 * @since 3.4.0
	 */
	public function maybe_remove_login_or_logout_menu_item( $sorted_menu_items ) {

		foreach( $sorted_menu_items as $menu => $item ) {

			$item_classes = $item->classes;

			// Maybe remove Log In menu item
			if ( in_array( 'asenha-login-menu-item', $item_classes ) ) {
				if ( is_user_logged_in() ) {
					unset( $sorted_menu_items[$menu] );
				}
			}

			// Maybe remove Log Out menu item
			if ( in_array( 'asenha-logout-menu-item', $item_classes ) ) {
				if ( ! is_user_logged_in() ) {
					unset( $sorted_menu_items[$menu] );
				}
			}

		}

		return $sorted_menu_items;

	}

	/**
	 * Redirect to custom internal URL after login for user roles
	 *
	 * @param string $redirect_to_url URL to redirect to. Default is admin dashboard URL.
	 * @param string $origin_url URL the user is coming from.
	 * @param object $user logged-in user's data.
	 * @since 1.5.0
	 */
	public function redirect_for_roles_after_login( $username, $user ) {

		$options = get_option( ASENHA_SLUG_U, array() );
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

		$options = get_option( ASENHA_SLUG_U, array() );
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

		$options = get_option( ASENHA_SLUG_U, array() );
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

		$options = get_option( ASENHA_SLUG_U, array() );
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

	/**
	 * Maybe show custom robots.txt content
	 *
	 * @since 3.5.0
	 */
	public function maybe_show_custom_robots_txt_content( $output, $public ) {

		$options = get_option( ASENHA_SLUG_U, array() );

		if ( array_key_exists( 'robots_txt_content', $options ) && ! empty( $options['robots_txt_content'] ) ) {

			$output = wp_strip_all_tags( $options['robots_txt_content'] );

		}

		return $output;

	}

	/**
	 * Insert code before </head> tag
	 *
	 * @since 3.3.0
	 */
	public function insert_head_code() {

		$this->insert_code( 'head' );

	}

	/**
	 * Insert code after <body> tag
	 *
	 * @since 3.3.0
	 */
	public function insert_body_code() {

		$this->insert_code( 'body' );
		
	}

	/**
	 * Insert code in footer section before </body> tag
	 *
	 * @since 3.3.0
	 */
	public function insert_footer_code() {

		$this->insert_code( 'footer' );
		
	}

	/**
	 * Insert code
	 *
	 * @since 3.3.0
	 */
	public function insert_code( $location ) {

		// Do not insert code in admin, feed, robots or trackbacks
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}

		// Get option that stores the code
		$options = get_option( ASENHA_SLUG_U, array() );

		if ( 'head' == $location ) {

			$code = array_key_exists( 'head_code', $options ) ? $options['head_code'] : '';

		}

		if ( 'body' == $location ) {

			$code = array_key_exists( 'body_code', $options ) ? $options['body_code'] : '';

		}

		if ( 'footer' == $location ) {

			$code = array_key_exists( 'footer_code', $options ) ? $options['footer_code'] : '';

		}

		echo wp_unslash( $code ) . PHP_EOL;

	}


}