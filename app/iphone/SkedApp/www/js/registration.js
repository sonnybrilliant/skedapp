var Registration =
{
    init: function()
    {
        console.log('registration init');
        $("#btn-register").bind("click",Registration.clickHandler);
    },
    clickHandler: function(event)
    {
        console.log('registration listener');
        event.preventDefault();
    }
};




