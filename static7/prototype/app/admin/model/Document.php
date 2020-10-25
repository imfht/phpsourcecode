<?php

namespace app\admin\model;

use app\admin\logic\DocumentArticle;
use think\Model;
use think\Request;
use think\Db;
use think\Validate;
use think\Config;

/**
 * Description of Article
 * 文档模型
 * @author static7
 */
class Document extends Model {

    protected $rule = [
        'title' => "require|max:80",
        'name' => "alphaDash|unique:document,name",
        'description' => 'max:200',
        'level' => 'number',
        'category_id' => 'require|checkCategory',
        'type' => 'number',
        'content' => "require",
        'bookmark' => 'number',
    ];
    protected $message = [
        'title.require' => '标题不能为空',
        'title.max' => '标题不能超过80个字符',
        'name.unique' => '标识已经存在',
        'name.alphaDash' => '标识只能为字母和数字，下划线_及破折号-',
        'description.max' => '描述不能超过200个字符',
        'level.number' => '优先级只能填整数',
        'category_id.require' => '分类不能为空',
        'type.number' => '内容类型不正确',
        'content.require' => '内容不能为空',
        'bookmark.number' => '收藏数只能填整数',
    ];
    protected $autoWriteTimestamp = true; //自动写入创建和更新的时间戳字段
    protected $createTime = false;
    protected $auto = ['status', 'uid'];
    protected $insert = [];
    protected $update = [];

    /**
     * 文章列表
     * @param array $map_tmp 临时条件,后期会合并
     * @param string $field 查询的字段
     * @param string $order 排序 默认id asc
     * @param array $query 额外的条件参数
     * @author staitc7 <static7@qq.com>
     */
    public function lists(array $map_tmp = [], $field = true, string $order = 'id ASC', array $query = []): array {
        $map = array_merge(['status' => ['neq', '-1'], 'category_id' => ['gt', 0]], $map_tmp); //合并条件
        $category_id = is_int($map['category_id']) ?
                checkCategory((int) $map['category_id'], 'list_row', true) :
                Config::get('list_rows'); //分页
        $object = $this::where($map)->order($order)->field($field)->paginate($category_id, false, ['query' => $query]);
        return $object ? array_merge($object->toArray(), ['page' => $object->render()]) : [];
    }

    /**
     * 文章详情
     * @param int $id 文章详情
     * @author staitc7 <static7@qq.com>
     */
    public function detail(int $id = 0) {
        $info = $this::get(function($query)use ($id) {
                    $query->where(['id' => $id, 'status' => ['gt', 0]]);
                });
        if (empty($info)) {
            return $this->error = '文章被禁用或已删除';
        }
        $DocumentArticle = new DocumentArticle();
        $detail = $DocumentArticle->detail($id);
        if ($DocumentArticle->getError()) {
            return $this->error = $DocumentArticle->getError();
        }
        return array_merge($info->data, $detail->data);
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
     * 用户更新或者添加文章
     * @author staitc7 <static7@qq.com>
     */
    public function renew() {
        $data = Request::instance()->post();
        $validate = Validate::make($this->rule, $this->message);
        $validate->extend([
            'checkCategory' => function($value) {
                return checkCategory((int) $value, 'allow_publish') ? true : '该分类不允许发布内容';
            },
        ]);
        if (!$validate->check($data)) {
            return $this->error = $validate->getError(); // 验证失败 输出错误信息
        }
        $article = null;
        foreach ($data as $k => &$v) {
            if (in_array($k, ['parse', 'content', 'template', 'bookmark', 'keywords', 'file_id', 'download', 'size'])) {
                $article[$k] = $v;
                unset($data[$k]);
            }
        }
        $object = (int) $data['id'] ? $this::update($data) : $this::create($data);
        if ($object) {
            $article['id'] = (int)$data['id'] ? $object->id : $object->getLastInsID();
            $DocumentArticle = new DocumentArticle();
            $content = $DocumentArticle->renew($article);
            if ($DocumentArticle->getError()) {
                $data['id'] || $this::destroy($data['id']); //新增失败，删除基础数据
                return $this->error = $DocumentArticle->getError();
            }
        }
        return $content ? $object->data : false;
    }

    /**
     * 生成推荐位的值
     * @return number 推荐位
     * @author huajie <banhuajie@163.com>
     */
    private function getPosition(int $position = 0) {
        if (!is_array($position)) {
            return 0;
        }
        $pos = 0;
        foreach ($position as $key => $value) {
            $pos += $value;  //将各个推荐位的值相加
        }
        return $pos;
    }

    /* ===================获取器======================== */

    public function getTypeAttr($value) {
        $status = Config::get('document_model_type');
        return $status[$value];
    }

    /* ===================自动完成====================== */

    protected function setUidAttr() {
        return is_login() ?? 0;
    }

    protected function setDescriptionAttr($value) {
        return htmlspecialchars($value);
    }

    protected function setCreateTimeAttr($value) {
        return $value ? strtotime($value) : Request::instance()->time();
    }

    protected function setDeadlineAttr($value) {
        return $value ? strtotime($value) : 0;
    }

    protected function setPositionAttr($value) {
        $pos = 0;
        if (is_array($value)) {
            foreach ($value as $key => $value) {
                $pos += $value;  //将各个推荐位的值相加
            }
        }
        return $pos;
    }

    protected function setStatusAttr() {
        $Request = Request::instance();
        $id = $Request->post('id');
        $check = checkCategory((int) $Request->post('category_id') ?? 0, 'check');
        if (empty($id)) {//新增
            $status = $check ? 2 : 1;
        } else {
            $status = $this::where('id', $id)->value('status');
            if ((int) $status === 3) {//编辑草稿改变状态           
                $status = $check ? 2 : 1;
            }
        }
        return $status;
    }

}
