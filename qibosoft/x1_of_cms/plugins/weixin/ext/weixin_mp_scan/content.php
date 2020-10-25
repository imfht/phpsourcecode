<?php
if (preg_match("/^qb-([a-z]+)-([\d]+)-([\d]*)$/", $data['keyword'],$array)) {
    $dirname = $array[1];
    $id = $array[2];
    $uid = $array[3];
    if (modules_config($dirname)) {
        $url = get_url(urls($dirname.'/content/show',['id'=>$id,'p_uid'=>$uid]));
        $class = "app\\{$dirname}\\model\\Content";;
        $obj = new $class;
        $info = $obj->getInfoByid($id,true);
        return [
            'title'=>$info['title'],
            'picurl'=>$info['picurl']?tempdir($info['picurl']):'',
            'about'=>get_word(del_html($info['content']),150),
            'url'=>get_url(iurl($dirname.'/content/show',['id'=>$id,'p_uid'=>$uid])),
        ];
    }elseif(plugins_config($dirname)){
        $url = get_url(purl($dirname.'/content/show',['id'=>$id,'p_uid'=>$uid]));
        return "<a href=\"$url\">请点击进入活动页</a>";
    }
}
