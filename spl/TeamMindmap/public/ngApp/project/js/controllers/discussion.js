/**
 * Created by spatra on 14-12-25.
 */

define(['projectJS/module'], function(projectModule){

  /**
   * 用于控制讨论模块的列表及相关显示
   */
  projectModule.controller('DiscussionListController', ['$scope', '$rootScope', '$stateParams', 'discussionList', 'DiscussionService', 'discussionFilterConditions', 'ClassHelperService', 'PaginationService',
    function($scope, $rootScope, $stateParams, discussionList, DiscussionService, discussionFilterConditions, ClassHelpService, PaginationService){
      //设置父级资源id
      DiscussionService.accessor['setParentResourceId']($stateParams);

      /*
       初始化用到的变量
       */
      $scope.discussionList = discussionList.data;
      $scope.conditionObj = {};
      $scope.currentProjectId = $stateParams.projectId;
      $scope.conditions = discussionFilterConditions;

      //定义分页辅助对象
      $scope.pagination = PaginationService.createPagination('number', {
        currentPage: discussionList.current_page,
        itemsPerPage: discussionList.per_page,
        totalItems: discussionList.total,

        resourceList: $scope.discussionList,
        resourceGetMethod: PaginationService.convertMethodToNormalFun(DiscussionService.accessor, 'get'),
        getResourceOps: $scope.conditionObj
      });
      $scope.pagination.init();

      //当必要时，重新加载讨论
      $rootScope.$on('reload:discussion', function(){
        $scope.pagination.getResource({
          resetCurrentPage: true
        });
      });


      //筛选条件改变时，重新加载讨论列表
      $scope.$watch('conditionObj', function(newValue, oldValue){
        if( ! ClassHelpService.objectEquals(newValue, oldValue)){
          //为了避免加载过程中显示分页模块，故先将其清零
          $scope.discussionList.length = 0;

          $scope.pagination.getResource({
            resetCurrentPage: true
          });
        }

      }, true);

  }]);//End of --> DiscussionListController

  /**
   * 用于控制新增讨论的控制器
   */
  projectModule.controller('DiscussionCreatingController', ['$rootScope', '$scope', '$stateParams', '$state', 'DiscussionService', 'userList',
    function($rootScope, $scope, $stateParams, $state, DiscussionService, userList){

      DiscussionService.accessor['setParentResourceId']($stateParams);

      //重置待添加的讨论对象（清空）
      function resetDiscussion(){
        $scope.addDiscussion = {
          followers: []
        };//待添加的讨论
      }

      /*
        初始化用到的变量
       */
      $scope.currentProjectId = $stateParams.projectId;   //当前所属项目的id
      $scope.userList = userList;   //当前项目的成员及创建者，用于生成请求关注列表
      $scope.sending = false;       //是否处于正在发送状态
      resetDiscussion();

      //执行添加讨论的操作
      $scope.add = function(){

        var followersId = [];

        for(var i = 0; i < $scope.addDiscussion.followers.length; ++i ){
          followersId.push($scope.addDiscussion.followers[i]['id']);
        }

        $scope.sending = true;
        var waitingOpts = {
          title: '正在创建讨论，请稍候'
        };

        $scope.$emit('message:wait', waitingOpts);
        DiscussionService.accessor.store({
          'title': $scope.addDiscussion.title,
          'content': $scope.addDiscussion.content,
          'followers': followersId
        })
          .success(function(){
            $rootScope.$emit('reload:discussion');
            $state.go('project.show.discussion', $stateParams);

            waitingOpts.show({
              type: 'success',
              'title': '操作成功',
              'msg': '讨论已成功创建'
            });

          })
          .error(function(data){
            console.error(data);

            waitingOpts.show({
              type: 'error',
              'title': '操作失败',
              'msg': data.error || ''
            });

            $scope.sending = false;
          });

      };//End of --> add

      //重置讨论的内容
      $scope.reset = resetDiscussion;
  }]);//End of --> DiscussionCreatingController

  /**
   * 此控制器对应查询具体的讨论信息
   */

  projectModule.controller('DiscussionInfoController', ['$scope', '$rootScope', '$stateParams', '$modal', 'projectModuleBaseUrl', 'currentDiscussion', 'DiscussionService', 'ScrollService', 'commentList', 'CommentService', 'PaginationService',
    function($scope, $rootScope, $stateParams, $modal, projectModuleBaseUrl, currentDiscussion, DiscussionService, ScrollService, commentList, CommentService, PaginationService) {

      DiscussionService.accessor['setParentResourceId']($stateParams);

      /*
       初始化使用到的变量
       */
      $scope.currentProjectId = $stateParams.projectId;
      $scope.currentDiscussion = currentDiscussion;
      $scope.commentList = commentList.data;   //评论列表

      ScrollService.init('body', 'commentLoadMore');

      $scope.pagination = PaginationService.createPagination('scroll', {
        currentPage: commentList.current_page,
        itemsPerPage: commentList.per_page,
        totalItems: commentList.total,
        resourceList: $scope.commentList,
        resourceGetMethod: PaginationService.convertMethodToNormalFun(CommentService.accessor, 'get'),
        ngPromiseHandle: function(ngPromise){
          ngPromise.then(function(resp){
            return resp;
          }, function(resp){
            console.error(resp.data);
            $scope.$emit('message:error', {
              title: '出错',
              msg: '获取评论信息失败'
            });
          });
        },
        getResourceOps: {},
        eventName: 'scroll:commentLoadMore',
        type: 'loadPartition'
      });

      $scope.pagination.init();


      //重新加载讨论信息
      function reloadDiscussion() {
        DiscussionService.accessor.show($stateParams['discussionId'])
          .success(function (data) {
            $scope.currentDiscussion = data;
          })
          .error(function (data) {
            console.error(data);
            $scope.$emit('message:error', {
              title: '出错',
              msg: '重新加载讨论信息失败'
            });
          });
      }

      /*
       打开添加评论的模态框，并进行相关的后台交互
       */
      $scope.addComment = function () {
        /*
         生成模态框
         */
        $scope.addCommentModal = $modal.open({
          templateUrl: projectModuleBaseUrl + 'tpls/add-discussion-comment-modal.html',
          controller: 'AddCommentModalController',
          size: 'lg'
        });

        /*
         如果选择了确定，则模态框关闭后执行提交操作
         */
        $scope.addCommentModal.result
          .then(function (data) {

            CommentService.accessor.store(data)
              .success(function () {
                $scope.pagination.update({added: true});
                $scope.$emit('unread:update');
                $scope.$emit('message:success', {
                  title: '操作成功',
                  msg: '评论已成功添加'
                });
              })
              .error(function (data) {
                console.error(data);
                $scope.$emit('message:error', {
                  title: '操作失败',
                  msg: data.error
                });
              });
          });
      };

      /*
       开启或关闭讨论
       */
      $scope.toggleOpenStatus = function () {
        var reverseStatus = !$scope.currentDiscussion.baseInfo.open;

        DiscussionService.accessor.update($stateParams['discussionId'], {
          'open': reverseStatus
        })
          .then(function () {
            $scope.currentDiscussion.baseInfo.open = reverseStatus;
            $scope.$emit('unread:update');
            $scope.$emit('message:success', {
              title: '操作成功',
              msg: '讨论状态已更改'
            });
          }, function (resp) {
            console.error(resp.data);
            $scope.$emit('message:error', {
              title: '操作失败',
              msg: resp.data.error
            });
          });
      };//End of --> toggleOpenStatus



    }]);//End of --> DiscussionInfoController

});