<?php

namespace WPENHA\Classes;

/**
 * Class related to content administration enhancements
 *
 * @since 1.0.0
 */
class Content_Admin {

	/**
	 * Current post type. For Content Admin >> Show Custom Taxonomy Filters functionality.
	 */
	public $post_type;

	/**
	 * Show featured images column in list tables for pages and post types that support featured image
	 *
	 * @since 1.0.0
	 */
	public function show_featured_image_column() {

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $post_types as $post_type_key => $post_type_name ) {

			if ( post_type_supports( $post_type_key, 'thumbnail' ) ) {

				add_filter( "manage_{$post_type_name}_posts_columns",[ $this, 'add_featured_image_column' ] );
				add_action( "manage_{$post_type_name}_posts_custom_column", [ $this, 'add_featured_image' ], 10, 2 );

			}

		}

	}

	/**
	 * Add a column called Featured Image as the first column
	 *
	 * @param mixed $columns
	 * @return void
	 * @since 1.0.0
	 */
	public function add_featured_image_column( $columns ) {

		$new_columns = array();

		foreach ( $columns as $key => $value ) {

			if ( $key == 'title' ) {

				$new_columns['wpenha-featured-image'] = 'Featured Image';	

			}

			$new_columns[$key] = $value;

		}

		return $new_columns;

	}

	/**
	 * Echo featured image's in thumbnail size to a column
	 *
	 * @param mixed $column_name
	 * @param mixed $id
	 * @since 1.0.0
	 */
	public function add_featured_image( $column_name, $id ) {

		if ( 'wpenha-featured-image' === $column_name ) {

			if ( has_post_thumbnail( $id ) ) {

				$size = 'thumbnail';

				echo get_the_post_thumbnail( $id, $size, '' );

			} else {

				echo '<img src="' . esc_url( plugins_url( 'assets/img/default_featured_image.jpg', __DIR__ ) ) . '" />';

			}
		}

	}

	/**
	 * Show excerpt column in list tables for pages and post types that support excerpt.
	 *
	 * @since 1.0.0
	 */
	public function show_excerpt_column() {

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $post_types as $post_type_key => $post_type_name ) {

			if ( post_type_supports( $post_type_key, 'excerpt' ) ) {

				add_filter( "manage_{$post_type_name}_posts_columns",[ $this, 'add_excerpt_column' ] );
				add_action( "manage_{$post_type_name}_posts_custom_column", [ $this, 'add_excerpt' ], 10, 2 );

			}

		}

	}

	/**
	 * Add a column called Featured Image as the first column
	 *
	 * @param mixed $columns
	 * @return void
	 * @since 1.0.0
	 */
	public function add_excerpt_column( $columns ) {

		$new_columns = array();

		foreach ( $columns as $key => $value ) {

			$new_columns[$key] = $value;

			if ( $key == 'title' ) {

				$new_columns['wpenha-excerpt'] = 'Excerpt';	

			}

		}

		return $new_columns;

	}

	/**
	 * Echo featured image's in thumbnail size to a column
	 *
	 * @param mixed $column_name
	 * @param mixed $id
	 * @since 1.0.0
	 */
	public function add_excerpt( $column_name, $id ) {

		if ( 'wpenha-excerpt' === $column_name ) {

			$excerpt = get_the_excerpt( $id ); // about 310 characters
			$excerpt = substr( $excerpt, 0, 160 ); // truncate to 160 characters
			$short_excerpt = substr( $excerpt, 0, strrpos( $excerpt, ' ' ) );

			echo $short_excerpt;

		}

	}

	/**
	 * Add ID column list table of pages, posts, custom post types, media, taxonomies, custom taxonomies, users amd comments
	 *
	 * @since 1.0.0
	 */
	public function show_id_column() {

		// For pages and hierarchical post types list table

		add_filter( 'manage_pages_columns', [ $this, 'add_id_column' ] );
		add_action( 'manage_pages_custom_column', [ $this, 'add_id_echo_value' ], 10, 2 );				

		// For posts and non-hierarchical custom posts list table

		add_filter( 'manage_posts_columns', [ $this, 'add_id_column' ] );
		add_action( 'manage_posts_custom_column', [ $this, 'add_id_echo_value' ], 10, 2 );

		// For media list table

		add_filter( 'manage_media_columns', [ $this, 'add_id_column' ] );
		add_action( 'manage_media_custom_column', [ $this, 'add_id_echo_value' ], 10, 2 );

		// For list table of all taxonomies

		$taxonomies = get_taxonomies( [ 'public' => true ], 'names' );

		foreach ( $taxonomies as $taxonomy ) {
			
			add_filter( 'manage_edit-' . $taxonomy . '_columns', [ $this, 'add_id_column' ] );
			add_action( 'manage_' . $taxonomy . '_custom_column', [ $this, 'add_id_return_value' ], 10, 3 );

		}

		// For users list table

		add_filter( 'manage_users_columns', [ $this, 'add_id_column' ]);	
		add_action( 'manage_users_custom_column', [ $this, 'add_id_return_value' ], 10, 3 );

		// For comments list table

		add_filter( 'manage_edit-comments_columns', [ $this, 'add_id_column' ]);	
		add_action( 'manage_comments_custom_column', [ $this, 'add_id_echo_value' ], 10, 3 );		

	}

	/**
	 * Add a column called ID
	 *
	 * @param mixed $columns
	 * @return void
	 * @since 1.0.0
	 */
	public function add_id_column( $columns ) {

		$columns['wpenha-id'] = 'ID';

		return $columns;

	}

	/**
	 * Echo post ID value to a column
	 *
	 * @param mixed $column_name
	 * @param mixed $id
	 * @since 1.0.0
	 */
	public function add_id_echo_value( $column_name, $id ) {

		if ( 'wpenha-id' === $column_name ) {
			echo $id;
		}

	}

	/**
	 * Return post ID value to a column
	 *
	 * @param mixed $value
	 * @param mixed $column_name
	 * @param mixed $id
	 * @since 1.0.0
	 */
	public function add_id_return_value( $value, $column_name, $id ) {

		if ( 'wpenha-id' === $column_name ) {
			$value = $id;
		}

		return $value;

	}

	/**
	 * Hide comments column in list tables for pages, post types that support comments, and alse media/attachments.
	 *
	 * @since 1.0.0
	 */
	public function hide_comments_column() {

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $post_types as $post_type_key => $post_type_name ) {

			if ( post_type_supports( $post_type_key, 'comments' ) ) {

				if ( 'attachment' != $post_type_name ) {

					// For list tables of pages, posts and other post types
					add_filter( "manage_{$post_type_name}_posts_columns", [ $this, 'remove_comment_column' ] );

				} else {

					// For list table of media/attachment
					add_filter( 'manage_media_columns', [ $this, 'remove_comment_column' ] );

				}

			}

		}

	}

	/**
	 * Add a column called ID
	 *
	 * @param mixed $columns
	 * @return void
	 * @since 1.0.0
	 */
	public function remove_comment_column( $columns ) {

		unset( $columns['comments'] );

		return $columns;

	}

	/**
	 * Hide tags column in list tables for posts.
	 *
	 * @since 1.0.0
	 */
	public function hide_post_tags_column() {

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $post_types as $post_type_key => $post_type_name ) {

			if ( $post_type_name == 'post' ) {

				add_filter( "manage_posts_columns", [ $this, 'remove_post_tags_column' ] );

			}

		}

	}

	/**
	 * Add a column called ID
	 *
	 * @param mixed $columns
	 * @return void
	 * @since 1.0.0
	 */
	public function remove_post_tags_column( $columns ) {

		unset( $columns['tags'] );

		return $columns;

	}

	/**
	 * Show custom (hierarchical) taxonomy filter(s) for all post types.
	 *
	 * @since 1.0.0
	 */
	public function show_custom_taxonomy_filters( $post_type ) {

		$post_taxonomies = get_object_taxonomies( $post_type, 'objects' );

		// Only show custom taxonomy filters for post types other than 'post'

		if ( 'post' != $post_type ) {

			array_walk( $post_taxonomies, [ $this, 'output_taxonomy_filter' ] );

		}

	}

	/**
	 * Output filter on the post type's list table for a taxonomy
	 *
	 * @since 1.0.0
	 */
	public function output_taxonomy_filter( $post_taxonomy ) {

		// Only show taxonomy filter when the taxonomy is hierarchical

		if ( true === $post_taxonomy->hierarchical ) {

			wp_dropdown_categories( array(
				'show_option_all'	=> sprintf( 'All %s', $post_taxonomy->label ),
				'orderby'			=> 'name',
				'order'				=> 'ASC',
				'hide_empty'		=> false,
				'hide_if_empty'		=> true,
				'selected'			=> filter_input( INPUT_GET, $post_taxonomy->query_var, FILTER_SANITIZE_STRING ),
				'hierarchical'		=> true,
				'name'				=> $post_taxonomy->query_var,
				'taxonomy'			=> $post_taxonomy->name,
				'value_field'		=> 'slug',
			) );

		}

	}

	/**
	 * Enable duplication of pages, posts and custom posts
	 *
	 * @since 1.0.0
	 */
	public function wpenha_enable_duplication() {

		$original_post_id = intval( sanitize_text_field( $_REQUEST['post'] ) );
		$nonce = sanitize_text_field( $_REQUEST['nonce'] );

		if ( wp_verify_nonce( $nonce, 'wpenha-duplicate-' . $original_post_id ) && current_user_can( 'edit_posts' ) ) {

			$original_post = get_post( $original_post_id );

			// Set some attributes for the duplicate post

			$new_post_title_suffix = ' (DUPLICATE)';
			$new_post_status = 'draft';
			$current_user = wp_get_current_user();
			$new_post_author_id = $current_user->ID;

			// Create the duplicate post and store the ID
			
			$args = array(

				'comment_status'	=> $original_post->comment_status,
				'ping_status'		=> $original_post->ping_status,
				'post_author'		=> $new_post_author_id,
				'post_content'		=> $original_post->post_content,
				'post_excerpt'		=> $original_post->post_excerpt,
				'post_parent'		=> $original_post->post_parent,
				'post_password'		=> $original_post->post_password,
				'post_status'		=> $new_post_status,
				'post_title'		=> $original_post->post_title . $new_post_title_suffix,
				'post_type'			=> $original_post->post_type,
				'to_ping'			=> $original_post->to_ping,
				'menu_order'		=> $original_post->menu_order,

			);

			$new_post_id = wp_insert_post( $args );

			// Copy over the taxonomies

			$original_taxonomies = get_object_taxonomies( $original_post->post_type );

			if ( ! empty( $original_taxonomies ) && is_array( $original_taxonomies ) ) {

				foreach( $original_taxonomies as $taxonomy ) {

					$original_post_terms = wp_get_object_terms( $original_post_id, $taxonomy, array( 'fields' => 'slugs' ) );

					wp_set_object_terms( $new_post_id, $original_post_terms, $taxonomy, false );

				}

			}

			// Copy over the post meta
			
			$original_post_metas = get_post_meta( $original_post_id ); // all meta keys and the corresponding values

			if ( ! empty( $original_post_metas ) ) {

				foreach( $original_post_metas as $meta_key => $meta_values ) {

					foreach( $meta_values as $meta_value ) {

						add_post_meta( $new_post_id, $meta_key, $meta_value );

					}

				}

			}

			// Redirect to list table of the corresponding post type of original post
			
			$post_type = get_post_type( $original_post_id );

			if ( 'post'	== $post_type ) {

				wp_redirect( admin_url( 'edit.php' ) );

			} else {

				wp_redirect( admin_url( 'edit.php?post_type=' . $post_type ) );

			}

		} else {

			wp_die( 'You do not have permission to perform this action.' );

		}

	}

	/** 
	 * Add row action link to perform duplication in page/post list tables
	 *
	 * @since 1.0.0
	 */
	public function add_duplication_action_link( $actions, $post ) {

		if ( current_user_can( 'edit_posts' ) ) {

			$actions['wpenha-duplicate'] = '<a href="admin.php?action=wpenha_enable_duplication&amp;post=' . $post->ID . '&amp;nonce=' . wp_create_nonce( 'wpenha-duplicate-' . $post->ID ) . '" title="Duplicate this as draft">Duplicate</a>';

		}

		return $actions;

	}

}