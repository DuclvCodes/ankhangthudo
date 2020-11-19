(function ($) {
    "use strict";

    if (!$.wjbExtensions)
        $.wjbExtensions = {};
    
    function WJBMainCore() {
        var self = this;
        self.init();
    };

    WJBMainCore.prototype = {
        /**
         *  Initialize
         */
        init: function() {
            var self = this;

            self.fileUpload($('.label-can-drag'));
            
            self.recaptchaCallback();

            self.submitProperty();

            self.userLoginRegister();

            self.userChangePass();
            
            self.removeProperty();

            // favorite
            self.addPropertyFavorite();

            self.removePropertyFavorite();
            
            // add agent
            self.agencyAddAgent();

            // compare
            self.addPropertyCompare();

            self.removePropertyCompare();

            self.reviewInit();

            self.propertySavedSearch();

            self.select2Init();
            
            self.filterListing();

            // property detail
            self.propertyChartInit();

            self.propertyNearbyYelp();

            self.propertyWalkScore();

            // mixes
            self.mixesFn();

            self.loadExtension();
        },
        loadExtension: function() {
            var self = this;
            
            // if ($.wjbExtensions.ajax_upload) {
            //     $.wjbExtensions.ajax_upload.call(self);
            // }
        },
        recaptchaCallback: function() {
            if (!window.grecaptcha) {
            } else {
                setTimeout(function(){
                    var recaptchas = document.getElementsByClassName("ga-recaptcha");
                    for(var i=0; i<recaptchas.length; i++) {
                        var recaptcha = recaptchas[i];
                        var sitekey = recaptcha.dataset.sitekey;

                        grecaptcha.render(recaptcha, {
                            'sitekey' : sitekey
                        });
                    }
                }, 500);
            }
        },
        fileUpload: function($el){
            
            var isAdvancedUpload = function() {
                var div = document.createElement('div');
                return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
            }();

            if (isAdvancedUpload) {

                var droppedFiles = false;
                $el.each(function(){
                    var label_self = $(this);
                    label_self.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }).on('dragover dragenter', function() {
                        label_self.addClass('is-dragover');
                    }).on('dragleave dragend drop', function() {
                        label_self.removeClass('is-dragover');
                    }).on('drop', function(e) {
                        droppedFiles = e.originalEvent.dataTransfer.files;
                        label_self.parent().find('input[type="file"]').prop('files', droppedFiles).trigger('change');
                    });
                });
            }
            $(document).on('click', '.label-can-drag', function(){
                $(this).parent().find('input[type="file"]').trigger('click');
            });
        },
        submitProperty: function() {
            var self = this;
            $('.cmb-repeatable-group').on('cmb2_add_row', function (event, newRow) {

                // Reinitialise the field we previously destroyed
                $(newRow).find('.label-can-drag').each(function () {
                    self.fileUpload($(this));
                });

            });
        },
        userLoginRegister: function() {
            var self = this;
            
            // sign in proccess
            $('body').on('submit', 'form.login-form', function(){
                var $this = $(this);
                $('.alert', this).remove();
                $this.addClass('loading');
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_login' ),
                    type:'POST',
                    dataType: 'json',
                    data:  $(this).serialize()+"&action=wp_realestate_ajax_login"
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $this.prepend( '<div class="alert alert-info">' + data.msg + '</div>' );
                        setTimeout(function(){
                            window.location.href = wp_realestate_opts.dashboard_url;
                        }, 500);
                    } else {
                        $this.prepend( '<div class="alert alert-warning">' + data.msg + '</div>' );
                    }
                });
                return false; 
            } );
            $('body').on('click', '.back-link', function(e){
                e.preventDefault();
                $('.form-container').hide();
                $($(this).attr('href')).show(); 
                return false;
            } );

             // lost password in proccess
            $('body').on('submit', 'form.forgotpassword-form', function(){
                var $this= $(this);
                $('.alert', this).remove();
                $this.addClass('loading');
                $.ajax({
                  url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_forgotpass' ),
                  type:'POST',
                  dataType: 'json',
                  data:  $(this).serialize()+"&action=wp_realestate_ajax_forgotpass"
                }).done(function(data) {
                     $this.removeClass('loading');
                    if ( data.status ) {
                        $this.prepend( '<div class="alert alert-info">'+data.msg+'</div>' );
                        setTimeout(function(){
                            window.location.reload(true);
                        }, 500);
                    } else {
                        $this.prepend( '<div class="alert alert-warning">'+data.msg+'</div>' );
                    }
                });
                return false; 
            } );
            $('body').on('click', '#forgot-password-form-wrapper form .btn-cancel', function(e){
                e.preventDefault();
                $('#forgot-password-form-wrapper').hide();
                $('#login-form-wrapper').show();
            } );

            // register
            $('body').on('submit', 'form.register-form', function(){
                var $this = $(this);
                $('.alert', this).remove();
                $this.addClass('loading');
                $.ajax({
                  url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_register' ),
                  type:'POST',
                  dataType: 'json',
                  data:  $(this).serialize()+"&action=wp_realestate_ajax_register"
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $this.prepend( '<div class="alert alert-info">'+data.msg+'</div>' );
                        if ( data.redirect ) {
                            setTimeout(function(){
                                window.location.href = wp_realestate_opts.dashboard_url;
                            }, 500);
                        }
                    } else {
                        $this.prepend( '<div class="alert alert-warning">'+data.msg+'</div>' );
                        grecaptcha.reset();
                    }
                });
                return false;
            } );
            
            // wp-realestate-resend-approve-account-btn
            $(document).on('click', '.wp-realestate-resend-approve-account-btn', function(e) {
                e.preventDefault();
                var $this = $(this),
                    $container = $(this).parent();
                $this.addClass('loading');
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_resend_approve_account' ),
                    type:'POST',
                    dataType: 'json',
                    data: {
                        action: 'wp_realestate_ajax_resend_approve_account',
                        login: $this.data('login'),
                    }
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $container.html( data.msg );
                    } else {
                        $container.html( data.msg );
                    }
                });
            });

        },
        userChangePass: function() {
            var self = this;
            $('body').on('submit', 'form.change-password-form', function(){
                var $this = $(this);
                $('.alert', this).remove();
                $this.addClass('loading');
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_change_password' ),
                    type:'POST',
                    dataType: 'json',
                    data:  $(this).serialize()+"&action=wp_realestate_ajax_change_password"
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $this.prepend( '<div class="alert alert-info">' + data.msg + '</div>' );
                        setTimeout(function(){
                            window.location.href = wp_realestate_opts.login_register_url;
                        }, 500);
                    } else {
                        $this.prepend( '<div class="alert alert-warning">' + data.msg + '</div>' );
                    }
                });
                return false; 
            } );
        },
        removeProperty: function() {
            var self = this;
            $('.property-button-delete').on('click', function() {
                var $this = $(this);
                var r = confirm( wp_realestate_opts.rm_item_txt );
                if ( r == true ) {
                    $this.addClass('loading');
                    var property_id = $(this).data('property_id');
                    var nonce = $(this).data('nonce');
                    $.ajax({
                        url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_remove_property' ),
                        type:'POST',
                        dataType: 'json',
                        data: {
                            'property_id': property_id,
                            'nonce': nonce,
                            'action': 'wp_realestate_ajax_remove_property',
                        }
                    }).done(function(data) {
                        $this.removeClass('loading');
                        if ( data.status ) {
                            $this.closest('.my-properties-item').remove();
                        }
                        self.showMessage(data.msg, data.status);
                    });
                }
            });
        },
        addPropertyFavorite: function() {
            var self = this;
            $(document).on('click', '.btn-add-property-favorite', function() {
                var $this = $(this);
                $this.addClass('loading');
                var property_id = $(this).data('property_id');
                var nonce = $(this).data('nonce');
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_add_property_favorite' ),
                    type:'POST',
                    dataType: 'json',
                    data: {
                        'property_id': property_id,
                        'nonce': nonce,
                        'action': 'wp_realestate_ajax_add_property_favorite',
                    }
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $this.removeClass('btn-add-property-favorite').addClass('btn-added-property-favorite');
                        $this.data('nonce', data.nonce);

                        $(document).trigger( "after_add_property_favorite", [$this, data] );
                    }
                    self.showMessage(data.msg, data.status);
                });
            });
        },
        removePropertyFavorite: function() {
            var self = this;
            $(document).on('click', '.btn-added-property-favorite', function() {
                var $this = $(this);
                $this.addClass('loading');
                var property_id = $(this).data('property_id');
                var nonce = $(this).data('nonce');
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_remove_property_favorite' ),
                    type:'POST',
                    dataType: 'json',
                    data: {
                        'property_id': property_id,
                        'nonce': nonce,
                        'action': 'wp_realestate_ajax_remove_property_favorite',
                    }
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $this.removeClass('btn-added-property-favorite').addClass('btn-add-property-favorite');
                        $this.data('nonce', data.nonce);

                        $(document).trigger( "after_remove_property_favorite", [$this, data] );
                    }
                    self.showMessage(data.msg, data.status);
                });
            });

            $('.btn-remove-property-favorite').on('click', function() {
                var $this = $(this);
                $this.addClass('loading');
                var property_id = $(this).data('property_id');
                var nonce = $(this).data('nonce');
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_remove_property_favorite' ),
                    type:'POST',
                    dataType: 'json',
                    data: {
                        'property_id': property_id,
                        'nonce': nonce,
                        'action': 'wp_realestate_ajax_remove_property_favorite',
                    }
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $this.closest('.property-favorite-wrapper').remove();

                        $(document).trigger( "after_remove_property_favorite", [$this, data] );
                    }
                    self.showMessage(data.msg, data.status);
                });
            });
        },
        // compare
        agencyAddAgent: function() {
            var self = this;

            if($.ui != undefined && $.ui.autocomplete != undefined){
                $('#team-agent-autocomplete').autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_get_agents' ),
                            type:'POST',
                            dataType: 'json',
                            data: {
                                action: 'wp_realestate_ajax_get_agents',
                                q: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function( event, ui ) {
                        $( "#team-agent-autocomplete" ).val('');
                        var html = '<div class="team-agent-inner">';
                        html += '<input type="hidden" name="agent_id" value="'+ui.item.value+'">';
                            html += '<div class="team-agent-img">';
                            if ( ui.item.img ) {
                                html += '<img src="'+ui.item.img+'">';
                            } else {
                                html += '<i class="fa fa-user"></i>';
                            }
                            html += '</div>';
                            html += '<div class="team-agent-content">';
                                html += '<span class="team-agent-label">'+ui.item.label+'</span>';
                                html += '<span class="team-agent-remove"><i class="fas fa-times"></i></span>';
                            html += '</div>';
                        html += '</div>';
                        $( "#team-agent-autocomplete" ).closest('.team-agent-autocomplete-wrapper').find('.team-agent-wrapper').html(html);
                        $(document).on('click', '.team-agent-remove', function(e) {
                            $(this).closest('.team-agent-inner').remove();
                        });
                        return false;
                    }
                }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                    var html = '<div class="team-agent-list-inner">';
                            html += '<div class="team-agent-list-img">';
                            if ( item.img ) {
                                html += '<img src="'+item.img+'">';
                            } else {
                                html += '<i class="fa fa-user"></i>';
                            }
                            html += '</div>';
                            html += '<div class="team-agent-list-content">';
                                html += '<span class="team-agent-list-label">'+item.label+'</span>';
                            html += '</div>';
                        html += '</div>';

                    return $( "<li>" ).append( html ).appendTo( ul );
                };
            }
            // add
            $(document).on('submit', 'form.agency-add-agents-form', function(e) {
                e.preventDefault();
                var $this = $(this);
                $this.addClass('loading');
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_agency_add_agent' ),
                    type:'POST',
                    dataType: 'json',
                    data: $this.serialize() + '&action=wp_realestate_ajax_agency_add_agent'
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $('.agency-agents-list-inner').append(data.html);
                        $this.find('.team-agent-inner').remove();
                    }
                    self.showMessage(data.msg, data.status);
                });
                return false;
            });
            // remove
            $(document).on('click', '.btn-agency-remove-agent', function() {
                var $this = $(this);
                var r = confirm( wp_realestate_opts.rm_item_txt );
                if ( r == true ) {
                    $this.addClass('loading');
                    var agent_id = $(this).data('agent_id');
                    var nonce = $(this).data('nonce');
                    $.ajax({
                        url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_agency_remove_agent' ),
                        type:'POST',
                        dataType: 'json',
                        data: {
                            'agent_id': agent_id,
                            'nonce': nonce,
                            'action': 'wp_realestate_ajax_agency_remove_agent',
                        }
                    }).done(function(data) {
                        $this.removeClass('loading');
                        if ( data.status ) {
                            $this.closest('article.agent-team').remove();
                        }
                        self.showMessage(data.msg, data.status);
                    });
                }
            });
        },
        // compare
        addPropertyCompare: function() {
            var self = this;
            $(document).on('click', '.btn-add-property-compare', function() {
                var $this = $(this);
                $this.addClass('loading');
                var property_id = $(this).data('property_id');
                var nonce = $(this).data('nonce');
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_add_property_compare' ),
                    type:'POST',
                    dataType: 'json',
                    data: {
                        'property_id': property_id,
                        'nonce': nonce,
                        'action': 'wp_realestate_ajax_add_property_compare',
                    }
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $this.removeClass('btn-add-property-compare').addClass('btn-added-property-compare');
                        $this.data('nonce', data.nonce);

                        $(document).trigger( "after_add_property_compare", [$this, data] );
                    }
                    self.showMessage(data.msg, data.status);
                });
            });
        },
        removePropertyCompare: function() {
            var self = this;
            $(document).on('click', '.btn-added-property-compare', function() {
                var $this = $(this);
                $this.addClass('loading');
                var property_id = $(this).data('property_id');
                var nonce = $(this).data('nonce');
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_remove_property_compare' ),
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
                        $this.removeClass('btn-added-property-compare').addClass('btn-add-property-compare');
                        $this.data('nonce', data.nonce);

                        $(document).trigger( "after_remove_property_compare", [$this, data] );
                    }
                    self.showMessage(data.msg, data.status);
                });
            });

            $('.btn-remove-property-compare').on('click', function() {
                var $this = $(this);
                $this.addClass('loading');
                var property_id = $(this).data('property_id');
                var nonce = $(this).data('nonce');
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_remove_property_compare' ),
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
                        location.reload();
                    }
                    self.showMessage(data.msg, data.status);
                });
            });
        },
        reviewInit: function() {
            var self = this;
            
            if ( $('.comment-form-rating').length > 0 ) {
                $('.comment-form-rating .rating-inner').each(function(){
                    var e_this = $(this);
                    var $star = e_this.find('.review-stars');
                    var $review = e_this.find('input.rating');
                    $star.find('li').on('mouseover',
                        function () {
                            $(this).nextAll().find('span').removeClass('active');
                            $(this).prevAll().find('span').removeClass('active').addClass('active');
                            $(this).find('span').removeClass('active').addClass('active');
                        }
                    );
                    $star.on('mouseout', function(){
                        var current = $review.val() - 1;
                        var current_e = $star.find('li').eq(current);

                        current_e.nextAll().find('span').removeClass('active');
                        current_e.prevAll().find('span').removeClass('active').addClass('active');
                        current_e.find('span').removeClass('active').addClass('active');
                    });

                    $star.find('li').on('click', function () {
                        $(this).nextAll().find('span').removeClass('active');
                        $(this).prevAll().find('span').removeClass('active').addClass('active');
                        $(this).find('span').removeClass('active').addClass('active');
                        
                        $review.val($(this).index() + 1);
                    } );

                });
            }
        },
        propertySavedSearch: function() {
            var self = this;
            $('.btn-saved-search').magnificPopup({
                mainClass: 'wp-realestate-mfp-container',
                type:'inline',
                midClick: true
            });
            
            $(document).on('submit', 'form.saved-search-form', function() {
                var $this = $(this);
                $this.addClass('loading');
                var url_vars = self.getUrlVars();
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_add_saved_search' ),
                    type:'POST',
                    dataType: 'json',
                    data: $this.serialize() + '&action=wp_realestate_ajax_add_saved_search' + url_vars
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $this.prepend( '<div class="alert alert-info">'+data.msg+'</div>' );
                        setTimeout(function(){
                            $.magnificPopup.close();
                        }, 1500);
                    } else {
                        $this.prepend( '<div class="alert alert-warning">'+data.msg+'</div>' );
                    }
                });

                return false;
            });

            // Remove property alert
            $(document).on('click', '.btn-remove-saved-search', function() {
                var $this = $(this);
                $this.addClass('loading');
                var saved_search_id = $(this).data('saved_search_id');
                var nonce = $(this).data('nonce');
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_remove_saved_search' ),
                    type:'POST',
                    dataType: 'json',
                    data: {
                        'saved_search_id': saved_search_id,
                        'nonce': nonce,
                        'action': 'wp_realestate_ajax_remove_saved_search',
                    }
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $this.closest('.saved-search-wrapper').remove();
                    }
                    self.showMessage(data.msg, data.status);
                });
            });
        },
        getUrlVars: function() {
            var self = this;
            var vars = '';
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for(var i = 0; i < hashes.length; i++) {
                vars += '&' +hashes[i];
            }
            return vars;
        },
        select2Init: function() {
            var self = this;
            if ( $.isFunction( $.fn.select2 ) && typeof wp_realestate_select2_opts !== 'undefined' ) {
                var select2_args = wp_realestate_select2_opts;
                select2_args['allowClear']              = false;
                select2_args['minimumResultsForSearch'] = 10;
                select2_args['width'] = 'auto';

                select2_args['language'] = {
                    noResults: function(){
                        return wp_realestate_select2_opts.language_result;
                    }
                };

                if($('select').hasClass('orderby')){
                    select2_args.theme = 'default orderby';
                    $('select.orderby').select2( select2_args );
                }
                $('select.property_id').select2( select2_args );
            }
        },
        filterListing: function() {
            var self = this;

            $(document).on('click', 'form .toggle-field .heading-label', function(){
                var container = $(this).closest('.form-group');
                container.find('.form-group-inner').slideToggle();
                if ( container.hasClass('hide-content') ) {
                    container.removeClass('hide-content');
                } else {
                    container.addClass('hide-content');
                }
            });
            $(document).on('click', '.toggle-filter-list', function() {
                var $this = $(this);
                var container = $(this).closest('.form-group');
                container.find('.terms-list .more-fields').each(function(){
                    if ( $(this).hasClass('active') ) {
                        $(this).removeClass('active');
                        $this.find('.text').text(wp_realestate_opts.show_more);
                    } else {
                        $(this).addClass('active');
                        $this.find('.text').text(wp_realestate_opts.show_less);
                    }
                });
            });

            if ( $.isFunction( $.fn.slider ) ) {
                $('.search-distance-slider').each(function(){
                    var $this = $(this);
                    var search_distance = $this.closest('.search-distance-wrapper').find('input[name^=filter-distance]');
                    var search_wrap = $this.closest('.search_distance_wrapper');
                    $(this).slider({
                        range: "min",
                        value: search_distance.val(),
                        min: 0,
                        max: 100,
                        slide: function( event, ui ) {
                            search_distance.val( ui.value );
                            $('.text-distance', search_wrap).text( ui.value );
                            $('.distance-custom-handle', $this).attr( "data-value", ui.value );
                            search_distance.trigger('change');
                        },
                        create: function() {
                            $('.distance-custom-handle', $this).attr( "data-value", $( this ).slider( "value" ) );
                        }
                    } );
                } );

                $('.main-range-slider').each(function(){
                    var $this = $(this);
                    $this.slider({
                        range: true,
                        min: $this.data('min'),
                        max: $this.data('max'),
                        values: [ $this.parent().find('.filter-from').val(), $this.parent().find('.filter-to').val() ],
                        slide: function( event, ui ) {
                            $this.parent().find('.from-text').text( ui.values[ 0 ] );
                            $this.parent().find('.filter-from').val( ui.values[ 0 ] )
                            $this.parent().find('.to-text').text( ui.values[ 1 ] );
                            $this.parent().find('.filter-to').val( ui.values[ 1 ] );
                            $this.parent().find('.filter-to').trigger('change');
                        }
                    } );
                });

                $('.price-range-slider').each(function(){
                    var $this = $(this);
                    $this.slider({
                        range: true,
                        min: $this.data('min'),
                        max: $this.data('max'),
                        values: [ $this.parent().find('.filter-from').val(), $this.parent().find('.filter-to').val() ],
                        slide: function( event, ui ) {
                            $this.parent().find('.from-text .price-text').text( self.addCommas(ui.values[ 0 ]) );
                            $this.parent().find('.filter-from').val( ui.values[ 0 ] )
                            $this.parent().find('.to-text .price-text').text( self.addCommas(ui.values[ 1 ]) );
                            $this.parent().find('.filter-to').val( ui.values[ 1 ] );
                            $this.parent().find('.filter-to').trigger('change');
                        }
                    } );
                });
            }

            $('.find-me').on('click', function() {
                $(this).addClass('loading');
                var this_e = $(this);
                var container = $(this).closest('.form-group');

                navigator.geolocation.getCurrentPosition(function (position) {
                    container.find('input[name="filter-center-latitude"]').val(position.coords.latitude);
                    container.find('input[name="filter-center-longitude"]').val(position.coords.longitude);
                    container.find('input[name="filter-center-location"]').val('Location');
                    container.find('.clear-location').removeClass('hidden');

                    var position = [position.coords.latitude, position.coords.longitude];

                    if ( typeof L.esri.Geocoding.geocodeService != 'undefined' ) {
                    
                        var geocodeService = L.esri.Geocoding.geocodeService();
                        geocodeService.reverse().latlng(position).run(function(error, result) {
                            container.find('input[name="filter-center-location"]').val(result.address.Match_addr);
                        });
                    }

                    return this_e.removeClass('loading');
                }, function (e) {
                    return this_e.removeClass('loading');
                }, {
                    enableHighAccuracy: true
                });
            });

            $('.clear-location').on('click', function() {
                var container = $(this).closest('.form-group');

                container.find('input[name="filter-center-latitude"]').val('');
                container.find('input[name="filter-center-longitude"]').val('');
                container.find('input[name="filter-center-location"]').val('');
                container.find('.clear-location').addClass('hidden');
                container.find('.leaflet-geocode-container').html('');
            });
            $('input[name="filter-center-location"]').on('keyup', function(){
                var container = $(this).closest('.form-group');
                var val = $(this).val();
                if ( $(this).val() !== '' ) {
                    container.find('.clear-location').removeClass('hidden');
                } else {
                    container.find('.clear-location').removeClass('hidden').addClass('hidden');
                }
            });
            $('input[name="filter-center-location"]').each(function(){
                var container = $(this).closest('.form-group');
                var val = $(this).val();
                if ( $(this).val() !== '' ) {
                    container.find('.clear-location').removeClass('hidden');
                } else {
                    container.find('.clear-location').removeClass('hidden').addClass('hidden');
                }
            });
            // search autocomplete location
            if ( typeof L.Control.Geocoder.Nominatim != 'undefined' ) {
                if ( wp_realestate_opts.geocoder_country ) {
                    var geocoder = new L.Control.Geocoder.Nominatim({
                        geocodingQueryParams: {countrycodes: wp_realestate_opts.geocoder_country}
                    });
                } else {
                    var geocoder = new L.Control.Geocoder.Nominatim();
                }

                function delay(fn, ms) {
                    let timer = 0
                    return function(...args) {
                        clearTimeout(timer)
                        timer = setTimeout(fn.bind(this, ...args), ms || 0)
                    }
                }

                $("input[name=filter-center-location]").attr('autocomplete', 'off').after('<div class="leaflet-geocode-container"></div>');
                $("input[name=filter-center-location]").on("keyup", delay(function (e) {
                    var s = $(this).val(), $this = $(this), container = $(this).closest('.form-group-inner');
                    if (s && s.length >= 2) {
                        
                        $this.parent().addClass('loading');
                        geocoder.geocode(s, function(results) {
                            var output_html = '';
                            for (var i = 0; i < results.length; i++) {
                                output_html += '<li class="result-item" data-latitude="'+results[i].center.lat+'" data-longitude="'+results[i].center.lng+'" ><i class="fas fa-map-marker-alt" aria-hidden="true"></i> '+results[i].name+'</li>';
                            }
                            if ( output_html ) {
                                output_html = '<ul>'+ output_html +'</ul>';
                            }

                            container.find('.leaflet-geocode-container').html(output_html).addClass('active');

                            var highlight_texts = s.split(' ');

                            highlight_texts.forEach(function (item) {
                                container.find('.leaflet-geocode-container').highlight(item);
                            });

                            $this.parent().removeClass('loading');
                        });
                    } else {
                        container.find('.leaflet-geocode-container').html('').removeClass('active');
                    }
                }, 500));
                $('.form-group-inner').on('click', '.leaflet-geocode-container ul li', function() {
                    var container = $(this).closest('.form-group-inner');
                    container.find('input[name=filter-center-latitude]').val($(this).data('latitude'));
                    container.find('input[name=filter-center-longitude]').val($(this).data('longitude'));
                    container.find('input[name=filter-center-location]').val($(this).text());
                    container.find('.leaflet-geocode-container').removeClass('active').html('');
                });
            }

            // advance
            $('.filter-toggle-adv').on('click', function(e){
                $('.filter-advance-fields').slideToggle();
                return false;
            });
        },
        propertyChartInit: function() {
            var $this = $('#property_chart_wrapper');
            if( $this.length <= 0 ) {
                return;
            }
            if ( $this.hasClass('loading') ) {
                return;
            }
            $this.addClass('loading');

            $.ajax({
                url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_get_property_chart' ),
                type:'POST',
                dataType: 'json',
                data: {
                    action: 'wp_realestate_get_property_chart',
                    property_id: $this.data('property_id'),
                    nonce: $this.data('nonce'),
                }
            }).done(function(response) {
                if (response.status == 'error') {
                    $this.remove();
                } else {
                    var ctx = $this.get(0).getContext("2d");
                    var myNewChart = new Chart(ctx);
                    var data = {
                        labels: response.stats_labels,
                        datasets: [
                            {
                                label: response.stats_view,
                                backgroundColor: response.bg_color,
                                borderColor: response.border_color,
                                borderWidth: 1,
                                data: response.stats_values
                            },
                        ]
                    };

                    var options = {
                        //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                        scaleBeginAtZero : true,
                        //Boolean - Whether grid lines are shown across the chart
                        scaleShowGridLines : false,
                        //String - Colour of the grid lines
                        scaleGridLineColor : "rgba(0,0,0,.05)",
                        //Number - Width of the grid lines
                        scaleGridLineWidth : 1,
                        //Boolean - Whether to show horizontal lines (except X axis)
                        scaleShowHorizontalLines: true,
                        //Boolean - Whether to show vertical lines (except Y axis)
                        scaleShowVerticalLines: true,
                        //Boolean - If there is a stroke on each bar
                        barShowStroke : false,
                        //Number - Pixel width of the bar stroke
                        barStrokeWidth : 2,
                        //Number - Spacing between each of the X value sets
                        barValueSpacing : 5,
                        //Number - Spacing between data sets within X values
                        barDatasetSpacing : 1,
                        legend: { display: false },

                        tooltips: {
                            enabled: true,
                            mode: 'x-axis',
                            cornerRadius: 4
                        },
                    }

                    var myBarChart = new Chart(ctx, {
                        type: response.chart_type,
                        data: data,
                        options: options
                    });
                }
                $this.removeClass('loading');
            });
        },
        propertyNearbyYelp: function() {
            var $this = $('#property-section-nearby_yelp');
            if ( $this.length <= 0 ) {
                return;
            }
            if ( $this.hasClass('loading') ) {
                return;
            }
            $this.addClass('loading');

            $.ajax({
                url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_get_nearby_yelp' ),
                type:'POST',
                dataType: 'json',
                data: {
                    action: 'wp_realestate_get_nearby_yelp',
                    property_id: $this.data('property_id'),
                    nonce: $this.data('nonce'),
                }
            }).done(function(response) {
                if (response.status) {
                    $this.html( response.html );
                } else {
                    $this.remove();
                }
                $(document).trigger( "after_nearby_yelp_content", [$this, response] );
                $this.removeClass('loading');
            });
        },
        propertyWalkScore: function() {
            var $this = $('#property-section-walk_score');
            if ( $this.length <= 0 ) {
                return;
            }
            if ( $this.hasClass('loading') ) {
                return;
            }
            $this.addClass('loading');

            $.ajax({
                url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_get_walk_score' ),
                type:'POST',
                dataType: 'json',
                data: {
                    action: 'wp_realestate_get_walk_score',
                    property_id: $this.data('property_id'),
                    nonce: $this.data('nonce'),
                }
            }).done(function(response) {
                if (response.status) {
                    $this.html( response.html );
                } else {
                    $this.remove();
                }
                $(document).trigger( "after_walk_score_content", [$this, response] );
                $this.removeClass('loading');
            });
        },
        mixesFn: function() {
            var self = this;
            
            $( '.my-properties-ordering' ).on( 'change', 'select.orderby', function() {
                $( this ).closest( 'form' ).submit();
            });

            $('.contact-form-wrapper').on('submit', function(){
                var $this = $(this);
                $this.addClass('loading');
                $this.find('.alert').remove();
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_contact_form' ),
                    type:'POST',
                    dataType: 'json',
                    data: $this.serialize() + '&action=wp_realestate_ajax_contact_form'
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $this.prepend( '<div class="alert alert-info">'+data.msg+'</div>' );
                    } else {
                        $this.prepend( '<div class="alert alert-warning">'+data.msg+'</div>' );
                    }
                });

                return false;
            });

            $(document).on( 'submit', 'form.delete-profile-form', function() {
                var $this = $(this);
                $this.addClass('loading');
                $(this).find('.alert').remove();
                $.ajax({
                    url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wp_realestate_ajax_delete_profile' ),
                    type:'POST',
                    dataType: 'json',
                    data: $this.serialize() + '&action=wp_realestate_ajax_delete_profile'
                }).done(function(data) {
                    $this.removeClass('loading');
                    if ( data.status ) {
                        $this.prepend( '<div class="alert alert-info">'+data.msg+'</div>' );
                        window.location.href = wp_realestate_opts.home_url;
                    } else {
                        $this.prepend( '<div class="alert alert-warning">'+data.msg+'</div>' );
                    }
                });

                return false;
            });

            if ( $( 'input.field-datetimepicker' ).length > 0 && $.isFunction( $.fn.datetimepicker ) ) {
                $('input.field-datetimepicker').datetimepicker({
                    timepicker: false,
                    format: 'Y-m-d'
                });
            }
            
            // Location Change
            $('body').on('change', 'select.select-field-region', function(){
                var val = $(this).val();
                var next = $(this).data('next');
                var main_select = 'select.select-field-region' + next;
                if ( $(main_select).length > 0 ) {
                    
                    var select2_args = wp_realestate_select2_opts;
                        select2_args['allowClear'] = true;
                        select2_args['minimumResultsForSearch'] = 10;
                        select2_args['width'] = '100%';

                    select2_args['language'] = {
                        noResults: function(){
                            return wp_realestate_select2_opts.language_result;
                        }
                    };

                    $(main_select).prop('disabled', true);
                    $(main_select).val('').trigger('change');

                    if ( val ) {
                        $(main_select).parent().addClass('loading');
                        $.ajax({
                            url: wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'wpre_process_change_location' ),
                            type:'POST',
                            dataType: 'json',
                            data:{
                                'action': 'wpre_process_change_location',
                                'parent': val,
                                'taxonomy': $(main_select).data('taxonomy'),
                                'security': wp_realestate_opts.ajax_nonce,
                            }
                        }).done(function(data) {
                            $(main_select).parent().removeClass('loading');
                            
                            $(main_select).find('option').remove();
                            if ( data ) {
                                $.each(data, function(i, item) {
                                    var option = new Option(item.name, item.id, true, true);
                                    $(main_select).append(option);
                                });
                            }
                            $(main_select).prop("disabled", false);
                            $(main_select).val(null).select2("destroy").select2(select2_args);
                        });
                    } else {
                        $(main_select).find('option').remove();
                        $(main_select).prop("disabled", false);
                        $(main_select).val(null).select2("destroy").select2(select2_args);
                    }
                }
            });
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
        showMessage: function(msg, status) {
            if ( msg ) {
                var classes = 'alert bg-warning';
                if ( status ) {
                    classes = 'alert bg-info';
                }
                var $html = '<div id="wp-realestate-popup-message" class="animated fadeInRight"><div class="message-inner '+ classes +'">'+ msg +'</div></div>';
                $('body').find('#wp-realestate-popup-message').remove();
                $('body').append($html).fadeIn(500);
                setTimeout(function() {
                    $('body').find('#wp-realestate-popup-message').removeClass('fadeInRight').addClass('delay-2s fadeOutRight');
                }, 1500);
            }
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
        },
    }

    $.wjbMainCore = WJBMainCore.prototype;
    
    $(document).ready(function() {
        // Initialize script
        new WJBMainCore();

    });
    
})(jQuery);

