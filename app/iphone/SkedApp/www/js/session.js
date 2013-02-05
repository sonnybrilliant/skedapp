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
            console.log("session: check for a valid session id")
            Session.session.id = localStorage.getItem("session_id");
            if(null == Session.session.id){
               console.log("session: session current null");
               Session.sessionInit();
            }else{
               console.log("session: value->"+Session.session.id); 
            }
        }catch(e){
            Conf.showAlert(Conf.alert._error,'Failed to get session'+e);
        }
    },
    sessionInit: function()
    {
        console.log("session: initialize ");
        var fullUrl = Conf.site._baseUrl+'/api/get/'+Conf.methods._init;

        $.ajax({
          dataType: 'jsonp',
          jsonpCallback: 'Conf.remoteAjaxCall',
          url: fullUrl
        });
    },
    sessionSet: function(data)
    {
        try{
            console.log("session: initialize ");
            var results = data.results;
            
            localStorage.setItem("session_id",data.results.session);
            Session.session.id = localStorage.getItem("session_id");
            console.log("session: get->"+Session.session.id);

        }catch(e){
            Conf.showAlert(Conf.alert._error,'Failed to write session'+e);
        }

    }

};



