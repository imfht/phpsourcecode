/**
 * 问卷页面的主JS脚本
 */
(function($){
$(function(){

/**
 * 定义问卷应用对象
 */
questionnaire = new Object({
	/* 问卷元属性 */
	id: null,
	type: null, //string, 调研卷还是考试卷
	name: null,
	description: null,
	create_date: null,
	expire_date: null,
	marked: null, //bool, 用户是否已作答过该问卷
	
	signimagesList: null, //签名图列表

	questionsList: null, //问题列表数据, 根据程序流程动态的会添加reply或standard属性

	/* 问卷当前配置 */
	theme: 'b', //颜色主题
	currentIndex: 0, //当前作答问题索引
	autoSwitch: false, //bool, 是否自动切题（仅限于单选题有效）


	/**
	 * 问卷应用启动点
	 */
	start: function(){
		$.mobile.loading('show'); //显示loading转圈
		this.delegateEvents(); //委托页面组件事件
		
		var self = this;		
		var questionnaireID = $('html').attr('questionnaire_id');

		this.loadQuestionnaire(questionnaireID) //加载问卷元数据到当前应用对象
			.then(function(){
				if( !self.marked ){ //用户未曾作答该问卷
					$.mobile.loading('hide');
					$('#start-btn').show(); //显示出“开始答题”按钮
				}else{ //用户已经作答过该问卷
					$.mobile.loading('hide');
					self.turnto('again-page');
				}
			});
	},


	/* 加载问卷元数据属性到当前应用对象 */
	loadQuestionnaire: function(questionnaireID){
		var self = this;

		return $.ajax({
			type: 'GET',
			url: '/WebService/questionnaires/' + questionnaireID + '.json',
			dataType: 'json',
		}).then(function(response){
			$.extend(self, response); //填充属性

			/* 写入问卷信息到页面中 */
			$('.questionnaire-name').text(self.name);
			$('#questionnaire-description').text(self.description);
		});
	},

	/* 加载问题列表到当前应用对象中 */
	loadQuestions: function(questionnaireID){
		var self = this;

		return $.ajax({
			type: 'GET',
			url: '/WebService/questionnaires/' + questionnaireID + '/questions.json',
			dataType: 'json',
		}).then(function(response){
			if( response.errorMsg ){ //请求有报错
				if( confirm(response.errorMsg) ){
					WeixinJSBridge.invoke('closeWindow', {}, function(res){});			
				}
			}else{ //请求正常
				$.extend(self, response); //填充属性
			}
		});
	},

	/* 加载判分情况 */
	loadJudgement: function(questionnaireID){
		var self = this;

		return $.ajax({
			type: 'GET',
			url: '/WebService/questionnaires/' + questionnaireID + '/judgement.json',
			dataType: 'json',
		}).then(function(response){
			self.questionsList.forEach(function(question){
				$.extend(question, response[question.id]);
			});
		});
	},

	/* 加载签名图列表数据到当前应用对象中 */
	loadSignimages: function(questionnaireID){
		var self = this;

		return $.ajax({
			type: 'GET',
			url: '/WebService/questionnaires/' + questionnaireID + '/signimages.json',
			dataType: 'json',
		}).then(function(response){
			if( response.signimagesList ){
				$.extend(self, response); //填充属性
				$('.signimage').show();
			}
		});
	},

	/* 根据问题列表数据渲染所有问题页面 */
	renderQuestions: function(){
		var self = this;

		this.questionsList.forEach(function(question, questionIndex){
			self.renderQuestionPage(questionIndex);
		});
	},

	/**
	 * 渲染问题单页
	 * @param int questionIndex  指定要渲染第几道问题的页面
	 */
	renderQuestionPage: function(questionIndex){
		var questionSection = Template.widgetCompiler.questionSection(questionIndex, this); //编译问题主区域
		
		var page = Template.pageCompiler.questionPage(questionIndex, questionSection, this); //编译整个问题页面

		$('body').append(page);
	},

	/* 渲染核对回答页面 */
	renderCheckPage: function(){
		var checkPage = Template.pageCompiler.checkPage(this);
		$('body').append(checkPage);
	},

	/* 渲染标准答案比对页面 */
	renderComparePage: function(){
		var comparePage = Template.pageCompiler.comparePage(this);
		$('body').append(comparePage);
	},	

	/* 更新滑动杆到指定进度值 */
	updateSliderBar: function(process){
		/* 更新进度到所有问题页面内已经渲染好的滑动杆 */
		$('input[data-type=range]').each(function(){
			$(this).val(process);
			$(this).slider('refresh');
		});

		/* 更新进度到所有问题页面内尚未渲染好的滑动杆 */
		$('input[type=range]').each(function(){
			$(this).attr('value', process);
		});
	},

	/* 切到上一题 */
	switchPre: function(){
		if( this.currentIndex == 0 ){
			return true;
		}else{
			this.currentIndex--;
			this.updateSliderBar(this.currentIndex + 1);
			this.turnto("question-page-" + this.currentIndex);
		}		
	},

	/* 切到下一题 */
	switchNext: function(){
		if( !this.checkReplyed(this.currentIndex) ){ //下一题前检查当前问题是否作答
			this.warnFillin(this.currentIndex);
			return;
		}		
 
		if( (this.currentIndex+1) == this.questionsList.length ){
			this.turnto("finish-page", "flow");
		}else{
			this.currentIndex++;
			this.updateSliderBar(this.currentIndex + 1);
			this.turnto("question-page-" + this.currentIndex);
		}		
	},

	/* 将作答信息写入到问题列表的reply属性中 */
	updateReply: function(questionIndex){
		var reply = [];
		var $form = $("#question-page-" + questionIndex).find('form').eq(0);
		
		$form.find('.ui-controlgroup-controls > div').each(function(index){
			$this = $(this);

			/* 检查当前选项类型 */
			var optionType = '';
			if( $this.children().first().is('textarea') ){
				optionType = 'text';
			}else if( $this.hasClass('option') && $this.children().first().hasClass('ui-radio') ){
				optionType = 'radio';
			}else if( $this.hasClass('option') && $this.children().first().hasClass('ui-checkbox') ){
				optionType = 'checkbox';
			}else if( $this.hasClass('multioption') && $this.children().first().hasClass('ui-radio') ){
				optionType = 'radio_othertext'
			}else if( $this.hasClass('multioption') && $this.children().first().hasClass('ui-checkbox') ){
				optionType = 'checkbox_othertext'
			}

			/* 依据不同选项类型进行答案收集 */
			switch( optionType ){
				case 'text':
							var value = escape( $this.children('textarea').val() );
							reply.push( value );
							break;
							
				case 'radio':							
				case 'checkbox':
							if( $this.find('input').is(':checked') ){
								reply.push(index);
							}
							break;
							
				case 'radio_othertext':							
				case 'checkbox_othertext':
							if( $this.find('input').is(':checked') ){
								var value = index + ':' + escape( $this.find('textarea').val() );
								reply.push(value);
							}
							break;
			}
		});

		reply = reply.join(',');
		this.questionsList[questionIndex]['reply'] = reply;
	},

	/* 检查指定问题是否已作答 */
	checkReplyed: function(questionIndex){
		return this.questionsList[questionIndex]['reply'] || false;
	},

	/* 检查问题是否全部作答 */
	checkAllReplyed: function(){
		return this.questionsList.every(function(item){
			return item.reply || false;
		});
	},

	/* 在指定问题页提示用户作答 */
	warnFillin: function(questionIndex){
		$('#question-page-' + questionIndex).find('.warn-fillin').fadeIn(300).delay(1000).fadeOut(600);
	},

	/* 随机获得一张签名图的路径 */
	getRandSignimage: function(signimagesList){
		var num = signimagesList.length;
		var randIndex = Math.round( Math.random() * num );
		return signimagesList[randIndex];
	},

	/* 以指定转场效果前往指定页面 */
	turnto: function(pageID, transition){
		var effect = transition?transition:"slide";
		$.mobile.changePage("#"+pageID, {transition: effect});
	},

	/* 前往最近一张问题页面 */
	gotoCurrentQuestionPage: function(transition){
		var effect = transition?transition:'flip';
		this.turnto('question-page-' + this.currentIndex, effect);
	},

	/* 提交问卷作答 */
	submitReply: function(questionnaireID){
		if(!this.checkAllReplyed() ){ //检查问题是否均已作答
			return this.turnto('warn-page');
		}

		var reply = {};
		this.questionsList.forEach(function(question){
			reply[question.id] = question.reply;
		});

		var self = this;
		return $.ajax({
			type: 'POST',
			url: '/WebService/questionnaires/' + questionnaireID + '/reply.json',
			data: JSON.stringify(reply),
			contentType: 'application/json',
			dataType: 'json',
		}).then(function(response){
			if( response.errorMsg == null ){
				self.replySuccess(response.total_score);
			}else{
				confirm('答卷提交失败，错误详情:' + response.errorMsg);
			}			
		});
	},

	replySuccess: function(totalScore){
		if( totalScore != null ){
			$("#success-page").find('.judgeArea').show();
			$('#success-page').find('.judgeArea .score').text(totalScore);
		}

		this.turnto('success-page', 'pop');
	},


    /**
     * 委托页面组件事件
     */
    delegateEvents: function(){
        var self = this;

        /* 欢迎页面的 “开始答题” 按钮 */
        $(document).delegate("#start-btn", 'click', function(){
            $.mobile.loading('show');

            self.loadQuestions(self.id) //加载问题列表数据
                .then(function(){
                    self.renderQuestions();	//渲染所有问题页面

                    self.loadSignimages(self.id) //加载签名图列表数据
                        .then(function(){
                            $.mobile.loading('hide');

                            self.turnto('question-page-0', 'pop'); //前往第一个问题页面
                        });
                });
        });

        /* 问题页面的滑动杆 */
        $(document).delegate(".question-page header input[data-type=range]", "slidestop", function(){
            var process = $(this).parent().find('a[role=slider]').attr('aria-valuenow'); //获得进度值

            /* 更新进度到其余问题页面内已经渲染好的滑动杆 */
            $('input[data-type=range]').each(function(){
                $(this).val(process);
                $(this).slider('refresh');
            });

            /* 更新进度到其余问题页面内还未渲染的滑动杆 */
            $('input[type=range]').each(function(){
                $(this).attr('value', process);
            });

            self.currentIndex = process-1;
            self.turnto('question-page-' + self.currentIndex); //前往滑动杆指定的问题页面
        });

        /* 自动切题开关 */
        $(document).delegate(".auto-switch", 'change', function(){
            self.autoSwitch = $(this).is(':checked');

            /* 同步开关状态到每道问题页面的切题开关状态 */
            $('.auto-switch').each(function(){
                this.checked = self.autoSwitch;

                if( $(this).hasClass('ui-flipswitch-input') ){ //已经渲染好的开关需要刷新一下渲染
                    $(this).flipswitch( "refresh" );
                }
            });
        });

        /* 每题作答后及时将作答信息写入到问题列表的reply属性中 */
        $(document).delegate(".question-page form input, .question-page form textarea", 'change', function(){
            self.updateReply(self.currentIndex);
        });

        /* 勾选其他项后，显示并撤销对其他项文本框的灰化状态 */
        $(document).delegate(".question-page form .multioption .ui-mini input", 'change', function(){
            $this = $(this);
            var status = ( $this.is(':checked') ) ? ('checked'):('unchecked');
            var $otherText = $this.parent().siblings('textarea');

            if(status=='checked'){
                $otherText.removeAttr('disabled');
                $otherText.show();
            }else{
                $otherText.attr('disabled', 'disabled');
                $otherText.hide();
            };
        });

        /* 对于单选题，根据切题开关状态决定是否在勾选后切题 */
        $(document).delegate(".question-page form .option input[type=radio]", 'change', function(){
            if( self.autoSwitch ){
                self.switchNext();
            }
        });

        /* 上一题按钮 */
        $(document).delegate(".pre-question", 'click', function(){
            self.switchPre();
        });

        /* 下一题按钮 */
        $(document).delegate(".next-question", 'click', function(){
            self.switchNext();
        });

        /* 提交问卷按钮 */
        $(document).delegate("#submit-btn", 'click', function(){
            $.mobile.loading('show');

            self.submitReply(self.id)
                .then(function(){
                    $.mobile.loading('hide');
                });
        });

        /* 返回修改按钮 */
        $(document).delegate("#modify-btn", 'click', function(){
            /* 前往最近一张问题页面 */
            self.gotoCurrentQuestionPage('flow');
        });

        /* 核对回答按钮 */
        $(document).delegate("#check-btn", 'click', function(){
            $.mobile.loading('show');

            /* 为保证显示loading效果，这里做延时处理 */
            setTimeout(function(){
                self.renderCheckPage();
                self.turnto("check-page", 'pop');
            },500);
        });

        /* 检查结果按钮（用于比较标准答案） */
        $(document).delegate("#compare-result", 'click', function(){
            $.mobile.loading('show');

            self.loadJudgement(self.id)
                .then(function(){
                    self.renderComparePage(); //渲染标准答案比对页面
                }).then(function(){
                    self.turnto("compare-page", 'flip');
                    $.mobile.loading('hide');
                });
        });

        /* 简要回顾按钮 */
        $(document).delegate("#review-btn", 'click', function(){
            $.mobile.loading('show');

            self.loadQuestions(self.id)
            	.then(function(){
            		self.loadJudgement(self.id)
            			.then(function(){
                            switch( self.type ){
                                case 'exam' : //渲染标准答案比对页面
                                            self.renderComparePage();
                                            return 'compare-page';

                                case 'survey' : //渲染作答回顾页面
                                                self.renderCheckPage();
                                                return 'check-page';
                            }
            			})
            			.then(function(pageName){
		                    self.turnto(pageName, 'flip');
		                    $.mobile.loading('hide');
            			});
            	});
        });


        /* 关闭应用按钮 */
        $(document).delegate(".close-app", 'click', function(){
            WeixinJSBridge.invoke('closeWindow', {}, function(res){});
        });

        /* 返回顶部按钮 */
        $(document).delegate("#totop", 'click', function(){
            $('body, html').animate({scrollTop:0}, 600);
        });

        /* 在问题页面显示之前，刷新该页的随机签名图 */
        $(document).delegate(".question-page", 'pagebeforeshow', function(){
            var signimagesList = self.signimagesList;

            if( signimagesList ){ //如果存在签名图
                var questionIndex = $(this).attr('id').split('-')[2];
                var randSignimage = self.getRandSignimage(signimagesList);
                $(this).find('.signimage').attr('src', randSignimage);
            }
        });

        /* 核对回答页面显示之前填充入用户作答（之所以在这处理填入而不再renderCheckPage里，因为有些效果必须在pagebeforeshow页面渲染完成后才能处理） */
        $(document).delegate("#check-page", 'pagebeforeshow', function(){
            PageOperator.fillReply($('#check-page'), self); //填充入当前作答数据
            $('#check-page input, #check-page textarea').attr('disabled', 'disabled');
        });

        /* 离开核对回答页面后，销毁之，避免用户更改答题后与先前核对回答页面有出入 */
        $(document).delegate("#check-page", 'pagehide', function(){
            $('#check-page').remove();
        });

        /* 标准答案比对页面显示之前填充入用户作答 */
        $(document).delegate("#compare-page", 'pagebeforeshow', function(){
            PageOperator.fillReply($('#compare-page'), self);
            PageOperator.highlightStandard($('#compare-page'), self);

            $('#compare-page input, #compare-page textarea').attr('disabled', 'disabled');
        });

        /* 离开标准答案比对页面后，销毁之 */
        $(document).delegate("#compare-page", 'pagehide', function(){
            $('#compare-page').remove();
        });
    }

});


/* 启动问卷应用 */
questionnaire.start();

});	
})(jQuery);