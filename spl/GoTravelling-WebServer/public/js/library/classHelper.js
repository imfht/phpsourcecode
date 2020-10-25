/**
 * Created by spatra on 15-5-20.
 */

define(function(){
  var explodes = {};

  /**
   * 模拟传统OOP语言中类的派生.
   *
   * @param child 派生类的构造函数
   * @param parent 父类的构造函数
   */
  explodes.inherit = function(child, parent){
    var agent = function(){};

    agent.prototype = parent.prototype;
    child.prototype = new agent();
    child.prototype.constructor = child;
    child.uber = parent.prototype;
  };

  /**
   * 扩展或重写派生类的方法.
   *
   * @param constructor 派生类的构造函数
   * @param methodName 派生类要重新的方法的名称
   * @param methodFun 目标函数
   */
  explodes.extendOrOverloadMethod = function(constructor, methodName, methodFun){
    constructor.prototype[ methodName ] = methodFun;
  };

  explodes.isObject = function(val){
    return val !== null && typeof val === 'object';
  };

  explodes.equal = function(lhs, rhs){
    if( !explodes.isObject(lhs) || !explodes.isObject(rhs) ){
      return lhs === rhs;
    }

    for(var prop in lhs ){
      if( lhs.hasOwnProperty(prop) ){

        if( !(prop in rhs) ){
          return false;
        } else if( explodes.isObject(lhs[prop]) && !explodes.equal(lhs[prop], rhs[prop]) ){
          return false;
        } else if( lhs[prop] !== rhs[prop] ){
          return false;
        }
      }
    }

    return true;
  };

  explodes.clone = function(obj){
    if( typeof obj !== 'object' || obj === null ){
      return obj;
    }

    var newObj = new obj.constructor;
    for( var item in obj ){
      if( typeof obj[ item ] === 'object' ){
        newObj[ item ] = explodes.clone( obj[ item ]);
      }
      else{
        newObj[ item ] = obj[ item ];
      }
    }

    return newObj;
  };

  explodes.extend = function(target, source){
    for(var prop in source ){
      if( source.hasOwnProperty(prop) ){
        target[prop] = explodes.clone(source[prop]);
      }
    }
  };

  explodes.isEmpty = function(obj){
    for(var prop in obj ){
      if( obj.hasOwnProperty(prop) ){
        return false;
      }
    }

    return true;
  };

  return explodes;
});