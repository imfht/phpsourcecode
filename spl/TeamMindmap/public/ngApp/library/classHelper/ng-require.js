/**
 * Created by spatra on 15-3-4.
 */

/**
 * 此module模拟典型的类继承(C++/Java风格)的辅助服务.
 *
 * 方法: extend , 用于模拟继承机制, 范例:
 *  function Parent(){}
 *  function Child(){}
 *  ClassHelperService.extend(Child, Parent);
 *
 * 方法: extendOrOverloadMethod, 用于重写类方法:
 *
 *  function Parent(){}
 *  Parent.prototype.parentMethod = function(){
   *   return 'Parent';
   *  }
 *  function Child(){}
 *
 *  ClassHelperService.extend(Child, Parent);
 *  ClassHelperService.extendOrOverloadMethod(Child, 'parentMethod', function(){
   *    return 'Child';
   *  });
 *
 *  方法： objectEquals, 用于比较两个类对象是否相等, 如果相等则返回true，否则返回false
 *
 *  方法： update，将(from)对象上的内容，添加或覆盖到(to)对象上.
 *
 *  方法： isEmpty, 判断对象是否为空（如果对象没有任何属性，又或者对象的属性逻辑全都是逻辑为真，则结果为true)
 *
 *  方法： objListToSet， 将对象数组转换成 key-value 的集合，第一个参数为列表（或类数组对象）， 第二个参数为作为key的字段.
 *     var objList = [{"name": "Jack", "age": 30}, {"name": "Mike", "age": 22}];
 *     var set = objListToSet(objList, 'name');
 *     //set 的形式为： {"Jack": {"name": "Jack", "age": 30}, "Mike": {"name": "Mike", "age": 22} }
 *
 * 方法： setToList， 将key-value的集合转换成列表形式，是 objListToSet 的相反操作.
 *
 * 方法： isEmpty, 当一个对象为“空”的时候返回true，否则返回false，“空”的定义是对象的所有属性的逻辑值都假
 *
 * 方法： setToList， 将一个对象的所有属性的值作为一个数组返回，一般用于提取 key-value 集合中的值
 *
 * 方法： objListToSet, 将一个对象组成的数据，依据对象某个属性为key，对象本身为value，构造一个 key-value 对的集合
 */

define(['angular'],
  function(angular){

    var module = angular.module('TeamMindmap.library.classHelper', []);

    module.factory('ClassHelperService', function(){
      var explodes = {};

      explodes['extend'] = function(child, parent){
        var agent = function(){};

        agent.prototype = parent.prototype;
        child.prototype = new agent();
        child.prototype.constructor = child;
        child.uber = parent.prototype;
      };

      explodes['extendOrOverloadMethod'] = function(constructor, methodName, methodFun){
        constructor.prototype[ methodName ] = methodFun;
      };

      explodes['objectEquals'] = function objectEquals(lhs, rhs){
        if( ! angular.isObject(lhs) || ! angular.isObject(rhs) ){
          return lhs === rhs;
        }

        for(var prop in lhs){
          if( ! (prop in rhs) ){
            return false;
          }

          if( typeof lhs[prop] === 'object' && ! objectEquals(lhs[prop], rhs[prop]) ){
            return false;
          }
          else if(lhs[prop] !== rhs[prop] ){
            return false;
          }
        }

        return true;
      };

      explodes['clone'] = function clone(obj){
        if( typeof obj !== 'object' || obj === null ){
          return obj;
        }

        var newObj = new obj.constructor;
        for( var item in obj ){
          if( typeof obj[ item ] === 'object' ){
            newObj[ item ] = clone( obj[ item ]);
          }
          else{
            newObj[ item ] = obj[ item ];
          }
        }

        return newObj;
      };

      explodes['update'] = function update(from, to){
        for(var prop in from ){
          if( ! explodes['objectEquals']( from[prop], to[prop] ) ){
            to[prop] = explodes['clone']( from[prop] );
          }
        }
      };

      explodes['isEmpty'] = function(obj){
        for(var prop in obj ){
          if( obj[prop] ){
            return false;
          }
        }

        return true;
      };

      explodes['setToList'] = function(setObj){
        var rtn = [];

        for(var prop in setObj ){
          rtn.push( setObj[prop] );
        }

        return rtn;
      };

      explodes['objListToSet'] = function(list, prop){
        var rtn = {};

        list.forEach(function(item){
          rtn[ item[prop] ] = item;
        });

        return rtn;
      };

      return explodes;
    });//End of --> ng-factory: ClassHelperService


    return module.name;
});