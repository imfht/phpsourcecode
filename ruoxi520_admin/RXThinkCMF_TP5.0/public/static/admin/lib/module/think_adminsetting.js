// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 *	广告管理
 *	@auth 牧羊人
 *	@date 2018-04-14
 */
layui.use(['form','layer','table','laytpl'],function(){
	
	//声明变量
	var layer = parent.layer === undefined ? layui.layer : top.layer
	,table = layui.table
	,form = layui.form
	,layedit = layui.layedit
	,laytpl = layui.laytpl
	,$ = layui.$;
	
	//调用API接口渲染TABLE(方法级)
	var tableIns = table.render({
		elem : "#tableList"
		,url : "/?app="+APP
		,method: 'post'
		,cellMinWidth : 150
		,page : true
		,height : "full-125"
		,limit : 20
		,limits : [20,30,40,50,60,70,80,90,100,150,200]
		,cols : [[
			{ type:'checkbox', fixed: 'left' }
		  ,{ field:'id', width:80, title: 'ID', align:'center', sort: true, fixed: 'left' }
		  ,{ field:'title', width:200, title: '广告标题', align:'center' }
		  ,{ field:'cover_url', width:100, title: '广告封面', align:'center', templet:function(d){
              return '<a href="'+d.cover_url+'" target="_blank"><img src="'+d.cover_url+'" height="26" /></a>';
          } }
		  ,{ field:'t_type', width:100, title: '广告类型', align:'center' }
		  ,{ field:'ad_sort_name', width:150, title: '所属广告位', align:'center' }
		  ,{ field:'description', width:400, title: '描述', align:'center' }
		  ,{ field:'format_add_user', width:120, title: '添加人', align:'center' }
		  ,{ field:'format_add_time', width:200, title: '添加时间', align:'center', sort: true }
		  ,{ field:'sort_order', width:120, title: '排序', align:'center' }
		  ,{ fixed:'right', width:150, title: '功能操作', align:'center', toolbar: '#toolBar' }
		]]
	});
	
	//监听工具条
	table.on('tool(tableList)', function(obj){
		var data = obj.data
		,layEvent = obj.event;
		
		if(layEvent === 'del'){
			layer.confirm('您确定要删除吗？删除后将无法恢复！', function(index){
				
				$.ajax({
                    url:"/?app="+APP+"&act=drop",
                    dataType:"json",
                    type:"POST",
                    data:{"id":data.id},
                    beforeSend:function () {
                        layer.msg('正在删除。。。', {
                            icon: 16
                            ,shade: 0.01
                            ,time: 0
                        });
                    },
                    success:function(res){
                        if(res.success){
                        	//2秒后关闭
                            layer.msg(res.msg,{ icon: 1,time: 1000}, function () {
                            	//tableIns.reload();
                            	obj.del();
                				layer.close(index);
                            });
                        }else{
                            layer.msg(res.msg,{ icon: 5 });
                        }
                    }
                });
				
			});
		} else if(layEvent === 'edit'){
			layer.msg('编辑操作');
			edit(data.id,data);
		}
	});
	
	//批量删除
    $(".btnDAll").click(function(){
        var checkStatus = table.checkStatus('tableList'),
            data = checkStatus.data,
            ids = [];
        if(data.length > 0) {
            for (var i in data) {
                ids.push(data[i].id);
            }
            var idsStr = ids.join(",");
            layer.confirm('确定删除选中的数据吗？', { icon: 3, title: '提示信息' }, function (index) {
            	
            	$.ajax({
                    url:"/?app="+APP+"&act=batchDrop",
                    dataType:"json",
                    type:"POST",
                    data:{"id":idsStr,"changeAct":0},
                    beforeSend:function () {
                        layer.msg('正在提交。。。', {
                            icon: 16
                            ,shade: 0.01
                            ,time: 0
                        });
                        $('#submitForm').attr('disabled',"true");
                    },
                    success:function(res){
                        $('#submitForm').removeAttr("disabled");
                        layer.closeAll();
                        if(res.success){
                        	//2秒后关闭
                            layer.msg(res.msg,{ icon: 1,time: 1000}, function () {
                            	tableIns.reload();
                            });
                        }else{
                            layer.msg(res.msg,{ icon: 5 });
                        }
                    }
                });
            	
            })
        }else{
            layer.msg("请选择需要删除的数据");
        }
    });
    
    //监听选择类型
    form.on('select(t_type)', function (data) {
		if (data.value == 4) {
			$(".t_type").removeClass("layui-hide");
		}else {
			$(".t_type").addClass("layui-hide");
		} 
    });
	
	//搜索功能
	$(".search_btn").on("click",function(){ 
        if($(".searchVal").val() != ''){
            table.reload("tableList",{
                page: {
                    curr: 1 //重新从第 1 页开始
                },
                where: {
                    key: $(".searchVal").val()  //搜索的关键字
                }
            })
        }else{
            layer.msg("请输入搜索的内容");
        }
    });
	
	//选择模块
	$("#type_name").click(function(){
        var index = layui.layer.open({
            title : "选择模块",
            type : 2,
            area : ["1200px","700px"],
            content : ["?app=link&act=index&id="+id+"&simple=1", 'no'],
            //closeBtn: false,
            shadeClose: true,  
            shade: 0.7,
            maxmin: true, //开启最大化最小化按钮
            skin: 'layui-layer-rim', //加上边框
            success : function(layero, index){
            	//给子页面传值
            	var body = layer.getChildFrame('body', index);
                body.contents().find("#test_id").val("1212");

            	//TODO...
                setTimeout(function(){
                    layer.tips('点击此处返回列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },500);

            },
            end: function (layero,index) {
            	
            }
        });
	});
	
	//添加
	$(".btnAdd").click(function(){
		edit(0);
    });
	
	//添加或编辑
	function edit(id,data){
		var title = '新增广告';
        if(id>0){
            title = '修改广告';
        }
        var index = layui.layer.open({
            title : title,
            type : 2,
            content : "?app="+APP+"&act=edit&id="+id,
            success : function(layero, index){
            	//TODO...
                setTimeout(function(){
                    layer.tips('点击此处返回列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },500)
            },
//            end: function () {
//                location.reload();
//            }
        });
        layui.layer.full(index);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
        $(window).on("resize",function(){
        	layui.layer.full(index);
        });
    };
    
	//监听提交
	form.on('submit(submitForm)', function(data){
		var index = layer.msg('数据提交中，请稍候',{icon: 16,time:false,shade:0.2});
		$.post(aUrl, data.field, function(data){
			if (data.success) {
				layer.close(index);
				layer.msg("保存成功！");
				layer.closeAll("iframe");
				
				//刷新父页面
	            parent.location.reload();
				
				return false ;
			}else{
				layer.close(index);
				layer.msg(data.msg);
			}
		}, 'json');
		
		return false;
	});

});

////回调方法
//function setAdVal(type_id,type_name){
//	layui.$("#type_name").val(type_name);
//  alert(type_id+","+type_name);
//}