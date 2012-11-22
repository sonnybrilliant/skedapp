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
  
});
