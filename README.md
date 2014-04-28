# NSM TinyMCE

Written by: Leevi Graham ([Twitter](http://twitter.com/leevigraham) | [Website](http://leevigraham.com)), Technical Director of [Newism](http://newism.com.au), based on [LG TinyMCE](http://leevigraham.com/cms-customisation/expressionengine/lg-tinymce/) (EE1.x)

NSM TinyMCE is an [ExpressionEngine 2](http://expressionengine.com/index.php?affiliate=newism&amp;page=public_beta/index) custom field that converts standard text areas to [TinyMCE (Moxicode)](http://tinymce.moxiecode.com/) WYSIWYG editor.

## Features

* Multiple configurations
* Per field configuration
* Matrix integration

## Screenshots

[![NSM TinyMCE custom field settings](http://s3.amazonaws.com/ember/T8QlIz969laR9TZNHFcAhSbxAxCXwOAV_s.jpg "NSM TinyMCE custom field settings")](http://emberapp.com/leevigraham/images/nsm-tinymce-custom-field-settings-1/sizes/l)
[![NSM TinyMCE custom field settings](http://s3.amazonaws.com/ember/hSHTScIKuQag5QGP4xMOL3cpFKmVXCJ4_s.jpg "NSM TinyMCE custom field settings")](http://emberapp.com/leevigraham/images/nms-tinymce-matrix/sizes/o)

## Installation

### Download

1. Download the master branch
2. Rename the downloaded folder to `nsm_tiny_mce`
3. Move the folder to `system/expressionengine/third_party`
4. Move `themes/third_party/nsm_tiny_mce` from the downloaded files to `themes/third_party` in your EE install.

## Config files

TinyMCE config files are located in `/system/expressionengine/third_party/nsm_tiny_mce/javascript/tiny_mce_config`.

Configuration example:

	<script language="javascript" type="text/javascript">
		NsmTinyMCEConfigs.basic = {
			button_tile_map : true,
			mode: 'none', // Must be none.
			theme : 'advanced',
			width : "99%",
		};
	</script>

Configuration files are just a json object of TinyMCE configuration options that extends the `NsmTinyMCEConfigs` global object. 

The key must be named the same as the actual file name. ie. If the configuration file is `simple.js` the `NsmTinyMCEConfigs` key will be `simple`.

** Important **: The `mode` key must be set to none, not `textareas` or exact.

## Notes

This custom field has been tested on EE 2.8.0

TinyMCE version 4.0.12 is included with the addon. Please review the [TinyMCE license](http://tinymce.moxiecode.com/license.php).

## Todo

1. Add PHP Compressor support
2. Add NSM Addon Updater integration
3. Fix Matrix cell settings bug
