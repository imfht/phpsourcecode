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
		readUrl: '/api/systems/releases',
		page: 1,
		page_size: 15,
		fixedLeftWidth: '50%',
		fixedRightWidth: 270,
		columns: [
			{width: 30, text: '#', type: 'number', flex: false, colClass: 'text-center', sort: true, name: 'id'},
			{width: 80, text: '版本名称', type: 'string', flex: false, colClass: '', sort: false, name: 'name'},
			{width: 80, text: '客户端类型', type: 'string', flex: false, colClass: '', sort: true, name: 'client_type', render: function (value) {
					if (value == 1)
					{
						return 'IOS';
					}
					if (value == 2)
					{
						return 'Android';
					}
				}},
			{width: 80, text: '客户端版本', type: 'string', flex: false, colClass: '', sort: false, name: 'client_version'},
			{width: 80, text: '版本状态', type: 'date', flex: false, colClass: '', sort: false, name: 'dostatus', render: function (value) {
					if (value == 1)
					{
						return '<label class="label label-info">开发中</label>';
					}
					if (value == 2)
					{
						return '<label class="label label-info">测试中</label>';
					}
					if (value == 3)
					{
						return '<label class="label label-info">预发布</label>';
					}
					if (value == 4)
					{
						return '<label class="label label-success">已发布</label>';
					}
					if (value == 5)
					{
						return '<label class="label label-danger">已废弃</label>';
					}
				}},
			{width: 80, text: '接口网关', type: 'string', flex: false, colClass: '', sort: false, name: 'api_gateway'},
			{width: 80, text: '接口版本', type: 'string', flex: false, colClass: '', sort: false, name: 'api_version'},
			{width: 80, text: '下载地址', type: 'string', flex: false, colClass: '', sort: false, name: 'download_url'},
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
	 * 删除版本
	 * @returns {undefined}
	 */
	this.actionDelete = function (data, button) {
		var params = {
			id: data.id
		};
		var button = $(button);
		button.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
		$.post('/api/systems/releases/delete', params, function (data) {
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
	 * 创建版本
	 * @returns {undefined}
	 */
	this.actionCreate = function () {
		//显示
		var modal = new ModalTrigger({custom: $('#create').html()});
		modal.show({
			title: '创建版本',
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
						name: form.find('.name').val(),
						changes: form.find('.changes').val(),
						client_type: form.find('.client_type').val(),
						dostatus: form.find('.dostatus').val(),
						client_version: form.find('.client_version').val(),
						api_gateway: form.find('.api_gateway').val(),
						api_version: form.find('.api_version').val(),
						download_url: form.find('.download_url').val(),
					};
					//修改
					$.post('/api/systems/releases/create', params, function (data) {
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
		var modal = new ModalTrigger({custom: $('#create').html()});
		modal.show({
			title: '更新版本',
			shown: function (event)
			{
				var form = $(event.target).find('.modal-content');
				//初始化状态值
				form.find('.name').val(rawData.name);
				form.find('.changes').val(rawData.changes);
				form.find(".client_type[value='" + rawData.client_type + "']").attr('checked', 'true');
				form.find('.client_version').val(rawData.client_version);
				form.find('.dostatus').val(rawData.dostatus);
				form.find('.api_gateway').val(rawData.api_gateway);
				form.find('.api_version').val(rawData.api_version);
				form.find('.download_url').val(rawData.download_url);
				//绑定保存按钮事件
				var saveBtn = form.find('.save');
				saveBtn.click(function () {
					//设置按钮状态
					saveBtn.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
					//设置参数
					var params = {
						id: rawData.id,
						name: form.find('.name').val(),
						changes: form.find('.changes').val(),
						client_type: form.find('.client_type:checked').val(),
						client_version: form.find('.client_version').val(),
						dostatus: form.find('.dostatus').val(),
						api_gateway: form.find('.api_gateway').val(),
						api_version: form.find('.api_version').val(),
						download_url: form.find('.download_url').val(),
					};
					//修改
					$.post('/api/systems/releases/update', params, function (data) {
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