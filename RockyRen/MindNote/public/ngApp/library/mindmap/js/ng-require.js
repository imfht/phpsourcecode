/**
 * Created by rockyren on 15/6/2.
 */
define(['angular', 'mindmapJS/imp/Graph', 'mindmapJS/imp/Renderer'], function(angular, Graph, Renderer){
  var mindmapModule = angular.module('mindmap', []);


  mindmapModule.directive('mindmap', function(){
    return {
      restrict: "EA",
      templateUrl: 'public/ngApp/library/mindmap/tpls/mindmap.html',
      scope: {
        mapTitle: '=',
        mapObj: '=',
        graphMethod: '='

      },
      link: function(scope, ele){
        scope.inputNodeName = '';

        var renderer = new Renderer({
          canvasId: 'mindmap-canvas'
        });
        var graph = new Graph(renderer);

        if(scope.mapTitle){
          graph.setRoot({
            label: scope.mapTitle
          });
        }
        if(scope.mapObj){
          graph.fromJson(scope.mapObj);
        }

        //对外暴露的借口
        scope.graphMethod = {
          toJson: function(){
            return graph.toJson();
          },
          getRootLabel: function(){
            return graph.root.label;
          }
        };

        //console.log(graph.getMaxNodeId());


        $('#node-plus').click(function(){
          if(graph.selected){
            graph.addNode(graph.selected, {});

          }

        });

        $('#node-cancel').click(function(){
          if(graph.selected){
            if(graph.selected.isRootNode()){
              console.log('cannot cancel root node');
            }else{
              graph.removeNode(graph.selected);
              graph.setSelected(null);
            }
          }
        });



        $('#label-group button').click(function(){
          var text = $('#label-group input').val();
          if(graph.selected){
            graph.setLabel(graph.selected, text);
            if(graph.selected.isRootNode()){
              scope.mapTitle = text;
            }

          }
        });
      }

    }
  });

  return mindmapModule.name;
});