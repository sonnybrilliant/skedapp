function updateConsultantServices(consultantSelect) {

    var consultantId = consultantSelect.attr('value');

    if (typeof consultantId == 'undefined') {
        return false;
    }

    $.getJSON(Routing.generate('sked_app_booking_ajax_get_by_consultant', {
        consultantId : consultantId
    }, true ),function(response){
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
}

$(document).ready(function() {
    $('a.lightbox').lightBox();
    $('a.lightbox1').lightBox();
    $('a.lightbox2').lightBox();
    $('a.lightbox3').lightBox();
    $('a.lightbox4').lightBox();
    $('a.lightbox5').lightBox();
    
    $('select.chosen').chosen();
    $('span.chosen select').chosen();
    
     
    $('.inputHelper').click(function() {
       $(this).val("");
    });
    
    $('.hiddenTimeSlots').hide(); 
    $('.showHiddenSlots').click(function(e){
        var className = $(this).attr('class');
        var tmp = className.split(' ');
        var str  = tmp[0];
        
        tmp = str.split('_');
        var consultant_id = tmp[2];
        $('.li_consultant_'+consultant_id).toggle();
        
        var link_text = $(this).text();
        
        if(link_text == "more"){
            $('.CollapseTimeSlots').hide();
            $('.CollapseTimeSlots').siblings(".slot-expander").children(".showHiddenSlots").text("more");            
            $('.CollapseTimeSlots').removeClass("CollapseTimeSlots");
            
            $('.a_consultant_'+consultant_id).text("less");
            $('.li_consultant_'+consultant_id).addClass("CollapseTimeSlots");
        }else{
            $('.a_consultant_'+consultant_id).text("more");
            $('.CollapseTimeSlots').hide();
            
            
        }

        //var link_parent = this.parent();
        //$(this).parent().siblpings().show();
        //link_parent.sublings('.hiddenTimeSlots').show();
        return false;
    });

    //update services
    $('#Consultant_category').change(function(){
        var categoryId = this.value;
        var consultantId = $('#Consultant_currentId').val();
        $.getJSON(Routing.generate('sked_app_consultant_ajax_get_by_category', {
            categoryId: categoryId,
            consultantId: consultantId
        }, true),function(response){
            if(response.results){

                var el = $('#Consultant_consultantServices');
                el.empty();
                el.removeAttr("disabled");
                $.each(response.results, function(key,value) {

                    selectedService = false;

                    //Check if service should be selected
                    if (response.selectedServices) {
                        $.each(response.selectedServices, function(count,serviceId) {
                            if (serviceId == value.id) {
                                selectedService = true;
                            }
                        });
                    }

                    el.append($("<option></option>")
                        .attr("value", value.id).text(value.name).attr('selected', selectedService));

                });
            }
        });
    });
    
    //update consultants
    $('#Booking_Consultants_company').change(function(){
        var companyId = this.value;
        
        $.getJSON(Routing.generate('sked_app_consultant_ajax_get_by_company', {
            companyId: companyId
        }, true),function(response){
            if(response.results){

                var el = $('#Booking_Consultants_consultant');
                el.empty();
                el.removeAttr("disabled");
                $.each(response.results, function(key,value) {

                    selectedService = false;

                    //Check if service should be selected
                    if (response.selectedServices) {
                        $.each(response.selectedServices, function(count,serviceId) {
                            if (serviceId == value.id) {
                                selectedService = true;
                            }
                        });
                    }

                    el.append($("<option></option>")
                        .attr("value", value.id).text(value.name).attr('selected', selectedService));

                });
            }
        });
    });
    
    
    

    //update services
    $('#Booking_consultant').change(function(){
        updateConsultantServices($('#Booking_consultant'));
    });

    updateConsultantServices($('#Booking_consultant'));

    //Run the ajax call to show only selected services
    var categoryId = 0;
    if (document.getElementById('Consultant_category')) {
        categoryId = document.getElementById('Consultant_category').value;

        if (categoryId <= 0)
            categoryId = 0;

        var consultantId = $('#Consultant_currentId').val();

        $.getJSON(Routing.generate('sked_app_consultant_ajax_get_by_category', {
            categoryId: categoryId,
            consultantId: consultantId
        }, true),function(response){
            if(response.results){

                var el = $('#Consultant_consultantServices');
                el.empty();
                $.each(response.results, function(key,value) {

                    selectedService = false;

                    //Check if service should be selected
                    if (response.selectedServices) {
                        $.each(response.selectedServices, function(count,serviceId) {
                            if (serviceId == value.id) {
                                selectedService = true;
                            }
                        });
                    }

                    el.append($("<option></option>")
                        .attr("value", value.id).text(value.name).attr('selected', selectedService));

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

function sendInviteEMail() {
    window.location = 'mailto:?subject=Make an appoint with us on SkedApp&body=Click on this link to register and find us on SkedApp: ' + Routing.generate('_welcome', null, true);
}