(function ($) {
    "use strict";

    if (!$.wjbWcAdminExtensions)
        $.wjbWcAdminExtensions = {};
    
    function WJBWCAdminMainCore() {
        var self = this;
        self.init();
    };

    WJBWCAdminMainCore.prototype = {
        /**
         *  Initialize
         */
        init: function() {
            var self = this;

            self.mixes();
        },
        mixes: function() {
            var self = this;

            var val_package_type = $('#_property_package_package_type').val();
            self.changePackageTypeFn(val_package_type);
            $('#_property_package_package_type').on('change', function() {
                var val_package_type = $(this).val();
                self.changePackageTypeFn(val_package_type);
            });

            self.productPackageTypeFn();
        },
        changePackageTypeFn: function(val_package_type) {
            if ( val_package_type == 'property_package' ) {
                $('#_property_package_property_package').css({'display': 'block'});
                //
            } else {
                $('#_property_package_property_package').css({'display': 'none'});
            }
        },
        productPackageTypeFn: function() {
            $('._tax_status_field').closest('div').addClass( 'show_if_property_package show_if_property_package_subscription' );
            $('.show_if_subscription, .grouping').addClass( 'show_if_property_package_subscription' );
            $('#product-type').change();

            $('#_property_package_subscription_type').change(function(){
                if ( $(this).val() === 'listing' ) {
                    $('#_properties_duration').closest('.form-field').hide().val('');
                } else {
                    $('#_properties_duration').closest('.form-field').show();
                }
            }).change();
        },
    }

    $.wjbWcAdminMainCore = WJBWCAdminMainCore.prototype;
    
    $(document).ready(function() {
        // Initialize script
        new WJBWCAdminMainCore();
    });
    
})(jQuery);

