<?php
namespace app\chat\controller;

use app\chat\model\ChatGroup;
use app\chat\model\ChatCluster;
use think\App;

class Chat extends Base {
    protected $group = null;
    protected $cluster = null;
    public function __construct(App $app = null){
        parent::__construct($app);
        $this->cluster = new ChatCluster;
        $this->group = new ChatGroup;
    }

    /**
     * 获取用户
     * @param int $id
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMembers($id=0){
        if(!$id){
            return $this->__e('群组id不能为空');
        }
        $list = $this->cluster->getGroupsFriendsById($id);
        if(!$list){
            return $this->__e('未添加任何组员');
        }
        return $this->__s('查询成功',$list);
    }
}