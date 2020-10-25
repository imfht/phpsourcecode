$(document).ready(function() {
	var uploaders = {};
	$('.uploader').each(function() {
		var uploader, $target, $id;
		$target = $(this);
		$id = $target.attr('add_file_id');
		$btn_id = 'btn_' + $id;
		$uploader_id = 'uploader_' + $id;

		settings = {
			runtimes : 'html5,flash',
			browse_button : $btn_id, // you can pass in id...
			container : document.getElementById($uploader_id),
			url : upload_url,
			flash_swf_url : app_path + '/Public/Static/plupload/Moxie.swf',
			filters : {
				max_file_size : '1000mb'
			}
		};

		uploader = new plupload.Uploader(settings);
		uploaders[$id] = uploader;

		function FilesAdded(up, files) {
			for (var i in files) {
				html = '<li class="tbody" id="' + files[i].id + '">\n';
				html += '<div class="loading"></div>\n';
				html += '<div class="data">\n';
				html += '<span class="del text-center"><a class="link del">删除</a></span>\n';
				html += '<span class="size text-right">' + plupload.formatSize(files[i].size) + '</span>';
				html += '<span class="auto autocut">' + files[i].name + '</span>';
				html += '</li>';
				html += '</div>\n';
				$('#uploader_' + $id + ' .file_list').append(html);
			}
			up.start();
		}

		uploader.bind("PostInit", function(up) {
			if ($(".uploader .tbody").length > 0) {
				$(".uploader .tbody .loading").css("width", "100%");
				$(".uploader .thead").show();
				$(".uploader .tbody").each(function() {
					id = $(this).attr("filename");
					filename = $(this).attr("filename");
					size = $(this).attr("size");
					file = new plupload.File(id, filename, size);
					file.status = plupload.DONE;
					count = uploader.files.length;
					uploader.files[count] = file;
				});
			}
		});

		uploader.init();

		uploader.bind('FilesAdded', FilesAdded);

		uploader.bind("UploadProgress", function(up, file) {
			$("#" + file.id).find("a.del").hide();
			$("#" + file.id).find('.loading').css("width", file.percent + "%");
		});

		uploader.bind('FileUploaded', function(up, file, data) {
			var myObject = eval('(' + data.response + ')');
			if (myObject.status) {
				if ($("#add_file_" + $id).length != 0) {
					$("#add_file_" + $id).val($("#add_file_" + $id).val() + myObject.sid + ";");
				}
				$("#" + file.id).attr("add_file", myObject.sid);

				$new_upload = $("#uploader_" + $id + " .file_list").attr("new_upload");
				$("#uploader_" + $id + " .file_list").attr("new_upload", $new_upload + myObject.sid + ";");

				$("#" + file.id).find("a.del").show();
			} else {
				ui_alert(myObject.info, function() {
					$("#" + file.id).remove();
				});
			}
		});
	});
});

window.onbeforeunload = function(e) {
	e = e || window.event;
	// For IE and Firefox prior to version 4
	$new_upload = $(".file_list").attr("new_upload");
	if ($new_upload !== undefined) {
		if ($new_upload.length) {
			if (e) {
				e.returnValue = '上传的附件将被删除，确定退出吗？';
			}
			// For Safari
			return '上传的附件将被删除，确定退出吗？';
		}
	}
};

$(document).on("click", ".uploader a.del", function() {
	$obj = $(this).parents("li");
	$uploader = $(this).parents('.uploader');
	ui_confirm("确定要删除吗？", function() {

		$current_del_file = $obj.attr("add_file");
		$(".add_file", $uploader).val($(".add_file", $uploader).val().replace($current_del_file + ";", ""));

		$new_upload = $(".file_list", $uploader).attr("new_upload");
		$(".file_list", $uploader).attr("new_upload", $new_upload.replace($current_del_file + ";", ""));

		$obj.remove();
	});
});
