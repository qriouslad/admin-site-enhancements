<?php

namespace ASENHA\Classes;

/**
 * Class related to Custom Code features
 *
 * @since 3.6.0
 */
class Custom_Code {

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