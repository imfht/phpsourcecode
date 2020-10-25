;(function ($) {
	//定义构造函数
	var plUploadCls = function (ele, opt) {
		var instance = this;
		instance.$element = ele;
		instance.args = {};

		//上传插件实例
		instance.pluploadInstance = null;
		instance.defaults = {
			'tag_browse_btn': 'plUpload_browse_btn',
			'uploadUrl': './Upload',
			'filters': {},
			'extensions': '',
			'fileAdded': function (uploader, args) {
			},
			'completedUpload': function () {
			},
			'pluploadError': function () {
			}
		};
		instance.options = $.extend({}, instance.defaults, opt);

		/**
		 * 完成上传
		 * @param up
		 * @param response
		 */
		instance.completedUpload = function (up, response) {
			//反馈图片上传结果
			instance.options.completedUpload(response);
		}

		/**
		 * 初始化上传插件
		 */
		instance.init = function () {
			//实例化前，销毁原插件实例
			if (instance.pluploadInstance != null) {
				instance.pluploadInstance.destroy();
				instance.pluploadInstance = null;
			}

			//实例化上传插件
			instance.pluploadInstance = new plupload.Uploader({
				runtimes: 'html5,flash,silverlight,html4',
				browse_button: instance.options.tag_browse_btn,
				multi_selection: false,
				url: instance.options.uploadUrl,
				filters: instance.options.filters,
				file_data_name: "upload",
				multipart_params: instance.args,
				init: {
					PostInit: function (up) {
					},
					FilesAdded: function (up, files) {
						if (up.files.length > 1) {
							up.removeFile(up.files[0]);
						}
						instance.options.fileAdded(up, instance.args);
					},
					FileUploaded: function (up, file, response) {
						instance.completedUpload(up, response);
					},
					Error: function (uploader, error) {
						//通知上传失败
						instance.options.pluploadError(uploader, error);
					}
				}
			});

			//执行上传插件初始化
			instance.pluploadInstance.init();
		}

	};

	//定义插件
	$.fn.plUpload = function (options) {
		//创建实例
		var instance = new plUploadCls(this, options);
		//进行初始化操作
		instance.init();
	};
})(jQuery);
