<?php

namespace Admin\Model;

use Admin\Lib\AdminPage;
use CigoAdminLib\Lib\Admin;
use Think\Model;

class TrashModel extends Model
{
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function')
    );

    public function getList()
    {
        $page = new AdminPage($this->count(), Admin::DATA_LIST_SIZE);
        $data_list = $this->limit($page->firstRow, $page->listRows)->order('create_time desc')->select();
        if ($data_list) {
            foreach ($data_list as $key => $item) {
                $data_list[$key]['tip_type'] = $this->getTrashDataTypeTip($item['type']);
                $data_list[$key]['url_edit'] = $this->getTrashDataEditUrl($item);
                $data_list[$key]['tip_del_time'] = time_format($item['create_time']);
            }

            return array(
                'showPage' => $page->show(),
                'dataList' => $data_list
            );
        } else {
            return false;
        }
    }

    /**
     * 获取回收站数据类型提示
     * @param int $type
     * @return string
     */
    private function getTrashDataTypeTip($type = 0)
    {
        $typeName = '';
        if (empty($type))
            return $typeName;

        switch ($type) {
            case Admin::DATA_TYPE_MENU_ADMIN:
                $typeName = '后台菜单';
                break;
            case Admin::DATA_TYPE_EDIT_DEMO:
                $typeName = '样例代码-编辑Demo';
                break;
        }

        return $typeName;
    }

    /**
     * 获取回收站数据编辑Url
     * @param null $data
     * @return string
     */
    private function getTrashDataEditUrl($data = null)
    {
        $url = '';
        if (!$data)
            return $url;

        switch ($data['type']) {
            case Admin::DATA_TYPE_MENU_ADMIN:
                $url = U('Admin/AdminMenu/edit', array('id' => $data['data_id']));
                break;
            case Admin::DATA_TYPE_EDIT_DEMO:
                $url = U('Admin/EditDemo/edit', array('id' => $data['data_id']));
                break;
        }

        return $url;
    }

}
