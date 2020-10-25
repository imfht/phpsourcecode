function app() {
	/**
	 * app对象
	 * @type app
	 */
	var self = this;

	/**
	 * grid组件对象
	 * @type jp.grid
	 */
	var grid = null;

	/**
	 * grid配置
	 * @type type
	 */
	var config = {
		readUrl: '/api/systems/historys',
		page: 1,
		page_size: 15,
		fixedLeftWidth: '50%',
		fixedRightWidth: 270,
		columns: [
			{width: 60, text: '#', type: 'number', flex: false, colClass: 'text-center', sort: false, name: 'id'},
			{width: 80, text: '管理员id', type: 'date', flex: false, colClass: 'text-center', sort: false, name: 'user_id'},
			{width: 100, text: '域名', type: 'string', flex: false, colClass: '', sort: false, name: 'domain'},
			{width: 200, text: 'URL', type: 'string', flex: false, colClass: '', sort: false, name: 'url'},
			{width: 60, text: 'IP', type: 'string', flex: false, colClass: '', sort: false, name: 'ip'},
			{width: 120, text: '操作时间', type: 'string', flex: false, colClass: '', sort: true, name: 'created_at'},
			{width: 400, text: '参数', type: 'string', flex: true, colClass: '', sort: false, name: 'params'},
		],
	};

	/**
	 * 运行
	 */
	this.run = function () {
		grid = new jp.grider(config);
	}

	/**
	 * 更新信息
	 * @returns {undefined}
	 */
	this.actionUpdate = function (rawData) {
		//显示
		var modal = new ModalTrigger({custom: $('#update').html()});
		modal.show({
			title: '更新作品信息',
			shown: function (event)
			{
				var form = $(event.target).find('.modal-content');
				//初始化状态值
				form.find('.dostatus').val(rawData.dostatus);
				form.find('.name').val(rawData.name);
				//绑定保存按钮事件
				var saveBtn = form.find('.save');
				saveBtn.click(function () {
					//设置按钮状态
					saveBtn.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
					//设置参数
					var params = {
						id: rawData.id,
						dostatus: form.find('.dostatus').val(),
						name: form.find('.name').val(),
					};
					//修改
					$.post('/api/albums/update', params, function (data) {
						if (data.errcode) {
							jp.messager.failure(data.errmsg);
						} else {
							modal.close();
							jp.messager.success('作品修改成功');
							grid.loadData();
						}
						saveBtn.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
					}, 'json');
				});
			}
		});
	}


}
var app = new app();
app.run();