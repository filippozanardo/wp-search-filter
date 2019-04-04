<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Wpsf_Admin {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
			self::$_instance->hooks();
		}
		return self::$_instance;
	}

	public function hooks() {
		add_filter( 'manage_wpsf_posts_columns', array( $this,'set_wpsf_columns' ) );
		add_action( 'manage_wpsf_posts_custom_column' , array( $this,'wpsf_columns'), 10, 2 );
  }

	public function set_wpsf_columns($columns){
		$columns['wpsf_shortcode'] = __( 'Shortcode', 'wp-search-filter' );

		return $columns;
	}

	public function wpsf_columns( $column, $post_id ) {
	    switch ( $column ) {

	        case 'wpsf_shortcode' :

	            echo '[wpsf id="'.$post_id.'"]';
	            break;

	    }
	}
}

Wpsf_Admin::instance();
