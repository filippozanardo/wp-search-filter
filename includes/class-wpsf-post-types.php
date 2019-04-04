<?php

class Wpsf_Post_Types {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_types' ), 0 );
	}

	/**
	 * Registers the custom post type and taxonomies.
	 */
	public function register_post_types() {

		$labels = array(
			'name'                  => __( 'Search Filter', 'wp-search-filter' ),
			'singular_name'         => __( 'Search Filter', 'wp-search-filter' ),
			'add_new'            => __( 'Add New', 'wp-search-filter' ),
			'add_new_item'       => __( 'Add New Search Filter', 'wp-search-filter' ),
			'edit_item'          => __( 'Edit Search Filter', 'wp-search-filter' ),
			'new_item'           => __( 'New Search Filter', 'wp-search-filter' ),
			'all_items'          => __( 'All Search Filters', 'wp-search-filter' ),
			'view_item'          => __( 'View Search Filter', 'wp-search-filter' ),
			'search_items'       => __( 'Search Search Filters', 'wp-search-filter' ),
			'not_found'          => __( 'No Search Filters found', 'wp-search-filter' ),
			'not_found_in_trash' => __( 'No Search Filters found in Trash', 'wp-search-filter' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Search Filter', 'wp-search-filter' )
		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => true,
			'query_var'           => false,
			'can_export'          => true,
			'rewrite'             => array( 'slug' => 'wpsf' ),
			'capability_type'     => 'post',
			'menu_position'       => null,
		);
		register_post_type( 'wpsf', $args );

	}

}
