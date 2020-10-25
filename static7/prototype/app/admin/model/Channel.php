<?php

/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\admin\model;

use think\Model;
use think\Request;
use think\Validate;
use think\Config;

/**
 * Description of Channel
 *
 * @author static7
 */
class Channel extends Model {

    protected $rule = ['title' => "require|max:30", 'url' => "require",];
    protected $msg = ['title.require' => '标题不能为空', 'title.max' => '标题最多不能超过30个字符', 'url.require' => '链接不能为空',];
    protected $auto = ['title', 'url'];
    protected $insert = ['status' => 1,];

    /**
     * 导航列表
     * @param int $pid 父级ID
     * @author staitc7 <static7@qq.com>
     * @return array
     */

    public function channelList(int $pid = 0): array {
        $map = ['pid' => $pid > 0 ? $pid : 0, 'status' => ['neq', -1]];
        $object = $this::where($map)->order('sort asc,id asc')->paginate(Config::get('list_rows') ?? 10);
        return $object ? array_merge($object->toArray(), ['page' => $object->render()]) : [];
    }

    /**
     * 查询父级导航
     * @param int $pid 父级导航ID
     * @author staitc7 <static7@qq.com>
     * @return array
     */

    public function father(int $pid = 0): array {
        $object = $this::get(function ($query) use ($pid) {
            $query->where('id', $pid)->field('pid,title');
        });
        return $object ? $object->toArray() : [];
    }

    /**
     * 查询菜单详情
     * @param int $id 菜单详情
     * @author staitc7 <static7@qq.com>
     * @return array
     */

    public function edit(int $id = 0): array {
        $object = $this::get(function ($query) use ($id) {
            $query->where('id', $id);
        });
        return $object ? $object->toArray() : [];
    }

    /**
     * 用户更新或者添加导航
     * @author staitc7 <static7@qq.com>
     */

    public function renew() {
        $data = Request::instance()->post();
        $validate = Validate::make($this->rule, $this->msg);
        if (!$validate->check($data)) {
            // 验证失败 输出错误信息
            return $this->error = $validate->getError();
        }
        $object = (int)$data['id'] ? $this::update($data) : $this::create($data);
        return $object ? $object->toArray() : null;
    }

    /**
     * 修改状态
     * @param int|array $map 数据的ID或者ID组
     * @param array $data 要修改的数据
     * @author staitc7 <static7@qq.com>
     * @return bool|int|string
     */

    public function setStatus($map = null, $data = null) {
        if (empty($map) || empty($data)) {
            return false;
        }
        return $this::where($map)->update($data);
    }

    /**
     * 配置名称过滤
     * @param string $value 值
     * @author staitc7 <static7@qq.com>
     * @return string
     */

    protected function setTitleAttr($value) {
        return htmlspecialchars($value);
    }

}
