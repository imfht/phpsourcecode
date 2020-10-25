(function(){
	var remindDialog = function(container, options){
		this.container = container;
    this.options = options || {};
	};

	remindDialog.prototype = {
		constructor: remindDialog,
		op: {
			getRemindersList: function(param){
				var url = Ibos.app.url("message/api/alarmsetlist");
        return $.post(url, param, $.noop, "json");
			},
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
			}
		},
		itemTpl: '' + 
			'<li data-param=\'{"eventid": "<%= eventid %>", "node": "<%= node %>", "id": "<%= id %>", "module": "<%= module %>"}\'>' +
        '<p class="mbm"><%= body %></p>' +
        '<span class="mbm">' +
            '<i class="o-remind-label"></i>' + 
            '<span class="mlm"><%= showtime %></span>' + 
        '</span>' +
        '<span style="float: right;">' +
            '<i class="o-remind-persion"></i>' + 
            '<span class="mlm">提醒: <%= reminder %></span>' + 
        '</span>' +
        '<a href="javascript:;" class="o-remind-delete" data-id="<%= id %>"></a>' +
	    '</li>',
		init: function(){
			var _this = this;
			var dialog = Ui.dialog({
				id: 'd_remind',
				title: Ibos.l('REMIND.REMIND_SETTINGS'),
				padding: 0,
				height: '540px',
				ok: false,
				lock: true
			});
			var remindUrl = Ibos.app.getStaticUrl("/js/app/remind/");
			Ibos.statics.load({
				type: 'css',
				url: remindUrl + 'remind.css'
			});
			Ibos.statics.load({ type: "html",
				url: remindUrl + 'remind.html'
			}).done(function(html){
				var param = $.extend(_this.options.getListParam, { offset: 0, pageSize: 50 });
				_this.op.getRemindersList(param).done(function(res){
					if(res.isSuccess){
						var resData = res.data,
							list = _this.formRemindListData(resData.list),
							nodeConfig = resData.nodeConfig,
							config = {
								activeAlarmType: nodeConfig.timeNodes.length > 0 ? '1' : '0',
								timeNodes: nodeConfig.timeNodes,
								mode: 'add'
							};

						_this.config = config;
						dialog.content($.template(html, { list: list, config: config }));
						var $container = dialog.DOM.content.find(_this.container);
						_this.initDialogContentElem();
						_this.bindEvents($container);
						_this.initRemindTime(config);
						_this.initFormPlugins();
					} else {
						Ui.tip(res.msg, "warning");
					}
				});
			}); 
		},
		bindEvents: function($container){
			var _this = this;
			$container.bindEvents({
				'click .toggole-form-btn': function(){
					var $remindForm = $container.find('.remind-mainer--form'),
						$footer = _this.$elem.$dialogContentFooter,
						status = +$(this).data('status');
					status && (_this.config.mode = 'add');  
					$remindForm.animate({'top': (status ? '20px' : '-600px')}, 500);
					$footer.toggle(!status);
					!status && _this.restoreForm();
				},
				'change #time_tpye_select': function(){
					var currNode = $(this).val(),
						diff = _this.$elem.$formTimeDiffSelect.val(),
						status = currNode != '0' ? 1 : 0;

					currNode && _this.caclTimeHandle(currNode, +diff);
					_this.toggleTimeDisplay(status);
				},
				'change #time_diff_select': function(){
					var diff = $(this).val(),
						currNode = _this.$elem.$formTimeTypeSelect.val();
					_this.caclTimeHandle(currNode, +diff);
				},
				'click .save-remind': function(){
					var options = _this.options,
						form = JSON.parse(_this.$elem.$remindForm.serializeJSON()),
						ajaxFormData = $.extend(form, 
							{ paramData: options.paramData, 
								eventId: options.getListParam.eventId,
								title: options.getListParam.title, 
								node: options.getListParam.node,
								module: options.getListParam.module 
							}, { alarmType: (form.timeNode != '0' ? 1 : 0) }),
						tip = _this.initFormValidator(ajaxFormData);
					var mode = _this.config.mode;
					if(tip) {
						Ui.tip(tip, "warning");
					} else {
						var $remindList = $container.find('.remind-list'),
							$remindForm = $container.find('.remind-mainer--form'),
							isAddStatus = mode == 'add';
						if(!isAddStatus) {
							var actvieIndex = _this.config.actvieIndex + 1,
								$activeLi = $remindList.find('li:nth-child(' + actvieIndex +')'),
								currId = $activeLi.data('param').id,
								ajaxFormData = $.extend(ajaxFormData, { id: currId });
						}
						ajaxFormData.timeNode = ajaxFormData.timeNode == '0' ? '' : ajaxFormData.timeNode;
						_this.op[isAddStatus ? 'addRemind' : 'updateRemind'](ajaxFormData).done(function(res) {
							if(res.isSuccess) {
								var data = res.data;
								data.reminder = _this.fomateReminder(data.receiveuids);
								if(isAddStatus) {
									$remindList.prepend($.template(_this.itemTpl, data));
								} else {
									$($.template(_this.itemTpl, data)).replaceAll($activeLi);
								}
								$remindForm.animate({'top': '-600px'}, 500);
								_this.restoreForm();
								Ui.tip(Ibos.l(isAddStatus ? 'REMIND.ADD_REMIND_SUCCESS' : 'REMIND.EDIT_REMIND_SUCCESS'));
							} else {
								Ui.tip(res.msg, 'warning');
							}
						});
					}
				},
				'click .o-remind-delete': function(event){
					event.stopPropagation();
					event.preventDefault();
					var id = $(this).data('id'),
						$currItem = $(this).closest('li');
					Ui.confirm(Ibos.l('REMIND.SUER_DELETE_THIS_REMIND'), function(){
						_this.op.deleteRemind({ ids: id }).done(function(res){
							if(res.isSuccess){
								$currItem.remove();
								Ui.tip(Ibos.l('DELETE_SUCCESS'));
							} else {
								Ui.tip(res.msg, 'warning');
							}
						});
					});
				},
				'click .remind-list li': function(){
					var param = $(this).data('param'),
						index = $(this).index(),
						$remindForm = $container.find('.remind-mainer--form');
					_this.op.getRemindDetail(param).done(function(res){
						if(res.isSuccess){
							var data = res.data.data;
							_this.config.mode = 'edit';
							_this.config.actvieIndex = index;
							_this.resetForm(data);
							_this.$elem.$dialogContentFooter.toggle(false);
							$remindForm.animate({'top': '20px'}, 500);
						} else {
							Ui.tip(res.msg, "warning");
						}
					});
				}
			});			
		},
		initDialogContentElem: function(){
			this.$elem = {
				$remindForm: $("#remind_form"),
				$formReminerInput: $('#reminder'),
				$formReminTimeInput: $("#remind_time"),
				$formReminTimeHiddenInput: $("#remind_time_input"),
				$formReminSendTimeInput: $("#send_time"),
				$formReminContent: $("#remind_content"),
				$formReminText: $("#remind_time_text"),
				$formTimeDiffSelect: $("#time_diff_select"),
				$formTimeTypeSelect: $("#time_tpye_select"),
				$dialogContentFooter: $("#remind_footer")
			};
		},
		initFormPlugins: function(){
			var $elem = this.$elem;
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
		},
		formateUrlParam: function(param){
			var urlParam = '';
			for (var key in param) {
				urlParam += ('&' + key + '=' + param[key]);
			}
			return urlParam;
		},
		formRemindListData: function(list){
			var _this = this;
			list.forEach(function(v, i){
				v.receiveuids = _this.fomateReminder(v.receiveuids);
			});
			return list;
		},
		initFormValidator: function(data){
				var tip; 
				if(!data.sendTime) {
					tip = Ibos.l('REMIND.PLZ_SELECT_REMIND_TIME');
				} else if(!data.receiveUids) {
					tip = Ibos.l('REMIND.PLZ_SELECT_REMIND_USER');
				}
				return tip;
		},
		initRemindTime: function(config){
			var isCustom = config.activeAlarmType == '1';
			isCustom && this.caclTimeHandle(config.timeNodes[0].timeNode, 0);
		},
		caclTimeHandle: function(currNode, diff){
			var nodes = this.config.timeNodes,
				$elem = this.$elem,
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
		},
		toggleTimeDisplay: function(status){
			var $elem = this.$elem, 
				$diffSelect = $elem.$formTimeDiffSelect, 
				$remindTimeInput = $elem.$formReminTimeHiddenInput.closest(".datepicker"),
				$remindTimeText = $elem.$formReminText.closest(".remind-form--item");

			$remindTimeInput[status ? 'addClass' : 'removeClass']("dn");
			$remindTimeText[status ? 'removeClass' : 'addClass']("dn");
			$diffSelect[status ? 'removeClass' : 'addClass']("dn");
			!status && $elem.$formReminTimeHiddenInput.val('');
			!status && $elem.$formReminSendTimeInput.val('');
		},
		restoreForm: function(){
			var isCustom = this.config.activeAlarmType == '1',
				$elem = this.$elem,
				$remindTimeInput = $elem.$formReminTimeHiddenInput,
				$reminder = $elem.$formReminerInput,
				reminder = $reminder.val().split(','),
				$content = $elem.$formReminContent;
			$remindTimeInput.val('');
			$reminder.userSelect("setValue", reminder, false);
			$reminder.userSelect("setValue", ['u_' + G.uid], true);
			$content.val('');
			if(isCustom) {
				$elem.$formReminTimeInput.addClass("dn");
				$elem.$formTimeDiffSelect.removeClass("dn").find("option:first-child").attr('selected', true);
				$elem.$formTimeTypeSelect.removeClass("dn").find("option:first-child").attr('selected', true);
				this.initRemindTime(this.config);
			} else {
				$elem.$formReminSendTimeInput.val('');
			}
		},
		 fomateReminder: function(data){
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
		resetForm: function(data){
			var isCustom = data.alarmtype == '1',
				$elem = this.$elem,
				$reminder = $elem.$formReminerInput,
				reminder = data.receiveuids.split(','),
				$content = $elem.$formReminContent,
				mode = this.config.mode;
			$reminder.userSelect("setValue", reminder, mode == 'edit');
			$content.val(data.body);
			if(isCustom){
				$elem.$formTimeDiffSelect.find("option[value=" + data.diffetime + "]").attr('selected', true);
				this.caclTimeHandle(data.timenode, +data.diffetime);
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
	};

	Ibos.remindDialog = remindDialog;
})();
