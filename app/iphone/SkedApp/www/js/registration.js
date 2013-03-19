var Registration =
{
    init: function()
    {
        console.log("registration: init");
        $('#btn-registration').bind("click",Registration.checkForm);
    },
	
    checkForm: function(event)
    {
        try{
            console.log("registration: check form triggered");

            Registration.firstName = $('#text-first-name').val();
            Registration.lastName = $('#text-last-name').val();
            Registration.mobile = $('#text-mobile').val();
            Registration.password = $('#text-password-field').val();
            Registration.confirmPassword = $('#text-confirm-password-field').val();
            Registration.email = $('#text-email-address').val();
            Registration.confirmEmail = $('#text-confirm-email-address').val();
			
            var isValid = true;
            var message = '';
            
            if(isValid){
                if(Registration.firstName == ""){
                    $('#text-first-name').focus();
                    message = 'Invalid, please enter first name';
                    isValid = false;
                    console.log('registration: form field-> firstName');
                }else{
                    var str = Registration.firstName;
					
                    if(str.length < 3){
                        message = 'Invalid, first name has minimum of 3 characters';
                        isValid = false;  
                        $('#text-first-name').focus();                  
                    }else if(str.length > 30){
                        message = 'Invalid, first name has maximum of 30 characters';
                        isValid = false;
                        $('#text-first-name').focus();
                    }else if(!Registration.isNameValid(str)){
                        message = 'Invalid, first name cannot have numbers and special characters';
                        isValid = false;
                        $('#text-first-name').focus();
                    }
                }
            }
			
            if(isValid){
                if(Registration.lastName == ""){
                    $('#text-last-name').focus();
                    message = 'Invalid, please enter last name';
                    isValid = false;
                    console.log('registration: form field-> lastName');
                }else{
                    var str = Registration.lastName;
                    if(str.length < 3){
                        message = 'Invalid, last name has minimum of 3 characters';
                        isValid = false; 
                        $('#text-last-name').focus();                   
                    }else if(str.length > 30){
                        message = 'Invalid, last name has maximum of 30 characters';
                        isValid = false;
                        $('#text-last-name').focus();
                    }else if(!Registration.isNameValid(str)){
                        message = 'Invalid, last name cannot have numbers and special characters';
                        isValid = false;
                        $('#text-last-name').focus();
                    }
                }
            }   

            if(isValid){            
                if(Registration.mobile == ""){
                    $('#text-mobile').focus();
                    message = 'Invalid, please enter cellphone number';
                    isValid = false;
                    console.log('registration: form field-> mobile');
                }else{
                    var str = Registration.mobile;
                    if(str.length < 10){
                        message = 'Invalid, cellphone number has minimum of 10 characters';
                        isValid = false; 
                        $('#text-mobile').focus();                   
                    }else if(str.length > 14){
                        message = 'Invalid, cellphone number has maximum of 14 characters';
                        isValid = false;
                        $('#text-mobile').focus(); 
                    }else if(!Registration.isMobileValid(str)){
                        message = 'Invalid, mobile cannot have alphabets and special characters';
                        isValid = false;
                        $('#text-mobile').focus(); 
                    }
                }
            }
			
            if(isValid){
                if(Registration.password == ""){
                    $('#text-password-field').focus();
                    message = 'Invalid, please enter password';
                    isValid = false;
                    console.log('registration: form field-> password');
                }else{
                    var str = Registration.password;
                    if(str.length < 6){
                        message = 'Invalid, password has minimum of 6 characters';
                        isValid = false;  
                        $('#text-password-field').focus();                  
                    }else if(str.length > 20){
                        message = 'Invalid, password number has maximum of 20 characters';
                        isValid = false; 
                        $('#text-password-field').focus();
                    }
                }
            }			
			
            if(isValid){
                if(Registration.password != Registration.confirmPassword){
                    $('#text-confirm-password-field').focus();
                    message = 'Invalid, passwords not match';
                    isValid = false;
                    console.log('registration: form field-> confirm password');
                }
            }			
	
            if(isValid){
                if(Registration.email == ""){
                    $('#text-email-address').focus();
                    message = 'Invalid, please enter email address';
                    isValid = false;
                    console.log('registration: form field-> email');
                }else{
                    var str = Registration.email;
                    if(!Registration.isEmailAddress(str)){
                        message = 'Invalid, please enter a valid email address';
                        isValid = false;
                        $('#text-email-address').focus();
                        console.log('registration: form field-> email');
                    }
                }
            }	
	
            if(isValid){
                if(Registration.confirmEmail != Registration.email){
                    $('#text-confirm-email-address').focus();
                    message = 'Invalid, emails addresses do not match';
                    isValid = false;
                    console.log('registration: form field-> confirm email address');
                }
            }
                
            if(!isValid){
                Conf.showAlert(Conf.alert._error,message);
            }else{
				
                console.log('Posting to API');
				
                var fullUrl   = Conf.site._baseUrl+"/api/get/"+Conf.methods._register+'/'+Session.session.id+'/';
                fullUrl  += Registration.firstName+'/'+Registration.lastName+'/'+Registration.mobile+'/'+Registration.password+'/'+Registration.email;
				
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
		
    isNameValid: function(str)
    {
        var pattern = new RegExp(/^[a-zA-Z]{3,30}$/);
        return str.match(pattern);
		    
    },

    isMobileValid: function(str)
    {
        var pattern = new RegExp(/^(\+\d+)?( |\-)?(\(?\d+\)?)?( |\-)?(\d+( |\-)?)*\d+$/);
        return str.match(pattern);
		    
    },
	
    onResult: function(data)
    {
        console.log('registration: results->'+data.count);
        
        if(data.count > 0){
            console.log("registration results");
            //check if registration was successful
            if(data.code == 1){
                window.location.href = "#registration_success";  
            }else{
                if(data.code == 2){
                    Conf.showAlert(Conf.alert._error,'Email address is already in use,please use another email address');
                    $('#text-email-address').focus();
                }
            }
            
        }else{
            console.log("search: count equal to 0");
        }
        
    }
};




