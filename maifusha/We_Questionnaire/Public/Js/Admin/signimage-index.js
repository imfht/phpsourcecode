/* Dropzone上传插件配置 */
Dropzone.options.uploadForm = {
	paramName: "signimage", //上传字段名
	maxFilesize: 2, // MB
	acceptedFiles: ".jpg,.jpeg,.png,.gif,.bmp",
	addRemoveLinks: true,
	clickable: true,
	autoProcessQueue: false, //关闭自动上传, 手动调度
	uploadMultiple: true,
	parallelUploads: 10, //最大并行处理量
	maxFiles: 100, //最大上传数量
	
	/* 插件消息翻译 */
	dictDefaultMessage: '<i class="fa fa-cloud-upload"></i>拖拉文件上传<br />或 <i class="fa fa-thumbs-down"></i>点此上传',
	dictInvalidFileType: '只支持图片文件上传',
	dictFileTooBig: '图片超出最大2M约束',
	dictMaxFilesExceeded: '超出最大上传数量',
	dictCancelUpload: '取消上传',
	dictRemoveFile: '去除文件',
	dictCancelUploadConfirmation: '确认取消上传',

	/* 上传缩略图预览模板 */
	previewTemplate: '	\
						<div class="dz-preview dz-file-preview">	\
						  <div class="dz-details">	\
						    <div class="dz-filename"><span data-dz-name></span></div>	\
						    <div class="dz-size" data-dz-size></div>	\
						    <img data-dz-thumbnail />	\
						  </div>	\
						  <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>	\
						  <div class="dz-success-mark"><span>✔</span></div>	\
						  <div class="dz-error-mark"><span>✘</span></div>	\
						  <div class="dz-error-message"><span data-dz-errormessage>""</span></div>	\
						</div>	\
					 ',

	/* 初始化期间注册一些事件处理句柄 */
	init: function(){
	    var self = this;

	    /* 点击上传按钮开始ajax上传 */
	    this.element.querySelector("button#uploadBtn").addEventListener("click", function(e) {
	      e.preventDefault();
	      e.stopPropagation();
	      self.processQueue();
	    });

	    /* 上传成功后 */
	    this.on("successmultiple", function(files, response) {
	    	$('#upload-form #uploadBtn').tooltip({
	    		title: response.info,
	    		trigger: 'manual',
	    		container: '#uploadBtn', //该行解决了一个排版问题
	    	}).tooltip('show');

	    	/* 刷新页面 */
	    	setTimeout(function(){
	    		window.location = response.url || window.location;
	    	}, 1200);
	    });

		this.on("error", function(file, errorMessage){
			$(file.previewElement).find('[data-dz-errormessage]').html(errorMessage);
		});
	},
};


$(function(){
	/* 重设按钮点击后，更改表单请求地址并上传，重新设定图片启用状态 */
	$(document).delegate('#updateStatusBtn', 'click', function(){
		$('#signimage-list-form').attr('action', '/Signimage/updateStatus');

		/* 重设所有开关为checked以使其值能被表单提交，但这里的设定不会触发开关的change事件 */
		$('#signimage-table .switch input[type=checkbox]').each(function(){
			this.checked = true;
		});

		$('#signimage-list-form').submit();
	});

	/* 修正开关插件的缺陷，使得它的值随切换状态而改变 */
	$(document).delegate('#signimage-table .switch input[type=checkbox]', 'change', function(){
		$this = $(this);
		var toStatus = $this.is(':checked') ? 'on' : 'off';
		$this.val(toStatus);
	});
});