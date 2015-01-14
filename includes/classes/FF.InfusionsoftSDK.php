<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FF_InfusionsoftSDK {

	/**
	 * Name of the Option stored in WP Options Table
	 * @var string
	 */
	private $option_name = 'fusionforms';

	private $options;

	/**
	 * Status of the API Connection
	 * @var string
	 */
	private $status = 'disconnected';

	public function __construct() {
		// set options
		$this->options = get_option( $this->option_name );
	}


	/**
	 * Display an Admin Notice based on Infusionsoft connection status
	 * @return null
	 */
	public function admin_notice() {

		switch($this->status) {
			case 'connected': break;

			case 'disconnected':
				$msg = sprintf(
					'%s <a href="%s">%s</a> %s',
					__( 'Your FusionForms setup is almost complete,', 'fusionforms' ),
					admin_url( 'options-general.php?page=fusionforms-settings' ),
					__( 'click here', 'fusionforms' ),
					__( 'to add your Infusionsoft credentials.', 'fusionforms' )
				);
				$type = 'update-nag';
			break;

			case 'error':
				$msg = __( 'FusionForms encountered an error connecting to Infusionsoft using the credentials you provided.', 'fusionforms' );
				$type = 'error';
			break;
		}

		if( $msg && $type )
			echo '<div class="'.$type.'"><p>'.$msg.'</p></div>';
	}


	/**
	 * Attempt to connect to an Infusionsoft Application
	 * @return mixed    instance of FF_iSDK if sucessful, false if error was encountered
	 */
	public function connect() {

		// kill if app or api key are empty
		if( empty( $this->options['is_appname'] ) || empty( $this->options['is_api_key'] ) )
			return false;

		$app = new FF_iSDK();

		try {

			$app->cfgCon($this->options['is_appname'], $this->options['is_api_key']);

			$this->status = 'connected';

			return $app;

		} catch(Exception $e) {

			$this->status = 'error';

			return false;

		}

	}


	/**
	 * Return the status of the Infusionsoft api connection
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}
}