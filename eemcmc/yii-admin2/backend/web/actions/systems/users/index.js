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
		readUrl: '/api/systems/users',
		page: 1,
		page_size: 15,
		fixedLeftWidth: '50%',
		fixedRightWidth: 270,
		columns: [
			{width: 30, text: '#', type: 'number', flex: false, colClass: 'text-center', sort: false, name: 'id'},
			{width: 80, text: '管理员', type: 'string', flex: false, colClass: '', sort: false, name: 'username'},
			{width: 80, text: '真实姓名', type: 'string', flex: false, colClass: '', sort: false, name: 'realname'},
			{width: 200, text: '角色', type: 'string', flex: false, colClass: '', sort: false, name: 'role_names'},
			{width: 100, text: '状态', type: 'date', flex: false, colClass: '', sort: false, name: 'status', render: function (value) {
					if (value == 1)
					{
						return '<label class="label label-success">正常</label>';
					}
					else
					{
						return '<label class="label label-danger">关闭</label>';
					}
				}},
			{width: 250, text: '操作', type: 'action', flex: false, colClass: '', sort: false, render: function () {
					var items = [{
							iconClass: 'icon-edit',
							text: '修改',
							handler: self.actionUpdate
						}, {
							iconClass: 'icon-remove',
							text: '删除',
							handler: self.actionDelete
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
	 * 删除管理员
	 * @returns {undefined}
	 */
	this.actionDelete = function (data, button) {
		var params = {
			id: data.id
		};
		var button = $(button);
		button.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
		$.post('/api/systems/users/delete', params, function (data) {
			if (data.errcode) {
				jp.messager.failure(data.errmsg);
			} else {
				jp.messager.success('删除成功');
				grid.loadData();
			}
			button.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
		}, 'json');
	}

	/**
	 * 创建管理员
	 * @returns {undefined}
	 */
	this.actionCreate = function () {
		//显示
		var modal = new ModalTrigger({custom: $('#create').html()});
		modal.show({
			title: '创建管理员',
			shown: function (event)
			{
				$(event.target).find('.chosen-select').chosen();
				var form = $(event.target).find('.modal-content');
				//绑定保存按钮事件
				var buuton = form.find('.save');
				buuton.click(function () {
					//设置按钮状态
					buuton.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
					//设置参数
					var params = {
						realname: form.find('.realname').val(),
						password: form.find('.password').val(),
						username: form.find('.username').val(),
						roles: form.find('.roles').val(),
					};
					//修改
					$.post('/api/systems/users/create', params, function (data) {
						if (data.errcode) {
							jp.messager.failure(data.errmsg);
						} else {
							modal.close();
							jp.messager.success('创建成功');
							grid.loadData();
						}
						buuton.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
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
			title: '更新管理员',
			shown: function (event)
			{
				var form = $(event.target).find('.modal-content');
				//初始化状态值
				form.find('.realname').val(rawData.realname);
				form.find('.username').val(rawData.username);
				$.each(rawData.roles, function (key, val) {
					form.find('.roles option[value="' + key + '"]').attr('selected', 'selected');
				});
				$(event.target).find('.chosen-select').chosen();
				//绑定保存按钮事件
				var saveBtn = form.find('.save');
				saveBtn.click(function () {
					//设置按钮状态
					saveBtn.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
					//设置参数
					var params = {
						id: rawData.id,
						realname: form.find('.realname').val(),
						password: form.find('.password').val(),
						username: form.find('.username').val(),
						roles: form.find('.roles').val(),
					};
					//修改
					$.post('/api/systems/users/update', params, function (data) {
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