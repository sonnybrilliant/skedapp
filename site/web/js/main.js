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
    
});
