var Login =
{
    init: function()
    {
        console.log("login: init");
        $('#btn-login').bind("click",Login.checkForm);
    },
	
    checkForm: function(event)
    {
        try{
            console.log("login: check form triggered");

          	Login.password = $('#text-login-password').val();
			Login.email = $('#text-login-email').val();
			
            var isValid = true;
            var message = '';
            
            if(isValid){
                if(Login.email == ""){
                    $('#text-login-email').focus();
                    message = 'Invalid, please enter email address';
                    isValid = false;
                    console.log('registration: form field-> email');
                }else{
					var str = Login.email;
                    if(!Login.isEmailAddress(str)){
                    	message = 'Invalid, please enter a valid email address';
                    	isValid = false;
						$('#text-login-email').focus();
                        console.log('registration: form field-> email');
					}
				}
            }
            
            if(isValid){
                if(Login.password == ""){
                    $('#text-login-password').focus();
                    message = 'Invalid, please enter password';
                    isValid = false;
                    console.log('registration: form field-> password');
				}else{
					var str = Login.password;
					if(str.length < 6){
						message = 'Invalid, password has minimum of 6 characters';
                    	isValid = false;  
						$('#text-login-password').focus();                  
					}else if(str.length > 20){
						message = 'Invalid, password number has maximum of 20 characters';
                    	isValid = false; 
						$('#text-login-password').focus();
					}
				}
            }			
			         
	
            if(!isValid){
               Conf.showAlert(Conf.alert._error,message);
            }else{
				
				console.log('Posting to API');
				
				var fullUrl   = Conf.site._baseUrl+"/api/get/"+Conf.methods._login+'/'+Session.session.id+'/';
					fullUrl  += Login.password+'/'+Login.email;
				
                console.log(fullUrl);
                $.mobile.showPageLoadingMsg();
                $.ajax({
                       dataType: 'jsonp',
                       jsonpCallback: 'Conf.remoteAjaxCall',
                       url: fullUrl
                });
                $.mobile.hidePageLoadingMsg();
					
           }
        }catch(err){
            Conf.showAlert(Conf.alert._error,'Opps,\n An error occured:'+error+', please contact support');
        }
        
    },
	
	isEmailAddress: function(str)
	{
		var pattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
		return str.match(pattern);    
	},
	
    onResult: function(data)
    {
        console.log('Login: results->'+data.count);
        
        /*
         * Check login status
         * code = 1  successfully login
         * code = 2  email account not found
         * code = 3  invalid password
         * code = 4  account not active
         * code = 5  acount not enabled
         */
        
        var message_invalid = 'Your email address and password are invalid, please try again.';
        var message_inactive = 'Your account is not active, activate your account by following the instructions on the email we sent you or please contact support.';
        var message_disabled = 'Your account is disabled, please contact support.';
        
        if(data.code == 1){
            //successfully login
            
            var results = data.results;
            
            localStorage.setItem("first_name",data.results.firstName);
            localStorage.setItem("last_name",data.results.lastName);
            localStorage.setItem("email",data.results.email);
            localStorage.setItem("mobile",data.results.mobile);
            localStorage.setItem("user_id",data.results.id);
            localStorage.setItem("user_type",data.results.type);
            
            $('#span-profile-name').html(data.results.firstName+' '+data.results.lastName);
            $('#span-profile-email').html(data.results.email);
            $('#span-profile-mobile').html(data.results.mobile);
            window.location.href = "#profile";
	
        }else{
            if(data.code == 2){
               Conf.showAlert(Conf.alert._error,message_invalid);
               console.log(message_invalid);
                
            }else if(data.code == 3){
               Conf.showAlert(Conf.alert._error,message_invalid);
               console.log(message_invalid);
            }else if(data.code == 4){
                Conf.showAlert(Conf.alert._error,message_inactive);
                console.log(message_inactive);
            }else if(data.code == 5){
                Conf.showAlert(Conf.alert._error,message_disabled);
                console.log(message_disabled);
            }
        }
    }};




