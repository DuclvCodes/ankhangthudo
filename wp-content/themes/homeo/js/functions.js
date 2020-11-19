(function ($) {
    "use strict";

    if (!$.apusThemeExtensions)
        $.apusThemeExtensions = {};
    
    function ApusThemeCore() {
        var self = this;
        // self.init();
    };

    ApusThemeCore.prototype = {
        /**
         *  Initialize
         */
        init: function() {
            var self = this;
            
            self.preloadSite();

            self.activeAccordion();

            // slick init
            self.initSlick($("[data-carousel=slick]"));

            // isoto
            self.initIsotope();

            // Unveil init
            setTimeout(function(){
                self.layzyLoadImage();
            }, 200);

            self.initHeaderSticky('main-sticky-header');

            // self.initHeaderSticky('header-mobile');

            // back to top
            self.backToTop();

            // popup image
            self.popupImage();

            $('[data-toggle="tooltip"]').tooltip();

            self.initMobileMenu();

            self.mainMenuInit();
            
            $(document.body).on('click', '.nav [data-toggle="dropdown"]', function(e){
                e.preventDefault();
                if ( this.href && this.href != '#' ){
                    if ( this.target && this.target == '_blank' ) {
                        window.open(this.href, '_blank');
                    } else {
                        window.location.href = this.href;
                    }
                }
            });

            self.loadExtension();
        },
        /**
         *  Extensions: Load scripts
         */
        loadExtension: function() {
            var self = this;
            
            if ($.apusThemeExtensions.quantity_increment) {
                $.apusThemeExtensions.quantity_increment.call(self);
            }

            if ($.apusThemeExtensions.shop) {
                $.apusThemeExtensions.shop.call(self);
            }

            if ($.apusThemeExtensions.property_map) {
                $.apusThemeExtensions.property_map.call(self);
            }

            if ($.apusThemeExtensions.property) {
                $.apusThemeExtensions.property.call(self);
            }
        },
        initSlick: function(element) {
            var self = this;
            element.each( function(){
                var config = {
                    infinite: false,
                    arrows: $(this).data( 'nav' ),
                    dots: $(this).data( 'pagination' ),
                    slidesToShow: 4,
                    slidesToScroll: 4,
                    prevArrow:"<button type='button' class='slick-arrow slick-prev'><i class='flaticon-left-arrow-1' aria-hidden='true'></i></span><span class='textnav'>"+ homeo_ajax.previous +"</span></button>",
                    nextArrow:"<button type='button' class='slick-arrow slick-next'><span class='textnav'>"+ homeo_ajax.next +"</span><i class='flaticon-right-arrow' aria-hidden='true'></i></button>",
                };
            
                var slick = $(this);
                if( $(this).data('items') ){
                    config.slidesToShow = $(this).data( 'items' );
                    var slidestoscroll = $(this).data( 'items' );
                }
                if( $(this).data('infinite') ){
                    config.infinite = true;
                }
                if( $(this).data('autoplay') ){
                    config.autoplay = true;
                    config.autoplaySpeed = 2500;
                }
                if( $(this).data('disable_draggable') ){
                    config.touchMove = false;
                    config.draggable = false;
                    config.swipe = false;
                    config.swipeToSlide = false;
                }
                if( $(this).data('centermode') ){
                    config.centerMode = true;
                }
                if( $(this).data('vertical') ){
                    config.vertical = true;
                }
                if( $(this).data('rows') ){
                    config.rows = $(this).data( 'rows' );
                }
                if( $(this).data('asnavfor') ){
                    config.asNavFor = $(this).data( 'asnavfor' );
                }
                if( $(this).data('slidestoscroll') ){
                    var slidestoscroll = $(this).data( 'slidestoscroll' );
                }
                if( $(this).data('focusonselect') ){
                    config.focusOnSelect = $(this).data( 'focusonselect' );
                }
                config.slidesToScroll = slidestoscroll;

                if ($(this).data('large')) {
                    var desktop = $(this).data('large');
                } else {
                    var desktop = config.items;
                }
                if ($(this).data('smalldesktop')) {
                    var smalldesktop = $(this).data('smalldesktop');
                } else {
                    if ($(this).data('large')) {
                        var smalldesktop = $(this).data('large');
                    } else{
                        var smalldesktop = config.items;
                    }
                }
                if ($(this).data('medium')) {
                    var medium = $(this).data('medium');
                } else {
                    var medium = config.items;
                }
                if ($(this).data('smallmedium')) {
                    var smallmedium = $(this).data('smallmedium');
                } else {
                    var smallmedium = 2;
                }
                if ($(this).data('extrasmall')) {
                    var extrasmall = $(this).data('extrasmall');
                } else {
                    var extrasmall = 2;
                }
                if ($(this).data('smallest')) {
                    var smallest = $(this).data('smallest');
                } else {
                    var smallest = 1;
                }


                if ($(this).data('slidestoscroll_large')) {
                    var slidestoscroll_desktop = $(this).data('slidestoscroll_large');
                } else {
                    var slidestoscroll_desktop = config.slidesToScroll;
                }
                if ($(this).data('slidestoscroll_smalldesktop')) {
                    var slidestoscroll_smalldesktop = $(this).data('slidestoscroll_smalldesktop');
                } else {
                    if ($(this).data('slidestoscroll_large')) {
                        var slidestoscroll_smalldesktop = $(this).data('slidestoscroll_large');
                    } else{
                        var slidestoscroll_smalldesktop = config.items;
                    }
                }
                if ($(this).data('slidestoscroll_medium')) {
                    var slidestoscroll_medium = $(this).data('slidestoscroll_medium');
                } else {
                    var slidestoscroll_medium = config.items;
                }
                if ($(this).data('slidestoscroll_smallmedium')) {
                    var slidestoscroll_smallmedium = $(this).data('slidestoscroll_smallmedium');
                } else {
                    var slidestoscroll_smallmedium = smallmedium;
                }
                if ($(this).data('slidestoscroll_extrasmall')) {
                    var slidestoscroll_extrasmall = $(this).data('slidestoscroll_extrasmall');
                } else {
                    var slidestoscroll_extrasmall = extrasmall;
                }
                if ($(this).data('slidestoscroll_smallest')) {
                    var slidestoscroll_smallest = $(this).data('slidestoscroll_smallest');
                } else {
                    var slidestoscroll_smallest = smallest;
                }
                config.responsive = [
                    {
                        breakpoint: 321,
                        settings: {
                            slidesToShow: smallest,
                            slidesToScroll: slidestoscroll_smallest,
                        }
                    },
                    {
                        breakpoint: 580,
                        settings: {
                            slidesToShow: extrasmall,
                            slidesToScroll: slidestoscroll_extrasmall,
                        }
                    },
                    {
                        breakpoint: 769,
                        settings: {
                            slidesToShow: smallmedium,
                            slidesToScroll: slidestoscroll_smallmedium
                        }
                    },
                    {
                        breakpoint: 981,
                        settings: {
                            slidesToShow: medium,
                            slidesToScroll: slidestoscroll_medium
                        }
                    },
                    {
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: smalldesktop,
                            slidesToScroll: slidestoscroll_smalldesktop
                        }
                    },
                    {
                        breakpoint: 1501,
                        settings: {
                            slidesToShow: desktop,
                            slidesToScroll: slidestoscroll_desktop
                        }
                    }
                ];

                if ( $('html').attr('dir') == 'rtl' ) {
                    config.rtl = true;
                }

                $(this).slick( config );

            } );

            // Fix owl in bootstrap tabs
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("href");
                var $slick = element;

                if ($slick.length > 0 && $slick.hasClass('slick-initialized')) {
                    $slick.slick('refresh');
                }
                self.layzyLoadImage();
            });
        },
        layzyLoadImage: function() {
            $(window).off('scroll.unveil resize.unveil lookup.unveil');
            var $images = $('.image-wrapper:not(.image-loaded) .unveil-image'); // Get un-loaded images only
            if ($images.length) {
                $images.unveil(1, function() {
                    $(this).load(function() {
                        $(this).parents('.image-wrapper').first().addClass('image-loaded');
                        $(this).removeAttr('data-src');
                        $(this).removeAttr('data-srcset');
                        $(this).removeAttr('data-sizes');
                    });
                });
            }

            var $images = $('.product-image:not(.image-loaded) .unveil-image'); // Get un-loaded images only
            if ($images.length) {
                $images.unveil(1, function() {
                    $(this).load(function() {
                        $(this).parents('.product-image').first().addClass('image-loaded');
                    });
                });
            }
        },
        initIsotope: function() {
            $('.isotope-items').each(function(){  
                var $container = $(this);
                
                $container.imagesLoaded( function(){
                    $container.isotope({
                        itemSelector : '.isotope-item',
                        transformsEnabled: true,         // Important for videos
                        masonry: {
                            columnWidth: $container.data('columnwidth')
                        }
                    }); 
                });
            });

            /*---------------------------------------------- 
             *    Apply Filter        
             *----------------------------------------------*/
            $('.isotope-filter li a').on('click', function(){
               
                var parentul = $(this).parents('ul.isotope-filter').data('related-grid');
                $(this).parents('ul.isotope-filter').find('li a').removeClass('active');
                $(this).addClass('active');
                var selector = $(this).attr('data-filter'); 
                $('#'+parentul).isotope({ filter: selector }, function(){ });
                
                return(false);
            });
        },
        initHeaderSticky: function(main_sticky_class) {
            if ( typeof Waypoint !== 'undefined' ) {
                if ( $('.' + main_sticky_class) && typeof Waypoint.Sticky !== 'undefined' ) {
                    var sticky = new Waypoint.Sticky({
                        element: $('.' + main_sticky_class)[0],
                        wrapper: '<div class="main-sticky-header-wrapper">',
                        offset: '-10px',
                        stuckClass: 'sticky-header'
                    });
                }
            }
        },
        backToTop: function () {
            $(window).scroll(function () {
                if ($(this).scrollTop() > 400) {
                    $('#back-to-top').addClass('active');
                } else {
                    $('#back-to-top').removeClass('active');
                }
            });
            $('#back-to-top').on('click', function () {
                $('html, body').animate({scrollTop: '0px'}, 800);
                return false;
            });
        },
        
        popupImage: function() {
            // popup
            $(".popup-image").magnificPopup({type:'image'});
            $('.popup-video').magnificPopup({
                disableOn: 700,
                type: 'iframe',
                mainClass: 'mfp-fade',
                removalDelay: 160,
                preloader: false,
                fixedContentPos: false
            });

            $('.widget-gallery').each(function(){
                var tagID = $(this).attr('id');
                $('#' + tagID).magnificPopup({
                    delegate: '.popup-image-gallery',
                    type: 'image',
                    tLoading: 'Loading image #%curr%...',
                    mainClass: 'mfp-img-mobile',
                    gallery: {
                        enabled: true,
                        navigateByImgClick: true,
                        preload: [0,1] // Will preload 0 - before current, and 1 after the current image
                    }
                });
            });
        },
        preloadSite: function() {
            // preload page
            if ( $('body').hasClass('apus-body-loading') ) {
                $('body').removeClass('apus-body-loading');
                $('.apus-page-loading').fadeOut(100);
            }
        },
        
        activeAccordion: function() {
            $('.panel-collapse').on('show.bs.collapse', function () {
                $(this).siblings('.panel-heading').addClass('active');
            });

            $('.panel-collapse').on('hide.bs.collapse', function () {
                $(this).siblings('.panel-heading').removeClass('active');
            });
        },

        initMobileMenu: function() {

            // menu
            var mobilemenu = $("#navbar-offcanvas").mmenu({
                offCanvas: true,
                navbar: {
                    title: homeo_ajax.mmenu_title
                }
            }, {
                // configuration
                offCanvas: {
                    pageSelector: "#wrapper-hidden"
                }
            });

            // sidebar mobile            
            $('body').on('click', '.mobile-sidebar-btn', function(){
                if ( $('.sidebar-left').length > 0 ) {
                    $('.sidebar-left').toggleClass('active');
                } else if ( $('.sidebar-right').length > 0 ) {
                    $('.sidebar-right').toggleClass('active');
                }
                $('.mobile-sidebar-panel-overlay').toggleClass('active');
                $('.mobile-sidebar-btn i').toggleClass('ti-menu-alt ti-close');
            });
            $('body').on('click', '.mobile-sidebar-panel-overlay, .close-sidebar-btn', function(){
                if ( $('.sidebar-left').length > 0 ) {
                    $('.sidebar-left').removeClass('active');
                } else if ( $('.sidebar-right').length > 0 ) {
                    $('.sidebar-right').removeClass('active');
                }
                $('.mobile-sidebar-panel-overlay').removeClass('active');
                $('.mobile-sidebar-btn i').toggleClass('ti-menu-alt ti-close');
            });


            if ($(window).width() < 992) {
                if ( $('.sidebar-wrapper:not(.offcanvas-filter-sidebar) > .sidebar-right, .sidebar-wrapper:not(.offcanvas-filter-sidebar) > .sidebar-left').length ) {
                    var ps = new PerfectScrollbar('.sidebar-wrapper:not(.offcanvas-filter-sidebar) > .sidebar-right, .sidebar-wrapper:not(.offcanvas-filter-sidebar) > .sidebar-left', {
                        wheelPropagation: true
                    });
                }
            }

            $(window).scroll(function () {
                if ($(window).width() <= 600) {
                    if ( $('#wpadminbar').length ) {
                        var admin_bar_h = $('#wpadminbar').outerHeight();
                        var scroll_h = $(this).scrollTop();
                        if (scroll_h > admin_bar_h) {
                            $('.admin-bar .header-mobile').css({'top': 0});
                        } else {
                            var top = admin_bar_h - scroll_h;
                            $('.admin-bar .header-mobile').css({'top': top});
                        }
                    }
                }
            });
        },
        mainMenuInit: function() {
            $('.apus-megamenu .megamenu .has-mega-menu.aligned-fullwidth').each(function(e){
                var $this = $(this),
                    i = $this.closest(".elementor-container"),
                    a = $this.closest('.apus-megamenu');
                $this.on('hover', function(){
                    var m = $(this).find('> .dropdown-menu .dropdown-menu-inner'),
                        w = i.width();

                    m.css({
                        width: w,
                        marginLeft: i.offset().left - a.offset().left
                    });
                });

                $this.find('.elementor-element').addClass('no-transparent');
            });
        },

        setCookie: function(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+d.toUTCString();
            document.cookie = cname + "=" + cvalue + "; " + expires+";path=/";
        },
        getCookie: function(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for(var i=0; i<ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1);
                if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
            }
            return "";
        }
    }

    $.apusThemeCore = ApusThemeCore.prototype;

    $(document).ready(function() {
        // Initialize script
        var apusthemecore_init = new ApusThemeCore();
        apusthemecore_init.init();
    });

    jQuery(window).on("elementor/frontend/init", function() {
        
        var apusthemecore_init = new ApusThemeCore();

        // General element
        elementorFrontend.hooks.addAction( "frontend/element_ready/apus_element_brands.default",
            function($scope) {
                apusthemecore_init.initSlick($scope.find('.slick-carousel'));
            }
        );

        elementorFrontend.hooks.addAction( "frontend/element_ready/apus_element_features_box.default",
            function($scope) {
                apusthemecore_init.initSlick($scope.find('.slick-carousel'));
            }
        );

        elementorFrontend.hooks.addAction( "frontend/element_ready/apus_element_instagram.default",
            function($scope) {
                apusthemecore_init.initSlick($scope.find('.slick-carousel'));
            }
        );

        elementorFrontend.hooks.addAction( "frontend/element_ready/apus_element_posts.default",
            function($scope) {
                apusthemecore_init.initSlick($scope.find('.slick-carousel'));
            }
        );

        elementorFrontend.hooks.addAction( "frontend/element_ready/apus_element_testimonials.default",
            function($scope) {
                apusthemecore_init.initSlick($scope.find('.slick-carousel'));
            }
        );

        // real estate elements
        elementorFrontend.hooks.addAction( "frontend/element_ready/apus_element_realestate_agencies.default",
            function($scope) {
                apusthemecore_init.initSlick($scope.find('.slick-carousel'));
            }
        );

        elementorFrontend.hooks.addAction( "frontend/element_ready/apus_element_realestate_agents.default",
            function($scope) {
                apusthemecore_init.initSlick($scope.find('.slick-carousel'));
            }
        );

        elementorFrontend.hooks.addAction( "frontend/element_ready/apus_element_realestate_properties.default",
            function($scope) {
                apusthemecore_init.initSlick($scope.find('.slick-carousel'));
            }
        );

        elementorFrontend.hooks.addAction( "frontend/element_ready/apus_element_realestate_properties_tabs.default",
            function($scope) {
                apusthemecore_init.initSlick($scope.find('.slick-carousel'));
            }
        );

        elementorFrontend.hooks.addAction( "frontend/element_ready/apus_element_realestate_properties_slider.default",
            function($scope) {
                apusthemecore_init.initSlick($scope.find('.slick-carousel'));
            }
        );
    });

})(jQuery);

