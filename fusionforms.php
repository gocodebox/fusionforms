<?php
/**
* Plugin Name: Fusion Forms by codeBOX
* Plugin URI: http://gocodebox.com
* Description: This plugin allows Infusionsoft users to quickly embed great looking webforms into their WordPress posts, pages, and sidebars!
* Version: 0.1.0
* Author: codeBOX
* Author URI: http://gocodebox.com
*
* Requires at least: 4.0
* Tested up to: 4.1
*
* @package 		FusionForms
* @category 	Core
* @author 		codeBOX
*/

/**
 * Restrict direct access
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'FusionForms') ) :

/**
 * Main FusionForms
 *
 * @class FusionForms
 */
final class FusionForms {

	protected static $_instance;

	public $version = '0.1.0';

	/**
	 * Publicly Accessible Infusionsoft SDK Instances
	 * @var object
	 */
	public $app;

	/**
	 * Main Instance of FusionForms
	 *
	 * Ensures only one instance of FusionForms is loaded or can be loaded.
	 *
	 * @static
	 * @return FusionForms - Main Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	/**
	 * Constructor
	 * @access public
	 * @return null
	 */
	public function __construct() {
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, "autoload" ) );

		$this->define_constants();

		add_action( 'init', array( $this, 'init' ), 0 );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_action_links' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_all' ) );

		add_action( 'wp_head', array($this, 'custom_css') );

		do_action( 'infusionpress_loaded' );
	}

	/**
	 * Autoloader for FusionForms classes
	 *
	 * @param  mixed $class
	 * @return void
	 */
	public function autoload( $class ) {
		$path = null;

		$file = str_replace( '_', '.', $class ) . '.php';

		if ( strpos ( $class, 'FF_iSDK' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/vendor/iSDK/';
			$file = 'isdk.php';
		}
		elseif ( strpos( $class, 'FF_' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/classes/';
		}

		if ( $path && is_readable( $path . $file ) ) {
			include_once( $path . $file );
			return;
		}
	}


	/**
	 * Define LifterLMS Constants
	 * @return null
	 */
	private function define_constants() {

		if ( ! defined( 'FF_PLUGIN_FILE' ) ) {
			define( 'FF_PLUGIN_FILE', __FILE__ );
		}

		if ( ! defined( 'FF_VERSION' ) ) {
			define( 'FF_VERSION', $this->version );
		}

		if ( ! defined( 'FF_PLUGIN_DIR' ) ) {
			define( 'FF_PLUGIN_DIR', WP_PLUGIN_DIR . "/" . plugin_basename( dirname(__FILE__) ) . '/');
		}
	}


	/**
	 * "Enqueue" custom CSS if it exists
	 * @return null
	 */
	public function custom_css() {
		$options = get_option( 'fusionforms' );

		if( $options['custom_css'] ) {
			echo '<style id="fusionforms-custom-css" type="text/css" media="all">'.$options['custom_css'].'</style>';
		}

	}


	/**
	 * Enqueue frontend scripts & styles
	 * @return null
	 */
	public function enqueue_all() {

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$stylesheet = 'fusionforms-style.css';
		} else {
			$stylesheet = 'fusionforms-style.min.css';
		}

		wp_register_style( 'fusionforms-style', $this->plugin_url() . '/assets/public/css/' . $stylesheet , array(), $this->version, 'all' );
		wp_enqueue_style( 'fusionforms-style' );
	}


	/**
	 * Init LifterLMS when WordPress Initialises.
	 * @return null
	 */
	public function init() {

		$iSDK = new FF_InfusionsoftSDK();
		$this->app = $iSDK->connect();

		if( !$this->app ) {

			if( is_admin() )
				add_action( 'admin_notices', array( $iSDK, 'admin_notice' ) );

		} else {

			new FF_TinyMCE();
			new FF_Shortcodes();

		}

		$this->status = $iSDK->get_status();

		if( is_admin() ) {
			new FF_OptionsPage();
		}
	}


	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}


	/**
	 * Add Action Links
	 * Settings action links
	 *
	 * @param   array  $links  array of links
	 * @return  array          updated array of links
	 */
	public function add_action_links ( $links ) {

		// kill function if there's 3 links (means the plugin is inactive)
		if (count($links) == 3) return $links;

		$links[] = '<a href="' . admin_url( 'options-general.php?page=fusionforms-settings' ) . '">' . __( 'Settings', 'infusionpress' ) . '</a>';

		return $links;
	}

}

endif;

/**
 * Returns the main instance of FusionForms
 *
 * @return LifterLMS
 */
function FusionForms() {
	return FusionForms::instance();
}

FusionForms();