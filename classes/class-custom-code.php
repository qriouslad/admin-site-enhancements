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
	 * Add Custom Body Class meta box for enabled post types
	 * 
	 * @since 3.9.0
	 */
	public function add_custom_body_class_meta_box( $post_type, $post ) {

		$options = get_option( ASENHA_SLUG_U, array() );
		$enable_custom_body_class_for = $options['enable_custom_body_class_for'];

		foreach ( $enable_custom_body_class_for as $post_type_slug => $is_custom_body_class_enabled ) {
			if ( ( get_post_type() == $post_type_slug ) && $is_custom_body_class_enabled ) {

				// Skip adding meta box for post types where Gutenberg is enabled
				// if ( 
				// 	function_exists( 'use_block_editor_for_post_type' ) 
				// 	&& use_block_editor_for_post_type( $post_type_slug ) 
				// ) {
				// 	continue; // go to the beginning of next iteration
				// }

				add_meta_box(
					'asenha-custom-body-class', // ID of meta box
					'Custom &lt;body&gt; Class', // Title of meta box
					[ $this, 'output_custom_body_class_meta_box' ], // Callback function
					$post_type_slug, // The screen on which the meta box should be output to
					'normal', // context
					'high' // priority
					// array(), // $args to pass to callback function. Ref: https://developer.wordpress.org/reference/functions/add_meta_box/#comment-342
				);

			}
		}

	}

	/**
	 * Render External Permalink meta box
	 *
	 * @since 3.9.0
	 */
	public function output_custom_body_class_meta_box( $post ) {
		?>
		<div class="custom-body-class-input">
			<input name="<?php echo esc_attr( 'custom_body_class' ); ?>" class="large-text" id="<?php echo esc_attr( 'custom_body_class' ); ?>" type="text" value="<?php echo esc_attr( get_post_meta( $post->ID, '_custom_body_class', true ) ); ?>" placeholder="e.g. light-theme new-year-promo" />
			<div class="custom-body-class-input-description">Use blank space to separate multiple classes, e.g. first-class second-class</div>
			<?php wp_nonce_field( 'custom_body_class_' . $post->ID, 'custom_body_class_nonce', false, true ); ?>
		</div>
		<?php
	}

	/**
	 * Save custom body class input
	 *
	 * @since 3.9.0
	 */
	public function save_custom_body_class( $post_id ) {

		// Only proceed if nonce is verified
		if ( isset( $_POST['custom_body_class_nonce'] ) && wp_verify_nonce( $_POST['custom_body_class_nonce'], 'custom_body_class_' . $post_id ) ) {

			// Get the value of external permalink from input field
			$custom_body_class = isset( $_POST['custom_body_class'] ) ? sanitize_text_field( trim( $_POST['custom_body_class'] ) ) : '';

			// Update or delete external permalink post meta
			if ( ! empty( $custom_body_class ) ) {
				update_post_meta( $post_id, '_custom_body_class', $custom_body_class );
			} else {
				delete_post_meta( $post_id, '_custom_body_class' );
			}

		}

	}

	/**
	 * Append custom body classes to the frontend <body> tag
	 *
	 * @since 4.4.0
	 */
	public function append_custom_body_class( $classes ) {

		// Only add custom body classes to the singular view of enabled post types
		if ( is_singular() ) {

			global $post;
			$custom_body_classes = get_post_meta( $post->ID, '_custom_body_class', true );

			if ( ! empty( $custom_body_classes ) ) {

				$custom_body_classes = explode( ' ', $custom_body_classes );

				foreach( $custom_body_classes as $custom_body_class ) {
					$classes[] = sanitize_html_class( $custom_body_class );
				}

			}

		}

		return $classes;

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