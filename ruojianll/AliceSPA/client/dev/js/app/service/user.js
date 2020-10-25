/**
 * Created by kunono on 2015/1/29.
 */
app.service('user',['$cookieStore','base','common','$q','data','component_ICODE','component_MCODE','log','$state',function($cookieStore,base,common,$q,data,component_ICODE,component_MCODE,log,$state){
    return{
        register:register,
        refreshState:refreshState,
        login:login,
        logout:logout,
        isExist:isExist,
        makeLogin:makeLogin
    };

    function hasPermissionAdminAccess(){

        var defer = $q.defer();
        base.post('/admin/hasPermission/access',{},'admin hasPermission access').then(function(res){
            defer.resolve(res.hasPermission);
        },function(err){
            defer.reject(err);
        });
        return defer.promise;
    }
    function refreshState(){
        var defer = $q.defer();
        data.user.data.stateRefreshed = false;
        base.get('/user/login-state','user login-state').then(function(res) {
            data.user.data = res;
            data.user.stateRefreshed = true;
            log.log('user refreshState : ');
            log.log(data.user.data);
            hasPermissionAdminAccess().then(function(res){
                data.user.isAdmin = res;
            });

            data.user.onRefreshState = _.filter(data.user.onRefreshState,function(func){return func()!=true;});
            console.log(data.user.onRefreshState);
            defer.resolve(res);
        });
        return defer.promise;
    }
    function register(name,pwd,mobilephone,e_mail){
        var json = {};
        json.name = name;
        json.password = common.passwordHash(pwd);
        json.mobilephone = mobilephone;
        json.e_mail = e_mail;
        component_MCODE.setRequestMCODE(json);
        var defer = $q.defer();
        base.post('/user/register',json,'user register').then(function(res){
            data.user.stateRefreshed = false;
            refreshState();
            defer.resolve(res);
        },function(err){
            defer.reject(err);
        });

        return defer.promise;

    }
    function login(name,mobilephone,pwd){
        var json={};
        json.name = name;
        json.mobilephone = mobilephone;
        json.password = common.passwordHash(pwd);
        component_ICODE.setRequestICODE(json);
        var defer = $q.defer();
        base.post('/user/login',json,'user login').then(function(res){
            defer.resolve();
            refreshState();
        },function(err){
            refreshState();
            defer.reject(err);
        });
        return defer.promise;
    }

    function logout(){
        base.get('/user/logout','user logout').then(function(res){
                refreshState();
        });
    }
    function isExist(field,value){
        var json = {};
        json.field = field;
        json.value = value;
        var defer = $q.defer();
        base.post('/user/isExist',json,'user isExist').then(function(res){
            defer.resolve(res.isExist);
        },function(err){
            defer.reject(err);
        });
        return defer.promise;
    }
    function makeLogin(){
        if(data.user.stateRefreshed){
            makeLoginCallback();
            return;
        }

        data.user.onRefreshState.push(makeLoginCallback);
    }
    function makeLoginCallback(){
        if(!data.user.data.isLoggedIn){
            $state.go('user.login');
        }
        return true;
    }
}]);