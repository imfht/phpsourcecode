//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.uploadpic = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		this.upload();
		router.$("#btn_uploadpic").click(function(){
			router.$(".chatbar").hide();	//把输入框架隐藏,不然会挡住上传控件的
			setTimeout(function(){
				router.$(".bui-mask").click(function(){
					router.$(".chatbar").show();
				});
			},500);
		});
				
	},
	once:function(){ //once() 不管PC还是WAP更换圈子都仅执行一次,logic_init()更换圈子无论PC还是WAP会重新执行,init()更换圈子可能不再执行,也有可能还要执行,根据界面是否需要渲染而定
		//jQuery.getScript("/public/static/js/base64uppic.js").done(function() {
		//	exif_obj = true;
		//}).fail(function() {
		//	console.log('/public/static/js/base64uppic.js加载失败');
		//});
		loader.import(["/public/static/js/exif.js"],function(){});	//上传图片要获取图片信息
	},
	finish:function(res){  //所有模块加载完才执行
		//jQuery.getScript("/public/static/js/exif.js").done(function() {
		//	exif_obj = true;
		//}).fail(function() {
		//	console.log('/public/static/js/exif.js加载失败');
		//});
	},
	logic_init:function(res){  //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},
	upload:function(){
		//loader.import(["/public/static/js/exif.js"],function(){});	//上传图片要获取图片信息
		var uiUpload = bui.upload();
		var uiActionsheet = bui.actionsheet({
					trigger: "#btn_uploadpic",
					opacity:"0.8",
					//mask:false,
					buttons: [{ name: "拍照上传", value: "camera" }, { name: "从相册选取", value: "photo" }],
					callback: function(e) {						
						var ui = this;
						var val = $(e.target).attr("value");
						switch (val) {
							case "camera":
								ui.hide();
								router.$(".chatbar").show();
								uiUpload.add({
									"from": "camera",
									"onSuccess": function(val, data) {
										// 展示本地图片
										this.toBase64({
											onSuccess: function(url) {
												upload_pic(url)
											}
										});

										// 也可以直接调用start上传图片
									}
								})
								break;
							case "photo":
								ui.hide();
								router.$(".chatbar").show();
								uiUpload.add({
									"from": "",
									"onSuccess": function(val, files) {
										 var Orientation=0
										 var filefield = document.getElementById($('input[name="uploadFiles"]').attr('id')) 
											 var file = filefield.files[0];
										     console.log($('input[name="uploadFiles"]'));
											EXIF.getData(file, function(){												 
												Orientation = EXIF.getTag(this, 'Orientation');
												console.log("Orientation ="+Orientation);
											});										
										//console.log(val);
										//console.log(this.data()[0]);
										//var url = window.URL.createObjectURL(files[0]);
										//document.querySelector('img').src = window.URL.createObjectURL(url);
										// 展示本地图片
										this.toBase64({
											onSuccess: function(url) {
												upload_pic(url,Orientation)
											}
										});
										// 也可以直接调用start上传图片
									}
								})

								break;
							case "cancel":
								ui.hide();
								router.$(".chatbar").show();
								break;
						}
					}
				})

				function templatePhoto(url) {
					var str = `<img src="${url}" class="big" />`;					
					return str;
				}

				
				function upload_pic(base64,Orientation){
					bui.hint("图片上传中,请稍候...");
					var image = new Image();
						image.src = base64;
						image.onload = function() {
						var resized = resizeUpImages(image);
						var severUrl = "/index.php/index/attachment/upload/dir/images/from/base64/module/bbs.html";
						$.post(severUrl, {'imgBase64':resized,'Orientation':Orientation,'tags':''},function (res) {
							if(res.code==1){
								//console.log(res);
								var url = res.path;
								if(url.indexOf('://')==-1 && url.indexOf('/public/')==-1){
									url = (typeof(web_url)!='undefined'?web_url:'')+'/public/'+url;
								}
								postmsg( templatePhoto(url) );
								//$("#chatInput").val( templatePhoto(url)+$("#chatInput").val() )
								//if($("#btnSend").hasClass("disabled"))$("#btnSend").removeClass("disabled").addClass("primary");
							}else{
								alert(res.info);
							}
						});
					}					
				}

				function resizeUpImages(img) {
					//压缩的大小
					var max_width = 1920; 
					var max_height = 1080;
					var canvas = document.createElement('canvas');
					var width = img.width;
					var height = img.height;
					if(width > height) {
						if(width > max_width) {
							height = Math.round(height *= max_width / width);
							width = max_width;
						}
					}else{
						if(height > max_height) {
							width = Math.round(width *= max_height / height);
							height = max_height;
						}
					}
					canvas.width = width;
					canvas.height = height;
					var ctx = canvas.getContext("2d");
					ctx.drawImage(img, 0, 0, width, height);
					//压缩率
					return canvas.toDataURL("image/jpeg",0.72); 
				}
	}

}