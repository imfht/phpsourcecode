/**
 * Created by rockyren on 15/3/3.
 */
define(['mindmapJS/imp/Graph', 'mindmapJS/imp/Renderer', 'mindmapJS/imp/otherModule/InitGraphHelper'],
  function(Graph, Renderer, InitGraphHelper){


  var renderer = null;

  /**
   * 根据"信息"列表创建graph
   * @param graph Graph对象
   * @param infoList: "信息"列表,单个"信息"与一个子节点对应
   *                  其例子格式为[{id:2, parent_id:null, name:'node2'}, {id:3, parent_id:2, name:'node3'}]
   * @private
   */
  function _initGraph(options, graph, infoList){
    var initGraphHelper = InitGraphHelper(graph, infoList);
    if(options.type == 'desktop'){


      var parentInfoGroup = initGraphHelper.createParentIdInfoGroup();
      var rootChildrenInfoList = parentInfoGroup['root'];
      initGraphHelper.batchSetParent(graph.root, rootChildrenInfoList);
    }else if(options.type == 'taskMore'){
      initGraphHelper.createTaskMoreGraph(options.goToTaskGraph);
    }


  }

  return {
    //根据type来创建不同的Graph
    createGraph: function(options){
      var graph;
      //@workaround:toolbar的问题
      renderer = new Renderer(options);
      graph = new Graph(renderer);
      if(options.infoObj){
        //@workaround:暂时不抽象_initGraph
        _initGraph(options, graph, options.infoObj);
      }
      return graph;
    },

    setRender: function(mixed){
      if( mixed instanceof Renderer ){
        renderer = mixed;
      }
      else{
        renderer = new Renderer(mixed);
      }
    }
    /*
    createGraph: function(canvasId, toolbarId, parentChangeRequest, checkNodeAuthority, nodeInfoList){
      var graph;
      if(canvasId && toolbarId) {
        renderer = new Renderer(canvasId, toolbarId, parentChangeRequest, checkNodeAuthority);
        graph = new Graph(renderer);
        if(nodeInfoList){
          _initGraph(graph, nodeInfoList);
        }
      }
      else{
       graph = new Graph(renderer);
      }
      return graph;
    },
    setRender: function(mixed){
      if( mixed instanceof Renderer ){
        renderer = mixed;
      }
      else{
        renderer = new Renderer(mixed);
      }
    }*/
  };
});