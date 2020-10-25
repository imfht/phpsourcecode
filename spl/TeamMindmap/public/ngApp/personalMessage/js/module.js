/**
 * Created by Dick on 14/12/12.
 */
define(['angular', 'projectJS/ng-require',
    'libraryJS/classHelper/ng-require', 'libraryJS/markdownEditor/ng-require', 'libraryJS/navigationBar/ng-require',
    'libraryJS/pagination/ng-require', 'libraryJS/resourceAccessor/ng-require', 'libraryJS/userManager/ng-require',
    'angularBootstrap', 'angularBootstrapTemplate'],
  function(angular, projectModule,
           classHelperModule, markdownEditorModule, navigationBarModule, paginationModule, resourceAccessorModule, userManagerModule){

    var messageModule = angular.module('TeamMindmap.personal.message',[projectModule,
      classHelperModule, markdownEditorModule, navigationBarModule, paginationModule,
      resourceAccessorModule, userManagerModule,
      'ui.bootstrap', 'ui.bootstrap.tpls'
    ]);

    messageModule.constant('personalMessageBaseUrl', 'ngApp/personalMessage/');

    messageModule.config(['$stateProvider', 'personalMessageBaseUrl',
      function($stateProvider, baseUrl){

        $stateProvider
          .state('personal.message',{
          url: '/message',
          views: {
            'third-nav@personal': {
              template: '<third-nav base-state="baseState" nav-items="navItems"></third-nav>',
              controller: ['$scope', function ($scope) {
                //初始化三级导航栏
                $scope.baseState = 'personal.message';
                $scope.navItems = [
                  {label: '查看私信', state: 'list'},
                  {label: '编写私信', state: 'creating'}
                ];
              }]
            }
          }
        })
          .state('personal.message.list',{
            url: '/list',
            views: {
              'third-title@personal': {
                template: '<div class="third-title"><h3>查看私信</h3><hr/></div>'
              },
              'main-content@personal': {
                templateUrl: baseUrl + 'tpls/message-list.html',
                resolve: {
                  messageList: ['MessageService', 'PaginationService', function(MessageService, PaginationService){
                    var getOps = PaginationService.resourceGetOpsHelper(
                      {option: 'received'},
                      {per_page: 10, page: 1}
                    );

                    return MessageService.accessor.get(getOps)
                      .then(function(resp){
                        return resp.data;
                      }, function(resp){
                        alert('私信列表加载出错!');
                        console.error(resp.data);
                      });
                  }]
                },
                controller: 'MessageListController'
              }
            }
          })
          .state('personal.message.list.show',{
            url: '/show',
            views: {
              'third-title@personal': {
                template: ''
              },
              'third-nav@personal': {
                template: ''
              },
              'main-content@personal': {
                templateUrl: baseUrl + 'tpls/message-show.html',
                resolve: {
                  currentMessageInfo: ['MessageService', function(MessageService){
                    return MessageService.currentMessageInfo;
                  }]
                },
                controller: 'MessageShowController'
              }
            }
          })
          .state('personal.message.creating',{
            url: '/creating',
            views: {
              'third-title@personal': {
                template: '<div class="third-title"><h3>编写私信</h3><hr/></div>'
              },
              'main-content@personal': {
                templateUrl: baseUrl + 'tpls/message-creating.html',
                resolve: {
                  projectList: ['ProjectService', function(ProjectService){
                    return ProjectService.getAllProjects();
                  }]
                },
                controller: 'MessageCreatingController'
              }
            }

          });
    }]);

    return messageModule;
});