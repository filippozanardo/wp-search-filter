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
      $wpsfid = (isset($_REQUEST['wpsfid'])) ? sanitize_text_field($_REQUEST['wpsfid']) : false;
	    if($query->query_vars['s'] == 'wpsf_search_on' && $wpsfid){

        $formid = absint(str_replace('wpsf-form-','',$_GET['wpsfid']));

        $post_type = carbon_get_post_meta($formid,'wpsf_post_type');

        $default_number = get_option('posts_per_page');

        $relation = carbon_get_post_meta($formid,'wpsf_relation');
        if ( empty($relation) ) $relation = 'OR';

        $number = (isset($_REQUEST['wpsf_per_page'])) ? sanitize_text_field($_REQUEST['wpsf_per_page']) : 0;
        if ( $number == 0 ) {
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

        $keyword = !empty($_REQUEST['wpsfkeyword']) ? sanitize_text_field($_REQUEST['wpsfkeyword']) : null;

        $paged = ( get_query_var( 'paged') ) ? get_query_var( 'paged' ) : 1;


        $args = array(
          'post_type' => $post_type,
          'post_status' => 'publish',
          'orderby' => $query_orderby,
          'order' => $query_order,
          'paged'=> $paged,
          'posts_per_page' => $number,
          's' => esc_html($keyword),
        );

        $year_from = !empty($_REQUEST['wpsf-year-from']) ? sanitize_text_field($_REQUEST['wpsf-year-from']) : false;
        $year_to = !empty($_REQUEST['wpsf-year-to']) ? sanitize_text_field($_REQUEST['wpsf-year-to']) : false;

        if ( $year_from && $year_to ) {
          if ($year_from > $year_to ) {
            $args['date_query'] = array(
              array(
                'after' => array(
                  'year' => $year_from
                ),
                'before' => array(
                  'year' => $year_to
                ),
                'inclusive' => true,
              )
            );
          }else{
            $args['date_query'] = array(
              array(
                'after' => array(
                  'year' => $year_from
                ),
                'inclusive' => true,
              )
            );
          }
        }elseif($year_from) {
          $args['date_query'] = array(
            array(
              'after' => array(
                'year' => $year_from
              ),
              'inclusive' => true,
            )
          );
        }elseif($year_to) {
          $args['date_query'] = array(
            array(
              'before' => array(
                'year' => $year_to
              ),
              'inclusive' => true,
            )
          );
        }


        if ( !empty($_REQUEST['wpsf-year']) ) {
          if ( is_array( $_REQUEST['wpsf-year'] ) ) {

            foreach ($_REQUEST['wpsf-year'] as $yv ) {
              $year_value = !empty($yv) ? sanitize_text_field($yv) : false;
              if ( $year_value ) {
                $args['date_query'][] = array( 'year' => $year_value );
              }
            }
            if ( !empty($args['date_query']) ) $args['date_query']['relation'] = $relation;
          }else{
            $year_value = !empty($_REQUEST['wpsf-year']) ? sanitize_text_field($_REQUEST['wpsf-year']) : false;
            if ( $year_value ) {
              $args['date_query'] = array( 'year' => $_REQUEST['wpsf-year'] );
            }
          }

        }

        $taxonomies = get_taxonomies( array('public'=> true), 'names' );

        foreach ($_REQUEST as $gkey => $gvalue) {
          $gpieces = explode("-", $gkey);
          $taxkey = (isset($gpieces[0])) ? sanitize_text_field($gpieces[0]) : false;

          if ( $taxkey && $taxkey == 'wpsftax' ) {
            $taxvalue = (isset($gpieces[1])) ? sanitize_text_field($gpieces[1]) : false;
            if ( $taxvalue ) {
              if ( in_array($taxvalue, $taxonomies)) {
                if (!empty($gvalue)) {
                  $gvalue = sanitize_text_field($gvalue);
                  $args['tax_query'][] =
                    array(
                        'taxonomy' => $taxvalue,
                        'field'    => 'term_id',
                        'terms'    => $gvalue,
                    );
                }
              }
            }
          }
          if ( !empty($args['tax_query']) ) $args['tax_query']['relation'] = $relation;
        }

        foreach($args as $k => $v){
          $query->set( $k, $v );
    		}

    		return $query;

      }
    }


  }


}

Wpsf_Query::instance();
