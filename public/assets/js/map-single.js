
		    // When the window has finished loading create our google map below
		    google.maps.event.addDomListener(window, 'load', initMap);

		    function initMap() {
		        // Basic options for a simple Google Map
		        // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
		        var mapOptions = {
		            // How zoomed in you want the map to start at (always required)
		            zoom: 15,
		            scrollwheel: false,
		            draggable: false,
		            mapTypeControl: false,
		            navigationControl: false,
		            streetViewControl: false,
		            // The latitude and longitude to center the map (always required)
		            center: new google.maps.LatLng(40.450748, -74.436050), // Milltown

		            // How you would like to style the map.
		            // This is where you would paste any style found on Snazzy Maps.
		            styles: [
		                {
		                    "featureType": "all",
		                    "elementType": "labels.text.fill",
		                    "stylers": [
		                        {
		                            "saturation": 36
		                        },
		                        {
		                            "color": "#444444"
		                        },
		                        {
		                            "lightness": 40
		                        }
		                    ]
		                },
		                {
		                    "featureType": "all",
		                    "elementType": "labels.text.stroke",
		                    "stylers": [
		                        {
		                            "visibility": "on"
		                        },
		                        {
		                            "color": "#ffffff"
		                        },
		                        {
		                            "lightness": 16
		                        }
		                    ]
		                },
		                {
		                    "featureType": "all",
		                    "elementType": "labels.icon",
		                    "stylers": [
		                        {
		                            "visibility": "off"
		                        }
		                    ]
		                },
		                {
		                    "featureType": "administrative",
		                    "elementType": "geometry.fill",
		                    "stylers": [
		                        {
		                            "lightness": 20
		                        },
		                        {
		                            "color": "#ffffff"
		                        }
		                    ]
		                },
		                {
		                    "featureType": "administrative",
		                    "elementType": "geometry.stroke",
		                    "stylers": [
		                        {
		                            "color": "#ffffff"
		                        },
		                        {
		                            "lightness": 17
		                        },
		                        {
		                            "weight": 1.2
		                        }
		                    ]
		                },
		                {
		                    "featureType": "landscape",
		                    "elementType": "geometry",
		                    "stylers": [
		                        {
		                            "color": "#ffffff"
		                        },
		                        {
		                            "lightness": 20
		                        }
		                    ]
		                },
		                {
		                    "featureType": "poi",
		                    "elementType": "geometry",
		                    "stylers": [
		                        {
		                            "color": "#ffffff"
		                        },
		                        {
		                            "lightness": 21
		                        }
		                    ]
		                },
		                {
		                    "featureType": "poi",
		                    "elementType": "geometry.fill",
		                    "stylers": [
		                        {
		                            "color": "#F4F3F3"
		                        }
		                    ]
		                },
		                {
		                    "featureType": "road",
		                    "elementType": "geometry.fill",
		                    "stylers": [
		                        {
		                            "visibility": "on"
		                        },
		                        {
		                            "color": "#ffffff"
		                        }
		                    ]
		                },
		                {
		                    "featureType": "road.highway",
		                    "elementType": "geometry.fill",
		                    "stylers": [
		                        {
		                            "color": "#d8d8d8"
		                        },
		                        {
		                            "lightness": 17
		                        }
		                    ]
		                },
		                {
		                    "featureType": "road.highway",
		                    "elementType": "geometry.stroke",
		                    "stylers": [
		                        {
		                            "color": "#ffffff"
		                        },
		                        {
		                            "lightness": 29
		                        },
		                        {
		                            "weight": 0.2
		                        }
		                    ]
		                },
		                {
		                    "featureType": "road.arterial",
		                    "elementType": "geometry",
		                    "stylers": [
		                        {
		                            "color": "#F4F3F3"
		                        },
		                        {
		                            "lightness": 18
		                        }
		                    ]
		                },
		                {
		                    "featureType": "road.local",
		                    "elementType": "geometry",
		                    "stylers": [
		                        {
		                            "color": "#F4F3F3"
		                        },
		                        {
		                            "lightness": 16
		                        }
		                    ]
		                },
		                {
		                    "featureType": "road.local",
		                    "elementType": "geometry.fill",
		                    "stylers": [
		                        {
		                            "color": "#F4F3F3"
		                        }
		                    ]
		                },
		                {
		                    "featureType": "road.local",
		                    "elementType": "labels.text.fill",
		                    "stylers": [
		                        {
		                            "color": "#B9B9B9"
		                        },
		                        {
		                            "weight": "1.19"
		                        }
		                    ]
		                },
		                {
		                    "featureType": "road.local",
		                    "elementType": "labels.text.stroke",
		                    "stylers": [
		                        {
		                            "color": "#ffffff"
		                        },
		                        {
		                            "weight": "0"
		                        }
		                    ]
		                },
		                {
		                    "featureType": "transit",
		                    "elementType": "geometry",
		                    "stylers": [
		                        {
		                            "color": "#B7B7B7"
		                        },
		                        {
		                            "lightness": 10
		                        }
		                    ]
		                },
		                {
		                    "featureType": "water",
		                    "elementType": "geometry",
		                    "stylers": [
		                        {
		                            "color": "#E9DBD2"
		                        },
		                        {
		                            "lightness": 17
		                        }
		                    ]
		                },
		                {
		                    "featureType": "water",
		                    "elementType": "geometry.fill",
		                    "stylers": [
		                        {
		                            "color": "#E9DBD2"
		                        }
		                    ]
		                },
		                {
		                    "featureType": "water",
		                    "elementType": "labels.text.fill",
		                    "stylers": [
		                        {
		                            "color": "#cacaca"
		                        }
		                    ]
		                }
		            ]
		        };
		        // Get the HTML DOM element that will contain your map
		        // We are using a div with id="map" seen below in the <body>
		        var mapElement = document.getElementById('map');

		        // Create the Google Map using our element and options defined above
		        var map = new google.maps.Map(mapElement, mapOptions);

		        // Let's also add a marker while we're at it
		        var marker = new google.maps.Marker({
		            position: new google.maps.LatLng(18.689100, 105.691398),
		            map: map,
		            title: 'Snazzy!'
		        });
		    }