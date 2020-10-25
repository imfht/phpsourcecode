var MessageManager = function (elems, options) {
	this.elems = elems || {};
	this.options = options;
	this.config = {
		isLoading: false,
		isPaginationInit: false,
		listData: null,
		listCount: 0,
		activeModule: '',
		activePage: 0
	};
	this._bindEvents();
};

MessageManager.prototype = {
	constructor: MessageManager,
	op: {
		addRemind: function(param){
			var url = Ibos.app.url("message/api/alarmadd");
			return $.post(url, param, $.noop, "json");
		},
		updateRemind: function(param){
			var url = Ibos.app.url("message/api/alarmedit");
			return $.post(url, param, $.noop, "json");
		},
		deleteRemind: function(param){
			var url = Ibos.app.url('message/api/alarmdel');
			return $.post(url, param, $.noop, "json");
		},
		getRemindDetail: function(param){
			var url = Ibos.app.url('message/api/alarmdetail');
			return $.get(url, param, $.noop, "json");
		},
		getRemindList: function(param){
			var url = Ibos.app.url('message/api/alarmlist');
			return $.get(url, param, $.noop, "json");
		}
	},
	init: function(){
		this._getListData({ offset: 0 });
		this._initPagination($(this.elems.page));
	},
	_bindEvents: function(){
		var _this = this,
			elems = _this.elems,
			$search = $(elems.search),
			$page = $(elems.page),
			$mainer = $(elems.mainer),
			$moduleList = $(elems.moduleList),
			$listMainer = $(elems.listMainer);

		$search.search(function() {
			var param = {
				module: _this.config.activeModule,
				search: $search.val(),
				offset: 0,
			};
      _this._refresh(param, $page);
    });

		$mainer.bindEvents({
			'click .multi-btn': function(){
				_this._utils.multiAccess(function(ids) {
					_this.op.deleteRemind({ ids: ids }).done(function(res){
						if(res.isSuccess) {
							var param = {
								module: _this.config.activeModule,
								search: $search.val()
							};
							Ui.tip(Ibos.l('DELETE_SUCCESS'));
							_this._refresh(param, $page);
						} else {
							Ui.tip(res.msg, "warning");
						}
					});
				});
			},
			'click .add-btn': function(){
				var model = 'add',
					config = {
						activeAlarmType: '0',
						timeNodes: []
					},
					data = {
						id: '',
						eventId: '',
						module: 'message',
						node: 'normal_alarm_notily',
						title: '普通提醒'
					};
				_this._dialog(model, config, { data: data });
			},
			'click .module-query--toggle': function(){
				$mainer.find('.module-query').slideToggle();
                $(this).toggleClass('module-query--toggle__down');
			}
		});

		$moduleList.bindEvents({
			'click a': function() {
				var module = $(this).data('param').value,
					param = {
						module: module,
						search: $search.val(),
						offset: 0,
					};
				_this.config.activeModule = module;
				_this._resetActiveModuleStyle($moduleList, $(this));
				_this._refresh(param, $page);
			}
		});

		$listMainer.bindEvents({
			'click li': function() {
				var $label =  $(this).find('input[type="checkbox"]'),
					isChecked = $label.prop('checked');
				$label.label(isChecked ?  'uncheck' : 'check');
			},
			'click .o-trash': function() {
				var id = $(this).data('param').id;
				Ui.confirm(Ibos.l("REMIND.SUER_DELETE_THIS_REMIND"), function() {
					_this.op.deleteRemind({ ids: id }).done(function(res){
						if(res.isSuccess) {
							var param = {
								module: _this.config.activeModule,
								search: $search.val()
							};
							Ui.tip(Ibos.l('DELETE_SUCCESS'));
							_this._refresh(param, $page);
						} else {
							Ui.tip(res.msg, "warning");
						}
					});
				});
			},
			'click .o-edit': function() {
				var param = $(this).data('param');
				_this.op.getRemindDetail(param).done(function(res) {
					if(res.isSuccess) {
						var resData = res.data,
							model = 'edit',
							config = {
								activeAlarmType: resData.data.alarmtype,
								timeNodes: resData.nodeConfig.timeNodes
							};
						_this._dialog(model, config, resData);

					} else {
						Ui.tip(res.msg, "warning");
					}
				});
			}
		});
	},
	_utils: {
		// 批量操作
    multiAccess: function(callback){
        var ids = U.getCheckedValue("remind", "#list_mainer");
        if (!ids) {
            Ui.tip("@SELECT_AT_LEAST_ONE_ITEM", "warning");
        } else {
            callback && callback(ids);
        }
    },
	},
	_getListData: function(param){
		$.ajaxSetup({
        async: false
    });
		var _this = this, 
			ajaxPram = $.extend(param, {
				module: param.module || '',
				search: param.search || '',
				eventId: param.eventId || '',
				offset: (param.offset || 0) * 10,
				pageSize: 10
			});
		if(!this.config.isisLoading) {
			_this.config.isLoading = true;
			_this.config.listData = null;
			$(_this.elems.listMainer).showModal();
			this.op.getRemindList(ajaxPram).done(function(res) {
				if(res.isSuccess) {
					_this.config.isLoading = false;
					_this.config.listData = res.data.list;
					_this.config.listCount = +res.data.count;
				}else {
					Ui.tip(res.msg, "warning");
				}
			});
		}
	},
	_renderList: function(listData){
		var _this = this, 
			$listMainer = $(this.elems.listMainer);
		if(listData && listData.length) {
			var resHtml = '';
			$.each(listData, function(i, data){
				var iconUrl = Ibos.app.getAssetUrl(data.module, '/image/icon.png');
				data.iconUrl = iconUrl;
				data.receiveuids = _this._fomateReminder(data.receiveuids);
				data.url = data.module == 'message' ? 'javascript:;' : data.url;
				resHtml += $.template("remind_tpl", data);
			});
			$listMainer.empty().html(resHtml).hideModal();
			$listMainer.find("[data-toggle='tooltip']").tooltip();
			$listMainer.find("[type='checkbox']").label();
		} else {
			$listMainer.empty().html('<div class="no-data-tip"></div>');
		}
	},
	_initPagination: function($page) {
		var _this = this, 
			_settings = {
				items_per_page: 9,
	      num_display_entries: 5,
	      prev_text: false,
	      next_text: false,
	      renderer: "ibosRenderer",
	      allow_jump: true,
	      callback: function(page, elem) {
	        if(_this.config.isPaginationInit) {
	        	var param = { 
	        		offset: page, 
	        		search: $(_this.elems.search).val(), 
	        		module: _this.config.activeModule 
	        	};
	          _this._getListData(param);
	        }
	        _this._renderList(_this.config.listData);
	        _this.config.isPaginationInit = true;
	        _this.config.activePage = page;
	        $.ajaxSetup({
            async: true
          });
	      }
			};

		if (!$.fn.pagination) {
	    $.getScript(Ibos.app.getStaticUrl("/js/lib/jquery.pagination.js"))
        .done(function() {
          $page.pagination(_this.config.listCount, _settings);
        });
		} else {
		  $page.pagination(_this.config.listCount, _settings);
		}
	},
	_refresh: function(ajaxParam, $page) {
		this.config.isPaginationInit = false;
    this._getListData(ajaxParam);
    this._initPagination($page);
	},
	_resetActiveModuleStyle: function($moduleList, $activeModule){
		$moduleList.find('li').removeClass('active');
		$activeModule.closest('li').addClass('active');
	},
	_fomateReminder: function(data){
		var reminder = data.split(','),
			reminderText = '';
		reminder.forEach(function(v, i){
			if(v == 'c_0') {
				var text = G.shortname;
			} else {
				var prefix = v.split('_')[0],
					type = prefix == 'u' ? 'user' : (prefix == 'd' ? 'department' : 'position'),
					text = Ibos.data.getItem(v)[type][v].text;
			}
			reminderText += (text + ',');
		});
		return reminderText.slice(0, reminderText.length - 1);
	},
	_dialog: function(model, config, data){
		var _this = this;
		var dialog = Ui.dialog({
			id: 'd_remind',
			title: Ibos.l(model == 'add' ? 'REMIND.ADD_REMIND' : 'REMIND.EDIT_REMIND'),
			padding: 0,
			ok: false,
			lock: true
		});

		var remindUrl = Ibos.app.getAssetUrl("message");
		Ibos.statics.load({
			type: 'css',
			url: remindUrl + '/js/remindDialog/remind.css'
		});
		Ibos.statics.load({ type: "html",
			url: remindUrl + '/js/remindDialog/remind.html'
		}).done(function(html){
			dialog.content($.template(html, { config: config }));
			initDialogContentElem();
			bindEvents(dialog.DOM.content.find('.remind-form'));
			initFormPlugins();
			model == 'edit' && resetForm(data);
		}); 

		function initDialogContentElem() {
			_this.$dialogElems = {
				$remindForm: $("#remind_form"),
				$formReminerInput: $('#reminder'),
				$formReminTimeInput: $("#remind_time"),
				$formReminTimeHiddenInput: $("#remind_time_input"),
				$formReminSendTimeInput: $("#send_time"),
				$formReminContent: $("#remind_content"),
				$formReminText: $("#remind_time_text"),
				$formTimeDiffSelect: $("#time_diff_select"),
				$formTimeTypeSelect: $("#time_tpye_select")
			};
		}

		function bindEvents($form) {
			$form.bindEvents({
				'click .cancel-btn': function(){
					Ui.getDialog('d_remind').close();
				},
				'click .save-btn': function(){
					var formData = JSON.parse($form.serializeJSON()),
						resData = data.data,
						ajaxParam = $.extend(formData, {
							id: resData.id,
							eventId: resData.eventid,
							module: resData.module,
							node: resData.node,
							title: resData.title,
							alarmType: (formData.timeNode != '0' ? 1 : 0),
							timeNode: formData.timeNode == '0' ? '' : formData.timeNode
						}),
						isValid = initFormValidator(ajaxParam),
						isAddMode = model == 'add';
					if(!isValid) {
						_this.op[isAddMode ? 'addRemind' : 'updateRemind'](ajaxParam).done(function(res){
							if(res.isSuccess) {
								var param = {
									module: _this.config.activeModule,
									search: $(_this.elems.search).val(),
									offset: (isAddMode ? 0 : _this.config.activePage),
								};
	    					_this._refresh(param, $(_this.elems.page));
								Ui.tip(Ibos.l('REMIND.EDIT_REMIND_SUCCESS'));
								Ui.getDialog('d_remind').close();
							} else {
								Ui.tip(res.msg, "warning");
							}
						});
					} else {
						Ui.tip(isValid, "warning");
					}
				},
				'change #time_tpye_select': function(){
					var currNode = $(this).val(),
						diff = _this.$dialogElems.$formTimeDiffSelect.val(),
						status = currNode != '0' ? 1 : 0;

					currNode && caclTimeHandle(config.timeNodes, currNode, +diff);
					toggleTimeDisplay(status);
				},
				'change #time_diff_select': function(){
					var diff = $(this).val(),
						currNode = _this.$dialogElems.$formTimeTypeSelect.val();
					caclTimeHandle(config.timeNodes, currNode, +diff);
				},
			});
		}

		function initFormPlugins() {
			var $elem = _this.$dialogElems;
			$elem.$formReminerInput.userSelect({
	        data: Ibos.data.get(),
	        type: "all"
	    });
	    $elem.$formReminTimeInput.datepicker({
	    	format: "yyyy-mm-dd hh:ii",
	    	pickTime: true,
	    	pickSeconds: false,
	    	startDate: new Date
	    }).on("hide", function(){
	        $elem.$formReminTimeHiddenInput.trigger("blur");
	    }).on("changeDate", function(evt) {
	        var time = Math.floor(new Date(evt.localDate).getTime()/1000);
	        $elem.$formReminSendTimeInput.val(time);
	    });
		}

		function resetForm(resData){
			var data =  resData.data,
				timeNodes = resData.nodeConfig.timeNodes,
				isCustom = data.alarmtype == '1',
				$elem = _this.$dialogElems,
				$reminder = $elem.$formReminerInput,
				reminder = data.receiveuids.split(','),
				$content = $elem.$formReminContent;
			$reminder.userSelect("setValue", reminder, true);
			$content.val(data.body);
			if(isCustom){
				$elem.$formTimeDiffSelect.find("option[value=" + data.diffetime + "]").attr('selected', true);
				caclTimeHandle(timeNodes, data.timenode, +data.diffetime);
			} else {
				$elem.$formReminTimeHiddenInput.val(Ibos.date.format(data.stime*1000, 'yyyy-mm-dd hh:ii'));
				$elem.$formReminSendTimeInput.val(data.stime);
				$elem.$formReminText.closest(".remind-form--item").addClass("dn");
			}
			$("#time_tpye_select option[value=" + (data.timenode || 0) + "]").attr('selected', true);
			$elem.$formTimeDiffSelect.toggleClass('dn', !isCustom);
			$elem.$formTimeTypeSelect.toggleClass('dn', !isCustom);
			$elem.$formReminTimeInput.toggleClass('dn', isCustom);
		}

		function caclTimeHandle(timeNodes, currNode, diff){
			var nodes = timeNodes,
				$elem = _this.$dialogElems,
				$remindTimeInput = $elem.$formReminTimeHiddenInput,
				$remindTimeText = $elem.$formReminText,
				$ajaxFormtime = $elem.$formReminSendTimeInput,
				time;
			nodes.forEach(function(node, i) {
				if(node.timeNode == currNode) {
					time = (node.eventTime + diff * 60) * 1000;
					$ajaxFormtime.val(Math.floor(time/1000));
					$remindTimeInput.val(Ibos.date.format(time, 'yyyy-mm-dd hh:ii'));
					$remindTimeText.text(Ibos.date.format(time, 'yyyy年mm月dd日 hh:ii'))
						.closest(".remind-form--item").removeClass("dn");
				}
			});
		}

		function toggleTimeDisplay(status){
			var $elem = _this.$dialogElems, 
				$diffSelect = $elem.$formTimeDiffSelect, 
				$remindTimeInput = $elem.$formReminTimeHiddenInput.closest(".datepicker"),
				$remindTimeText = $elem.$formReminText.closest(".remind-form--item");

			$remindTimeInput[status ? 'addClass' : 'removeClass']("dn");
			$remindTimeText[status ? 'removeClass' : 'addClass']("dn");
			$diffSelect[status ? 'removeClass' : 'addClass']("dn");
			!status && $elem.$formReminTimeHiddenInput.val('');
			!status && $elem.$formReminSendTimeInput.val('');
		}

		function initFormValidator(data){
			var tip; 
			if(!data.sendTime) {
				tip = Ibos.l('REMIND.PLZ_SELECT_REMIND_TIME');
			} else if(!data.receiveUids) {
				tip = Ibos.l('REMIND.PLZ_SELECT_REMIND_USER');
			}
			return tip;
		}
	}
};

$(function(){
	var mainer = new MessageManager({
		mainer: "#mainer",
		listMainer: '#list_mainer',
		moduleList: '#module_list',
		search: '#notify_manage_search',
		page: '#pagination',
	});
	mainer.init();
});