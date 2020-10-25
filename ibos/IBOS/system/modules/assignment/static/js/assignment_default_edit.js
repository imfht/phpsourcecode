/**
 * 分派任务编辑任务
 * @version  $Id$
 */
// 初始化时间选择器
$(function(){
	$("#am_edit_starttime").datepicker({
		target: "am_edit_endtime",
		format: "yyyy-mm-dd hh:ii",
		pickTime: true,
		pickSeconds: false
	});

	// 初始化人员选择
	var userData = Ibos.data.get("user");
	// 负责人为单选
	$("#am_edit_charge").userSelect({
		data: userData,
		type: "user",
		maximumSelectionSize: 1,
		placeholder: U.lang("ASM.CHARGER")
	});
	// 参与人人员选择
	$("#am_edit_participant").userSelect({
		data: userData,
		type: "user",
		placeholder: U.lang("ASM.PARTICIPANT")
	});

	// 任务说明计数器
	var template = "<strong><%=count%></strong>/<strong><%=maxcount%></strong>";
	$("#am_edit_description").charCount({
		display: "am_edit_description_charcount",
		template: template,
		warningTemplate: template,
		countdown: false
	});

	var initUpload = function(){
		// 附件上传功能
		Ibos.upload.attach({
			formData: { module: 'assignment' },
			pick: '#am_edit_att_upload',
			custom_settings: {
				containerId: 'am_edit_att_list',
				inputId: 'am_edit_attachmentid'
			}
		});
	};

	// 先检测 SWFUpload 是否存在 避免重复加载 SWFUpload 插件
	if (WebUploader){
		initUpload();
	} else {
		$.getScript(Ibos.app.getStaticUrl("/js/lib/webuploader/webuploader.js"), function(){
			$.getScript(Ibos.app.getStaticUrl("/js/lib/webuploader/handlers.js"), function(){
				initUpload();
			});
		});
	}
});