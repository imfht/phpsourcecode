/**
 * 以百度上传组件为基础二次封装 插件
 * @file  webuploader.custom.js
 * @author molong <molong@tensent.cn>
 * @time  2016年1月12日18:04:09
 * @Copyright (c) 2007-2014 http://www.tensent.cn All rights reserved.
 */


/***
 * 传递服务器参数
 * fileType 文件类型 'sys' 系统文件或 'space' 用户空间图片
 * objType sys类型文件存储的路径 'ad' 广告图片 'auth' 认证图片 'mark' 等级图片存放目录 'tools' 增值服务图片存放目录
 * filename 文件表单name
 * taskId 任务编号
 * workId 稿件编号
 * 服务器回调json数据
 * msg : {url : 文件存储路径, filename : 文件上传表单name, name : 文件名, field : 文件上传后数据表记录id值 }
 */
;(function($, w) {
	"use strict";
	var pluginName = 'SentUploader';

	function Upload($fileInput, options, seropts) {
		this.options = $.extend({}, $.fn[pluginName].defaults, options);
		this.seropts = seropts; //传递给服务器的参数
		this.fileInput = $fileInput;
		this.BDUploader = undefined;
		this.baseUrl = w.baseurl !== undefined ? w.baseurl : '';
		this.allowNum = this.options.fileNumLimit; //剩余上传的个数
	}
	//插件扩展方法
	$.extend(Upload.prototype, {
		/**
		 * 调试开关
		 */
		debug: false,
		/**
		 * 上传调试输出
		 */
		dump: function(object) {
			if (this.debug) {
				if (typeof object == 'object') {
					console.dir(object);
				} else {
					console.log(object);
				}
			}
		},
		/**
		 * 拼接到上传组件的server地址参数  传递给服务器的数据
		 */
		postData: function() {

			var parm = '';
			for (var serv in this.seropts) {
				parm += "&" + serv + "=" + this.seropts[serv];
			}
			if (parm != '') {
				this.options.server += parm
			};
		},

		absolutePath: function() {
			if (this.baseUrl != '') {
				this.options.server = this.baseUrl + '/' + this.options.server;
				this.options.delUrl = this.baseUrl + '/' + this.options.delUrl;
				this.options.swf = this.baseUrl + '/' + this.options.swf;
			}
		},
		/**
		 * 初始化上传组件，监听各种事件
		 */
		initUploader: function() {
			var self = this;
			self.absolutePath();
			self.postData();
			self.BDUploader = WebUploader.create(self.options);

			for (var funcName in self.options.uploadEvents) {
				if (typeof self.options.uploadEvents[funcName] == 'function') {
					switch (funcName) {
						case 'dndAccept': //阻止此事件可以拒绝某些类型的文件拖入进来。目前只有 chrome 提供这样的 API，且只能通过 mime-type 验证。
							self.BDUploader.on(funcName, function(items) {
								self.options.uploadEvents[funcName](items);
							});
							break;
						case 'filesQueued': //当一批文件添加进队列以后触发。
							self.BDUploader.on(funcName, function(files) {
								self.options.uploadEvents[funcName](files);
							});
							break;
						case 'reset': //当 uploader 被重置的时候触发。
						case 'startUpload': //当开始上传流程时触发。
						case 'stopUpload': //当开始上传流程暂停时触发。
						case 'uploadFinished': //当所有文件上传结束时触发。
							self.BDUploader.on(funcName, function() {
								self.options.uploadEvents[funcName]();
							});
							break;
						case 'uploadBeforeSend': //当某个文件的分块在发送前触发，主要用来询问是否要添加附带参数，大文件在开起分片上传的前提下此事件可能会触发多次。
							self.BDUploader.on(funcName, function(object, data, headers) {
								self.options.uploadEvents[funcName](object, data, headers);
							});
							break;
						case 'uploadAccept': //当某个文件上传到服务端响应后，会派送此事件来询问服务端响应是否有效。如果此事件handler返回值为false, 则此文件将派送server类型的uploadError事件。
							self.BDUploader.on(funcName, function(object, ret) {
								self.options.uploadEvents[funcName](object, ret);
							});
							break;
						case 'uploadProgress': //上传过程中触发，携带上传进度。
							self.BDUploader.on(funcName, function(file, percentage) {
								self.options.uploadEvents[funcName](file, percentage);
							});
							break;
						case 'uploadError': //当文件上传出错时触发。
							self.BDUploader.on(funcName, function(file, reason) {
								self.options.uploadEvents[funcName](file, reason);
							});
							break;
						case 'uploadSuccess': //当文件上传成功时触发。//用户自定义回调处理
							self.BDUploader.on(funcName, function(file, response) {
								self.options.uploadEvents[funcName](file, response);
							});
							break;
						case 'error': //当validate不通过时，会以派送错误事件的形式通知调用者。通过upload.on('error', handler)可以捕获到此类错误，目前有以下错误会在特定的情况下派送错来。
							self.BDUploader.on(funcName, function(type) {
								self.options.uploadEvents[funcName](type);
							});
							break;
						case 'beforeFileQueued': //当文件被加入队列之前触发，此事件的handler返回值为false，则此文件不会被添加进入队列。
						case 'fileQueued': //当一批文件添加进队列以后触发。
						case 'fileDequeued': //当文件被移除队列后触发。
						case 'uploadStart': //某个文件开始上传前触发，一个文件只会触发一次。
						case 'uploadComplete': //不管成功或者失败，文件上传完成时触发。
						default:
							self.BDUploader.on(funcName, function(file) {
								self.options.uploadEvents[funcName](file);
							});
							break;
					}
				}
			}

			/**
			 * 默认上传添加队列处理
			 */
			if (!self.options.uploadEvents.fileQueued) {
				self.BDUploader.on('fileQueued', function(file) {

					if (self.allowNum == 0 || self.isAllowUpload() == 0) {
						self.BDUploader.removeFile(file);
						$.messager.show('上传文件数量超出限制,最多上传' + self.options.fileNumLimit + '个文件', {
							placement: 'bottom'
						});
						return false;
					}

					if ($("#" + self.options.listName).length > 0) {
						//文件上传后 列表显示效果
						var divProgress = '<div class="progress file-progress"><div class="progress-bar progress-bar-striped active" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 1%"></div></div>';
						var closeHtml = '<span class="webuploader-pick-file-close" data-queued-id="' + file.id + '"><i class="close"></i></span>';
						var fileName = '<span class="fname">' + file.name + '</span>';
						var fileSize = '<span class="fsize">大小:' + WebUploader.formatSize(file.size) + '</span>';
						var fileBox = '<div class="filebox"></div>';
						var clearfix = '<div class="clearfix"></div>';
						var liHtml = '<li class="affix-list-item" id=' + file.id + '>' + '<div class="upload-file-info">' + closeHtml + fileName + fileSize + clearfix + '</div>' + fileBox + divProgress + '</li>';
						$("#" + self.options.listName).append(liHtml);
					}
				});
			}
			/**
			 * 默认上传进度处理
			 */
			if (!self.options.uploadEvents.uploadProgress) {
				self.BDUploader.on('uploadProgress', function(file, percentage) {
					var $li = $('#' + file.id),
						$percent = $li.find('.progress .progress-bar');
					if (percentage > 0.2) {
						$percent.text('已上传' + parseInt(percentage * 100, 10) + '%');
					}
					$percent.css('width', percentage * 100 + '%');
					if (percentage == 1) {
						$percent.text('上传完成100%').removeClass('active').attr('aria-valuenow', parseInt(percentage * 100, 10));
					}
				});
			}
			/**
			 * 默认文件上传验证错误提示
			 */
			if (!self.options.uploadEvents.error) {
				self.BDUploader.on('error', function(type) {
					var title = '',
						msg = '',
						errtype = 'error';
					switch (type) {
						case 'Q_EXCEED_NUM_LIMIT':
							title = '上传文件数量超出限制';
							msg = '最多上传' + self.options.fileNumLimit + '个文件';
							break;
						case 'F_EXCEED_SIZE':
							title = '单个文件大小超出限制';
							msg = '最大上传' + WebUploader.formatSize(self.options.fileSingleSizeLimit);
							break;
						case 'Q_EXCEED_SIZE_LIMIT':
							title = '文件总大小超出限制';
							msg = '最大上传' + WebUploader.formatSize(self.options.fileSizeLimit);
							break;
						case 'Q_TYPE_DENIED':
							title = '文件类型限制';
							msg = self.options.accept.extensions;
							break;
						case 'F_DUPLICATE':
							title = '同名文件已存在';
							break;
						default:
							title = '未知类型上传错误' + type;
							break;
					}
					$.messager.show(title + msg, {
						placement: 'bottom'
					});
				});
			}
			/**
			 * 默认文件上传出错时处理
			 */
			if (!self.options.uploadEvents.uploadError) {
				self.BDUploader.on('uploadError', function(file, reason) {
					self.dump(file);
					self.dump(reason);
				});
			}
			/**
			 * 默认文件上传成功处理
			 */
			if (!self.options.uploadEvents.uploadSuccess) {
				self.BDUploader.on('uploadSuccess', function(file, response) {
					self.dump(file);
					//客户端完成上传，服务端返回错误信息
					if (response.status == 0) {
						$('#' + file.id).remove();
						self.BDUploader.removeFile(file.id, true);
						$.messager.show(response.info, {
							placement: 'bottom'
						});
						self.dump('上传完成但服务端有错误');
						return false;
					}
					self.dump('上传成功');
					var fileVal = self.getHiddenValue();
					var fileItem = "#" + this.options.listName + " #" + file.id;
					var $fileItem = $(fileItem);
					var type = (file.type).split('/')[0];
					//类型为图片时
					var filebox_html = '';
					if (type == 'image') {
						filebox_html = '<img src="' + BASE_URL + response.info.path + '" class="img-responsive" />';
					}else{
						filebox_html = '';
					}
					$fileItem.find('div.filebox').addClass(type).html(filebox_html);
					$fileItem.find('.upload-file-info span').eq(0).attr('data-id', response.info.id).attr('data-fileurl', response.info.path);
					var responseVal = '';
					responseVal = self.options.hiddenValType == '1' ? response.info.id : response.info.path
					fileVal = fileVal != '' ? fileVal + self.options.separator + responseVal : responseVal;
					self.setHiddenValue(fileVal);
					self.allowNum--;
				});
			}
			/**
			 * 默认文件上传完成时触发。不管成功或者失败
			 */
			if (!self.options.uploadEvents.uploadComplete) {
				self.BDUploader.on('uploadComplete', function(file) {
					self.dump('上传完成');
				});
			}

		}

		,
		delFile: function() {
				var self = this;
				var selecter = '';
				if (self.options.listName) {
					selecter = '#' + self.options.listName;
				}
				if (self.options.editListName) {
					selecter += ',#' + self.options.editListName;
				}
				$(selecter).on('click', '.webuploader-pick-file-close', function() {
					var thisClose = $(this);
					var fid = parseInt(thisClose.attr('data-id'), 10);
					var furl = $.trim(thisClose.attr('data-fileurl'));
					var qfid = $.trim(thisClose.attr('data-queued-id')); //文件所在队列id
					thisClose.html('<i class="loading"></i>'); //load加载标识
					if (fid > 0) {
						$.post(self.options.delUrl, {
							"id": fid
						}, function(json) {
							if (json.status == '1') {
								if (self.options.hiddenValType == '1') {
									self.resetHiddenVal(fid);
								} else {
									self.resetHiddenVal(furl);
								}
								$('#'+self.options.listName+' #'+qfid).remove();

								// if (qfid.substring(0, 7) == 'WU_FILE') {
								// 	self.BDUploader.removeFile(qfid, true);
								// }
								self.allowNum++;
							} else {
								$.messager.show(json.msg, {
									placement: 'bottom'
								});
								thisClose.html('<i class="close"></i>'); //load加载标识
							}
						}, 'json');
					}
				});

			}
			//重置隐藏域的值
			,
		resetHiddenVal: function(val) {
				var self = this;
				var hdnVal = self.getHiddenValue();
				var valArr = hdnVal.split(self.options.separator);
				valArr = self.returnNewArr(val, valArr);
				self.setHiddenValue(valArr.join(self.options.separator));
			}
			//删除数组中的指定的值
			,
		returnNewArr: function(value, arr) {
				var tmpArr = new Array();
				for (var i = 0; i < arr.length; i++) {
					if (arr[i] != value) {
						tmpArr.push(arr[i]);
					}
				}
				return tmpArr;
			}
			//获取隐藏域中的值的个数
			,
		getHiddenValNum: function() {
				var self = this,
					existsVal = new Array();
				var hdnVal = self.getHiddenValue();
				if (hdnVal) {
					existsVal = hdnVal.split(self.options.separator);
				}
				return existsVal.length;
			}
			//是否允许上传
			,
		isAllowUpload: function() {
			return this.options.fileNumLimit - this.getHiddenValNum();
		},
		getHiddenValue: function() {
			return $("#" + this.options.hiddenName).val();
		},
		setHiddenValue: function(val) {
			return $("#" + this.options.hiddenName).val(val);
		}
	});
	//插件入口
	$.fn[pluginName] = function(options, seropts) {
		var opts = $.extend({
			pick: '#' + this.attr("id")
		}, options);
		var uploader = new Upload(this, opts, seropts);
		uploader.initUploader();
		if (uploader.options.delFile == true) {
			uploader.delFile();
		}
	}
		//插件默认参数
	$.fn[pluginName].defaults = {
		dnd: undefined, //指定Drag And Drop拖拽的容器，如果不指定，则不启动 
		disableGlobalDnd: false, //是否禁掉整个页面的拖拽功能，如果不禁用，图片拖进来的时候会默认被浏览器打开
		paste: undefined, //指定监听paste事件的容器，如果不指定，不启用此功能。此功能为通过粘贴来添加截屏的图片。建议设置为document.body.
		/** 
		 * pick                :undefined,//指定选择文件的按钮容器，不指定则不创建按钮。
		 * id {Seletor|dom} 指定选择文件的按钮容器，不指定则不创建按钮。注意 这里虽然写的是 id, 但是不是只支持 id, 还支持 class, 或者 dom 节点。
		 * label {String} 请采用 innerHTML 代替
		 * innerHTML {String} 指定按钮文字。不指定时优先从指定的容器中看是否自带文字。
		 * multiple {Boolean} 是否开起同时选择多个文件能力。
		 */
		accept: null, //指定接受哪些类型的文件。 由于目前还有ext转mimeType表，所以这里需要分开指定。
		/**
		* 
		* title {String} 文字描述
		* extensions {String} 允许的文件后缀，不带点，多个用逗号分割。
		* mimeTypes {String} 多个用逗号分割。
		* 如：{
		title: 'Images',
		extensions: 'gif,jpg,jpeg,bmp,png',
		mimeTypes: 'image/*'
		}
		*/

		thumb: {}, //配置生成缩略图的选项。
		/**
		* {
		width: 110,
		height: 110,

		// 图片质量，只有type为`image/jpeg`的时候才有效。
		quality: 70,

		// 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
		allowMagnify: true,

		// 是否允许裁剪。
		crop: true,

		// 为空的话则保留原有图片格式。
		// 否则强制转换成指定的类型。
		type: 'image/jpeg'
		}
		*/
		compress: {}, // {Object} [可选]         配置压缩的图片的选项。如果此选项为false, 则图片在上传前不进行压缩。
		/**
		* {
		width: 1600,
		height: 1600,

		// 图片质量，只有type为`image/jpeg`的时候才有效。
		quality: 90,

		// 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
		allowMagnify: false,

		// 是否允许裁剪。
		crop: false,

		// 是否保留头部meta信息。
		preserveHeaders: true,

		// 如果发现压缩后文件大小比原来还大，则使用原来图片
		// 此属性可能会影响图片自动纠正功能
		noCompressIfLarger: false,

		// 单位字节，如果图片大小小于此值，不会采用压缩。
		compressSize: 0
		}
		*/
		auto: true, //设置为 true 后，不需要手动调用上传，有文件选择即开始上传。
		runtimeOrder: 'html5,flash', //指定运行时启动顺序。默认会想尝试 html5 是否支持，如果支持则使用 html5, 否则则使用 flash.可以将此值设置成 flash，来强制使用 flash 运行时。
		prepareNextFile: true, //是否允许在文件传输时提前把下一个文件准备好。 对于一个文件的准备工作比较耗时，比如图片压缩，md5序列化。 如果能提前在当前文件传输期处理，可以节省总体耗时。
		chunked: false, //是否要分片处理大文件上传。
		chunkSize: 5242880, //如果要分片，分多大一片？ 默认大小为5M.
		chunkRetry: 2, //如果某个分片由于网络问题出错，允许自动重传多少次？
		threads: 3, //上传并发数。允许同时最大上传进程数。
		formData: {}, //文件上传请求的参数表，每次发送都会发送此对象中的参数。
		fileVal: 'file', //设置文件上传域的name。
		method: 'POST', //文件上传方式，POST或者GET。
		sendAsBinary: false, //是否已二进制的流的方式发送文件，这样整个上传内容php://input都为文件内容， 其他参数在$_GET数组中。
		fileNumLimit: undefined, //验证文件总数量, 超出则不允许加入队列。
		fileSizeLimit: undefined, //验证文件总大小是否超出限制, 超出则不允许加入队列。以字节为单位
		fileSingleSizeLimit: undefined, //验证单个文件大小是否超出限制, 超出则不允许加入队列。以字节为单位
		duplicate: undefined, //去重， 根据文件名字、文件大小和最后修改时间来生成hash Key.
		disableWidgets: undefined, //默认所有 Uploader.register 了的 widget 都会被加载，如果禁用某一部分，请通过此 option 指定黑名单。 
		swf: 'static/js/webuploader/Uploader.swf',
		server: BASE_URL + '/admin/upload/upload.html?flash=1', // 文件接收服务端。
		/**
		 * ΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔΔ
		 * 以上参数是百度上传组件默认参数
		 * 以下参数是非百度上传组件参数（即本插件根据实际开发设置的参数）
		 * ∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨∨
		 */
		uploadEvents: {}, //上传事件
		delFile: true, //文件删除是否开启，开启后上传文件显示列表有删除文件按钮，默认开启
		hiddenName: 'fileid', //文件上传隐藏域ID
		hiddenValType: '1', //文件上传隐藏域保存的值的类型   1=保存的是file表的文件编号ID，2=保存的是文件的实际路径
		listName: 'fileList', //文件上传完成显示列表区域id
		editListName: 'editfileList', //文件上传完成显示列表区域id
		delUrl: BASE_URL + '/admin/upload/delete.html',
		separator: ',', //默认逗号
	}

})(jQuery, window);