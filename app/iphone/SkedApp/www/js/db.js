function Database (key,value)
{
    var _db = localStorage;
    
    this.key = key;
    this.value = value;
    
    //save value
    this.save = function(key,value)
    {
        if(!_db)
        {
            console.error('The database is null, unable to save '+key);
            Conf.showAlert(Conf.alert._error,'database is null, please contact support');
        }else{
            try{
               console.log('save data:'+key)
              _db.setItem(key,value);
            }catch(error){
              Conf.showAlert(Conf.alert._error,'database error:'+error+' ,please contact support');
            }
        }
        
    }
    
    //get value
    this.getData = function(key)
    {
        if(!_db)
        {
            console.error('The database is null, unable to save '+key);
            Conf.showAlert(Conf.alert._error,'database is null, please contact support');
        }else{
            try{
                console.log('get data:'+key)
                _db.getItem(key);
            }catch(error){
                Conf.showAlert(Conf.alert._error,'database error:'+error+' ,please contact support');
            }
        }
    }
    
}




