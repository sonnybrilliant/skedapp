$(document).ready(function() {
    
    $('select.chosen').chosen();
    $('span.chosen select').chosen();

    //update services
    $('#Consultant_category').change(function(){
        var categoryId = this.value;
        $.getJSON("ajaxGetByCategory/"+categoryId,function(response){
            if(response.results){

                var el = $('#Consultant_consultantServices');
                el.empty();
                $.each(response.results, function(key,value) {
                    el.append($("<option></option>")
                        .attr("value", value.id).text(value.name));

                });

            }
        });
    });
    
    //update services
    $('#Booking_consultant').change(function(){
        var consultantId = this.value;
        $.getJSON("ajaxGetByConsultant/"+consultantId,function(response){
            if(response.results){
                
                var el = $('#Booking_service');
                el.empty();
                $.each(response.results, function(key,value) {
                    el.append($("<option></option>")
                        .attr("value", value.id).text(value.name));
                    
                });
            }
        });
    });
    
    
    jQuery(function($){
        $( ".datepicker" ).datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            minDate: 0, 
            maxDate: "+1M +10D" 
        });
    });
    
    if (blnAddress) {
        var addresspickerMap = $( "#Company_address" ).addresspicker({
            regionBias: "za",
            mapOptions: {
                zoom: 10
            },
            elements: {
                map:      "#map",
                lat:      "#Company_lat",
                lng:      "#Company_lng",
                locality: '#Company_locality',
                //		    administrative_area_level_2: '#administrative_area_level_2',
                //		    administrative_area_level_1: '#administrative_area_level_1',
                country:  '#Company_country'
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
    }


});
