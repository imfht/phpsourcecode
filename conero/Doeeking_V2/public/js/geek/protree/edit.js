$(function(){
    // pupop 窗
    $('#pnode_popup_btn').click(function(){
        var option = {
            title:"父节点选择器",
            field:{
                'node_code':'节点码',
                'node_name':'节点名称',
                'no':'hidden'
            },
            post:{
                table:'project_tree',
                order:'node_code',
                map:'pro_code=\''+Cro.getQuery('code')+'\''
            },
            single:true
        };
        Cro.pupop(option);
    });
});
var Cro = new Conero();