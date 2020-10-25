(function(){
	var myApp = angular.module('myApp', ['objectFilters','lumx','ui.router','ngResource','object','angularFileUpload','ng.ueditor']); 
	//http://ui.lumapps.com/
	//https://docs.angularjs.org/api/ngResource/service/$resource
	//http://angular-ui.github.io/ui-router/site/#/api

	myApp.config(function($stateProvider, $urlRouterProvider) {

	    // For any unmatched url, redirect to /
	    $urlRouterProvider.otherwise("/");
	   
	    // Now set up the states
		$stateProvider
			.state('defaul', {
	    		url: "/",
	    		templateUrl: "views/main/index.html"
	  		})
	  		.state('object', {
	    		url: "/object/:objectName",
	    		params: {'category_id':{type: 'string'}},
	    		templateUrl: "views/object/object_list.html",
	    		controller:'ObjectController'
	    	})
	    	.state('objectDetail', {
	    		url: "/object/:objectName/:objectId",
	    		templateUrl: "views/object/object_show.html",
	    		controller:'ObjectController'
	    	}).state('objectRelate', {
	    		url: "/object/:objectName/:objectId/:objectRelate",
	    		templateUrl: "views/object/object_list.html",
	    		controller:'ObjectController'
	    	}).state('objectRelateDetail', {
	    		url: "/object/:objectName/:objectId/:objectRelate/one",
	    		templateUrl: "views/object/object_show.html",
	    		controller:'ObjectController'
	    	});
  	});

  	myApp.factory('API', ['$resource', function($resource) {
		return $resource('/api/:object/:id', null,
    	{	
        	'update': 	{ method:'PUT' 	},
        	'query':  	{ method:'GET'  }
    	});
	}]);

	myApp.controller('GlobalController',function($location,$scope,$http,$state,$rootScope,$urlRouter,$upload,API,LxNotificationService){
        
        $scope.menuData       = menuData;
        $scope.globalData     = globalData;
        $scope.favorMenuData  = favorMenuData;
        $scope.objectMenuData = objectMenuData;

        $scope.menuLess       = false;
        $scope.objectName     = "";
        $scope.objectId       = 0;
        
        //接口刷新快捷导航
        API.get({"object":"favormenu"},function(returnData){
			console.log("快捷导航：",returnData);
	        if(!returnData.error){
	        	$scope.favorMenuData = returnData.result;
	        }
      	});

	    //注销
	    $scope.name = kget("name");
	    $scope.logout = function(){
	      $http.get('api/user/logout').success(function(data){
	          if(!data.error ){
	              window.location.href = '/';             
	          }else{
	              LxNotificationService.error(data.message);
	          }
	      });
	    } 

	    //跳转
    	$scope.stateGo = function(menu){
    		if($scope.objectId != 0){
	    		angular.forEach(menu.state[1],function(val,key){
					if(key == "objectId") menu.state[1][key] = $scope.objectId;
	    		});
	    	}
	    	console.log("---------------------------------new state",menu);
    		$state.go(menu.state[0],menu.state[1],{reload:true});
    		return false;
    	}
    	$scope.go = function(path){
    		$location.path(path);

    	}
		$('body').show();
    	//面包屑导航
        $rootScope.$on('$stateChangeSuccess',function(event, toState, toParams, fromState, fromParams){
        	
			$scope.breadcrumbData = [];
			for(var i=0;i<menuData.length;i++){
				if($scope.breadcrumbData != "") break;
				menuOne = menuData[i];
				if(menuOne.son){
				for(var j=0;j<menuOne.son.length;j++){
					if($scope.breadcrumbData != "") break;
					menuTwo = menuOne.son[j];
					if($state.is(menuTwo.state[0],menuTwo.state[1])){
			  			$scope.breadcrumbData = [angular.copy(menuOne),angular.copy(menuTwo)];break;
			  		}
			  		var objectName = menuTwo.state[1]["objectName"];
			  		if(typeof objectMenuData[objectName] != "undefined"){
				  		for(var z=0;z<objectMenuData[objectName].length;z++){
				  			if($scope.breadcrumbData != "") break;
				  			menuThree = objectMenuData[objectName][z];
				  			menuThree.state[1]['objectName'] = objectName;
				  			if(typeof toParams["objectId"] != "undefined"){
								menuThree.state[1]['objectId'] = toParams["objectId"];
							}
							if($state.is(menuThree.state[0],menuThree.state[1])){
								$scope.breadcrumbData = [angular.copy(menuOne),angular.copy(menuTwo),angular.copy(menuThree)]; break;
					  		}
						}
					}
						
				}}
			}
			
			if($scope.breadcrumbData.length == 0){
				return;
			}

			var breadcrumbLength = $scope.breadcrumbData.length-1;
			var menuCurrent  	 = $scope.breadcrumbData[breadcrumbLength];
			$scope.objectName    = menuCurrent.state[1]["objectName"];

			if(typeof toParams["objectId"] != "undefined"){
				$scope.objectId = toParams["objectId"];

				API.get({"object":toParams["objectName"],"id":$scope.objectId},function(returnData){
					console.log("本体数据-面包屑：",returnData);
			        if(!returnData.error){
			        	$scope.breadcrumbData.push(angular.copy(menuCurrent));
			            $scope.breadcrumbData[breadcrumbLength].name = returnData.result[menuCurrent.name_field];
			          	$scope.breadcrumbData[breadcrumbLength].state = ["objectDetail",{'objectName':menuCurrent.state[1].objectName,'objectId':'id'}]
			        }
		      	});
			
			}else{
				$scope.objectId = 0;
			}


		});


    });


	myApp.directive('ngEnter', function () {
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


})();
