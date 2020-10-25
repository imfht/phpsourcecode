$(function(){app.pageInit();})
var Cro = new Conero();
var app = Cro.extends(function(th){
    var _self = this;
    this.pageInit = function(){
        th.panelToggle('.panel-toggle');
        // 新建节点
        $('#axis_node_newlnk').click(function(){
            // th.modal();
            $('#axis_node_modal').modal();            
        });
    }
});