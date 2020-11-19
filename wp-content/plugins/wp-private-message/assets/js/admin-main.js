(function ($) {
    "use strict";

    if (!$.wjbAdminExtensions)
        $.wjbAdminExtensions = {};
    
    function WJBAdminMainCore() {
        var self = this;
        self.init();
    };

    WJBAdminMainCore.prototype = {
        /**
         *  Initialize
         */
        init: function() {
            var self = this;
        },
        
    }

    $.wjbAdminMainCore = WJBAdminMainCore.prototype;
    
    $(document).ready(function() {
        // Initialize script
        new WJBAdminMainCore();
    });
    
})(jQuery);

