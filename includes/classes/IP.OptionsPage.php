<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Define an Options Page
 */
class IP_OptionsPage {

	/**
	 * Name of the Option stored in WP Options Table
	 * @var string
	 */
	private $option_name = 'infusionpress';

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
			'InfusionPress - Infusionsoft Settings',
			'InfusionPress',
			'manage_options',
			'infusionpress-settings',
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
			<h2>InfusionPress Settings</h2>
			<form method="post" action="options.php">
			<?php
				settings_fields( 'infusionpress_settings' );
				do_settings_sections( 'infusionpress-settings' );
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
			'infusionpress_settings',
			$this->option_name,
			null
		);

		add_settings_section(
			'is_api_settings',
			'Infusionsoft Credentials',
			null,
			'infusionpress-settings'
		);

		add_settings_field(
			'is_appname',
			'Application Name',
			array( $this, 'text_field_callback' ),
			'infusionpress-settings',
			'is_api_settings',
			array(
				'description' => 'Your Infusionsoft application name is the subdomain of your Infusionsoft Login URL. If your login URL is "http://d8me3.infusionsoft.com", your machine name would be "d8me3".',
				'label_for'   => 'is_appname'
			)
		);

		add_settings_field(
			'is_api_key',
			'API Key',
			array( $this, 'text_field_callback' ),
			'infusionpress-settings',
			'is_api_settings',
			array(
				'description' => 'Instructions on how to find your Infusionsot API Key can be found here: <a href="http://ug.infusionsoft.com/article/AA-00442" tabindex="999" target="_blank">http://ug.infusionsoft.com/article/AA-00442</a>.',
				'label_for'   => 'is_api_key'
			)
		);
	}


	/**
	 * Print the HTML for a text input
	 *
	 * @param  array   $args    array of config args
	 * @return null
	 */
	public function text_field_callback($args) {
		printf(
			'<input class="regular-text" type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" />',
			$args['label_for'],
			$this->option_name,
			isset( $this->options[$args['label_for']] ) ? esc_attr( $this->options[$args['label_for']]) : ''
		);

		if($args['description'])
			printf(
				'<br><span class="description">%s</span>',
				$args['description']
			);
	}

}