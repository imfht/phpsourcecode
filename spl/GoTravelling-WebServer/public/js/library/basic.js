/**
 * Created by spatra on 15-4-19.
 */

define(function(){
  var whenReady = (function(){
    var funcs = []; // 当获得事件时，要运行的函数
    var isReady = false; // 当触发事件处理程序时，切换到true

    // 当文档准备就绪时，调用事件处理程序
    function handler(e){
      if( isReady ) return;

      if( document.addEventListener || e.type == 'load' || document.readyState ){
        detach();
        ready();
      }
    }

    // Clean-up method for dom ready events
    function detach() {
      if (document.addEventListener) {
        document.removeEventListener("DOMContentLoaded", handler, false);
        window.addEventListener("load", handler, false)
      } else {
        document.detachEvent("onreadystatechange");
        window.detachEvent("onload");
      }
    }

    // Handle when the DOM is ready
    function ready() {
      // 运行所有注册函数
      // 注意每次都要计算fucs.length,
      // 以防这些函数的调用会导致注册更多的函数
      for (var i = 0; i < funcs.length; i++) {
        funcs[i].call(document);
      };

      // 现在设置ready标识为true，并移除所有函数
      isReady = true;
      funcs = null;
    }

    // Standards-based browsers support DOMContentLoaded
    // 为接受到的任何事件注册处理程序
    if (document.addEventListener) {
      document.addEventListener("DOMContentLoaded", handler, false);
      window.addEventListener("load", handler, false);
    }
    // If IE event model is used
    else if (document.attachEvent) {
      document.attachEvent("onreadystatechange", handler);
      window.attachEvent("onload", handler);
      // If IE and not a frame
      // continually check to see if the document is ready
      var top = false;

      try {
        top = window.frameElement == null && document.documentElement;
      } catch (e) {}

      if (top && top.doScroll) {
        (function doScrollCheck() {
          if (!ready) {

            try {
              // Use the trick by Diego Perini
              // http://javascript.nwbox.com/IEContentLoaded/
              top.doScroll("left");
            } catch (e) {
              return setTimeout(doScrollCheck, 50);
            }

            // detach all dom ready events
            detach();

            // and execute any waiting functions
            ready();
          }
        })();
      }
    }

    return function whenReady(f) {
      if (ready) f.call(document);
      else funcs.push(f);
    };
  })();

  var addEvent = function(target, type, handler){
    if( target.addEventListener ){
      target.addEventListener(type, handler, false);
    }
    else{
      target.attachEvent('on' + type, function(event){
        return handler.call(target, event);
      })
    }
  };

  var applyTarget = function(target, handler){
    if( target === undefined || target === null ) return;

    if( target.hasOwnProperty('length') ){
      for(var i = 0, length = target.length; i < length; ++i ){
        handler.apply(target[i]);
      }
    }
    else{
      handler.apply(target);
    }

    return target;
  };

  return {
    'addEvent': addEvent,
    'whenReady': whenReady,
    'applyTarget': applyTarget
  };
});
