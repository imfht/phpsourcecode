/**
 * Created by rockyren on 15/3/7.
 */
define(['mindmapJS/imp/otherModule/DataHelper'], function(DataHelper){
  /**
   * 拖动模块
   */

  var Drag = function(aNode, aToolbar, aViewportHandle, aParentChangeRequest, aEnableRender, aCheckNodeAuthority, aType){
    var node = aNode;
    //小工具条引用,用于显示和移动小工具条
    var toolbar = aToolbar;

    var viewportHandle = aViewportHandle;

    var paper = node.shape[0].paper;
    var graph = node.graph;
    var cloneShape = null;

    var addableBoxSet;

    var lastOverlapId;

    var parentChangeRequest = aParentChangeRequest;
    var checkNodeAuthority = aCheckNodeAuthority;

    var enableRender = aEnableRender;

    var type = aType;




    function _cloneNodeShape(node){

      var newRect = node.shape[1].clone();
      newRect.attr({
        r: 7
      });
      var newLabel = node.shape[0].clone();
      var newShape = paper.set().push(newLabel).push(newRect);
      return newShape;
    }

    function _getAddableBBoxSet(){
      var addableBBoxSet = {};
      var addableSet = graph.getParentAddableNodeSet(node);

      DataHelper.forEach(addableSet, function(curNode){
        addableBBoxSet[curNode.id] = curNode.shape.getBBox();
      });
      return addableBBoxSet;
    }

    function _selectedHandle(){
      node.graph.setSelected(node);

      //设置toolbar位置
      toolbar.setToolbarPosition({
        x: node.x,
        y: node.y
      }, node.isRootNode());
    }

    function _getOverlapNodeId(){
      var nodeBBox = node.shape.getBBox();
      for(var id in addableBoxSet){
        var curBBox = addableBoxSet[id];
        if(Raphael.isBBoxIntersect(nodeBBox, curBBox)){
          return id;
        }
      }
      return null;
    }

    function moveFnc(dx, dy){
      //如果不可渲染，则不可调用move
      if(!enableRender.canRender) { return false; }
      if(checkNodeAuthority && !checkNodeAuthority(node.data)) { return false; }


      //移动节点时讲鼠标的样式设为move
      node.shape[1].node.style.cursor = 'move';

      node.shape.transform('t' + dx + ',' + dy);

      var overlapNodeId = _getOverlapNodeId();

      if(overlapNodeId !== lastOverlapId){
        if(overlapNodeId){
          graph.nodes[overlapNodeId].shape[1].attr({
            'stroke': 'blue'
          });
        }
        if(lastOverlapId){
          graph.nodes[lastOverlapId].shape[1].attr({
            'stroke': 'black'
          });
        }
      }

      lastOverlapId = overlapNodeId;
    }

    function startFnc(){
      //如果不可渲染，则不可调用start
      if(!enableRender.canRender) { return false; }
      if(checkNodeAuthority && !checkNodeAuthority(node.data)) {
        _selectedHandle();
        //@workaround:暂时直接隐藏修改(增加,删除)键
        if(!node.isRootNode()){
          var alterHideButton = document.getElementsByClassName('alter-hide');
          for(var i=0; i<alterHideButton.length; i++){
            alterHideButton[i].style.display = 'none';
          }
        }

        return false;
      }else {
        if(!node.isRootNode()){
          var alterHideButton = document.getElementsByClassName('alter-hide');
          for(var i=0; i<alterHideButton.length; i++){
            alterHideButton[i].style.display = 'inline';
          }
        }
      }

      //设置节点的选择渲染
      _selectedHandle();

      //创一个克隆的节点占位
      cloneShape = _cloneNodeShape(node);

      //@workaround:将节点设为未选择样式
      node.shape.attr({
        opacity: 0.5
      });
      node.shape[1].attr({
        stroke: 'black',
        'stroke-width': 2.5
      });
      node.shape.toFront();

      //获得当前节点可添加的父节点BBox的集合
      addableBoxSet = _getAddableBBoxSet();
      lastOverlapId = null;

    }
    function endFnc(){
      //如果不可渲染，则不可调用end
      if(!enableRender.canRender) { return false; }
      if(checkNodeAuthority && !checkNodeAuthority(node.data)) { return false; }

      cloneShape.remove();
      cloneShape = null;


      //@workaround：将节点设为被选择样式
      node.shape.attr({
        opacity: 1
      });

      node.shape[1].attr({
        stroke: '#ff0033',
        'stroke-width': 3.5
      });
      node.shape[0].toFront();

      if(lastOverlapId){
        graph.nodes[lastOverlapId].shape[1].attr({
          'stroke': 'black'
        });
      }

      var overlapNodeId = _getOverlapNodeId();
      if(overlapNodeId){
        //var newParentNode = graph.nodes[overlapNodeId];
        //graph.setParent(newParentNode, node);
        //graph.setParent(overlapNodeId, node.id);
        if(parentChangeRequest){
          parentChangeRequest({
            parentId: parseInt(overlapNodeId),
            childId: node.id
          });
        }

      }

      node.shape.transform('t' + 0 + ',' + 0);

      node.shape[1].node.style.cursor = 'default';
    }

    return {
      setDrag: function(){
        if(!node.isRootNode()){
          node.shape.drag(moveFnc, startFnc, endFnc);
          /*
          node.shape.hover(function(){
            if(checkNodeAuthority && !checkNodeAuthority(node.data)){
              node.shape[1].node.style.cursor = 'not-allowed';
              node.shape[0].node.style.cursor = 'not-allowed';
            }
          });*/
        }else{
          node.shape.mousedown(function(){
            if(!enableRender.canRender) { return false; }
            viewportHandle.mousedownHandle();
          });
          node.shape.mousemove(function(){
            if(!enableRender.canRender) { return false; }

            viewportHandle.mousemoveHandle();

          });
          node.shape.mouseup(function(){
            if(!enableRender.canRender) { return false; }
            viewportHandle.mouseupHandle();
          });

          if(type == 'desktop'){
            node.shape.mousedown(function(){
              if(!enableRender.canRender) { return false; }
              _selectedHandle();
            });
            node.shape.mousemove(function(){
              if(!enableRender.canRender) { return false; }
              if(viewportHandle.isDragging()){

                toolbar.setToolbarPosition(null);
              }


            });
            node.shape.mouseup(function(){
              if(!enableRender.canRender) { return false; }
              toolbar.setToolbarPosition({
                x: node.x,
                y: node.y
              }, node.isRootNode());
            });
          }
        }

      },
      setRootDrag: function(){

      }
    };
  };
  return Drag;
});
