var Services =
{
   getServices: function(categoryId)
   {
        if(categoryId > 0){
             var fullUrl = Configure.site._url+Configure.methods._services+'/'+Session.session.id+'/'+categoryId;

            $.ajax({
              dataType: 'jsonp',
              jsonpCallback: 'Configure.remoteAjaxCall',
              url: fullUrl
            });
        }else{
            Configure.showAlert(Configure.alert._warning,"Please select a category");
        }
   },
   setServices: function(data)
   {

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
           Configure.showAlert(Configure.alert._warning,"Hello again");
       }
   }

};



