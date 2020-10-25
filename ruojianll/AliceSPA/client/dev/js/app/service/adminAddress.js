/**
 * Created by kunono on 2015/2/27.
 */
app.service('adminAddress',['log','$q','base',function(log,$q,base){
    return {
        get:get
    };
    function get(id){
        var json = {};
        json.id = id;
        return base.post('/admin/address/get',json,'admin address get');
    }
}]);