(function ($) {
    "use strict";
    var map, CustomHtmlIcon, geocoder, marker;
	var listingLocation = {
		init: function() {
			var self = this;
			setTimeout(function(){
				$( '.cmb-type-pw-map' ).each( function() {
					self.initializeMap( $( this ) );
				});
			}, 100);
		},
		initializeMap: function(mapInstance) {
			var self = this;
			var maps = [];

			var mapCanvas = mapInstance.find( '.pw-map' ).attr('id');
			var location_address = mapInstance.find( '.pw-map-search' );
			var latitude = mapInstance.find( '.pw-map-latitude' );
			var longitude = mapInstance.find( '.pw-map-longitude' );

			var latLng = [wp_realestate_maps_opts.default_latitude, wp_realestate_maps_opts.default_longitude];
			var zoom = 8;

			// If we have saved values, let's set the position and zoom level
			if ( latitude.val().length > 0 && longitude.val().length > 0 ) {
				latLng = [latitude.val(), longitude.val()];
				zoom = 13;
			}

			var $window = $(window);

			if ( wp_realestate_maps_opts.geocoder_country ) {
				geocoder = new L.Control.Geocoder.Nominatim({
					geocodingQueryParams: {countrycodes: wp_realestate_maps_opts.geocoder_country}
				});
			} else {
				geocoder = new L.Control.Geocoder.Nominatim();
			}
			map = L.map(mapCanvas, {
                scrollWheelZoom: false,
                center: latLng,
	            zoom: zoom,
	            zoomControl: true,
            });

            CustomHtmlIcon = L.HtmlIcon.extend({
                options: {
                    html: "<div class='map-popup'></div>",
                    iconSize: [42, 42],
                    iconAnchor: [22, 42],
                    popupAnchor: [0, -42]
                }
            });

            $window.on('pxg:refreshmap', function() {
            	map._onResize();
            });

            $window.on('pxg:simplerefreshmap', function() {
                map._onResize();
            });

            if ( wp_realestate_maps_opts.map_service == 'mapbox' ) {
                var tileLayer = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/'+wp_realestate_maps_opts.mapbox_style+'/tiles/{z}/{x}/{y}?access_token='+ wp_realestate_maps_opts.mapbox_token, {
                    attribution: " &copy;  <a href='https://www.mapbox.com/about/maps/'>Mapbox</a> &copy;  <a href='http://www.openstreetmap.org/copyright'>OpenStreetMap</a> <strong><a href='https://www.mapbox.com/map-feedback/' target='_blank'>Improve this map</a></strong>",
                    maxZoom: 18,
                });
            } else {
                if ( wp_realestate_maps_opts.custom_style != '' ) {
                    try {
                        var custom_style = $.parseJSON(wp_realestate_maps_opts.custom_style);
                        var tileLayer = L.gridLayer.googleMutant({
                            type: wp_realestate_maps_opts.googlemap_type,
                            styles: custom_style
                        });

                    } catch(err) {
                        var tileLayer = L.gridLayer.googleMutant({
                            type: wp_realestate_maps_opts.googlemap_type
                        });
                    }
                } else {
                    var tileLayer = L.gridLayer.googleMutant({
                        type: wp_realestate_maps_opts.googlemap_type
                    });
                }
                $('#' + mapCanvas).addClass('map--google');
            }

            map.addLayer(tileLayer);

            var mapPinHTML = "<div class='map-popup map-popup-empty'><div class='icon-wrapper'><div class='icon-cat'></div></div></div>";

            marker = new L.marker(latLng, {
	            draggable: 'true',
	            icon: new CustomHtmlIcon({ html: mapPinHTML })
	        });

            marker.on('dragend', function(event) {
	            var position = marker.getLatLng();
	            marker.setLatLng(position, {
	              	draggable: 'true'
	            }).bindPopup(position).update();

	            geocoder.reverse(position, map.options.crs.scale(map.getZoom()), function(results) {
	              	location_address.val(results[0].name);
	            });
	            latitude.val( position.lat );
				longitude.val( position.lng );
	        });

            // search location
            location_address.attr('autocomplete', 'off').after('<div class="leaflet-geocode-container"></div>');

            function delay(fn, ms) {
                let timer = 0
                return function(...args) {
                    clearTimeout(timer)
                    timer = setTimeout(fn.bind(this, ...args), ms || 0)
                }
            }

            $(document).on('keyup', '.pw-map-search', delay(function (e) {
            	var s = $(this).val(), $this = $(this), container = $(this).closest('.pw-map-search-wrapper');
              	if ( s && s.length >= 2 ) {
              		search_location_fc($this, s, container);
	            } else {
	            	container.find('.leaflet-geocode-container').html('').removeClass('active');
	            }
	        }, 500));

            var search_location_fc = function($this, s, container) {
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
            }

	        $(document).on( "click", ".leaflet-geocode-container ul li", function(e) {
	        	var container = $(this).closest('.pw-map-search-wrapper');
	            var newLatLng = new L.LatLng($(this).data('latitude'), $(this).data('longitude'));
	            location_address.val($(this).text());
	            marker.setLatLng(newLatLng).update(); 
	            map.panTo(newLatLng);

	            latitude.val($(this).data('latitude'));
	            longitude.val($(this).data('longitude'));
	            container.find('.leaflet-geocode-container').html('').removeClass('active');
	        });

	        // change latitude
	        latitude.change(function() {
	            var position = [latitude.val(), longitude.val()];
	            marker.setLatLng(position, {
	              draggable: 'true'
	            }).bindPopup(position).update();
	            map.panTo(position);

	            var geocodeService = L.esri.Geocoding.geocodeService();
		        geocodeService.reverse().latlng(position).run(function(error, result) {
			      	$('.pw-map-search').val(result.address.Match_addr);
			    });
	            
	        });

	        // change longitude
			longitude.change(function() {
	            var position = [latitude.val(), longitude.val()];
	            marker.setLatLng(position, {
	              draggable: 'true'
	            }).bindPopup(position).update();
	            map.panTo(position);

	            var geocodeService = L.esri.Geocoding.geocodeService();
		        geocodeService.reverse().latlng(position).run(function(error, result) {
			      	$('.pw-map-search').val(result.address.Match_addr);
			    });

	            
	        });

			// find me
			$(document).on('click', '.find-me-location', function() {
		        $(this).addClass('loading');
		        navigator.geolocation.getCurrentPosition(self.getLocation, self.getErrorLocation);
		    });

	        map.addLayer(marker).setView(marker.getLatLng(), zoom);
	        map._onResize();
		},
		getLocation: function(position) {
			$('.pw-map-latitude').val(position.coords.latitude);
	        $('.pw-map-longitude').val(position.coords.longitude);
	        $('.pw-map-search').val('Location');
	        
	        var position = [position.coords.latitude, position.coords.longitude];

	        marker.setLatLng(position, {
              	draggable: 'true'
            }).bindPopup(position).update();
	        map.panTo(position);

	        var geocodeService = L.esri.Geocoding.geocodeService();
	        geocodeService.reverse().latlng(position).run(function(error, result) {
		      	$('.pw-map-search').val(result.address.Match_addr);
		    });

	        return $('.find-me-location').removeClass('loading');
		},
		getErrorLocation: function(position) {
	        return $('.find-me-location').removeClass('loading');
	    }
	}
	listingLocation.init();

    

})(jQuery);


