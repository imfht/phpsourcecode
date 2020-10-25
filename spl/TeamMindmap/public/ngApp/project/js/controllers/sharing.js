/**
 * Created by laiyuanjin on 15-1-29.
 */

define(['projectJS/module'], function(projectModule){

  /**
   * 管理分享列表的控制器
   */
  projectModule.controller('ProjectSharingListController', ['$scope', '$rootScope', '$stateParams', 'ProjectSharingService', 'sharingList', 'ScrollService', 'PaginationService',
    function($scope, $rootScope, $stateParams, ProjectSharingService, sharingList, ScrollService, PaginationService){

      $scope.sharingList = sharingList.data;
      $scope.currentProjectId = $stateParams.projectId;

      //滚动条监听,当滚动条到底时发送一个scroll:sharingLoadMore事件
      ScrollService.init('body', 'sharingLoadMore');

      var resourceGetMethod = PaginationService.convertMethodToNormalFun(ProjectSharingService.accessor, 'get');
      $scope.pagination = PaginationService.createPagination('scroll', {
        currentPage: 1,
        itemsPerPage: sharingList['per_page'],
        totalItems: sharingList['total'],
        resourceList: $scope.sharingList,
        resourceGetMethod: resourceGetMethod,
        getResourceOps: {},
        eventName: 'scroll:sharingLoadMore'
      });
      $scope.pagination.init();

  }]);//End of --> ProjectSharingListController

  /**
   * 实现新建分享的控制器
   */
  projectModule.controller('ProjectSharingCreatingController', ['$scope', '$state', '$upload', 'ProjectSharingService', 'currentProjectId', 'projectTags', 'ProjectTagService', 'ClassHelperService',
    function($scope, $state, $upload, ProjectSharingService, currentProjectId, projectTags, ProjectTagService, ClassHelperService){
      //设定父级资源
      ProjectSharingService.accessor['setParentResourceId']({
        projectId: currentProjectId
      });
      ProjectTagService.accessor['setParentResourceId']({
        projectId: currentProjectId
      });

      /*
        初始化控制器作用域内用到的变量
       */
      $scope.newSharing = {
        tag: [],
        resource: []
      };  //待添加的 分享 的数据集


      $scope.uploadingFiles = [];  //表示上传中的文件对象集合
      $scope.uploading = false;
      $scope.uploadingProgresses = {};  //用于保存上传中的文件的进度条信息


      $scope.projectTags = projectTags;   //当前项目的所有已添加标签
      $scope.projectTagSet = ClassHelperService.objListToSet(projectTags, 'id');
      $scope.addTagMethods = [
        {label: '自定义标签', value: 'byUser'},
        {label: '项目已有标签', value: 'inProject'}
      ]; //添加标签的方法集合
      $scope.addTagMethodValue = 'byUser'; //当前的添加标签方法值
      $scope.sharingTagSet = {}; //标签集合(K-V对的集合)
      $scope.hasAddTag = false; //表示是否已经为分享添加了标签

      $scope.sending = false; //是否处于发送状态

      /*
        监听 sharingTagSet 的改变，判断集合是否为空
       */
      $scope.$watch('sharingTagSet', function(newValue){
        if( ClassHelperService.isEmpty(newValue) ){
          $scope.hasAddTag = false;
        }else{
          $scope.hasAddTag = true;
        }
      },true);

      /*
        新建分享
       */
      $scope.addSharing = function(){
        $scope.newSharing.tag = [];
        for(var prop in $scope.sharingTagSet ){
          if( $scope.sharingTagSet.hasOwnProperty(prop) && parseInt(prop)){
            $scope.newSharing.tag.push(parseInt(prop));
          }
        }

        $scope.sending = true;
        var waitingOps = {
          title: '正在创建，请稍候'
        };
        $scope.$emit('message:wait', waitingOps);
        ProjectSharingService.accessor.store($scope.newSharing)
          .success(function(){
            waitingOps.show({
              type: 'success',
              title: '操作成功',
              msg: '分享已成功创建'
            });

            $state.go('project.show.sharing.list', {
              projectId: currentProjectId
            });
          })
          .error(function(data){
            $scope.sending = false;
            $scope.show({
              type: 'error',
              title: '操作失败',
              msg: data.error
            });
            console.error(data);
          });
      };//End of --> function:addSharing

      /*
        在上传完成后检查进度条，移除已经上传完毕的
       */
      function checkProgressbar(filename){
        delete $scope.uploadingProgresses[filename];
        $scope.uploading = ! ClassHelperService.isEmpty($scope.uploadingProgresses);
      }

      /**
       * 当用户添加文件时，实现上传操作
       */
      $scope.$watch('uploadingFiles', function(){

        $scope.uploadingFiles.forEach(function(currentFile){
          var filename = currentFile.name;

          //显示进度条，并创建用于显示进度条的有关数据集
          $scope.uploading = true;
          $scope.uploadingProgresses[filename] = {
            filename: filename
          };

          $scope.upload = $upload.upload({
            url: ProjectSharingService.getTempUploadUri(),
            method: 'post',
            data: {myObj: $scope.myModelObj},
            file: currentFile,
            fileFormDataName: 'file'
          })
            .progress(function(evt){
              //更新上传进度条的信息
              $scope.uploadingProgresses[filename]['value'] = parseInt(100.0 * evt.loaded / evt.total);
            })
            .success(function(data){
              $scope.$emit('message:success', {
                title: '操作成功',
                msg: '文件' + filename + '已成功上传'
              });

              $scope.newSharing.resource.push(data);
              checkProgressbar(filename);
            })
            .error(function(data){
              $scope.$emit('message:error', {
                title: '操作失败',
                msg: data.error
              });
              console.error(data);
              checkProgressbar(filename);
            });
        });
      });//End of --> $scope.$watch:uploadingFiles

      /**
       * 新增自定义标签
       */
      $scope.addNewTag = function(){

        function addInProject(){
          $scope.sharingTagSet[ $scope['addMixed'] ] = $scope.projectTagSet[ $scope['addMixed'] ];
        }//End of --> function:addInProject

        function addByUser(){
          ProjectTagService.accessor.store({
            name: $scope.addMixed
          })
            .success(function(data){
              $scope.sharingTagSet[ data.id ] = data;
            })
            .error(function(data){
              console.error(data);
              $scope.$emit('message:error', {
                title: '操作失败',
                msg: data.error
              });
            });
        }//End of --> function:addByUser

        switch ($scope.addTagMethodValue){
          case 'byUser':
            addByUser();
            break;
          case 'inProject':
            addInProject();
            break;
          default: break;
        }

        $scope.addMixed = null;

      };

      /**
       * 移除标签
       * @param tagId
       */
      $scope.removeTag = function(tagId){
        delete $scope.sharingTagSet[tagId];
      };


  }]);//End of --> ProjectSharingCreatingController

  /**
   * 实现 分享 信息的具体显示
   */
  projectModule.controller('ProjectSharingInfoController', ['$scope', '$stateParams', 'sharingInfo', 'ProjectSharingService',
    function($scope, $stateParams, sharingInfo, ProjectSharingService){
      $scope.sharingInfo = sharingInfo;
      $scope.currentProjectId = $stateParams.projectId;

      $scope.downloadResource = ProjectSharingService.downloadResource;

  }]);

});