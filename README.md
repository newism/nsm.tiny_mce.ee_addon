# NSM TinyMCE 1.0.0a1

Written by: Leevi Graham ([Twitter](http://twitter.com/leevigraham) | [Website](http://leevigraham.com)), Technical Director of [Newism](http://newism.com.au), based on [LG TinyMCE](http://leevigraham.com/cms-customisation/expressionengine/lg-tinymce/) (EE1.x)

NSM TinyMCE is an [ExpressionEngine 2](http://expressionengine.com/index.php?affiliate=newism&amp;page=public_beta/index) custom field that converts standard text areas to [TinyMCE (Moxicode)](http://tinymce.moxiecode.com/) WYSIWYG editor.

## Notes

This custom field has only been tested on EE2.0.2

TinyMCE version 3.3.5.1 is included with the addon. Please review the [TinyMCE license](http://tinymce.moxiecode.com/license.php).

### Config files

TinyMCE config files are located in `/system/expressionengine/third_party/nsm_tiny_mce/javascript/tiny_mce_config`.

Configuration example:

	<script language="javascript" type="text/javascript">
		NsmTinyMCEConfigs.basic = {
			button_tile_map : true,
			mode: none, // Must be none.
			theme : 'advanced',
			width : "99%",
		};
	</script>

Configuration files are just a json object of TinyMCE configuration options that extends the `NsmTinyMCEConfigs` global object. 

The key must be named the same as the actual file name. ie. If the configuration file is `simple.js` the `NsmTinyMCEConfigs` key will be `simple`.

** Important **: The `mode` key must be set to none, not `textareas` or exact.

## Features

* Multiple configurations
* Per field configuration
* Matrix integration

## Screenshots

[![NSM TinyMCE custom field settings](http://s3.amazonaws.com/ember/T8QlIz969laR9TZNHFcAhSbxAxCXwOAV_s.jpg "NSM TinyMCE custom field settings")](http://emberapp.com/leevigraham/images/nsm-tinymce-custom-field-settings-1/sizes/l)
[![NSM TinyMCE publish form](http://s3.amazonaws.com/ember/Lx7NaGhbP2nn2kJoB4RMzEUUjuGVMgpq_s.jpg "NSM TinyMCE publish form")](http://emberapp.com/leevigraham/images/nsm-tinymce-ee2-custom-field/sizes/l)

## Installation

1. Copy `/system/expressionengine/third_party/nsm_tiny_mce` to `/your_system_folder/expressionengine/third_party/nsm_tiny_mce`

## Todo

1. Add PHP Compressor support
2. Fix Matrix cell settings bug
3. Add NSM Addon Updater integration