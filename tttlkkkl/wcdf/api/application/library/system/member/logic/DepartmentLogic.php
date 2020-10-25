<?php

/**
 * 部门本地curd操作类
 * Date: 16-10-24
 * Time: 下午3:57
 * author :李华 yehong0000@163.com
 */
namespace system\member\logic;

use tool\check;

class DepartmentLogic
{
    protected static $Obj;
    protected        $DbModel;
    private          $record;

    private function __construct()
    {
        $this->DbModel = db('department');
    }

    /**
     * @return DepartmentLogic
     */
    static public function getInstance()
    {
        if (!self::$Obj) {
            self::$Obj = new self();
        }
        return self::$Obj;
    }

    /**
     * 添加
     *
     * @param $data
     *
     * @throws \Exception
     */
    public function post($data)
    {
        $parentData = [];
        $insertData = $this->checkData($data, $parentData);
        //数据重复性检查
        $map = [
            'c_id'     => CID,
            'parentid' => $insertData['parentid'],
            'name'     => $insertData['name']
        ];
        if ($this->DbModel->where($map)->field('id')->find()) {
            throw new \Exception('已存在相同的部门!', 4005);
        }
        if ($insertData['parentid'] == 0) {
            $map['parentid'] = $insertData['parentid'];
            unset($map['id']);
            if ($this->DbModel->where($map)->field('id')->find()) {
                throw new \Exception('根部门只能添加一个！', 4002);
            }
        }
        $insertData['create_time'] = time();
        $this->DbModel->transaction();
        try {
            if (!$insertData['id']) {
                $id = $this->DbModel->field('max(id) as maxID')->where(['c_id' => CID])->find()['maxID'];
                $id = $id ? $id + 1 : 1;
                $insertData['id'] = $id;
            }
            if (!$insertData['parentid']) {
                $up = 1;
            } else {
                $up = $this->DbModel
                    ->where('rgt', '>=', $parentData['rgt'])
                    ->whereOr('lft', '>=', $parentData['rgt'])
                    ->fetchSql(true)
                    ->update([
                        'lft' => ['exp', 'lft + 2'],
                        'rgt' => ['exp', 'rgt + 2']
                    ]);
            }
            //一定要先更新
            $ins = $this->DbModel->insert($insertData);
            if ($ins && $up) {
                $this->DbModel->commit();
                return $id;
            } else {
                $this->DbModel->rollback();
                throw new \Exception('添加失败', 4003);
            }
        } catch (\Exception $E) {
            $this->DbModel->rollback();
            throw new \Exception('插入失败，系统错误！', 5001);
        }
    }

    public function put($data)
    {
        $updateData = $this->checkData($data, $parentData);
        //数据重复性检查
        $map = [
            'c_id'     => CID,
            'parentid' => $updateData['parentid'],
            'id'       => ['neq', $updateData['id']],
            'name'     => $updateData['name']
        ];
        if ($this->DbModel->where($map)->field('id')->find()) {
            throw new \Exception('已存在相同的部门!', 4005);
        }
        //移动部门位置
        if ($updateData['parentid'] > 0 && $updateData['parentid'] != $parentData['id']) {
            throw new \Exception('暂不支持部门移动', 40014);
            /**
             * $newParentData=$this->getRecordById($updateData['parentid']);
             * if(!$newParentData){
             * throw new \Exception('不存在的父级部门',4011);
             * }
             * if($newParentData['lft']>$updateData['lft'] && $newParentData['rgt']<$updateData['rgt']){
             * throw new \Exception('非法移动,不能移动到子部门下!',4012);
             * }
             * */
        }
        $upMap = [
            'c_id' => CID,
            'id'   => $updateData['id']
        ];
        $up = $this->DbModel->where($upMap)->update($updateData);
        if ($up) {
            return true;
        } else {
            throw new \Exception('修改失败!', 40015);
        }
    }

    /**
     * 获取一条记录
     *
     * @param $id
     *
     * @return mixed
     */
    private function getRecordById($id)
    {
        if (!isset($this->record[$id])) {
            $map = [
                'c_id'        => CID,
                'id'          => $id,
                'delete_time' => 0
            ];
            $this->record[$id] = $this->DbModel->where($map)->find();
        }
        return $this->record[$id];
    }

    /**
     * 数据验证
     *
     * @param $data
     *
     * @return array
     * @throws \Exception
     */
    private function checkData($data, &$parentData)
    {
        if (isset($data['parentid']) && !(is_numeric($data['parentid']) && $data['parentid'] >= 0)) {
            throw new \Exception('parentid 非法!', 4004);
        }
        $data['parentid'] = intval($data['parentid']);
        $insertData = [
            'name'        => check::checkStrLen($data['name'], 1, 32, '部门名称'),
            'parentid'    => $data['parentid'],
            'order'       => (is_int($data['order']) && $data['order'] >= 0) ? $data['order'] : 0,
            'c_id'        => CID,
            'update_time' => date('Y-m-d H:i:s')
        ];
        if ($data['id'] && is_numeric($data['id'])) {
            $insertData['id'] = intval($data['id']);
        }
        if (preg_match('/[\\|:|*|?|"|<|>|\|]/', $data['name'])) {
            throw new \Exception('部门名称包含非法字符！', 4001);
        }
        $map = [
            'c_id'        => CID,
            'id'          => $insertData['parentid'],
            'delete_time' => 0
        ];
        $parentData = $insertData['parentid'] ? $this->DbModel->where($map)->find() : 1;
        if (!$parentData && $insertData['parentid']) {
            throw new \Exception('不存在的父级部门!', 4003);
        }
        if ($insertData['parentid'] == 0) {
            $insertData['lft'] = $insertData['rgt'] = $insertData['depth'] = 1;
        } else {
            $insertData['lft'] = $parentData['rgt'];
            $insertData['rgt'] = $parentData['rgt'] + 1;
            $insertData['depth'] = $parentData['depth'] + 1;
        }
        return $insertData;
    }

    /**
     * 删除数据
     *
     * @param $id
     *
     * @return bool
     * @throws \Exception
     * @throws \think\Exception
     */
    public function delete($id)
    {
        $id = intval($id);
        if ($id <= 0) {
            throw new \Exception('参数错误', 3001);
        }
        $map = [
            'c_id'        => CID,
            'id'          => $id,
            'delete_time' => 0
        ];
        $record = $this->getRecordById($id);
        if ($record) {
            $this->DbModel->transaction();
            //如果不是根节点
            if($record['lft'] !=1){
                $up = $this->DbModel
                    ->where('rgt', '>=', $record['rgt'])
                    ->whereOr('lft', '>=', $record['rgt'])
                    ->fetchSql(true)
                    ->update([
                        'lft' => ['exp', 'lft - 2'],
                        'rgt' => ['exp', 'rgt - 2']
                    ]);
            }else{
                $up=1;
            }
            if ($up && $this->DbModel->where($map)->update(['delete_time' => time()])) {
                $this->DbModel->commit();
                return true;
            } else {
                $this->DbModel->rollback();
                throw new \Exception('资源不存在或删除失败', 4033);
            }
        }else{
            throw new \Exception('资源不存在',4032);
        }
    }

    /**
     * 获取部门详情
     * @param $id
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($id){
        $id = intval($id);
        if ($id <= 0) {
            throw new \Exception('参数错误', 3001);
        }
        return $this->getRecordById($id);
    }
}