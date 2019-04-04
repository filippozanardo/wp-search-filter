<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Wpsf_Query {

  private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

  public function __construct() {

		$this->init_hooks();

	}

  public function init_hooks() {
    add_action( 'pre_get_posts', array($this ,'wpsf_search_query'),1000 );
  }

  public function wpsf_search_query($query){

    if($query->is_search()){
	    if($query->query_vars['s'] == 'wpsf_search_on' && isset($_GET['wpsfid'])){

        $formid = absint(str_replace('wpsf-form-','',$_GET['wpsfid']));

        $post_type = carbon_get_post_meta($formid,'wpsf_post_type');

        $default_number = get_option('posts_per_page');

        $relation = carbon_get_post_meta($formid,'wpsf_relation');
        if ( empty($relation) ) $relation = 'OR';

        if ( !empty($_GET['wpsf_per_page']) ) {
          $number = $_GET['wpsf_per_page'];
        }else{
          $per_page = carbon_get_post_meta($formid,'wpsf_per_page');
          if ( !empty($per_page) ) {
            $number = $per_page;
          }else{
            $number = $default_number;
          }
        }

        $query_order = null;
        $order = carbon_get_post_meta($formid,'wpsf_order');
        if ( !empty($order) ) {
          $query_order = $order;
        }

        $keyword = !empty($_GET['wpsfkeyword']) ? sanitize_text_field($_GET['wpsfkeyword']) : null;

        $paged = ( get_query_var( 'paged') ) ? get_query_var( 'paged' ) : 1;


        $args = array(
          'post_type' => $post_type,
          'post_status' => 'publish',
          //'meta_key'=> $ordermeta,
          'orderby' => $query_orderby,
          'order' => $query_order,
          'paged'=> $paged,
          'posts_per_page' => $number,
          //'meta_query' => $get_meta,
          //'tax_query' => $get_tax,
          's' => esc_html($keyword),
        );

        if ( !empty($_GET['wpsf-year-from']) && !empty($_GET['wpsf-year-to']) ) {
          if ($_GET['wpsf-year-to'] > $_GET['wpsf-year-from'] ) {
            $args['date_query'] = array(
              array(
                'after' => array(
                  'year' => $_GET['wpsf-year-from']
                ),
                'before' => array(
                  'year' => $_GET['wpsf-year-to']
                ),
                'inclusive' => true,
              )
            );
          }else{
            $args['date_query'] = array(
              array(
                'after' => array(
                  'year' => $_GET['wpsf-year-from']
                ),
                'inclusive' => true,
              )
            );
          }
        }elseif(!empty($_GET['wpsf-year-from'])) {
          $args['date_query'] = array(
            array(
              'after' => array(
                'year' => $_GET['wpsf-year-from']
              ),
              'inclusive' => true,
            )
          );
        }elseif(!empty($_GET['wpsf-year-to'])) {
          $args['date_query'] = array(
            array(
              'before' => array(
                'year' => $_GET['wpsf-year-to']
              ),
              'inclusive' => true,
            )
          );
        }

        if ( !empty($_GET['wpsf-year']) ) {
          if ( is_array( $_GET['wpsf-year'] ) ) {
            foreach ($_GET['wpsf-year'] as $yv ) {
              $args['date_query'][] = array( 'year' => $yv );
            }
            if ( !empty($args['date_query']) ) $args['date_query']['relation'] = $relation;
          }else{
            $args['date_query'] = array( 'year' => $_GET['wpsf-year'] );
          }

        }

        $taxonomies = get_taxonomies( array('public'=> true), 'names' );

        //$args['tax_query'] = null;

        foreach ($_GET as $gkey => $gvalue) {
          $gpieces = explode("-", $gkey);

          if ( isset($gpieces[0]) && $gpieces[0] == 'wpsftax' ) {
            if ( isset($gpieces[1]) ) {
              if ( in_array($gpieces[1], $taxonomies)) {
                if (!empty($gvalue)) {
                  $args['tax_query'][] =
                    array(
                        'taxonomy' => $gpieces[1],
                        'field'    => 'term_id',
                        'terms'    => $gvalue,
                    );
                }
              }
            }
          }
          if ( !empty($args['tax_query']) ) $args['tax_query']['relation'] = $relation;
        }

        // echo '<pre>';
        // print_r($_GET);
        // echo '</pre>';
        //
        // echo '<pre>';
        // print_r($args);
        // echo '</pre>';


        foreach($args as $k => $v){
          $query->set( $k, $v );
    		}
    		return $query;


      }
    }


  }


}

Wpsf_Query::instance();
