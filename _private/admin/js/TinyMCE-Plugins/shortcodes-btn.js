/*global tinymce */
(function(){
	var FusionForms = window.FusionForms;


	/**
	 * Convert the the webform key: val object into a format usable by TinyMCE listbox
	 * @return obj
	 */
	function get_webforms_list() {
		var r = [];

		// convert into the TinyMCE listbox format
		for(var f in FusionForms.webforms) {
			if(FusionForms.webforms) {
				r.push({
					text: FusionForms.webforms[f],
					value: f
				});
			}
		}

		// sort the list alphabetically by "text" value
		r.sort(function(a,b){
			if(a.text === b.text) {
				return 0;
			}

			return (a.text < b.text) ? -1 : 1;
		});

		// return the object
		return r;
	}


	/**
	 * Get the name of a webform from the global object using the webform ID
	 * @param  {int} 	id 		webform id
	 * @return {string}    		name of the webform
	 */
	function get_webform_name_by_id(id) {
		return FusionForms.webforms[id];
	}


	tinymce.create('tinymce.plugins.fusionforms_shortcodes_btn', {
		init: function(editor) {
			var menu = [{
				text: 'InfusionSoft Webform',
				onclick: function() {
					editor.windowManager.open({
						title: 'Select an InfusionSoft Webform',
						body: [{
							label:  'Webform',
							name:   'webform_id',
							type:   'listbox',
							values: get_webforms_list()
						},{
							label:  'Label Positioning',
							name:   'label_position',
							type:   'listbox',
							values: [
								{text: 'Above', value:'above'},
								{text: 'Left',  value:'left'}
							]
						},{
							html: '<p style="font-style:italic;text-align:right;font-size:85%;">Where the form field label will positioned.</p>',
							type: 'container'
						},{
							label: 'Form Max Width',
							name: 'width',
							type: 'textbox',
							value: '100%'
						},{
							html: '<p style="font-style:italic;text-align:right;font-size:85%;">Use percentage ("%") or Pixels ("px")</p>',
							type: 'container'
						},{
							label: 'Form Alignment',
							name: 'align',
							type: 'listbox',
							values: [
								{text: 'Center',  value:'center'},
								{text: 'Left',    value:'left'},
								{text: 'Right',   value:'right'}
							]
						}],
						onsubmit: function( e ) {
							var name = get_webform_name_by_id(e.data.webform_id);
							editor.insertContent('[fusionform name="'+name+'" id="'+e.data.webform_id+'" align="'+e.data.align+'" label="'+e.data.label_position+'" width="'+e.data.width+'"]');
						}
					});
				}
			}];

			editor.addButton('fusionforms_shortcodes_btn', {
				title: 'Fusion Forms Shortcodes',
				type: 'menubutton',
				image: FusionForms.dir + '/assets/admin/img/ff-icon.png',
				menu: menu
			});
		}
	});

	tinymce.PluginManager.add('fusionforms_shortcodes_btn', tinymce.plugins.fusionforms_shortcodes_btn);
})();