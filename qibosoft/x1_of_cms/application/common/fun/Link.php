<?php
namespace app\common\fun;

/**
 * 链接用到的相关函数
 */
class Link{
    
    /**
     * 带下拉菜单的链接，比如
     * $code = fun('link@more',"<i class='fa fa-gears'>相关操作</i>",[
		                    '通过审核'=>urls('pass',['id'=>$value,'status'=>1]),
		                    '拒绝通过'=>urls('pass',['id'=>$value,'status'=>-1]),
		                ]);
     * @param string $title
     * @param array $link_array
     * @return string
     */
    public static function more($title='',$link_array=[]){
        $str = '';
        foreach ($link_array AS $name=>$link){
            if (is_array($link)) {
                $str .= "<a class='more_links' target=\"{$link['target']}\" href=\"{$link['url']}\">{$name}</a>";;
            }else{
                $str .= "<a class='more_links' href=\"javascript:layer.confirm('你确定要{$name}？', { btn: ['确定', '取消'] },function(){ window.location.href='{$link}' });\">{$name}</a>";
            }            
        }
        $code = "<style type='text/css'>
.more_links{
	font-size:16px;
	background:#fff;
	color:rgb(15, 166, 216) !important;
	padding:3px 8px 3px 8px;
	border-radius:3px;
	margin:5px 0 15px 0;
	display:block;
    text-align:center;
}			
</style>
<a href='javascript:' title='请点击选择相应选项！' onclick=\"layer.tips($(this).next().html(), $(this), {tips: [3, '#0FA6D8'],tipsMore: false,time:5000 });\">{$title}</a>
<div style='display:none;'>{$str}
</div>";
        return $code;
    }
}
