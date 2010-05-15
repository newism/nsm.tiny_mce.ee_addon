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

		console.log(NsmTinyMCEConfigs);
		console.log(cell.col.id);
		tinyMCE.settings = NsmTinyMCEConfigs[NsmTinyMCEColConfig[cell.col.id]];
		tinyMCE.execCommand("mceAddControl", true, id);
	};

	Matrix.bind('nsm_tiny_mce', 'display', onDisplay);

})(jQuery);
