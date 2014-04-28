<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * NSM TinyMCE Fieldtype
 *
 * @package			NsmTinyMCE
 * @version			1.2.0
 * @author			Leevi Graham <http://leevigraham.com> - Technical Director, Newism
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://ee-garage.com/nsm-tiny-mce
 * @see				http://expressionengine.com/public_beta/docs/development/fieldtypes.html
 */
class Nsm_tiny_mce_ft extends EE_Fieldtype
{
	/**
	 * Field info - Required
	 * 
	 * @access public
	 * @var array
	 */
	public $info = array(
		'name'		=> 'NSM TinyMCE',
		'version'	=> '1.2.0'
	);

	/**
	 * The field settings array
	 * 
	 * @access public
	 * @var array
	 */
	public $settings = array();

	/**
	 * Path to the TinyMCE config files. Set in the constructor
	 * 
	 * @access private
	 * @var string
	 */
	private $tiny_mce_config_path = "";

	/**
	 * The field type - used for form field prefixes. Must be unique and match the class name. Set in the constructor
	 * 
	 * @access private
	 * @var string
	 */
	private $field_type = '';

	/**
	 * Constructor
	 * 
	 * @access public
	 * 
	 * Calls the parent constructor
	 * Sets the tiny_mce_config_path using the PATH_THRID variable
	 */
	public function __construct()
	{
		parent::__construct();

		$this->tiny_mce_config_path = PATH_THEMES . "third_party/nsm_tiny_mce/scripts/tiny_mce_config/";

		$this->field_type = $this->addon_id = strtolower(substr(__CLASS__, 0, -3));

		if(!isset($this->EE->session->cache[__CLASS__]))
		{
			$this->EE->session->cache[__CLASS__]['loaded_configs'] = array();
		}
	}	

	/**
	 * Replaces the custom field tag
	 * 
	 * @access public
	 * @param $data string Contains the field data (or prepped data, if using pre_process)
	 * @param $params array Contains field parameters (if any)
	 * @param $tagdata mixed Contains data between tag (for tag pairs) FALSE for single tags
	 * @return string The HTML replacing the tag
	 * 
	 */
	public function replace_tag($data, $params = FALSE, $tagdata = FALSE)
	{
		return $data;
	}

	/**
	 * Display the field in the publish form
	 * 
	 * @access public
	 * @param $data String Contains the current field data. Blank for new entries.
	 * @return String The custom field HTML
	 * 
	 * Includes the TinyMCE base script and the field specific configuration.
	 * Returns a standard textarea with a configuration specific class
	 */
	public function display_field($data, $field_id = false)
	{
		$this->_addConfJs($this->settings["conf"]);

		if(!$field_id)
			$field_id = $this->field_name;
		
		$this->EE->cp->add_to_foot('<script type="text/javascript">'
										. 'tinyMCE.settings = NsmTinyMCEConfigs["'.substr($this->settings["conf"], 0, -3).'"];'
										. 'tinyMCE.execCommand("mceAddEditor", true, "'.$field_id.'");'
									. '</script>');
		
		return form_textarea(array(
			'name'	=> $this->field_name,
			'id'	=> $field_id,
			'value'	=> $data,
			'rows' => ' ',
			'style' => "height: {$this->settings['height']}px"
		));
	}

	/**
	 * Displays the cell
	 * 
	 * @access public
	 * @param $data The cell data
	 */
	public function display_cell($data)
	{
		$this->_addConfJs($this->settings["conf"]);

		if(!isset($this->EE->session->cache[__CLASS__]['cell_js_loaded']))
		{
			$theme_url = $this->_getThemeUrl();
			$this->EE->cp->add_to_foot("<script src='{$theme_url}/scripts/matrix2.js' type='text/javascript' charset='utf-8'></script>");
			$this->EE->session->cache[__CLASS__]['cell_js_loaded'] = TRUE;
		}

		$this->EE->cp->add_to_foot('<script type="text/javascript">NsmTinyMCEColConfig["col_id_'.$this->col_id.'"] = "'.substr($this->settings["conf"], 0, -3).'"</script>');

		return form_textarea(array(
			'name'	=> $this->cell_name,
			'id'	=> $this->col_id,
			'value'	=> $data,
			'rows' => ' ',
			'style' => "height: {$this->settings['height']}px"
		));
	}

	/**
	 * Displays the Low Variable field
	 * 
	 * @access public
	 * @param $var_data The variable data
	 * @see http://loweblog.com/software/low-variables/docs/fieldtype-bridge/
	 */
	public function display_var_field($var_data)
	{
		return $this->display_field($var_data, "nsm_tiny_mce_"  . substr($this->field_name, 4, 1));
	}

	/**
	 * Publish form validation
	 * 
	 * @param $data array Contains the submitted field data.
	 * @return mixed TRUE or an error message
	 */
	public function validate($data)
	{
		return TRUE;
	}

	/**
	 * Default field settings
	 * 
	 * @access private
	 * @return The default field settings
	 */
	private function _defaultFieldSettings(){
		return array(
			"conf" => FALSE,
			"height" => 300
		);
	}

	/**
	 * Save the custom field settings
	 * 
	 * @param $data array Not sure what this is yet, probably the submitted post data.
	 * @return boolean Valid or not
	 */
	public function save_settings($field_settings)
	{
		$field_settings = array_merge($this->_defaultFieldSettings(), $this->EE->input->post('nsm_tiny_mce'));

		// Force formatting
		$field_settings['field_fmt'] = 'none';
		$field_settings['field_show_fmt'] = 'n';
		$field_settings['field_type'] = 'nsm_tiny_mce';

		// Cleanup
		unset($_POST['nsm_tiny_mce']);
		foreach (array_keys($field_settings) as $setting)
		{
			if (isset($_POST["nsm_tiny_mce_{$setting}"]))
			{
				unset($_POST["nsm_tiny_mce_{$setting}"]);
			}
		}

		return $field_settings;
	}

	/**
	 * Process the cell settings before saving
	 * 
	 * @access public
	 * @param $col_settings array The settings for the column
	 * @return array The new settings
	 */
	public function save_cell_settings($col_settings)
	{
		$col_settings = $col_settings['nsm_tiny_mce'];
		return $col_settings;
	}

	/**
	 * Save the Low variable settings
	 * 
	 * @access public
	 * @param $var_settings The variable settings
	 * @see http://loweblog.com/software/low-variables/docs/fieldtype-bridge/
	 */
	public function save_var_settings($var_settings)
	{
		return $this->EE->input->post('nsm_tiny_mce');
	}




	/**
	 * Prepares settings array for fields and matrix cells
	 * 
	 * @access public
	 * @param $settings array The field / cell settings
	 * @return array Labels and form inputs
	 */
	private function _fieldSettings($settings)
	{
		$r = array();

		// TinyMCE height
		$r[] = array(
			lang('Height <small>in px</small>', 'nsm_tiny_mce_height'),
			form_input("nsm_tiny_mce[height]", $settings['height'], "id='nsm_tiny_mce_height' class='matrix-textarea'")
		);

		// Configs
		if($configs = $this->_readTinyMCEConfigs())
		{
			foreach ($configs as $key => $value)
			{
				$options[$key] = ucfirst(str_replace(array("_", ".js"), array(""), $key));
			}
			$confs = form_dropdown("nsm_tiny_mce[conf]", $options, $settings['conf'], "id='nsm_tiny_mce_conf'");
		}
		else
		{
			$confs = "<p class='notice'>
							No configuration files could be found. Check that
							<code>".$this->tiny_mce_config_path."</code>
							is readable and contains at least one configuration file.
						</p>";
			$confs .= form_hidden("nsm_tiny_mce[conf]", '');
		}

		$r[] = array(
					lang('Configuration', 'nsm_tiny_mce_conf'),
					$confs
				);
		
		return $r;
	}

	/**
	 * Display the settings form for each custom field
	 * 
	 * @access public
	 * @param $data mixed Not sure what this data is yet :S
	 * @return string Override the field custom settings with custom html
	 * 
	 * In this case we add an extra row to the table. Not sure how the table is built
	 */
	public function display_settings($field_settings)
	{
		$field_settings = array_merge($this->_defaultFieldSettings(), $field_settings);
		$rows = $this->_fieldSettings($field_settings);

		// add the rows
		foreach ($rows as $row)
		{
			$this->EE->table->add_row($row[0], $row[1]);
		}
	}

	/**
	 * Display Cell Settings
	 * 
	 * @access public
	 * @param $cell_settings array The cell settings
	 * @return array Label and form inputs
	 */
	public function display_cell_settings($cell_settings)
	{
		$cell_settings = array_merge($this->_defaultFieldSettings(), $cell_settings);
		return $this->_fieldSettings($cell_settings);
	}

	/**
	 * Display Variable Settings
	 * 
	 * @access public
	 * @param $var_settings array The variable settings
	 * @return array Label and form inputs
	 */
	public function display_var_settings($var_settings)
	{
		$var_settings = array_merge($this->_defaultFieldSettings(), $var_settings);
		return $this->_fieldSettings($var_settings);
	}

	/**
	 * Adds the TinyMCE configuration to the CP
	 */
	private function _addConfJs($conf, $cell = FALSE)
	{
		
		$theme_url = $this->_getThemeUrl();

		if(!isset($this->EE->session->cache[__CLASS__]['tiny_mce_loaded']))
		{
			$this->EE->cp->add_to_head("<link rel='stylesheet' href='{$theme_url}/styles/admin.css' type='text/css' media='screen' charset='utf-8' />");

			$this->EE->cp->add_to_foot("<script src='{$theme_url}/scripts/tinymce/tinymce.min.js' type='text/javascript' charset='utf-8'></script>");
			$this->EE->cp->add_to_foot('<script type="text/javascript">NsmTinyMCEConfigs = {};</script>');
			$this->EE->session->cache[__CLASS__]['tiny_mce_loaded'] = TRUE;
		}

		if(!in_array($conf, $this->EE->session->cache[__CLASS__]['loaded_configs']))
		{
			$this->EE->session->cache[__CLASS__]['loaded_configs'][] = $conf;
			$this->EE->cp->add_to_foot("<script type='text/javascript' src='{$theme_url}/scripts/tiny_mce_config/{$conf}'></script>");
			
		}
	}

	/**
	 * Get the current themes URL from the theme folder + / + the addon id
	 * 
	 * @access private
	 * @return string The theme URL
	 */
	private function _getThemeUrl()
	{
		$EE =& get_instance();
		if(!isset($EE->session->cache[$this->addon_id]['theme_url']))
		{
			$theme_url = $EE->config->item('theme_folder_url');
			if (substr($theme_url, -1) != '/') $theme_url .= '/';
			$theme_url .= "third_party/" . $this->addon_id;
			$EE->session->cache[$this->addon_id]['theme_url'] = $theme_url;
		}
		return $EE->session->cache[$this->addon_id]['theme_url'];
	}

	/**
	 * Reads the custom TinyMCE configs from the directory
	 * 
	 * @access private
	 * @return mixed FALSE if no configs are found or an array of filenames
	 */
	private function _readTinyMCEConfigs()
	{
		// have the configs been processed?
		if(isset($this->EE->session->cache[__CLASS__]['tiny_mce_configs']) === FALSE)
		{
			// assume there are no configs
			$configs = FALSE;
			// if the provided string an actual directory
			if(is_dir($dir = $this->tiny_mce_config_path))
			{
				// open the directory and assign it to a handle
				$dir_handle = opendir($dir);
				// if there is a dir handle
				if($dir_handle)
				{
					/* This is the correct way to loop over the directory. */
		    		// loop over the files
					while (false !== ($file = readdir($dir_handle)))
					{
						// if this is a real file
						if ($file != "." && $file != ".." && $file != "Thumb.db" && substr($file, 0, 1) != '-' && substr($file, -3) == ".js")
						{
							// add the config to the list
							$configs[$file] = file_get_contents($dir.$file);
						}
					}
				}
			}
			// assign the configs to a session var
			$this->EE->session->cache[__CLASS__]['tiny_mce_configs'] = $configs;
		}

		// return the session var
		return $this->EE->session->cache[__CLASS__]['tiny_mce_configs'];
	}

}
//END CLASS
