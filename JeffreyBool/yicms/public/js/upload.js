var Upload = {
	obj:null,		  //渲染按钮
	upType:'static',  //上传类型 static|img|video
	isMulti:false,	  //是否允许多张
	saveType:'local', //local | qiniu
	qn:[], 		      //七牛参数[]
	callback:null,	  //确定回调方法
	data:[],		  //确定返回结果数组
	html:'',
	maxFileSize:50,//允许上传最大文件
	//绑定按钮事件
	bandEvent:function(obj) {
		var _t = this;
		_t.obj = obj;
		_t.html = '';
		//点击事件
		$(obj).click(function() {
			_t.open();
		});
	},
	//上传系统静态资源文件，本地保存
	static:function(obj,isMulti,maxFileSize,callback) {
		this.isMulti = isMulti;
		this.saveType = 'local';
		this.upType = 'static';
		this.callback = callback;
		this.bandEvent(obj);
		this.maxFileSize = maxFileSize;
	},
	//图片数据
	img:function(obj,isMulti,saveType,qn,maxFileSize,callback) {
		this.isMulti = isMulti;
		this.saveType = saveType;
		this.upType = 'img';
		this.qn = qn;
		this.callback = callback;
		this.bandEvent(obj);
		this.maxFileSize = maxFileSize;
	},
	//视频数据
	video:function(obj,saveType,qn,maxFileSize,callback) {
		this.isMulti = false;
		this.saveType = saveType;
		this.upType = 'video';
		this.qn = qn;
		this.callback = callback;
		this.bandEvent(obj);
		this.maxFileSize = maxFileSize;
	},
	//弹出框
	open:function() {
		var _t = this;
		//展示区域
		_t.html =  '<div id="uploadShow" class="filelist">';
		//_t.html += '	<img src="" />';
		//_t.html += '	<img src="" />';
		_t.html += '</div>';
		_t.html += '<div id="container">';
		_t.html += '	<span id="showInfo"></span><span id="fileuploader" class="btn btn-primary">上传图片</span>';
		_t.html += '</div>';

		//引入artdialog.js
		dialog({
			title:'选择文件',
			content:_t.html,
			width:400,
			top: '50%',
			ok:function(){
				if(_t.callback == null) {
					//执行默认回调
					_t.cb();
					//清空数据
					_t.data = [];	 
				}else {
					//执行自定义回调
					if(_t.data.length > 0){
						_t.callback(_t.data);
					}else{
						alert('别闹！你还没有上传呢');
						return false;
					}
					//清空数据
					_t.data = [];
				}
			},
			cancel:true 
		}).showModal();

		if(_t.saveType == 'local') {
			_t.local();
		}
		if(_t.saveType == 'qiniu') {
			_t.qiniu();
		}
	},
	//本地上传
	local:function() {
		var _t = this;
		var url = '';
		var btnStr = '上传图片';
		var ext = 'jpg,jpeg,gif,png,bmp';
		if(_t.upType == 'static')  {
			url = '/api/uploads/upload';
		}
		if(_t.upType == 'img')  {
			url = '/api/uploads/uploadimg';
		}
		if(_t.upType == 'video')  {
			url = '/api/uploads/uploadvedio';
			btnStr = '上传视频';
			ext = 'mp4,flv,mov,rmvb';
		}

		
		//引入jquery.uploadfile.min.js后
		$("#fileuploader").uploadFile({
			url:url,
			fileName: "file",
			dragDrop : false,
			doneStr : '使用',
			uploadStr:btnStr,
			maxFileSize:_t.maxFileSize*102400,
			returnType : 'json',
			showStatusAfterSuccess : false,
			allowedTypes : ext,
			acceptFiles : "image/",
			sequential:true,
			sequentialCount:1,
			multiple : _t.isMulti,
			showDone : false,
			showError : true,
			showProgress : true,
			showAbort:false,
			onSubmit:function(files) {
				$('#showInfo').show().html('上传中...');
			},
			onSuccess:function(files,data,xhr,pd){
				if(data.status == 0) {
					$('#showInfo').show().html(data.info);
				}else {
					var url = data.url;
					var data = data;
					if(_t.upType == 'video') {
						_t.html = '<li><img src="/Public/images/videologo.gif"></li>'; //视频展示
					}else {
						_t.html = '<li><img src="'+url+'" /></li>';//图片展示
					}
					if(_t.isMulti) {
						//保存数组返回
						_t.data[_t.data.length] = data;
						$('#uploadShow').append(_t.html);	
					}else {
						_t.data[0] = data;
						$('#uploadShow').html(_t.html);	
					}
					$('#showInfo').show().html('上传完成');
				}
			}
		});
	},
	//七牛上传
	qiniu:function() {
		var _t = this;
		var btnStr = '上传图片';
		var ext = 'jpg,jpeg,gif,png,bmp';
		if(_t.upType == 'video')  {
			btnStr = '上传视频';
			ext = 'mp4,flv,mov,rmvb';
		}
			 //引入Plupload 、qiniu.js后
	    var fileuploader = Qiniu.uploader({
	        runtimes: 'html5,flash,html4',    //上传模式,依次退化
	        browse_button: 'fileuploader',       //上传选择的点选按钮，**必需**
	        uptoken_url: '/api/uploads/token',            //Ajax请求upToken的Url，**强烈建议设置**（服务端提供）
	         //uptoken : '9GcsZeCchX2kRs8dmce1I6nA4FNSXvPV0gmTnmXH:k8aFF1mmBCK7wzvYm2t46CQ0y5A=:eyJzY29wZSI6InhpYWppb25nIiwiZGVhZGxpbmUiOjE0NDM0MzEwMjR9', //若未指定uptoken_url,则必须指定 uptoken ,uptoken由其他程序生成
	        unique_names: false, // 默认 false，key为文件名。若开启该选项，SDK为自动生成上传成功后的key（文件名）。
	        save_key: false,   // 默认 false。若在服务端生成uptoken的上传策略中指定了 `sava_key`，则开启，SDK会忽略对key的处理
	        domain: 'http://qiniu-plupload.qiniudn.com/',   //bucket 域名，下载资源时用到，**必需**
	        get_new_uptoken: false,  //设置上传文件的时候是否每次都重新获取新的token
	        container: 'container',           //上传区域DOM ID，默认是browser_button的父元素，
	        max_file_size: '100mb',           //最大文件体积限制
	        flash_swf_url: '__PUBLIC__/js/plupload/Moxie.swf',  //引入flash,相对路径
	        max_retries: 3,                   //上传失败最大重试次数
	        dragdrop: true,                   //开启可拖曳上传
	        drop_element: 'container',        //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
	        chunk_size: '4mb',                //分块上传时，每片的体积
	        auto_start: true,                 //选择文件后自动上传，若关闭需要自己绑定事件触发上传
	        filters:{
	        	mime_types: [{title:btnStr,extensions:ext}],
	        },
		      resize: {
					  width: 1800,
					  height: 2800,
					  crop: false,
					  quality: 100,
					  preserve_headers: false
					},
	        init: {
	        	Key: function(up, file) {
	        		//console.log(file);
		            // 若想在前端对每个文件的key进行个性化处理，可以配置该函数
		            // 该配置必须要在unique_names: false，save_key: false时才生效
		            var key = file.id;
		            var name = file.name;
		            var params = name.split('.');
		            if(_t.upType == 'video')  {
			            var ext = params[1];
			            // do something with key here
			            return key+'.'+ext;
			        }
			        return key;
		        },
	        	BeforeUpload: function(up, files) {
					$('#showInfo').show().html('上传中...');
	            },
	            FileUploaded: function(up, file, info) {
	            	var result  = eval("("+info+")");
	            	var url = '';
					if(_t.upType == 'video') {
						url = _t.qn['domain'] + result.key;
						_t.html = '<li><img src="/Public/images/videologo.gif"></li>'; //视频展示
					}else {
						url = _t.qn['domain'] + result.key + _t.qn['style'];
						_t.html = '<li><img src="'+url+'" /></li>';//图片展示
					}
					var data = new Object();
					data.m_url = url.replace("_g.gif", "_m.jpg");
					data.url = url;

					console.log(data);


					if(_t.isMulti) {
						//保存数组返回
						_t.data[_t.data.length] = data;
						$('#uploadShow').append(_t.html);	
					}else {
						_t.data[0] = data;
						$('#uploadShow').html(_t.html);	
					}






            	$('#showInfo').show().html('上传完成');
            },
            Error: function(up, err, errTip) {
            	$('#showInfo').show().html(errTip);
            },
	        UploadProgress: function(up, file) {
	        	$('#showInfo').show().html('<span>' + file.percent + '%</span>');
	        }
        }
	    });
	},
	//默认回调处理
	cb:function() {
		//结构体参照 <div>
		//结构体参照 	<input type="hidden" value="" name="image" />
		//结构体参照 	<div>内容展示区域</div>
		//结构体参照 	<span id="uploadBtn">上传按钮</span>
		//结构体参照 </div>
		var parent = $(this.obj).parent();
		var input = $(parent).find('input:first');
		var div = $(parent).find('div:first');
		var str = '';
		//保持值在隐藏域
		var v = this.data.join(';');

		for(var i  in this.data) {
			//展示视频
			if(this.upType == 'video') {
				str += '<span>'+this.data[i]+'</span><br/>';
			}else {
			//展示图片	
				str += '<img src="'+this.data[i]+'" /><br/>';
			}
		}
		if(this.isMulti) {
			var old = $(input).val();
			if(old) {
				v = old + ';' + v;
			}
			$(input).val(v);
			$(div).append(str);
		}else {
			$(input).val(v);
			$(div).html(str);
		}
	}
};

