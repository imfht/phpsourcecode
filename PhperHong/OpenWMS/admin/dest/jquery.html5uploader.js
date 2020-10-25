
(function($){
$.fn.html5uploader = function(opts){
	
	var defaults = {
		fileTypeExts:'',//允许上传的文件类型，填写mime类型
		url:'',//文件提交的地址
		auto:false,//自动上传
		multi:true,//默认允许选择多个文件
		buttonText:'选择文件',//上传按钮上的文字
		removeTimeout: 1000,//上传完成后进度条的消失时间
		itemTemplate:'<div class="progress progress-striped active " data-percent="0%"><div class="progress-bar" style="width:0%;"></div></div>',//上传队列显示的模板,最外层标签使用<li>
		onUploadStart:function(){},//上传开始时的动作
		onUploadSuccess:function(){},//上传成功的动作
		onUploadComplete:function(){},//上传完成的动作
		onUploadError:function(){}, //上传失败的动作
		onInit:function(){},//初始化时的动作
		onCheckFileTypeExts:function(){}, //后缀名错误时的回调
		onSelect:function(){},
		formData:{},
		input_file_id:'input-file',
		}
		
	var option = $.extend(defaults,opts);
	
	//将文件的单位由bytes转换为KB或MB
	var formatFileSize = function(size){
		if (size> 1024 * 1024){
			size = (Math.round(size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
			}
		else{
			size = (Math.round(size * 100 / 1024) / 100).toString() + 'KB';
			}
		return size;
		}
	//根据文件序号获取文件
	var getFile = function(files){
		return files[0];
	}
	//将文件类型格式化为数组
	var formatFileType = function(str){
		if(str){
			return str.split(",");	
			}
		return false;
		}
	var obj;
	this.each(function(){
		var _this = $(this);
		//先添加上file按钮和上传列表
		var inputstr = '<input type="file" id="'+option.input_file_id+'" class="uploadfile"  name="fileselect" />';
		var fileInputButton = $(inputstr);
		_this.append(fileInputButton);
		//创建文件对象
			  var ZXXFILE = {
			  fileInput: fileInputButton.get(0),				//html file控件
			  upButton: null,					//提交按钮
			  url: option.url,						//ajax地址
			  formData:option.formData,
			  input_file_id:option.input_file_id,
			  abc:function(){alert('123')},
			  filter: function(files) {		//选择文件组的过滤方法
				  var arr = [];
				  var typeArray = formatFileType(option.fileTypeExts);
				  if(!typeArray){

						  if(files[0].constructor==File){
							arr.push(files[0]);
						  }

					  }
				  else{
	
						  if(files[0].constructor==File){
							if($.inArray(files[0].type,typeArray)>=0  || '' == files[0].type){
								arr.push(files[0]);	
								}
							else{
								fileInputButton.val('');
								option.onCheckFileTypeExts();
								}  	
							} 
					
					  }
				  return arr;  	
			  },
			  //文件选择后
			  onSelect: function(files){
				
							
							var file = files;
							
							var html = option.itemTemplate;
							//处理模板中使用的变量
							html = html.replace(/\${fileID}/g,file.index).replace(/\${fileName}/g,file.name).replace(/\${fileSize}/g,formatFileSize(file.size));
							_this.find('.progress').remove();
							_this.append(html);
							//判断是否是自动上传
							 if(option.auto){
								 ZXXFILE.funUploadFile(file[0]);
							}
					
						 
						 //如果配置非自动上传，绑定上传事件
						 if(!option.auto){
				
								//$('#'+option.button_id).die().on('click',function(){
									//ZXXFILE.funUploadFile(files[0]);
								//});
						 }
						 //选择文件后的回调函数
						 option.onSelect(files[0]);
				},		
			  
			  onProgress: function(file, loaded, total) {
				  var  percent = (loaded / total * 100).toFixed(2) + '%';
				  _this.find('.progress-bar').css('width',percent);
				  _this.find('.progress').attr('data-percent', percent);
		  		},		//文件上传进度
			  onUploadSuccess: option.onUploadSuccess,		//文件上传成功时
			  onUploadError: option.onUploadError,		//文件上传失败时,
			  onUploadComplete: option.onUploadComplete,		//文件全部上传完毕时
			  
			  /* 开发参数和内置方法分界线 */
			  
			  //获取选择文件，file控件或拖放
			  funGetFiles: function(e) {
						  
				  // 获取文件列表对象
				  var files = e.target.files || e.dataTransfer.files;
				
				  //继续添加文件
				  files = this.filter(files)
				  this.funDealFiles(files);
				  return this;
			  },
			  //选中文件的处理与回调
			  funDealFiles: function(files) {
				  //执行选择回调
				  this.onSelect(files);
				  return this;
			  },
			  //文件上传
			  funUploadFile: function(file) {
				  var self = this;	
				  (function(file) {
					  var xhr = new XMLHttpRequest();
					  if (xhr.upload) {
						  // 上传中
						  xhr.upload.addEventListener("progress", function(e) {
							  self.onProgress(file, e.loaded, e.total);
						  }, false);
			  
						  // 文件上传成功或是失败
						  xhr.onreadystatechange = function(e) {
							  if (xhr.readyState == 4) {
								  if (xhr.status == 200) {
									  self.onUploadSuccess(file, xhr.responseText);
									  setTimeout(function(){_this.find('.progress').fadeOut();},option.removeTimeout);
									  self.onUploadComplete();	
									  $('#'+self.input_file_id).val('');
									  _this.find('.file-name').attr('data-title', '点击选择文件 ...').find('i').removeClass('icon-file').addClass('icon-upload-alt');
								  } else {
									  self.onUploadError(file, xhr.responseText);		
								  }
							  }
						  };
			  
			  			  option.onUploadStart();	
						  // 开始上传
						  xhr.open("POST", self.url, true);
						  //xhr.setRequestHeader("X_FILENAME", file.name);
				
						  
						var fd = new FormData();
						fd.append("fileToUpload", file);
				
						 for(var i in self.formData){
					
							 fd.append(i, self.formData[i]);	
						 }
						  xhr.send(fd);
					  }	
				  })(file);	  
			  },
			  init: function() {
				  var self = this;
				  
				  //文件选择控件选择
				  if (this.fileInput) {
					  this.fileInput.addEventListener("change", function(e) { self.funGetFiles(e); }, false);	
				  }
				
				  
				  option.onInit();
			  }
		  };
		  //初始化文件对象
		  ZXXFILE.init();
			obj = ZXXFILE;
		}); 
		return obj;
	}	
	
})(jQuery)
