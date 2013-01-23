var searchResultsMap = '';
var myMarker = '';

$(document).ready(function() {

    $('#searchToggle').click(function(){
        $('#searchFrm').toggle();
        $('#searchResults').toggle();
    });

    //update services
    $('#Search_category').change(function(){
        var categoryId = this.value;
        $.getJSON(Routing.generate('sked_app_search_ajax_get_services_by_category', {
            categoryId: categoryId
        }, true),function(response){
            if(response.results){

                var el = $('#Search_consultantServices');
                el.empty();
                $.each(response.results, function(key,value) {
                    el.append($("<option></option>")
                        .attr("value", value.id).text(value.name));

                });

            }
        });
    });


    $( "#Search_booking_date" ).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        minDate: 0,
        maxDate: "+1M +10D",
        dateFormat: 'yy-mm-dd'
    });


    //Run the ajax call to show only selected services
    var categoryId = 0;
    if (document.getElementById('Search_category')) {
        categoryId = document.getElementById('Search_category').value;
    }

    if (categoryId <= 0)
        categoryId = 0;

    $.getJSON(Routing.generate('sked_app_search_ajax_get_services_by_category', {
        categoryId: categoryId
    }, true),function(response){
        if(response.results){

            var el = $('#Search_consultantServices');
            el.empty();
            $.each(response.results, function(key,value) {
                el.append($("<option></option>")
                    .attr("value", value.id).text(value.name));

            });

        }
    });

    if (blnAddress) {

        if ($('#Search_lat').val() == '') {
            $('#Search_lat').val('-25.7500');
            $('#Search_lng').val('28.2200');
        //$('#Search_country').val('Pretoria,South Africa')
        }

        var addresspickerMap = $( "#Search_address" ).addresspicker({
            regionBias: "za",
            mapOptions: {
                zoom: 8
            },
            elements: {
                map:      "#map_canvas",
                lat:      "#Search_lat",
                lng:      "#Search_lng",
                locality: '#Search_locality',
                administrative_area_level_2: '#Search_administrative_area_level_2',
                administrative_area_level_1: '#Search_administrative_area_level_1',
                country:  '#Search_country'
            //		    postal_code: '#postal_code',
            //        type:    '#type'
            }
        });

        var gmarker = addresspickerMap.addresspicker( "marker");
        gmarker.setVisible(true);

        var image = new google.maps.MarkerImage("http://maps.google.com/mapfiles/marker.png",
            // This marker is 20 pixels wide by 34 pixels tall.
            new google.maps.Size(20, 34)
            );

        gmarker.setIcon (image);
        addresspickerMap.addresspicker( "updatePosition");

        searchResultsMap = gmarker.getMap();
        myMarker = gmarker;

    }

});
