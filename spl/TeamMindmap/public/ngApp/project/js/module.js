/**
 * Created by spatra on 14-12-2.
 */

define(['angular',
    'mindmapJS/ng-require',
    'libraryJS/classHelper/ng-require', 'libraryJS/markdownEditor/ng-require', 'libraryJS/filter/ng-require', 'libraryJS/navigationBar/ng-require',
    'libraryJS/pagination/ng-require', 'libraryJS/resourceAccessor/ng-require', 'libraryJS/userManager/ng-require',
    'libraryJS/globalNotification/ng-require', 'libraryJS/ngBootstrapUIHelper/ng-require', 'libraryJS/datetime/ng-require',
    'libraryJS/authorization/ng-require',
    'angularUIRouter', 'angularAnimate', 'bxslider', 'angularBootstrap', 'angularBootstrapTemplate', 'ngFileUpload', 'angularDeckgrid'],

  function(angular, mindmapModule,
           classHelperModule, markdownEditorModule, filterModule, navigationBarModule, paginationModule,
           resourceAccessorModule, userManager, globalNotificationModule, ngBootstrapUIHelperModule, datetimeModule, authorizationModule){

    var projectModule = angular.module('TeamMindmap.project', [mindmapModule,
        classHelperModule, markdownEditorModule, filterModule, navigationBarModule, paginationModule,
        resourceAccessorModule, userManager, globalNotificationModule, ngBootstrapUIHelperModule, datetimeModule, authorizationModule,
        'ui.router', 'ngAnimate', 'ui.bootstrap', 'ui.bootstrap.tpls', 'angularFileUpload', 'akoenig.deckgrid']);

    projectModule.constant('projectModuleBaseUrl', 'ngApp/project/');


    projectModule.config(['$stateProvider', 'projectModuleBaseUrl', 'GlobalNotificationProvider', function($stateProvider, baseUrl, GlobalNotificationProvider){

      function defaultResourceResolve(ngPromise, errorMsg, treatedCallback){
       return ngPromise.then(function(resp){
         if( treatedCallback !== undefined ){
           return treatedCallback(resp.data);
         }
         else{
           return resp.data;
         }
       }, function(resp){
         var eMsg = errorMsg || ('加载失败:' + resp.data.error);
         alert(eMsg);
         console.error(resp.data);
       });
      }


      $stateProvider
        .state('project',{
          url: '/project',
          views: {
            '': {
              templateUrl: baseUrl + 'tpls/project-layout.html'
            },
            'notification@project': {
              templateUrl: GlobalNotificationProvider.getTemplateUri()
            }
          },
          abstract: true
        })
        .state('project.list', {
          url: '/list',
          title: '项目列表',
          views: {
            'main@project': {
              templateUrl: baseUrl + 'tpls/project-list.html',
              controller: 'ProjectListController'
            }
          },
          resolve: {
            projectList: ['ProjectService', function(ProjectService) {
              return ProjectService.getAllProjects();
            }]
          }

        })
        .state('project.creating', {
          url: '/creating',
          title: '新建项目',
          views: {
            'main@project': {
              templateUrl: baseUrl + 'tpls/project-creating.html',
              controller: "ProjectCreatingController"
            }
          },
          resolve: {
            roleList: ['RoleService', function(RoleService){
              return defaultResourceResolve(RoleService.accessor.get(), '用户角色加载出错');
            }],
            currUser: ['$http', function($http){
              return defaultResourceResolve($http.get('/api/personal/info'), '个人信息加载出错');
            }]
          }
        })
        .state('project.show', {
          url: '/:projectId',
          resolve:{
            currentProjectId: ['$stateParams', function($stateParams){
              return $stateParams.projectId;
            }],
            currentProjectInfo: ['currentProjectId', 'ProjectService', function(currentProjectId, ProjectService){
              return defaultResourceResolve(ProjectService.accessor.show(currentProjectId), '加载项目信息出错!');
            }],
            taskChecker: ['currentProjectInfo', 'ProjectTaskAuthorizationService', function(currentProjectInfo, ProjectTaskAuthorizationService){
              ProjectTaskAuthorizationService.setProjectInfo(currentProjectInfo);

              return ProjectTaskAuthorizationService.buildChecker();
            }],
            taskPriorityList: ['TaskPriorityService', function(TaskPriorityService){
              return defaultResourceResolve(TaskPriorityService.accessor.get(), '获取任务优先级列表失败');
            }]
          },
          views: {
            'main@project': {
              templateUrl: baseUrl + 'tpls/project-workpanel.html'
            },
            'sidebar@project.show': {
              template: '<project-sidebar></project-sidebar>'
            }
          },
          controller: ['$scope', function($scope){
            $scope.loading = false;
          }],
          abstract: true
        })
        .state('project.show.desktop', {
          url: '/desktop',
          title: '项目工作台',
          resolve: {
            taskSet: ['$stateParams', 'TaskService', function($stateParams, TaskService){
              TaskService.accessor['setParentResourceId']($stateParams);
              //@workaround 暂时只获取第一层任务
              return defaultResourceResolve(TaskService.accessor.get(), '加载任务信息出错');
            }]
          },
          views: {
            'main-content@project.show': {
              templateUrl: baseUrl + 'tpls/project-desktop.html'
              //controller: 'ProjectDesktopController'
            },
            'mindmap@project.show.desktop': {
              templateUrl: baseUrl + 'tpls/mindmap-main.html',
              controller: 'DesktopMindmapController'
            }
          }
        })
        .state('project.show.desktop.taskInfo', {
          url: '/taskInfo/:taskId',
          title: '项目具体任务',
          views: {
            'info@project.show.desktop': {
              templateUrl: baseUrl + 'tpls/task-info.html',
              resolve: {
                projectMembersAndCreater: ['$stateParams', 'MemberService', function($stateParams, MemberService){
                  return defaultResourceResolve(MemberService.getCreaterAndMembersToArray($stateParams), '加载项目成员失败');
                }],
                taskStatusList: ['TaskStatusService', function(TaskStatusService){
                  return defaultResourceResolve(TaskStatusService.accessor.get(), '加载任务状态列表失败');
                }]
              },
              controller: 'TaskInfoController'
            }
          }
        })

        .state('project.show.desktop.taskGraph', {
          url: '/taskGraph/:taskId',
          title: '项目具体任务图',
          resolve: {
            taskInfo: ['TaskService', '$stateParams', function(TaskService, $stateParams){
              TaskService.accessor['setParentResourceId']($stateParams);
               return TaskService.accessor.show($stateParams.taskId)
               .then(function(resp){
               return resp.data;
               },function(data){
               console.error(data);
               })
            }]
          },
          views: {
            'mindmap@project.show.desktop': {
              templateUrl: baseUrl + 'tpls/mindmap-task-info.html',
              controller: 'TaskMoreMindmapController'
            }
          }
        })
        .state('project.show.task', {
          url: '/task',
          title: '项目任务列表',
          resolve: {
            taskStatusSet: ['TaskStatusService', function(TaskStatusService){
              return TaskStatusService.getStatusSet();
            }],

            taskSet: ['$stateParams', 'TaskService', 'TaskStatusService', function($stateParams, TaskService, TaskStatusService){

              return TaskStatusService.accessor.get()
                .then(function(resp){
                  return TaskService.getTaskSet($stateParams, resp.data);
                },function(data){
                  console.error(data);
                })
            }],
            taskConditions: ['TaskPriorityService', 'TaskService', function(TaskPriorityService, TaskService){
              return TaskPriorityService.accessor.get()
                .then(function(resp){
                  return {
                    'priority_id': TaskService.convertToCondItems(resp.data, 'id', 'label')
                  };
                }, function(resp){
                  alert('加载添加任务条件信息失败');
                  console.error(resp.data);
                });
            }]
          },
          views: {
            'main-content@project.show': {
              templateUrl: baseUrl + 'tpls/project-task.html',
              controller: 'TaskController'
            }
          }
        })
        .state('project.show.task.info', {
          url: '/:taskId',
          title: '项目具体任务',
          views: {
            'info@project.show.task': {
              templateUrl: baseUrl + 'tpls/task-info.html',
              resolve: {
                projectMembersAndCreater: ['$stateParams', 'MemberService', function($stateParams, MemberService){
                  return defaultResourceResolve(MemberService.getCreaterAndMembersToArray($stateParams), '加载项目成员失败');
                }],
                taskStatusList: ['TaskStatusService', function(TaskStatusService){
                  return defaultResourceResolve(TaskStatusService.accessor.get(), '加载任务状态列表失败');
                }]
              },
              controller: 'TaskInfoController'
            }
          }
        })
        .state('project.show.discussion', {
          url: '/discussion',
          title: '讨论',
          views: {
            'main-content@project.show': {
              templateUrl: baseUrl + 'tpls/discussion-list.html',
              resolve: {
                discussionList: ['$stateParams', 'DiscussionService', 'PaginationService', function($stateParams, DiscussionService, PaginationService){
                  DiscussionService.accessor['setParentResourceId']($stateParams);
                  var getOps = PaginationService.resourceGetOpsHelper(
                    { options: {open: 1, user: 1} }, {per_page: 10, page: 1}
                  );

                  return defaultResourceResolve(DiscussionService.accessor.get(getOps), '加载讨论列表出错');
                }],
                'discussionFilterConditions': ['BackendFilterService', function(BackendFilterService){
                  return defaultResourceResolve(BackendFilterService.getMethods('projectDiscussion'), '加载过滤条件出错');
                }]
              },
              controller: 'DiscussionListController'
            }
          }
        })
        .state('project.show.discussion.creating', {
          url: '/creating',
          title: '发起新的讨论',
          views:{
            'main-content@project.show':{
              templateUrl: baseUrl + 'tpls/discussion-creating.html',
              resolve: {
                'userList': ['$stateParams', 'ProjectService', function($stateParams, ProjectService){
                  return ProjectService.getCreaterAndMembers( $stateParams.projectId );
                }]
              },
              controller: 'DiscussionCreatingController'
            }
          }
        })
        .state('project.show.discussion.info', {
          url: '/:discussionId',
          title: '讨论查看',
          views: {
            'main-content@project.show': {
              templateUrl: baseUrl + 'tpls/discussion-info.html',
              resolve: {
                'currentDiscussion': ['$stateParams', 'DiscussionService', function($stateParams, DiscussionService){
                  return DiscussionService.accessor.show($stateParams['discussionId'])
                    .then(function(resp){ return resp.data; },
                    function(resp){
                      console.error(resp.data);
                      alert('加载讨论出错!');
                    }
                  );
                }],
                'commentList': ['$stateParams', 'CommentService', function($stateParams, CommentService){
                  CommentService.accessor['setParentResourceId']($stateParams);
                  return defaultResourceResolve(
                    CommentService.accessor.get({params: {per_page: 2, page: 1}}), '加载评论出错!');
                }]
              },
              controller: 'DiscussionInfoController'
            }
          }
        })
        .state('project.show.member',{
          url: '/member',
          title: '项目成员',
          views: {
            'main-content@project.show': {
              templateUrl: baseUrl + 'tpls/project-member.html',
              resolve: {
                conditions: ['roleInfo', function(roleInfo){
                  var rtn = {'role': []};

                  rtn['role'].push({cond: 0, label: '所有人'});

                  roleInfo.forEach(function(item){
                    rtn['role'].push({
                      'cond': item.id,
                      'label': item.label
                    });
                  });

                  return rtn;
                }]
              },
              controller: 'ProjectMemberController'
            }
          },
          resolve: {
            roleInfo: ['RoleService', function(RoleService){
              return defaultResourceResolve(RoleService.accessor.get());
            }]
          }
        })
        .state('project.show.member.creating', {
          url: '/creating',
          views: {
            'main-content@project.show': {
              templateUrl: baseUrl + 'tpls/project-member-creating.html',
              controller: 'ProjectMemberCreatingController'
            }
          }
        })
        .state('project.show.setting', {
          url: '/setting',
          title: '项目设置',
          views: {
            'main-content@project.show': {
              templateUrl: baseUrl + 'tpls/project-setting.html',
              resolve: {
              },
              controller: 'ProjectSettingController'
            }
          }
        })
        .state('project.show.sharing', {
          url: '/sharing',
          views: {
            'main-content@project.show': {
              templateUrl: baseUrl + 'tpls/sharing-content.html'
            },
            'third-nav@project.show.sharing': {
              template: '<module-third-nav nav-items="navItems" parent-state-name="project.show.sharing"></module-third-nav>',
              controller: ['$scope', function($scope){
                $scope.navItems = [
                  {label: '分享列表', uiSref: 'project.show.sharing.list'},
                  {label: '创建分享', uiSref: 'project.show.sharing.creating'}
                ];
              }]
            }
          },
          abstract: true
        })
        .state('project.show.sharing.list', {
          url: '/list',
          title: '分享墙',
          views: {
            'sharing-content@project.show.sharing': {
              templateUrl: baseUrl + 'tpls/sharing-list.html',
              resolve: {
                'sharingList': ['$stateParams', 'ProjectSharingService',
                  function($stateParams, ProjectSharingService){
                    return defaultResourceResolve(ProjectSharingService.getOnStartPaginationConf($stateParams), '加载分享列表失败');
                  }]
              },
              controller: 'ProjectSharingListController'
            }
          }
        })
        .state('project.show.sharing.info', {
          url: '/info/:sharingId',
          title: '分享的详细信息',
          views: {
            'sharing-content@project.show.sharing': {
              templateUrl: baseUrl + 'tpls/sharing-info.html',
              resolve: {
                'sharingInfo': ['$stateParams', 'ProjectSharingService',
                  function($stateParams, ProjectSharingService){
                    ProjectSharingService.accessor['setParentResourceId']($stateParams);
                    return defaultResourceResolve(ProjectSharingService.accessor.show($stateParams['sharingId']));
                  }]
              },
              controller: 'ProjectSharingInfoController'
            },
            'third-nav@project.show.sharing': {
              template: ''
            }
          }
        })
        .state('project.show.sharing.creating', {
          url: '/creating',
          title: '创建分享',
          views: {
            'sharing-content@project.show.sharing': {
              templateUrl: baseUrl + 'tpls/sharing-creating.html',
              resolve: {
                'projectTags': ['$stateParams', 'ProjectTagService', function($stateParams, ProjectTagService){
                  ProjectTagService.accessor['setParentResourceId']($stateParams);
                  
                  return defaultResourceResolve(ProjectTagService.accessor.get(), '加载项目标签出错');
                }]
              },
              controller: 'ProjectSharingCreatingController'
            }
          }
        })
      ;

    }]);//End of config

    return projectModule;
});