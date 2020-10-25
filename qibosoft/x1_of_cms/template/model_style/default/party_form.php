<?php
function_exists('query')||die('err');
$data = [];

if(GET_CFG!==true && modules_config('party')){  //避免没有装此模块的时候或者仅获取公共碎片的时候,也执行下面的代码
    $_array = query('party_content',['where'=>['uid'=>login_user('uid')]]);
    foreach($_array AS $rs){
        $data[$rs['id']]=query('party_content'.$rs['mid'],['where'=>['id'=>$rs['id']],'value'=>'title']);
    }
}

return [
	'title'=>'活动报名表单',
	'form'=>[
		['select','ids','请选择主题表单','',$data],
	],
	'type1'=>'',		//wap 或 pc
	'type2'=>'',		//www 或 hy 
	'type3'=>'big',		//这一项是为PC考虑的,WAP不需要考虑,可设置big small 或留空. big代表很宽的 small代表窄边
	'quote'=>'party',    //设置为true发布信息时允许站内引用使用此风格,不允许使用就删除或设置为false,若要指定频道使用的话,就设置频道的目录名,如果即要限频道又要限模型的话,就用类似这样的格式化 cms|3 如果有多个模型或多个频道的话,就用逗号隔开,比如说  cms,bbs,shop|3,2,4
	'forbid_field'=>'rows,by',
];