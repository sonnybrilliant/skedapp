var Search =
{
   //add click listner
    init: function()
    {
        console.log("Entering Search:init");
        $('#btn-search').bind("click",Search.checkForm);
    },
    checkForm: function()
    {
        try{
                console.log("Check form triggered");
                Search.category = $('#select-choice-categories').val();
                Search.service = $('#select-choice-services').val();
                Search.location = $('#text-location').val();
                Search.date = $('#text-date').val();
            
                var isValid = true;
                var isGeoCode  = true;
            
                //validate service
                if(Search.service == 0)
                {
                    console.log('service is zero');
                    isValid = false;
                    $('#select-choice-services').focus();
                }
            
                if(Search.location == '')
                {
                    isGeoCode = false;
                    navigator.geolocation.getCurrentPosition(Search.onGeolocationSuccess, Search.onGeolocationError);
                    Search.location = null;
                }else{
                    Search.lat = null;
                    Search.long = null;
                }
            
                //validate date
                if(Search.date == '')
                {
                    console.log('date is empty');
                    isValid = false;
                    $('#text-date').focus();
                }else{
                    var str = Search.date;
                    var tmp = str.split('-');
                    var currentDate = new Date();
                    var bookingDate = new Date(tmp[0], tmp[1], tmp[2]);
                    
                    if(currentDate > bookingDate)
                    {
                       Conf.showAlert(Conf.alert._error,'Your booking date is invalid.');
                       $('#text-date').focus(); 
                    }
                }
            
            
                //validate category
                if(Search.category == 0)
                {
                    console.log('category is zero');
                    isValid = false;
                    $('#select-choice-categories').focus();
                }
            
                
            
                //form is valid procceed and post to api
                if(isValid)
                {
                    console.log('Posting to API');
                    console.log('Cat:'+Search.category+' location:'+Search.location+' date:'+Search.date+' service:'+Search.service);
                    
                    var fullUrl = Conf.site._url+Conf.methods._search+'/'+Session.session.id+'/'+Search.category+'/'+Search.service+'/'+Search.location+'/';
                        fullUrl+= Search.date+'/'+Search.lat+'/'+Search.long+'/1';
                                        
                    $.ajax({
                      dataType: 'jsonp',
                      jsonpCallback: 'Conf.remoteAjaxCall',
                      url: fullUrl
                    });
                }
            
        }catch(err){
           Conf.showAlert(Conf.alert._error,'Opps,\n An error occured, please contact support');
        }
        
    },
    onGeolocationSuccess: function(position)
    {
        Search.lat = position.coords.latitude;
        Search.long = position.coords.longitude;
        
        console.log('Latitude:'+Search.lat+' Longitude:'+Search.long);
    },
    onGeolocationError: function(error)
    {
       Conf.showAlert(Conf.alert._error,'Opps,\n'+'Error code:'+error.code+'\n Error message:'+error.message);
    },
    onResult: function(data)
    {
        console.log('results...');
        //console.log('search results'+data);
        //if(data.status == true){
           Conf.showAlert(Conf.alert._error,data.request);
        //}
    }

};




