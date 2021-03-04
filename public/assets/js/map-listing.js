var markers = [];
	    function initMap() {
	        var map = new google.maps.Map(document.getElementById('place-map-filter'), {
	        	mapTypeControl: false,
	          zoom: 6,
	          center: {lat: -30.9, lng: 151.2},
	          styles: [
			    {
			        "featureType": "landscape",
			        "elementType": "labels",
			        "stylers": [
			            {
			                "visibility": "off"
			            }
			        ]
			    },
			    {
			        "featureType": "transit",
			        "elementType": "labels",
			        "stylers": [
			            {
			                "visibility": "off"
			            }
			        ]
			    },
			    {
			        "featureType": "poi",
			        "elementType": "labels",
			        "stylers": [
			            {
			                "visibility": "off"
			            }
			        ]
			    },
			    {
			        "featureType": "water",
			        "elementType": "labels",
			        "stylers": [
			            {
			                "visibility": "on"
			            }
			        ]
			    },
			    {
			        "featureType": "road",
			        "elementType": "labels.icon",
			        "stylers": [
			            {
			                "visibility": "off"
			            }
			        ]
			    },
			    {
			        "stylers": [
			            {
			                "hue": "#00aaff"
			            },
			            {
			                "saturation": -100
			            },
			            {
			                "gamma": 2.15
			            },
			            {
			                "lightness": 12
			            }
			        ]
			    },
			    {
			        "featureType": "road",
			        "elementType": "labels.text.fill",
			        "stylers": [
			            {
			                "visibility": "on"
			            },
			            {
			                "lightness": 24
			            }
			        ]
			    },
			    {
			        "featureType": "road",
			        "elementType": "geometry",
			        "stylers": [
			            {
			                "lightness": 57
			            }
			        ]
			    }
			],

	        });

	        setMarkers(map);
	    }

	    var mattone_restaurant = '<div class="places-item" id="mattone_restaurant">'+
            '<img src="images/listing/01.jpg" alt="">'+
            '<div class="places-item__info">'+
            '<span class="places-item__category">Restaurant</span>' +
            '<h3>Mattone Restaurant</h3>' +
            '<div class="places-item__meta">' +
            '<div class="places-item__reviews">' +
            '<span class="places-item__number">' +
            '4.8<i class="la la-star"></i>' +
            '<span class="places-item__count">(9 reviews)</span>' +
            '</span>'+
            '</div>'+
            '<div class="places-item__currency">$$</div>' +
            '</div>'+
            '</div>'+
            '</div>';

        var retro_fitness = '<div class="places-item" id="retro_fitness">'+
            '<img src="images/listing/02.jpg" alt="">'+
            '<div class="places-item__info">'+
            '<span class="places-item__category">Gym</span>' +
            '<h3>Retro Fitness</h3>' +
            '<div class="places-item__meta">' +
            '<div class="places-item__reviews">' +
            '<span class="places-item__number">' +
            '4.8<i class="la la-star"></i>' +
            '<span class="places-item__count">(9 reviews)</span>' +
            '</span>'+
            '</div>'+
            '<div class="places-item__currency">$$</div>' +
            '</div>'+
            '</div>'+
            '</div>';

        var body_spa = '<div class="places-item" id="body_spa">'+
            '<img src="images/listing/03.jpg" alt="">'+
            '<div class="places-item__info">'+
            '<span class="places-item__category">Beaty & Spa</span>' +
            '<h3>Body Spa</h3>' +
            '<div class="places-item__meta">' +
            '<div class="places-item__reviews">' +
            '<span class="places-item__number">' +
            '4.8<i class="la la-star"></i>' +
            '<span class="places-item__count">(9 reviews)</span>' +
            '</span>'+
            '</div>'+
            '<div class="places-item__currency">$$</div>' +
            '</div>'+
            '</div>'+
            '</div>';

        var antoinette = '<div class="places-item" id="antoinette">'+
            '<img src="images/listing/04.jpg" alt="">'+
            '<div class="places-item__info">'+
            '<span class="places-item__category">Shop</span>' +
            '<h3>Antoinette</h3>' +
            '<div class="places-item__meta">' +
            '<div class="places-item__reviews">' +
            '<span class="places-item__number">' +
            '4.8<i class="la la-star"></i>' +
            '<span class="places-item__count">(9 reviews)</span>' +
            '</span>'+
            '</div>'+
            '<div class="places-item__currency">$$</div>' +
            '</div>'+
            '</div>'+
            '</div>';

        var vago_restaurant = '<div class="places-item" id="vago_restaurant">'+
            '<img src="images/listing/05.jpg" alt="">'+
            '<div class="places-item__info">'+
            '<span class="places-item__category">See & Doo</span>' +
            '<h3>Vago Restaurant</h3>' +
            '<div class="places-item__meta">' +
            '<div class="places-item__reviews">' +
            '<span class="places-item__number">' +
            '4.8<i class="la la-star"></i>' +
            '<span class="places-item__count">(9 reviews)</span>' +
            '</span>'+
            '</div>'+
            '<div class="places-item__currency">$$</div>' +
            '</div>'+
            '</div>'+
            '</div>';
       
            
        var kathay_cinema = '<div class="places-item" id="kathay_cinema">'+
            '<img src="images/listing/06.jpg" alt="">'+
            '<div class="places-item__info">'+
            '<span class="places-item__category">Cinema</span>' +
            '<h3>Kathay Cinema</h3>' +
            '<div class="places-item__meta">' +
            '<div class="places-item__reviews">' +
            '<span class="places-item__number">' +
            '4.8<i class="la la-star"></i>' +
            '<span class="places-item__count">(9 reviews)</span>' +
            '</span>'+
            '</div>'+
            '<div class="places-item__currency">$$</div>' +
            '</div>'+
            '</div>'+
            '</div>';

        var jardin_club = '<div class="places-item" id="jardin_club">'+
            '<img src="images/listing/07.jpg" alt="">'+
            '<div class="places-item__info">'+
            '<span class="places-item__category">Nightlife</span>' +
            '<h3>Jardin Club</h3>' +
            '<div class="places-item__meta">' +
            '<div class="places-item__reviews">' +
            '<span class="places-item__number">' +
            '4.8<i class="la la-star"></i>' +
            '<span class="places-item__count">(9 reviews)</span>' +
            '</span>'+
            '</div>'+
            '<div class="places-item__currency">$$</div>' +
            '</div>'+
            '</div>'+
            '</div>'; 

        var vivi = '<div class="places-item" id="vivi">'+
            '<img src="images/listing/08.jpg" alt="">'+
            '<div class="places-item__info">'+
            '<span class="places-item__category">Massage</span>' +
            '<h3>Vivi Body Spa</h3>' +
            '<div class="places-item__meta">' +
            '<div class="places-item__reviews">' +
            '<span class="places-item__number">' +
            '4.8<i class="la la-star"></i>' +
            '<span class="places-item__count">(9 reviews)</span>' +
            '</span>'+
            '</div>'+
            '<div class="places-item__currency">$$</div>' +
            '</div>'+
            '</div>'+
            '</div>';               

	    var beaches = [
	        ['mattone_restaurant', mattone_restaurant, -30.143205, 150.131925, 1],
	        ['retro_fitness', retro_fitness, -31.261533, 150.244157, 2],
	        ['body_spa', body_spa, -32.261533, 152.320614, 3],
	        ['antoinette', antoinette, -32.261533, 151.459052, 4],
	        ['vago_restaurant', vago_restaurant, -31.261533, 151.557560, 5],
	        ['kathay_cinema', kathay_cinema, -30.261533, 150.631968, 6],
	        ['jardin_club', jardin_club, -30.261533, 151.731976, 7],
	        ['vivi', vivi, -31.461533, 150.831984, 8],
	        
	        
	    ];

	    var beaches_hover = [
	        ['mattone_restaurant', mattone_restaurant, -30.143205, 150.131925, 1],
	        ['retro_fitness', retro_fitness, -31.261533, 150.244157, 2],
	        ['body_spa', body_spa, -32.261533, 152.320614, 3],
	        ['antoinette', antoinette, -32.261533, 151.459052, 4],
	        ['vago_restaurant', vago_restaurant, -31.261533, 151.557560, 5],
	        ['kathay_cinema', kathay_cinema, -30.261533, 150.631968, 6],
	        ['jardin_club', jardin_club, -30.261533, 151.731976, 7],
	        ['vivi', vivi, -31.461533, 150.831984, 8],
	    ];

	    function setMarkers(map) {

	    	var restaurant_mapker = {
	          	url: 'images/icons/mapker/restaurant.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(36, 36),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 36).
	          	anchor: new google.maps.Point(0, 36)
	        };

	        var restaurant_mapker_hover = {
	          	url: 'images/icons/mapker/restaurant.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(46, 46),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 46).
	          	anchor: new google.maps.Point(0, 46)
	        };

	        var gym_mapker = {
	          	url: 'images/icons/mapker/gym.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(36, 36),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 36).
	          	anchor: new google.maps.Point(0, 36)
	        };

	        var gym_mapker_hover = {
	          	url: 'images/icons/mapker/gym.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(46, 46),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 46).
	          	anchor: new google.maps.Point(0, 46)
	        };

	        var beauty_mapker = {
	          	url: 'images/icons/mapker/beauty.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(36, 36),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 36).
	          	anchor: new google.maps.Point(0, 36)
	        };

	        var beauty_mapker_hover = {
	          	url: 'images/icons/mapker/beauty.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(40, 40),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 46).
	          	anchor: new google.maps.Point(0, 46)
	        };

	        var shop_mapker = {
	          	url: 'images/icons/mapker/shop.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(36, 36),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 36).
	          	anchor: new google.maps.Point(0, 36)
	        };

	        var shop_mapker_hover = {
	          	url: 'images/icons/mapker/shop.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(46, 46),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 46).
	          	anchor: new google.maps.Point(0, 46)
	        };

	        var cinema_mapker = {
	          	url: 'images/icons/mapker/cinema.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(36, 36),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 36).
	          	anchor: new google.maps.Point(0, 36)
	        };

	        var cinema_mapker_hover = {
	          	url: 'images/icons/mapker/cinema.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(46, 46),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 46).
	          	anchor: new google.maps.Point(0, 46)
	        };

	        var nightlife_mapker = {
	          	url: 'images/icons/mapker/nightlife.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(36, 36),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 36).
	          	anchor: new google.maps.Point(0, 36)
	        };
	        var nightlife_mapker_hover = {
	          	url: 'images/icons/mapker/nightlife.svg',
	          	// This marker is 36 pixels wide by 36 pixels high.
	          	scaledSize: new google.maps.Size(46, 46),
	          	// The origin for this image is (0, 0).
	          	origin: new google.maps.Point(0, 0),
	          	// The anchor for this image is the base of the flagpole at (0, 46).
	          	anchor: new google.maps.Point(0, 46)
	        };



	        var mapker_icon = [];
	        mapker_icon.push(restaurant_mapker);
	        mapker_icon.push(gym_mapker);
	        mapker_icon.push(beauty_mapker);
	        mapker_icon.push(shop_mapker);
	        mapker_icon.push(restaurant_mapker);
	        mapker_icon.push(cinema_mapker);
	        mapker_icon.push(nightlife_mapker);
	        mapker_icon.push(beauty_mapker);

	        var mapker_icon_hover = [];
	        mapker_icon_hover.push(restaurant_mapker_hover);
	        mapker_icon_hover.push(gym_mapker_hover);
	        mapker_icon_hover.push(beauty_mapker_hover);
	        mapker_icon_hover.push(shop_mapker_hover);
	        mapker_icon_hover.push(restaurant_mapker_hover);
	        mapker_icon_hover.push(cinema_mapker_hover);
	        mapker_icon_hover.push(nightlife_mapker_hover);
	        mapker_icon_hover.push(beauty_mapker_hover);

	        for (var i = 0; i < beaches.length; i++) {
	        	beaches[ i ].push( mapker_icon[i] );
	        }

	        for (var i = 0; i < beaches_hover.length; i++) {
	        	beaches_hover[ i ].push( mapker_icon_hover[i] );
	        }

	        var shape = {
	          	coords: [1, 1, 1, 20, 18, 20, 18, 1],
	          	type: 'poly'
	        };

	        var elements = document.querySelectorAll(".place-hover");

	        var mk = '';

	        for (var i = 0; i < beaches.length; i++) {
	          	var beach = beaches[i];
	          	var contentString = '<div class="places-item" data-title="Mattone Restaurant" data-lat="-33.843205" data-lng="150.831925" data-index="1">'+
		            '<img src="images/listing/01.jpg" alt="">'+
		            '<div class="places-item__info">'+
		            '<span class="places-item__category">Restaurant</span>' +
		            '<h3>Mattone Restaurant</h3>' +
		            '<div class="places-item__meta">' +
		            '<div class="places-item__reviews">' +
		            '<span class="places-item__number">' +
		            '4.8<i class="la la-star"></i>' +
		            '<span class="places-item__count">(9 reviews)</span>' +
		            '</span>'+
		            '</div>'+
		            '<div class="places-item__currency">$$</div>' +
		            '</div>'+
		            '</div>'+
		            '</div>';

		        var infowindow = new google.maps.InfoWindow({
		          	content: contentString
		        });
	          	var marker = new google.maps.Marker({
		            position: {lat: beach[2], lng: beach[3]},
		            map: map,
		            icon: beach[5],
		            shape: shape,
		            title: beach[0],
		            zIndex: beach[4]
	          	});
	          	marker.data = beaches[i];
	          	marker.addListener('click', function() {
		          	infowindow.setContent("<div id='infowindow'>"+ this.data[1] +"</div>");
			        infowindow.open(map, this);
		        });

		        markers[marker.title] = marker;

		        mk = marker.title;
		        
	        }

			elements.forEach(function(element) {
			    element.addEventListener("mouseenter", function() {
		        	for (var i = 0; i < beaches_hover.length; i++) {
		        		var bh = beaches_hover[i];
		        		if (element.dataset.maps_name == bh[0]) {
				          	markers[element.dataset.maps_name].setIcon(bh[5]);
				        }
			        }
		        	
		        });
		        element.addEventListener("mouseleave", function() {
		        	for (var i = 0; i < beaches.length; i++) {
		        		var b = beaches[i];
		        		if (element.dataset.maps_name == b[0]) {
				          	markers[element.dataset.maps_name].setIcon(b[5]);
				        }
			        }
		        	// closeLastOpenedInfoWindow();
		        });
			});
	    }