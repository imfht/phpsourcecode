var mmg;
function initGrid(p){
    var parentId=$('#h_areaId').val();
    var h = WST.pageHeight();
    var cols = [
            {title:'地区名称', name:'areaName', width: 300},
            {title:'是否显示', name:'isShow', width: 30,renderer: function(val,item,rowIndex){
            	return '<input type="checkbox" '+((item['isShow']==1)?"checked":"")+' name="isShow2" lay-skin="switch" lay-filter="isShow2" data="'+item['areaId']+'" lay-text="显示|隐藏">';

            }},
            {title:'排序字母', name:'areaKey', width: 30},
            {title:'排序号', name:'areaSort', width: 30},
            {title:'操作', name:'' ,width:140, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
                h += "<a  class='btn btn-blue' onclick='javascript:toView("+item['areaId']+")'><i class='fa fa-search'></i>查看</a> ";
                if(WST.GRANT.DQGL_02)h += "<a  class='btn btn-blue' onclick='javascript:toEdit("+item['areaId']+","+item["parentId"]+")'><i class='fa fa-pencil'></i>修改</a>";
                if(WST.GRANT.DQGL_03)h += "<a  class='btn btn-red' onclick='javascript:toDel(" + item['areaId'] + ")'><i class='fa fa-trash-o'></i>删除</a>";
                return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-89,indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/areas/pageQuery','parentId='+parentId), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    }); 
    mmg.on('loadSuccess',function(){
    	layui.form.render('','gridForm');
        layui.form.on('switch(isShow2)', function(data){
            var id = $(this).attr("data");
            if(this.checked){
  				toggleIsShow(0,id);
  			}else{
  				toggleIsShow(1,id);
  			}
        });
    })
    loadQuery(p);
}
function loadQuery(p){
    p=(p<=1)?1:p;
    mmg.load({page:p});
}

function toggleIsShow(t,v){
	if(!WST.GRANT.DQGL_02)return;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    	$.post(WST.U('admin/areas/editiIsShow'),{id:v,isShow:t},function(data,textStatus){
			  layer.close(loading);
			  var json = WST.toAdminJson(data);
			  if(json.status=='1'){
			    	WST.msg(json.msg,{icon:1});
                  loadQuery(WST_CURR_PAGE);
			  }else{
			    	WST.msg(json.msg,{icon:2});
			  }
		});
}

function toReturn(){
	location.href=WST.U('admin/areas/index','parentId='+$('#h_parentId').val()+'&p='+WST_CURR_PAGE);
}

function letterOnblur(obj){
	if($.trim(obj.value)=='')return;
	var loading = WST.msg('正在生成排序字母，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/areas/letterObtain'),{code:obj.value},function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status == 1){
			$('#areaKey').val(json.msg);
		}
	});
}

function toEdit(id,pid){
	$('#areaForm')[0].reset();
	if(id>0){
		var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
		$.post(WST.U('admin/areas/get'),{id:id},function(data,textStatus){
			layer.close(loading);
			var json = WST.toAdminJson(data);
			if(json){
				WST.setValues(json);
				layui.form.render();
				editsBox(id);
			}
		});
	}else{
		WST.setValues({parentId:pid,areaId:0});
		layui.form.render();
		editsBox(id);
	}
}
function toView(id){
	location.href = WST.U('admin/areas/index','parentId='+id);
}
function editsBox(id){
	var box = WST.open({title:(id>0)?'修改地区':"新增地区",type:1,content:$('#areasBox'),area: ['460px', '360px'],btn:['确定','取消'],
		end:function(){$('#areasBox').hide();},yes:function(){
		$('#areaForm').submit();
	          }});
	$('#areaForm').validator({
	    fields: {
	    	areaName: {
	    		tip: "请输入地区名称",
	    		rule: '地区名称:required;length[~10];'
	    	},
		    areaKey: {
	    		tip: "请输入排序字母",
	    		rule: '排序字母:required;length[~1];'
	    	},
	    	areaSort: {
            	tip: "请输入排序号",
            	rule: '排序号:required;length[~8];'
            }
	    },
	    valid: function(form){
	        var params = WST.getParams('.ipt');
	        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	    		$.post(WST.U('admin/areas/'+((id>0)?"edit":"add")),params,function(data,textStatus){
	    			  layer.close(loading);
	    			  var json = WST.toAdminJson(data);
	    			  if(json.status=='1'){
	    			    	WST.msg(json.msg,{icon:1});
	    			    	$('#areasBox').hide();
	    			    	layer.close(box);
                          loadQuery(WST_CURR_PAGE);
	    			  }else{
	    			        WST.msg(json.msg,{icon:2});
	    			  }
	    		});
	    }
	});
}

function toDel(id){
	var box = WST.confirm({content:"您确定要删除该地区吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/areas/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg(json.msg,{icon:1});
	           			    	layer.close(box);
                              loadQuery(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}