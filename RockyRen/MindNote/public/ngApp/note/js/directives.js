/**
 * Created by rockyren on 15/5/16.
 */
define(['noteJS/module', 'angular'], function(noteModule, angular){
  noteModule.directive('catalogueMenu', function(){
    return {
      restrict: 'EA',
      templateUrl: 'public/ngApp/note/tpls/menu.html',
      scope: {
        showMenuIcon: '=',
        menuType: '@',  //'group-menu' | 'notebook-menu'
        translateToType: '@' //'group-notebook-translate-li | notebook-translate-li'
      },
      link: function(scope, element){

        var menuPanelClass = '.' + scope.menuType;

        //console.log(element.find('.menu-icon'));
        var $menuIcon = element.find('.menu-icon');
        //监听menu-icon的点击:点击后菜单框到对应icon下面
        $menuIcon.click(function(){
          var menuIconDom = $(this).get(0);
          var menuPosition = getElementPos(menuIconDom);
          //获取菜单键的高度
          var iconHeight = menuIconDom.clientHeight + 2;

          //隐藏其他所有的菜单框
          angular.element('.note-menu').hide();

          //显示panel
          $(menuPanelClass).show().css({
            top: menuPosition.y + iconHeight + 'px',
            left: menuPosition.x + 'px'
          });

          //隐藏所有translate-li
          $('.translate-switch-li').hide();

          //显示对应的translate-li
          if(scope.translateToType){
            $('.' + scope.translateToType).show();
          }

        });

        /**
         * 获取元素的文档坐标
         * @param ele
         * @returns {{x: number, y: number}}
         */
        function getElementPos(ele){
          var x = 0, y = 0;
          //循环累加偏移量
          for(var e = ele; e != null; e = e.offsetParent){
            x += e.offsetLeft;
            y += e.offsetTop;
          }

          //再次循环所有的祖先元素，减去滚动的偏移量
          for(var e = ele.parentNode; e != null && e.nodeType == 1; e = e.parentNode){
            x -= e.scrollLeft;
            y -= e.scrollTop;
          }

          return {
            x: x,
            y: y
          }
        };
      }
    }
  });

  /**
   * 用于设置div#note的事件的指令
   */
  noteModule.directive('noteHideMenu', function(){
    return {
      restrict: 'A',
      replace: true,
      link: function(scope, element){
        //点击背景隐藏菜单框
        element.click(function(event){
          if(!angular.element(event.target).hasClass('menu-icon')){
            angular.element('.note-menu').hide();
          }

        });


      }
    }
  });


  noteModule.directive('menuOperation', function(){
    return {
      restrict: 'A',
      replace: true,
      link: function(scope, element){
        //点击菜单框中的内容时，隐藏菜单框
        element.click(function(){
          angular.element('.note-menu').hide();
        });

        element.find('.translate-item').hover(function(){
          element.find('.translate-list').show();
        }, function(){
          element.find('.translate-list').hide();
        });

      }
    }
  });

  noteModule.directive('firstFocus', function(){
    return {
      restrict: 'A',
      replace: true,
      link: function(scope, element){
        angular.element(element).focus();
      }
    }
  });

  noteModule.directive('textareaNotEditable', function(){
    return {
      restrict: 'A',
      replace: true,
      link: function(scope, element){
        console.log($('.note-info-text').find('div').get(0));
      }
    }
  });
});