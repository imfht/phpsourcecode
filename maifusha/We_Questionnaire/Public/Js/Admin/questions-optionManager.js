optionManager = {
	questionnaireType: '', //考试卷还是调研卷

	/* 选项管理器应用启动 */
	start: function(){
		this.questionnaireType = $('#questionnaire_type').val();

		this.updateAlterOption();

		this.delegateEvents();
	},

	/* 处理一些事件委托事务 */
	delegateEvents: function(){
		var self = this;

		/* 点击选项下拉菜单 */
		$(document).delegate('#question-form .alter-option-list > li > a', 'click', function(){
			self.addOption( $(this).attr('type') );
			self.updateAlterOption();
		});

		/* 提交问题 */
		$(document).delegate('#submit', 'click', function(){
			var state1 = true;
			var state2 = true;

			state1 = self.collectOptions();
			if(self.questionnaireType=='exam') //考试卷才进行标准答案的收集
				state2 = self.collectStandard();

			if(state1 && state2) //选项和标准答案都收集成功则提交表单
				$('#question-form').submit();
		});

		/* 删除选项 */
		$(document).delegate('.option-del', 'click', function(){
			/* 先复位全部选项可用 */
			$('#question-form .alter-option-list > li > a').removeAttr('disabled');
			$(document).delegate('#question-form .alter-option-list > li > a[disabled]', 'click', function(){
				self.addOption( $(this).attr('type') );
				self.updateAlterOption();
			});

			$(this).parents('.option-item').remove(); //删除选项

			self.updateAlterOption(); //更新可用选项
		});
	},

	/* 页面添加指定类型的选项 */
	addOption: function(type){
		$('#question-form .option-list').append( OptionTemplate.get(type) );
	},

	/* 更新可添加的选项 */
	updateAlterOption: function(){
		var self = this;
		var types = [];

		$('.option-list .option-item').each(function(){
			var type = $(this).attr('class').replace('option-item ', '').replace('_option', '');

			if( types.indexOf(type) == -1 )
				types.push(type);
		});

		types.forEach(function(type){
			self.disableOptAgainestCase(type);
		});
	},

	/* 禁用与type类型选项冲突的选项 */
	disableOptAgainestCase: function(type){
		$options = $('#question-form .alter-option-list > li > a');
		
		switch(type){
			case 'radio':
							$options.not('[type=radio]').not('[type=radio_othertext]').attr('disabled', 'disabled').unbind('click');
							break;
			case 'checkbox':
							$options.not('[type=checkbox]').not('[type=checkbox_othertext]').attr('disabled', 'disabled').unbind('click');
							break;
			case 'text':
							$options.attr('disabled', 'disabled').unbind('click');
							break;
			case 'radio_othertext':
							$options.not('[type=radio]').attr('disabled', 'disabled').unbind('click');
							break;
			case 'checkbox_othertext':
							$options.not('[type=checkbox]').attr('disabled', 'disabled').unbind('click');
							break;
		}
	},

	/* 从配置好的选项上收集相应的json结构的选项信息 */
	collectOptions: function(){
		if( !this.validateOptions() ) //验证是否输入好选项文本
			return false;

		var options = {};

		/* 循环选项，收集选项信息 */
		$('#question-form .option-list .option-item').each(function(i){
			$this = $(this);
			var type = $this.attr('class').split(' ')[1].replace('_option', '');
			var text = (type!='radio' && type!='checkbox')? '':$this.find('input.form-control').val();
			options[i] = {'type':type, 'text':text};
		});

		//序列化选项信息，写入到隐藏域#options中
		var optionsVal = JSON.stringify(options);
		$('#options').val( optionsVal );

		return true;
	},

	/* 从配置好的选项上收集标准答案信息 */
	collectStandard: function(){
		if( !this.validateStandard() ) //验证是否配置好标准答案
			return false;

		var standard = [];

		//循环选项，收集标准答案信息
		$('#question-form .option-list .option-item').each(function(i){
			$this = $(this);
			var type = $this.attr('class').split(' ')[1].replace('_option', '');

			switch(type){
				case 'radio':
				case 'checkbox':
							if( $this.children('input').is(':checked') )
								standard.push(i);
							break;
							
				case 'radio_othertext':
				case 'checkbox_othertext':
							if( $this.children('input').is(':checked') ){
								var value = $this.find('input.form-control').val();
								value = escape(value); //对输入文本unicode转码，避免冲突到下面几行代码构造数据结构时用到的":"和","两个符号
								standard.push(i + ':' + value);
							}
							break;
							
				case 'text':
							var value = $this.children().val();
							value = escape(value);
							standard.push(value);
							break;
			}
		});

		//序列化标准答案信息，写入到隐藏域#standard中
		var standardVal = standard.join(',');
		$('#standard').val( standardVal );

		return true;
	},

	/* 检查选项是否配置了相应选项文本 */
	validateOptions: function(){
		var self =  this;
		var state = true;

		$('#question-form .option-list .option-item').each(function(i){
			$this = $(this);
			if($this.hasClass('radio_option') || $this.hasClass('checkbox_option')){
				if( !$this.find('input[type=text]').val() ){
					self.validateOptionTip($this);
					state = state && false;
				}
			}
		});

		return state;
	},

	/* 检查选项是否配置好了标准答案 */
	validateStandard: function(){
		if( $('#question-form .option-list .option-item').first().children('input').length ){ //case：选项型问题
			/* 没有配置标准答案 */
			if( $('#question-form .option-list .option-item > input').filter(':checked').length == 0 ){
				this.validateStandardTip('请选出标准答案的选项');
				return false;
			}

			/* 选择其他项为标准答案，但是没有为其配置答案文本 */
			var $othertext = $('#question-form .option-list .radio_othertext_option > input');
			if( $othertext && $othertext.is(':checked') &&  !$othertext.siblings('div').children('input').val() ){
				this.validateStandardTip('请为选中的其他项配置相应的答案文本');
				return false;
			}
		}else if( $('#question-form .option-list .option-item').hasClass('text_option') ){ //case：文本输入型问题
			if( !$('#question-form .option-list .option-item').children().val() ){
				this.validateStandardTip('请为文本框配置标准答案');
				return false;
			}
		}

		return true;
	},

	/* 选项验证提示 */
	validateOptionTip: function($target){
		var $tip = $('<span class="tip">请填写选项文本</span>');
		$target.append($tip);

		setTimeout(function(){
			$tip.remove();
		}, 1500);
	},

	/* 标准答案验证提示 */
	validateStandardTip: function(msg){
		var $tip = $('<span class="tip">'+ msg +'</span>');
		$('#question-form .option-list').append($tip);

		setTimeout(function(){
			$tip.remove();
		}, 1500);
	},

};


/* 选项模板 */
OptionTemplate = {
	/* 取得指定选项类型的选项模板 */
	get: function(type){
		/* 针对 “其他选项” 做的一些配置 */
		if(optionManager.questionnaireType == 'exam'){
			var disabled = '';
			var opacity = 1;
		}else{
			var disabled = 'disabled';
			var opacity = 0.3
		}

		switch(type){
			case 'radio': //单选项
					return '<div class="option-item radio_option"> <input name="-" type="radio" /> <div> <input type="text" class="form-control" placeholder="请输入选项文本" /> <img class="option-del" src="/Public/Images/btn-del.png" title="删除该选项"> </div> </div>';

			case 'checkbox': //多选项
					return '<div class="option-item checkbox_option"> <input type="checkbox" /> <div> <input type="text" class="form-control" placeholder="请输入选项文本" /> <img class="option-del" src="/Public/Images/btn-del.png" title="删除该选项"> </div> </div>';

			case 'text': //简答项
					return '<div class="option-item text_option"> <textarea class="form-control"> </textarea> <img class="option-del" src="/Public/Images/btn-del.png" title="删除该选项"> </div>';

			case 'radio_othertext': //其他-单选项
					return '<div class="option-item radio_othertext_option"> <input type="radio" class="" /> <div> <strong style="position: absolute;line-height: 25px;">其他：</strong> <input style="margin-left:40px;opacity: '+ opacity +';" type="text" class="form-control" '+ disabled +' /> <img class="option-del" src="/Public/Images/btn-del.png" title="删除该选项"> </div> </div>';

			case 'checkbox_othertext': //其他-多选项
					return '<div class="option-item checkbox_othertext_option"> <input type="checkbox" class="" /> <div> <strong style="position: absolute;line-height: 25px;">其他：</strong> <input style="margin-left:40px;opacity: '+ opacity +';" type="text" class="form-control" '+ disabled +' /> <img class="option-del" src="/Public/Images/btn-del.png" title="删除该选项"> </div> </div>';
		}
	},

};


/* 选项管理器启动 */
optionManager.start();