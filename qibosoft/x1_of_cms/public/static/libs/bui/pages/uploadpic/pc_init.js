//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.uploadpic = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		
		$('#btn_uploadpic').click(function(){
			$('#fileToUpload').trigger("click");
		});

				
	},
	once:function(){ //once() 不管PC还是WAP更换圈子都仅执行一次,logic_init()更换圈子无论PC还是WAP会重新执行,init()更换圈子可能不再执行,也有可能还要执行,根据界面是否需要渲染而定
		jQuery.getScript("/public/static/js/base64uppic.js").done(function() {
			exif_obj = true;
		}).fail(function() {
			layer.msg('/public/static/js/base64uppic.js加载失败',{time:800});
		});
		$("body").append(`<input style="display:none;" type="file" name="fileToUpload" id="fileToUpload" accept="image/*" /><input type="text" name="picurl" id="compressValue"  style="display:none;" />`);

		$('#fileToUpload').change(function(){
			var pics = [];
			uploadBtnChange($(this).attr("id"),'compressValue',pics,function(url,pic_array){
				if(pic_array[0].indexOf('://')==-1 && pic_array[0].indexOf('/public/')==-1){
					pic_array[0] = '/public/'+pic_array[0];
				}
				$("#input_box").val("<img src='"+pic_array[0]+"' class='big' />"+$("#input_box").val());			
			 });
		});
	},
	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){  //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},

}