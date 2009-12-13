<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package			NSM
 * @subpackage		tiny_mce
 * @version			1.0.0a1
 * @author			Leevi Graham <leevi@newism.com.au>
 * @link			http://github.com/newism/nsm.tiny_mce.ee_addon
 * @see				http://expressionengine.com/public_beta/docs/development/fieldtypes.html
*
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
		'version'	=> '1.0.0a1'
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
		parent::EE_Fieldtype();
		$this->tiny_mce_config_path = PATH_THIRD . "nsm_tiny_mce/config/tiny_mce/";
		$this->field_type = strtolower(substr(__CLASS__, 0, -3));
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
	public function display_field($data)
	{
		$field_class = $this->field_type . "-" . preg_replace('#[^a-zA-Z0-9]#', "", substr($this->settings['field_tiny_mce_conf'], 0, -4));

		if(isset($this->EE->session->cache[__CLASS__]['tiny_mce_loaded']) === FALSE)
		{
			$script_url = $this->EE->config->system_url() . "expressionengine/third_party/nsm_tiny_mce/javascript/tiny_mce/tiny_mce.js";
			$this->EE->cp->add_to_head("<script src='".$script_url."' type='text/javascript' charset='utf-8'></script>");
		}

		if(isset($this->EE->session->cache[__CLASS__]['loaded_configs'][$this->settings['field_tiny_mce_conf']]) === FALSE)
		{
			$this->EE->cp->add_to_head(
				$this->EE->session->cache[__CLASS__]['loaded_configs'][$this->settings['field_tiny_mce_conf']] = $this->EE->load->_ci_load(
					array(
						'_ci_path' => $this->tiny_mce_config_path . $this->settings['field_tiny_mce_conf'],
						'_ci_vars' => array('field_class' => $field_class),
						'_ci_return' => TRUE
					)
				)
			);
		}

		return form_textarea(array(
			'name'	=> $this->field_name,
			'id'	=> $this->field_name,
			'value'	=> $data,
			'class' => $field_class
		));

	}

	/**
	 * Display the settings form for each custom field
	 * 
	 * @access public
	 * @param $data mixed Not sure what this data is yet :S
	 * @return string Override the field custom settings with custom html
	 * 
	 * In this case we add an extra row to the table. Not sure how the tabe is built
	 */
	public function display_settings($data)
	{

		$field_rows	= ($data['field_ta_rows'] == '') ? 6 : $data['field_ta_rows'];

		if($configs = $this->_readTinyMCEConfigs())
		{
			foreach ($configs as $key => $value)
			{
				$options[$key] = ucfirst(str_replace(array("_", EXT), array(""), $key));
			}
		}

		// new custom fields
		if(isset($data['field_tiny_mce_conf']) == FALSE)
		{
			$data['field_tiny_mce_conf'] = FALSE;
		}

		$this->EE->table->add_row(
			form_hidden($this->field_type.'_field_fmt', 'none') .
			form_hidden('field_show_fmt', 'n') .
			lang('TinyMCE Configuration', 'field_tiny_mce_conf'),
			($configs) 
				? form_dropdown($this->field_type.'_field_settings[field_tiny_mce_conf]', $options, $data['field_tiny_mce_conf'])
				: "<p class='notice'>No configuration files could be found. Check that <code>".$this->tiny_mce_config_path."</code> is readable and contains at least one configuration file.</p>"
				 . form_hidden($this->field_type.'_field_settings[field_tiny_mce_conf]', '')
		);
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
		return $this->EE->typography->parse_type(
			$this->EE->functions->encode_ee_tags($data),
			array(
				'text_format'	=> $this->row['field_ft_'.$this->field_id],
				'html_format'	=> $this->row['channel_html_formatting'],
				'auto_links'	=> $this->row['channel_auto_link_urls'],
				'allow_img_url' => $this->row['channel_allow_img_urls']
			)
		);
	}

	/**
	 * Save the custom field settings
	 * 
	 * @param $data array Not sure what this is yet, probably the submitted post data.
	 * @return boolean Valid or not
	 */
	public function save_settings($data)
	{
		return $this->EE->input->post('nsm_tiny_mce_field_settings');
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
						if ($file != "." && $file != ".." && $file != "Thumb.db" && substr($file, 0, 1) != '-' && substr($file, -4) == EXT)
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