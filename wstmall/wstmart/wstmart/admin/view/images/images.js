function initSummary(){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
	 $.post(WST.U('admin/images/summary'),{rnd:Math.random()},function(data,textStatus){
	       layer.close(loading);
	       var json = WST.toAdminJson(data);
	       if(json.status==1){
	    	   json = json.data;
	    	   var html = [],tmp,i=1,divLen = 0;
	    	   for(var key in json){
	    		   if(key=='_WSTSummary_')continue;
	    		   tmp = json[key];
	    		   html.push('<tr class="mmg-body wst-grid-tree-row" height="28" align="center">'
	    				     ,'<td class="wst-grid-tree-row-cell" style="width:26px;">'+(i++)+'</td>'
	    				     ,'<td class="wst-grid-tree-row-cell">'+WST.blank(tmp.directory,'未知目录')+'('+key+')'+'</td>'
	    				     ,'<td class="wst-grid-tree-row-cell" align="left">'+getCharts(json['_WSTSummary_'],tmp.data['1'],tmp.data['0'])+'</td>'
	    				     ,'<td class="wst-grid-tree-row-cell" nowrap>'+tmp.data['1']+'/'+tmp.data['0']+'</td>'
	    				     ,'<td class="wst-grid-tree-row-cell"><a class="btn btn-blue" href="'+WST.U('admin/images/lists','keyword='+key)+'"><i class="fa fa-search"></i>查看详情</a></td>');
	    	   }
	    	   $('#list').html(html.join(''));
	       }else{
	           WST.msg(json.msg,{icon:2});
	       }
	 });
	 $('#headTip').WSTTips({width:90,height:35,callback:function(v){}});  
}
function getCharts(maxSize,size1,size2){
	var w = WST.pageWidth()-400;
	var tlen = (parseFloat(size1,10)+parseFloat(size2,10))*w/maxSize+1;
	var s1len = parseFloat(size1,10)*w/maxSize;
	var s2len = parseFloat(size2,10)*w/maxSize;
	return ['<div style="width:'+tlen+'px"><div style="height:20px;float:left;width:'+s1len+'px;background:#5cb85c;"></div><div style="height:20px;float:left;width:'+s2len+'px;background:#ddd;"></div></div>'];
}
var mmg;
function initGrid(p){
   var h = WST.pageHeight();
   var cols = [
            {title:'图片', name:'imgPath', width: 50, renderer: function(val,item,rowIndex){
              
            	return "<span class='weixin'><img id='img' onmouseout='toolTip()' onmouseover='toolTip()' style='height:60px;width:60px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['imgPath']
            	+"'><span class='imged' ><img  style='height:180px;width:180px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['imgPath']+"'></span></span>";
            }},
            {title:'上传者', name:'userName' ,width:250, renderer: function(val,item,rowIndex){
               if(item['fromType']==1){
	        		return "【职员】"+item['loginName'];
	        	}else{
	        		if(WST.blank(item['userType'])==''){
	        			return '游客';
	        		}else{
	        			if(item['userType']==1){
	        				return "【商家:"+item['shopName']+"】"+item['loginName'];
	        			}else{
	        				return item['loginName'];
	        			}
	        		}
	        	}
            }},
            {title:'文件大小(M)', name:'imgSize' ,width:30},
            {title:'状态', name:'isUse' ,width:30, renderer: function(val,item,rowIndex){
               return (val==1)?"<span class='statu-yes'><i class='fa fa-check-circle'></i> 有效</span>":"<span class='statu-no'><i class='fa fa-ban'></i> 无效</span>";
            }},
            {title:'上传时间', name:'createTime' ,width:120},
            {title:'操作', name:'' ,width:80, align:'center', renderer: function(val,item,rowIndex){
                var h = '<a class="btn btn-blue btn-mright" href="javascript:toView('+item['imgId']+',\''+item['imgPath']+'\')"><i class="fa fa-search"></i>查看</a>';
	        	if(WST.GRANT.TPKJ_04)h += "<button  class='btn btn-red' onclick='javascript:toDel(" + item['imgId'] + ")'><i class='fa fa-trash-o'></i>删除</button> ";
                return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-85,indexCol: true,indexColWidth:50, cols: cols,method:'POST',checkCol:true,multiSelect:true,
        url: WST.U('admin/images/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator()
        ]
    }); 
    loadGrid(p);
}
function loadGrid(p){
	p=(p<=1)?1:p;
	mmg.load({page:p,keyword:$('#key').val(),isUse:$('#isUse').val()});
}
function toView(id,img){
    parent.showBox({title:'图片详情',type:2,content:WST.U('admin/images/checkImages','imgPath='+img),area: ['700px', '510px'],btn:['关闭']});
}
//批量删除
function toBatchDel(){
    var rows = mmg.selectedRows();
    if(rows.length==0){
        WST.msg('请选择要删除的图片',{icon:2});
        return;
    }
    var ids = [];
    for(var i=0;i<rows.length;i++){
        ids.push(rows[i]['imgId']);
    }
    var content="您确定要删除这几个文件吗?<br/>注意：删除该文件后将不可找回!";
    toDel(ids,content);
    return false;
}
function toDel(id,content){
	if(!content) content="您确定要删除该图片吗?<br/>注意：删除该图片后将不可找回!";
	var box = WST.confirm({content:content,yes:function(){
		var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		$.post(WST.U('admin/images/del'),{id:id},function(data,textStatus){
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
function toolTip(){
    WST.toolTip();
}