# NSM TinyMCE 1.0.0a1

Written by: Leevi Graham, Technical Director of Newism, based on LG TinyMCE (EE1.x)

NSM TinyMCE is an ExpressionEngine 2 custom field that converts standard text areas to [TinyMCE (Moxicode)](http://tinymce.moxiecode.com/) WYSIWYG editors.

## Notes

This custom field has only been tested on EE2.0 Beta 1 Build 20091207 and requires some very minor core hacking to enable custom field custom settings. Build 20091211 should be ok but the code changes might not be in the exactly the same place. See the installation instructions.

TinyMCE version 3.2.7 is included with the addon. Please review the [TinyMCE license](http://tinymce.moxiecode.com/license.php).

### Config files

TinyMCE config files are located in `/system/expressionengine/third_party/nsm_tiny_mce/config/tiny_mce` and are loaded as CI Views into the footer of the CP. One variable is passed through `$field_class` which should be used to uniquely identify fields with different configurations like so:

	<script language="javascript" type="text/javascript">
		tinyMCE.init({
			button_tile_map : true,
			editor_selector : '<?= $field_class ?>',
			mode:'textareas',
			theme : 'advanced',
			height : 300,
			width : "99%",
		});
	</script>

**Configuration files must contain the opening and closing script tags.**

## Features

* Multiple configurations
* Per field configuration

## Screenshots

[![NSM TinyMCE custom field settings](http://s3.amazonaws.com/ember/T8QlIz969laR9TZNHFcAhSbxAxCXwOAV_s.jpg "NSM TinyMCE custom field settings")](http://emberapp.com/leevigraham/images/nsm-tinymce-custom-field-settings-1/sizes/l)
[![NSM TinyMCE publish form](http://s3.amazonaws.com/ember/Lx7NaGhbP2nn2kJoB4RMzEUUjuGVMgpq_s.jpg "NSM TinyMCE publish form")](http://emberapp.com/leevigraham/images/nsm-tinymce-ee2-custom-field/sizes/l)

## Installation

1. Copy `/system/expressionengine/third_party/nsm_tiny_mce` to `/your_system_folder/expressionengine/third_party/nsm_tiny_mce`

### Core hacks for build 20091207

1. Add a new table col titled `field_settings` to `exp_extensions` and set the data type to 'text'
2. In <code>/system/expressionengine/controllers/cp/admin_content.php</code>:
	- Line 4514: Uncomment `$native_settings['field_settings'] = base64_encode(serialize($ft_settings));`
	- Line 4464: Add `'field_settings'` to the `$native` array variable.

## Todo

1. Add PHP Compressor support
2. Double check config files
3. Add NSM Addon Updater integration - possibly requires extension

## Known issues

1. PHP error on Add/Edit custom field page:  
	<pre><code>A PHP Error was encountered
Severity: Notice
Message: Undefined variable: field_type
Filename: cp/admin_content.php
Line Number: 3898</code></pre>
2. There is some craziness when you click the _view source_ toolbar button with mutiple fields in the same publish form.