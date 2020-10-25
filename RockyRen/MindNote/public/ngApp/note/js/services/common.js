/**
 * Created by rockyren on 15/5/19.
 */
define(['noteJS/module'], function(noteModule){
  noteModule.factory('MenuHelperService', function(){
    return {
      getInputOpenObj: function(resolveObj){
        return {
          templateUrl: 'public/ngApp/note/tpls/singleInputModal.html',
          controller: 'InputHandleModalController',
          size: 'sm',
          resolve: {
            inputInfo: function(){
              return resolveObj;
            }
          }
        }
      },
      getAlertOpenObj: function(resolveObj){
        return {
          templateUrl: 'public/ngApp/note/tpls/alertModal.html',
          controller: 'AlertHandleModalController',
          size: 'sm',
          resolve: {
            alertInfo: function(){
              return resolveObj
            }
          }
        }
      }
    }
  });

  noteModule.factory('ResourceListService', function(){
    /**
     * notebook或note的增删取
     * @param notebooks
     * @returns {{getNotebook: Function, deleteNotebook: Function, addNotebook: Function}}
     * @constructor
     */
    var SingleResourceListHandle = function(resourceList){
      var resourceList = resourceList;

      return {
        getResource: function(resourceId){
          for(var i=0; i<resourceList.length; i++){
            if(resourceId == resourceList[i].id){
              return resourceList[i];
            }
          }

          return null;

        },
        //如果有则返回删除的对象，否则返回null
        deleteResource: function(resourceId){
          for(var i=0; i<resourceList.length; i++){
            if(resourceId == resourceList[i].id){
              var cancelResourceArray = resourceList.splice(i, 1);
              return cancelResourceArray[0];
            }
          }

          return null;
        },
        addResource: function(resource){
          resourceList.push(resource);
        }
      }
    };

    /**
     * group及其内的notebooks的增删取
     * @param groups
     * @param singleNotebooks 不属于任何笔记本组的笔记本
     * @returns {{getGroup: Function, getNotebook: Function, deleteGroup: Function, deleteNotebook: Function, addGroup: Function, addNotebook: Function}}
     * @constructor
     */
    var GroupListHandle = function(groups, singleNotebooks){
      var groups = groups;

      initGroupNotebooks();


      var singleNotebooksHandle = SingleResourceListHandle(singleNotebooks);


      /**
       * 确保groups中的notebooks为一个数组
       */
      function initGroupNotebooks(){
        for(var i=0; i<groups.length; i++){
          if(!groups[i].hasOwnProperty('notebooks')){
            groups[i].notebooks = [];
          }
        }
      }

      /**
       *
       * @param groupId：number | 'singleNotebooksGroup' 笔记本组的id
       * @returns {{getNotebook: Function, deleteNotebook: Function, addNotebook: Function}}
       */
      function getNotebookHandle(groupId){
        var group = getGroup(groupId);
        //if(!group.hasOwnProperty('notebooks') || !group.notebooks){
        //  group.notebooks = [];
        //}
        var notebooks = group.notebooks;
        var notebooksHandle = SingleResourceListHandle(notebooks);

        return notebooksHandle;
      }

      function getGroup(groupId){
        for(var i=0; i<groups.length; i++){
          if(groupId == groups[i].id){
            return groups[i];
          }
        }

        return null;
      }

      return {
        getGroup: getGroup,

        deleteGroup: function(groupId){
          for(var i=0; i<groups.length; i++){
            if(groupId == groups[i].id){
              var cancelGroupArray = groups.splice(i, 1);
              return cancelGroupArray[0];
            }
          }
          return null;
        },

        addGroup: function(group){
          if(!group.hasOwnProperty('notebooks')){
            group.notebooks = [];
          }
          groups.push(group);
        },
        getGroupByNotebook: function(notebookId){
          if(!notebookId) return null;
          for(var i=0; i<groups.length; i++){
            var curGroup = groups[i];
            for(var j=0; curGroup.notebooks && j<curGroup.notebooks.length; j++){
              if(curGroup.notebooks[j].id == notebookId){
                return curGroup;
              }
            }
          }

          return null;
        },

        getNotebook: function(groupId, notebookId){
          var notebooksHandle = getNotebookHandle(groupId);
          return notebooksHandle.getResource(notebookId);
        },

        deleteNotebook: function(groupId, notebookId){
          var notebooksHandle = getNotebookHandle(groupId);
          return notebooksHandle.deleteResource(notebookId);


        },
        addNotebook: function(groupId, notebook){
          var notebooksHandle = getNotebookHandle(groupId);
          notebooksHandle.addResource(notebook);
        },
        translateNotebook: function(fromGroupId, toGroupId, notebookId){
          var notebookObj = this.deleteNotebook(fromGroupId, notebookId);
          this.addNotebook(toGroupId, notebookObj);
        },
        //将某组的笔记本放到 非组 上
        moveOutGroup: function(fromGroupId, notebookId){
          var notebookObj = this.deleteNotebook(fromGroupId, notebookId);
          singleNotebooksHandle.addResource(notebookObj);
        },
        moveInGroup: function(toGroupId, notebookId){
          var notebookObj = singleNotebooksHandle.deleteResource(notebookId);
          this.addNotebook(toGroupId, notebookObj);
        }

      }
    };

    return {
      SingleResourceListHandle: SingleResourceListHandle,
      GroupListHandle: GroupListHandle
    }
  });

});