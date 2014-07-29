/**
 * Matrix integration for NSM TinyMCE
 *
 * @package			NSMTinyMCE
 * @version			1.2.0
 * @author			Leevi Graham <http://leevigraham.com> - Technical Director, Newism
 * @link			http://github.com/newism/nsm.tiny_mce.ee_addon
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 */
(function($) {

	NsmTinyMCEColConfig = {};

	/**
	 * Display
	 */
	var onDisplay = function(cell){

		var $textarea = $('textarea', cell.dom.$td),
			config = NsmTinyMCEConfigs[cell.col.id],
			id = cell.field.id+'_'+cell.row.id+'_'+cell.col.id+'_'+Math.floor(Math.random()*100000000);

		$textarea.attr('id', id);

		// console.log(NsmTinyMCEConfigs);
		// console.log(cell.col.id);
		tinyMCE.settings = NsmTinyMCEConfigs[NsmTinyMCEColConfig[cell.col.id]];
		tinyMCE.execCommand("mceAddEditor", true, id);
	};

	Matrix.bind('nsm_tiny_mce', 'display', onDisplay);

})(jQuery);
