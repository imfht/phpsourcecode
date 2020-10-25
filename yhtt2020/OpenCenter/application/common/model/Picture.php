<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: lin(lt@ourstu.com)
 * Date: 2018/9/20
 * Time: 13:23
 * ----------------------------------------------------------------------
 */
namespace app\common\model;

use think\Model;

class Picture extends Model
{
    protected $table = COMMON . 'picture';

    protected $autoWriteTimestamp = true;

    /**
     * @param $info
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author:lin(lt@ourstu.com)
     */
    public function upload($info){
        $md5 = $info->md5();
        $sha1 = $info->sha1();
        $img = $this::where(['md5'=>$md5,'sha1'=>$sha1])->find();
        if(!empty($img)){
            return $img['id'];
        }
        $data['type'] = 'local';
        $data['path'] = '/uploads/'.$info->getSaveName();
        $data['md5'] = $md5;
        $data['sha1'] = $sha1;
        $size = getimagesize($info->getPathname());
        $data['width'] = $size['0'];
        $data['height'] = $size['1'];
        $data['status'] = 1;
        $this->save($data);
        return $this->id;
    }
}