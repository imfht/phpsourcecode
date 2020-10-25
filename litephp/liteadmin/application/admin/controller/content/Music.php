<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/11
 * Time: 15:33
 */

namespace app\admin\controller\content;

use app\common\controller\BaseAdmin;
use app\common\model\content\Music as MusicModel;
use LiteAdmin\Music163;

/**
 * @title 音乐管理
 * Class Music
 * @package app\admin\controller\content
 */
class Music extends BaseAdmin
{
    /**
     * @title 列表页
     * @return mixed
     */
    public function index()
    {
        return $this->_list(new MusicModel());
    }

    /**
     * @title 添加歌曲
     * @return array|mixed|null|\PDOStatement|string|\think\Model
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        return $this->_form(new MusicModel(), 'form');
    }

    /**
     * 添加前置
     * @param $data
     */
    protected function _add_form_before(&$data){
        if ($this->request->isPost()){
            $music_id = $data['music_id'];
            unset($data['music_id']);

            $data = Music163::getMusic($music_id);
        }
    }

    /**
     * @title 删除歌曲
     */
    public function del()
    {
        $ids = $this->request->post('ids',false);
        !$ids && $this->error("缺少参数！");

        $this->_del(new MusicModel(),$ids);
    }
}