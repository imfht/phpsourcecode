<!DOCTYPE html>
<html lang="en" ng-app="myApp" ng-controller="appCtrl" >
    <title ng-bind="globalData.site_title"></title>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.png" />

    <link rel="stylesheet" href="./bower_components/lumx/dist/lumx.css">
    <link rel="stylesheet" href="./css/app.css">
</head>

<body  style="position:relative;display:none;">

<div flex-container="row" style="background:#eee;height:100%">
    <div flex-item="12">
        <div class="toolbar bgc-blue-grey-800 tc-white-1" style="height:48px;padding:4px 20px;">
            <span class="toolbar__label fs-title" ng-bind="globalData.site_title"></span>
        </div>
        <div class="p++ card tc-black-1" flex-container="row" flex-gutter="24" style="position:absolute;top:40%;right:25%;">
            <div flex-item="12">
                <form>
                    <lx-text-field label="用户名：" fixed-label="true" icon="account">
                        <input type="text" ng-model="userName">
                    </lx-text-field>
                    <lx-text-field label="密码:" fixed-label="true" icon="lock">
                        <input type="password" ng-model="passWord">
                    </lx-text-field>
                    <button style="float:right;" class="btn btn--m btn--blue-grey btn--raised" ng-click="login()" lx-ripple>登录</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="./bower_components/jquery/dist/jquery.min.js"></script>
<script src="./bower_components/velocity/velocity.min.js"></script>
<script src="./bower_components/moment/min/moment-with-locales.min.js"></script>
<script src="./bower_components/angular/angular.min.js"></script>
<script src="./bower_components/lumx/dist/lumx.min.js"></script>
<script src="./config.js"></script>
<script src="./app.js"></script>
<script type="text/javascript">
var myApp = angular.module('myApp',['lumx']);

myApp.controller('appCtrl',['$scope','$http','LxNotificationService',function($scope,$http,LxNotificationService) { 
    $scope.globalData = globalData;
    $scope.userName   = window.localStorage.getItem( "name" );

    $scope.login = function(){
        if(!$scope.userName || !$scope.passWord){
            LxNotificationService.error("登录信息不完整。");
            return false;
        }

        var login_data = {
            'userName' : $scope.userName,
            'passWord' : $scope.passWord
        };

        $http.post('api/user/login', login_data).success(function(data){
            console.log("登陆结果",data);
            if(!data.error ){
                window.localStorage.setItem( "name" , login_data.userName );
                window.location.href = '/';             
            }else{
                LxNotificationService.error(data.message);
            }
        });
    }
    $('body').show();
}]); 
</script>
</body>
</html>

