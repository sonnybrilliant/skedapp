
function addMarkers()
{

    var markerBounds = new google.maps.LatLngBounds();

    markerBounds.extend(myMarker.getPosition());

    var intResultCnt = 1;

    for (intSPCnt = 0; intSPCnt < serviceProviderIDs.length; intSPCnt++) {
        //Loop through service providers in the search results

        var consultantNames = new String();

        //Compile one marker for each location (service provider) containing info about all consultants
        for (intCCnt = 0; intCCnt < serviceProviders[serviceProviderIDs[intSPCnt]]['consultants'].length; intCCnt++) {

            consultantNames += '<p><a href="' + Routing.generate('sked_app_consultant_view', {
                  id: serviceProviders[serviceProviderIDs[intSPCnt]]['consultants'][intCCnt]['id'],
                  slug: '',
                  lat: searchLatitude,
                  lng: searchLongitude,
                  date: searchDate,
                  categoryId: searchCategoryId,
                  serviceId: searchServiceIds
                }, true) + '">' + serviceProviders[serviceProviderIDs[intSPCnt]]['consultants'][intCCnt]['fullName'] + '</a></p>';
        } //for each consultant

        markerText = '<div class="divMarkerText"><strong>' + intResultCnt + ' '
          + serviceProviders[serviceProviderIDs[intSPCnt]]['name'] + '</strong>' + consultantNames + '<p>' + serviceProviders[serviceProviderIDs[intSPCnt]]['address'] + '</p></div>';

        markerPoint = addOneMarker(searchResultsMap, serviceProviders[serviceProviderIDs[intSPCnt]]['lat'], serviceProviders[serviceProviderIDs[intSPCnt]]['lng'], markerText, intResultCnt)

        markerBounds.extend(markerPoint);
        intResultCnt++;

    } //for each service provider

    if (serviceProviderIDs.length > 0) {
        searchResultsMap.fitBounds(markerBounds);
    }

}

function addOneMarker(mapObject, lat, lng, infoHTML, intResultCnt)
{

    var point = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));

    var marker = new google.maps.Marker({
            position: point,
            map: mapObject
        });

    var infoWindow = new google.maps.InfoWindow();

    if (intResultCnt > 0) {
        var iconName = intResultCnt;
    } else {
        var iconName = '';
    }

    var image = new google.maps.MarkerImage("/img/assets/icons/service-icon" + iconName + ".png",
        // This marker is 26 pixels wide by 38 pixels tall.
        new google.maps.Size(26, 38)
        );

    marker.setIcon (image);

    google.maps.event.addListener(marker, 'click', function() {
            infoWindow.setContent(infoHTML);
            infoWindow.open(mapObject, marker);
        });

    return point;
}