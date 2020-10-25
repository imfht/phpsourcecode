/**
 * 在Template命名空间下定义一些自建的模板编译器
 */
Template = {
	/* 小部件的模板 */
	widgetCompiler: {
		/**
		 * 问题主区域-模板
		 * @param int questionIndex  问题索引号
		 * @param object questionnaire  问卷应用对象
		 * @param string pagePrefix  用于id的页面前缀，可能是"checkpage", "comparepage"
		 * @return string  编译好的问题主区域html代码
		 */
		questionSection: function(questionIndex, questionnaire, pagePrefix){
			var question = questionnaire.questionsList[questionIndex];
			var pagePrefix = pagePrefix ? pagePrefix : '';

			/* 去除简答题类型的外部边框 */
            var options = JSON.parse(question.options);
			var className = (options[0]['type'] == "text") ? 'noborder' : '';

			var outputHtml = '	\
								<h3 class="ui-bar ui-bar-b ui-corner-all" data-inline="true">' + question.name + '</h3>	\
								<div class="ui-body">	\
									<fieldset data-role="controlgroup" data-mini="true" class="'+ className +'">	\
										{outlet}	\
									</fieldset>	\
								</div>	\
			';
			
			var optionsHtml = ''; //选项的html代码串
			for(var key in options){
				var option = options[key];
				var optionType = option['type'];
				var optionText = option['text'];

				switch( optionType ){
					case 'radio': //单选题
							 optionsHtml += '<div class="option">';
							 optionsHtml += '<label for="' + pagePrefix + 'question-' + questionIndex + '-option' + key + '">' + optionText + '</label><input type="radio"    name="question-' + questionIndex + '" id="' + pagePrefix + 'question-' + questionIndex + '-option' + key + '">';
							 optionsHtml += '</div>';
							 break;

					case 'checkbox': //多选题
							 optionsHtml += '<div class="option">';
							 optionsHtml += '<label for="' + pagePrefix + 'question-' + questionIndex + '-option' + key + '">' + optionText + '</label><input type="checkbox" name="question-' + questionIndex + '" id="' + pagePrefix + 'question-' + questionIndex + '-option' + key + '">';
							 optionsHtml += '</div>';
							 break;

					case 'text': //简答题
							 optionsHtml += '<div class="option">';
							 optionsHtml += '<textarea name="question-' + questionIndex + '" id="' + pagePrefix + 'question-' + questionIndex + '-option' + key + '" data-mini="true" data-clear-btn="true" placeholder="您的回答..."></textarea>';
							 optionsHtml += '</div>';
							 break;

					case 'radio_othertext': //其他-单选题
							 optionsHtml += '<div class="multioption">';
							 optionsHtml += '<label for="' + pagePrefix + 'question-' + questionIndex + '-option' + key + '-switch">' + '其它' + '</label><input type="radio"    name="question-' + questionIndex + '" id="' + pagePrefix + 'question-' + questionIndex + '-option' + key + '-switch">';
							 optionsHtml += '<textarea name="question-' + questionIndex + '" id="' + pagePrefix + 'question-' + questionIndex + '-option' + key + '" data-mini="true" placeholder="您的回答..." /></textarea>';
							 optionsHtml += '</div>';
							 break;

					case 'checkbox_othertext': //其他-多选题
							 optionsHtml += '<div class="multioption">';
							 optionsHtml += '<label for="' + pagePrefix + 'question-' + questionIndex + '-option' + key + '-switch">' + '其它' + '</label><input type="checkbox" name="question-' + questionIndex + '" id="' + pagePrefix + 'question-' + questionIndex + '-option' + key + '-switch">';
							 optionsHtml += '<textarea name="question-' + questionIndex + '" id="' + pagePrefix + 'question-' + questionIndex + '-option' + key + '" data-mini="true" placeholder="您的回答..." /></textarea>';
							 optionsHtml += '</div>';
							 break;
				}
			}

			return outputHtml.replace('{outlet}', optionsHtml);
		},

		/**
		 * 折叠组件-模板
		 * @param int questionIndex  问题索引号
		 * @param object questionnaire  问卷应用对象
		 * @param string pagePrefix  用于id的页面前缀，可能是"checkpage", "comparepage"
		 * @param string append  需要追加的html代码
		 * @return string  编译好的折叠组件html代码
		 */
		collapse: function(questionIndex, questionnaire, pagePrefix, append){
			var theme = questionnaire.theme; //问卷主题色
			
			var outputHtml = '	\
						    <div data-role="collapsible" data-inset="false" data-collapsed="false" data-mini="true" data-theme="' + theme + '" data-content-theme="' + theme + '">	\
						    	{outlet}	\
						    </div>	\
			';

			var section = Template.widgetCompiler.questionSection(questionIndex, questionnaire, pagePrefix);

			section += ( append || '');				

			return outputHtml.replace('{outlet}', section);
		},
	},


	/* 页面模板 */
	pageCompiler: {
		/**
		 * 单个问题页面-模板
		 * @param int questionIndex  问题索引号
		 * @param string questionSection  问题主区域html代码
		 * @param object questionnaire  问卷应用对象
		 * @return string  编译好的单个问题页面html代码
		 */
		questionPage: function(questionIndex, questionSection, questionnaire){
			var count = questionnaire.questionsList.length;

			var template = '	\
							<section id="question-page-' + questionIndex + '" class="question-page" data-role="page">	\
								<header data-role="header" data-position="fixed" data-tap-toggle="false">	\
									<h1 class="ui-bar ui-icon-bullets ui-btn-icon-left">' + questionnaire.name + '</h1>	\
									<div class="tool-box-wrap">	\
										<div class="tool-box">	\
											<input type="checkbox" data-role="flipswitch" data-mini="true" data-on-text="自动切题" data-off-text="手动切题" class="ui-mini auto-switch">	\
											<a class="question-count ui-btn ui-corner-all ui-btn-inline">' + count + '</a>	\
											<input type="range" name="question-step" min="1" max="' + count + '" value="1" data-show-value="true" data-popup-enabled="true" data-highlight="true" data-track-theme="a">	\
										</div>	\
									</div>	\
								</header>	\
								<div data-role="content" class="content">	\
									<form>	\
										<a class="ui-nodisc-icon ui-btn ui-mini ui-alt-icon ui-corner-all ui-icon-comment ui-btn-icon-notext ui-btn-inline">	\
											<span class="question-index">' + (questionIndex+1) + '</span> \
										</a>	\
										{outlet}	\
									</form>	\
									<h5 class="warn-fillin">下一题前请作答!</h5>	\
									<img src="" class="signimage" style="display:none;" />	\
								</div>	\
								<footer data-role="footer" data-position="fixed" style="border-top:0;" data-tap-toggle="false">	\
									<aside style="background:#252525;">	\
										<div class="ui-grid-d">	\
										    <div class="ui-block-a"></div>	\
										    <div class="ui-block-b"><a class="pre-question ui-btn ui-icon-carat-l ui-btn-icon-left ui-btn-inline ui-corner-all ui-mini ui-shadow-icon">上一题</a></div>	\
										    <div class="ui-block-c"></div>	\
										    <div class="ui-block-d"><a class="next-question ui-btn ui-icon-carat-r ui-btn-icon-right ui-btn-inline ui-corner-all ui-mini ui-shadow-icon">下一题</a></div>	\
										    <div class="ui-block-e"></div>	\
										</div>	\
									</aside>	\
									<h4> Copyright © 2014-2015 </h4>	\
								</footer>	\
							</section>	\
			';

			return template.replace('{outlet}', questionSection); //向template中写入问题区域代码
		},

		/**
		 * 作答回顾页面-模板
		 * @param object questionnaire  问卷应用对象
		 * @return string  编译好的作答回顾页面html代码
		 */
		checkPage: function(questionnaire){
			var tempalte = '	\
							<section id="check-page" data-role="page">	\
								<header data-role="header" data-position="fixed" data-tap-toggle="false">	\
									<h1 class="ui-bar ui-icon-eye ui-btn-icon-left">问卷作答回顾</h1>		\
								</header>	\
								<div data-role="content" class="content collapse-list">	\
									{outlet}	\
								</div>	\
								<aside id="toolbox" class="ui-alt-icon">	\
								    <a class="ui-btn ui-btn-a ui-shadow ui-corner-all ui-icon-carat-u ui-btn-icon-notext" id="totop"></a>	\
								    <a class="ui-btn ui-btn-a ui-shadow ui-corner-all ui-icon-back ui-btn-icon-notext" data-rel="back"></a>	\
								</aside>	\
								<footer data-role="footer" data-position="fixed" data-tap-toggle="false">	\
									<h4> Copyright © 2014-2015 </h4>	\
								</footer>	\
							</section>	\
			';

			var collapsetHtml = '';
			questionnaire.questionsList.forEach(function(question, index){
				 var section = Template.widgetCompiler.collapse(index, questionnaire, 'checkPage');
				 collapsetHtml += section;
			});

			return tempalte.replace('{outlet}', collapsetHtml);
		},

		/**
		 * 查看结果页面-模板
		 * @param object questionnaire  问卷应用对象
		 * @return string  编译好的查看结果页面html代码
		 */
		comparePage: function(questionnaire){
			var tempalte = '	\
							<section id="compare-page" data-role="page">	\
								<header data-role="header" data-position="fixed" data-tap-toggle="false">	\
									<h1 class="ui-bar ui-icon-eye ui-btn-icon-left">问卷正解核对</h1>		\
								</header>	\
								<div data-role="content" class="content">	\
									<div class="compare-tip">	\
										<h3 style="float: left;line-height: 25px;margin-top: 0;">标准答案会有如下底色：</h3>	\
										<p style="background: #009933;margin-top: 0;padding: 5px;text-align: center;border-radius: 3px;float: left;vertical-align: middle;">标准答案的底色</p>	\
									</div>	\
									<div class="collapse-list">	\
										{outlet}	\
									</div>	\
								</div>	\
								<aside id="toolbox" class="ui-alt-icon">	\
								    <a class="ui-btn ui-btn-a ui-shadow ui-corner-all ui-icon-carat-u ui-btn-icon-notext" id="totop"></a>	\
								    <a class="ui-btn ui-btn-a ui-shadow ui-corner-all ui-icon-back ui-btn-icon-notext" data-rel="back"></a>	\
								</aside>	\
								<footer data-role="footer" data-position="fixed" data-tap-toggle="false">	\
									<h4> Copyright © 2014-2015 </h4>	\
								</footer>	\
							</section>	\
			';

			var collapsetHtml = [];
			questionnaire.questionsList.forEach(function(question, index){
				/* 给考试卷的每道题添加判分提示 */
				 var scoreNote = '';
				 if( questionnaire.type == 'exam' ){
				 	scoreNote = '<h3 class="score-note">计分: '+ question.score +'分 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 实得: '+ question.achieve_score +'分</h3>';
				 }

				 var section = Template.widgetCompiler.collapse(index, questionnaire, 'comparePage', scoreNote);
				 collapsetHtml += section;
			});

			return tempalte.replace('{outlet}', collapsetHtml);
		},
	},
};