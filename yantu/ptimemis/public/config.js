var globalData = {
	"site_title":"PTIMECMS"
};

var favorMenuData = [
	{'name': '棋院新闻','url':'#','son':[
		{'name': '国际象棋','path': 'object/category/16/article'},
		{'name': '国际象棋','path': 'object/article/44'}
	]},			
];
var menuData = [
		{
			'key'			: 'cms',
			'name'			: 'CMS',
			'description'	: '对网站数据进行管理',
			'ico'			: 'pencil',
			'url'			: '#',
			'son'			:
			[
				{
					'key'			: 'article',
					'name'			: '文章管理',
					'description'	: '对文章进行管理',
					'ico'			: 'equal-box',
					'state'			: ['object',{'objectName':'article'}]
				},
				{
					'key'			: 'album',
					'name'			: '相册管理',
					'description'	: '对相册进行管理',
					'ico'			: 'image-album',
					'state'			: ['object',{'objectName':'album'}]
				},
				{
					'key'			: 'link',
					'name'			: '链接管理',
					'description'	: '对链接进行管理',
					'ico'			: 'link',
					'state'			: ['object',{'objectName':'link'}]
				},
				{
					'key'			: 'topic',
					'name'			: '专题管理',
					'description'	: '对专题进行管理',
					'ico'			: 'file',
					'state'			: ['object',{'objectName':'topic'}]
				},
				{
					'key'			: 'category',
					'name'			: '分类管理',
					'description'	: '对分类进行管理',
					'ico'			: 'database-outline',
					'state'			: ['object',{'objectName':'category'}]
				},
				{
					'key'			: 'menu',
					'name'			: '导航管理',
					'description'	: '对导航进行管理',
					'ico'			: 'menu',
					'state'			: ['object',{'objectName':'menu'}]
				}

			]
		},	
		{	
			'key'			: 'system',
			'name'			: 'SYSTEM',
			'description'	: '对系统进行管理',
			'ico'			: 'pencil',
			'url'			: '#',
			'son'			:
			[
				{
					'key'			: 'user',
					'name'			: '用户管理',
					'description'	: '对用户进行管理',
					'ico'			: 'account',
					'state'			: ['object',{'objectName':'user'}]
				},
				{
					'key'			: 'role',
					'name'			: '角色管理',
					'description'	: '对角色进行管理',
					'ico'			: 'account-multiple',
					'state'			: ['object',{'objectName':'role'}]
				},
				{
					'key'			: 'setting',
					'name'			: '系统设置',
					'description'	: '对系统进行设置',
					'ico'			: 'settings',
					'state'			: ['object',{'objectName':'setting'}]
				}
			]
		},
	];

var objectMenuData = {
	'article'           :
	[
		{
			'key'			: 'detail',
			'name'			: '详情',
			'name_field'    : 'title',
			'description'	: '您可在此页面查看文章的详情',
			'ico'			: 'eye',
			'state'			: ['objectDetail',{}]
		},
		{
			'key'			: 'alias',
			'name'			: '别名',
			'name_field'    : 'title',
			'description'	: '您可在此页面查看和管理文章的别名',
			'ico'			: 'apple',
			'state'			: ['objectRelateDetail',{'objectName':'article','objectId':'','objectRelate':'alias'}]
		},
		{
			'key'			: 'seo',
			'name'			: 'SEO优化',
			'name_field'    : 'title',
			'description'	: '您可在此页面查看和管理文章的SEO优化',
			'ico'			: 'apple',
			'state'			: ['objectRelateDetail',{'objectRelate':'seo'}]
		},
		{
			'key'			: 'comment',
			'name'			: '评论',
			'name_field'    : 'title',
			'description'	: '您可在此页面查看和管理文章下的所有评论',
			'ico'			: 'apple',
			'state'			: ['objectRelate',{'objectRelate':'comment'}]
		}
	],
	'album'           :
	[
		{
			'key'			: 'detail',
			'name'			: '详情',
			'name_field'    : 'title',
			'description'	: '您可在此页面查看相册的详情',
			'ico'			: 'eye',
			'state'			: ['objectDetail',{}]
		},
		{
			'key'			: 'picture',
			'name'			: '照片',
			'name_field'    : 'title',
			'description'	: '您可在此页面查看和管理相册下的所有照片',
			'ico'			: 'image-area',
			'state'			: ['objectRelate',{'objectRelate':'picture'}]
		}
	],
	'link'           :
	[
		{
			'key'			: 'detail',
			'name'			: '详情',
			'name_field'    : 'title',
			'description'	: '您可在此页面查看链接的详情',
			'ico'			: 'eye',
			'state'			: ['objectDetail',{}]
		}
	],
	'topic'           :
	[
		{
			'key'			: 'detail',
			'name'			: '详情',
			'name_field'    : 'title',
			'description'	: '您可在此页面查看专题的详情',
			'ico'			: 'eye',
			'state'			: ['objectDetail',{}]
		},
		{
			'key'			: 'category',
			'name'			: '板块',
			'name_field'    : 'title',
			'description'	: '您可在此页面查看专题的板块',
			'ico'			: 'eye',
			'state'			: ['objectRelate',{'objectRelate':'category'}]
		}
	],
	'category'           :
	[
		{
			'key'			: 'detail',
			'name'			: '详情',
			'name_field'    : 'name',
			'description'	: '您可在此页面查看分类的详情',
			'ico'			: 'eye',
			'state'			: ['objectDetail',{}]
		},
		{
			'key'			: 'article',
			'name'			: '文章',
			'name_field'    : 'name',
			'description'	: '您可在此页面查看分类下的文章',
			'ico'			: 'eye',
			'state'			: ['objectRelate',{'objectRelate':'article'}]
		},
		{
			'key'			: 'album',
			'name'			: '相册',
			'name_field'    : 'name',
			'description'	: '您可在此页面查看分类下的相册',
			'ico'			: 'eye',
			'state'			: ['objectRelate',{'objectRelate':'album'}]
		},
		{
			'key'			: 'link',
			'name'			: '链接',
			'name_field'    : 'name',
			'description'	: '您可在此页面查看分类下的链接',
			'ico'			: 'eye',
			'state'			: ['objectRelate',{'objectRelate':'link'}]
		},
		{
			'key'			: 'topic',
			'name'			: '专题',
			'name_field'    : 'name',
			'description'	: '您可在此页面查看分类下的专题',
			'ico'			: 'eye',
			'state'			: ['objectRelate',{'objectRelate':'topic'}]
		}
	],
	'menu'           :
	[
		{
			'key'			: 'detail',
			'name'			: '详情',
			'name_field'    : 'title',
			'description'	: '您可在此页面查看导航的详情',
			'ico'			: 'eye',
			'state'			: ['objectDetail',{}]
		}
	]
}

var relateData = {
	'article_alias':
	{
		'table'    :'article',
		'field'    :'id',
		'r_table'  :'alias',
		'r_field'  :'object_id',
		'r_filter' :{'object':'article'}
	},
	'article_seo':
	{
		'table'    :'article',
		'field'    :'id',
		'r_table'  :'seo',
		'r_field'  :'object_id',
		'r_filter' :{'object':'article'}
	},
	'article_comment':
	{
		'table'    :'article',
		'field'    :'id',
		'r_table'  :'comment',
		'r_field'  :'object_id',
		'r_filter' :{'object':'article','father_id':'0'}
	},
	'album_picture':
	{
		'table'    :'album',
		'field'    :'id',
		'r_table'  :'picture',
		'r_field'  :'album_id',
		'r_filter' :{}
	},
	'topic_category':
	{
		'table'    :'topic',
		'field'    :'id',
		'r_table'  :'topic_category',
		'r_field'  :'topic_id',
		'r_filter' :{}
	},
	'category_article':
	{
		'table'    :'category',
		'field'    :'id',
		'r_table'  :'article',
		'r_field'  :'category_id',
		'r_filter' :{}
	},
	'category_album':
	{
		'table'    :'category',
		'field'    :'id',
		'r_table'  :'album',
		'r_field'  :'category_id',
		'r_filter' :{}
	},
	'category_link':
	{
		'table'    :'category',
		'field'    :'id',
		'r_table'  :'link',
		'r_field'  :'category_id',
		'r_filter' :{}
	},
	'category_topic':
	{
		'table'    :'category',
		'field'    :'id',
		'r_table'  :'topic',
		'r_field'  :'category_id',
		'r_filter' :{}
	}					
}

//自定义操作
var operaData = {};

//列表背景色
var bgColorData = {};
