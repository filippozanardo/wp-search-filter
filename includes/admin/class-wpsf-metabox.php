<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Wpsf_Metabox {

		private $prefix = 'wpsf_';
		private static $_instance = null;


    public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
				self::$_instance->hooks();
			}
			return self::$_instance;
		}

		public function hooks() {

        add_action( 'carbon_fields_register_fields', array( $this, 'register_carbonfields' ) );
    }

    public function register_carbonfields() {

    	Container::make('post_meta', 'Option')
      ->where( 'post_type', '=', 'wpsf' )
			->add_tab( __('General'), array(
				Field::make("set", $this->prefix . 'post_type', __( 'Post Type', 'wp-search-filter' ))
	      	//->set_datastore( new Serialized_Theme_Options_Datastore() )
					->help_text(__( 'Choose the post type you want to include in the search', 'wp-search-filter' ))
	        ->set_options(array( $this, 'get_post_types' )),
				Field::make("text", $this->prefix . 'per_page', __( 'Results per Page:', 'wp-search-filter' ))
		      //->set_datastore( new Serialized_Theme_Options_Datastore() )
					->set_default_value( 20 )
					->help_text(__( 'How many posts shows at each result page?' )),
				Field::make( 'select', $this->prefix . 'orderby', __( 'Order by:', 'wp-search-filter' ) )
		    	->add_options( array(
		        'date' => 'Date',
						'title' => 'Post Title',
						'name' => 'Slug',
						'modified' => 'Modified Date',
						'parent' => 'Post Parent ID',
						'rand' => 'Random',
						'menu_order' => 'Menu Order',
						'meta_value' => 'Meta Value',
						'meta_value_num' => 'Numeric Meta Value',
		    	) ),
					Field::make( 'select', $this->prefix . 'order', 'Sorting Order:' )
			    ->add_options( array(
			        'ASC' => 'Ascending',
			        'DESC' => 'Descending',
			    ) ),
					Field::make("text", $this->prefix . 'button_text', __( 'Search Button Text:', 'wp-search-filter' ))
			      //->set_datastore( new Serialized_Theme_Options_Datastore() )
						->set_default_value( 'Search' )
						->help_text(__( 'The text of the submit button' )),
					Field::make( 'select', $this->prefix . 'relation', 'Default relation:' )
				    ->add_options( array(
				        'OR' => 'OR',
				        'AND' => 'AND',
				    ) ),
					Field::make( 'checkbox', $this->prefix . 'add_textbox', __( 'Add Default Text Box' ) )
			    	->set_option_value( 'yes' ),
    	) )
    	->add_tab( __('Layout'), array(
				Field::make( 'radio', $this->prefix . 'template', 'Template' )
				->add_options( array(
						'default' => __( 'Default Search Template', 'wp-search-filter' ),
						//'ajax' => __( 'Ajax', 'wp-search-filter' ),
				) )
    	) );


			//var_dump($taxonomies);
			$ui = Container::make( 'post_meta', 'Search Field' )->where( 'post_type', '=', 'wpsf' );
			$ui->add_fields( array(
				$this->generateui()
			));


			Container::make( 'theme_options', __( 'Search Options', 'wp-search-filter' ) )
			->set_page_parent( 'edit.php?post_type=wpsf' )
	    ->add_fields( array(
				Field::make( 'select', $this->prefix . 'advanced_page', __( 'Advanced Search Page' ) )
					->set_options($this->getPages()),
				Field::make( 'select', $this->prefix . 'def_filter', __( 'Default Filter' ) )
					->set_options($this->getSearchFilter()),
				Field::make( 'checkbox', $this->prefix . 'enable_search', __( 'Use In Default Search' ) )
	    		->set_option_value( 'yes' ),

	    ) );


    }

		public function generateui() {

			$args = array(
			  'public'   => true,
			);
			$output = 'names';
			$taxonomies = get_taxonomies( $args, $output );

			$z = Field::make( 'complex', $this->prefix . 'search-field', __( 'Search UI', 'wp-search-filter' ) );
				if ($taxonomies) {
						foreach ($taxonomies as $taxk => $tax) {
							$z->add_fields( $tax, array(
									Field::make("text", $this->prefix . 'label', __( 'Label:', 'wp-search-filter' )),
									Field::make( 'radio', $this->prefix . 'hide_empty', 'Hide Empty Terms' )
									->add_options( array(
											'yes' => __( 'Yes', 'wp-search-filter' ),
											'no' => __( 'No', 'wp-search-filter' ),
									) ),
									Field::make( 'radio', $this->prefix . 'filter', 'Filter' )
									->add_options( array(
											'include' => __( 'Include', 'wp-search-filter' ),
											'exclude' => __( 'Exclude', 'wp-search-filter' ),
									) ),
									Field::make( 'multiselect', $this->prefix . 'taxsel', 'Select Taxonomies' )
									->set_options($this->getTaxValue($taxk)),
									Field::make( 'radio', $this->prefix . 'display', 'Display Type' )
									->add_options( array(
											'dropdown' => __( 'Drop Down', 'wp-search-filter' ),
											'radio' => __( 'Radio', 'wp-search-filter' ),
											'checkbox' => __( 'Check Box', 'wp-search-filter' ),
											'multiselect' => __( 'Multi Select', 'wp-search-filter' ),
									) ),
									Field::make( 'text', $this->prefix . 'drop_label', 'Dropdown Label' )
									->set_default_value( 'Select' )
							    ->set_conditional_logic( array(
							        'relation' => 'OR',
							        array(
							            'field' => $this->prefix . 'display',
							            'value' => 'dropdown',
							            'compare' => '=',
							        ),
											array(
							            'field' => $this->prefix . 'display',
							            'value' => 'multiselect',
							            'compare' => '=',
							        )
							    ) ),
									Field::make( 'radio', $this->prefix . 'enable_search', 'Enable Search' )
									->add_options( array(
											'yes' => __( 'Yes', 'wp-search-filter' ),
											'no' => __( 'No', 'wp-search-filter' ),
									) )
									->set_conditional_logic( array(
											array(
													'field' => $this->prefix . 'display',
													'value' => 'multiselect',
													'compare' => '=',
											)
									) ),

							));
							}
					}

					$z->add_fields( 'year', array(
							Field::make("text", $this->prefix . 'label', __( 'Label:', 'wp-search-filter' )),
							Field::make( 'radio', $this->prefix . 'display', 'Display Type' )
							->add_options( array(
									'dropdown' => __( 'Drop Down', 'wp-search-filter' ),
									'radio' => __( 'Radio', 'wp-search-filter' ),
									'checkbox' => __( 'Check Box', 'wp-search-filter' ),
									'fromto' => __( 'From To', 'wp-search-filter' ),
							) ),
							Field::make( 'text', $this->prefix . 'drop_label', 'Dropdown Label' )
							->set_default_value( 'Select' )
							->set_conditional_logic( array(
									'relation' => 'OR',
									array(
											'field' => $this->prefix . 'display',
											'value' => 'dropdown',
											'compare' => '=',
									),
									array(
											'field' => $this->prefix . 'display',
											'value' => 'fromto',
											'compare' => '=',
									)
							) ),
					));

			return $z;
		}

    public function get_post_types() {

        $post_types = apply_filters( 'wpsf_post_types', get_post_types( array( 'public' => true ), 'objects' ) );

        foreach ( $post_types as $post_type ) {
            if ( $post_type->name  == 'attachment' ) continue;
            $types[ $post_type->name ] = $post_type->labels->name;
        }

        return $types;

    }

		public function getTaxValue($tax) {
			$terms = get_terms( $tax, array(
			    'hide_empty' => false,
			) );

			$taxs = array();
			if ( $terms ) {
				foreach ($terms as $term) {
					$taxs[ $term->term_id ] = $term->name;
				}
			}
			return $taxs;
		}

		public function get_all_metakeys(){
			global $wpdb;
			$table = $wpdb->prefix.'postmeta';
			$keys = $wpdb->get_results( "SELECT meta_key FROM $table GROUP BY meta_key",ARRAY_A);

			foreach($keys as $key){
				if($key['meta_key']=='uwpqsf-cpt' || $key['meta_key']=='uwpqsf-taxo' || $key['meta_key']=='uwpqsf-relbool' || $key['meta_key']=='uwpqsf-cmf'){
				}
				else{
					$meta_keys[] = 	$key['meta_key'];
				}
			}
			return $meta_keys;
		}


    public function check_number($value) {
        if ( ! is_int( $value ) ) {
            // Empty the value
            $value = '100';
        }
        return $value;
    }

		public function default_compare() {
			$compare = array(
				'1' => '=',
				'2' => '!=',
				'3' => '>',
				'4' => '>=',
				'5' => '<',
				'6' => '<=',
				'7' => 'LIKE',
				'8' => 'NOT LIKE',
				'9' => 'IN',
				'10' => 'NOT IN',
				'11' => 'BETWEEN',
				'12' => 'NOT BETWEEN',
				'13' => 'NOT EXISTS',
			);
			return $compare;
		}

		public function getPages() {
			// The Query
			$args = array(
				'post_type' => 'page',
				'posts_per_page' => -1
			);
			$the_query = new WP_Query( $args );
			$return = array();
			$return[0] = '';

			// The Loop
			if ( $the_query->have_posts() ) {
				while($the_query->have_posts()):$the_query->the_post();
					$return[ $the_query->post->ID ] = $the_query->post->post_title;
			  endwhile;
				wp_reset_postdata();
			}

			return $return;
		}

		public function getSearchFilter() {
			// The Query
			$args = array(
				'post_type' => 'wpsf',
				'posts_per_page' => -1
			);
			$the_query = new WP_Query( $args );
			$return = array();
			$return[0] = '';

			// The Loop
			if ( $the_query->have_posts() ) {
				while($the_query->have_posts()):$the_query->the_post();
					$return[ $the_query->post->ID ] = $the_query->post->post_title;
			  endwhile;
				wp_reset_postdata();
			}

			return $return;
		}


}

Wpsf_Metabox::instance();
