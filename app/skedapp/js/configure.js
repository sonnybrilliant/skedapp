var Configure =
{
	site:
    {
      _siteURL: "http://www.skedapp.co.za/app_dev.php",
	  _methodInit: "/api/session/start",
      _sessionId : 0
    },
    database: function()
    {
        //initialize database
        try{
            var sessionKey = localStorage.getItem("session_id");
            if(null == sessionKey){
               Configure.sessionInit();
            }else{
               Configure._sessionId = sessionKey;
            }
        }catch(e){
            alert('Failed to get session');
        }
    },
    sessionInit: function()
    {
        alert('init session');
        $.ajax( {
        type:'Get',
        url:Configure._siteURL+Configure._methodInit,
        success:function(data) {
         alert(data);
        },
        error: function(){
            alert('an error happened');
        }

        });
    },
    init: function()
    {
        Configure.database();

    }


};

Configure.init();