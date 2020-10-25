var grid;
var h;

function initGrid(p){
	var h = WST.pageHeight();
  var cols = [
            {title:'消息类型', name:'msgType', width: 30,renderer: function(val,item,rowIndex){
               return (val==0)?'手工发送':'系统发送';
            }},
            {title:'发送者', name:'stName' ,width:50},
            {title:'接收者', name:'loginName' ,width:50,renderer: function(val,item,rowIndex){
               return (val!=null)?val:item['shopName'];
            }},
            {title:'消息内容', name:'msgContent' ,width:280},
            {title:'阅读状态', name:'msgStatus' ,width:30,renderer: function(val,item,rowIndex){
               return (val==0)?"<span class='statu-no'><i class='fa fa-ban'></i> 未读</span>":"<span class='statu-yes'><i class='fa fa-check-circle'></i> 已读</span>";
            }},
            {title:'有效状态', name:'dataFlag' ,width:30, align:'center',renderer: function(val,item,rowIndex){
               return (val==-1)?"<span class='statu-wait'><i class='fa fa-ban'></i> 已删除</span>":"<span class='statu-yes'><i class='fa fa-check-circle'></i> 有效</span>";
            }},
            {title:'发送时间', name:'createTime' ,width:80},
           
            {title:'操作', name:'' ,width:80, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
                if(WST.GRANT.SCXX_00)h += "<button  class='btn btn-blue' onclick='javascript:showFullMsg("+item['id']+")'><i class='fa fa-search'></i>查看</button> ";
                if(WST.GRANT.SCXX_03)h += "<button  class='btn btn-red' onclick='javascript:toDel(" + item['id'] + ")'><i class='fa fa-trash-o'></i>删除</button> "; 
                return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-122,indexCol: true,indexColWidth:50,  cols: cols,method:'POST',checkCol:true,multiSelect:true,
        url: WST.U('admin/Messages/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    }); 
    msgQuery(p);
}



function showFullMsg(id){
	  parent.showBox({title:'内容详情',type:2,content:WST.U('admin/messages/showFullMsg','id='+id),area: ['800px', '500px'],btn:['关闭']});

}

function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/messages/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	layer.close(box);
	           		        msgQuery(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}

//切换卡
$(function (){ 
//编辑器
KindEditor.ready(function(K) {
editor1 = K.create('textarea[name="msgContent"]', {
  uploadJson : WST.conf.ROOT+'/admin/messages/editorUpload',
  height:'350px',
  allowFileManager : false,
  allowImageUpload : true,
  themeType : "default",
  items:[     'source', 'undo', 'redo',  'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
                'plainpaste', 'wordpaste', 'justifyleft', 'justifycenter', 'justifyright',
                'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                'superscript', 'clearhtml', 'quickformat', 'selectall',  'fullscreen',
                'formatblock', 'fontname', 'fontsize',  'forecolor', 'hilitecolor', 'bold',
                'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', 'image','multiimage','media','table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
                'anchor', 'link', 'unlink'
  ],
  afterBlur: function(){ this.sync(); }
});
});
});


function sendToTheUser(t){
        if($('#theUser').prop('checked')){
          $('#user_query').show();
          $('#send_to').show();
        }else{
          $('#user_query').hide();
          $('#send_to').hide();
        }
        
     }
     //账号模糊查找
     function userQuery(){
      var key = $('#loginName').val();
      var html = '';
      $.post(WST.U('admin/messages/userQuery'),{'loginName':key},function(text,dataStatus){
          $(text).each(function(k,v){
            html += '<option value="'+v.userId+'">'+v.loginName+'</option>';
          });
          $('#ltarget').html(html);
      });
      
     }
     //发送消息
     function sendMsg(){
        var params = WST.getParams('.ipt');
        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
        $.post(WST.U('admin/messages/add'),params,function(data,textStatus){
          layer.close(loading);
          var json = WST.toAdminJson(data);
          if(json.status=='1'){
              WST.msg("操作成功",{icon:1});
              $('#ltarget').html('');
              $('#rtarget').html('');
              $('#loginName').val('');
              editor1.html('');

          }else{
                WST.msg(json.msg,{icon:2});
          }
        });
     }


function msgQuery(p){
    p=(p<=1)?1:p;
    var query = WST.getParams('.query');
    query.page = p;
    mmg.load(query);
  }

//批量删除
function toBatchDelete(){
	var rows = mmg.selectedRows();
	if(rows.length==0){
		 WST.msg('请选择记录',{icon:2});
		 return;
	}
	var ids = [];
	for(var i=0;i<rows.length;i++){
       ids.push(rows[i]['id']); 
	}
	var box2 = WST.confirm({content:"您确定要删除选中的记录吗?",yes:function(){
        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	       	$.post(WST.U('admin/messages/batchDel'),{ids:ids.join(',')},function(data,textStatus){
	 			  layer.close(loading);
	 			  var json = WST.toAdminJson(data);
	 			  if(json.status=='1'){
	 			    	WST.msg(json.msg,{icon:1});
	 			    	layer.close(box2);
	 			    	msgQuery(WST_CURR_PAGE);
	 			  }else{
	 			    	WST.msg(json.msg,{icon:2});
	 			  }
	 		});
         }});
}	