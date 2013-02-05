var Search =
{
    init: function()
    {
        console.log("search: init");
        $('#btn-search').bind("click",Search.checkForm);
    },
    checkForm: function(event)
    {
        try{
                console.log("search: check form triggered");
                $("#div-search-results").html("");
                Search.category = $('#select-choice-categories').val();
                Search.service = $('#select-choice-services').val();
                Search.location = $('#text-location').val();
                Search.date = $('#text-date').val();
                Search.latitude = null;
                Search.longitude = null;
            
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
                    
                    
                    database = new Database();
                    Search.latitude = localStorage.getItem("latitude");
                    Search.longitude = localStorage.getItem("longitude");
                    
                }else{
                    Search.latitude = null;
                    Search.longitude = null;
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
                    
                    if(bookingDate < currentDate)
                    {
                       isValid = false;
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
                    
                    var fullUrl  = Conf.site._baseUrl+"/api/get/"+Conf.methods._search+'/'+Session.session.id+'/';
                        fullUrl += Search.category+'/'+Search.service+'/'+Search.location+'/'+Search.date+'/'+Search.latitude+'/'+Search.longitude+'/1';
                    
                    console.log(fullUrl);
                                        
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
        console.log('search: save coordinate to database');
       
        
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        
        localStorage.setItem("latitude",latitude);
        localStorage.setItem("longitude",longitude);
        
        console.log('search: latitude:'+latitude+'\n Longitude:'+longitude);
    },
    onGeolocationError: function(error)
    {
       Conf.showAlert(Conf.alert._error,'Opps,\n'+'Error code:'+error.code+'\n Error message:'+error.message);
    },
    onResult: function(data)
    {
        console.log('search: results->'+data.count);
        $('#span-results-count').html(data.count);
        
        if(data.count > 0){
            console.log("search: count is greater than 0");
            var consultants = data.results;
            $.each( consultants , function(index, consultant) {
                   var strImage = Conf.site._url+consultant.image;
                   var str = '';
                   str +="<div id='blockWrapper'>";
                   str +=" <img class='imageWrapper' width='60px' width='60px' src='"+strImage+"'/>";
                   str +="     <div class='paddingBottom15px'>";
                   str +="         <div class='nameTags'>"+consultant.fullName+"</div>";
                   str +="         <div class='descriptionDetails'>"+consultant.address+"</div>";
                   str +="         <div class='descriptionDetails'>"+consultant.distance+" km</div>";
                   str +="     </div>";
                   str +="     <table class='tableWrapper'>";
                   str +="         <tbody>";
                   str +="             <tr>";
                   str +="                 <td class='tableDetails'>16:00</td>";
                   str +="                 <td class='tableDetails'>16:45</td>";
                   str +="                 <td class='tableDetails'>17:30</td>";
                   str +="                 <td class='rightDetails'>18:15</td>";
                   str +="             </tr>";
                   str +="         </tbody>";
                   str +="     </table>";
                   str +="     <div class='marginTop10px'></div>";
                   str +="</div>";
                   $("#div-search-results").append(str);
            });

            window.location.href = "#page2";
        }else{
            console.log("search: count equal to 0");
        }
        
    }

};



