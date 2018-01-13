var geocoder = new google.maps.Geocoder();
function geocodePosition(pos) {
    geocoder.geocode({
        latLng: pos
    }, function(responses) {
        //	console.log(responses);
        if (responses && responses.length > 0) {
            //console.log(responses[0].address_components);
            updateMarkerAddress(responses[0].formatted_address, responses[0].address_components);
        } else {
            updateMarkerAddress('Cannot determine address at this location.', '');
        }
    });
}

function updateMarkerStatus(str) {
    document.getElementById('markerStatus').innerHTML = str;
}

function updateMarkerPosition(latLng) {
    document.getElementById('info').innerHTML = [
        latLng.lat(),
        latLng.lng()
    ].join(', ');
    document.getElementById('lat').value = latLng.lat();
    document.getElementById('log').value = latLng.lng();

}
infowindow = new google.maps.InfoWindow({
    'size': new google.maps.Size(292, 120)
});


function updateMarkerAddress(str, str1) {
    $("#address").val(str);
    //console.log(str1);

    //document.getElementById('address').text = str;
    //$('#vAreaName').text(str1[3]['long_name']);
    $.each(str1, function(index, val) {
        var arr = val.types;
        if (arr[0] == 'route') {
            infowindow.setContent("Address : " + str + "<br/> Locality : " + val.long_name);
            //$('#vAreaName').val(val.long_name);
        }
        if (arr[0] == 'sublocality') {
            infowindow.setContent("Address : " + str + "<br/> Locality : " + val.long_name);
            //$('#vAreaName').val(val.long_name);
        }
        if (arr[0] == 'locality') {
            infowindow.setContent("Address : " + str + "<br/> Locality : " + val.long_name);
            $('#vCity').val(val.long_name);
        }
        if (arr[0] == 'administrative_area_level_1') {
            infowindow.setContent("Address : " + str + "<br/> Locality : " + val.long_name);
            $('#vState').val(val.long_name);
        }
    });
}

function initialize() {

    lat = $('#lat').val();
    log = $('#log').val();
    var latLng = new google.maps.LatLng(lat, log);
    var map = new google.maps.Map(document.getElementById('mapCanvas'), {
        zoom: 12,
        center: latLng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var input = /** @type {HTMLInputElement} */(
            document.getElementById('pac-input'));

    var types = document.getElementById('type-selector');
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);


    var marker = new google.maps.Marker({
        position: latLng,
        title: 'Select Restaurant Place',
        map: map,
        draggable: true
    });

    // Update current position info.

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }
        //console.log(place.address_components);
        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
        }
        /*marker.setIcon(({
         url: place.icon,
         size: new google.maps.Size(71, 71),
         origin: new google.maps.Point(0, 0),
         anchor: new google.maps.Point(17, 34),
         scaledSize: new google.maps.Size(35, 35)
         }));*/
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        //console.log(place.geometry.location);
        var address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
        }

        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        $("#address").val(address);
        updateMarkerAddress(address, place.address_components);
        updateMarkerPosition(marker.getPosition());
        //updateMarkerAddress(results[0].formatted_address, results[0].address_components);
        infowindow.open(map, marker);
    });

    updateMarkerPosition(latLng);
    geocodePosition(latLng);
    // Add dragging event listeners.
    google.maps.event.addListener(marker, 'dragstart', function() {
        //updateMarkerAddress('Dragging...');
    });
    google.maps.event.addListener(marker, 'drag', function() {
        //updateMarkerStatus('Dragging...');
        updateMarkerPosition(marker.getPosition());
    });
    google.maps.event.addListener(marker, 'dragend', function() {
        //updateMarkerStatus('Drag ended');
        geocodePosition(marker.getPosition());
    });
    google.maps.event.addListener(marker, 'click', function(e) {
        //updateMarkerStatus('Drag ended');
        //geocodePosition(marker.getPosition());
        geocoder.geocode(
                {'latLng': e.latLng},
        function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    if (marker) {
                        marker.setPosition(e.latLng);
                    } else {
                        marker = new google.maps.Marker({
                            position: e.latLng,
                            map: map});
                    }
                    updateMarkerAddress(results[0].formatted_address, results[0].address_components);
                    //infowindow.setContent("Address : "+results[0].formatted_address+"<br/> Locality : ");
                    //responses[0].address_components
                    infowindow.open(map, marker);
                }
            }
        });

        //openInfoWindow(geocodePosition(marker.getPosition()),marker);
    });

    var openInfoWindow = function(result, marker) {
        google.maps.fitBounds(result.geometry.viewport);
        infowindow.setContent(getAddressComponentsHtml(result.address_components));
        infowindow.open(map, marker);
    }
}
// Onload handler to fire off the app. 
google.maps.event.addDomListener(window, 'load', initialize);