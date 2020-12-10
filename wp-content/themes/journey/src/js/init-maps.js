var mapStyles = [
    {
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#f5f5f5"
        }
      ]
    },
    {
      "elementType": "labels.icon",
      "stylers": [
        {
          "visibility": "off"
        }
      ]
    },
    {
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#616161"
        }
      ]
    },
    {
      "elementType": "labels.text.stroke",
      "stylers": [
        {
          "color": "#f5f5f5"
        }
      ]
    },
    {
      "featureType": "administrative.land_parcel",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#bdbdbd"
        }
      ]
    },
    {
      "featureType": "poi",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#eeeeee"
        }
      ]
    },
    {
      "featureType": "poi",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#757575"
        }
      ]
    },
    {
      "featureType": "poi.park",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#e5e5e5"
        }
      ]
    },
    {
      "featureType": "poi.park",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#9e9e9e"
        }
      ]
    },
    {
      "featureType": "road",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#ffffff"
        }
      ]
    },
    {
      "featureType": "road.arterial",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#757575"
        }
      ]
    },
    {
      "featureType": "road.highway",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#dadada"
        }
      ]
    },
    {
      "featureType": "road.highway",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#616161"
        }
      ]
    },
    {
      "featureType": "road.local",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#9e9e9e"
        }
      ]
    },
    {
      "featureType": "transit.line",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#e5e5e5"
        }
      ]
    },
    {
      "featureType": "transit.station",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#eeeeee"
        }
      ]
    },
    {
      "featureType": "water",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#c9c9c9"
        }
      ]
    },
    {
      "featureType": "water",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#9e9e9e"
        }
      ]
    }
  ];

jQuery(document).ready(function($) {
    function initMap($el) {
        var $markers = $el.find('.jrny-map__marker');
    
        var mapArgs = {
            zoom: $el.data('zoom') || 16,
            mapTypeId: 'roadmap',
            styles: mapStyles,
            zoomControl: true,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: false
        };
    
        map = new google.maps.Map( $el[0], mapArgs);
    
        map.markers = [];
    
        $markers.each(function() {
            initMarker( $(this), map );
        });

        centerMap( map );

        return map;
    }
    
    function initMarker( $marker, map ) {
    
        var lat = $marker.data('lat');
        var lng = $marker.data('lng');
        var iconUrl = $marker.data('icon-url');
        var iconSize = $marker.data('icon-size');
        var markerType = $marker.data('type');
        var markerLink = $marker.data('link');
        var latLng = {
            lat: parseFloat(lat),
            lng: parseFloat(lng),
        };
    
        var marker = new google.maps.Marker({
            position: latLng,
            map: map,
            icon: {
                url: iconUrl,
                scaledSize: iconSize ? new google.maps.Size(parseInt(iconSize), parseInt(iconSize)) : iconSize,
            },
            type: markerType,
        });
    
        map.markers.push(marker);

        if (markerLink) {
          console.log('markerLink', markerLink);
          google.maps.event.addListener(marker, 'click', function() {
            console.log('markerLink', markerLink);
            window.location = markerLink;
          });
        }
    
        if ($marker.html()) {
    
            var infoWindow = new google.maps.InfoWindow({
                content: $marker.html(),
            });
    
            google.maps.event.addListener(marker, 'click', function() {
                infoWindow.open( map, marker );
            });
        }
    }
    
    function centerMap( map ) {
        var bounds = new google.maps.LatLngBounds();
    
        map.markers.forEach(function(marker) {
            bounds.extend({
                lat: marker.position.lat(),
                lng: marker.position.lng(),
            });
        });
    
        if (map.markers.length === 1) {
            map.setCenter( bounds.getCenter() );
        } else {
            map.fitBounds( bounds);
        }

        // Reset the zoom level to 14 if it's too close
        var listener = google.maps.event.addListener(map, 'idle', function() {
          console.log('nope');
          console.log(map.getZoom());
          if (map.getZoom() > 12) map.setZoom(12);
          google.maps.event.removeListener(listener);
        });
    }

    $('.jrny-map').each(function() {
        var map = initMap($(this));

        // Add click handlers for any marker switches in the same section
        $(this).closest('.jrny-section').find('.jrny-marker-switch').each(function() {
          var type = $(this).data('type');
          $(this).change(function() {
            var checked = this.checked;
            for(var i = 0; i < map.markers.length; i++) {
              if (map.markers[i].type === type) {
                map.markers[i].setVisible(checked);
              }
            }
          });
        });
    });
});