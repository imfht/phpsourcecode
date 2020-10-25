var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'充值金额', name:'chargeMoney', width: 60},
            {title:'赠送金额', name:'giveMoney' ,width:60},
            {title:'排序号', name:'itemSort' ,width:50},
            {title:'创建时间', name:'createTime' ,width:30},
            {title:'操作', name:'op' ,width:150, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
                if(WST.GRANT.CZGL_02)h += "<a  class='btn btn-blue' onclick='javascript:location.href=\""+WST.U('admin/Chargeitems/toEdit','id='+item['id'])+'&p='+WST_CURR_PAGE+"\"'><i class='fa fa-pencil'></i>修改</a> ";
                if(WST.GRANT.CZGL_03)h += "<a  class='btn btn-red' onclick='javascript:toDel(" + item['id'] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
                return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-90),indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/Chargeitems/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadQuery(p);
}

function loadQuery(p){
    p=(p<=1)?1:p;
    var query = WST.getParams('.query');
    query.page = p;
    mmg.load(query);
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/Chargeitems/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	layer.close(box);
	           		        loadQuery(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}



function editInit(p){
	 /* 表单验证 */
    $('#adPositionsForm').validator({
            fields: {
            	chargeMoney: {
                  rule:"required",
                  msg:{required:"请输入充值金额"},
                  tip:"请输入充值金额",
                  ok:"",
                },
                giveMoney: {
                  rule:"required;",
                  msg:{required:"请输入赠送金额"},
                  tip:"请输入赠送金额",
                  ok:"",
                }
            },
          valid: function(form){
            var params = WST.getParams('.ipt');
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('admin/Chargeitems/'+((params.id==0)?"add":"edit")),params,function(data,textStatus){
              layer.close(loading);
              var json = WST.toAdminJson(data);
              if(json.status=='1'){
                  WST.msg("操作成功",{icon:1});
                  location.href=WST.U('Admin/Chargeitems/index','p='+p);
              }else{
                    WST.msg(json.msg,{icon:2});
              }
            });
      }
    });
}