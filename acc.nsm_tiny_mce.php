<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * NSM TinyMCE Accessory
 *
 * @package			NsmTinyMCE
 * @version			1.2.0
 * @author			Leevi Graham <http://leevigraham.com> - Technical Director, Newism
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://ee-garage.com/nsm-example-addon
 * @see				http://expressionengine.com/public_beta/docs/development/accessories.html
 */

class Nsm_tiny_mce_acc 
{
	var $id;
	var $version		= '1.2.0';
	var $name			= 'NSM TinyMCE';
	var $description	= 'Update catgeory description and member fields to TinyMCE';
	var $sections		= array();

	function set_sections()
	{
		$this->EE =& get_instance();
		$this->id = strtolower(__CLASS__);
		$this->addon_id = substr(strtolower(__CLASS__),0,-4);

		$this->tiny_mce_config_path = PATH_THEMES . "third_party/nsm_tiny_mce/scripts/tiny_mce_config/";

		if(!isset($this->EE->session->cache[__CLASS__]))
			$this->EE->session->cache[__CLASS__]['loaded_configs'] = array();

		$theme_url = $this->_getThemeUrl();
		$this->EE->cp->add_to_head("<link rel='stylesheet' href='{$theme_url}/styles/admin.css' type='text/css' media='screen' charset='utf-8' />");


		if($this->EE->input->get('M') == 'category_edit' 
			&& $catgegory_config = $this->EE->config->item('category_description_conf', 'nsm_tiny_mce'))
		{
			$this->_addConfJs($catgegory_config);
			$this->EE->cp->add_to_foot('<script type="text/javascript">'
											. 'tinyMCE.settings = NsmTinyMCEConfigs["'.$catgegory_config.'"];'
											. 'tinyMCE.execCommand("mceAddEditor", true, "cat_description");'
										. '</script>');
		}
		
		if($this->EE->input->get('M') == 'edit_profile' 
			&& $member_config = $this->EE->config->item('member_field_conf', 'nsm_tiny_mce'))
		{
			foreach ($member_config as $member_field => $conf)
			{
				$this->_addConfJs($conf);
				$this->EE->cp->add_to_foot('<script type="text/javascript">'
												. 'tinyMCE.settings = NsmTinyMCEConfigs["'.$conf.'"];'
												. 'tinyMCE.execCommand("mceAddEditor", true, "'.$member_field.'");'
											. '</script>');
			}
			
		}

		$this->sections[] = '<script type="text/javascript" charset="utf-8">$("#accessoryTabs a.nsm_tiny_mce_acc").parent().remove();</script>';
	}

	/**
	 * Adds the TinyMCE configuration to the CP
	 */
	private function _addConfJs($conf, $cell = FALSE)
	{
		
		$script_url = $this->_getThemeUrl() . "/scripts/";

		if(!isset($this->EE->session->cache[__CLASS__]['tiny_mce_loaded']))
		{
			$this->EE->cp->add_to_foot("<script src='{$script_url}tinymce/tinymce.min.js' type='text/javascript' charset='utf-8'></script>");
			$this->EE->cp->add_to_foot('<script type="text/javascript">NsmTinyMCEConfigs = {};</script>');
			$this->EE->session->cache[__CLASS__]['tiny_mce_loaded'] = TRUE;
		}

		if(!in_array($conf, $this->EE->session->cache[__CLASS__]['loaded_configs']))
		{
			$this->EE->session->cache[__CLASS__]['loaded_configs'][] = $conf;
			$this->EE->cp->add_to_foot("<script type='text/javascript' src='{$script_url}tiny_mce_config/{$conf}'></script>");
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
	
}
