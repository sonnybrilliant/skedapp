var Booking =
{   
    init: function()
    {
        console.log("login: init");
        $('#link-book').bind("click",Booking.bookNow);
    },
    
    confirmBooking: function(event)
    {
        console.log("booking: confirm booking");
        var consultant = $(this).parent().attr('class'); 
        localStorage.setItem("booking_date",$(this).html());
        localStorage.setItem("consultant",consultant);
        
        var fullUrl  = Conf.site._baseUrl+"/api/get/"+Conf.methods._consultant+'/'+Session.session.id+'/';
        fullUrl += consultant;
                    
        console.log(fullUrl);
        
        $.mobile.showPageLoadingMsg();
        $.ajax({
            dataType: 'jsonp',
            jsonpCallback: 'Conf.remoteAjaxCall',
            url: fullUrl
        });
        
    },
    
    bookNow: function()
    {
        console.log("make booking");
        
        var fullUrl  = Conf.site._baseUrl+"/api/get/"+Conf.methods._book+'/'+Session.session.id+'/';
        fullUrl += localStorage.getItem("search_service")+'/'+localStorage.getItem("consultant")+'/';
        fullUrl += localStorage.getItem("user_id")+'/'+localStorage.getItem("search_date")+'/';
        fullUrl += localStorage.getItem("booking_date");
        
        console.log(fullUrl);
    },
    
    onConfirmResult: function(data)
    {
        console.log('booking confirm: results->'+data.count);
        
        if(data.count > 0){
            console.log("booking confirm results");
            $("#div-confirm-booking").html('');
            var consultant = data.results
            var strImage = Conf.site._url+consultant.image;
            
            var serviceName = '';
            
            for(var x=0; x < consultant.services.length ; x++){
                if(localStorage.getItem("search_service") == consultant.services[x].id){
                    serviceName = consultant.services[x].name
                    console.debug('found service name');
                }
            }
            
            
            var str = '';
            str +="<div class='blockWrapper'>";
            str +="      <img class='imageWrapper' width='80px' width='80px' src='"+strImage+"'/>";
            str +="          <div class='paddingBottom15px'>";
            str +="              <div class='nameTags'>"+consultant.fullName+"</div>";
            str +="              <div class='descriptionDetails'>Service provider: <strong>"+consultant.servicesProvider+"</strong></div>";
            str +="              <div class='descriptionDetails'>Contact: <strong>"+consultant.contact+"</strong></div>";
            str +="              <div class='descriptionDetails'>Gender: <strong>"+consultant.gender+"</strong></div>";
            str +="              <div class='descriptionDetails'>Address: <strong>"+consultant.address+"</strong></div>";
            str +="              <div class='descriptionDetails'>Service: <strong>"+serviceName+"</strong></div>";
            str +="              <div class='descriptionDetails'>Date: <strong>"+localStorage.getItem("search_date")+"</strong></div>";
            str +="              <div class='descriptionDetails'>Time: <strong>"+localStorage.getItem("booking_date")+"</strong></div>";
            str +="          </div>";
            str +="          <div class='marginTop10px'></div>";
            str +="</div>";
            $("#div-confirm-booking").html(str);
            
            $.mobile.hidePageLoadingMsg();
            window.location.href = "#booking_confirm"; 
            return false;
        }else{
            console.log("booking confirm 0");
        }
        
    }
    

};
