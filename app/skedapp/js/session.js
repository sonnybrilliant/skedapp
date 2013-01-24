var Session =
{
    session:
    {
       id:'',
       isLoggedIn:''
    },
    database: function()
    {
        //initialize database
        try{

            Session.session.id = localStorage.getItem("session_id");
            if(null == Session.session.id){
               Session.sessionInit();
            }
        }catch(e){
            Configure.showAlert(Configure.alert._error,'Failed to get session'+e);
        }
    },
    sessionInit: function()
    {
        var fullUrl = Configure.site._url+Configure.methods._init;

        $.ajax({
          dataType: 'jsonp',
          jsonpCallback: 'Configure.remoteAjaxCall',
          url: fullUrl
        });
    },
    sessionSet: function(data)
    {
        try{
            var results = data.results;

            localStorage.setItem("session_id",data.results.session);
            Session.session.id = localStorage.getItem("session_id");

        }catch(e){
            Configure.showAlert(Configure.alert._error,'Failed to write session'+e);
        }

    }

};



