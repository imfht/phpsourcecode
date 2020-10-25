/**
 * Created by spatra on 15-5-24.
 */

define(['routeApp/module'], function(module){

  module.directive('baiduMapAutoComplete', function(){
    function getId($add){
      if( $add === true){
        ++getId.currentId;
      }

      return getId.prefix + getId.currentId;
    }

    getId.currentId = 0;
    getId.prefix = 'baidu-map-autocomplete-';

    return {
      restrict: 'EA',
      scope: {
        placeName: '=',
        placeCoordinates: '='
      },
      template: '<input type="text" ng-blur="blur()" placeholder="{{info}}"/>',
      link: function(scope, elem){
        var inputDom = elem[0].querySelector('input');

        inputDom.id = getId(true);

        inputDom.addEventListener('change', function(){
          scope.$apply(function(){
            scope.placeName = inputDom.value;
          });
        });

        var autoComplete = new BMap.Autocomplete({
          'input': getId()
        });

        scope.$watch('placeName', function(newValue){
          if( newValue ) return;

          inputDom.value = null;
        });

        scope.blur = function(){
          scope.$emit('baiduMapAutoComplete.blur', {
            id: inputDom.id
          });
        };

        var myGeo = new BMap.Geocoder();

        autoComplete.addEventListener('onhighlight', function(){
          if( !scope.info ) return;

          scope.$apply(function(){
            scope.info = '';
          })
        });

        autoComplete.addEventListener('onconfirm', function(event){
          var currentValue = event.item.value;
          var currentAddress = currentValue.province + currentValue.city +
            currentValue.district + currentValue.street + currentValue.business;

          myGeo.getPoint(currentAddress, function(point){
            if( point ){
              scope.$apply(function(){
                scope.placeName = currentAddress;
                scope.placeCoordinates = [point['lat'], point['lng']];
              })
            } else {
              scope.$apply(function(){
                scope.placeName = '';
                scope.placeCoordinates = [];
                scope.info = '没有匹配的地址'
              });
            }
          })
        });
      }
    };
  });

  /**
   * 此指令用于 路线-游记-新建， 该部分的图片放大预览
   */
  module.directive('imgSlider', ['tplsDir', '$window', function(tplsDir, $window){

    return {
      restrict: 'EA',
      scope: {
        imgFiles: '=',
        currentIndex: '=',
        enable: '='
      },
      templateUrl: tplsDir + 'img-slider-template.html',
      link: function(scope, elem){
        scope.imgFilesLength = (scope.imgFiles.length === undefined) ? -1 : scope.imgFiles.length;

        //监听图片列表的变化，并重新设定数量值
        scope.$watch('imgFiles', function(){
          scope.imgFilesLength = (scope.imgFiles.length === undefined) ? -1 : scope.imgFiles.length;
        }, true);

        //下一张图片
        scope.next = function(){
          if( scope.currentIndex >= scope.imgFilesLength -1 ){
            scope.currentIndex = 0;
          } else {
            ++scope.currentIndex;
          }
        };

        //上一张图片
        scope.prev = function(){
          if( scope.currentIndex === 0 ){
            scope.currentIndex = scope.imgFilesLength - 1;
          } else {
            --scope.currentIndex;
          }
        };

        //监听键盘事件，以切换图片
        function keyboardHandler(event){
          var clientRect = elem[0].getBoundingClientRect(),
            isHidden = (clientRect.width === 0 && clientRect.height === clientRect.width);

          if( isHidden ) return;

          var keyCode = event.keyCode || event.charCode;

          if( keyCode === 37 ){
            scope.$apply(function(){
              scope.prev();
            });
          } else if( keyCode === 39 ){
            scope.$apply(function(){
              scope.next();
            });
          }
        }

        var jQLiteWindow = angular.element($window);

        jQLiteWindow.on('keyup', keyboardHandler);
        scope.$on('$destroy', function(){
          jQLiteWindow.off('keyup', keyboardHandler);
        });
      }
    };
  }]);

  /**
   * 此指令用于 标记位置，可根据IP地址自动定位，也可以由用户自行输入
   */
  module.directive('baiduMapLocation', ['tplsDir', function(tplsDir){

    return {
      restrict: 'EA',
      templateUrl: tplsDir + 'baidu-map-location-template.html',
      scope: {
        locName: '=',
        loc: '='
      },
      link: function(scope, elem){
        //设置初始的提示信息
        scope.info = '正在定位...';
        scope.status = 'locating';

        scope.coordinates = null;
        scope.name = null;

        var localCity = new BMap.LocalCity();
        localCity.get(function(result){
          scope.$apply(function(){
            if( result && result.name ){
              scope.info = result.name;
              scope.status = 'done';
              scope.locName = result.name;
              scope.loc = {"type": "Point", "coordinates": [result.center.lng, result.center.lat]}
            } else {
              scope.info = '定位失败';
              scope.status = 'failure';
              scope.log_name = '';
              scope.loc = null;
            }
          });
        });

        scope.edit = function(){
          if( scope.status === 'locating' ) return;

          scope.status = 'editing';
        };

        scope.$watch('name', function(newValue, oldValue){
          if( (newValue !== oldValue) && scope.coordinates && scope.coordinates.length ){
            scope.locName = scope.name;
            scope.loc = {"type": "Point", "coordinates" : scope.coordinates.concat()}
            scope.info = scope.name;
            scope.status = 'done';
          }

          if( (newValue !== oldValue)  && ( !scope.coordinates || !scope.coordinates.length) ){
            scope.status = 'failure';
          }
        });

        scope.$on('baiduMapAutoComplete.blur', function(){

          if( (!scope.coordinates || !scope.coordinates.length) && scope.name && (scope.name !== scope.locName) ){
            scope.coordinates = scope.name = scope.locName = scope.loc = null;
            scope.status = 'failure';
            scope.info = '无匹配地址，请重试';
          } else {
            scope.status = 'done';
          }
        });
      }
    };
  }]);

  /**
   * 此指令实现了 路线-查看 页面当中的侧边栏
   */
  module.directive('routeSidebar', ['tplsDir', '$state', '$rootScope', function(tplsDir, $state, $rootScope){
    return {
      restrict: 'EA',
      templateUrl: tplsDir + 'route-sidebar-template.html',
      scope: {
        labelItems: '=',
        moduleName: '@'
      },
      link: function(scope){

        function highLight(currentState){
          var name = currentState.name;

          if( !scope.labelItems || !scope.labelItems.length || !name) return;

          for(var i = 0, length = scope.labelItems.length; i < length; ++i ){
            var currentLabelObj = scope.labelItems[i];

            if( name.indexOf(currentLabelObj['uiSref']) !== -1 ){
              currentLabelObj.active = true;
              highLight.last.active = false;
              highLight.last = currentLabelObj;
              break;
            }
          }
        }

        highLight.last = {};

        highLight($state.current);

        $rootScope.$on('$stateChangeSuccess', function(evt, toState){
          if( scope.moduleName.indexOf(toState['name'])){
            highLight(toState);
          }
        });
      }
    };
  }]);

  /**
   * 该指令用于按列表显示图片的缩略图，并提供放大显示的功能
   */
  module.directive('imgShower', ['tplsDir', function(tplsDir){

    return {
      restrict: 'EA',
      templateUrl: tplsDir + 'img-shower-template.html',
      scope: {
        images: '='
      },
      link: function(scope){
        scope.currentIndex = 0;
        scope.currentImage = scope.images[scope.currentIndex];
        scope.showing = false;

        var total = scope.images.length || 0;

        scope.selected = function(index){
          scope.currentIndex = index;
          scope.showing = true;
        };

        scope.$watch('currentIndex', function(newValue, oldValue){
          if( newValue === oldValue ) return;

          if( newValue < 0 ){
            scope.currentIndex = total - 1;
          } else if( newValue >= total ){
            scope.currentIndex = 0;
          } else {
            scope.currentIndex = newValue;
          }

          scope.currentImage = scope.images[scope.currentIndex];
        });

        scope.next = function(){
          ++scope.currentIndex;
        };

        scope.prev = function(){
          --scope.currentIndex;
        }
      }
    };
  }]);

  /**
   *
   */
  module.directive('baiduMapTransportation', function(){

    function getId(next){
      if( next === true ){
        ++getId.currentId;
      }

      return getId.currentId + getId.prefix;
    }

    getId.currentId = 0;
    getId.prefix = 'baidu-map-transportation-';

    function getSearchCond(obj, prop, stringName){
      if( !(typeof obj === 'object') || !obj.hasOwnProperty(prop) ) return;

      if( typeof obj[prop] === 'object' ){
        var loc = obj[prop];
        return new BMap.Point(loc['coordinates'][1], loc['coordinates'][0]);
      } else if( typeof obj[stringName] === 'string' ){
        return obj[stringName];
      } else {
        return null;
      }
    }

    function policyFormating(input, type){console.log(input);
      if( input && input.length ){
        return 'BMAP_' + type.toUpperCase() + '_POLICY_' + input[0]['name'].toUpperCase();
      } else {
        return undefined;
      }
    }

    return {
      restrict: 'EA',
      replace: true,
      template: '<div class="gt-directive-baidu-map-transportation"><div class="bmap-container"  style="width: 98%; margin: auto; height: 500px;"></div><div id="result-0"></div></div>',
      scope: {
        currentMethod: '='
      },
      link: function(scope, elem){
        var divDom = elem[0].querySelector('.bmap-container'), divId = getId(true);

        divDom.id = divId;

        var map = new BMap.Map(divId);

        scope.$watch('currentMethod', function(newValue, oldValue){
          var fromCond = getSearchCond(newValue, 'from_loc', 'from_name'),
            toCond = getSearchCond(newValue, 'to_loc', 'to_name');
          console.log(newValue);

          if( typeof fromCond === 'object' || typeof toCond === 'object' ){

            if(typeof fromCond === 'object' ){
              map.centerAndZoom( fromCond );
            } else {
              map.centerAndZoom( toCond );
            }
          } else {
            var localCity = new BMap.LocalCity();
            localCity.get(function(result){
              if( result ){
                map.centerAndZoom( new BMap.Point(result.center.lat, result.center.lng), 11);
              } else {
                map.centerAndZoom(new BMap.Point(116.404, 39.915), 11);
              }
            });
          }
          map.clearOverlays();

          var currentSearchObj = null, type = newValue['description']['type'];
          switch (type)
          {
            case 'drive':
              currentSearchObj = new BMap.DrivingRoute(map,
                {
                  renderOptions: {map: map, panel: "result-0", autoViewport: true},
                  policy: policyFormating(newValue['description']['policy'], type)
                });
              break;
            case 'bus':
              currentSearchObj = new BMap.TransitRoute(map,
                {
                  renderOptions: {map: map, panel: 'result-0'},
                  policy: policyFormating(newValue['description']['policy'], type)
                });
              console.log(fromCond);
              console.log(toCond);
              break;
            case 'walk':
              currentSearchObj = new BMap.WalkingRoute(map,
                {renderOptions: {map: map, panel: "result-0", autoViewport: true}});
              break;
            default :
              alert('数据有误！');
              break;
          }

          currentSearchObj && currentSearchObj.search(newValue['from_name'], newValue['to_name']);
        }, true);

      }
    };
  });

  /**
   *
   */
  module.directive('transportationSelector', ['tplsDir', function(tplsDir){

    return {
      restrict: 'EA',
      templateUrl: tplsDir + 'transportation-selector-template.html',
      scope:{
        transportationList: '=',
        currentMethod: '='
      },
      link: function(scope){
        if( scope.currentMethod === null ) return;

        scope.showCtrlBtn = (scope.transportationList.length >= 3) ? true : false;
        scope.mockArray = [0, 1, 2];
        scope.delta = 0;
        scope.expand = true;

        var innerDelta = 0;

        if( !scope.showCtrlBtn ){
          scope.mockArray.length = scope.transportationList.length;
        } else {

          function checkPreCtrl(){
            scope.showPrev = !!(innerDelta !== 0);
          }

          function checkNextCtrl(){
            scope.showNext = !!(innerDelta < scope.transportationList.length - 3);
          }

          checkPreCtrl();
          checkNextCtrl();

          scope.changeDelta= function(step){
            innerDelta += step;

            if( innerDelta === -1 ){
              innerDelta = 0;
            } else if( innerDelta > scope.transportationList.length - 3 ){
              innerDelta = scope.transportationList.length - 3;
            }

            scope.delta = innerDelta;
            checkPreCtrl();
            checkNextCtrl();
          }

        }

        scope.changeExpand = function(){
          scope.expand = !(scope.expand);
        };

        scope.changeCurrent = function(index){
          scope.currentMethod = scope.transportationList[index];
        };
      }
    };
  }]);

  module.directive('transportationMethodShow', function(){
    var kvSet = {
      'drive': '自驾',
      'bus': '公交',
      'walk': '步行'
    };

    var setLabel = function(value, scope){
      if( kvSet.hasOwnProperty(value) ){
        scope['label'] = kvSet[value]
      } else {
        scope['label'] = '';
      }
    };

    return {
      restrict: 'EA',
      template: '<span>{{label}}</span>',
      replace: true,
      scope: {
        value: '@'
      },
      link: function(scope){
        setLabel(scope.value, scope);

        scope.$watch('value', function(newValue, oldValue){
          if( newValue !== oldValue ){
            setLabel(scope.value, scope);
          }
        });
      }
    };
  });

  module.directive('transportationPolicySelector', function(){
    var methodToPolicy = {
      'drive':[
        {name: 'least_block', label: '躲避拥塞'},
        {name: 'least_distance', label: '最短距离'},
        {name: 'least_cost', label: '最少费用'},
        {name: 'least_time', label: '时间优先'}
      ],
      'bus': [
        {name: 'avoid_subway', label: '不含地铁'},
        {name: 'least_exchange', label: '最少换乘'},
        {name: 'least_walk', label: '最少步行距离'},
        {name: 'least_time', label: '时间优先'}
    ]
    };

    var setSelector = function(currentMethod, scope){
      if( methodToPolicy.hasOwnProperty(currentMethod) ){
        scope.policies = methodToPolicy[currentMethod];
        scope.showSelector = true;
      } else {
        scope.policies = [];
        scope.showSelector = false;
      }
    };

    return {
      restrict: 'EA',
      template: '<span ng-show="showSelector" >更多策略： <select ' +
                        ' ng-model="currentPolicy"' +
                        ' ng-options="policy.name as policy.label for policy in policies"></select></span>',
      scope: {
        currentMethod: '=',
        currentPolicy: '='
      },
      link: function(scope){console.log(scope.currentMethod);
        setSelector(scope.currentMethod, scope);

        scope.$watch('currentMethod', function(newValue, oldValue){
          if( newValue === oldValue ) return;

          setSelector(scope.currentMethod, scope);
        }, true);
      }
    };
  });

  /**
   *
   */
  module.directive('baiduMapLocationShower', function(){
    function getId(next){
      if( next === true ){
        ++getId.currentId;
      }

      return getId.currentId + getId.prefix;
    }

    getId.currentId = 0;
    getId.prefix = 'baidu-map-location-shower-';

    function setLocation(loc, bmap){
      if( !loc || !loc['coordinates'] ) return;

      var coordinates = loc['coordinates'];

      if( coordinates[0] !== -1 && coordinates[1] !== -1 ){
        var point = new BMap.Point(coordinates[1], coordinates[0]);
        var marker = new BMap.Marker(point);

        bmap.centerAndZoom(point, 18);
        bmap.addOverlay(marker);
        bmap.panTo(point);
      }
    }

    return {
      restrict: 'EA',
      scope: {
        'loc': '='
      },
      template: '<div class="gt-baidu-map-location-shower" style="height: 100%;width: 100%">' +
      '<div class="bmap-container" style="width: 100%; height: 100%"></div></div>',
      link: function(scope, elem){
        var bmapDom = elem[0].querySelector('.bmap-container'), id = getId(true);
        bmapDom.id = id;

        var map = new BMap.Map(id);

        setLocation(scope.loc, map);

        scope.$watch('loc', function(newValue){
          setLocation(scope.loc, map);
        }, true);
      }
    };
  });

  return module;
});