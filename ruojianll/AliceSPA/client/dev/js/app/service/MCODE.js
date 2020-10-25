/**
 * Created by kunono on 2015/3/4.
 */
app.service('MCODE',['$q','base',function($q,base){
    return {
        check:check,
        send:send
    };
    function check(MCODE){
        var json = {};
        json.MCODE = MCODE;
        var defer = $q.defer();
        base.post('/MCODE/check',json,'MCODE check').then(function(success){
            defer.resolve(success.correct);
        },function(err){
            defer.reject(err);
        });
        return defer.promise;
    }
    function send(ICODE,mobilephone){
        var json = {};
        json.ICODE = ICODE;
        json.mobilephone = mobilephone;
        return base.post('/MCODE/generate',json,'MCODE generate');
    }
}]);