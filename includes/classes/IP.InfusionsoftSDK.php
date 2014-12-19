<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class IP_InfusionsoftSDK {

	/**
	 * Name of the Option stored in WP Options Table
	 * @var string
	 */
	private $option_name = 'infusionpress';

	private $options;

	/**
	 * Status of the API Connection
	 * @var string
	 */
	private $status = 'Not Connected';

	public function __construct() {
		// set options
		$this->options = get_option( $this->option_name );
	}

	/**
	 * Attempt to connect to an Infusionsoft Application
	 * @return mixed    instance of IP_iSDK if sucessful, false if error was encountered
	 */
	public function connect() {

		// kill if app or api key are empty
		if( empty( $this->options['is_appname'] ) || empty( $this->options['is_api_key'] ) )
			return false;

		$app = new IP_iSDK();

		try {

			$app->cfgCon($this->options['is_appname'], $this->options['is_api_key']);

			$this->status = __( 'Connected', 'infusionpress' );

			return $app;

		} catch(Exception $e) {

			$this->status = __( 'InfusionPress encountered an issue connecting to Infusionsoft using the credentials you provided!', 'infusionpress' );

			return false;

		}

	}


	/**
	 * Display an Admin Notice
	 * @return [type] [description]
	 */
	public function admin_notice() {

		echo '<div class="error"><p>';
		printf(
			__( '%s', 'infusionpress' ),
			$this->status
		);
		echo '</p></div>';

	}

}