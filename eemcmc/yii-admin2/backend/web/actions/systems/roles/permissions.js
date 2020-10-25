function app() {
	/**
	 * app对象
	 * @type app
	 */
	var self = this;

	/**
	 * 运行
	 */
	this.run = function () {
		//选择行
		$('.select-row').change(function (event) {
			$('input[data-parent=' + this.value + ']').prop('checked', this.checked);
		});

		//选择所有
		$('.select-all').click(function (event) {
			$('input[type=checkbox]').prop('checked', this.checked);
		});

		//返回按钮
		$('#back').click(function () {
			window.location.href = '/systems/roles';
		});

		//提交保存
		$('#submit').click(function () {
			var button = $(this);
			button.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
			//获取请求参数
			var role_name = button.attr('data-rolename');
			var permissions = [];
			$("input[data-parent]:checked").each(function () {
				permissions.push($(this).val());
			});
			var params = {role_name: role_name, permissions: permissions};
			//保存权限
			$.post('/api/systems/roles/save-permissions', params, function (data) {
				if (data.errcode) {
					jp.messager.failure(data.errmsg);
				} else {
					jp.messager.success('操作完成');
				}
				button.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
			}, 'json');
		});
	}
}
var app = new app();
app.run();