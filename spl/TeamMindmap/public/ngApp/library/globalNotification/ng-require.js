/**
 * Created by spatra on 15-3-4.
 */

/**
 * 此模块提供全局性的消息提示功能，并依赖于第三方模块 angular-toasty.
 *  Bower安装指令： bower install angular-toasty -S
 *
 *  使用此模块还需要引入CSS文件(angular-toasty-directory为Angular Toasty的目录）:
 *    angular-toasty-directory/css/ng-toasty.css
 *
 *  还需要在使用该通知的视图(template)内引入 `tpls/notification-template.html`, 示例：
 *    views: {
 *      '': {
 *        template: '<div ui-view="container"></div><div ui-view="globalNotification"></div>'
 *      },
 *      'globalNotification@lesson': {
 *        templateUrl: baseUrl + 'globalNotification/tpls/notification-template.html'
 *      }
 *    }
 *
 *  除引入模块`TeamMindmap.library.globalNotification`之外，在使用的作用域内进行初始化，如在$rootScope中:
 *    app.run(['$rootScope', 'GlobalNotificationService', function($rootScope, GlobalNotificationService){
 *      GlobalNotificationService.init($rootScope);
 *    });
 *
 *   使用示例:
 *   //除success之外，还有:warning/waiting/error
 *
 *   $scope.$emit('message:success', {
 *      title: '消息提示的标题',
 *      msg: '消息提示内容'
 *   });
 */

define(['angular', 'libraryJS/classHelper/ng-require', 'angularAnimate', 'angularToasty', 'angularUIRouter'],
  function(angular, classHelperModule){

    var globalNotificationModule = angular.module(
      'TeamMindmap.library.globalNotification',
      ['ngAnimate', 'toasty', 'ui.router', classHelperModule]
    );

    var currentModuleBaseUri = 'ngApp/library/globalNotification/';

    globalNotificationModule.provider('GlobalNotification', {
      /**
       * 默认的配置对象
       */
      _options: {
        title: '',
        msg: '',
        timeout: 5000,
        showClose: true,
        clickToClose: false,
        myData: 'Testing 1 2 3',
        onClick: function(){}
      },
      /**
       * 用一个对象的属性来更新一个原有的对象（Key-Value对覆盖）
       * @param sourceObj 原有的对象
       * @param givenObj 用于扩展原有对象的对象
       * @private
       */
      _updateObj: function(sourceObj, givenObj){
        for(var prop in givenObj ){
          if( sourceObj.hasOwnProperty(prop) ){
            sourceObj[prop] = givenObj[prop];
          }
        }
      },
      /**
       * 修改默认的配置对象
       * @param opts
       */
      setOptions: function(opts){
        if( typeof opts !== 'object' ){
          throw '错误的配置参数(TeamMindmap.library.globalNotification)';
        }

        var self = this;
        self._updateObj(self._options, opts);
      },
      /**
       * 得到全局通知相关的模板路径
       * @returns {string}
       */
      getTemplateUri: function(){
        return currentModuleBaseUri + 'notification-template.html';
      },

      $get: ['toasty', '$state', 'ClassHelperService', function(toasty, $state, ClassHelperService){

        var self = this;
        var messagesTypes = ['success', 'warning', 'info', 'error', 'wait'];

        /**
         * 获得扩展后的对象
         *
         * @param options
         * @returns {*}
         */
        function getExtendObj(options){
          var extendObj;

          extendObj = ClassHelperService.clone(self._options);
          self._updateObj(extendObj, options);

          //根据clickAction的类型,设置不同的点击事件
          if(extendObj.hasOwnProperty('clickAction')){

            //如果clickAction是string,则点击时状态跳转
            if(typeof extendObj.clickAction == 'string') {

              extendObj.onClick = function() {
                $state.go(extendObj.clickAction);

              }
            }
            //如果clickAction是function,则点击时调用该函数
            else if(typeof extendObj.clickAction == 'function'){
              extendObj.onClick = function() {
                extendObj.clickAction();
              }
            }

          }
          return extendObj;
        }

        function extendsWait(options, userOptions){

          options['onAdd'] = function(toasty){
            userOptions.show = function(afterOptions){
              toasty.title = afterOptions.title || '加载成功';
              toasty.msg = afterOptions.msg || '';
              toasty.timeout = 4000;
              var type = afterOptions.type || 'success';
              toasty.setType(type);
              toasty.showClose = true;
            }

          };
          return options;

         }

        return {
          init: function(scope){
            messagesTypes.forEach(function(currentType){
              //实现监听
              scope.$on('message:' + currentType, function(e, userOptions){
                var options;

                if( userOptions ){
                  options = getExtendObj(userOptions);
                }
                else{
                  options = defaultOptions;
                }
                if(currentType === 'wait'){
                  //需要设置userOptions.show，从而使外部可以设置等待之后的样式
                  options = extendsWait(options, userOptions);
                }

                toasty.pop[currentType](options);
              });
            });
          }
        };

      }]
    });//End of ng-provider:GlobalNotification

    return globalNotificationModule.name;
});