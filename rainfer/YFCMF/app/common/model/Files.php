<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Model;

/**
 * 本地附件模型
 * @Author: rainfer <rainfer520@qq.com>
 */
class Files extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * 增加
     *
     * @param array $data 数据
     *
     * @return mixed
     */
    public function add($data)
    {
        return self::insertGetId($data);
    }

    /**
     * 修改
     *
     * @param array $where 条件
     * @param array $data  数据
     *
     * @return mixed
     */
    public function edit($where, $data)
    {
        return self::where($where)->update($data);
    }
}
