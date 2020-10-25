$(document).ready(function(){
	/*
	* 文件上传下载处理
	*/
	var setobj,my_interval, bt=0;  
	var upOptions = {
        //overwriteInitial: false,
        maxFileSize: 40048,
        showRemove: false,
		showUpload: false,
        maxFilesNum: 1, 
        maxFileCount:1, 
        previewSettings:{image: {width: "auto", height: "60px"}},
        //allowedFileTypes: ['image', 'video', 'flash'],
        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
	}	
    //显示音乐上传窗口
    $('.up_music').click(function () {		
		var opts = {allowedFileExtensions : ['mp3','MP3','ogg']};
		handelUp($(this),opts)
    });
	
	 //显示图片上传窗口
    $('.up_pic').click(function () {			
		var opts = {allowedFileExtensions : ['jpg','gif','png']};
		handelUp($(this),opts)
    });

	function handelUp(that,opts){
		var parent = that.parent();
    	var upUrl = that.attr('url'),resultUrlObj =  $(that.attr('rel_url')),resultIdObj =  $(that.attr('rel_fileid'));
		var options = $.extend({uploadUrl:upUrl},upOptions);
		var fileInput = $('<input id="file-1" type="file" multiple name="user_file" class="file"  data-overwrite-initial="false" data-min-file-count="1">');
		$('.file-input').remove();
		fileInput.insertAfter(parent);		
		fileInput.fileinput('refresh',$.extend(opts,options));
		fileInput.on('fileloaded', function(event) {$(this).fileinput('upload');});
		fileInput.on('fileuploaded', function(event, data, previewId) {
			var data = data.response;
			if (data.status == 1){ //上传成功
				$('.file-preview,.btn-file').remove();
				var path = data['url'] || data['path'];
				resultUrlObj.val(path);
				resultIdObj.val(data['id']);
				$('.kv-upload-progress').remove();
			}else{
				$('#'+previewId).find('.file-upload-indicator').html('<i class="fa fa-warning text-danger"></i>');
				topAlert(data.info)
			}
			options='';
		});		
	}
      	
   	//图片预览	
	$('.look_pic').click(function () {
        var  src =$('#cover').val();
	 	$('.up-show').hide();
	 	$('.modal-title').text('封面图片预览');
	 	$('.modal-body').find('.alert-info').html('保存位置: '+src);
	 	$('#show-cover img').attr('src',src);
	 	$('#show-cover').show();
     	$('#myModal').modal('show');
	});    
	
	$('.ajax-find').click(function () {
		var url = $(this).attr('href');
		var tabstr = $(this).attr('rel');
		var sort = makeSort();
		$('.modal-title').text('点击字母查找数据');
		$('.modal-dialog').css('margin-right','140px');
		$('.modal-body').html(makeSort(tabstr));
		$('#myModal').modal('show');
		return false;
	
	});
		
	$("#f-s-btn, #f-a-btn").click(function () {
		var tabstr = $(this).attr('rel');
		$.colorbox({html:makeSort(tabstr),right:'220px',width:'460px',opacity:' 0.3'});
	});
	
	$('#set-down-url').click(function () {
		var setObj=$(this).attr('rel');
		$('#music-down').val($(setObj).val());
	})
	$('.set-name').change(function () {
		var nameVal = $(this).find('option:selected').html();
		$(this).prev('input').val(nameVal);
	});
	
	//点击开始下载远程文件
    $('.ajax-down').click(function () {
    	var $url = $('#down_url').val();
    	$('.down-bar').css('width','0%');
    	$('.down-progress').show();
    	$.get(downUrl,{type:'down',url:$url},function(data)	{	   
		   if (data.status == 1){		   			
		   		$('.down-filename').text(data.name);
		   		$('.down-bt').text(data.info);
		   		$('.down-progress').hide();
		   		$(setobj).val(data.save_path);
		   		$(setobj).prev().val(data.id);//设置文件id
		   		bt=0;
		   }else{
		   		setTimeout(function () {$(
		   			'.down-progress').hide();
		   		},2000);     		
		   		$('.modal-tip').html(data.info);
		   		window.clearInterval(my_interval); //清楚定时器		   		
		   		return false;
		   }
		});
		my_interval = setInterval(function () {getFilen();}, 1000);
		$('.down-filename').text('正在下载');		
    	return false;
    });

});
//设置对应表单的字母下拉列表
function makeSort (str) {
	var letters ='<div class="row"><div class="col-sm-12"><ul class="pager mt0">';
	for(var i=0;i<26;i++){
		var sort= String.fromCharCode((65+i));
		letters+= '<li><a class="s-f-btn" href="javascript:void(0);" onclick="findData(\''+str+'\',\''+sort+'\')" >'+sort+'</a></li>';
	}
	letters+= '<li><a class="s-f-btn" href="javascript:void(0);" onclick="findData(\''+str+'\',\'0\')" >other</a></li>';
	letters+= '</ul></div><div class="col-sm-12" id="gain-data"></div></div>';
	return letters;
}

function findData(str,val) {
	var sel=$('#gain-data');
		sel.html('<div class="csspinner shadow">正在查询数据，请稍后.....</div>');		
	$.ajax({
		type:"post",
		url:findUrl,
		data:"sort="+val+"&table="+str,
		dataType: "json",			
		success:function (data) {
				if (data != null){ 
					var con ="";
					$.each(data,function(i){
						con+='<a class="btn btn-sm" onclick="setVal1(\''+str+'\',\''+data[i]["id"]+'\',\''+data[i]["name"]+'\')">'+data[i]["name"]+'</a>';
					});
				} else {
						con="<span>暂无数据</span>";
				}
				sel.html(con);
				
		},
		error: function(){
			//alert('AJAX 请求失败！');	
		}
	});
}

function setVal1 (str,id,name) {
	$("#"+str+"-name").val(name);
	$("#"+str+"-id").val(id);
}
function showImg (obj) {
	var url = $(obj).val();
	$.colorbox({href:url });
}

//获取下载字节数     
function getFilen(){
    $.get(downUrl,{type:'percent'}, function(data){
    	if(data.status != 0){ 
            if(data.length == data.size){
				clearInterval(my_interval);					
            }else{
            	if(data.size != 0){
            		var num = Math.round(data.size / data.length * 10000) / 100.00+ "%";
            		$('.down-bar').css('width',num);
            		$('.sr-on').text(num);
            		var size = data.size;                	
            		$('.down-bt').text(bytesToSize(size)+'/S ('+ bytesToSize(data.length)+')');
            		bt = data.size;
            	}
            }             	
    	}
    }, "json");
    
}


//js转换字节
function bytesToSize(bytes) {
    if (bytes === 0) return '0 B';
    	var k = 1024, // or 1024
        sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
        i = Math.floor(Math.log(bytes) / Math.log(k));
   return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
}