<?php

namespace WPENHA\Classes;

/**
 * Class related to content administration enhancements
 *
 * @since 1.0.0
 */
class Content_Admin {

	/**
	 * Add ID column list table of pages, posts, custom post types, media, taxonomies, custom taxonomies, users amd comments
	 *
	 * @since 1.0.0
	 */
	public function show_ids() {

		// For pages and hierarchical post types list table

		add_filter( 'manage_pages_columns', [ $this, 'add_id_column' ], 10, 1 );
		add_action( 'manage_pages_custom_column', [ $this, 'add_id_echo_value' ], 10, 2 );				

		// For posts and non-hierarchical custom posts list table

		add_filter( 'manage_posts_columns', [ $this, 'add_id_column' ], 10, 1 );
		add_action( 'manage_posts_custom_column', [ $this, 'add_id_echo_value' ], 10, 2 );

		// For media list table

		add_filter( 'manage_media_columns', [ $this, 'add_id_column' ], 10, 1 );
		add_action( 'manage_media_custom_column', [ $this, 'add_id_echo_value' ], 10, 2 );

		// For list table of all taxonomies

		$taxonomies = get_taxonomies( [ 'public' => true ], 'names' );

		foreach ( $taxonomies as $taxonomy ) {
			
			add_filter( 'manage_edit-' . $taxonomy . '_columns', [ $this, 'add_id_column' ], 10, 1 );
			add_action( 'manage_' . $taxonomy . '_custom_column', [ $this, 'add_id_return_value' ], 10, 3 );

		}

		// For users list table

		add_filter( 'manage_users_columns', [ $this, 'add_id_column' ], 10, 1);	
		add_action( 'manage_users_custom_column', [ $this, 'add_id_return_value' ], 10, 3 );

		// For comments list table

		add_filter( 'manage_edit-comments_columns', [ $this, 'add_id_column' ], 10, 1);	
		add_action( 'manage_comments_custom_column', [ $this, 'add_id_echo_value' ], 10, 3 );		

	}

	/**
	 * Add a column called ID
	 *
	 * @param mixed $columns
	 * @return void
	 * @since 1.0.0
	 */
	public function add_id_column( $post_columns ) {

		$post_columns['wpenha-show-ids'] = 'ID';

		return $post_columns;

	}

	/**
	 * Echo post ID value to a column
	 *
	 * @param mixed $column_name
	 * @param mixed $id
	 * @since 1.0.0
	 */
	public function add_id_echo_value( $column_name, $id ) {

		if ( 'wpenha-show-ids' === $column_name ) {
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

		if ( 'wpenha-show-ids' === $column_name ) {
			$value = $id;
		}

		return $value;

	}

}