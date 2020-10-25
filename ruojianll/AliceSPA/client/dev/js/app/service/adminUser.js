/**
 * Created by kunono on 2015/3/22.
 */
app.service('adminUser',['base','$q',function(base,$q){
    return {
        hasPermission:hasPermission,
        getUserCount:getUserCount
    };
    function hasPermission(){
        var defer = $q.defer();
        base.post('/admin/hasPermission/user',{},'admin hasPermission user').then(function(success){
            defer.resolve(success.hasPermission);
        },function(err){
            defer.reject(null);
        });
        return defer.promise;
    }
    function getUserCount(){
        var defer = $q.defer();
        base.get('/admin/user/count','admin user count').then(function(data){
            defer.resolve(data.count);
        },function(err){
            defer.reject(err);
        });
        return defer.promise;
    }
}]);
