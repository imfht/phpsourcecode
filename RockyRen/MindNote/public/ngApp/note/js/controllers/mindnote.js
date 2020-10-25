/**
 * Created by rockyren on 15/5/23.
 */
define(['noteJS/module'], function(noteModule){
  noteModule.controller('MindnoteController', ['$scope', '$rootScope', '$modal', '$state', '$stateParams', 'catalogueInfo', 'ResourceListService', 'MenuHelperService', 'NoteService', 'NotebookService', 'GroupService', function($scope, $rootScope, $modal, $state, $stateParams, catalogueInfo, ResourceListService, MenuHelperService, NoteService, NotebookService, GroupService){


    ////包含笔记本的笔记本组
    //$scope.groups = catalogueInfo['groups'] || [];
    //
    ////非组的笔记本列表
    //$scope.singleNotebooks = catalogueInfo['singleNotebooks'] || [];

    if(catalogueInfo && catalogueInfo['groups']){
      //包含笔记本的笔记本组
      $scope.groups = catalogueInfo['groups'];
    }else{
      $scope.groups = [];
    }

    if(catalogueInfo && catalogueInfo['singleNotebooks']){
      $scope.singleNotebooks = catalogueInfo['singleNotebooks'];
    }else{
      $scope.singleNotebooks = [];
    }


    //groups和singleNotebooks的列表管理对象
    $scope.groupsHandle = ResourceListService.GroupListHandle($scope.groups, $scope.singleNotebooks);
    $scope.singleNotebooksHandle = ResourceListService.SingleResourceListHandle($scope.singleNotebooks);



    //当前的groupId和notebookId对象
    //$scope.curResourceId = {
    //  groupId: $stateParams.type == 'group' ? $stateParams.typeId : null,
    //  notebookId: $stateParams.type == 'notebook' ? $stateParams.typeId : null
    //};
    $scope.curResourceId = {
      groupId: null,
      notebookId: null
    };

    //当前被选择的笔记本（组）名
    $scope.curName = '';

    $rootScope.$on("reload:curResourceId", function(event, curResourceId){
      $scope.curResourceId = curResourceId;

      //如果url指定了notebook，则展开对应的group
      var curGroup = $scope.groupsHandle.getGroupByNotebook($scope.curResourceId.notebookId);
      if(curGroup){
        curGroup.showNotebook = true;
      }
    });




    $scope.reloadNotes = function(groupId, notebookId, curName){
      if($scope.curResourceId.groupId == groupId && $scope.curResourceId.notebookId == notebookId){
        return;
      }

      $scope.curResourceId.groupId = groupId || null;
      $scope.curResourceId.notebookId = notebookId || null;


      $scope.curName = curName || '';

      var listStateParams = {};
      if(!groupId && !notebookId){
        listStateParams.type = 'all';
        listStateParams.typeId = 'notes';
      }
      else if(groupId && !notebookId){
        listStateParams.type = 'group';
        listStateParams.typeId = groupId;
      }
      else{
        listStateParams.type = 'notebook';
        listStateParams.typeId = notebookId;
      }
      $state.go('mindnote.list', listStateParams);
    };


    /**
     * 新建笔记
     */
    $scope.addNewNote = function(){
      $state.go('mindnote.list.noteAdd');
    };


    /**
     * 新建笔记本
     * @param isAddInGroup: boolean  是否加入到笔记本组中
     */
    $scope.addNewNotebook = function(isAddInGroup){
      var openObj = MenuHelperService.getInputOpenObj({
        modalTitle: '新建笔记本',
        inputName: ''
      });

      //打开模态框，并返回一个模态框实例
      var modalInstance = $modal.open(openObj);

      //第一个函数为close后的调用，第二个为dismiss后的调用
      modalInstance.result.then(function(notebookName){
        ////如果要加到笔记本组中，则设置其groupId
        //if(isAddInGroup){
        //  NotebookService.accessor.setParentId($scope.curResourceId);
        //}

        var requestObj = {
          name: notebookName
        };
        if(isAddInGroup){
          requestObj.group_id = $scope.curResourceId.groupId;
        }


        NotebookService.accessor.store(requestObj)
          .success(function(data){
            //如果加到笔记本组中，则放入笔记本组中
            if(isAddInGroup){
              $scope.groupsHandle.addNotebook($scope.curResourceId.groupId, data);
            }
            //否则，加入到singleNotebooks中
            else{
              $scope.singleNotebooksHandle.addResource(data);
            }

          })
          .error(function(data){
            console.error(data.message);
          });
      });
    };


    /**
     * 新建笔记本组：新建了笔记本组后，被选择的singleNotebooks的笔记本放到新的group中
     */

    $scope.addNewGroup = function(){
      var openObj = MenuHelperService.getInputOpenObj({
        modalTitle: '新建笔记本组',
        inputName: ''
      });

      var modalInstance = $modal.open(openObj);

      modalInstance.result.then(function(GroupName){
        GroupService.accessor.store({
          name: GroupName
        }).success(function(newGroupData){
          //新建了Group后,将选择的notebook放到新的Group中
          NotebookService.accessor.update($scope.curResourceId.notebookId, {
            group_id: newGroupData.id
          }).success(function(notebookData){
            //添加新的笔记本组
            $scope.groupsHandle.addGroup(newGroupData);
            $scope.groupsHandle.moveInGroup(newGroupData.id, $scope.curResourceId.notebookId);
            $scope.toggleShowNotebook(newGroupData);
          }).error(function(data){
            console.error('转移笔记本到新笔记本组失败');
          })

        }).error(function(data){
          console.error('新建笔记本组失败');
        });
      });

    };


    /**
     * 转移笔记本：将笔记本放到另一个笔记本中
     */
    $scope.translateGroup = function(toGroupId){
      //修改选择的notebook的groupId
      NotebookService.accessor.update($scope.curResourceId.notebookId, {
        group_id: toGroupId
      }).success(function(notebookData){
        var fromGroupId = $scope.curResourceId.groupId;
        //如果是在group中notebook
        if(fromGroupId){
          $scope.groupsHandle.translateNotebook(fromGroupId, toGroupId, $scope.curResourceId.notebookId);
        }
        //如果是在singleNotebooks中的notebook:在singleNotebooks中移除，放到对应group中
        else{
          $scope.groupsHandle.moveInGroup(toGroupId, $scope.curResourceId.notebookId);

        }
        //展开对应的group
        var toGroup = $scope.groupsHandle.getGroup(toGroupId);
        toGroup.showNotebook = true;

      }).error(function(){
        console.error("转移笔记本失败")
      });

    };

    /**
     * 移出笔记本组：在group中的笔记本放到singleNotebooks中
     */
    $scope.moveOutGroup = function(){
      var fromGroupId = $scope.curResourceId.groupId;
      var curNotebookId = $scope.curResourceId.notebookId;
      NotebookService.accessor.setParentId($scope.curResourceId);
      NotebookService.accessor.update(curNotebookId, {
        group_id: -1
      }).success(function(notebookData){
        //$scope.groupsHandle.deleteNotebook(fromGroupId, notebookData.id);
        //$scope.singleNotebooksHandle.addResource(notebookData);
        $scope.groupsHandle.moveOutGroup(fromGroupId, $scope.curResourceId.notebookId);
      }).error(function(){
        console.error('移出笔记本组失败');
      });

    };

    /**
     * 重命名笔记本组
     */
    $scope.reNameGroup = function(){
      var openObj = MenuHelperService.getInputOpenObj({
        modalTitle: '重命名笔记本组',
        inputName: $scope.curName
      });

      var modalInstance = $modal.open(openObj);


      modalInstance.result.then(function(GroupName){
        var curGroupId = $scope.curResourceId.groupId;
        //当笔记本组名改变时才发出请求
        if(GroupName != $scope.curName){
          GroupService.accessor.update(curGroupId, {
            name: GroupName
          }).success(function(data){
            var group = $scope.groupsHandle.getGroup(curGroupId);
            if(group) { group.name = data.name }
          }).error(function(){
            console.error('重命名笔记本组失败');
          });
        }

      });
    };

    /**
     * 重命名笔记本
     */

    $scope.reNameNotebook = function(){
      var openObj = MenuHelperService.getInputOpenObj({
        modalTitle: '重命名笔记本',
        inputName: $scope.curName
      });
      var modalInstance = $modal.open(openObj);

      modalInstance.result.then(function(newNotebookName){
        var curNotebookId = $scope.curResourceId.notebookId;
        var curGroupId = $scope.curResourceId.groupId;

        NotebookService.accessor.setParentId($scope.curResourceId);
        NotebookService.accessor.update(curNotebookId, {
          name: newNotebookName
        }).success(function(data){
          var notebook;

          //group中的笔记本
          if(curGroupId){
            notebook = $scope.groupsHandle.getNotebook(curGroupId, curNotebookId);
          }
          //singleNotebooks中的笔记本
          else{
            notebook = $scope.singleNotebooksHandle.getResource(curNotebookId);
          }

          if(notebook) { notebook.name = newNotebookName; }

        }).error(function(){
          console.error('重命名笔记本失败');
        });

      });
    };

    /**
     * 删除笔记本组
     */
    $scope.deleteGroup = function(){
      var openObj = MenuHelperService.getAlertOpenObj({
        modalTitle: '删除笔记本组',
        alertMessage: '你真的要删除' + $scope.curName + "？"
      });
      var modalInstance = $modal.open(openObj);


      modalInstance.result.then(function(){
        GroupService.accessor.destroy($scope.curResourceId.groupId)
          .success(function(){
            $scope.groupsHandle.deleteGroup($scope.curResourceId.groupId);
            //删除后，跳转到全笔记本
            $state.go('mindnote.list', {type: 'all', typeId: 'notes'});
          }).error(function(data){
            console.error(data.message);
          });
      });
    };

    /**
     * 删除笔记本
     */
    $scope.deleteNotebook = function(){
      var openObj = MenuHelperService.getAlertOpenObj({
        modalTitle: '删除笔记本',
        alertMessage: '你真的要删除' + $scope.curName + "？"
      });
      var modalInstance = $modal.open(openObj);

      //@workaround:暂不写请求
      modalInstance.result.then(function(){
        var curGroupId = $scope.curResourceId.groupId;
        var curNotebookId = $scope.curResourceId.notebookId;

        NotebookService.accessor.setParentId($scope.curResourceId);
        NotebookService.accessor.destroy(curNotebookId)
          .success(function(){
            //group中的笔记本
            if(curGroupId){
              $scope.groupsHandle.deleteNotebook(curGroupId, curNotebookId);
            }
            //singleNotebooks中的笔记本
            else{
              $scope.singleNotebooksHandle.deleteResource(curNotebookId);
            }

            //删除后，跳转到全笔记本
            $state.go('mindnote.list', {type: 'all', typeId: 'notes'});
          })
          .error(function(data){
            console.error(data.message);
          });
      });
    };


    /**
     * 新建思维导图
     */
    $scope.addNewMap = function(){
      $state.go('mindnote.list.mapAdd');
    };

    //表示目录的隐显
    $scope.showCatalogue = true;
    $scope.setShowCatalogue = function(isShow){
      $scope.showCatalogue = isShow;
    };

    //切换group中的notebook的显示和隐藏

    $scope.toggleShowNotebook = function(group){
      if(group.showNotebook){ group.showNotebook = false; }
      else { group.showNotebook = true; }
    };
  }]);


});