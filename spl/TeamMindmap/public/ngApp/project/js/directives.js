/**
 * Created by spatra on 14-12-3.
 */


define(['projectJS/module'], function(projectModule){

  /**
   * 项目模块当中的侧边栏
   */
  projectModule.directive('projectSidebar', ['$rootScope', '$state', '$stateParams', 'projectModuleBaseUrl',
    function($rootScope, $state, $stateParams, projectModuleBaseUrl){
      return {
        restrict: 'EA',
        templateUrl: projectModuleBaseUrl + '/tpls/project-sidebar.html',
        replace: true,
        scope: {},
        link: function(scope, element){
          //将当前项目id的信息注册到指令的作用域
          scope.currentProjectId = $stateParams.projectId;

          var startStatusName = $state.current.name;
          var $sidebarLis = $(element).find('li');

          //高亮当前项目
          var highLightItem = function(targetStatusName){
            var sidebarLis = $sidebarLis.get();
            var statusPattern = /(project.show(\.[\w]+)?)/;

            for( var i = 0; i < sidebarLis.length; ++i ){
              var $currentLi = $( sidebarLis[i] );
              var uiSref = $currentLi.find('a').attr('ui-sref');
              var status = uiSref.match(statusPattern);

              if( targetStatusName.indexOf(status[1]) !== -1 ){
                $currentLi.addClass('active');
                break;
              }
            }
          };

          highLightItem(startStatusName);

          $rootScope.$on('$stateChangeStart',function(evt, toState){
            $sidebarLis.removeClass('active');
            highLightItem( toState.name );
          });

        }
      }; //End of --> `return`
  }]); //End of --> projectSidebar

  /**
   * 实现任务的展开显示.
   *
   * @example
   *    <task-expander ng-repeat = "task in taskList" task-title="task.title">
   *      //这里放需要展开的内容
   *    </task-expander>
   */
  projectModule.directive('taskExpander', function(){
    return {
      restrict: 'EA',
      replace: true,
      transclude: true,  //可嵌套,为了将内容嵌套到列表中
      scope: {
        currentTask: '=currentTask',
        showDetail: '&showDetail'
      },
      template: '<ul class="list-group task-list">'
      + '<li class="list-group-item task-title" ng-class="{taskOpen: showMe}" ng-click="toggle()">{{ currentTask.name }}</li>'
      + '<li class="list-group-item task-body" ng-show="showMe" ng-transclude></li>'
      + '</ul>',
      link: function(scope){
        //showMe属性,boolean类型,用于决定是否显示内容
        scope.showMe = false;
        //toggle函数,转换showMe值
        scope.toggle = function() {
          scope.showMe = !scope.showMe;

          if( scope.showMe ){
            scope.showDetail();
          }
        };
      }
    };//End of --> `return`
  });//End of --> taskExpander

  /**
   * 项目图片选择框的指令
   */
  projectModule.directive('imageSlider', ['projectModuleBaseUrl', function(projectModuleBaseUrl){
    return {
      restrict: 'EA',
      replace: false,
      templateUrl: projectModuleBaseUrl + 'tpls/image-slider.html',
      scope: {
        currentCover: '=cover'
      },
      link: function(scope, element){

        //DOM准备好之后调用jQuery插件 bxSlider
        element.ready(function(){
          console.log();
          $( "." + element[0].childNodes[0].getAttribute('class') ).bxSlider();

          //添加点击事件到fa图片,触发点击事件时更改主图片
          $('.project-image-bxslider i.fa').click(function(){
            //点击时改变主图片,并使选中元素高亮

            var self = this;

            scope.$apply(function(){
              var faClass = /fa(-[a-zA-Z]+)+/;
              var imageClass = $(self).attr('class');
              var result = imageClass.match(faClass);

              scope.currentCover = result[0];
            });
          });
        });
      }//End of link

    };//End of `return`
  }]);//End of imageSlider

  /**
   * 成员选择指令: 通过该项目的所有成员和已经被选择的成员,实现下拉选择成员的效果
   * scope绑定: addableMembers,selectedMembers
   * @example:
   *    //其中projectMembers,appointedMembers是外部作用域中的属性
   *    <member-selection addable-members="projectMembers" selectedMembers="appointedMembers"></member-selection>
   *
   */
  projectModule.directive('memberSelection',['projectModuleBaseUrl',function(projectModuleBaseUrl){
    return {
      restrict: 'EA',
      replace: false,
      scope: {
        allList: '=addableMembers',
        selectedList: '=selectedMembers',
        editable: '='
      },
      templateUrl: projectModuleBaseUrl + 'tpls/member-selection.html',
      link: function(scope) {
        /*
          设置某些变量的默认值
         */
        scope.selectedList = scope.selectedList || [];

        /**
         * 返回currentMember返回在selectedList中的下标位置,否则返回-1
         * @param currentMember
         * @returns {*}
         */
        function indexOfSelected(currentMember) {

          for(var i=0;i<scope.selectedList.length;i++) {
            //如果在selectedList中存在该成员,则从selectedList中删除
            if(scope.selectedList[i].id === currentMember.id) {
              return i;
            }

          }
          return -1;
        }

        scope.toggleAppointed = function(currentMember){
          var curIndex = indexOfSelected(currentMember);
          if(curIndex > -1) {
            scope.selectedList.splice(curIndex,1);
          }else {
            scope.selectedList.push(currentMember);
          }

        };

        scope.isSelected = function(currentMember) {
            return indexOfSelected(currentMember) > -1 ? true : false;
        };

      }//End of --> function:link
    };//End of --> return
  }]);//End of --> memberSelection

  projectModule.directive('handlerSelection', ['projectModuleBaseUrl', function(projectModuleBaseUrl){
    return {
      restrict: 'EA',
      replace: false,
      transclude: true,
      scope: {
        memberList: '=',
        handler: '='
      },
      templateUrl: projectModuleBaseUrl + 'tpls/handler-selection.html',
      link: function(scope){

       if( scope.handler === null ){
         scope.handler = scope.memberList[0];
       }

        scope.selectedHandler = function(currentMember){
          scope.handler = currentMember;
        }

      }
    }
  }]);


  /**
   * 过滤指令, 注意此指令依赖于 Angular UI Bootstrap 中的 Dropdown
   */
  projectModule.directive('filterTaskOnCondition', ['projectModuleBaseUrl', function(moduleBaseUrl) {

    return {
      restrict: 'EA',
      replace: false,
      transclude: true,
      templateUrl: moduleBaseUrl + 'tpls/filter-on-condition.html',
      scope: {
        conditions: '=conditions',
        conditionObj: '=conditionObj'
      },
      link: function (scope) {
        // 设置开关状态
        scope.openStatus = false;
        scope.dropdownToggle = function () {
          scope.openStatus = !scope.openStatus;
        };

        //用于记录对应的上一条记录的集合
        var lastItems = {};
        for (var condName in scope.conditions) {
          lastItems[condName] = {};
        }

        //执行选择操作
        scope.setSelected = function (condItem, condName) {
          lastItems[condName].selected = false;
          lastItems[condName] = condItem;
          condItem.selected = true;

          scope.conditionObj[condName] = condItem['cond'];

        };


        /*
         重置，清空已有的选择条件
         */
        scope.reset = function () {
          scope.conditionObj = {};

          for (var item in lastItems) {
            lastItems[item].selected = false;
          }
        };
      }
    };

  }]);//End of --> filterTaskOnCondition

  /**
   * 此指令用于讨论显示列表中，显示一条讨论的简要信息
   */
  projectModule.directive('discussionListItem', ['$stateParams', 'projectModuleBaseUrl',
    function($stateParams, projectModuleBaseUrl){

      return {
        restrict: 'EA',
        replace: true,
        templateUrl: projectModuleBaseUrl + 'tpls/discussion-list-item.html',
        scope: {
          'discussion': '='
        },
        link: function(scope){
          scope.currentProjectId = $stateParams.projectId;
        }
      };

  }]);//End of --> discussionListItem

  /**
   * 当前元素的滑动条到底事件指令
   */
  projectModule.directive('taskGroup', ['ScrollService', '$window', function( ScrollService, $window){
    return {
      restrict: 'EA',
      scope: {
        'currentStatusId': '='
      },


      link: function(scope, ele){
        //ScrollService.init(ele, 'load:status', scope.currentStatusId);
        ScrollService.init(ele, 'taskStatusLoadMore' + scope.currentStatusId);

        var screenHeight = $window.screen.height;
        var statusPanel = ele.get(0);
        statusPanel.style.maxHeight = Math.floor(screenHeight * 0.5) + 'px';



      }
    };
  }]);//End of --> taskGroup

  /**
   * 用于分享墙的附件指令(由于deckgrid内不能使用ng-repeat)
   */
  projectModule.directive('attachmentClip', ['ProjectSharingService',

    function(ProjectSharingService){
      return {
        restrict: 'EA',
        scope: {
          resourceList: '='
        },
        template: '<ul> <li ng-repeat="resource in resourceList"><a ng-click="downloadResource(resource.id)" ng-show="$index < 2">' +
          '<i class="fa fa-paperclip"></i>{{resource.origin_name}}</a><span ng-show="$index == 2">……</span></li> </ul>',
        link: function(scope){
          scope.downloadResource = function(resourceId){
            ProjectSharingService.downloadResource(resourceId);
          };
        }
    };
  }]);//End of --> attachmentClip

  /**
   * 用于分享墙的标签指令
   */
  projectModule.directive('sharingItemLabelBox', function(){
    return {
      restrict: 'EA',
      scope: {
        tags: '='
      },
      template: '<ul class="label-list sharing-label-list">' +
      '<li ng-class="{leaveOut: $index >= 2}" ng-repeat="tag in tags"><span class="label-content" ng-show="$index < 2">{{ tag.name }}</span><span  ng-show="$index == 2">……</span></li>' +
      '</ul>'

    };
  });//End of --> sharingItemLabelBox

  /**
   * 这个指令用于实现并管理 project.show 状态下加载的过度动画
   */
  projectModule.directive('projectSwitchLoading', ['projectModuleBaseUrl', '$rootScope', '$timeout',
    function(projectModuleBaseUrl, $rootScope, $timeout){

      return {
        restrict: 'EA',
        replace: true,
        templateUrl: projectModuleBaseUrl + 'tpls/switch-loading-animate.html',
        scope: {
          statePrefix: '@',
          loading: '=',
          delay: '@'
        },
        link: function(scope){
          scope.delay = parseInt(scope.delay) || 400;

          var cancel = null;

          $rootScope.$on('$stateChangeStart', function(event, toState, roParams, fromState, fromParams){
            if( fromState['name'].indexOf(scope.statePrefix) !== -1 ){
              if( cancel !== null ) $timeout.cancel(cancel);

              cancel = $timeout(function(){
                scope.loading = true;
              }, scope.delay);

            }
          });

          $rootScope.$on('$stateChangeSuccess', function(event, toState){

            if( toState['name'].indexOf(scope.statePrefix) !== -1 ){

              scope.loading = false;

              if( cancel !== null ){
                $timeout.cancel(cancel);
                cancel = null;
              }
            }
          });

        }
      };
  }]);


  projectModule.directive('desktopBreadcrumb', ['TaskService', 'projectModuleBaseUrl', function(TaskService, projectModuleBaseUrl){
    return {
      restrict: 'EA',

      templateUrl: projectModuleBaseUrl + 'tpls/desktop-breadcrumb.html',
      scope: {
        breadcrumbNeedObj: '='
      },
      link: function(scope){

        scope.pathList = [];
        TaskService.accessor['setParentResourceId']({
          projectId: scope.breadcrumbNeedObj.projectId,
          taskId: scope.breadcrumbNeedObj.taskId
        });

        TaskService.accessor.get()
          .success(function(data){
            var taskInfoSet = getTaskInfoSet(data);


            //根据task id为键的散列创建pathList
            var curTaskInfo = taskInfoSet[scope.breadcrumbNeedObj.curTaskId];
            var childId = curTaskInfo.parentId;
            var parentId;
            if(childId){
              parentId = taskInfoSet[childId].parentId;
            }



            while(childId){
              scope.pathList.unshift(taskInfoSet[childId]);
              childId = parentId;
              if(childId) {parentId = taskInfoSet[childId].parentId;}
            }

          })
          .error(function(data){
            console.error(data);
          });



        //根据任务列表创建以task id为键的散列
        function getTaskInfoSet(taskList){
          var taskInfoSet = {};
          var curTaskInfo;
          for(var i=0; i<taskList.length; i++){
             curTaskInfo= {
               id: taskList[i].id,
               name: taskList[i].name,
               parentId: taskList[i].parent_id
            };
            taskInfoSet[taskList[i].id] = curTaskInfo;
          }
          return taskInfoSet;
        }
      }
    }
  }]);

});