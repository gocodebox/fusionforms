<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Define an Options Page
 */
class FF_OptionsPage {

	/**
	 * Name of the Option stored in WP Options Table
	 * @var string
	 */
	private $option_name = 'fusionforms';

	/**
	 * Holds the values to be used in the fields callbacks
	 * @var
	 */
	private $options;


	/**
	 * Constructor
	 *
	 * @return  null
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}


	/**
	 * Add options page
	 *
	 * @return  null
	 */
	public function add_options_page() {
		add_options_page(
			'Fusion Forms - Infusionsoft Settings',
			'Fusion Forms',
			'manage_options',
			'fusionforms-settings',
			array( $this, 'admin_page_callback' )
		);
	}

	/**
	 * Callback function for adding options page, renders the screen HTML
	 *
	 * @return null
	 */
	public function admin_page_callback() {

		$this->options = get_option( $this->option_name );
		?>
		<div class="wrap">
			<h2>Fusion Forms Settings</h2>
			<form method="post" action="options.php">
			<?php
				settings_fields( 'fusionforms_settings' );
				do_settings_sections( 'fusionforms-settings' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register all necessary settings
	 *
	 * @return  null
	 */
	public function register_settings() {
		register_setting(
			'fusionforms_settings',
			$this->option_name,
			null // sanitizer callback
		);

		add_settings_section(
			'is_api_settings',
			'Infusionsoft Credentials',
			array( $this, 'settings_callback' ),
			'fusionforms-settings'
		);

		add_settings_field(
			'is_appname',
			'Application Name',
			array( $this, 'form_field_callback' ),
			'fusionforms-settings',
			'is_api_settings',
			array(
				'description' => 'Your Infusionsoft application name is the subdomain of your Infusionsoft Login URL. If your login URL is "http://d8me3.infusionsoft.com", your machine name would be "d8me3".',
				'label_for'   => 'is_appname',
				'type'        => 'text'
			)
		);

		add_settings_field(
			'is_api_key',
			'API Key',
			array( $this, 'form_field_callback' ),
			'fusionforms-settings',
			'is_api_settings',
			array(
				'description' => 'Instructions on how to find your Infusionsot API Key can be found here: <a href="http://ug.infusionsoft.com/article/AA-00442" tabindex="999" target="_blank">http://ug.infusionsoft.com/article/AA-00442</a>.',
				'label_for'   => 'is_api_key',
				'type'        => 'text'
			)
		);

		add_settings_section(
			'ff_customization',
			'Web Form Customization',
			array( $this, 'settings_callback' ),
			'fusionforms-settings'
		);

		add_settings_field(
			'custom_css',
			'Custom CSS',
			array( $this, 'form_field_callback' ),
			'fusionforms-settings',
			'ff_customization',
			array(
				'description' => '<p style="text-align: right;width:99%;"><a class="wp-core-ui button-primary button-large" style="background:#d72227; border-color:#b60000; font-size: 20px; height: auto; padding: 10px 18px;" href="http://infusioncast.co/fusionforms-custom" tabindex="999" target="_blank">Need help making your form look great?</a></p>',
				'label_for'   => 'custom_css',
				'type'        => 'textarea'
			)
		);
	}



	/**
	 * Print the HTML for a form field
	 *
	 * @param  array   $args    array of config args
	 * @return null
	 */
	public function form_field_callback($args) {
		if( $args['type'] == 'text' ) {
			printf(
				'<input class="regular-text" type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" />',
				$args['label_for'],
				$this->option_name,
				isset( $this->options[$args['label_for']] ) ? esc_attr( $this->options[$args['label_for']]) : ''
			);
		}
		elseif( $args['type'] == 'textarea' ) {
			printf(
				'<textarea class="large-text code" id="%1$s" name="%2$s[%1$s]" rows="8">%3$s</textarea>',
				$args['label_for'],
				$this->option_name,
				isset( $this->options[$args['label_for']] ) ? esc_attr( $this->options[$args['label_for']]) : ''
			);
		}


		if( $args['description'] )
			printf(
				'<br><span class="description">%s</span>',
				$args['description']
			);
	}


	/**
	 * Output html content immediately pror
	 * @param  array   $args    array of config args
	 * @return null
	 */
	public function settings_callback( $args ) {
		if($args['id'] == 'is_api_settings' && FusionForms()->status == 'connected')
			printf(
				'<i>%s</i>',
				__( 'FusionForms is connected to your Infusionsoft application', 'pushpress' )
			);

	}

}