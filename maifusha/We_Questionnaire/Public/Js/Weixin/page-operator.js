/**
 * 页面修饰器，包含填充客户作答和高亮标准答案功能
 */
PageOperator = new Object({
	/**
	 * 填充用户作答
	 * @param object $page  指定页面的Jquery对象
	 * @param object questionnaire  问卷应用对象
	 */
	fillReply: function($page, questionnaire){
		questionnaire.questionsList.forEach(function(question, questionIndex){
			$options = $page.find('.ui-controlgroup-controls').eq(questionIndex).children('div');
			
			var selectList = question.reply.split(',');
			selectList.forEach(function(optionVal){
				if( optionVal.indexOf('%') == 0 ){ //case: 简答题
					$options.children('textarea').val( unescape(optionVal) );
				}else{ //case: 勾选题
					var detail = optionVal.split(':');

					$options.eq( detail[0] ).find('label').trigger('click'); //勾选指定项

					if( detail[1] ){ //case: 勾选题且是个其他选项
						var $text = $options.eq( detail[0] ).find('textarea');
						$text.removeAttr('disabled').show();
						$text.val( unescape(detail[1]) );
					}
				}
			});
		});
	},

	/**
	 * 高亮标准答案
	 * @param object $page  指定页面的Jquery对象
	 * @param object questionnaire  问卷应用对象
	 */
	highlightStandard: function($page, questionnaire){
		questionnaire.questionsList.forEach(function(question, questionIndex){
			var $options = $page.find('.ui-controlgroup-controls').eq(questionIndex).children('div');
			
			var selectList = question.standard.split(',');
			selectList.forEach(function(optionVal){
				if( optionVal.indexOf('%') == 0 ){ //case: 简答题
					var $text = $('<textarea data-mini="true" class="ui-input-text ui-shadow-inset ui-body-inherit ui-corner-all ui-mini ui-textinput-autogrow highlightOption"></textarea>');
					$text.val( unescape(optionVal) );					
					$options.append($text);
				}else{ //case: 勾选题
					var detail = optionVal.split(':');

					$options.eq( detail[0] ).addClass('highlightOption'); //高亮指定项

					if( detail[1] ){ //case: 勾选题且是个其他选项
						var $text = $('<textarea data-mini="true" class="ui-input-text ui-shadow-inset ui-body-inherit ui-corner-all ui-mini ui-textinput-autogrow highlightOption"></textarea>');
						$text.val( unescape(detail[1]) ).show();					
						$options.eq( detail[0] ).append($text);
					}
				}
			});
		});
	},

});