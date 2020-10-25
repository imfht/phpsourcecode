/**
 * Created by rockyren on 15/2/4.
 */
define(['mindmapJS/imp/Graph'], function(Graph){
  describe('Graph测试', function(){
    var graph, mockRenderer;

    beforeEach(function(){
      mockRenderer = jasmine.createSpyObj('mockRenderer',
        ['addNodeRender', 'translateSingleNodeRender', 'removeNodeRender',
          'setSelectedRender', 'setCanvasClick', 'setLabelRender', 'rootNodeRender', 'getCanvasWidth']);
      graph = new Graph(mockRenderer);

    });
    describe('Graph模块测试', function(){
      var node1, node2, node3, node4, node5;
      beforeEach(function(){
        node1 = graph.root;
        node2 = graph.addNode(node1, {id: 22});
        node3 = graph.addNode(node1, {id: 33});
        node4 = graph.addNode(node2, {id: 44});
        node5 = graph.addNode(node1, {id: 55});

      });

      it('测试新增节点时的渲染方法的调用', function(){
        expect(graph.gRenderer.addNodeRender).toHaveBeenCalled();
      });
      it('测试setCanvasClick方法的调用', function(){
        expect(graph.gRenderer.setCanvasClick).toHaveBeenCalled();
      });
      it('测试rootNodeRender方法的调用', function(){
        expect(graph.gRenderer.getCanvasWidth).toHaveBeenCalled();
        expect(graph.gRenderer.rootNodeRender).toHaveBeenCalled();
      });
      it('测试direction设置', function(){
        expect(node1.direction).toBeNull();
        expect(node2.direction).toBe(1);
        expect(node3.direction).toBe(-1);
        expect(node4.direction).toBe(1);
        expect(node5.direction).toBe(1);
      });

      it('测试nodes集合', function(){
        expect(graph.nodes[22]).toEqual(node2);
        expect(graph.nodes[33]).toEqual(node3);
        expect(graph.nodes[44]).toEqual(node4);
        expect(graph.nodes[55]).toEqual(node5);
      });
      /*
      it('测试nodes集合', function(){
        expect(graph.nodes).toEqual({
          22: node2,
          33: node3,
          44: node4,
          55: node5
        })
      });*/
      it('测试父子关系', function(){
        /**
         * 测试子父节点的点和边的引用
         * @param parent
         * @param child
         */
         var checkParentChild = function(parent, child){
         //父节点的children和connectChild的正确性
         expect(parent.children[child.id]).toBe(child);
         var connectChild = parent.connectChildren[child.connectFather.id];
         expect(connectChild.source).toBe(parent);
         expect(connectChild.target).toBe(child);
         //子节点的father与connectFather的正确性
         expect(child.father).toBe(parent);
         expect(child.connectFather.source).toBe(parent);
         expect(child.connectFather.target).toBe(child);
         };

        checkParentChild(node1, node2);
        checkParentChild(node1, node3);
        checkParentChild(node1, node5);
        checkParentChild(node2, node4);

        //换父节点:将node2->node4 变为 node5->node4
        graph.setParentData(node5, node4);
        checkParentChild(node5, node4);
        //旧父节点不存在子节点的children和connectChildren
        expect(node2.children[node5.id]).toBeUndefined();
        expect(node2.connectChildren[node5.connectFather.id]).toBeUndefined();

        //设置父节点为自己,返回null
        expect(graph.setParentData(node4, node4)).toBeNull();

      });
      it('测试addEdge方法', function(){
        var edge = graph.addEdge();

      });




      it('测试removeNode方法', function(){


        graph.removeNode(node2);
        //删除渲染的调用测试
        expect(graph.gRenderer.removeNodeRender).toHaveBeenCalled();

        //删除node1与node2的联系的正确性
        expect(node1.children[node2.id]).toBeUndefined();
        expect(node2.father).toBeNull();
        expect(node2.connectFather).toBeNull();

        //删除node2和node4的联系的正确性
        expect(node2.children[node4.id]).toBeUndefined();
        expect(node4.father).toBeNull();
        expect(node4.connectFather).toBeNull();

        expect(graph.nodes[22]).toBeUndefined();
        expect(graph.nodes[44]).toBeUndefined();
        expect(graph.nodes[33]).toEqual(node3);
        expect(graph.nodes[55]).toEqual(node5);

      });

      it('测试setSelected方法', function(){
        graph.setSelected(node1);
        expect(graph.gRenderer.setSelectedRender).toHaveBeenCalled();
        expect(graph.selected).toBe(node1);
      });

      it('测试setLabel方法', function(){
        graph.setLabel(node1, '123');
        expect(node1.label).toBe('123');

        expect(graph.gRenderer.setLabelRender).toHaveBeenCalled();
      });

      it('测试_isFirstNodeRightMoreThanLeft方法', function(){
        expect(graph._isFirstNodeRightMoreThanLeft()).toBeTruthy();
        graph.addNode(node1);
        expect(graph._isFirstNodeRightMoreThanLeft()).toBeFalsy();
      });

      it('测试setRoot方法', function(){
        graph.setRoot({label: 'hello', x:100, y:100});
        expect(graph.root.label).toBe('hello');

        expect(graph.gRenderer.rootNodeRender).toHaveBeenCalled();

      });

      it('测试getChildrenNodeSet方法', function(){
        expect(graph.getChildrenNodeSet(node2)).toEqual({
          44: node4
        });
        var node1ChildrenNodeSet = graph.getChildrenNodeSet(node1);
        expect(node1ChildrenNodeSet[22]).toEqual(node2);
        expect(node1ChildrenNodeSet[33]).toEqual(node3);
        expect(node1ChildrenNodeSet[44]).toEqual(node4);
        expect(node1ChildrenNodeSet[55]).toEqual(node5);
        expect(node1ChildrenNodeSet[1]).toBeUndefined();
      });

      it('测试getParentAddableNodeSet方法', function(){
        var node2AddbaleNodeSet = graph.getParentAddableNodeSet(node2);
        expect(node2AddbaleNodeSet[33]).toEqual(node3);
        expect(node2AddbaleNodeSet[55]).toEqual(node5);
        expect(node2AddbaleNodeSet[1]).toBeUndefined();
        expect(node2AddbaleNodeSet[22]).toBeUndefined();
        expect(node2AddbaleNodeSet[44]).toBeUndefined();

      });


    });


    describe('Node模块测试', function(){
      var node1, node2, node3, node4;
      beforeEach(function(){
        //预设节点
        node1 = graph.root;
        node2 = graph.addNode(node1, {x:200, y:200});
        node3 = graph.addNode(node2, {x:300, y:300});
        node4 = graph.addNode(node1, {x:400, y:400, id:44});
      });

      it('childrenCount方法测试', function(){
        //断言node1,node2的子节点数
        var count1 = node1.childrenCount();
        expect(count1).toBe(2);
        var count2 = node2.childrenCount();
        expect(count2).toBe(1);
      });

      it('childrenWithShapeCount方法测试', function(){
        node2.shape = {};
        node4.shape = {};

        expect(node1.childrenWithShapeCount()).toBe(2);
      });

      it('getRootNode方法测试', function(){
        //通过node1,node2取得根结点,判断是否为根节点
        var root = node1.getRootNode();
        expect(root.father).toBeNull();
        root = node2.getRootNode();
        expect(root.father).toBeNull();
      });

      it('isRootNode方法测试', function(){
        //判断node1,node2是否根结点
        expect(node1.isRootNode()).toBeTruthy();
        expect(node2.isRootNode()).toBeFalsy();
      });


      it('translate方法测试', function(){
        //移动node2节点,看node2节点及其节点有无移动
        var dx = 5, dy = 6;
        node2.translate(dx, dy);
        expect(node2.x).toBe(200 + dx);
        expect(node2.y).toBe(200 + dy);

        expect(node3.x).toBe(300 + dx);
        expect(node3.y).toBe(300 + dy);

        expect(node4.x).toBe(400);
        expect(node4.y).toBe(400);

        expect(graph.gRenderer.translateSingleNodeRender).toHaveBeenCalled();


      });

      it('isFirstLevelNode方法测试', function(){
        //判断node1-3是否第一层节点
        expect(node2.isFirstLevelNode()).toBeTruthy();
        expect(node1.isFirstLevelNode()).toBeFalsy();
        expect(node3.isFirstLevelNode()).toBeFalsy();
      });
    });


  })
});