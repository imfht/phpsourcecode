/**
 * Created by kunono on 2015/1/29.
 */
var app = angular.module('ruojian',['ui.router','ngCookies','angularFileUpload']);
app.value('config',{
    showLog:true,//show log for debug
    baseUrl:'http://localhost:8081',//baseUrl + baseAPI for api url
    baseAPI:'',
    baseData:'http://localhost:8080/data',//baseData for data url
    appTypes:{'IMAGE':{typeApi:'/image'}
    },
    cnzz_site_id:'',//CNZZ id
    withCredentials:true,//set true when CORS
    errors:{
        1:'JSON格式错误',
        2:'JSON字段不全',
        3:'手机验证码错误',
        4:'未获取手机验证码',
        5:'图片验证码错误',
        6:'没有权限',
        7:'服务器执行错误',
        8:'没有找到指定记录',
        9:'已存在指定记录',
        10:'数字无效',
        11:'商品数量不足',
        12:'验证码发送错误',
        13:'商品价格已变化',
        14:'用户名或验证码错误'
    },
    bannerTypes:[
        {type:1,text:'商品ID'},
        {type:2,text:'分类ID'}
    ],
    MCODE_length:6,
    ICODE_length:6
});
app.value('data',{
    user:{
        onRefreshState:[],
        isAdmin:false,
        data:{
            isLoggedIn:false
        }
    },
    cart:{
        data:{
            items:[]
        }
    },
    order:{
        states:{
            1:'待支付',
            2:'待发货',
            3:'待收货',
            4:'已收货'
        },
        data:{
            orders:[]
        }
    },
    address:{
        edit:{mode:0},
        region:{},
        data:[]
    }
});
app.service('base',['$http','config','common','$q','log',function($http,config,common,$q,log){
    return{
        get:get,
        post:post,
        getData:getData
    };
    function checkHttpResponse(resp,comment,isData){
        var defer = $q.defer();
        resp.success(function(res){
            if(isData){
                defer.resolve(res);
                return;
            }
            if(res.success == true){
                log.log('http check ' + comment+' success:');
                log.log(res);
                defer.resolve(res);
            }
            else{
                log.log('http check ' + comment+' success false:');
                log.log(res);
                for(var ind in res.errors){
                    log.log(config.errors[res.errors[ind]]);
                }

                defer.reject(res);
            }
        }).error(function(err){
            log.log('http check ' + comment+' error:');
            log.log(err);
            defer.reject(err);
        });
        return defer.promise;
    }
    function get(api,comment){
        log.log(api);
        return checkHttpResponse($http.get(common.generateAPI(api)),'get |' + comment,false);
    }
    function post(api,data,comment){
        log.log(api);
        log.log(data);
        return checkHttpResponse($http.post(common.generateAPI(api),data),'post |' + comment,false);
    }
    function getData(url,comment){
        log.log(url);
        return checkHttpResponse($http.get(config.baseData + url),'get Data |' + comment,true);
    }
}]);

app.config([
    '$stateProvider','$urlRouterProvider',
    function($stateProvider,$urlRouterProvider){


        $urlRouterProvider.otherwise('/index');
        $stateProvider.
                state('main',{
                    url:'/index',
                    templateUrl:'/template/home.html',
                    controller:'homeCtrl as vm'
                }).
                state('user',{
                    abstract:true,
                    url:'/user',
                    templateUrl:'template/user/user.html',
                    controller:'userCtrl as vm'
                }).
                state('user.register',{
                    url:'/register',
                    templateUrl:'/template/user/register.html'
                }).
                state('user.login',{
                    url:'/login',
                    templateUrl:'/template/user/login.html'
                }).
                state('cart',{
                    url:'/cart',
                    templateUrl:'/template/cart/cart.html',
                    controller:'cartCtrl as vm'
                }).
                state('order',{
                    url:'/order',
                    templateUrl:'/template/order/order.html',
                    controller:'orderCtrl as vm'
                }).
                state('pay',{
                    url:'/pay/{id:[0-9]+}-{price:[0-9\.]+}',
                    templateUrl:'/template/pay/pay.html',
                    controller:'payCtrl as vm'
                }).
                state('address',{
                    url:'/address',
                    templateUrl:'/template/address/address.html',
                    controller:'addressCtrl as vm'
                }).
                state('address.edit',{
                    url:'/edit',
                    templateUrl:'/template/address/add.html'
                }).
                state('admin',{
                    url:'/admin',
                    templateUrl:'/template/admin/admin.html',
                    controller:'adminCtrl as vm'
                }).
                state('admin.order',{
                    url:'/order',
                    templateUrl:'/template/admin/order.html',
                    controller:'adminOrderCtrl as vm'
                }).
                state('admin.product',{
                    url:'/product',
                    templateUrl:'/template/admin/product.html',
                    controller:'adminProductCtrl as vm'
                }).
                state('admin.story',{
                    url:'/story',
                    templateUrl:'/template/admin/story.html',
                    controller:'adminStoryCtrl as vm'
                }).
                state('admin.category',{
                    url:'/category',
                    templateUrl:'/template/admin/category.html',
                    controller:'adminCategoryCtrl as vm'
                }).
                state('admin.marketing',{
                    url:'/marketing',
                    templateUrl:'/template/admin/marketing.html',
                    controller:'adminMarketingCtrl as vm'
                }).
                state('admin.user',{
                    url:'/user',
                    templateUrl:'/template/admin/user.html',
                    controller:'adminUserCtrl as vm'
                }).
                state('product',{
                    url:'/product/{id:[0-9]+}',
                    templateUrl:'/template/product/product.html',
                    controller:'productCtrl as vm'
                }).
                state('story',{
                    url:'/story/{id:[0-9]+}',
                    templateUrl:'/template/story/story.html',
                    controller:'storyCtrl as vm'
                }).
                state('category',{
                    url:'/category/{id:[0-9]+}',
                    templateUrl:'/template/category/category.html',
                    controller:'categoryCtrl as vm'
            });
     //   $urlRouterProvider. otherwise('/inde');
    }]);

app.run(['user','cart','order','data','address','utility','$http','config',function(user,cart,order,data,address,utility,$http,config){
    $http.defaults.withCredentials = config.withCredentials;
    data.user.onRefreshState.push(function(){cart.refresh();});
    data.user.onRefreshState.push(function(){order.refresh();});
    data.user.onRefreshState.push(function(){address.refresh();});
    user.refreshState().then();
}]);

app.filter('to_trusted', ['$sce', function ($sce) {
    return function (text) {
        return $sce.trustAsHtml(text);
    };
}]);


