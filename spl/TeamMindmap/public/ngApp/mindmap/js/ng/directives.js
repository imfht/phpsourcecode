/**
 * Created by rockyren on 14/12/22.
 */
define(['mindmapJS/ng/module', 'mindmapJS/imp/Mindmap'],
  function(mindmapModule, Mindmap){
  mindmapModule.directive('mindmap', ['$state', 'mindmapModuleBaseUrl', '$timeout',
    function($state, mindmapModuleBaseUrl, $timeout){

    return {
      restrict: 'EA',
      templateUrl: mindmapModuleBaseUrl + 'tpls/mindmap.html',
      scope: {
        type: '@',
        infoObj: '=',
        rootName: '=',

        checkNodeAuthority: '=',
        handleNode: '=handleNode',
        requestHandle: '=requestHandle'

      },
      link: function(scope){
        if(scope.type === 'taskMore'){
          var goToTaskGraph = function(taskId){
            $state.go('project.show.desktop.taskGraph', {taskId: taskId});
          };
          var graph = Mindmap.createGraph({
            type: scope.type,
            canvasId: 'mindmap-canvas',
            toolbarId: 'toolbar',
            goToTaskGraph: goToTaskGraph,
            infoObj: scope.infoObj
          });
          graph.setRoot({
            label: scope.rootName
          });
        }
        else if(scope.type === 'desktop'){

          var graph = Mindmap.createGraph({
            type: scope.type,
            canvasId: 'mindmap-canvas',
            toolbarId: 'toolbar',
            parentChangeRequest: scope.requestHandle.changeParentTask,
            checkNodeAuthority: scope.checkNodeAuthority,
            infoObj: scope.infoObj
          });
          graph.setRoot({
            label: scope.rootName
          });
          scope.isLoading = false;
          var loadingAnimatePromise = null;
          //对外暴露的结点的操作模块
          scope.handleNode = {
            addNode: function(name, outerId, data){
              var node = graph.addNode(graph.selected, {
                id: outerId,
                label: name,
                data: data
              });
              console.log(node);
            },
            deleteNode: function(){
              if(graph.selected){
                if(graph.selected.isRootNode()){
                  console.log('cannot cancel root node');
                }else{
                  graph.removeNode(graph.selected);
                  graph.setSelected(null);
                }
              }
            },
            setParentNode: function(parentId, childId){
              graph.setParent(parentId, childId);
            },
            setEnableRender: function(canRender){
              if(!canRender){
                if( loadingAnimatePromise !== null ) $timeout.cancel(loadingAnimatePromise);

                loadingAnimatePromise = $timeout(function(){
                  graph.setEnableRender(canRender);
                  scope.isLoading = true;
                }, 450);

              }else{
                $timeout.cancel(loadingAnimatePromise);
                loadingAnimatePromise = null;
                graph.setEnableRender(canRender);
                scope.isLoading = false;
              }


            }
          };
          scope.nodePlus = function(){
            var selectedNode = graph.selected;
            var parentTaskInfo = null;

            if(!selectedNode.isRootNode()){
              parentTaskInfo = {
                id: selectedNode.id,
                label: selectedNode.label
              }
            }

            scope.requestHandle.createTask(parentTaskInfo);
          };

          scope.nodeCancel = function(){
            if( confirm('删除后无法恢复，您确定要执行吗？')){
              scope.requestHandle.deleteTask(graph.selected.id);
            }

          };

          scope.nodeMore = function(){
            $state.go('project.show.desktop.taskGraph', {taskId: graph.selected.id});
          };

          scope.nodeInfo = function(){
            $state.go('project.show.desktop.taskInfo', {taskId: graph.selected.id});
          };



        }


      }
    }
  }])
});