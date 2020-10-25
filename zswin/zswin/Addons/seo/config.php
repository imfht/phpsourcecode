<?php
return array(
    'type'=>array(//配置在表单中的键名 ,这个会是config[title]
        'title'=>'是否开启seo设置：',//表单的文字
        'type'=>'radio',		 //表单的类型：text、textarea、checkbox、radio、select等
        'options'=>array(
            '1'=>'是',
            '0'=>'否',
        ),
        'value'=>'0',
        'tip'=>'不开启则使用默认seo配置'
    ),
    
    'group'=>array(
        'type'=>'group',
        'options'=>array(
        'Index'=>array(
                'title'=>'首页',
                'options'=>array(
                  
	'indextitle'=>array(
        'title'=>'首页动态title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述'
    ),
	'indexkey'=>array(
        'title'=>'首页动态关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'indexdes'=>array(
        'title'=>'首页动态描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'hottitle'=>array(
        'title'=>'首页热门title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述'
    ),
	'hotkey'=>array(
        'title'=>'首页热门关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'hotdes'=>array(
        'title'=>'首页热门描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'zantitle'=>array(
        'title'=>'首页超赞title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述'
    ),
	'zankey'=>array(
        'title'=>'首页超赞关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'zandes'=>array(
        'title'=>'首页超赞描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
   'gztitle'=>array(
        'title'=>'首页关注title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述'
    ),
	'gzkey'=>array(
        'title'=>'首页关注关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'gzdes'=>array(
        'title'=>'首页关注描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ), 
                ),
        ),
       'Con'=>array(
                'title'=>'内容页',
                'options'=>array(
                    'contitle'=>array(
        'title'=>'内容页title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述[arttitle]文章标题[artcate]文章分类'
    ),
	'conkey'=>array(
        'title'=>'内容页关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'condes'=>array(
        'title'=>'内容页描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
                ),
        ),
    'User'=>array(
                'title'=>'用户页',
                'options'=>array(
                   
    'uctitle'=>array(
        'title'=>'用户中心title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述[username]用户名[nickname]用户昵称[appname]个人中心应用名称'
    ),
	'uckey'=>array(
        'title'=>'用户中心关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'ucdes'=>array(
        'title'=>'用户中心描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
     'regtitle'=>array(
        'title'=>'注册title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述'
    ),
	'regkey'=>array(
        'title'=>'注册关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'regdes'=>array(
        'title'=>'注册描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
     'logintitle'=>array(
        'title'=>'登录title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述'
    ),
	'loginkey'=>array(
        'title'=>'登录关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'logindes'=>array(
        'title'=>'登录描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
     'pstitle'=>array(
        'title'=>'找回密码title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述'
    ),
	'pskey'=>array(
        'title'=>'找回密码关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'psdes'=>array(
        'title'=>'找回密码描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
                ),
        ),
    'List'=>array(
                'title'=>'列表页',
                'options'=>array(
                    
    'tltitle'=>array(
        'title'=>'标签列表title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述[tagname]标签名称[tagdes]标签描述'
    ),
	'tlkey'=>array(
        'title'=>'标签列表关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'tldes'=>array(
        'title'=>'标签列表描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'sltitle'=>array(
        'title'=>'搜索列表title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述[slname]搜索词名称'
    ),
	'slkey'=>array(
        'title'=>'搜索列表关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'sldes'=>array(
        'title'=>'搜索列表描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
        'cltitle'=>array(
        'title'=>'分类列表title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述[catename]分类名称[catedes]分类描述'
    ),
	'clkey'=>array(
        'title'=>'分类列表关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'cldes'=>array(
        'title'=>'分类列表描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
        'attitle'=>array(
        'title'=>'所有标签页title：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>'[title]网站标题[keyword]网站关键词[description]网站描述'
    ),
	'atkey'=>array(
        'title'=>'所有标签页关键词：',//表单的文字
        'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
    'atdes'=>array(
        'title'=>'所有标签页描述：',//表单的文字
        'type'=>'textarea',		 //表单的类型：text、textarea、checkbox、radio、select等
        'value'=>'',			 //表单的默认值
        'tip'=>''
    ),
                ),
        ),

    
    )
    ),
    
    
    
  
);
					