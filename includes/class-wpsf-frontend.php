<?php

class Wpsf_Frontend {

	private static $_instance = null;

  public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
			self::$_instance->hooks();
		}
		return self::$_instance;
	}

    public function isEnabled() {

				$wpsf_enable_search = carbon_get_theme_option('wpsf_enable_search');
				$wpsf_advanced_page = carbon_get_theme_option('wpsf_advanced_page');

        if ( $wpsf_advanced_page ) {
            if ( is_page( $wpsf_advanced_page ) ) {
                return true;
            }
        }

				if ( $wpsf_enable_search ) {
					if ( is_search() ) {
						return true;
					}
				}

        return false;
    }

		public function hooks() {
        add_action( 'wp_enqueue_scripts', array($this, 'wpsf_enqueue_scripts') , 99);
				add_filter( 'template_include' , array($this, 'template_include') );

				add_action( 'wp_ajax_nopriv_wpsf_remove_facet', array($this, 'wpsf_remove_facet') );
    		add_action( 'wp_ajax_wpsf_remove_facet', array($this, 'wpsf_remove_facet') );
    }

		public function wpsf_remove_facet(){
			$cperma = get_permalink();
			$resp = (isset($_REQUEST['key'])) ? sanitize_text_field($_REQUEST['key']) : false;
			if ( $resp ) {
					$response = ' '.$resp;
					$item = (isset($_REQUEST['item'])) ? sanitize_text_field($_REQUEST['item']) : false;
					if ( $item ) {
						$response .= ' '.$item;
					}
					wp_send_json_success($cperma.$response);
			}
			wp_send_json_error();
		}

		public function template_include( $template ) {

			$wpsf_advanced_page = carbon_get_theme_option('wpsf_advanced_page');
			$wpsf_enable_search = carbon_get_theme_option('wpsf_enable_search');

			if ( $wpsf_advanced_page ) {
				if ( is_page( $wpsf_advanced_page ) ) {
					$template = WPSF()->template_loader->get_template_part( 'wpsf' ,'advanced',false );
				}
			}

			if ( $wpsf_enable_search ) {
				if ( is_search() ) {
					$template = WPSF()->template_loader->get_template_part( 'wpsf' ,'advanced',false );
				}
			}

			return $template;
		}

    public function wpsf_enqueue_scripts() {

        $enabled = $this->isEnabled();

        if($enabled) {

            wp_enqueue_style( 'wpsf', WPSF_PLUGIN_URL . 'css/styles.css"', false );

						wp_register_script( 'wpsf-ext', WPSF_PLUGIN_URL . 'js/ext.js', null, null, true );
            wp_enqueue_script( 'wpsf-ext' );

						wp_register_script( 'wpsf', WPSF_PLUGIN_URL . 'js/main.js', null, null, true );
            wp_localize_script( 'wpsf', 'wpsflocalize', array(
            	'dataurl' => WPSF_PLUGIN_URL,
							'ajaxurl' => admin_url( 'admin-ajax.php' )
            ));
            wp_enqueue_script( 'wpsf' );

        }
    }

}

Wpsf_Frontend::instance();
