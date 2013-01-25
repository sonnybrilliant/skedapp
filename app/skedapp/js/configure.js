var Configure =
{

    alert:
    {
       _error: 1,
       _warning: 2,
       _success: 3
    },
	site:
    {
      _url: "http://skedapp.kaizania.co.za/app_dev.php/api/get/",
      _url: "http://www.skedapp.co.za/app_dev.php/api/get/"
    },
    methods:
    {
      _init: "init",
      _categories: "categories",
      _services: "services"
    },
    remoteAjaxCall: function(data)
    {
       if(data.request == Configure.methods._init){
          //set session
          Session.sessionSet(data);
       }else if(data.request == Configure.methods._categories){
          Categories.setCategories(data)
       }else if(data.request == Configure.methods._services){
          Services.setServices(data)
       }

    },
    showAlert:function(alertType ,msg )
    {
       
       if(undefined != navigator.notification){
           if(Configure.alert._error == alertType){
               navigator.notification.alert(msg,function(){},'Error');
           }else if(Configure.alert._warning == alertType){
               navigator.notification.alert(msg,function(){},'Warning');
           }else if(Configure.alert._success == alertType){
               navigator.notification.alert(msg,function(){},'Success');
           }
       }else{
           if(Configure.alert._error == alertType){
               alert("Error: "+msg);
           }else if(Configure.alert._warning == alertType){
               alert("Warning: "+msg);
           }else if(Configure.alert._success == alertType){
               alert("Success: "+msg);
           }
       }
    },
    checkRequirements: function()
    {
        if(undefined != navigator.network){
           if (navigator.network.connection.type == Connection.NONE)
           {
              navigator.notification.alert(
                 'To use this app you must enable your internet connection',
                 function(){},
                 'Warning'
              );
              return false;
           }
        }
        return true;
    },
    init: function()
    {
        if(Configure.checkRequirements()){
            Session.database();
            Categories.getCategories();
        }

    }


};



