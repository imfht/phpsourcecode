var Ibos = Ibos || {};
(function(){

	function FileProgress(file, targetID) {
		this.fileProgressID = file.id;

		this.opacity = 100;
		this.height = 0;


		this.fileProgressElement = document.getElementById(this.fileProgressID);
		if (!this.fileProgressElement) {
			this.fileProgressElement = document.createElement("div");
			this.fileProgressElement.className = "progressContainer";
			this.fileProgressElement.id = this.fileProgressID;

			var progressCancel = document.createElement("a");
			progressCancel.className = "progressCancel";
			progressCancel.href = "#";
			progressCancel.style.visibility = "hidden";
			progressCancel.appendChild(document.createTextNode(" "));

			var progressText = document.createElement("div");
			progressText.className = "progressName";
			progressText.appendChild(document.createTextNode(file.name));

			var progressBarWrap = document.createElement("div");
			progressBarWrap.className = "progress progress-small";

			var progressBar = document.createElement("div");
			progressBar.className = "progress-bar";

			progressBarWrap.appendChild(progressBar);

			var progressStatus = document.createElement("div");
			progressStatus.className = "progressBarStatus";
			progressStatus.innerHTML = "&nbsp;";

			this.fileProgressElement.appendChild(progressCancel);
			this.fileProgressElement.appendChild(progressText);
			this.fileProgressElement.appendChild(progressStatus);
			this.fileProgressElement.appendChild(progressBarWrap);

			document.getElementById(targetID).appendChild(this.fileProgressElement);
		} else {
			this.reset();
		}

		this.height = this.fileProgressElement.offsetHeight;
		this.setTimer(null);


	}

	FileProgress.prototype.setTimer = function (timer) {
		this.fileProgressElement["FP_TIMER"] = timer;
	};
	FileProgress.prototype.getTimer = function (timer) {
		return this.fileProgressElement["FP_TIMER"] || null;
	};

	FileProgress.prototype.reset = function () {
		this.fileProgressElement.className = "progressContainer";

		this.fileProgressElement.childNodes[2].innerHTML = "&nbsp;";
		this.fileProgressElement.childNodes[2].className = "progressBarStatus";

		this.fileProgressElement.childNodes[3].className = "progress progress-small";
		this.fileProgressElement.childNodes[3].childNodes[0].className = "progress-bar";
		this.fileProgressElement.childNodes[3].childNodes[0].style.width = "0%";

		this.appear();
	};

	FileProgress.prototype.setProgress = function (percentage) {
		this.fileProgressElement.className = "progressContainer blue";
		this.fileProgressElement.childNodes[3].childNodes[0].className = "progress-bar";
		this.fileProgressElement.childNodes[3].childNodes[0].style.width = percentage + "%";

		this.appear();
	};
	FileProgress.prototype.setComplete = function () {
		this.fileProgressElement.className = "progressContainer green";
		this.fileProgressElement.childNodes[3].className = "progressBarComplete";
		this.fileProgressElement.childNodes[3].style.width = "";

		// var oSelf = this;
		// this.setTimer(setTimeout(function () {
		// 	oSelf.disappear();
		// }, 10000));
	};
	FileProgress.prototype.setError = function () {
		this.fileProgressElement.className = "progressContainer red";
		this.fileProgressElement.childNodes[3].className = "progressBarError";
		this.fileProgressElement.childNodes[3].style.width = "";

		var oSelf = this;
		this.setTimer(setTimeout(function () {
			oSelf.disappear();
		}, 5000));
	};
	FileProgress.prototype.setCancelled = function () {
		this.fileProgressElement.className = "progressContainer gray";
		this.fileProgressElement.childNodes[3].className = "progressBarError";
		this.fileProgressElement.childNodes[3].style.width = "";

		var oSelf = this;
		this.setTimer(setTimeout(function () {
			oSelf.disappear();
		}, 2000));
	};
	FileProgress.prototype.setStatus = function (status) {
		this.fileProgressElement.childNodes[2].innerHTML = status;
	};

	// Show/Hide the cancel button
	FileProgress.prototype.toggleCancel = function (show, swfUploadInstance) {
		this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
		if (swfUploadInstance) {
			var fileID = this.fileProgressID;
			this.fileProgressElement.childNodes[0].onclick = function () {
				$(this).closest('div').remove();
				swfUploadInstance.removeFile(fileID);
				return false;
			};
		}
	};

	FileProgress.prototype.appear = function () {
		if (this.getTimer() !== null) {
			clearTimeout(this.getTimer());
			this.setTimer(null);
		}

		if (this.fileProgressElement.filters) {
			try {
				this.fileProgressElement.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 100;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
				this.fileProgressElement.style.filter = "progid:DXImageTransform.Microsoft.Alpha(opacity=100)";
			}
		} else {
			this.fileProgressElement.style.opacity = 1;
		}

		this.fileProgressElement.style.height = "";

		this.height = this.fileProgressElement.offsetHeight;
		this.opacity = 100;
		this.fileProgressElement.style.display = "";

	};

	// Fades out and clips away the FileProgress box.
	FileProgress.prototype.disappear = function () {

		var reduceOpacityBy = 15;
		var reduceHeightBy = 4;
		var rate = 30;	// 15 fps

		if (this.opacity > 0) {
			this.opacity -= reduceOpacityBy;
			if (this.opacity < 0) {
				this.opacity = 0;
			}

			if (this.fileProgressElement.filters) {
				try {
					this.fileProgressElement.filters.item("DXImageTransform.Microsoft.Alpha").opacity = this.opacity;
				} catch (e) {
					// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
					this.fileProgressElement.style.filter = "progid:DXImageTransform.Microsoft.Alpha(opacity=" + this.opacity + ")";
				}
			} else {
				this.fileProgressElement.style.opacity = this.opacity / 100;
			}
		}

		if (this.height > 0) {
			this.height -= reduceHeightBy;
			if (this.height < 0) {
				this.height = 0;
			}

			this.fileProgressElement.style.height = this.height + "px";
		}

		if (this.height > 0 || this.opacity > 0) {
			var oSelf = this;
			this.setTimer(setTimeout(function () {
				oSelf.disappear();
			}, rate));
		} else {
			this.fileProgressElement.style.display = "none";
			this.setTimer(null);
		}
	};

	var _addAttachId = function(targetId, id){
		var elem = document.getElementById(targetId),
			defVal;
		if(elem) {
			defVal = elem.value;
			elem.value = defVal ? (defVal + "," + id) : id;
		}
	}
	var _removeAttachId = function(targetId, id){
		var elem = document.getElementById(targetId),
			defVal,
			valArr,
			index;

		if(elem) {
			defVal = elem.value,
				valArr = defVal.split(','),
				index = valArr.indexOf(id);

			if(index !== -1){
				valArr.splice(index, 1);
			}

			elem.value = valArr.join(",")
		}
	};

	var getCookie = (function(){
		var obj = {},
			cookies = document.cookie.split("; "),
			item, arr;
		for(var i=0, len = cookies.length; i < len; i++ ){
			item = cookies[i];
			arr = item.split("=");
			obj[arr[0]] = arr[1];
		};
		return obj;
	})();

	var noop = function(){}


	/**
	 * -100: "已达到文件上限"
     * -110: "文件超出上传大小限制"
     * -120: "不能上传零字节文件"
     * -130: "禁止上传该类型的文件"
     * -200: "上传出现错误：<%= message %>"
     * -210: "未设置上传地址"
     * -220: "服务器写入错误"
     * -230: "安全性错误"
     * -240: "已达到上传文件数上限"
     * -250: "上传失败"
     * -260: "未找到要上传的文件"
     * -270: "上传过程中发生错误"
     * -280: "已取消"
     * -290: "已暂停"
     * -300: "调整大小"
	 */
	var getErrorInfo = (function(){
		var infos = {
			'0': '该格式不支持上传',
			'-1': '上传失败',
			Q_TYPE_DENIED: '请上传JPG、PNG、GIF、BMP格式文件',
			Q_EXCEED_SIZE_LIMIT: '文件大小不能超过20M',
			F_EXCEED_SIZE: '单个文件不能超过20M',
			F_DUPLICATE: '该文件已上传'
		};

		return function(err, msg){
			return $.template(infos[err], { message: msg }) || U.lang("UPLOAD.UNHANDLED_ERROR", { message: msg })
		}
	})();

	//临时扩展Array.prototype
	Array.prototype.indexOf = Array.prototype.indexOf||function(item, index){
			if(this.length == undefined){
				throw new Error("Type Error: not Array!");
			}
			index = index||0;
			for(; index < this.length; index++){
				if(item === this[index])return index;
			}
			return -1;
		}

	var uploadConfig = Ibos.app.g('upload') || {
		fileSingleSizeLimit: 1024 * 20,
		attachexts: {
			depict: 'All Support Formats',
			ext: "*.csv;*.chm;*.pdf;*.zip;*.rar;*.tar;*.gz;*.bzip2;*.gif;*.jpg;*.jpeg;*.png;*.txt;*.doc;*.xls;*.ppt;*.docx;*.xlsx;*.pptx;*.htm;*.html"
		}
	}

	WebUploader.instances = WebUploader.instances || {};
	WebUploader.defaults = {
		formData: {
			PHPSESSID: getCookie.PHPSESSID
		},
		fileVal: 'Filedata',
		auto: true,
		swf: Ibos.app.getStaticUrl("/js/lib/webuploader/Uploader.swf"),
		server: Ibos.app.url('main/attach/upload', { "uid": Ibos.app.g("uid"), "hash": uploadConfig.hash }),
		custom_settings: {
			containerId: '',
			inputId: '',
			template: '<div class="attl-item" data-node-type="attachItem">' +
				'<a href="javascript:;" title="' + U.lang("UPLOAD.DELETE_ATTACH") + '" class="cbtn o-trash" data-aid="<%=aid%>" data-id="<%=id%>" data-node-type="attachRemoveBtn"></a>' +
				'<i class="atti"><img width="44" height="44" src="<%=icon%>" alt="<%=name%>" title="<%=name%>" /></i>' +
				'<div class="attc"><%=name%></div>' +
				'</div>'
		},
		fileSingleSizeLimit: uploadConfig.fileSingleSizeLimit * 1024
	}

	Ibos.fileUpload = function (options) {
		var uploader = WebUploader.create($.extend(true, {}, WebUploader.defaults, options));
		WebUploader.instances[options.pick.slice(1)] = uploader;
		return uploader
	}

	// @Todo: 上传前预览，涉及到AS
	// @Todo: 还是有点乱，需要继续整理
	// @deprecated 结构太复杂，不方便使用
	// 图片上传默认的HTML结构
	// 考虑将cover、progress动态生成，或作为配置项
	// <div class="img-upload">
	//  <div class="img-upload-cover"></div>
	//  <div class="img-upload-progress"></div>
	//  <div class="img-upload-imgwrap"></div>
	//  <span id="button_placeholder_id"></span>
	// </div>

	Ibos.imgUpload = function(options){
		var $movie,
			$wrap,
			$cover,
			$progress,
			$imgWrap;
		var imgUploadSettings = {
			// File Upload Settings
			// 默认不限制大小、数目和类型
			// fileNumLimit : "1",
			accept: {
				title: 'Images',
				extensions: 'gif,jpg,jpeg,bmp,png',
				mimeTypes: 'image/*'
			},
		}
		var uploader = Ibos.fileUpload($.extend({}, imgUploadSettings, options));
		uploader.on('beforeFileQueued', function (file) {
			var that = this;
			$movie = $(this.options.pick);
			$wrap = $movie.parent();
			$cover = $movie.siblings(".img-upload-cover");
			$progress = $movie.siblings(".img-upload-progress");
			$imgWrap = $movie.siblings(".img-upload-imgwrap");
		});

		// 当有文件添加进入的时候
		uploader.on('startUpload', function () {
			$cover.css("height", 0);
			$progress.text("0%")
			$wrap.removeClass("img-upload-success img-upload-error").addClass("img-upload-start")
		});
		// 上传文件
		uploader.on('uploadProgress', function (file, percentage) {
			var percent = Math.ceil(percentage * 100);
			$cover.css("height", percent + "%");
			$progress.text(percent + "%");
		});
		// 上传成功
		uploader.on('uploadSuccess', function (file, resData) {
			var $img = $imgWrap.find("img"),
				data = resData;

			// 检测是否已存在图片，存在时替换src，不存在时创建
			if (!$img.length) {
				$img = $('<img/>').appendTo($imgWrap);
			}
			$img.attr("src", data.thumburl || data.url);
			$wrap.addClass("img-upload-success").removeClass("img-upload-start");
			this.options.custom_settings.success && this.options.custom_settings.success.call(this, file, data);
		});
		var error = function () {
			$wrap.removeClass("img-upload-start").addClass("img-upload-error")
			$progress.html('')
		}
		// 上传失败
		uploader.on('uploadError', error);
		// 上传失败
		uploader.on('error', error);
		// 上传完成
		return uploader;
	}

	Ibos.upload = {};

	/**
	 * 附件上传
	 */

	var _attachUploadProgress = function (file, percentage) {
		console.log('_attachUploadProgress')
		try {
			var percent = Math.ceil(percentage * 100),
				progress = new FileProgress(file, this.options.custom_settings.containerId);

			progress.toggleCancel(true, this);
			progress.setProgress(percent);
			progress.setStatus(percent + " %");
		} catch(e){
			console.log(e);
		}
	};
	var _attachUploadError = function (file, error, message) {
		try {
			var progress = new FileProgress(file, this.options.custom_settings.containerId);
			progress.setError();
			progress.toggleCancel(false);
			progress.setStatus(getErrorInfo(error, message));
		} catch (e) {
			console.log(e);
		}
	};
	var _attachUploadSuccess = function (file, serverData, response) {
		console.log('_attachUploadSuccess')
		try {
			var that = this,
				cs = this.options.custom_settings,
				data = serverData;
			// 上传失败
			if (data.aid === -1 || data.aid === 0) {
				_attachUploadError.call(this, file, data.aid, data.name || '不支持的格式')
			}else{
				var progress = new FileProgress(file, cs.containerId);
	
				progress.setComplete();
				progress.toggleCancel(false);
	
				var $item = $.tmpl(cs.template, $.extend({}, file, {
					icon: data.imgurl || data.icon || "",
					type: file.type,
					aid: data.id || data.aid,
					id: file.id
				}))
	
				$("#" + file.id).replaceWith($item);
	
				if(cs.inputId) {
					_addAttachId(cs.inputId, data.id||data.aid);
				}
	
				cs.success && cs.success.call(this, file, data, $item, response)
			}
		} catch(e){
			console.log(e);
		}
	};

	Ibos.upload.attach = function(options){
		var attachexts = uploadConfig.attachexts
		var ext = attachexts.ext.replace(/\*/g, '').replace(/;/g, ',')
		var _settings = {
			// Backend Settings
			resize: false,

			accept: {
				title: attachexts.depict,
				extensions: ext.replace(/\./g, ''),
				mimeTypes: ext
			},
			pick: '#upload_btn'
		};
		var uploader = Ibos.fileUpload($.extend(true, {}, _settings, options));
		var _attachLoaded = function () {
			var cs = options.custom_settings || {},
				$container = $("#" + cs.containerId);
			if (!$container.length) {
				$.error("Ibos.upload.attach: 未找到节点#" + cs.containerId);
			}

			$container.on("click", '[data-node-type="attachRemoveBtn"]', function () {
				var $elem = $(this),
					aid = $elem.attr("data-aid"),
					id = $elem.attr("data-id");

				$elem.closest('[data-node-type="attachItem"]').remove();
				if (uploader.getFile(id)) {
					uploader.removeFile(id)
				}
				if (cs.inputId) {
					_removeAttachId(cs.inputId, aid || id);
				}

				cs.remove && cs.remove.call(uploader, aid || id);
			})
		};
		_attachLoaded()
		// 当有文件添加进入的时候
		uploader.on('fileQueued', _attachLoaded);
		// 上传文件
		uploader.on('uploadProgress', options.uploadProgress || _attachUploadProgress);
		// 上传成功
		uploader.on('uploadSuccess', options.uploadSuccess || _attachUploadSuccess);
		// 上传失败
		uploader.on('uploadError', options.uploadError || _attachUploadError);
		// 上传完成
		uploader.on('uploadComplete', function (file) {
			$('#' + file.id).find('.progress').fadeOut();
		});
		// 错误
		uploader.on('error', function (type, file) {
			var title = ''
			if (type == "F_DUPLICATE") {
				title = '请勿重新上传'
			} else if (file.size === 0) {
				title = '文件不能为空'
			} else if (type == "F_EXCEED_SIZE") {
				title = '单个文件大小，不能超过20m'
			} else if (type == "Q_TYPE_DENIED") {
				title = "请上传指定格式文件"
			} else if (type == "Q_EXCEED_SIZE_LIMIT") {
				title = "文件大小不能超过2M"
			} else {
				title = "上传出错！请检查后重新上传！错误代码" + type
			}

			Ui.tip(title, 'danger')
		});
		return uploader;
	}

	var _imageUploadStart = function(file){
		var _this = this,
			progressElem = document.getElementById(this.options.custom_settings.progressId);

		if(progressElem) {
			var modal = document.createElement("div");
			var modalText = document.createElement("div");

			modal.className = "img-upload-mask";
			modalText.className = "img-upload-mask-text";

			modalText.innerHTML = "0%";

			// 点击遮盖层可取消上传
			modal.onclick = function(){
				_this.cancelUpload(file.id)
			}

			modal.appendChild(modalText);
			progressElem.appendChild(modal);

			$.data(progressElem, "modal", modal);
			$.data(progressElem, "modalText", modalText);
		}
	}
	var _imageUploadProgress = function (file, percentage ) {
		try {
			var percent = Math.ceil(percentage * 100),
				progressElem = document.getElementById(this.options.custom_settings.progressId),
				modal, modalText;

			if(progressElem) {
				modal = $.data(progressElem, "modal");
				modalText = $.data(progressElem, "modalText");
				if(modal && modalText) {
					modal.style.height = percent + "%";
					modalText.innerHTML = percent + "%";
				}

			}
		} catch(e){
			console.log(e);
		}
	}
	var _imageUploadError =function(file, error, message){
		try {
			Ui.tip(getErrorInfo(error, message), "danger");
		} catch (e) {
			console.log(e);
		}
	};
	var _imageUploadSuccess = function(file, serverData, response){
		try {
			var that = this,
				cs = this.options.custom_settings,
				data = serverData;

			if(cs.targetId){
				$("#" + cs.targetId).html("<img src='" + data.imgurl + "' title='" + data.name + "' alt='" + data.name + "'>");
			}
			if(cs.inputId) {
				$("#" + cs.inputId).val(data.aid);
			}

			cs.success && cs.success.call(this, file, data, response)
		} catch(e){
			console.log(e);
		}
	}
	var _imageUploadComplete = function(file){
		try {
			var progressElem = document.getElementById(this.options.custom_settings.progressId),
				modal;

			if(progressElem) {
				modal = $.data(progressElem, "modal");
				progressElem.removeChild(modal);
				$.removeData(progressElem, "modal modalText");
			}

		} catch(e){
			console.log(e);
		}
	}
	Ibos.upload.image = function(options){
		var _settings = {
			// 只允许选择图片文件。
			accept: {
				title: 'Images',
				extensions: 'gif,jpg,jpeg,bmp,png',
				mimeTypes: 'image/*'
			}
		}
		var uploader = Ibos.fileUpload($.extend(true, {}, _settings, options))
		// 当有文件添加进入的时候
		uploader.on('fileQueued', options.fileQueued || _imageUploadStart);
		// 上传文件
		uploader.on('uploadProgress', options.uploadProgress || _imageUploadProgress);
		// 上传成功
		uploader.on('uploadSuccess', options.uploadSuccess || _imageUploadSuccess);
		// 上传失败
		uploader.on('uploadError', options.uploadError || _imageUploadError);
		// 上传完成
		uploader.on('uploadComplete', options.uploadComplete || _imageUploadComplete);
		return uploader
	}
})()
