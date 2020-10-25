/**
 * Created by spatra on 15-3-4.
 */

/**
 * 此模块为项目提供Markdown编辑和预览的通用 ng-directive 或 其他相关的扩展
 *
 * 使用此模块需要进行以下配置：
 * 1. 通过bower安装boostrap-markdown，用于Markdown的在线编辑：bower install boostrap-markdown -S
 *
 * 2. 通过bower安装markdown，用于Markdown的在线预览：bower install markdown -S
 *
 * 3. 在requirejs中引入相关的js文件：
 * 示例：
 *   paths : {
 *     'bootstrapMarkdown': 'packages/bower/bootstrap-markdown/js/bootstrap-markdown',
 *     'markdownJs': 'packages/bower/markdown/lib/markdown',
 *
 *     //以下2个文件用于Markdown的前端解析（参考 'public/packages/pagedown-converter/'下的README.md）
 *      'markdownConverterJS': 'packages/pagedown-converter/Markdown.Converter',
 *      'markdownSanitizerJS': 'packages/pagedown-converter/Markdown.Sanitizer',
 *   }
 *
 *   shim : {
 *    'bootstrapMarkdown': {
 *         deps: ['bootstrap', 'markdownJs'],
 *         exports: 'bootstrapMarkdown'
 *     },
 *     'markdownSanitizerJS': {
 *         deps: ['markdownConverterJS'],
 *         exports: 'markdownSanitizerJS'
 *     }
 *   }
 *
 */

define(['angular', 'bootstrapMarkdown', 'markdownJs', 'markdownConverterJS', 'markdownSanitizerJS'],
  function(angular){

    var mdModule = angular.module('TeamMindmap.library.markdownEditor', []);

    /**
    * angular指令，实现Markdown编辑器的预览功能
    *
    * 范例：
    *  <div markdown-previewer bind-content="content"></div>
    *
    */
    mdModule.directive('markdownPreviewer', ['$window', '$sce',
      function($window, $sce){

        var converter = $window.Markdown.getSanitizingConverter();

        return {
          template: "<div ng-bind-html='sanitisedHtml' />",
          restrict: 'EA',
          replace: true,
          scope: {
            contentInMd: '=bindContent' ,
            class: '='
          },
          link: function(scope) {
            scope.$watch('contentInMd', function(value) {
              if (value !== undefined && value !== '') {
                scope.html = converter.makeHtml(value);
                scope.sanitisedHtml = $sce.trustAsHtml(scope.html);
              }
            });
          }
        };
      }]);//End of --> markdownPreviewer

    /**
    * angular指令，实现对Markdown编辑器进行配置和初始化
    *
    * 范例：
    *  [1]
    *  <textarea  ng-model="message.content" placeholder="请输入私信内容" markdown-editor>
    *  [2]: 需要隐藏指定的按钮（按钮命名参考：http://toopay.github.io/bootstrap-markdown/）
    *  <textarea
    *    ng-model="message.content"
    *    placeholder="请输入私信内容"
    *    markdownHiddenButtons＝"需要隐藏的按钮１，需要隐藏的按钮２"
    *    markdown-editor>
    *  [3]:full-screen用来控制是否启用全屏按钮，默认为允许。enable表示允许，disable表示不允许
     * <textarea full-screen="disable" ng-model="message.content" placeholder="请输入私信内容" markdown-editor>
    */
    mdModule.directive('markdownEditor', ['$window', function($window){

      /**
       * Markdown编辑器的本地化语言配置
       */
      $window.jQuery.fn.markdown.messages['zh'] = {
        'Bold': '加粗',
        'Italic': '斜体',
        'Preview': '预览',
        'Heading': '标题',
        'emphasized text': '强调',
        'Image': '图片',
        'Unordered List': '无序列表',
        'Ordered List': '有序列表',
        'Code': '代码',
        'Quote': '引用',
        'URL/Link': '链接',
        'Save': '保存'
      };

      return {
        restrict: 'EA',
        replace: false,
        scope: {
          markdownHiddenButtons: '@markdownHiddenButtons',
          focus: '=',
          fullScreen: '@',
          hiddenCallback: '&hiddenCallback'
        },
        link: function(scope, element, attrs) {
          var hiddenButtons = scope.markdownHiddenButtons ? scope.markdownHiddenButtons.split(',') : [];

          //处理是否启用全屏按钮
          if( scope.fullScreen === undefined ){
            scope.fullScreen = true;
          }
          else{
            scope.fullScreen = (scope.fullScreen === 'disable') ? false : true;
          }


          //启动Bootstrap-Markdown
          element.markdown({
            hiddenButtons: hiddenButtons,
            language: 'zh',
            fullscreen: {enable: scope.fullScreen}
          });

          scope.$watch('focus', function(newValue){
            if( newValue === true ){
              setTimeout(function(){
                element.focus();
              }, 200);
            }
          });

          element.blur(function(event){

            var relateElement = event.relatedTarget;

            if( relateElement && relateElement.nodeName === 'BUTTON' && relateElement.tabIndex === -1 ) return;

            scope.$apply(scope.hiddenCallback({}));
          });

        }
      };
    }]);//End of --> markdownEditor

    /**
    * angular指令，去除文本中的一些特殊字符和Markdown的语法标记，便于预览
    *
    * 范例：
    *   <div markdown-extractor bind-content="content"></div>
    */
    mdModule.directive('markdownExtractor', ['$sce',
      function($sce){

        //设置解析Markdown的正则表达式
        var regString = /[#*`>]+/g;

        var extract = function(scope, markdownText, regString) {
          if ( markdownText !== undefined ) {
            //确保为文本类型
            markdownText += '';

            //使用javascript的正则表达式去除一些特殊字符和Markdown的语法标记
            scope.html = markdownText.replace(regString, ' ');
            scope.sanitisedHtml = $sce.trustAsHtml(scope.html);
          }
        };

        return {
          template: "<div ng-bind-html='sanitisedHtml' />",
          restrict: 'EA',
          replace: true,
          scope: {
            contentInPre: '=bindContent'
          },
          link: function (scope) {
            //监听变化，重新渲染
            scope.$watch('contentInPre', function (value) {
              extract(scope, value, regString);
            });
          }
        };
      }]);//End of --> markdownExtractor


    //End of module
    return mdModule.name;
});