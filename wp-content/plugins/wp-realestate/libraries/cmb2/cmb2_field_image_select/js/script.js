(function ($) {
    "use strict";
    
    $('.image-select-wrapper').each(function(){
    	var $this = $(this);
    	$(this).find('.image-select-item label').on('click', function(){
    		$this.find('.image-select-item label').removeClass('selected');
    		$(this).addClass('selected');
	    });
    });
    

})(jQuery);
