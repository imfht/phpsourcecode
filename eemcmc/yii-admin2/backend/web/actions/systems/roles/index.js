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
		readUrl: '/api/systems/roles',
		page: 1,
		page_size: 15,
		fixedLeftWidth: '50%',
		fixedRightWidth: 270,
		columns: [
			{width: 80, text: '角色名称', type: 'string', flex: false, colClass: '', sort: false, name: 'description'},
			{width: 100, text: '角色唯一标识', type: 'date', flex: false, colClass: '', sort: false, name: 'name'},
			{width: 250, text: '操作', type: 'action', flex: false, colClass: '', sort: false, render: function (value, rawData) {
					if (rawData.name == 'admin') {
						return [];
					}
					var items = [{
							iconClass: 'icon-edit',
							text: '修改',
							handler: self.actionUpdate
						}, {
							iconClass: 'icon-remove',
							text: '删除',
							handler: self.actionDelete
						}, {
							iconClass: 'icon-lock',
							text: '权限维护',
							handler: function (rawData) {
								window.location.href = '/systems/roles/permissions?name=' + rawData.name;
							}
						}, {
							iconClass: 'icon-group',
							text: '成员维护',
							handler: function (rawData) {
							}
						}];
					return items;
				}
			}
		],
	};

	/**
	 * 运行
	 */
	this.run = function () {
		$('.addrole').click(self.actionCreate);
		grid = new jp.grider(config);
	}

	/**
	 * 删除角色
	 * @returns {undefined}
	 */
	this.actionDelete = function (data, button) {
		var params = {
			name: data.name
		};
		var button = $(button);
		button.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
		$.post('/api/systems/roles/delete', params, function (data) {
			if (data.errcode) {
				jp.messager.failure(data.errmsg);
			} else {
				jp.messager.success('创建成功');
				grid.loadData();
			}
			button.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
		}, 'json');
	}

	/**
	 * 创建角色
	 * @returns {undefined}
	 */
	this.actionCreate = function () {
		//显示
		var modal = new ModalTrigger({custom: $('#create').html()});
		modal.show({
			title: '创建角色',
			shown: function (event)
			{
				var form = $(event.target).find('.modal-content');
				//初始化状态值
				//绑定保存按钮事件
				var saveBtn = form.find('.save');
				saveBtn.click(function () {
					//设置按钮状态
					saveBtn.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
					//设置参数
					var params = {
						description: form.find('.description').val(),
						name: form.find('.name').val(),
					};
					//修改
					$.post('/api/systems/roles/create', params, function (data) {
						if (data.errcode) {
							jp.messager.failure(data.errmsg);
						} else {
							modal.close();
							jp.messager.success('创建成功');
							grid.loadData();
						}
						saveBtn.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
					}, 'json');
				});
			}
		});
	}

	/**
	 * 更新信息
	 * @returns {undefined}
	 */
	this.actionUpdate = function (rawData) {
		//显示
		var modal = new ModalTrigger({custom: $('#update').html()});
		modal.show({
			title: '更新角色',
			shown: function (event)
			{
				var form = $(event.target).find('.modal-content');
				//初始化状态值
				form.find('.description').val(rawData.description);
				form.find('.name').val(rawData.name);
				//绑定保存按钮事件
				var saveBtn = form.find('.save');
				saveBtn.click(function () {
					//设置按钮状态
					saveBtn.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
					//设置参数
					var params = {
						description: form.find('.description').val(),
						name: form.find('.name').val(),
					};
					//修改
					$.post('/api/systems/roles/update', params, function (data) {
						if (data.errcode) {
							jp.messager.failure(data.errmsg);
						} else {
							modal.close();
							jp.messager.success('更新成功');
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