var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'位置名称', name:'positionName', width: 100},
            {title:'宽度', name:'positionWidth' ,width:30},
            {title:'高度', name:'positionHeight' ,width:30},
            {title:'位置类型', name:'' ,width:30, align:'center', renderer: function(val,item,rowIndex){
               var pName;
               switch(item['positionType']){
                  case 2:
                    pName='微信版';
                    break;
                  case 3:
                    pName='手机版';
                    break;
                  case 4:
                    pName='APP版';
                    break;
                  default:
                    pName='PC版';
                    break;
               }
               return pName;
            }},
            {title:'位置代码', name:'positionCode' ,width:40},
            {title:'操作', name:'' ,width:120, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
                if(WST.GRANT.GGGL_00)h += "<a  class='btn btn-blue' href='javascript:toAds("+item['positionId']+")'><i class='fa fa-pencil'></i>广告管理</a> ";
                if(WST.GRANT.GGWZ_02)h += "<a  class='btn btn-blue' href='javascript:toEdit("+item['positionId']+")'><i class='fa fa-pencil'></i>修改</a> ";
                if(WST.GRANT.GGWZ_03)h += "<a  class='btn btn-red' href='javascript:toDel(" + item['positionId'] + ")'><i class='fa fa-trash-o'></i>删除</a> "; 
                return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-184,indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/Adpositions/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    }); 
    $('#headTip').WSTTips({width:90,height:35,callback:function(v){
         if(v){
             mmg.resize({height:h-185});
         }else{
             mmg.resize({height:h-137});
         }
    }});
    loadQuery(p);
}
function toEdit(id){
	location.href = WST.U('admin/Adpositions/toedit','id='+id+'&p='+WST_CURR_PAGE);
}
function toAds(id){
	location.href = WST.U('admin/ads/index2','id='+id+'&p='+WST_CURR_PAGE);
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
	           	$.post(WST.U('admin/AdPositions/del'),{id:id},function(data,textStatus){
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
                positionType: {
                  rule:"required",
                  msg:{required:"请选择位置类型"},
                  tip:"请选择位置类型",
                  ok:"",
                },
                positionName: {
                  rule:"required;",
                  msg:{required:"请输入位置名称"},
                  tip:"请输入位置名称",
                  ok:"",
                },
                positionCode: {
                    rule:"required;",
                    msg:{required:"请输入位置代码"},
                    tip:"请输入位置代码",
                    ok:"",
                  },
                positionWidth: {
                  rule:"required;",
                  msg:{required:"请输入建议宽度"},
                  ok:"",
                },
                positionHeight: {
                  rule:"required",
                  msg:{required:"请输入建议高度"},
                  ok:"",
                }
            },
          valid: function(form){
            var params = WST.getParams('.ipt');
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('admin/Adpositions/'+((params.positionId==0)?"add":"edit")),params,function(data,textStatus){
              layer.close(loading);
              var json = WST.toAdminJson(data);
              if(json.status=='1'){
                  WST.msg("操作成功",{icon:1});
                  location.href=WST.U('Admin/Adpositions/index',"p="+p);
              }else{
                    WST.msg(json.msg,{icon:2});
              }
            });
      }
    });
}