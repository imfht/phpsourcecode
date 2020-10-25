/**
 * Created by rockyren on 14/12/11.
 */

/**
 *
 */
define(['angular', 'projectJS/ng-require',
    'libraryJS/classHelper/ng-require', 'libraryJS/navigationBar/ng-require', 'libraryJS/pagination/ng-require',
    'libraryJS/resourceAccessor/ng-require', 'libraryJS/userManager/ng-require'],
  function(angular, projectModule,
           classHelperModule, navigationBarModule, paginationModule, resourceAccessorModule, userManagerModule){

    var notificationModule = angular.module('TeamMindmap.personal.notification',[projectModule,
      classHelperModule, navigationBarModule, paginationModule,
      resourceAccessorModule, userManagerModule
    ]);

    notificationModule.constant('personalNotificationBaseUrl', 'ngApp/personalNotification/');

    notificationModule.config(['$stateProvider', 'personalNotificationBaseUrl', function($stateProvider, baseUrl){
      $stateProvider.state('personal.notification', {
        url: '/notification',
        views: {
          'third-title@personal': {
            template: '<div class="third-title"><h3>我的通知</h3><hr/></div>'
          },
          'main-content@personal': {
            templateUrl: baseUrl + 'tpls/notification-index.html',

            resolve: {

              projectList: ['ProjectService', function(ProjectService) {
                return ProjectService.getAllProjects();
              }],

              notificationList: ['NotificationService', 'PaginationService', function(NotificationService, PaginationService){
                var getOps = PaginationService.resourceGetOpsHelper(
                  {read: 0}, {per_page: 10, page: 1}
                );

                return NotificationService.accessor.get(getOps)
                  .then(function(resp){
                    return resp.data;
                  },
                  function(resp){ alert('通知列表加载失败');  console.error(resp.data); });
              }],
              'typeLabelSet': ['NotificationTypeService', function(NotificationTypeService){
                return NotificationTypeService.getTypeLabelSet();
              }]
            },
            controller: 'NotificationController'
          }
        }
      });
    }]);

    return notificationModule;
});