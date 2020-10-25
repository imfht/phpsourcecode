var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'导航类型', name:'navType', width: 30, renderer: function(val,item,rowIndex){
            	return (val==0)?'顶部':'底部';
            }},
            {title:'导航名称', name:'navTitle',width:30 },
            {title:'导航链接', name:'navUrl' ,width:120},
            {title:'是否显示', name:'isShow',width:20, renderer: function(val,item,rowIndex){
            	return '<span class="layui-form"><input type="checkbox" '+ ((item.isShow==1)?"checked":"" )+' class="ipt" id="isShow" name="isShow" lay-skin="switch" lay-filter="isShow" data="'+item['id']+'" lay-text="显示|隐藏"></span>';
            }},
            {title:'打开方式', name:'isOpen',width:30,renderer: function(val,item,rowIndex){
            	return (val==1)?'<span style="cursor:pointer" onclick="isShowtoggle(\'isOpen\','+item['id']+', 0)">新窗口打开</span>':'<span style="cursor:pointer" onclick="isShowtoggle(\'isOpen\','+item['id']+', 1)">页面跳转</span>';
            }},
            {title:'排序号', name:'navSort',width:10},
            {title:'操作', name:'op' ,width:80, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
	            if(WST.GRANT.DHGL_02)h += "<a  class='btn btn-blue' href='"+WST.U('admin/Navs/toEdit','id='+item['id'])+'&p='+WST_CURR_PAGE+"'><i class='fa fa-pencil'></i>修改</a> ";
	            if(WST.GRANT.DHGL_03)h += "<a  class='btn btn-red' href='javascript:toDel(" + item['id'] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-165),indexCol: true, cols: cols,method:'POST',nowrap: true,
        url: WST.U('admin/Navs/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });   
    mmg.on('loadSuccess',function(){
    	layui.form.render();
        layui.form.on('switch(isShow)', function(data){
            var id = $(this).attr("data");
            if(this.checked){
                isShowtoggle('isShow',id, 1);
            }else{
                isShowtoggle('isShow',id, 0);
            }
        });
    })   
     $('#headTip').WSTTips({width:90,height:35,callback:function(v){
       var diff = v?155:128;
       mmg.resize({height:h-diff})
    }});
    loadGrid(p)
}
function loadGrid(p){
    p = (p<=1)?1:p;
    mmg.load({page:p});
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/Navs/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	layer.close(box);
                              loadGrid(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}
function edit(id,p){
  //获取所有参数
  var params = WST.getParams('.ipt');
    params.id = id;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/Navs/'+((id==0)?"add":"edit")),params,function(data,textStatus){
      layer.close(loading);
      var json = WST.toAdminJson(data);
      if(json.status=='1'){
          WST.msg("操作成功",{icon:1});
          location.href=WST.U('Admin/Navs/index','p='+p);
      }else{
            WST.msg(json.msg,{icon:2});
      }
    });
}
function isShowtoggle(field, id, val){
	if(!WST.GRANT.DHGL_02)return;
	$.post(WST.U('admin/Navs/editiIsShow'), {'field':field, 'id':id, 'val':val}, function(data, textStatus){
		var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
                              loadGrid(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	})
}


function changeFlink(obj){
     var flink = $(obj).val();
     if(flink==1)
       $("#articles").hide();
     else
       $("#articles").show();
     
}
function changeArticles(obj){
     var url = $(obj).val();
    
     $("#navUrl").val(url);
}
