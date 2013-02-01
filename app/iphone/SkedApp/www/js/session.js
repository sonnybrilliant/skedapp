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
            Conf.showAlert(Conf.alert._error,'Failed to get session'+e);
        }
    },
    sessionInit: function()
    {
        var fullUrl = Conf.site._url+Conf.methods._init;

        $.ajax({
          dataType: 'jsonp',
          jsonpCallback: 'Conf.remoteAjaxCall',
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
            Conf.showAlert(Conf.alert._error,'Failed to write session'+e);
        }

    }

};



