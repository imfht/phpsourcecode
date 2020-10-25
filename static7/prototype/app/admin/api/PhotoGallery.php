<?php

namespace app\admin\api;

use think\Model;
use think\Config;
use think\Request;

/**
 * Description of PhotoGallery
 * 相册类
 * @author static7
 */
class PhotoGallery extends Model {

    /**
     * 文件上传
     * @param  array  $file_name   要上传的文件名称
     * @param  array  $tmp_config  临时上传配置
     * @param  string $driver  上传名称（后期完善）
     * @return array           文件上传成功后的信息
     * @author staitc7 <static7@qq.com>

     */
    public function upload($file_name = null, $tmp_config = null, $driver = 'local') {
        $config = empty($tmp_config) ? Config::get('picture_path') : $tmp_config;
        $Request = Request::instance();
        $file = $Request->file($file_name);
        if (is_array($file)) {
            $data = $this->arrayProcess($file, $config, $driver);
        } else {
            $data = $this->oneProcess($file, $config, $driver);
        }
        return $data;
    }

    /**
     * 数组处理文件
     * @param array $file 处理的数据
     * @param  array  $config  上传配置
     * @param  string $driver  上传名称（后期完善）
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    private function arrayProcess($file, $config, $driver) {
        $ids = [];
        foreach ($file as $k => $v) {
            $info = $v->rule('uniqid')->move(getcwd() . $config);
            if (!$info) {
                return $file->getError(); // 上传失败获取错误信息
            }
            $data = [
                'user_id' => is_login(),
                'group' => 0,
                'status' => 1,
                'path' => $config . $info->getBasename(),
                'create_time' => $info->getATime(),
            ];
            $object = $this::create($data);
            $ids[] = $object ? $object->toArray()['id'] : 0;
        }
        return empty($ids) ? 0 : ['status' => 1, 'info' => $ids];
    }

    /**
     * 单个文件处理
     * @param array $file 处理的数据
     * @param  array  $config  上传配置
     * @param  string $driver  上传名称（后期完善）
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    private function oneProcess($file, $config, $driver) {
        $info = $file->rule('uniqid')->move(getcwd() . $config);
        if (!$info) {
            return $file->getError(); // 上传失败获取错误信息
        }
        $data = [
            'user_id' => is_login(),
            'group' => 0,
            'status' => 1,
            'path' => $config . $info->getBasename(),
            'create_time' => $info->getATime(),
        ];
        $object = $this::create($data);
        $id = $object->toArray();
        return $object ? ['status' => 1, 'info' => $id['id']] : 0;
    }

    /**
     * 相册列表
     * @param array $map_tmp 临时条件,后期会合并
     * @param string $field 查询的字段
     * @param string $order 排序 默认id asc
     * @author staitc7 <static7@qq.com>
     */
    public function photoGalleryList(array $map_tmp = [], $field = true, string $order = 'id ASC'): array {
        $map = array_merge(['status' => ['neq', '-1']], $map_tmp);
        $object = $this::where($map)->order($order)->field($field)->paginate(Config::get('list_rows') ?? 10);
        return $object ? array_merge($object->toArray(), ['page' => $object->render()]) : [];
    }

}
