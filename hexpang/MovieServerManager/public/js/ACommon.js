/**
 * AngularJS 通用方法封装
 * By HexPang
 * Update : 2016-08-06
 *   可完全独立使用
 * Update : 2016-08-05
 *   增加 CommonService.Http.Put
 */
var app = angular.module('App', ['angularFileUpload']);

var entry = "dashboard/api/";

app.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
});

app.factory('CommonService', function($rootScope,$http,$location) {
    var factory = {};
    factory.Navigation = {
        Back : function(){
            if(history.length > 0){
                history.go(-1);
            }else{

            }
        },
        URL : function(url){
            $location.url(url);
        }
    };
    factory.Http = {
        Get:function(action,param,callback){
            factory.Core.request_get(action,param,callback);
        },
        Post:function(action,data,callback){
            factory.Core.request_post(action,data,callback);
        },
        Put:function(action,param,callback){
            factory.Core.request_put(action,param,callback);
        },
        File:function(url,callback){
            factory.Core.request_file(url,callback);
        }};
    factory.Core = {
        create_post_header:function(){
            return {
                'Content-Type':'application/x-www-form-urlencoded'
            }
        },
        create_header:function(){

        },
        request_put:function(action,param,callback){
            if(typeof param == 'function'){
                callback = param;
                param = '';
            }else if(typeof param == 'object'){
                var p = "";
                for(var i in param){
                    if(p != ""){
                        p += "&";
                    }
                    p += i + "=" + encodeURIComponent(param[i]);
                }
                param = p;
            }
            var params = action.split("/");
            var p = location.href.split("/");
            action = entry + action;
            var url = action + "/";
            var token = clientToken ? "token=" + clientToken : "";
            if(param != ""){
                url += "?" + param;
            }
            $http.put(url,{headers:factory.Core.create_post_header()}).success(function(data){
                callback(data);
            }).error(function(data, status, headers, config){
                handle_error(data);
            });
        },
        request_file:function(url,callback){
            $http.get(url).success(function(data){
                callback(data);
            });
        },
        request_post:function($action,$data,callback){
            if($action.substr(0,4) != "http"){
                $action = entry + $action;
            }
            console.log($.param($data));
            $http({
                method  : 'POST',
                url     : $action,
                data    : $.param($data),  // pass in data as strings
                headers : factory.Core.create_post_header()
            })
                .success(function(data) {
                    callback(data);
                }).error(
                function(err,status,headers,config){
                    callback({error:err});
                });
        },
        request_get:function(action,param,callback){
            if(typeof param == 'function'){
                callback = param;
                param = '';
            }else if(typeof param == 'object'){
                var p = "";
                for(var i in param){
                    if(p != ""){
                        p += "&";
                    }
                    p += i + "=" + encodeURIComponent(param[i]);
                }
                param = p;
            }
            var params = action.split("/");
            var p = location.href.split("/");
            action = entry + action;
            var url = action + "/";
            //var token = clientToken ? "token=" + clientToken : "";
            if(param != ""){
                url += "?" + param;
            }
            $http.get(url,{headers:factory.Core.create_header()}).success(function(data){
                callback(data);
            }).error(function(data, status, headers, config){
                handle_error(data);
            });
        }
    }
    return factory;
});

app.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.ngEnter);
                });

                event.preventDefault();
            }
        });
    };
});