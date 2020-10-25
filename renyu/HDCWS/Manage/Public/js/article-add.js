$(function(){

	var keditor = KindEditor.create('#content', {
		
		width : '670px',

		height : '500px',

		resizeType : 0,
		
		uploadJson : URL + 'uploadJson',
		
		fileManagerJson : URL + 'fileManagerJson',
		
		extraFileUploadParams : {

			s : sid

		}
	
	}),

	me = {
	
		delImg : function(fileId){
	
			var me = this;
	
			for(var i in me.imgList){
			
				if(i == fileId){
	
					delete me.imgList[fileId];
				
					break;
	
				}
			
			}
		
		}
	
	}, config = {

	flash_url : flashurl,

	file_size_limit : '10MB',

	file_types : "*.jpg;*.jpeg;*.png;*.bmp;*.gif",

	button_width : 120,

	button_height : 30,

	button_cursor : -2,

	button_window_mode : 'transparent',

	post_params : {

		s : sid
			
	},

	file_dialog_complete_handler : function(select, queue, total){

		if(queue > 0){

			this.startUpload();

		}

	},

	upload_complete_handler : function(file){

		this.startUpload();

	}

};

new SWFUpload($.extend({}, config, {

	upload_url : uploadurl,

	button_placeholder_id : 'article_thumb_img',

	file_post_name : 'thumbImg',

	button_action : -100,

	upload_start_handler : function(file){

		if(me.thumbImg){
		
			me.thumbImg.delSelf();
		
		}

		var showCom = $('#article_thumb_img_panel'),

			img = new ShowImg({

				fileId : file.id,
			
				showDel : false,
				
				formPanel : me
			
			});

		me.thumbImg = img;

		showCom.append(img.$el);
	
	},

	upload_progress_handler : function(file, bytes, total){

		me.thumbImg.showProcss(file, bytes, total);
			
	},

	upload_success_handler : function(file, data, response){

		me.thumbImg.showImg(file, data, response);
		
	},

	upload_error_handler : function(file, error, message){

		this.cancelUpload();

		this.stopUpload();

		me.thumbImg.showError(file, error, message);

	}

}));

function ShowImg(opts){
	
	var me = this;	

	for(var i in opts) me[i] = opts[i];
	
	this.initComponent();
	
}

ShowImg.prototype = {

	showDel : true,

	showProcss : function(file, bytes, total){

		var val = bytes / total; 

		this.$el.find('div.percent').css('width', val * 100 + '%');
	
	},

	showImg : function(file, data){
	
		if(data != 0){

			this.imgUrl = data;
			
			this.$el.find('img').attr('src', ROOT + data).show();
			
			this.$el.find('div.progress').remove();

		}else this.showFail();
	
	},

	showFail : function(){
	
		alert('图片上传失败');

		this.delSelf();
	
	},

	showError : function(){

		alert('图片上传出错');

		this.delSelf();
	
	},

	delSelf : function(){
	
		this.$el.remove();
	
	},

	delImg : function(){

		var me = this;

		$.post(delimgurl,{ imgUrl : me.imgUrl}, function(data){

			if(data == 1){
			
				me.formPanel.delImg(me.fileId);

				me.delSelf();
			
			}else{
			
				alert('删除失败');
			
			}
			
		});
	
	},

	initComponent : function(){

		var me = this;
		
		this.$el = $('<li class="img-li"><a class="button"></a><img width="190" height="133" src="" /><div class="progress"><div class="percent"></div></div></li>');

		if(me.showDel){

			me.$el.on({
			
				mouseover : function(){
				
					$(this).find('a.button').show();
				
				},

				mouseleave : function(){
				
					$(this).find('a.button').hide();
				
				}
			
			});
			
			me.$el.find('a.button').click(function(){
				
				if(confirm('确定删除吗?')) me.delImg();
				
			});

		}

		if(this.initShow) this.showImg(null, this.imgUrl);
	
	}

};

$('#submit-btn').click(function(){
	
	$('textarea[name=content]').val(keditor.html());

	if(me.thumbImg) $(':hidden[name=thumburl]').val(me.thumbImg.imgUrl);
	
	else{
		
		alert('未上传缩略图');
		
		return false;
	
	}
	
	$('form').submit();

});

});