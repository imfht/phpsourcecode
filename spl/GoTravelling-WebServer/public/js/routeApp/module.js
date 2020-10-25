/**
 * Created by spatra on 15-5-20.
 */

define(['library/classHelper', 'angular', 'angularUIRouter', 'ngFileUpload'], function(classHelper, angular){

  var module = angular.module('GoTravelling.RouteApp', ['ui.router', 'ngFileUpload']);

  module.constant('tplsDir', 'js/routeApp/tpls/');

  module.run(function(){
    //去掉加载动画
    var loadingBox = document.getElementById('loading-wrapper');
    loadingBox.style.display = 'none';
  });

  module.config(['$httpProvider', '$stateProvider', '$urlRouterProvider', 'tplsDir',
    function($httpProvider, $stateProvider, $urlRouterProvider, tplsDir){
      //设置CSRF TOKEN
      var csrfToken = document.querySelector('meta[name=csrf_token]').getAttribute('content');
      $httpProvider.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

      $urlRouterProvider.otherwise('/');

      //设置HTTP拦截器
      $httpProvider.interceptors.push('AuthInterceptor');

      /**
       * 对一些必须加载的资源的获取进行处理.
       */
      function defaultResourceResolve(ngPromise, errorMsg, treatedCallback){
        return ngPromise.then(function(resp){
          if( typeof treatedCallback === 'function' ){
            return treatedCallback(resp.data);
          } else {
            return resp.data;
          }
        }, function(resp){
          var eMsg = errorMsg || ('加载失败' + resp.data.error);
          alert(eMsg);
          console.error(resp.data);
        })
      }

      $stateProvider
        .state('list', {
          'url': '/',
          views: {
            '':{
              templateUrl: tplsDir + 'list.html',
              resolve: {
                routeList: ['RouteService', function(RouteService){
                  return defaultResourceResolve(RouteService.getMyRoutes());
                }]
              },
              controller: 'RouteListController'
            }
          }
        })
        .state('show', {
          'url': '/:routeId/show',
          views: {
            '':{
              templateUrl: tplsDir + 'show-layout.html'
            },
            'title@show': {
              templateUrl: tplsDir + 'show-title.html',
              controller: ['$scope', 'currentRoute', function($scope, currentRoute){
                $scope.currentRoute = currentRoute;
              }]
            },
            'sidebar@show': {
              template: '<div class="sidebar"><route-sidebar label-items="labelItems" module-name="show"></route-sidebar></div>',
              controller: ['$scope', function($scope){
                $scope.labelItems =[
                  {label: '日程景点', uiSref: 'show.daily'},
                  {label: '交通方式', uiSref: 'show.transportation'},
                  {label: '旅游小记', uiSref: 'show.jotting'}
                ];
              }]
            }
          },
          resolve: {
            'currentRoute': ['$stateParams', 'RouteService', function($stateParams, RouteService){
              return defaultResourceResolve(
                RouteService.accessor.show($stateParams['routeId'])
              );
            }]
          }
        })
        .state('show.daily', {
          'url': '/daily',
          views: {
            'content@show': {
              templateUrl: tplsDir + 'show-info-daily.html',
              controller: 'ShowInfoDailyController'
            }
          }
        })
        .state('show.transportation', {
          'url': '/transportation',
          'views': {
            'content@show': {
              templateUrl: tplsDir + 'show-info-transportation.html',
              controller: ['$scope', 'currentRoute', function($scope, currentRoute){
                $scope.transportation = currentRoute.transportation;
                if( $scope.transportation.length ){
                  $scope.currentMethod = currentRoute.transportation[0];
                } else {
                  $scope.currentMethod = null;
                }

                console.log($scope.transportation);
              }]
            }
          }
        })
        .state('show.jotting', {
          'url': '/jotting',
          'views': {
            'content@show': {
              templateUrl: tplsDir + 'show-info-jotting.html',
              controller: ['$scope', 'currentNotes', function($scope, currentNotes){
                $scope.notes = currentNotes;
              }]
            }
          },
          resolve: {
            currentNotes: ['$stateParams', 'RouteNoteService', function($stateParams, RouteNoteService){
              RouteNoteService.accessor['setParentResourceId']($stateParams);

              return defaultResourceResolve(RouteNoteService.accessor.get());
            }]
          }
        })
        .state('edit', {
          'url': '/:routeId/edit',
          'views': {
            '':{
              templateUrl: tplsDir + 'edit-layout.html'
            },
            'title@edit': {
              template: '<span class="text">{{route.name}}</span> <a class="btn back-btn" ui-sref="list"><i class="fa fa-reply"></i>返回我的路线</a>',
              controller: ['$scope', 'currentRoute', function($scope, currentRoute){
                $scope.route = currentRoute;
              }]
            },
            'content@edit': {
              templateUrl: tplsDir + 'edit-main-content.html',
              controller: 'RouteEditingMainController'
            }
          },
          resolve: {
            currentRouteId:['$stateParams', function($stateParams){
              return $stateParams.routeId;
            }],
            currentRoute: ['RouteService', 'currentRouteId', function(RouteService, currentRouteId){
              return defaultResourceResolve(
                RouteService.accessor.show(currentRouteId)
              );
            }]
          }
        })
        .state('edit.daily', {
          'url': '/daily',
          'views': {
            'title@edit': {
              template: '<span class="text">{{route.name}} -- 日程景点</span> <a class="btn back-btn" ui-sref="edit({routeId: route._id})"><i class="fa fa-reply"></i>返回上一页</a>',
              controller: ['$scope', 'currentRoute', function($scope, currentRoute){
                $scope.route = currentRoute;
              }]
            },
            'content@edit': {
              templateUrl: tplsDir + 'edit-daily-main.html',
              controller: 'RouteEditingDailyMainController'
            }
          }
        })
        .state('edit.daily.show', {
          'url': '/:dailyIndex',
          'views':{
            'title@edit': {
              template: '<span class="text">{{route.name}} -- 第{{currentDailyIndex}}天</span> <a class="btn back-btn" ui-sref="edit.daily({routeId: route._id})"><i class="fa fa-reply"></i>返回上一页</a>',
              controller: ['$scope', 'currentDailyIndex', 'currentRoute', function($scope, currentDailyIndex, currentRoute){
                $scope.currentDailyIndex = parseInt(currentDailyIndex) + 1;
                $scope.route = currentRoute;
              }]
            },
            'content@edit': {
              templateUrl: tplsDir + 'edit-daily-sight.html',
              controller: 'RouteEditingDailySightController'
            }
          },
          resolve: {
            currentDailyIndex: ['$stateParams', function($stateParams){
              return $stateParams['dailyIndex'];
            }],
            currentDaily: ['$state', 'currentDailyIndex', 'currentRoute', function($state, currentDailyIndex, currentRoute){
              if( currentRoute.daily[currentDailyIndex] ){
                return currentRoute.daily[currentDailyIndex];
              } else {
                $state.go('list');
              }
            }]
          }
        })
        .state('edit.transportation', {
          'url': '/transportation',
          'views': {
            'title@edit': {
              template: '<span class="text">{{route.name}} -- 交通方式</span> <a class="btn back-btn" ui-sref="edit({routeId: route._id})"><i class="fa fa-reply"></i>返回上一页</a>',
              controller: ['$scope', 'currentRoute', function($scope, currentRoute){
                $scope.route = currentRoute;
              }]
            },
            'content@edit': {
              templateUrl: tplsDir + 'edit-transportation-main.html',
              controller: 'RouteEditingTransportationMainController'
            }
          }
        })
        .state('edit.transportation.create', {
          'url': '/create',
          'views': {
            'title@edit':{
              template: '<span class="text">{{route.name}} -- 新建交通方式</span> <a class="btn back-btn" ui-sref="edit.transportation({routeId: route._id})"><i class="fa fa-reply"></i>返回上一页</a>',
              controller: ['$scope', 'currentRoute', function($scope, currentRoute){
                $scope.route = currentRoute;
              }]
            },
            'content@edit': {
              templateUrl:tplsDir + 'edit-transportation-create.html',
              controller: 'RouteEditingTransportationCreatingController'
            }
          }
        })
        .state('edit.transportation.show', {
          'url': '/:transportationIndex/show',
          'views': {
            'title@edit': {
              template: '<span class="text">{{route.name}} -- 查看交通方式</span> <a class="btn back-btn" ui-sref="edit.transportation({routeId: route._id})"><i class="fa fa-reply"></i>返回上一页</a>',
              controller: ['$scope', 'currentRoute', function($scope, currentRoute){
                $scope.route = currentRoute;
              }]
            },
            'content@edit': {
              templateUrl: tplsDir + 'edit-transportation-show.html',
              controller: ['$scope', 'currentTransportation', function($scope, currentTransportation){
                $scope.currentTransportation = currentTransportation;
              }]
            }
          },
          resolve: {
            currentTransportation: ['$stateParams', '$state', 'currentRoute', function($stateParams, $state, currentRoute){
              var current = currentRoute['transportation'][ $stateParams['transportationIndex'] ];

              if( current ){
                return current;
              } else {
                $state.go('edit.transportation', {
                  routeId: currentRoute['_id']
                });
              }
            }]
          }
        })
        //.state('edit.jotting', {
        //  'url': '/jotting',
        //  'views': {
        //    'title@edit': {
        //      template: '<span class="text">{{route.name}} -- 旅游小记</span> <a class="btn back-btn" ui-sref="edit({routeId: route._id})"><i class="fa fa-reply"></i>返回上一页</a>',
        //      controller: ['$scope', 'currentRoute', function($scope, currentRoute){
        //        $scope.route = currentRoute;
        //      }]
        //    },
        //    'content@edit': {
        //      template: '<p>暂不开放</p>'
        //    }
        //  }
        //})
        .state('edit.jotting', {
          'url': '/create',
          'views': {
            'title@edit': {
              template: '<span class="text">{{route.name}} -- 写游记</span> <a class="btn back-btn" ui-sref="edit({routeId: route._id})"><i class="fa fa-reply"></i>返回上一页</a>',
              controller: ['$scope', 'currentRoute', function($scope, currentRoute){
                $scope.route = currentRoute;
              }]
            },
            'content@edit': {
              templateUrl: tplsDir + 'edit-jotting-create.html',
              controller: 'EditJottingCreatingController'
            }
          }
        })
        .state('create', {
          'url': '/create',
          'views':{
            '':{
              templateUrl: tplsDir + 'create.html',
              controller: 'RouteCreatingController'
            }
          }
        })
        .state('sight', {
          'url': '/sight',
          'views': {
            '': {
              templateUrl: tplsDir + 'sight-layout.html'
            }
          },
          'abstract': true
        })
        .state('sight.show', {
          'url': '/show/:sightId/:referrer?',
          'views': {
            'title@sight': {
              templateUrl: tplsDir + 'sight-title.html',
              controller: ['$scope', '$state', '$stateParams', 'currentSight',
                function($scope, $state, $stateParams, currentSight){
                  $scope.titleName = currentSight.name;
                  console.log(currentSight);

                  $scope.goBack = function(){
                    if( $stateParams['referrer'] ){
                      history.back();
                    } else {
                      $state.go('list');
                    }
                  };
              }]
            },
            'content@sight': {
              templateUrl: tplsDir + 'sight-show.html',
              controller: ['$scope', 'currentSight', function($scope, currentSight){
                $scope.currentSight = currentSight;

                if( $scope.currentSight.images && $scope.currentSight.images.length ){
                  var randomIndex = Math.floor(Math.random() * ($scope.currentSight.images.length - 1));
                  $scope.showImage = $scope.currentSight.images[randomIndex];
                } else {
                  $scope.showImage = null;
                }
              }]
            }
          },
          resolve: {
            currentSight: ['$stateParams', 'SightService', function($stateParams, SightService){
              return defaultResourceResolve(SightService.accessor.show($stateParams['sightId']));
            }]
          }
        })
        .state('sight.creating', {
          url: '/creating/:name/:referrer?',
          views: {
            'title@sight': {
              templateUrl: tplsDir + 'sight-title.html',
              controller: ['$scope', '$state', '$stateParams', 'tmpSightName',
                function($scope, $state, $stateParams, tmpSightName){
                  $scope.titleName = '新建景点 -- ' + tmpSightName;

                  $scope.goBack = function(){
                    if( $stateParams['referrer'] ){
                      history.back();
                    } else {
                      $state.go('list');
                    }
                  };
                }]
            },
            'content@sight': {
              templateUrl: tplsDir + 'sight-create.html',
              controller: 'SightCreatingController'
            }
          },
          resolve: {
            tmpSightName: ['$stateParams', function($stateParams){
              return decodeURIComponent($stateParams['name']);
            }],
            tmpInfo: ['$q', '$stateParams', function($q, $stateParams){
              var defer = $q.defer(), tmpInfo = localStorage.getItem($stateParams['name']);

              if( tmpInfo ){
                tmpInfo = JSON.parse(tmpInfo);
                var coordinates = tmpInfo['loc']['coordinates'];
                var geocoder = new BMap.Geocoder();

                geocoder.getLocation(new BMap.Point(coordinates[1], coordinates[0]), function(rs){
                  var addressInfo = {
                    'address': rs.address || ''
                  };

                  if( typeof rs['addressComponents'] === 'object' ){
                    addressInfo['city'] = rs['addressComponents']['city'] || '';
                    addressInfo['province'] = rs['addressComponents']['province'] || '';
                  }

                 classHelper.extend(tmpInfo, addressInfo);

                  defer.resolve(tmpInfo);
                });
              } else {
                defer.resolve({});
              }

              return defer.promise;
            }]
          }
        });
    }
  ]);


  return module;
});
