
var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
        {title:'账号名', name:'loginName' ,width:200,sortable:true},
        {title:'门店名称', name:'storeName' ,width:200,sortable:true},
        {title:'所在地区', name:'storeAddress' ,width:300,sortable:true, renderer: function(val,item,rowIndex){
            	
            	return "<div>"+item['areaNames']+item['storeAddress']+"</div><div>"+item['storeTel']+"</div>";
            }},
        {title:'实景图片', name:'storeImg' ,width:150, renderer: function(val,item,rowIndex){
            	var thumb = item['storeImg'];
	        	thumb = thumb.replace('.','_thumb.');
            	return "<span class='weixin'><img id='img' onmouseout='toolTip()' onmouseover='toolTip()' style='height:50px;width:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+thumb
            	+"'><span class='imged' ><img  style='height:180px;max-width:480px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['storeImg']+"'></span></span>";
            }},
        {title:'启用状态', name:'storeStatus' ,width:150,sortable:true,renderer: function(val,item,rowIndex){
            	return '<input type="checkbox" '+((item['storeStatus']==1)?"checked":"")+' id="storeStatus" name="storeStatus" value="1" class="ipt" lay-skin="switch" lay-filter="storeStatus" data="'+item['storeId']+'" lay-text="启用|关闭">'
            }},
        {title:'创建时间', name:'createTime' ,width:150,sortable:true},
        {title:'操作', name:'' ,width:150, align:'center', renderer: function(val,item,rowIndex){
            var h = "";
            h += "<a  class='btn btn-blue' onclick='javascript:toEdit("+item['storeId']+")'><i class='fa fa-pencil'></i>编辑</a> ";
            h += "<a  class='btn btn-red' onclick='javascript:del(" + item['storeId'] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
           
            return h;
        }}
    ];

    mmg = $('.mmg').mmGrid({height: h-89,indexCol: true, cols: cols,method:'POST',
        url: WST.U('shop/stores/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    mmg.on('loadSuccess',function(data){
    	layui.form.render();
    	layui.form.on('switch(storeStatus)', function(data){
           var id = $(this).attr("data");
		   if(this.checked){
			   toggleStoreStatus(id, 1);
		   }else{
			   toggleStoreStatus(id, 0);
		   }
		});
    });
    loadGrid(p);
}

function toggleStoreStatus( storeId, status){
	$.post(WST.U('shop/stores/setStoreStatus'), {'storeId':storeId, 'storeStatus':status}, function(data, textStatus){
		var json = WST.toJson(data);
		if(json.status=='1'){
			WST.msg("操作成功",{icon:1});
			loadGrid(WST_CURR_PAGE);
		}else{
			WST.msg(json.msg,{icon:2});
		}
	})
}


function loadGrid(p){
	p=(p<=1)?1:p;
    var parmas = WST.getParams('.s-query');
    parmas.areaIdPath = WST.ITGetAllAreaVals('areaId','j-areas').join('_');
    mmg.load(parmas);
}

function toEdit(storeId){
	location.href = WST.U('shop/stores/edit','storeId='+storeId+'&p='+WST_CURR_PAGE);
}
function toAdd(){
    location.href = WST.U('shop/stores/add','p='+WST_CURR_PAGE);
}
function toolTip(){
    WST.toolTip();
}


//删除角色
function del(id){
	var c = WST.confirm({content:'删除门店，并将删除门店下所有店员帐号与门店的关系，您确定要删除吗?',yes:function(){
		layer.close(c);
		var load = WST.load({msg:'正在删除，请稍后...'});
		$.post(WST.U('shop/stores/del'),{storeId:id},function(data,textStatus){
			layer.close(load);
		    var json = WST.toJson(data);
		    if(json.status==1){
		    	WST.msg(json.msg,{icon:1});
                loadGrid(WST_CURR_PAGE);
		    }else{
		    	WST.msg(json.msg,{icon:2});
		    }
		});
	}});
}


