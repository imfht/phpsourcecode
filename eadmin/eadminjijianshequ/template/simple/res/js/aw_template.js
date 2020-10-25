var AW_TEMPLATE = {

		
		
	'userCard':
			'<div id="aw-card-tips" class="aw-card-tips aw-card-tips-user">'+
				'<div class="aw-mod">'+
					'<div class="mod-head">'+
						'<a href="{{url}}" class="img">'+
							'<img src="{{avatar_file}}" alt="" />'+
						'</a>'+
						'<p class="title clearfix">'+
							'<a href="{{url}}" class="name pull-left" data-id="{{uid}}">{{user_name}}</a>'+
							'<i class="iconfont {{verified_enterprise}} pull-left" title="{{verified_title}}"></i>'+
						'</p>'+
						'<p class="aw-user-center-follow-meta">'+
							'<span>' + _t('金币') + ': <em class="aw-text-color-green">{{reputation}}</em></span>'+
							'<span>' + _t('经验') + ': <em class="aw-text-color-orange">{{agree_count}}</em></span>'+
						'</p>'+
					'</div>'+
					'<div class="mod-body">'+
						'<p>{{signature}}</p>'+
					'</div>'+
					'<div class="mod-footer clearfix">'+
						
						'<a class="btn btn-normal btn-success follow {{focus}} pull-right" onclick="AWS.User.follow($(this), \'user\', {{uid}});"><span>{{focusTxt}}</span> <em>|</em> <b>{{fansCount}}</b></a>'+
					'</div>'+
				'</div>'+
			'</div>',
	'commentBox' :
				'<div class="aw-comment-box" id="{{comment_form_id}}">'+
					'<div class="aw-comment-list"><p align="center" class="aw-padding10"><i class="aw-loading"></i></p></div>'+
					'<form action="{{comment_form_action}}" method="post" onsubmit="return false">'+
						'<div class="aw-comment-box-main">'+
							'<textarea class="aw-comment-txt form-control" rows="2" name="content" placeholder="' + _t('评论一下') + '..."></textarea>'+
							'<div class="aw-comment-box-btn">'+
								'<span class="pull-right">'+
									'<a href="javascript:;" class="btn btn-mini btn-success" onclick="AWS.User.save_comment($(this));">' + _t('评论') + '</a>'+
									'<a href="javascript:;" class="btn btn-mini btn-gray close-comment-box">' + _t('取消') + '</a>'+
								'</span>'+
							'</div>'+
						'</div>'+
					'</form>'+
				'</div>',
	'commentBoxClose' :
					'<div class="aw-comment-box" id="{{comment_form_id}}">'+
						'<div class="aw-comment-list"><p align="center" class="aw-padding10"><i class="aw-loading"></i></p></div>'+
					'</div>',
		'searchDropdownListTopics' :
							'<li class="topic clearfix"><span class="topic-tag" ><a href="{{url}}" class="text">{{name}}</a></span> <span class="pull-right text-color-999">{{discuss_count}} ' + _t('个帖子') + '</span></li>',
		'searchDropdownListUsers' :
							'<li class="user clearfix"><a href="{{url}}"><img src="{{img}}" />{{name}}<span class="aw-hide-txt">{{intro}}</span></a></li>',
		'searchDropdownListArticles' :
							'<li class="question clearfix"><a class="aw-hide-txt pull-left" href="{{url}}">{{content}} </a><span class="pull-right text-color-999">{{comments}} ' + _t('条评论') + '</span></li>',
				
'searchHt' :
	'<div class="aw-item"><a href="{{url}}" class="aw-topic-img" ><img src="{{img}}" alt="{{name}}" class="middleimg" /></a><p class="aw-title"><span class="topic-tag"><a href="{{url}}" class="text" target="_blank">{{name}}</a></span></p><p class="aw-text-color-666"><i class="iconfont icon-dongtaiwenben"></i> {{topiccount}}个帖子			</p></div>',
'searchUsers' :
	'<div class="aw-item"><a href="{{userurl}}" class="aw-user-img" data-id="{{id}}"><img src="{{userhead}}" style="width:50px;height:50px;" alt="" /></a><p class="aw-title"><a href="{{userurl}}" target="_blank">{{nickname}}</a></p><p class="aw-text-color-666"><span><i class="iconfont icon-gongxianbang"></i> 威望{{expoint2}} </span><span><i class="iconfont icon-jifen"></i> 金币 {{point}}</span><span><i class="iconfont icon-huoyuedu"></i> 经验 {{expoint1}}</span><span></p></div>',
'searchTopics' :
	'<div class="aw-item active"><p class="aw-title"><a href="{{url}}" target="_blank">{{title}}</a>'+
'</p><p class="aw-text-color-666"><i class="iconfont icon-052shucedongtai fz16"></i> {{reply}} 个回复 • {{focuscount}} 人关注</p></div>',


'httopiclist':
	'<div class="aw-item " data-topic-id="{{id}}"><a class="aw-user-name aw-user-name-o hidden-xs" data-id="{{uid}}"  href="{{userurl}}" rel="nofollow"><img src="{{userhead}}" alt="{{nickname}}"><i class="iconfont {{rzicon}}"></i></a><div class="aw-question-content"><h4 class="ellipsis"><a class="listlink" href="{{url}}">{{title}}</a></h4><p><span class="text-color-999">{{replystr}} • {{reply}} 个回复 • {{view}} 次浏览 • 	{{praise}}次点赞			</span></p></div></div>',


					

					
}
