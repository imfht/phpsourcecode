$(function(){
	//皮肤选择
	$("#background_save").on("click", function(){
		var isCheckedVal = $('input[type="radio"]:checked').val(),
			param = { type:  isCheckedVal},
			url = Ibos.app.url("dashboard/background/skin");

		$.post(url, param, function(res) {
            if (res.isSuccess) {
                Ui.tip(Ibos.l("OPERATION_SUCCESS"));
                //加载对应的css文件
            } else {
                Ui.tip(Ibos.l("OPERATION_FAILED"), "danger");
            }
        });
	});
});