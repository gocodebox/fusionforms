<?php
/**
* Plugin Name: InfusionPress by codeBOX
* Plugin URI: http://gocodebox.com
* Description: Enables the Infusionsoft PHP SDK for use in various other Infusionsoft Related Plugins
* Version: 0.1.0
* Author: codeBOX
* Author URI: http://gocodebox.com
*
* Requires at least: 3.8
* Tested up to: 4.1
*
* @package 		InfusionPress
* @category 	Core
* @author 		codeBOX
*/

/**
 * Restrict direct access
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'InfusionPress') ) :

/**
 * Main InfusionPress
 *
 * @class InfusionPress
 */
final class InfusionPress {

	protected static $_instance;

	public $version = '0.1.0';

	/**
	 * Publicly Accessible Infusionsoft SDK Instances
	 * @var object
	 */
	public $app;


	/**
	 * Main Instance of InfusionPress
	 *
	 * Ensures only one instance of InfusionPress is loaded or can be loaded.
	 *
	 * @static
	 * @return InfusionPress - Main Instance
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

		// $this->includes();

		add_action( 'init', array( $this, 'init' ), 0 );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_action_links' ) );

		do_action( 'infusionpress_loaded' );
	}

	/**
	 * Autoloader for InfusionPress classes
	 *
	 * @param  mixed $class
	 * @return void
	 */
	public function autoload( $class ) {
		$path = null;

		$file = str_replace( '_', '.', $class ) . '.php';

		if ( strpos ( $class, 'IP_iSDK' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/vendor/iSDK/';
			$file = 'isdk.php';
		}
		elseif ( strpos( $class, 'IP_' ) === 0 ) {
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

		if ( ! defined( 'IP_PLUGIN_FILE' ) ) {
			define( 'IP_PLUGIN_FILE', __FILE__ );
		}

		if ( ! defined( 'IP_VERSION' ) ) {
			define( 'IP_VERSION', $this->version );
		}

		if ( ! defined( 'IP_PLUGIN_DIR' ) ) {
			define( 'IP_PLUGIN_DIR', WP_PLUGIN_DIR . "/" . plugin_basename( dirname(__FILE__) ) . '/');
		}
	}


	/**
	 * Init LifterLMS when WordPress Initialises.
	 * @return null
	 */
	public function init() {

		$iSDK = new IP_InfusionsoftSDK();
		$this->app = $iSDK->connect();

		if( !$this->app ) {

			if( is_admin() )
				add_action( 'admin_notices', array( $iSDK, 'admin_notice' ) );

		}

		if( is_admin() )
			new IP_OptionsPage();
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

		$links[] = '<a href="' . admin_url( 'admin.php?page=infusionpress-settings' ) . '">' . __( 'Settings', 'infusionpress' ) . '</a>';

		return $links;
	}

}

endif;

/**
 * Returns the main instance of InfusionPress
 *
 * @return LifterLMS
 */
function InfusionPress() {
	return InfusionPress::instance();
}

global $InfusionPress;
$InfusionPress = InfusionPress();