/**
 * Created by rockyren on 15/3/8.
 */


define(['mindmapJS/imp/Graph', 'mindmapJS/imp/otherModule/InitGraphHelper'],
  function(Graph, InitGraphHelper){
    describe('InitGraphHelper测试', function(){
      var graph, mockRenderer, initGraphHelper;
      beforeEach(function(){
        mockRenderer = jasmine.createSpyObj('mockRenderer',
          ['addNodeRender', 'translateSingleNodeRender', 'removeNodeRender',
            'setSelectedRender', 'setCanvasClick', 'setLabelRender', 'rootNodeRender', 'getCanvasWidth']);
        graph = new Graph(mockRenderer);
        var infoList = [
          {
            id: 11,
            name: 'node11',
            parent_id: null
          },
          {
            id: 12,
            name: 'node12',
            parent_id: null
          },
          {
            id: 111,
            name: 'node111',
            parent_id: 11
          },
          {
            id: 112,
            name: 'node112',
            parent_id: 11
          }
        ];
        


        initGraphHelper = InitGraphHelper(graph, infoList);
      });
      it('测试getParentInfoGroup方法', function(){
        var parentInfoGroup = initGraphHelper.createParentIdInfoGroup();
        expect(parentInfoGroup).toEqual(
          {
            'root': [
              {
                id: 11,
                name: 'node11',
                parent_id: null
              },
              {
                id: 12,
                name: 'node12',
                parent_id: null
              }
            ],
            '11': [
              {
                id: 111,
                name: 'node111',
                parent_id: 11
              },
              {
                id: 112,
                name: 'node112',
                parent_id: 11
              }
            ]
          }
        );
      });
      
      it('测试batchSetParent方法', function(){
        var parentInfoGroup = initGraphHelper.createParentIdInfoGroup();
        initGraphHelper.batchSetParent(graph.root, parentInfoGroup['root']);

        var node11 = graph.root.children['11'];
        var node12 = graph.root.children['12'];
        var node111 = node11.children['111'];
        var node112 = node11.children['112'];

        expect(node11.father).toBe(graph.root);
        expect(node12.father).toBe(graph.root);
        expect(node111.father).toBe(node11);
        expect(node112.father).toBe(node11);
        
      });
    });
});
