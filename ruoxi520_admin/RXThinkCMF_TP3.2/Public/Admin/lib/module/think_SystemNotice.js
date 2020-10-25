/**
 *	通知公告
 *
 *	@auth lys
 *	@date 2018-12-04
 */
layui.use(['form','func'],function(){
    var form = layui.form,
        func = layui.func,
        $ = layui.$;

    if(A=='index') {

        //【TABLE列数组】
        var cols = [
            { type:'checkbox', fixed: 'left' }
            ,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
            ,{ field:'title', width:400, title: '公告标题', align:'center', }
            ,{ field:'format_add_user', width:150, title: '添加人', align:'center', sort: true }
            ,{ field:'format_add_time', width:180, title: '添加时间', align:'center', }
            ,{ fixed: 'right', width:180, title: '功能操作', align:'center', toolbar: '#toolBar' }
        ];

        //【TABLE渲染】
        func.tableIns(cols,"tableList");

        //【设置弹框】
        func.setWin("通知公告");

    }

});
