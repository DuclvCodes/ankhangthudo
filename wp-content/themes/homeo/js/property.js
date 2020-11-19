(function($) {
    "use strict";
    
    $.extend($.apusThemeCore, {
        /**
         *  Initialize scripts
         */
        property_init: function() {
            var self = this;

            self.select2Init();

            self.searchAjaxInit();
            
            self.mortgageCalculator();

            self.submitProperty();

            self.listingDetail();

            self.filterListingFnc();

            self.userLoginRegister();

            self.listingBtnFilter();

            self.changePaddingTopContent();

            $(window).resize(function(){
                setTimeout(function(){
                    self.changePaddingTopContent();
                }, 50);
            });

            self.agentAgencyLoadMore();

            self.propertyCompare();

            if ( $('.properties-listing-wrapper.main-items-wrapper, .agents-listing-wrapper.main-items-wrapper, .agencies-listing-wrapper.main-items-wrapper').length ) {
                $(document).on('change', 'form.filter-listing-form input, form.filter-listing-form select', function (e) {
                    var form = $(this).closest('form.filter-listing-form');
                    setTimeout(function(){
                        form.trigger('submit');
                    }, 200);
                });

                $(document).on('submit', 'form.filter-listing-form', function (e) {
                    e.preventDefault();
                    var url = $(this).attr('action');

                    var formData = $(this).find(":input").filter(function(index, element) {
                            return $(element).val() != '';
                        }).serialize();

                    if( url.indexOf('?') != -1 ) {
                        url = url + '&' + formData;
                    } else{
                        url = url + '?' + formData;
                    }
                    
                    self.propertiesGetPage( url );
                    return false;
                });

                // Sort Action
                $(document).on('change', 'form.properties-ordering select.orderby', function(e) {
                    e.preventDefault();
                    $('form.properties-ordering').trigger('submit');
                });
                
                $(document).on('submit', 'form.properties-ordering', function (e) {
                    var url = $(this).attr('action');

                    var formData = $(this).find(":input").filter(function(index, element) {
                            return $(element).val() != '';
                        }).serialize();
                    
                    if( url.indexOf('?') != -1 ) {
                        url = url + '&' + formData;
                    } else{
                        url = url + '?' + formData;
                    }
                    self.propertiesGetPage( url );
                    return false;
                });

                // display mode
                $(document).on('change', 'form.properties-display-mode input', function(e) {
                    e.preventDefault();
                    $('form.properties-display-mode').trigger('submit');
                });

                $(document).on('submit', 'form.properties-display-mode', function (e) {
                    var url = $(this).attr('action');

                    if( url.indexOf('?') != -1 ) {
                        url = url + '&' + $(this).serialize();
                    } else{
                        url = url + '?' + $(this).serialize();
                    }
                    self.propertiesGetPage( url );
                    return false;
                });
            }

            $(document).on('click', '.advance-search-btn', function(e) {
                e.preventDefault();
                $(this).closest('.search-form-inner').find('.advance-search-wrapper-fields').slideToggle();
            });
            // ajax pagination
            if ( $('.ajax-pagination').length ) {
                self.ajaxPaginationLoad();
            }

            if ( $('.page-template-page-dashboard .sidebar-wrapper:not(.offcanvas-filter-sidebar) > .sidebar-right, .page-template-page-dashboard .sidebar-wrapper:not(.offcanvas-filter-sidebar) > .sidebar-left').length ) {
                var ps = new PerfectScrollbar('.page-template-page-dashboard .sidebar-wrapper:not(.offcanvas-filter-sidebar) > .sidebar-right, .page-template-page-dashboard .sidebar-wrapper:not(.offcanvas-filter-sidebar) > .sidebar-left', {
                    wheelPropagation: true
                });
            }
            // filter fixed
            if ( $('.properties-filter-sidebar-wrapper .inner').length ) {
                var ps = new PerfectScrollbar('.properties-filter-sidebar-wrapper .inner', {
                    wheelPropagation: true
                });
            }
            self.galleryPropery();

            $('.btn-send-mail').on('click', function(){
                var target = $('form.contact-form-wrapper');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                    return false;
                }
                return false;
            });
        },
        select2Init: function() {
            // select2
            if ( $.isFunction( $.fn.select2 ) && typeof wp_realestate_select2_opts !== 'undefined' ) {
                var select2_args = wp_realestate_select2_opts;
                select2_args['allowClear']              = true;
                select2_args['minimumResultsForSearch'] = 10;
                
                if ( typeof wp_realestate_select2_opts.language_result !== 'undefined' ) {
                    select2_args['language'] = {
                        noResults: function(){
                            return wp_realestate_select2_opts.language_result;
                        }
                    };
                }

                // fix for widget search
                if( $('.widget-property-search-form.horizontal select').length ){
                    select2_args.theme = 'default customizer-search';
                }
                 
                if ( $('.layout-type-half-map .widget-property-search-form.horizontal select').length ){
                    select2_args.theme = 'default customizer-search customizer-search-halpmap';
                }
                
                $('.filter-listing-form select').select2( select2_args );

                select2_args['allowClear'] = false;
                
                // filter
                
                $('select[name=email_frequency]').select2( select2_args );
                $('.register-form select').select2( select2_args );
            }
        },
        searchAjaxInit: function() {
            if ( $.isFunction( $.fn.typeahead ) ) {
                $('.apus-autocompleate-input').each(function(){
                    var $this = $(this);
                    $this.typeahead({
                            'hint': true,
                            'highlight': true,
                            'minLength': 2,
                            'limit': 10
                        }, {
                            name: 'search',
                            source: function (query, processSync, processAsync) {
                                processSync([homeo_property_opts.empty_msg]);
                                $this.closest('.twitter-typeahead').addClass('loading');

                                var values = {};
                                $.each($this.closest('form').serializeArray(), function (i, field) {
                                    values[field.name] = field.value;
                                });

                                var ajaxurl = homeo_property_opts.ajaxurl;
                                if ( typeof wp_realestate_opts.ajaxurl_endpoint !== 'undefined' ) {
                                    var ajaxurl =  wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'homeo_autocomplete_search_properties' );
                                }

                                return $.ajax({
                                    url: ajaxurl,
                                    type: 'GET',
                                    data: {
                                        'search': query,
                                        'action': 'homeo_autocomplete_search_properties',
                                        'data': values
                                    },
                                    dataType: 'json',
                                    success: function (json) {
                                        $this.closest('.twitter-typeahead').removeClass('loading');
                                        $this.closest('.has-suggestion').removeClass('active');
                                        return processAsync(json);
                                    }
                                });
                            },
                            templates: {
                                empty : [
                                    '<div class="empty-message">',
                                    homeo_property_opts.empty_msg,
                                    '</div>'
                                ].join('\n'),
                                suggestion: Handlebars.compile( homeo_property_opts.template )
                            },
                        }
                    );
                    $this.on('typeahead:selected', function (e, data) {
                        e.preventDefault();
                        setTimeout(function(){
                            $('.apus-autocompleate-input').val(data.title);    
                        }, 5);
                        
                        return false;
                    });
                });
            }
        },
        mortgageCalculator: function() {
            $('#btn_mortgage_get_results').on('click', function (e) {
                e.preventDefault();

                var property_price = $('#apus_mortgage_property_price').val();
                var deposit = $('#apus_mortgage_deposit').val();
                var interest_rate = parseFloat($('#apus_mortgage_interest_rate').val(), 10) / 100;
                var years = parseInt($('#apus_mortgage_term_years').val(), 10);
                

                var interest_rate_month = interest_rate / 12;
                var nbp_month = years * 12;

                var loan_amount = property_price - deposit;
                var monthly_payment = parseFloat((loan_amount * interest_rate_month) / (1 - Math.pow(1 + interest_rate_month, -nbp_month))).toFixed(2);

                if (monthly_payment === 'NaN') {
                    monthly_payment = 0;
                }
                
                $('.apus_mortgage_results').html( homeo_property_opts.monthly_text + homeo_property_opts.currency + monthly_payment);

            });
        },
        submitProperty: function() {
            $(document).on('click', 'ul.submit-property-heading li a', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                if ( $(href).length ) {
                    $('ul.submit-property-heading li').removeClass('active');
                    $(this).closest('li').addClass('active');
                    $('.before-group-row').removeClass('active');
                    $(href).addClass('active');

                    $( "input" ).trigger( "pxg:simplerefreshmap" );
                }
            });

            $(document).on('click', '.job-submission-previous-btn, .job-submission-next-btn', function(e) {
                e.preventDefault();
                var index = $(this).data('index');
                if ( $('.before-group-row-'+index).length ) {
                    $('.before-group-row').removeClass('active');
                    $('.before-group-row-'+index).addClass('active');

                    $('.submit-property-heading li').removeClass('active');
                    $('.submit-property-heading-'+index).addClass('active');

                    $( "input" ).trigger( "pxg:simplerefreshmap" );
                }
            });
        },
        changePaddingTopContent: function() {
            if ($(window).width() >= 1200) {
                var header_h = $('#apus-header').outerHeight();
                $('#apus-main-content').css({ 'padding-top': 0 });
                $('body.page-template-page-dashboard #apus-main-content').css({ 'padding-top': header_h });
            } else {
                var header_h = $('#apus-header-mobile').outerHeight();
                $('#apus-main-content').css({ 'padding-top': header_h });
            }
            
            if ($('#properties-google-maps').is('.fix-map')) {
                $('#properties-google-maps').css({ 'top': header_h, 'height': 'calc(100vh - ' + header_h+ 'px)' });
                // filter for half 3
                if ($(window).width() >= 1200) {
                    $('.layout-type-half-map-v3 .properties-filter-sidebar-wrapper ').css({ 'top': header_h, 'height': 'calc(100vh - ' + header_h+ 'px)' });
                    $('#apus-main-content').css({ 'padding-top': header_h });
                }
            }
            // fix for half map
            $('.layout-type-half-map .filter-sidebar').css({ 'padding-top': header_h + 30 });
            // $('.layout-type-half-map .filter-scroll').perfectScrollbar();
            if ( $('.layout-type-half-map .filter-scroll').length ) {
                var ps = new PerfectScrollbar('.layout-type-half-map .filter-scroll', {
                    wheelPropagation: true
                });
            }
            // offcanvas-filter-sidebar 
            $('.offcanvas-filter-sidebar').css({ 'padding-top': header_h + 10 });
        },
        agentAgencyLoadMore: function() {
            $(document).on('click', '.ajax-properties-pagination .apus-loadmore-btn', function(e){
                e.preventDefault();
                var $this = $(this);
                
                $this.addClass('loading');

                var ajaxurl = homeo_property_opts.ajaxurl;
                if ( typeof wp_realestate_opts.ajaxurl_endpoint !== 'undefined' ) {
                    var ajaxurl =  wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'homeo_get_ajax_properties_load_more' );
                }

                $.ajax({
                    url: ajaxurl,
                    type:'POST',
                    dataType: 'json',
                    data: {
                        action: 'homeo_get_ajax_properties_load_more',
                        paged: $this.data('paged'),
                        post_id: $this.data('post_id'),
                        type: $this.data('type'),
                    }
                }).done(function(data) {
                    $this.removeClass('loading');
                    $this.closest('.agent-agency-detail-properties').find('.row').append(data.output);
                    $this.data('paged', data.paged);
                    if ( !data.load_more ) {
                        $this.closest('.ajax-properties-pagination').addClass('all-properties-loaded');
                    }
                });
            });

            $(document).on('click', '.ajax-agents-pagination .apus-loadmore-btn', function(e){
                e.preventDefault();
                var $this = $(this);
                    
                $this.addClass('loading');

                var ajaxurl = homeo_property_opts.ajaxurl;
                if ( typeof wp_realestate_opts.ajaxurl_endpoint !== 'undefined' ) {
                    var ajaxurl =  wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'homeo_get_ajax_agents_load_more' );
                }

                $.ajax({
                    url: ajaxurl,
                    type:'POST',
                    dataType: 'json',
                    data: {
                        action: 'homeo_get_ajax_agents_load_more',
                        paged: $this.data('paged'),
                        post_id: $this.data('post_id'),
                    }
                }).done(function(data) {
                    $this.removeClass('loading');
                    $this.closest('.agent-detail-agents').find('.row').append(data.output);
                    $this.data('paged', data.paged);
                    if ( !data.load_more ) {
                        $this.closest('.ajax-agents-pagination').addClass('all-properties-loaded');
                    }
                });
            });
        },
        propertyChangeMarginTopAffix: function() {
            var affix_height = 0;
            //if ($(window).width() > 991) {
                if ( $('.panel-affix').length > 0 ) {
                    affix_height = $('.panel-affix').outerHeight();
                    $('.panel-affix-wrapper').css({'height': affix_height});
                }
            //}
            return affix_height;
        },
        listingDetail: function() {
            var self = this;
            
            // sticky tabs
            var affix_height = 0;
            var affix_height_top = 0;
            setTimeout(function(){
                affix_height = affix_height_top = self.propertyChangeMarginTopAffix();
            }, 50);
            $(window).resize(function(){
                affix_height = affix_height_top = self.propertyChangeMarginTopAffix();
            });
            if ($(window).width() >= 1200) {
                //Function from Bluthemes, lets you add li elemants to affix object without having to alter and data attributes set out by bootstrap
                setTimeout(function(){
                    // name your elements here
                    var stickyElement   = '.panel-affix',   // the element you want to make sticky
                        bottomElement   = '#apus-footer'; // the bottom element where you want the sticky element to stop (usually the footer) 

                    // make sure the element exists on the page before trying to initalize
                    if($( stickyElement ).length){
                        $( stickyElement ).each(function(){
                            var header_height = 0;
                            if ($(window).width() >= 1200) {
                                if ($('.main-sticky-header').length > 0) {
                                    header_height = $('.main-sticky-header').outerHeight();
                                    affix_height_top = affix_height + header_height;
                                }
                            } else {
                                header_height = $('#apus-header-mobile').outerHeight();
                                affix_height_top = affix_height + header_height;
                                header_height = 0;
                            }
                            affix_height_top = affix_height_top + 10;
                            // let's save some messy code in clean variables
                            // when should we start affixing? (the amount of pixels to the top from the element)
                            var fromTop = $( this ).offset().top, 
                                // where is the bottom of the element?
                                fromBottom = $( document ).height()-($( this ).offset().top + $( this ).outerHeight()),
                                // where should we stop? (the amount of pixels from the top where the bottom element is)
                                // also add the outer height mismatch to the height of the element to account for padding and borders
                                stopOn = $( document ).height()-( $( bottomElement ).offset().top)+($( this ).outerHeight() - $( this ).height()); 
                
                            // if the element doesn't need to get sticky, then skip it so it won't mess up your layout
                            if( (fromBottom-stopOn) > 200 ){
                                // let's put a sticky width on the element and assign it to the top
                                $( this ).css('width', $( this ).width()).css('top', 0).css('position', '');
                                // assign the affix to the element
                                $( this ).affix({
                                    offset: { 
                                        // make it stick where the top pixel of the element is
                                        top: fromTop - header_height,  
                                        // make it stop where the top pixel of the bottom element is
                                        bottom: stopOn
                                    }
                                // when the affix get's called then make sure the position is the default (fixed) and it's at the top
                                }).on('affix.bs.affix', function(){
                                    var header_height = 0;
                                    if ($(window).width() >= 1200) {
                                        if ($('.main-sticky-header').length > 0) {
                                            header_height = $('.main-sticky-header').outerHeight();
                                            affix_height_top = affix_height + header_height;
                                        }
                                    } else {
                                        header_height = $('#apus-header-mobile').outerHeight();
                                        affix_height_top = affix_height + header_height;
                                        header_height = 0;
                                    }
                                    affix_height_top = affix_height_top + 10;
                                    $( this ).css('top', header_height).css('position', header_height);
                                });
                            }
                            // trigger the scroll event so it always activates 
                            $( window ).trigger('scroll'); 
                        }); 
                    }

                    //Offset scrollspy height to highlight li elements at good window height
                    $('body').scrollspy({
                        target: ".header-tabs-wrapper",
                        offset: affix_height_top + 20
                    });
                }, 50);
            }
            

            //Smooth Scrolling For Internal Page Links
            $('.panel-affix a[href*="#"]:not([href="#"])').on('click', function() {
                if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                    if (target.length) {
                        $('html,body').animate({
                            scrollTop: target.offset().top - affix_height_top
                        }, 1000);
                        return false;
                    }
                }
            });



            $(document).on('click', '.add-a-review', function(e) {
                e.preventDefault();
                var $id = $(this).attr('href');
                if ( $($id).length > 0 ) {
                    $('html,body').animate({
                        scrollTop: $($id).offset().top - 100
                    }, 1000);
                }
            });

            $(document).on('click', '.btn-view-all-photos', function(e){
                $(this).closest('.property-detail-gallery').find('a:first').trigger('click');
            });

            $('.btn-print-property').on('click', function(e){
                e.preventDefault();
                
                var $this = $(this);
                $this.addClass('loading');
                var property_id = $(this).data('property_id');
                var nonce = $(this).data('nonce');

                var ajaxurl = homeo_property_opts.ajaxurl;
                if ( typeof wp_realestate_opts.ajaxurl_endpoint !== 'undefined' ) {
                    var ajaxurl =  wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'homeo_ajax_print_property' );
                }

                var printWindow = window.open('', 'Print Me', 'width=700 ,height=842');
                $.ajax({
                    url: ajaxurl,
                    type:'POST',
                    dataType: 'html',
                    data: {
                        'property_id': property_id,
                        'nonce': nonce,
                        'action': 'homeo_ajax_print_property',
                    }
                }).done(function(data) {
                    $this.removeClass('loading');
                    
                    
                    printWindow.document.write(data);
                    printWindow.document.close();
                    printWindow.focus();

                    // setTimeout(function(){
                    //     printWindow.print();
                    // }, 2000);
                    // printWindow.close();

                });

            });
        },
        listingBtnFilter: function(){
            $('.btn-view-map').on('click', function(e){
                e.preventDefault();
                $('#properties-google-maps').removeClass('hidden-sm').removeClass('hidden-xs');
                $('.content-listing .properties-listing-wrapper, .content-listing .agencies-listing-wrapper').addClass('hidden-sm').addClass('hidden-xs');
                $('.btn-view-listing').removeClass('hidden-sm').removeClass('hidden-xs');
                $(this).addClass('hidden-sm').addClass('hidden-xs');
                $('.properties-pagination-wrapper, .agencies-pagination-wrapper').addClass('p-fix-pagination');
                setTimeout(function() {
                    $(window).trigger('pxg:refreshmap');
                }, 100);
            });
            $('.btn-view-listing').on('click', function(e){
                e.preventDefault();
                $('#properties-google-maps').addClass('hidden-sm').addClass('hidden-xs');
                $('.content-listing .properties-listing-wrapper, .content-listing .agencies-listing-wrapper').removeClass('hidden-sm').removeClass('hidden-xs');
                $('.btn-view-map').removeClass('hidden-sm').removeClass('hidden-xs');
                $(this).addClass('hidden-sm').addClass('hidden-xs');
                $('.properties-pagination-wrapper, .agencies-pagination-wrapper').removeClass('p-fix-pagination');
            });

            $('.show-filter-properties, .filter-in-sidebar').on('click', function(e){
                e.stopPropagation();
                $('.layout-type-half-map .filter-sidebar').toggleClass('active');
                $('.filter-sidebar + .over-dark').toggleClass('active');
            });
            
            $(document).on('click', '.filter-sidebar + .over-dark', function(){
                $('.layout-type-half-map .filter-sidebar').removeClass('active');
                $('.filter-sidebar + .over-dark').removeClass('active');
            });

            // filter sidebar fixed
            $(document).on('click', '.properties-filter-sidebar-wrapper .close-filter, .btn-show-filter, .properties-filter-sidebar-wrapper + .over-dark-filter', function(){
                $('.properties-filter-sidebar-wrapper').toggleClass('active');
            });
        },
        userLoginRegister: function(){
            var self = this;
            // login/register
            $('.user-login-form').on('click', function(e){
                e.preventDefault();
                if ( $('.apus-user-login').length ) {
                    $('.apus-user-login').trigger('click');
                }
            });
            $('.apus-user-login, .apus-user-register').magnificPopup({
                mainClass: 'apus-mfp-zoom-in login-popup',
                type:'inline',
                midClick: true,
                callbacks: {
                    open: function() {
                        self.layzyLoadImage();
                    }
                }
            });
              
        },

        filterListingFnc: function(){

            $('body').on('click', '.btn-show-filter, .offcanvas-filter-sidebar + .over-dark', function(){
                $('.offcanvas-filter-sidebar, .offcanvas-filter-sidebar + .over-dark').toggleClass('active');
                // $('.offcanvas-filter-sidebar').perfectScrollbar();
                if ( $('.offcanvas-filter-sidebar').length ) {
                    var ps = new PerfectScrollbar('.offcanvas-filter-sidebar', {
                        wheelPropagation: true
                    });
                }
                
            });

            $(document).on('after_add_property_favorite', function(e, $this, data) {
                $this.attr('data-original-title', homeo_property_opts.favorite_added_tooltip_title);
            });
            $(document).on('after_remove_property_favorite', function( event, $this, data ) {
                $this.attr('data-original-title', homeo_property_opts.favorite_add_tooltip_title);
            });

        },
        propertyCompare: function(){
            var self = this;
            if ( $('.compare-sidebar-inner .compare-list').length ) {
                var ps = new PerfectScrollbar('.compare-sidebar-inner .compare-list', {
                    wheelPropagation: true
                });
            }

            $(document).on('after_add_property_compare', function(e, $this, data) {
                var html_output = '';
                if ( data.html_output ) {
                    html_output = data.html_output;
                }
                $('#compare-sidebar .compare-sidebar-inner').html(html_output);
                $('.compare-sidebar-btn .count').html(data.count);

                if ( $('.compare-sidebar-inner .compare-list').length ) {
                    var ps = new PerfectScrollbar('.compare-sidebar-inner .compare-list', {
                        wheelPropagation: true
                    });
                }

                self.layzyLoadImage();

                if ( !$('#compare-sidebar').hasClass('active') ) {
                    $('#compare-sidebar').addClass('active');
                }
                if ( !$('#compare-sidebar').hasClass('open') ) {
                    $('#compare-sidebar').addClass('open');
                }
                $this.attr('data-original-title', homeo_property_opts.compare_added_tooltip_title);
            });
            
            $(document).on('after_remove_property_compare', function( event, $this, data ) {
                var html_output = '';
                if ( data.html_output ) {
                    html_output = data.html_output;
                }
                $('#compare-sidebar .compare-sidebar-inner').html(html_output);
                $('.compare-sidebar-btn .count').html(data.count);
                
                if ( $('.compare-sidebar-inner .compare-list').length ) {
                    var ps = new PerfectScrollbar('.compare-sidebar-inner .compare-list', {
                        wheelPropagation: true
                    });
                }

                if ( data.count == '0' ) {
                    $('#compare-sidebar').removeClass('active');
                }

                $this.attr('data-original-title', homeo_property_opts.compare_add_tooltip_title);

                self.layzyLoadImage();
            });

            $(document).on('click', '.btn-remove-property-compare-list', function() {
                var $this = $(this);
                $this.addClass('loading');
                var property_id = $(this).data('property_id');
                var nonce = $(this).data('nonce');

                var ajaxurl = homeo_property_opts.ajaxurl;
                if ( typeof wp_realestate_opts.ajaxurl_endpoint !== 'undefined' ) {
                    var ajaxurl =  wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_remove_property_compare' );
                }
                $.ajax({
                    url: ajaxurl,
                    type:'POST',
                    dataType: 'json',
                    data: {
                        'property_id': property_id,
                        'nonce': nonce,
                        'action': 'wp_realestate_ajax_remove_property_compare',
                    }
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        
                        $(document).trigger( "after_remove_property_compare", [$this, data] );

                        if ( $('.btn-remove-property-compare-list').length <= 0 ) {
                            $('#compare-sidebar').removeClass('active');
                            $('#compare-sidebar').removeClass('open');
                        }

                        $('a.btn-added-property-compare').each(function(){
                            if ( property_id == $(this).data('property_id') ) {
                                $(this).removeClass('btn-added-property-compare').addClass('btn-add-property-compare');
                                $(this).data('nonce', data.nonce);
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-remove-compare-all', function() {
                var $this = $(this);
                $this.addClass('loading');
                var nonce = $(this).data('nonce');

                var ajaxurl = homeo_property_opts.ajaxurl;
                if ( typeof wp_realestate_opts.ajaxurl_endpoint !== 'undefined' ) {
                    var ajaxurl =  wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_remove_all_property_compare' );
                }

                $.ajax({
                    url: ajaxurl,
                    type:'POST',
                    dataType: 'json',
                    data: {
                        'nonce': nonce,
                        'action': 'wp_realestate_ajax_remove_all_property_compare',
                    }
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        
                        $(document).trigger( "after_remove_property_compare", [$this, data] );

                        $('a.btn-added-property-compare').each(function(){
                            $(this).removeClass('btn-added-property-compare').addClass('btn-add-property-compare');
                            $(this).data('nonce', data.nonce);
                        });
                    }
                });
            });

            $('body').on('click', '#compare-sidebar .compare-sidebar-btn', function(){
                $('#compare-sidebar').toggleClass('open');
            });
        },
        propertiesGetPage: function(pageUrl, isBackButton){
            var self = this;
            if (self.filterAjax) { return false; }

            self.propertiesSetCurrentUrl();

            if (pageUrl) {
                // Show 'loader' overlay
                self.propertiesShowLoader();
                
                // Make sure the URL has a trailing-slash before query args (301 redirect fix)
                pageUrl = pageUrl.replace(/\/?(\?|#|$)/, '/$1');
                
                if (!isBackButton) {
                    self.setPushState(pageUrl);
                }

                self.filterAjax = $.ajax({
                    url: pageUrl,
                    data: {
                        load_type: 'full'
                    },
                    dataType: 'html',
                    cache: false,
                    headers: {'cache-control': 'no-cache'},
                    
                    method: 'POST', // Note: Using "POST" method for the Ajax request to avoid "load_type" query-string in pagination links
                    
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        console.log('Apus: AJAX error - propertiesGetPage() - ' + errorThrown);
                        
                        // Hide 'loader' overlay (after scroll animation)
                        self.propertiesHideLoader();
                        
                        self.filterAjax = false;
                    },
                    success: function(response) {
                        // Update properties content
                        self.propertiesUpdateContent(response);
                        
                        self.filterAjax = false;
                    }
                });
                
            }
        },
        propertiesHideLoader: function(){
            $('body').find('.main-items-wrapper').removeClass('loading');
        },
        propertiesShowLoader: function(){
            $('body').find('.main-items-wrapper').addClass('loading');
        },
        setPushState: function(pageUrl) {
            window.history.pushState({apusShop: true}, '', pageUrl);
        },
        propertiesSetCurrentUrl: function() {
            var self = this;
            
            // Set current page URL
            self.searchAndTagsResetURL = window.location.href;
        },
        /**
         *  Properties: Update properties content with AJAX HTML
         */
        propertiesUpdateContent: function(ajaxHTML) {
            var self = this,
                $ajaxHTML = $('<div>' + ajaxHTML + '</div>');

            var $properties = $ajaxHTML.find('.main-items-wrapper'),
                $display_mode = $ajaxHTML.find('.properties-display-mode-wrapper-ajax .properties-display-mode-wrapper'),
                $pagination = $ajaxHTML.find('.main-pagination-wrapper');

            // Replace properties
            if ($properties.length) {
                $('.main-items-wrapper').replaceWith($properties);
            }
            if ($display_mode.length) {
                $('.properties-display-mode-wrapper').replaceWith($display_mode);
            }
            // Replace pagination
            if ($pagination.length) {
                $('.main-pagination-wrapper').replaceWith($pagination);
            }
            
            // Load images (init Unveil)
            self.layzyLoadImage();

            // pagination
            if ( $('.ajax-pagination').length ) {
                self.infloadScroll = false;
                self.ajaxPaginationLoad();
            }

            if ( $.isFunction( $.fn.select2 ) && typeof wp_realestate_select2_opts !== 'undefined' ) {
                var select2_args = wp_realestate_select2_opts;
                select2_args['allowClear']              = false;
                select2_args['minimumResultsForSearch'] = 10;
                select2_args['width'] = 'auto';
                
                if ( typeof wp_realestate_select2_opts.language_result !== 'undefined' ) {
                    select2_args['language'] = {
                        noResults: function(){
                            return wp_realestate_select2_opts.language_result;
                        }
                    };
                }
                
                $('select.orderby').select2( select2_args );
            }

            $('.btn-saved-search').magnificPopup({
                mainClass: 'wp-realestate-mfp-container',
                type:'inline',
                midClick: true
            });
            
            self.updateMakerCards('properties-google-maps');
            setTimeout(function() {
                // Hide 'loader'
                self.propertiesHideLoader();
            }, 100);
        },

        /**
         *  Shop: Initialize infinite load
         */
        ajaxPaginationLoad: function() {
            var self = this,
                $infloadControls = $('body').find('.ajax-pagination'),                   
                nextPageUrl;

            self.infloadScroll = ($infloadControls.hasClass('infinite-action')) ? true : false;
            
            if (self.infloadScroll) {
                self.infscrollLock = false;
                
                var pxFromWindowBottomToBottom,
                    pxFromMenuToBottom = Math.round($(document).height() - $infloadControls.offset().top);
                
                /* Bind: Window resize event to re-calculate the 'pxFromMenuToBottom' value (so the items load at the correct scroll-position) */
                var to = null;
                $(window).resize(function() {
                    if (to) { clearTimeout(to); }
                    to = setTimeout(function() {
                        var $infloadControls = $('.ajax-pagination'); // Note: Don't cache, element is dynamic
                        pxFromMenuToBottom = Math.round($(document).height() - $infloadControls.offset().top);
                    }, 100);
                });
                
                $(window).scroll(function(){
                    if (self.infscrollLock) {
                        return;
                    }
                    
                    pxFromWindowBottomToBottom = 0 + $(document).height() - ($(window).scrollTop()) - $(window).height();
                    
                    // If distance remaining in the scroll (including buffer) is less than the pagination element to bottom:
                    if (pxFromWindowBottomToBottom < pxFromMenuToBottom) {
                        self.ajaxPaginationGet();
                    }
                });
            } else {
                var $productsWrap = $('body');
                /* Bind: "Load" button */
                $productsWrap.on('click', '.main-pagination-wrapper .apus-loadmore-btn', function(e) {
                    e.preventDefault();
                    self.ajaxPaginationGet();
                });
                
            }
            
            if (self.infloadScroll) {
                $(window).trigger('scroll'); // Trigger scroll in case the pagination element (+buffer) is above the window bottom
            }
        },
        /**
         *  Shop: AJAX load next page
         */
        ajaxPaginationGet: function() {
            var self = this;
            
            if (self.filterAjax) return false;
            
            // Get elements (these can be replaced with AJAX, don't pre-cache)
            var $nextPageLink = $('.apus-pagination-next-link').find('a'),
                $infloadControls = $('.ajax-pagination'),
                nextPageUrl = $nextPageLink.attr('href');
            
            if (nextPageUrl) {
                // Show 'loader'
                $infloadControls.addClass('apus-loader');
                
                self.setPushState(nextPageUrl);

                self.filterAjax = $.ajax({
                    url: nextPageUrl,
                    data: {
                        load_type: 'items'
                    },
                    dataType: 'html',
                    cache: false,
                    headers: {'cache-control': 'no-cache'},
                    method: 'GET',
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        console.log('APUS: AJAX error - ajaxPaginationGet() - ' + errorThrown);
                    },
                    complete: function() {
                        // Hide 'loader'
                        $infloadControls.removeClass('apus-loader');
                    },
                    success: function(response) {
                        var $response = $('<div>' + response + '</div>'), // Wrap the returned HTML string in a dummy 'div' element we can get the elements
                            $gridItemElement = $('.items-wrapper', $response).html(),
                            $resultCount = $('.results-count .last', $response).html(),
                            $display_mode = $('.main-items-wrapper').data('display_mode');
                        

                        // Append the new elements
                        if ( $display_mode == 'grid') {
                            $('.main-items-wrapper .items-wrapper .row').append($gridItemElement);
                        } else {
                            $('.main-items-wrapper .items-wrapper').append($gridItemElement);
                        }
                        
                        // Append results
                        $('.main-items-wrapper .results-count .last').html($resultCount);

                        // Update Maps
                        self.updateMakerCards('properties-google-maps');
                        
                        // Load images (init Unveil)
                        self.layzyLoadImage();
                        
                        // Get the 'next page' URL
                        nextPageUrl = $response.find('.apus-pagination-next-link').children('a').attr('href');
                        
                        if (nextPageUrl) {
                            $nextPageLink.attr('href', nextPageUrl);
                        } else {
                            $('.main-items-wrapper').addClass('all-properties-loaded');
                            
                            if (self.infloadScroll) {
                                self.infscrollLock = true;
                            }
                            $infloadControls.find('.apus-loadmore-btn').addClass('hidden');
                            $nextPageLink.removeAttr('href');
                        }
                        
                        self.filterAjax = false;
                        
                        if (self.infloadScroll) {
                            $(window).trigger('scroll'); // Trigger 'scroll' in case the pagination element (+buffer) is still above the window bottom
                        }
                    }
                });
            } else {
                if (self.infloadScroll) {
                    self.infscrollLock = true; // "Lock" scroll (no more products/pages)
                }
            }
        },
        addCommas: function(str) {
            var parts = (str + "").split("."),
                main = parts[0],
                len = main.length,
                output = "",
                first = main.charAt(0),
                i;
            
            if (first === '-') {
                main = main.slice(1);
                len = main.length;    
            } else {
                first = "";
            }
            i = len - 1;
            while(i >= 0) {
                output = main.charAt(i) + output;
                if ((len - i) % 3 === 0 && i > 0) {
                    output = wp_realestate_opts.money_thousands_separator + output;
                }
                --i;
            }
            // put sign back
            output = first + output;
            // put decimal part back
            if (parts.length > 1) {
                output += wp_realestate_opts.money_dec_point + parts[1];
            }
            return output;
        },
        galleryPropery: function() {
            var self = this;
            $(document).on( 'hover', 'article.property-item', function(){
                if ( !$(this).hasClass('loaded-gallery') && $(this).data('images') ) {
                    var $this = $(this);
                    var href = $(this).find('a.property-image').attr('href')
                    var images = $(this).data('images');
                    var html = '<div class="slick-carousel-gallery-properties hidden" style="width: ' + $(this).find('.property-thumbnail-wrapper').width() + 'px;"><div class="slick-carousel" data-items="1" data-smallmedium="1" data-extrasmall="1" data-pagination="false" data-nav="true" data-disable_draggable="true">';
                    images.forEach(function(img_url, index){
                        html += '<div class="item"><a class="property-image" href="'+ href +'"><img src="'+img_url+'"></a></div>';
                    });
                    html += '</div></div>';
                    $(this).find('.property-thumbnail-wrapper .image-thumbnail').append(html);

                    $(this).find('.slick-carousel-gallery-properties').imagesLoaded( function(){

                        $this.find('.slick-carousel-gallery-properties').removeClass("hidden").delay(200).queue(function(){
                            $(this).addClass("active").dequeue();
                        });

                        self.initSlick($this.find('.slick-carousel'));
                        
                    }).progress( function( instance, image ) {
                        $this.addClass('images-loading');
                    }).done( function( instance ) {
                        $this.addClass('images-loaded').removeClass('images-loading');
                    });

                    $(this).addClass('loaded-gallery');
                }
            });
        }
    });

    $.apusThemeExtensions.property = $.apusThemeCore.property_init;

    
})(jQuery);
