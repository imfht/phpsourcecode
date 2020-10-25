/**
 * Created by spatra on 15-3-5.
 */

define(['libraryJS/classHelper/ng-require', 'angular', 'angularMocks'], function(currentModule){

  //注入当前的ngModule
  beforeEach( module(currentModule) );

  describe('单元测试： TeamMindmap.library.classHelper', function(){
    var ClassHelperService;

    //测试使用的父类
    function Parent(){}
    Parent.prototype = {
      constructor: Parent,
      testMethod: function(){
        return 'Parent'
      }
    };

    //测试使用的派生类
    function Child(){}

    beforeEach(inject(function(_ClassHelperService_){
      ClassHelperService = _ClassHelperService_;
    }));

    it('method: extend', function(){
      ClassHelperService.extend(Child, Parent);

      var obj = new Child;

      expect( obj.testMethod()).toEqual('Parent');
      expect( obj instanceof Parent).toBe(true);
    });

    it('method: extendOrOverloadMethod', function(){
      ClassHelperService.extend(Child, Parent);
      ClassHelperService.extendOrOverloadMethod(Child, 'testMethod', function(){
        return 'Child'
      });

      var obj = new Child;

      expect( obj instanceof Parent).toBe(true);
      expect( obj.testMethod()).toEqual('Child');
    });

    it('method: objectEquals', function(){
      var aObj = {'name': 'value', 'changed': 'changed'};
      var bObj = {'name': 'value', 'changed': 'changed'};

      expect( ClassHelperService.objectEquals(aObj, bObj) ).toBe(true);

      bObj['changed'] = 'add';
      expect( ClassHelperService.objectEquals(aObj, bObj) ).toBe(false);

      expect( ClassHelperService.objectEquals('notObject', 'notObject') ).toBe(true);
      expect( ClassHelperService.objectEquals(123, 'notObject') ).toBe(false);
    });

    it('method: clone', function(){
      var oldObj = new Parent();
      oldObj['name'] = 'oldValue';

      var newObj = ClassHelperService.clone(oldObj);

      expect(newObj instanceof Parent).toBe(true);
      expect( ClassHelperService.objectEquals(newObj, oldObj) ).toBe(true);
      expect( newObj == oldObj ).toBe(false);

      expect( ClassHelperService.clone('notObject') ).toEqual('notObject');
    });

    it('method: update', function(){
      var fromObj = {name: 'from', add: 'add'};
      var toObj = {name: 'to'};

      ClassHelperService.update(fromObj, toObj);

      expect( ClassHelperService.objectEquals(fromObj, toObj) ).toBe(true);
    });

    it('method: isEmpty', function(){
      var emptyObj = {};
      expect(ClassHelperService.isEmpty(emptyObj)).toBeTruthy();

      var obj = {"key": "value"};
      expect(ClassHelperService.isEmpty(obj)).toBeFalsy();
    });

    it('method: setToList', function(){
      var mockSet = {1: 'a', 2: 'b', 3: 'c'};

      expect(ClassHelperService.setToList(mockSet)).toEqual(['a', 'b', 'c']);
    });

    it('method: objListToSet', function(){
      var objList = [
        {name: "spatra", age: 22},
        {name: "bbc", age:23}
      ];

      expect(ClassHelperService.objListToSet(objList, 'age')).toEqual({
        22: objList[0],
        23: objList[1]
      });
    });

  });//End of --> ClassHelperService

});