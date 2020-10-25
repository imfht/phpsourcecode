/**
 * Created by rockyren on 15/3/7.
 */
define([], function(){
  /**
   * 小工具条模块
   * @param toolbarId
   * @param aViewBox
   * @returns {{setToolbarPosition: Function, translateToolbar: Function}}
   * @constructor
   */
  var Toolbar = function(toolbarId, aViewBox){
    //toolbar的dom对象
    var toolbar = document.getElementById(toolbarId);
    //视野的位置对象({x,y,width,height})
    var viewBox = aViewBox;

    //@workaround: 根据节点是否为根节点,调整toolbar的button显示
    function resetToolbarButton(isRoot){
      var rootHideButton = document.getElementsByClassName('root-hide');
      if(isRoot){
        for(var i=0;i<rootHideButton.length;i++){
          rootHideButton[i].style.display = 'none';
        }
        var addButton = document.getElementById('node-plus');
        addButton.style.display = 'inline';
      }else{
        for(var i=0;i<rootHideButton.length;i++){
          rootHideButton[i].style.display = 'inline';
        }
      }
    }


    return {
      /**
       * 设置小工具条位置
       * @param points: 设置的位置({x,y}),为null时隐藏孝工具条
       * @param isRoot
       */
      setToolbarPosition: function(points, isRoot){
        resetToolbarButton(isRoot);
        if(points) {
          var left = points.x - viewBox.x;
          var top = points.y - 38 - viewBox.y;
          toolbar.style.left = left + 'px';
          toolbar.style.top = top + 'px';
          toolbar.style.display = 'block';
        }else{
          toolbar.style.display = 'none';
        }
      },
      /**
       * 移动小工具条
       * @param dPoints
       */
      translateToolbar: function(dPoints){
        if(dPoints){
          var left = parseInt(toolbar.style.left);
          var top = parseInt(toolbar.style.top);
          left += dPoints.x;
          top += dPoints.y;
          toolbar.style.left = left + 'px';
          toolbar.style.top = top + 'px';
        }
      },
      resetToolbarButton: resetToolbarButton
    };
  };
  return Toolbar;
});