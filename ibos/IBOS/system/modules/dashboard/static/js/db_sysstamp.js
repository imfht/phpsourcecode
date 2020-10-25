// 图章上传及背景图上传
var PicUpload = {
	create: function($el, settings){
		var that = this,
			uploadObj;
		var id = '#' + $el.attr("id");
		// 找到SWFUpload控件的替换ID并加入settings中
		settings.pick = id
		// 此属性用于缓存SWFUpload替代节点的父节点，以便后续调用
		this[id] = Ibos.upload.image(settings);
	},
	init: function($els, settings){
		var that = this;
		$els.each(function(){
			var $el = $(this);
			that.create($el, settings);
		})
	},

	remove: function(id, callback){
		this['#'+ id].destroy();	
		callback && callback();
	}
};

(function() {
	// 图章上传
	var stampUploadSettings = {
		server: Ibos.app.url("dashboard/sysstamp/index", { op: "upload" }),
		fileVal: 'stamp',
		custom_settings: {
			success: function(data, file) {
				var uploadBtnWrapId = this.options.pick,
					uploadBtnWrap = $(uploadBtnWrapId),
					uploadWrap = uploadBtnWrap.parent().parent();
				uploadWrap.removeClass("stamp-img-new").find("img").attr("src", file.url);
				$(uploadWrap.find('input[type=hidden]').get(0)).val(file.fakeUrl);
			}
		}
	};

	// Init
	var $stampList = $("#stamp_list"),
		stampUploadSelector = ".stamp-img-upload div",
		$stampUploadHolders = $stampList.find(stampUploadSelector);

	PicUpload.init($stampUploadHolders, stampUploadSettings);

	// 小图章上传
	var stampIconUploadSettings = {
		server: Ibos.app.url("dashboard/sysstamp/index", { op: "upload" }),
		fileVal: 'stamp',
		custom_settings: {
			success: function(data, file) {
				var uploadBtnWrapId = this.options.pick,
					uploadBtnWrap = $(uploadBtnWrapId),
					uploadWrap = uploadBtnWrap.parent().parent();
				uploadWrap.removeClass("stamp-icon-new").find("img").attr("src", file.url);
				$(uploadWrap.find('input[type=hidden]').get(0)).val(file.fakeUrl);
			}
		}
	}

	// Init
	var $stampIconList = $("#stamp_list"),
		stampIconUploadSelector = ".stamp-icon-upload div";
	$stampIconUploadHolders = $stampList.find(stampIconUploadSelector);
	PicUpload.init($stampIconUploadHolders, stampIconUploadSettings);
	var stampPrefix = "stamp_upload_",
			stampIconPrefix = "stamp_icon_upload_";
	// 删除图章
	$stampList.on("click", ".o-trash", function() {
		var $el = $(this),
				uploadId = $el.attr("data-id"),
				isOld = $el.attr("data-type"),
				hasRemoveStamp = false,
				removeIdObj = $('#removeId');
		// 销毁对应SWFUpload对象
		PicUpload.remove((stampPrefix + uploadId), function() {
			hasRemoveStamp = true;
		});
		PicUpload.remove((stampIconPrefix + uploadId), function() {
			if (hasRemoveStamp) {
				// 移除对应节点
				$el.parents("li").first().remove();
			}
		});
		if (isOld) {
			var removeId = removeIdObj.val(),
					removeIdSplit = removeId.split(',');
			removeIdSplit.push(uploadId);
			removeIdObj.val(removeIdSplit.join());
		}
	});

	// 添加一个图章
	var stampAdd = function(data, lastItem) {
		var uploadTpl = $.template("upload_add_tpl", data),
				uploadNode = $(uploadTpl),
				stampUploadHolder,
				stampIconUploadHolder;
		// 需先插入到文档再初始化
		uploadNode.insertBefore(lastItem);
		stampUploadHolder = uploadNode.find(stampUploadSelector);
		stampIconUploadHolder = uploadNode.find(stampIconUploadSelector);

		// 初始化其SWFUpload对象
		PicUpload.create(stampUploadHolder, stampUploadSettings);
		PicUpload.create(stampIconUploadHolder, stampIconUploadSettings);
	};
	var $uploadBtn = $("#upload_add"),
		$stampItemLast = $uploadBtn.parent(),
		stampGid = Ibos.app.g("maxSort");
	$uploadBtn.on("click", function() {
		stampAdd({id: +new Date(), sort: stampGid++}, $stampItemLast);
	});
})();
