/**
 * Created by kunono on 2015/2/26.
 */
app.service('adminOrder',['log','base','$q',function(log,base,$q){
    return {
        hasPermission:hasPermissionOrder,
        refresh:refresh,
        setSend:setSend
    };
    function hasPermissionOrder(){
        return base.post('/admin/hasPermission/order',{},'admin hassPermission order');
    }
    function refresh(){
        return base.post('/admin/order/all',{},'admin order all');
    }
    function setSend(id,post_id,post_company){
        var json = {};
        json.id = id;
        json.post_id = post_id;
        json.post_company = post_company;
        return base.post('/admin/order/setSend',json,'admin order setSend');
    }
}]);
