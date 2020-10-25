/**
 * Created by kunono on 2015/2/7.
 */

app.service('log', ['config',function(config){
    return {
        log:log
    };
    function log(data){
        if(config.showLog){
            console.log(data);
        }
    }
}]);
