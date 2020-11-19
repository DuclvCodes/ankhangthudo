(function($) {
    "use strict";
    
    var map, mapSidebar, markers, CustomHtmlIcon, group;
    var markerArray = [];

    $.extend($.apusThemeCore, {
        /**
         *  Initialize scripts
         */
        property_map_init: function() {
            var self = this;

            if ($('#properties-google-maps').length) {
                L.Icon.Default.imagePath = 'wp-content/themes/homeo/images/';
            }
            
            setTimeout(function(){
                
                self.mapInit('properties-google-maps');
                self.mapInit('single-property-google-maps');
                self.initStreetView();

                self.getProperties('properties-google-maps');
            }, 50);
            
        },
        getProperties: function(map_e_id) {
            var self = this;
            if ( $('.widget-properties-maps .properties-google-maps').length ) {
                $('.widget-properties-maps .properties-google-maps').each(function(e){
                    var $this = $(this);
                    
                    $this.addClass('loading');
                    var $settings = $(this).data('settings');

                    var ajaxurl = homeo_property_opts.ajaxurl;
                    if ( typeof wp_realestate_opts.ajaxurl_endpoint !== 'undefined' ) {
                        var ajaxurl =  wp_realestate_opts.ajaxurl_endpoint.toString().replace( '%%endpoint%%', 'homeo_get_ajax_properties' );
                    }
                    
                    $.ajax({
                        url: ajaxurl,
                        type:'POST',
                        dataType: 'html',
                        data: {
                            action: 'homeo_get_ajax_properties',
                            settings: $settings,
                        }
                    }).done(function(data) {
                        $this.removeClass('loading');
                        $this.closest('.widget-properties-maps').find('.main-items-wrapper').html(data);
                        setTimeout(function(){
                            self.updateMakerCards(map_e_id);
                        });
                    });
                });
            }
        },
        mapInit: function(map_e_id) {
            var self = this;

            var $window = $(window);

            if (!$('#' + map_e_id).length) {
                return;
            }

            map = L.map(map_e_id, {
                scrollWheelZoom: false
            });

            markers = new L.MarkerClusterGroup({
                showCoverageOnHover: false
            });

            CustomHtmlIcon = L.HtmlIcon.extend({
                options: {
                    html: "<div class='map-popup'></div>",
                    iconSize: [38, 50],
                    iconAnchor: [19, 50],
                    popupAnchor: [0, -40]
                }
            });

            $window.on('pxg:refreshmap', function() {
                map._onResize();
                setTimeout(function() {
                    
                    if(markerArray.length > 0 ){
                        group = L.featureGroup(markerArray);
                        map.fitBounds(group.getBounds()); 
                    }
                }, 100);
            });

            $window.on('pxg:simplerefreshmap', function() {
                map._onResize();
            });

            $('.tabs-gallery-map .nav-tabs .tab-google-map').on('click', function(){
                window.dispatchEvent(new Event('resize'));
                
            });

            if ( homeo_property_map_opts.map_service == 'mapbox' ) {
                var tileLayer = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/'+homeo_property_map_opts.mapbox_style+'/tiles/{z}/{x}/{y}?access_token='+ homeo_property_map_opts.mapbox_token, {
                    attribution: " &copy;  <a href='https://www.mapbox.com/about/maps/'>Mapbox</a> &copy;  <a href='http://www.openstreetmap.org/copyright'>OpenStreetMap</a> <strong><a href='https://www.mapbox.com/map-feedback/' target='_blank'>Improve this map</a></strong>",
                    maxZoom: 18,
                });
            } else {
                if ( homeo_property_map_opts.custom_style != '' ) {
                    try {
                        var custom_style = $.parseJSON(homeo_property_map_opts.custom_style);
                        var tileLayer = L.gridLayer.googleMutant({
                            type: homeo_property_map_opts.googlemap_type,
                            styles: custom_style
                        });
                    } catch(err) {
                        var tileLayer = L.gridLayer.googleMutant({
                            type: homeo_property_map_opts.googlemap_type
                        });
                    }
                } else {
                    var tileLayer = L.gridLayer.googleMutant({
                        type: homeo_property_map_opts.googlemap_type
                    });
                }
                $('#apus-listing-map').addClass('map--google');
            }

            map.addLayer(tileLayer);

            // check archive/single page
            if ( !$('#'+map_e_id).is('.single-property-map') ) {
                self.updateMakerCards(map_e_id);
            } else {
                var $item = $('.single-listing-wrapper');
                
                if ( $item.data('latitude') !== "" && $item.data('latitude') !== "" ) {
                    var zoom = (typeof MapWidgetZoom !== "undefined") ? MapWidgetZoom : 15;
                    self.addMakerToMap($item);
                    map.addLayer(markers);
                    map.setView([$item.data('latitude'), $item.data('longitude')], zoom);
                    $(window).on('update:map', function() {
                        map.setView([$item.data('latitude'), $item.data('longitude')], zoom);
                    });

                    $('.location-map-view').on('click', function(e){
                        e.preventDefault();
                        $('#single-property-street-view-map').hide();
                        $('#'+map_e_id).show();
                        $('.location-street-view').removeClass('hidden');
                        $(this).removeClass('hidden').addClass('hidden');
                        map._onResize();
                    });

                } else {
                    $('#' + map_e_id).hide();
                }
            }
        },
        updateMakerCards: function(map_e_id) {
            var self = this;
            var $items = $('.main-items-wrapper .map-item');

            if ($('#' + map_e_id).length && typeof map !== "undefined") {
                
                if (!$items.length) {
                    map.setView([homeo_property_map_opts.default_latitude, homeo_property_map_opts.default_longitude], 12);
                    return;
                }

                map.removeLayer(markers);
                markers = new L.MarkerClusterGroup({
                    showCoverageOnHover: false
                });
                $items.each(function(i, obj) {
                    self.addMakerToMap($(obj), true);
                });

                map.addLayer(markers);

                if(markerArray.length > 0 ){
                    group = L.featureGroup(markerArray);
                    map.fitBounds(group.getBounds()); 
                }
            }
        },
        addMakerToMap: function($item, archive) {
            var self = this;
            var marker;

            if ( $item.data('latitude') == "" || $item.data('longitude') == "") {
                return;
            }
            
            if(homeo_property_map_opts.default_pin){
                var img_agency = "<img src='" + homeo_property_map_opts.default_pin + "'>";
            }else{
                var img_agency = "<i class='flaticon-maps-and-flags'></i>";
            }
            var mapPinHTML = "<div class='map-popup'><div class='icon-wrapper has-img'>" + img_agency + "</div></div>";
            

            marker = L.marker([$item.data('latitude'), $item.data('longitude')], {
                icon: new CustomHtmlIcon({ html: mapPinHTML })
            });

            if (typeof archive !== "undefined") {
                
                $item.hover(function() {
                    $(marker._icon).find('.map-popup').addClass('map-popup-selected');
                }, function() {
                    $(marker._icon).find('.map-popup').removeClass('map-popup-selected');
                });

                var customOptions = {
                    'maxWidth': '290',
                };

                var logo_html = '';
                if ( $item.data('img') ) {
                    logo_html =  "<div class='image-wrapper image-loaded'>" +
                                "<img src='" + $item.data('img') + "'>" +
                            "</div>";
                }

                var title_html = '';
                if ( $item.find('.property-title').length ) {
                    title_html = "<h3 class='property-title'>" + $item.find('.property-title').html() + "</h3>";
                } else if ( $item.find('.agency-title').length ) {
                    title_html = "<h3 class='property-title agency-title'>" + $item.find('.agency-title').html() + "</h3>";
                }


                var price_html = '';
                if ( $item.find('.property-price').length ) {
                    price_html = "<div class='property-price'>" + $item.find('.property-price').html() + "</div>";
                }

                var property_type = '';
                if ( $item.find('.property-type').length ) {
                    property_type = "<div class='property-type '>" + $item.find('.property-type').html() + "</div>";
                }

                var property_location  = '';
                if ( $item.find('.property-location ').length ) {
                    property_location = "<div class='property-location  '>" + $item.find('.property-location ').html() + "</div>";
                }

                var property_metas  = '';
                if ( $item.find('.property-metas ').length ) {
                    property_metas = "<div class='property-metas  '>" + $item.find('.property-metas ').html() + "</div>";
                }

                marker.bindPopup(
                    "<div class='property-item property-grid'><div class='top-info'>" +
                        "<div class='property-thumbnail-wrapper flex-middle justify-content-center'>" + logo_html +
                            "<div class='bottom-label flex-middle'>" + price_html + "</div>" +
                        "</div>" + 
                        "<div class='property-information'>" + property_type + title_html + property_location + property_metas + "</div>" +
                    "</div></div>", customOptions).openPopup();
            }

            markers.addLayer(marker);
            markerArray.push(L.marker([$item.data('latitude'), $item.data('longitude')]));
        },
        initStreetView: function() {
            var panorama = null;
            
            $('.tab-google-street-view-map').on('click', function(e){
                e.preventDefault();
                //$('#single-tab-property-street-view-map').show();

                var $item = $('.single-listing-wrapper');

                if ( $item.data('latitude') !== "" && $item.data('longitude') !== "") {
                    var zoom = (typeof MapWidgetZoom !== "undefined") ? MapWidgetZoom : 15;
                    
                    if ( panorama == null ) {
                        var fenway = new google.maps.LatLng($item.data('latitude'),$item.data('longitude'));
                        var panoramaOptions = {
                            position: fenway,
                            pov: {
                                heading: 34,
                                pitch: 10
                            }
                        };
                        panorama = new  google.maps.StreetViewPanorama(document.getElementById('single-tab-property-street-view-map'),panoramaOptions);
                    }
                }
            });

            $('.location-street-view').on('click', function(e){
                e.preventDefault();
                $('#single-property-street-view-map').show();
                $('#single-property-google-maps').hide();
                $(this).removeClass('hidden').addClass('hidden');
                $('.location-map-view').removeClass('hidden');

                var $item = $('.single-listing-wrapper');

                if ( $item.data('latitude') !== "" && $item.data('longitude') !== "") {
                    var zoom = (typeof MapWidgetZoom !== "undefined") ? MapWidgetZoom : 15;
                    
                    if ( panorama == null ) {
                        var fenway = new google.maps.LatLng($item.data('latitude'),$item.data('longitude'));
                        var panoramaOptions = {
                            position: fenway,
                            pov: {
                                heading: 34,
                                pitch: 10
                            }
                        };
                        panorama = new  google.maps.StreetViewPanorama(document.getElementById('single-property-street-view-map'),panoramaOptions);
                    }
                }
            });
        }

    });

    $.apusThemeExtensions.property_map = $.apusThemeCore.property_map_init;

    
})(jQuery);