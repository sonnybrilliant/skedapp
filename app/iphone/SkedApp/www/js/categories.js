var Categories =
{
   getCategories: function()
   {
        var fullUrl = Conf.site._url+Conf.methods._categories+'/'+Session.session.id;

        $.ajax({
          dataType: 'jsonp',
          jsonpCallback: 'Conf.remoteAjaxCall',
          url: fullUrl
        });
   },
   setCategories: function(data)
   {
       if(data.status == true){
           if(data.count > 0){

               var results = data.results;
               var options;

               $('#select-choice-categories').html();
               options += '<option value="' + 0 + '">Select Category</option>';

               $.each(results, function(index, category ) {
                    options += '<option value="' + category.id + '">' + category.name + '</option>';
                });
               //select-choice-categories
               $('#select-choice-categories').html(options);
               Categories.init();
           }

            //Conf.showAlert(Conf.alert._success,data);
       }else{
           alert('hello');
       }
   },
   init: function()
   {
       $("#select-choice-categories").change(function(){
           Services.getServices(this.value);
       });
   }


};



