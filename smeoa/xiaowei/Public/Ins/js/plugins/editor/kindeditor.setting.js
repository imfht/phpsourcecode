var editor;
function editor_init() {
	if (is_mobile()) {
		settings = {
			resizeType : 1,
			filterMode : true,
			uploadJson : upload_url,
			width : '100%',
			items : [],
			afterBlur : function() {
				this.sync();
			}
		};
	} else {
		settings = {
			resizeType : 1,
			filterMode : true,
			uploadJson : upload_url,
			width : '100%',
			items:['undo', 'redo', '|','plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
        'superscript', 'clearhtml', 'quickformat','|', 'fullscreen', '/',
        'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image',
       'table', 'hr','link', 'unlink',],
			afterBlur : function() {
				this.sync();
			}
		};
	}
	window.editor = new KindEditor.create(".editor", settings);

	if (is_mobile()) {
		settings = {
			resizeType : 1,
			filterMode : true,
			uploadJson : upload_url,
			width : '100%',
			items : [],
			afterBlur : function() {
				this.sync();
			}
		};
	} else {
		settings = {
			width : '100%',
			resizeType : 1,
			allowPreviewEmoticons : true,
			uploadJson : upload_url,
			allowImageUpload : true,
			syncType : 'form',
			height : 200,
			items : ['fontsize', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|', 'image', '|', 'fullscreen'],
			afterBlur : function() {
				this.sync();
			}
		};
	}
	window.editor = new KindEditor.create(".simple", settings);
}

function df() {
	var haspicContainer = document.getElementById("has_pic");
	if (haspicContainer == null) {
		haspicContainer = document.createElement("div");
		haspicContainer.id = "has_pic";
		haspicContainer.innerHTML = "<input type='text' id='piclist' value='' style='display:none;'/><div id='upload'><b>您有图片需要上传到服务器</b>&nbsp;&nbsp;<a href='javascript:uploadpic();' >上传</a></div><div id='confirm'></div>";
		$(".ke-toolbar").after(haspicContainer);
	}

	var img = $(".ke-edit-iframe").contents().find("img");

	var piccount = 0;
	var sstr = "";
	$(img).each(function(i) {
		var that = $(this);
		if (that.attr("src").indexOf("http://") >= 0 || that.attr("src").indexOf("https://") >= 0) {
			piccount++;
			if (i == $(img).length - 1)
				sstr += that.attr("src");
			else
				sstr += that.attr("src") + "|";
		}
	});

	$("#piclist").val(sstr);
	document.getElementById("has_pic").style.display = (piccount > 0) ? "block" : "none";
}

function closeupload() {
	$("#has_pic").hide();
	$("#upload").show();
}

function uploadpic() {
	var piclist = encodeURI($("#piclist").val());
	if (piclist.length == 0)
		return false;

	$.ajax({
		url : upload_url,
		data : "pic=" + piclist,
		type : "GET",
		beforeSend : function() {
			$("#upload").hide();
			$("#confirm").text("正在上传中...");
		},
		success : function(msg) {
			if (msg !== "") {
				var str = new Array();
				str = msg.split('|');
				var img = $(".ke-edit-iframe").contents().find("img");

				$(img).each(function(i) {
					var that = $(this);
					if (that.attr("src").indexOf("http://") >= 0 || that.attr("src").indexOf("https://") >= 0) {
						that.attr("src", "/uploads/image/" + str[i]);
						that.attr("data-ke-src", "/uploads/image/" + str[i]);
					}
				});

				$("#confirm").html(img.length + "张图片已经上传成功！&nbsp;&nbsp;<a href='javascript:closeupload();'>关闭</a>");
			} else
				$("#confirm").text("上传失败！");
		}
	});
}