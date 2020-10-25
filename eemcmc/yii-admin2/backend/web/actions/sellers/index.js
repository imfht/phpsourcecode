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
		readUrl: '/api/sellers',
		page: 1,
		page_size: 15,
		fixedLeftWidth: '50%',
		fixedRightWidth: 220,
		columns: [
			{width: 60, text: '#', type: 'number', flex: false, colClass: 'text-center', sort: true, name: 'uid'},
			{width: 100, text: '昵称', type: 'date', flex: false, colClass: '', sort: false, name: 'nickname'},
			{width: 100, text: '真实姓名', type: 'date', flex: false, colClass: '', sort: false, name: 'realname'},
			{width: 80, text: '手机', type: 'string', flex: false, colClass: '', sort: false, name: 'mobile_number'},
			{width: 100, text: '商家证件照', type: 'string', flex: false, colClass: '', sort: false, name: 'idphoto', render: function (value) {
					return '<img src="' + value + '" width="100" height="30" />';
				}},
			{width: 200, text: '商家描述', type: 'string', flex: true, colClass: '', sort: false, name: 'intro'},
			{width: 80, text: '商家状态', type: 'string', flex: true, colClass: '', sort: false, name: 'status', render: function (value) {
					if (value == 1) {
						return '<label class="label label-info">待审核</label>';
					} else if (value == 2) {
						return '<label class="label label-success">已通过</label>';
					}
				}},
			{width: 80, text: '开户行', type: 'string', flex: true, colClass: '', sort: false, name: 'bank'},
			{width: 150, text: '银行帐号', type: 'string', flex: true, colClass: '', sort: false, name: 'bank_account'},
			{width: 60, text: '开户名称', type: 'string', flex: true, colClass: '', sort: false, name: 'bank_realname'},
			{width: 100, text: '从业时间', type: 'string', flex: true, colClass: '', sort: false, name: 'working_time', render: function (value) {
					return value + '年';
				}},
			{width: 100, text: '商家订单数', type: 'string', flex: true, colClass: '', sort: false, name: 'order_count', render: function (value) {
					return '<label class="label label-badge label-primary">' + value + '</label>';
				}},
//			{width: 100, text: '商家接单状态', type: 'string', flex: true, colClass: '', sort: false, name: ''},
			{width: 220, text: '操作', type: 'action', flex: false, colClass: '', sort: false, render: function (value, data) {
					var items = [{
							text: '登录此商家',
							handler: function (data) {
								jp.redirect(data.auth_key);
							}
						}, {
							more: true,
							show: data.status == 1,
							text: '通过审核',
							handler: self.actionPass
						}, {
							text: '修改商家',
							handler: self.actionUpdate

						}, {
							more: true,
							text: '发送消息',
							handler: function (data) {
								jp.messager.send(data.uid);
							}
						}, {
							more: true,
							text: '摄影师详情',
							handler: self.actionDetails
						}, {
							more: true,
							text: '摄影作品管理',
							handler: function () {
								var url = '/sellers/photographs?uid=' + data.uid;
								jp.redirectUrl(url, false);
							}
						}
					];
					return items;
				}
			}
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
			title: '修改商家信息',
			shown: function (event)
			{
				var form = $(event.target).find('.modal-content');
				//初始化状态值
				form.find('.nickname').val(rawData.nickname);
				form.find('.mobile_number').val(rawData.mobile_number);
				form.find('.is_auth').prop('checked', this.checked);
				form.find(".is_auth[value='" + rawData.is_auth + "']").attr('checked', 'true');
				
				form.find('.realname').val(rawData.realname);
				form.find('.bank').val(rawData.bank);
				form.find('.bank_account').val(rawData.bank_account);
				form.find('.bank_realname').val(rawData.bank_realname);
				//绑定保存按钮事件
				var saveBtn = form.find('.save');
				saveBtn.click(function () {
					//设置按钮状态
					saveBtn.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
					//设置参数
					var params = {
						uid: rawData.uid,
						nickname: form.find('.nickname').val(),
						mobile_number: form.find('.mobile_number').val(),
						password_hash: form.find('.password_hash').val(),
						is_auth: form.find('.is_auth:checked').val(),
						
						realname: form.find('.realname').val(),
						bank: form.find('.bank').val(),
						bank_account: form.find('.bank_account').val(),
						bank_realname: form.find('.bank_realname').val(),
					};
					//修改
					$.post('/api/sellers/update', params, function (data) {
						if (data.errcode) {
							jp.messager.failure(data.errmsg);
						} else {
							modal.close();
							jp.messager.success('商家修改成功');
							grid.loadData();
						}
						saveBtn.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
					}, 'json');
				});
			}
		});
	}

	/**
	 * 审核通过
	 */
	this.actionPass = function (rawData) {
		var params = {
			uid: rawData.uid,
		};
		$.post('/api/sellers/pass', params, function (data) {
			if (data.errcode) {
				jp.messager.failure(data.errmsg);
			} else {
				jp.messager.success('审核通过');
				grid.loadData();
			}
		}, 'json');
	}


	/**
	 * 摄影师详情
	 * @returns {undefined}
	 */
	this.actionDetails = function (rawData) {
		//显示
		var modal = new ModalTrigger({backdrop: 'static', custom: $('#seller-details').html()});
		modal.show({
			title: '摄影师详情',
			shown: function (event)
			{
				var form = $(event.target).find('.modal-content');
				//初始化值
				//form.find(".is_auth[value='" + rawData.is_auth + "']").attr('checked', 'true');
				form.find('a.s-lookit').attr('href','http://jiapai.cn/photog/profile?uid=' + rawData.uid);

				form.find('.realname').val(rawData.realname);
				form.find('.working_time').val(rawData.working_time);
				form.find('.intro').val(rawData.intro);
				form.find('.temp_intro').val(rawData.temp_intro);
				form.find('.get_prizes').val(rawData.get_prizes);
				form.find('.resume').val(rawData.resume);
				form.find('.goodat_styles').val(rawData.goodat_styles);
				//绑定保存按钮事件
				var saveBtn = form.find('.save');
				saveBtn.click(function () {
					
					//设置按钮状态
					saveBtn.toggleClass('disabled').find('i').toggleClass('icon-spinner icon-spin');
					//设置参数
					var params = {
						uid: rawData.uid,
						
						working_time: form.find('.working_time').val(),
						intro: form.find('.intro').val(),
						temp_intro: form.find('.temp_intro').val(),
						get_prizes: form.find('.get_prizes').val(),
						resume: form.find('.resume').val(),
						goodat_styles: form.find('.goodat_styles').val(),
					};
					//修改
					$.post('/api/sellers/seller-details', params, function (data) {
						if (data.errcode) {
							jp.messager.failure(data.errmsg);
						} else {
							modal.close();
							jp.messager.success('摄影师详情修改成功');
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