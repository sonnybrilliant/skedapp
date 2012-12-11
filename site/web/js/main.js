$(document).ready(function() {

    $('select.chosen').chosen();
    $('span.chosen select').chosen();

    //update services
    $('#Consultant_category').change(function(){
        var categoryId = this.value;
        $.getJSON(Routing.generate('sked_app_consultant_ajax_get_by_category', { categoryId: categoryId}, true),function(response){
            if(response.results){

                var el = $('#Consultant_consultantServices');
                el.empty();
                el.removeAttr("disabled");
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
        $.getJSON(Routing.generate('sked_app_booking_ajax_get_by_consultant', { consultantId : consultantId}, true ),function(response){
            if(response.results){

                var el = $('#Booking_service');
                el.empty();
                el.removeAttr("disabled");
                $.each(response.results, function(key,value) {
                    el.append($("<option></option>")
                        .attr("value", value.id).text(value.name));

                });
            }
        });
    });

    //Run the ajax call to show only selected services
    var categoryId = 0;
    if (document.getElementById('Consultant_category')) {
      categoryId = document.getElementById('Consultant_category').value;

      if (categoryId <= 0)
        categoryId = 0;

          $.getJSON(Routing.generate('sked_app_consultant_ajax_get_by_category', { categoryId: categoryId}, true),function(response){
              if(response.results){

                  var el = $('#Consultant_consultantServices');
                  el.empty();
                  $.each(response.results, function(key,value) {
                      el.append($("<option></option>")
                          .attr("value", value.id).text(value.name));

                  });

              }
          });

    }
    //http://stackoverflow.com/questions/11270675/how-can-i-disable-the-new-chrome-html5-date-input

    if (navigator.userAgent.indexOf('Chrome/2') != -1) {
    $('input[type=date]').on('click', function(event) {
        event.preventDefault();
    });
}

});
