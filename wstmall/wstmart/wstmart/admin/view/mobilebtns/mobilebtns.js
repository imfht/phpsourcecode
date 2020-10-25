var mmg,isInitUpload = false;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'图标', name:'btnImg', width: 50,renderer: function(val,item,rowIndex){
                return '<img src="'+WST.conf.RESOURCE_PATH+'/'+item['btnImg']+'" height="60px" style="margin-top:5px;" />';
            }},
            {title:'按钮名称', name:'btnName' ,width:60},
            {title:'按钮Url', name:'btnUrl' ,width:300},
            {title:'按钮类别', name:'btnSrc' ,width:20,renderer: function(val,item,rowIndex){
                var rs = '手机版';
                switch(val){
                    case 1:
                      rs='微信版';
                    break;
                    case 2:
                      rs='小程序';
                    break;
                    case 3:
                      rs='App';
                    break;
                }
                return rs;
            }},
            {title:'所属插件', name:'addonsName' ,width:20},
            {title:'排序号', name:'btnSort' ,width:10},
            {title:'操作', name:'' ,width:100, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
    			      if(WST.GRANT.ANGL_02)h += "<a  class='btn btn-blue' onclick='javascript:getForEdit(" + item['id'] + ")'><i class='fa fa-pencil'></i>修改</a> ";
    			      if(WST.GRANT.ANGL_03)h += "<a  class='btn btn-red' onclick='javascript:toDel(" + item['id'] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
                return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-170),indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/mobilebtns/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });  
    $('#headTip').WSTTips({width:90,height:35,callback:function(v){
       var diff = v?155:128;
       mmg.resize({height:h-diff})
    }});   
    $('#btnSrc').change(function(v){
        if($(this).val()==3){// App端
           if($('#appBtns').length==0){
               var options = ['<option value="0">请选择页面说明</option>'];
               for(var i in appScreens){
                  var _obj = appScreens[i];
                  options.push('<option value="'+_obj.explain+'">'+_obj.screenName+'</option>');
               }
               var select = '<select id="appBtns" >'+options.join('')+'</select>'
               var html= '<tr><th>app端按钮说明：</th><td>'+select+'</td></tr><tr id="screenExplain"><th></th><td></td></tr>';
               $('#mbBtnType').after(html);
               $('#appBtns').change(function(v){
                  var _explain = $(this).val()==0?'':'<span style="color:red">示例Url：</span>'+$(this).val();
                  $('#screenExplain td').html(_explain);
               })
           }
        }else{
          $('#appBtns').parent().parent().remove();
          $('#screenExplain').remove();
        }
    })
    loadGrid(p);
}
// app按钮
var appScreens = []
$(function(){
  $.post(WST.U('admin/appscreens/pagequery'),{},function(responData){
    appScreens = (responData instanceof Object)?responData:[];
  });
});

function loadGrid(p){
    p=(p<=1)?1:p;
	var query = WST.getParams('.query');
    query.page = p;
	mmg.load(query);
}
function getForEdit(id){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
     $.post(WST.U('admin/mobileBtns/get'),{id:id},function(data,textStatus){
           layer.close(loading);
           var json = WST.toAdminJson(data);
           if(json.id){
           		WST.setValues(json);
           		//显示原来的图片
           		$('#preview').html('<img src="'+WST.conf.RESOURCE_PATH+'/'+json.btnImg+'" height="30px;"/>');
           		$('#isImg').val('ok');
           		toEdit(json.id);
           }else{
           		WST.msg(json.msg,{icon:2});
           }
    });
}

function toEdit(id){
  if(!isInitUpload){
    initUpload();
    isInitUpload = true;
  }
	var title =(id==0)?"新增":"编辑";
	var box = WST.open({title:title,type:1,content:$('#mbtnBox'),area: ['680px', '480px'],btn: ['确定','取消'],yes:function(){
			$('#mbtnForm').submit();
	},cancel:function(){
		//重置表单
		$('#mbtnForm')[0].reset();
		//清空预览图
		$('#preview').html('');
		$('#btnImg').val('');

	},end:function(){
		//重置表单
		$('#mbtnForm')[0].reset();
		//清空预览图
		$('#preview').html('');
		$('#btnImg').val('');
    $('#mbtnBox').hide();
    // 隐藏app端说明
    $('#appBtns').parent().parent().remove();
     $('#screenExplain').remove();

	}});
	$('#mbtnForm').validator({
        fields: {
            btnName: {
            	rule:"required;",
            	msg:{required:"请输入按钮名称"},
            	tip:"请输入按钮名称",
            	ok:"",
            },
            btnUrl: {
            	rule:"required;",
            	msg:{required:"请输入按Url"},
            	tip:"请输入按Url",
            	ok:"",
            },
            btnImg:  {
            	rule:"required;",
            	msg:{required:"请上传图标"},
            	tip:"请上传图标",
            	ok:"",
            },
            
        },
       valid: function(form){
		        var params = WST.getParams('.ipt');
		        	params.id = id;
		        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		   		$.post(WST.U('admin/mobileBtns/'+((id==0)?"add":"edit")),params,function(data,textStatus){
		   			  layer.close(loading);
		   			  var json = WST.toAdminJson(data);
		   			  if(json.status=='1'){
		   			    	WST.msg("操作成功",{icon:1});
		   			    	$('#mbtnForm')[0].reset();
		   			    	//清空预览图
		   			    	$('#preview').html('');
		   			    	//清空图片隐藏域
		   			    	$('#btnImg').val('');
		   			    	layer.close(box);
		   		            loadGrid(WST_CURR_PAGE);
		   			  }else{
		   			        WST.msg(json.msg,{icon:2});
		   			  }
		   		});

    	}

  });
}
function initUpload(){
  WST.upload({
    pick:'#adFilePicker',
    formData: {dir:'sysconfigs'},
    accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
    callback:function(f){
      var json = WST.toAdminJson(f);
      if(json.status==1){
        $('#uploadMsg').empty().hide();
        //将上传的图片路径赋给全局变量
      $('#btnImg').val(json.savePath+json.thumb);
      $('#preview').html('<img src="'+WST.conf.RESOURCE_PATH+'/'+json.savePath+json.thumb+'" height="30" />');
      }else{
        WST.msg(json.msg,{icon:2});
      }
  },
  progress:function(rate){
      $('#uploadMsg').show().html('已上传'+rate+"%");
  }
});
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/mobileBtns/del'),{id:id},function(data,textStatus){
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





		