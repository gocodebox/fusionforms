<?php
/**
 * Tiny MCE Customizations for the Built-in WYSYWIG Editor
 */
class FF_TinyMCE {
	public function __construct() {
		add_action('admin_footer',         array($this, 'enqueue_forms'));

		add_filter('mce_external_plugins', array($this, 'add_plugins'));
		add_filter('mce_buttons_2',        array($this, 'register_buttons'));
	}

	/**
	 * Register custom TinyMCE Plugins
	 * @param array / $plugins / array of plugins to load
	 * @return array / $plugins / array of plugins to load
	 */
	public function add_plugins($plugins) {
		$plugins['fusionforms_shortcodes_btn'] = FusionForms()->plugin_url() . '/assets/admin/js/TinyMCE-Plugins/shortcodes-btn.js';
		return $plugins;
	}


	/**
	 * Add a key=>val list of InfusionSoft forms to as a global JS variable to the admin foot so TinyMCE Plugin can get a list of the forms
	 *
	 * @return null
	 */
	public function enqueue_forms() {
		// get list of forms
		$forms = FusionForms()->app->getWebFormMap();

		// output the variable as a JS global var
		echo '<script type="text/javascript">window.FusionForms = window.FusionForms || {}; FusionForms.webforms = '.json_encode($forms).'; FusionForms.dir = "'.FusionForms()->plugin_url().'";</script>';

	}


	/**
	 * Add custom buttons to the WP TinyMCE Editor
	 * @param  array / $buttons / array of buttons
	 * @return array /          / array of buttons
	 */
	public function register_buttons($buttons) {
		$new_buttons = array(array_shift($buttons));
		$new_buttons[] = 'fusionforms_shortcodes_btn';
		$r = array_merge($new_buttons,$buttons);
		return $r;
	}
}