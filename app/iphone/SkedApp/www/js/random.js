var Random =
{
    init: function()
    {
        console.log("random: init");
        $('.link-profile').bind("click",Random.setProfile);
        
        
    },
    
    bindSearchResults: function()
    {
       console.log("random: bind search results");
       $('.timeslot').bind("click",Booking.confirmBooking); 
    },
	
    setProfile: function(event)
    {
        try{
            console.log("random: set profile details");
            var firstName = localStorage.getItem("first_name");
            var lastName = localStorage.getItem("last_name");
            var emailAddress = localStorage.getItem("email");
            var mobile = localStorage.getItem("mobile");
            
            $('#span-profile-name').html(firstName+' '+lastName);
            $('#span-profile-email').html(emailAddress);
            $('#span-profile-mobile').html(mobile);
           
        }catch(err){
            Conf.showAlert(Conf.alert._error,'Opps,\n An error occured:'+error+', please contact support');
        }
        
    }
    

};




