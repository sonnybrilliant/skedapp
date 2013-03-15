var Services =
{
   getServices: function(categoryId)
   {
        if(categoryId > 0){
            console.log("services: initialize");
            var fullUrl = Conf.site._baseUrl+"/api/get/"+Conf.methods._services+'/'+Session.session.id+'/'+categoryId;

            $.mobile.showPageLoadingMsg();
            $.ajax({
                 dataType: 'jsonp',
                 jsonpCallback: 'Conf.remoteAjaxCall',
                 url: fullUrl
            });
            $.mobile.hidePageLoadingMsg();
            
        }else{
            Conf.showAlert(Conf.alert._warning,"Please select a category");
        }
   },
   setServices: function(data)
   {
       console.log("services: set data");
       if(data.status == true){
           if(data.count > 0){

               var results = data.results;
               var options;

               $('#select-choice-services').html();
               options += '<option value="' + 0 + '">Select Services</option>';

               $.each(results, function(index, service ) {
                    options += '<option value="' + service.id + '">' + service.name + '</option>';
                });

               $('#select-choice-services').html(options);
           }
       }else{
           Conf.showAlert(Conf.alert._warning,"Error on the server, please contact support.");
       }
   }

};



