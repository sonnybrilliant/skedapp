var Conf =
{
    alert:
    {
    	_error: 1,
       _warning: 2,
       _success: 3
    },
	site:
    {
      //_url: "http://skedapp.kaizania.co.za/app_dev.php/api/get/"
      _baseUrl: "http://www.skedapp.co.za/app_dev.php",
      _url:"http://www.skedapp.co.za",
      
    },
    methods:
    {
    	_init: "init",
      	_categories: "categories",
      	_services: "services",
      	_search: "search",
		_register: "register",
        _login: "login"
        
    },
    // Bind any events that are required on startup. Common events are:
    // 'load', 'deviceready', 'offline', and 'online'.
    bindEvents: function() 
    {
        document.addEventListener('deviceready', Conf.onDeviceReady, false);
    },
    // deviceready Event Handler
    //
    // The scope of 'this' is the event. In order to call the 'receivedEvent'
    // function, we must explicity call 'app.receivedEvent(...);'
    onDeviceReady: function() 
    {
    	Conf.receivedEvent();
    },
    // Update DOM on a Received Event
    receivedEvent: function() 
    {
		if(Conf.checkRequirements()){
		   Session.database();
		   Categories.getCategories();
           Search.init();
           Registration.init();
           Login.init();
           //alert($('#form-registration'));
		}       
    },	
    remoteAjaxCall: function(data)
    {
       if(data.request == Conf.methods._init){
          //set session
          Session.sessionSet(data);
       }else if(data.request == Conf.methods._categories){
          //get catgories 
          Categories.setCategories(data)
       }else if(data.request == Conf.methods._services){
          //get services
          Services.setServices(data)
       }else if(data.request == Conf.methods._search){
          //get search results
          Search.onResult(data);
       }else if(data.request == Conf.methods._register){
          Registration.onResult(data);
       }else if(data.request == Conf.methods._login){
          Login.onResult(data);
       }

    },
    showAlert:function(alertType ,msg )
    {
       
       if(undefined != navigator.notification){
           if(Conf.alert._error == alertType){
               navigator.notification.alert(msg,function(){},'Error');
           }else if(Conf.alert._warning == alertType){
               navigator.notification.alert(msg,function(){},'Warning');
           }else if(Conf.alert._success == alertType){
               navigator.notification.alert(msg,function(){},'Success');
           }
       }else{
           if(Conf.alert._error == alertType){
               alert("Error: "+msg);
           }else if(Conf.alert._warning == alertType){
               alert("Warning: "+msg);
           }else if(Conf.alert._success == alertType){
               alert("Success: "+msg);
           }
       }
    },
    checkRequirements: function()
    {
        if(undefined != navigator.network){
           if (navigator.connection.type == Connection.NONE)
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
        console.log('system init');
	    Conf.bindEvents();
        
    }


};



