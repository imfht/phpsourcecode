/**
 * Created by kunono on 2015/2/1.
 */
app.service('ICODE',['base','$q','common',function(base,$q,common){

    return{
        generateUrl:generateUrl,
        check:check
    };
    function generateUrl(){
        return common.generateAPI('/ICODE/generate' + '?time=' + Date.parse(new Date()));
    }
    function check(code){
        var json = {};
        json.ICODE = code
        var defer = $q.defer();
        var ret = {};
        base.post('/ICODE/check',json,'ICODE check').then(function(res){
            ret = res;
            ret.success = true;
            defer.resolve(ret);
        },function(err){
            ret.success = false;
            defer.reject(err);
        });
        return defer.promise;
    }
}]);