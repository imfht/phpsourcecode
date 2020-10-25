/**
 * Created by spatra on 15-5-20.
 */

define(['routeApp/module', 'library/classHelper'], function(module, classHelper){

  /**
   * 路线列表页面所对应的控制器
   */
  module.controller('RouteListController', ['$scope', '$state', 'routeList', 'RouteService',
    function($scope, $state, routeList, RouteService){
      $scope.routeList = routeList;
      $scope.editing = false;

      $scope.editRoute = function(routeId){
        $state.go('edit', {
          routeId: routeId
        });
      };

      $scope.deleteRoute = function(routeId) {
        if (!confirm('您确定要删除吗？')) return;

        $scope.editing = true;
        RouteService.accessor.destroy(routeId)
          .then(function () {
            return RouteService.getMyRoutes();
        })
          .then(function(resp){
            $scope.editing = false;
            $scope.routeList.length = 0;
            Array.prototype.push.apply($scope.routeList, resp.data);
          }, function(resp){
            $scope.editing = false;
            console.error(resp);
            if( resp.data && resp.data.error ){
              alert(resp.data.error);
            } else{
              alert('删除失败');
            }
          });
      };
  }]);

  /**
   * 新建路线列表所对应的控制器
   */
  module.controller('RouteCreatingController', ['$scope', '$state', 'RouteService',
    function($scope, $state, RouteService){
      $scope.route = {};

      $scope.resetRoute = function(){
        $scope.route.name = $scope.route.description = null;
      };

      $scope.addRoute = function(validValue){
        if( ! validValue ) return ;

        RouteService.accessor.store($scope.route)
          .success(function(data){
            $state.go('edit', {
              routeId: data._id
            });
          })
          .error(function(data){
            alert(data.error || '添加失败');
            console.error(data);
          });
      };
    }]);

  /**
   * 路线编辑页面所对应的控制器
   */
  module.controller('RouteEditingMainController', ['$scope', 'RouteService', 'currentRoute',
    function($scope, RouteService, currentRoute){
      $scope.route = currentRoute;
      $scope.updating = false;

      $scope.save = function(){
        RouteService.updateWithStatus($scope.route, $scope);
      };
  }]);

  /**
   * 路线-日程列表 中所对应的控制器
   */
  module.controller('RouteEditingDailyMainController', ['$scope', '$state', 'RouteDailyService', 'currentRoute', 'currentRouteId',
    function($scope, $state, RouteDailyService, currentRoute, currentRouteId){
      $scope.daily = currentRoute.daily;
      $scope.currentRouteId = currentRouteId;
      $scope.deleting = false;

      $scope.remove = function(id, index){
        if( !confirm('您确实要删除吗？') ) return;

        RouteDailyService.deleteWithStatus(id, $scope).success(function(){
          $scope.daily.splice(index, 1);
        });
      };

      var defaultRemark = '新建日程';
      $scope.create = function(){
        RouteDailyService.create(defaultRemark, $scope).success(function(data){
          $scope.daily.push(data);

          $state.go('edit.daily.show', {
            routeId: currentRouteId,
            dailyIndex: $scope.daily.length - 1
          });
        });
      };
  }]);

  /**
   * 此控制对应 路线-日程 具体查看的页面
   */
  module.controller('RouteEditingDailySightController', ['$scope', 'currentDailyIndex', 'currentDaily', 'currentRoute', 'RouteDailyService',
    function($scope, currentDailyIndex, currentDaily, currentRoute, RouteDailyService){
      $scope.updating = false;
      $scope.currentDaily = classHelper.clone(currentDaily);
      $scope.newSightName = '';
      $scope.newSightCoordinates = [];

      //保存更改
      $scope.save = function(){
        console.log($scope.currentDaily);
        RouteDailyService.saveWithStatus($scope.currentDaily['_id'], $scope.currentDaily, $scope).success(function(data){
          classHelper.extend(currentDaily, data);
        });
      };

      //移除一个景点
      $scope.removeSight = function(index){
        var sights = $scope.currentDaily['sights'];

        if( sights && sights.length > index){
          sights.splice(index, 1);
        }
      };

      //新增一个景点
      $scope.addSight = function(){
        $scope.currentDaily['sights'] = $scope.currentDaily['sights'] || [];

        $scope.currentDaily['sights'].push({
          name: $scope.newSightName,
          check_in: false,
          loc: {"type": "Point", "coordinates": $scope.newSightCoordinates}
        });

        $scope.newSightName = '';
        $scope.newSightCoordinates = [];
      };
  }]);

  module.controller('RouteEditingTransportationMainController', ['$scope', 'currentRoute', 'RouteTransportationService',
    function($scope, currentRoute, RouteTransportationService){
      $scope.currentRouteId = currentRoute['_id'];
      $scope.currentTransportation = currentRoute['transportation'];
      $scope.editing = false;

      $scope.remove = function(id, index){
        if( !confirm("您确实要删除吗？") ) return;

        RouteTransportationService.getAccessorWithStatus()
          .destroy($scope, id)
          .success(function(){
            $scope.currentTransportation.splice(index, 1);
          });
      };
    }]
  );

  /**
   * 对应 路线-交通方式 新增交通方式的页面的控制器
   */
  module.controller('RouteEditingTransportationCreatingController', ['$scope', '$state', 'currentRoute', 'RouteTransportationService',
    function($scope, $state, currentRoute, RouteTransportationService){
      $scope.editing = false;

      function resetFiled(){
        $scope.from = {name: '', coordinates: []};
        $scope.to =  {name: '', coordinates: []};
        $scope.method = 'drive';
        $scope.policy = null;
      }

      function buildStoreData(){
        return {
          'from_name': $scope.from['name'],
          'from_loc': {"type": "Point", "coordinates": $scope.from['coordinates']},
          'to_name': $scope.to['name'],
          'to_loc': {"type": "Point", "coordinates": $scope.to['coordinates']},
          'description': {},
          'prize': 0,
          'consuming': 0
        };
      }

      resetFiled();

      $scope.reset = resetFiled;

      $scope.save = function(){
        if( $scope.from['coordinates'].length === 0 || $scope.to['coordinates'].length === 0 ){
          if( $scope.from['coordinates'].length === 0 ) $scope.from['name'] = '';
          if( $scope.to['coordinates'].length === 0 ) $scope.to['name'] = '';
          alert('无匹配地址');

        } else {
          var add = buildStoreData();

          add['description']['type'] = $scope.method;
          if( $scope.policy && $scope.method !== 'walk' ){
            add['description']['policy'] = [$scope.policy];
          }

          RouteTransportationService.getAccessorWithStatus().store($scope, add).success(function(data){
            currentRoute['transportation'].push(data);
            $state.go('edit.transportation', {
              routeId: currentRoute['_id']
            });
          });
        }
      };

    }]
  );

  /**
   * 新建路线游记的页面对应的控制器
   */
  module.controller('EditJottingCreatingController', ['$scope', '$state', '$stateParams', 'currentRoute', 'RouteNoteService',
    function($scope, $state, $stateParams, currentRoute, RouteNoteService){
      RouteNoteService.accessor['setParentResourceId']($stateParams);
      $scope.newNote = {
        content: '',
        onlyMe: false,
        loc_name: '',
        loc: null
      };
      $scope.uploadImages = [];
      $scope.creating = false;

      /*
        监听新增的文件变动，并添加到预览
       */
      $scope.addingFiles = [];
      $scope.$watch('addingFiles', function(){
        var files = $scope.addingFiles;
        if( files && files.length ){
          Array.prototype.push.apply($scope.uploadImages, files);
        }
      });

      /*
        点击显示图片的放大图
       */
      $scope.showingImage = false;
      $scope.currentIndex = 0;
      $scope.showImage = function(indexOfUploadImages){
        $scope.showingImage = true;
        $scope.currentIndex = indexOfUploadImages;
      };

      /*
         移除选中的图片
       */
      $scope.remove = function(indexOfUploadImages){
        if( $scope.uploadImages && $scope.uploadImages.length ){
          $scope.uploadImages.splice(indexOfUploadImages, 1);
        }
      };

      /*
        保存游记
       */
      $scope.saveNote = function(){
        var newData = classHelper.clone($scope.newNote);
        newData.images = $scope.uploadImages.concat();

        RouteNoteService.saveNode(newData, $scope).error(function(data){
          console.error(data);
          var errorMsg = (data && data.error) || '新建失败';
          alert(errorMsg);
        }).success(function(){
          $state.go('edit', {
            routeId: currentRoute['_id']
          });
        });
      };
    }]
  );

  /**
   *
   */
  module.controller('ShowInfoDailyController', ['$scope', 'currentRoute', '$window', '$state', '$stateParams', 'RouteDailyService', 'SightService',
    function($scope, currentRoute, $window, $state, $stateParams, RouteDailyService, SightService){
      $scope.daily = currentRoute.daily;
      $scope.dailyIndex = 0;
      $scope.currentDaily = $scope.daily[$scope.dailyIndex];

      $scope.total = currentRoute.daily.length;
      $scope.delta = 0;

      if( $scope.total >= 5 ){
        $scope.dailyMock = [1, 2, 3, 4, 5];
      } else{
        $scope.dailyMock = $scope.daily;
      }

      $scope.$watch('dailyIndex', function(newValue, oldValue){
        if( newValue !== oldValue ){
          $scope.currentDaily = $scope.daily[newValue];
          if( newValue > 4 ){
            $scope.delta = newValue - 4;
          } else {
            $scope.delta = 0
          }
        }
      });

      $scope.selectedDaily = function(index){
        $scope.dailyIndex = index;
      };


      $scope.nextDaily = function(){
        if( $scope.dailyIndex < $scope.total - 1){
          ++$scope.dailyIndex;
        } else {
          $scope.dailyIndex = 0;
        }
      };

      $scope.prevDaily = function(){
        if( $scope.dailyIndex > 0 ){
          --$scope.dailyIndex;
        } else {
          $scope.dailyIndex = $scope.total - 1;
        }
      };

      $scope.showSight = function(currentSight){
        console.log(currentSight);
        if( currentSight.hasOwnProperty('sights_id') && currentSight['sights_id'] ){
          $state.go('sight.show', {
            'sightId': currentSight['sights_id'],
            'referrer': 1
          });
        } else {
          var choice = $window.confirm('地点 "' + currentSight['name'] + '" 还没有关联的景点，您要创建吗？');

          if( choice ){
            var encodedName = encodeURIComponent(currentSight['name']);
            var currentStateParams = classHelper.clone($stateParams);

            SightService.setCreatedCallback(function(data, callback){
              currentSight['sights_id'] = data['_id'];
              RouteDailyService.updateWithParams(currentStateParams, $scope.currentDaily).success(callback);
            });

            localStorage.setItem(encodedName, JSON.stringify({
              'name': currentSight['name'],
              'loc': currentSight['loc']
            }));

            $state.go('sight.creating', {
              'name': encodedName,
              'referrer': 1
            });
          }
        }
      }
    }]
  );

  /**
   *
   */
  module.controller('SightCreatingController', ['$scope', '$stateParams', '$state', 'tmpInfo', 'SightService',
    function($scope, $stateParams, $state, tmpInfo, SightService){
      $scope.add = classHelper.clone(tmpInfo);
      $scope.loc = {
        'coordinates': [],
        'type': 'Point'
      };

      if( typeof $scope.add.loc === 'object' ){
        $scope.currentCoordinateStr = $scope.add.loc.coordinates[1] + ',' + $scope.add.loc.coordinates[0];
        $scope.loc['coordinates'] = classHelper.clone($scope.add.loc.coordinates);
      } else {
        $scope.currentCoordinateStr = '0,0';
      }


      $scope.$watch('currentCoordinateStr', function(newValue, oldValue){
        if( newValue === oldValue ) return;

        var parties = $scope.currentCoordinateStr.split(',');
        var coordinates = new Array(2);
        coordinates[1] = parseFloat(parties[0]);
        coordinates[0] = parseFloat(parties[1]);

        if( !isNaN(coordinates[0] && !isNaN(coordinates[1])) ){
          $scope.loc['coordinates'] = coordinates;
        }

        console.log($scope.loc);
      });

      $scope.save = function(){

        $scope.add.loc = $scope.loc;
        SightService.accessor.store($scope.add).then(function(resp){
          var data = resp.data;console.log(data);

          SightService.runCreatedCallback(data, function(){
            if( $stateParams['referrer'] ){
              history.back();
            } else {
              $state.go('list');
            }
          });
        }, function(resp){
          alert('出错了～');
        });

      };

  }]);
  return module;
});