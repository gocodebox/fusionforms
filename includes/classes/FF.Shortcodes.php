<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Define Shortcodes
 */
class FF_Shortcodes {

	public function __construct() {
		add_shortcode('fusionform', array($this,'output'));

		// enable shortcode output in widgets
		add_filter('widget_text', 'do_shortcode');
	}




	/**
	 * Output the webform
	 * @param  array    $atts   user submitted shortcode args
	 * @return html
	 */
	public function output($atts) {
		extract( shortcode_atts( array(
			'align' => 'center',
			'id'    => 0,
			'label' => 'above',
			'width' => '100%',

			'name'  => '' // pass through note attribute for users benefit only
		), $atts, 'fusionform') );

		if(!$id) return '<p><em>Fusion Forms Shortcode Error: Missing Infusionsoft Webform ID!</em></p>';

		$html = FusionForms()->app->getWebFormHtml($id);

		// get only the form element
		$form_start = strpos($html,'<form');
		$form_end = strpos($html,'</form>');
		$form_length = ($form_end+7)-$form_start;
		$form = substr($html,$form_start,$form_length);

		// remove some undesired attributes
		$patterns = array(
			'/(<[^>]+) onsubmit=".*?"/is',
			'/(<[^>]+) style=".*?"/i',
			'/(<[^>]+) bgcolor=".*?"/i',
			'/(<[^>]+) width=".*?"/i',
			'/(<[^>]+) sectionid=".*?"/i',
			'/(<[^>]+) valign=".*?"/i',
		);
		$form = preg_replace($patterns, '$1', $form);

		// remove tables
		$form = preg_replace('/\<[\/]?(table|thead|tfoot|tbody|tr|td)([^\>]*)\>/i', '', $form);

		// trim, cleanup new lines, remove whitespace characters
		$form = trim(str_replace(array('&nbsp;', "\r", "\n"), '', $form));

		$r = '<div class="fusionforms ff-webform align--'.$align.' label--'.$label.'" style="width:'.$width.';">'.$form.'</div><br class="ff-clear">';

		return $r;
	}

}