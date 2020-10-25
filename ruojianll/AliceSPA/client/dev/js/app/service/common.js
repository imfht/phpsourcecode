/**
 * Created by kunono on 2015/2/1.
 */
app.service('common',['config',function(config){
    return {
        passwordHash:passwordHash,
        generateAPI:generateAPI
    };
    function passwordHash(pwd){
        return hex_sha1(pwd);
    }
    function generateAPI(api){
        return config.baseUrl + config.baseAPI + api;
    }
}]);