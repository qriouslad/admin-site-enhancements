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
	 * Handler for image uploads. Convert and resize images.
	 *
	 * @since 4.3.0
	 */
	public function image_upload_handler( $upload ) {

		// Exlude from conversion and resizing images with filenames ending with '-nr', e.g. birds-nr.png
		if ( false !== strpos( $upload['file'], '-nr.' ) ) {
			return $upload;
		}

		// Convert BMP into JPG
		if ( ( 'image/bmp' === $upload['type'] || 'image/x-ms-bmp' === $upload['type'] ) ) {
			$upload = $this->convert_image_to_jpg( 'bmp', $upload );
		}

		// Convert PNG without transparency into JPG
		if ( 'image/png' === $upload['type'] ) {
			$upload = $this->convert_image_to_jpg( 'png', $upload );
		}

		// At this point, BMPs and PNGs are already converted to JPGs, unless excluded with '-nr' suffix.
		// So, we will only resize JPGs
		$mime_types_to_resize = array( 'image/jpeg', 'image/jpg' );

		if ( 
			( ! is_wp_error( $upload ) && 
			in_array( $upload['type'], $mime_types_to_resize ) &&
			filesize( $upload['file'] > 0 ) )
		) {

		}

		do_action( 'inspect', [ 'file', $upload['file'] ] );
		do_action( 'inspect', [ 'type', $upload['type'] ] );

		// https://developer.wordpress.org/reference/classes/wp_image_editor/
		$wp_image_editor = wp_get_image_editor( $upload['file'] );
		$image_size = $wp_image_editor->get_size();

		// do_action( 'inspect', [ 'wp_image_editor', $wp_image_editor ] );
		do_action( 'inspect', [ 'image_size', $image_size ] );

		$options = get_option( ASENHA_SLUG_U, array() );
		$max_width = $options['image_max_width'];
		$max_height = $options['image_max_height'];

		// Check upload image's dimension and only resize if larger than the defined max dimension
		if (
			( isset( $image_size['width'] ) && $image_size['width'] > $max_width ) ||
			( isset( $image_size['height'] ) && $image_size['height'] > $max_height )
		) {
			$wp_image_editor->resize( $max_width, $max_height, false ); // false is for no cropping
			$wp_image_editor->set_quality( 90 ); // default is 82
			$wp_image_editor->save( $upload['file'] );
		}

		return $upload;

	}

	/**
	 * Convert BMP or PNG without transparency into JPG
	 *
	 * @since 4.3.0
	 */
	public function convert_image_to_jpg( $file_extension, $upload ) {

		$image_object = null;

		// Get image object from uploaded BMP/PNG

		if ( 'bmp' === $file_extension ) {
			// Generate image object from BMP for conversion to JPG later
			if ( function_exists( 'imagecreatefrombmp' ) ) { // PHP >= v7.2
				$image_object = imagecreatefrombmp( $upload['file'] );
			} else { // PHP < v7.2
				require_once ASENHA_PATH . 'includes/bmp-to-image-object.php';
				$image_object = bmp_to_image_object( $upload['file'] );
			}
		}

		if ( 'png' === $file_extension ) {

			// Detect alpha/transparency in PNG
			$png_has_transparency = false;

			// We assume GD library is present, so 'imagecreatefrompng' function is available
			// Generate image object from PNG for potential conversion to JPG later. 
			$image_object = imagecreatefrompng( $upload['file'] );

			// Get image dimension
			list( $width, $height ) = getimagesize( $upload['file'] );

			// Run through pixels until transparent pixel is found
		    for ( $x = 0; $x < $width; $x++ ) {
		        for ( $y = 0; $y < $height; $y++ ) {
		            $pixel_color_index = imagecolorat( $image_object, $x, $y );
		            $pixel_rgba = imagecolorsforindex( $image_object, $pixel_color_index ); // array of red, green, blue and alpha values

		            if ( $pixel_rgba['alpha'] > 0 ) { // a pixel with alpha/transparency has been found
		            	// alpha value range from 0 (completely opaque) to 127 (fully transparent). 
		            	// Ref: https://www.php.net/manual/en/function.imagecolorallocatealpha.php
						$png_has_transparency = true;
						break 2; // Break both 'for' loops
		            }
		        }
		    }

			// Do not convert to JPG if PNG has alpha/transparency
			if ( $png_has_transparency ) {
				return $upload;
			}

		}

		// Assign new, unique file name for the converted image
		$wp_uploads 	= wp_upload_dir();
		$old_filename 	= wp_basename( $upload['file'] );
		$new_filename 	= wp_basename( str_ireplace( '.' . $file_extension, '.jpg', $old_filename ) );
		$new_filename 	= wp_unique_filename( $uploads['path'], $new_filename );

		// When conversion from BMP/PNG to JPG is successful. Last parameter is JPG quality (0-100).
		if ( imagejpeg( $image_object, $wp_uploads['path'] . '/' . $new_filename, 90 ) ) {

			unlink( $upload['file'] ); // delete original BMP/PNG

			// Add converted JPG info into $upload
			$upload['file'] = $wp_uploads['path'] . '/' . $new_filename;
			$upload['url'] 	= $wp_uploads['url'] . '/' . $new_filename;
			$upload['type']	= 'image/jpeg';

		}

		return $upload;

	}

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