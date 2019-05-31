<?php
/**
 * Plugin Name: WP Search Filter
 * Plugin URI: http://filippozanardo.com/
 * Description: WP Search and Filter you can create multiple search filter.
 * Version: 0.0.1
 * Author: Filippo Zanardo
 * Author URI: http://filippozanardo.com/
 * Requires at least: 4.3
 * Tested up to: 5.1
 * Text Domain: wp-search-filter
 * Domain Path: /languages/
 * License: GPL3+
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wsf_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wsf_fs() {
        global $wsf_fs;

        if ( ! isset( $wsf_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $wsf_fs = fs_dynamic_init( array(
                'id'                  => '3685',
                'slug'                => 'wp-search-filter',
                'type'                => 'plugin',
                'public_key'          => 'pk_fe26dcdbec4ed5ccc632d823bb824',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'edit.php?post_type=wpsf',
                    'account'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $wsf_fs;
    }

    // Init Freemius.
    wsf_fs();
    // Signal that SDK was initiated.
    do_action( 'wsf_fs_loaded' );
}

include_once( __DIR__ . '/vendor/autoload.php' );

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class WP_Search_Filter {
	/**
	 * The single instance of the class.
	 */
	private static $_instance = null;

	public $asana;

	public $roles;

	/**
	 * Main Instance.
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 */
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

		$this->setup_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'wpsf_loaded' );
	}

	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-search-filter' ), '1' );
	}

	/**
	 * Disable unserializing of the class.
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-search-filter' ), '1' );
	}

	private function setup_constants() {

		if ( ! defined( 'WPSF_VERSION' ) ) {
			define( 'WPSF_VERSION', '0.0.1' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'WPSF_PLUGIN_DIR' ) ) {
			define( 'WPSF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'WPSF_PLUGIN_URL' ) ) {
			define( 'WPSF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

	}

	private function init_hooks() {

		// Activation - works with symlinks
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this, 'activate' ) );
		//register_uninstall_hook(basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( 'WP_Search_Filter', 'deactivate' ));

		add_action( 'after_setup_theme', array( $this, 'carbon_boot' ) );
		add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );

		add_filter( 'template_include' , array($this, 'template_include') );
		add_action( 'wp_enqueue_scripts', array($this, 'wpsf_enqueue_scripts') , 99);
	}

	public function carbon_boot() {
		\Carbon_Fields\Carbon_Fields::boot();
	}

	public function activate() {
		$this->post_types->register_post_types();

		flush_rewrite_rules();
	}

	public function deactivate() {
	}

	public function wpsf_enqueue_scripts() {
		wp_enqueue_style( 'wpsf', WPSF_PLUGIN_URL . 'css/styles.css"', false );
	}


	public function template_include( $template ) {
		return $template;
	}

	public function includes() {


		include_once( 'includes/class-wpsf-post-types.php' );
		include_once( 'includes/class-wpsf-template-loader.php' );
		include_once( 'includes/admin/class-wpsf-metabox.php' );
		include_once( 'includes/class-wpsf-widget.php' );
		include_once( 'includes/class-wpsf-query.php' );
		include_once( 'includes/class-wpsf-frontend.php' );
		include_once( 'includes/shortcodes.php' );

		include_once( 'includes/functions.php' );

		if ( is_admin() ) {
			include_once( 'includes/admin/class-wpsf-admin.php' );
		}

		$this->post_types = Wpsf_Post_Types::instance();
		$this->template_loader = new Wpsf_Template_Loader;
	}

	public function load_textdomain() {

	}

}


function WPSF() {
	return WP_Search_Filter::instance();
}

$GLOBALS['wp_searh_filter'] = WPSF();
