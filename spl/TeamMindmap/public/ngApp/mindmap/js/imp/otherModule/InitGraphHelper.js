/**
 * Created by rockyren on 15/3/8.
 */
define(['mindmapJS/imp/otherModule/DataHelper'], function(DataHelper){
  var InitGraphHelper = function(aGraph, aInfoList){
    //graph对象
    var graph = aGraph;
    //"信息"列表
    var infoList = aInfoList;
    //父节点id到子任务信息列表的索引
    var parentInfoGroup = {};

    return {
      /**
       * 根据总"信息"列表,创建父节点id到子"信息"列表的索引
       * @return 返回父节点id到子"信息"列表的索引
       *          其例子格式为{'root': [nodeInfo1,nodeInfo2], 'parentId2': [nodeInfo3, nodeInfo4]}
       */
      createParentIdInfoGroup: function(){
        parentInfoGroup = {
          'root': []
        };

        for(var i=0; i<infoList.length; i++){
          var curInfo = infoList[i];
          //如果curInfo的parent_id为null,则分到root组(即属于第一层节点)
          if(curInfo.parent_id == null){
            parentInfoGroup['root'].push(curInfo);
          }
          //如果curInfo的parent_id不为null,则按parend_id分组
          else{
            if(!parentInfoGroup[curInfo.parent_id]) { parentInfoGroup[curInfo.parent_id] = []}
            parentInfoGroup[curInfo.parent_id].push(curInfo);
          }
        }

        return parentInfoGroup;
      },

      /**
       * 逐层创建节点:根据子任务的信息列表(childrenInfoList)逐个创建子节点,然后与连接父节点,递归上述操作
       * @param parentNode 父节点
       * @param childrenInfoList 信息列表
       */
      batchSetParent: function(parentNode, childrenInfoList){
        for(var i=0; i<childrenInfoList.length; i++){
          var currentInfo = childrenInfoList[i];
          var node = graph.addNode(parentNode, { id: currentInfo.id, data: currentInfo });
          graph.setLabel(node, currentInfo.name);


          var newChildrenInfoList = parentInfoGroup[currentInfo.id];
          if(newChildrenInfoList){
            this.batchSetParent(node, newChildrenInfoList);
          }
        }
      },

      createTaskMoreGraph: function(goToTaskGraph){
         var taskMoreNodesInfo = {
           'description': {
              label: '任务描述',
              content: infoList.baseInfo.description
           },
           'handler': {
             label: '任务负责人',
             content: infoList.handler.username,
             data: infoList.handler
           },
           'finish_at': {
             label: '任务截止日期',
             content: infoList.baseInfo.finish_at || '无限期'
           },
           'appointed_member': {
             label: '参与者',
             content: infoList.appointed_member
           },
           'sub_task': {
             label: '子任务',
             content: infoList.sub_task
           }
         };


        var secondLevelNode;
        var firstLevelNode;

        function setFirstLevelNode(label){
          var fNode = graph.addNode(graph.root);
          graph.setLabel(fNode, label);
          graph.setBiggerNode(fNode);
          return fNode;
        }
        DataHelper.forEach(taskMoreNodesInfo, function(curInfo){

          if(typeof(curInfo.content) == 'string'){
            firstLevelNode = setFirstLevelNode(curInfo.label);
            secondLevelNode = graph.addNode(firstLevelNode);
            graph.setLabel(secondLevelNode, curInfo.content);
          }else if(Array.isArray(curInfo.content) && curInfo.label !== '子任务'){
            firstLevelNode = setFirstLevelNode(curInfo.label);
            if(curInfo.content.length == 0){
              secondLevelNode = graph.addNode(firstLevelNode);
              graph.setLabel(secondLevelNode, '无');
            }else{
              DataHelper.forEach(curInfo.content, function(curSecondInfo){
                secondLevelNode = graph.addNode(firstLevelNode);
                var secondName = curSecondInfo.username || curSecondInfo.name;
                graph.setLabel(secondLevelNode, secondName);
              });
            }
          }else if(curInfo.label === '子任务'){
            var subTaskList = curInfo.content;
            if(subTaskList.length > 0){
              firstLevelNode = setFirstLevelNode(curInfo.label);
              DataHelper.forEach(subTaskList, function(curSecondInfo){
                secondLevelNode = graph.addNode(firstLevelNode);
                graph.setLabel(secondLevelNode, curSecondInfo.name);
                graph.setSubTaskNodeClick(secondLevelNode, function(){
                  goToTaskGraph(curSecondInfo.id);
                });

              });
            }


          }

        });


      }

    }
  };

  return InitGraphHelper;

});