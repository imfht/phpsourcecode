<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\taglib;

use esclass\TagLib;
use app\common\logic\Common as LogicCommon;

class Common extends TagLib
{
    /**
     * 定义标签列表
     */
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'topiclist'     => ['attr' => 'length,order,cateid,limit,settop,choice,uid,focus', 'close' => 1],
        'topiclistpage' => ['attr' => 'length,cateid,settop,choice,uid,focus', 'close' => 0],
        'htlist'        => ['attr' => 'length,order,cateid,limit,cover,choice,uid,focus', 'close' => 1],
        'htlistpage'    => ['attr' => 'length,cateid,cover,choice,uid,focus', 'close' => 0],
        'userlist'      => ['attr' => 'length,order,grades,limit,status,leaderid,inside,rz,focus,isfocus', 'close' => 1],
        'userlistpage'  => ['attr' => 'length,grades,status,leaderid,inside,rz,focus,isfocus', 'close' => 0],
    ];

    public function tagUserlistpage($tag)
    {


        $n   = ['length', 'grades', 'status', 'leaderid', 'inside', 'rz', 'focus', 'isfocus'];
        $map = strapiarr($n, $tag['length'], $tag['grades'], $tag['status'], $tag['leaderid'], $tag['inside'], $tag['rz'], $tag['focus'], $tag['isfocus']);


        $parse = '<?php ';
        $parse .= '$map=' . $map . ';';
        $parse .= '$commonLogic = get_sington_object("commonLogic", LogicCommon::class); ';
        $parse .= 'echo $artlist=$commonLogic->getuserlistpage($map);';

        $parse .= ' ?>';


        return $parse;
    }

    /**
     * 这是一个非闭合标签的简单演示
     */
    public function tagUserlist($tag, $content)
    {


        $name = $tag['name']; // name是必填项，这里不做判断了


        $n   = ['limit', 'length', 'order', 'grades', 'status', 'leaderid', 'inside', 'rz', 'focus', 'isfocus'];
        $map = strapiarr($n, $tag['limit'], $tag['length'], $tag['order'], $tag['grades'], $tag['status'], $tag['leaderid'], $tag['inside'], $tag['rz'], $tag['focus'], $tag['isfocus']);


        $parse = '<?php ';
        $parse .= '$map=' . $map . ';';
        $parse .= '$commonLogic = get_sington_object("commonLogic", LogicCommon::class); ';
        $parse .= '$artlist=$commonLogic->getuserlist($map);';

        $parse .= ' ?>';
        $parse .= '{volist name="artlist" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';


        return $parse;
    }

    public function tagHtlistpage($tag)
    {


        $n   = ['length', 'cover', 'choice', 'cateid', 'uid', 'focus'];
        $map = strapiarr($n, $tag['length'], $tag['cover'], $tag['choice'], $tag['cateid'], $tag['uid'], $tag['focus']);


        $parse = '<?php ';
        $parse .= '$map=' . $map . ';';
        $parse .= '$commonLogic = get_sington_object("commonLogic", LogicCommon::class); ';
        $parse .= 'echo $artlist=$commonLogic->gethtpage($map);';

        $parse .= ' ?>';


        return $parse;
    }

    /**
     * 这是一个非闭合标签的简单演示
     */
    public function tagHtlist($tag, $content)
    {


        $name = $tag['name']; // name是必填项，这里不做判断了


        $n   = ['limit', 'length', 'order', 'cover', 'choice', 'cateid', 'uid', 'focus'];
        $map = strapiarr($n, $tag['limit'], $tag['length'], $tag['order'], $tag['cover'], $tag['choice'], $tag['cateid'], $tag['uid'], $tag['focus']);


        $parse = '<?php ';
        $parse .= '$map=' . $map . ';';
        $parse .= '$commonLogic = get_sington_object("commonLogic", LogicCommon::class); ';
        $parse .= '$artlist=$commonLogic->gethtlist($map);';

        $parse .= ' ?>';
        $parse .= '{volist name="artlist" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';


        return $parse;
    }

    public function tagTopiclistpage($tag)
    {


        $n   = ['length', 'settop', 'choice', 'cateid', 'uid', 'focus'];
        $map = strapiarr($n, $tag['length'], $tag['settop'], $tag['choice'], $tag['cateid'], $tag['uid'], $tag['focus']);

        $parse = '<?php ';
        $parse .= '$map=' . $map . ';';
        $parse .= '$commonLogic = get_sington_object("commonLogic", LogicCommon::class); ';
        $parse .= 'echo $artlist=$commonLogic->gettopicpage($map);';

        $parse .= ' ?>';


        return $parse;
    }

    /**
     * 这是一个非闭合标签的简单演示
     */
    public function tagTopiclist($tag, $content)
    {


        $name = $tag['name']; // name是必填项，这里不做判断了


        $n   = ['limit', 'length', 'order', 'settop', 'choice', 'cateid', 'uid', 'focus'];
        $map = strapiarr($n, $tag['limit'], $tag['length'], $tag['order'], $tag['settop'], $tag['choice'], $tag['cateid'], $tag['uid'], $tag['focus']);


        $parse = '<?php ';
        $parse .= '$map=' . $map . ';';
        $parse .= '$commonLogic = get_sington_object("commonLogic", LogicCommon::class); ';
        $parse .= '$artlist=$commonLogic->gettopiclist($map);';

        $parse .= ' ?>';
        $parse .= '{volist name="artlist" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';


        return $parse;
    }

}