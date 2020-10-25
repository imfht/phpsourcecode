<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

/**
 * 回收站逻辑
 */
class Trash extends AdminBase
{

    /**
     * 获取回收站列表
     */
    public function getTrashList()
    {

        $list = [];

        $trash_config = parse_config_array('trash_config');


        foreach ($trash_config as $k => $v) {

            $temp = [];

            $temp['name'] = $k;

            $temp['title'] = $k;

            $temp['number'] = $this->setname($k)->getStat([DATA_COMMON_STATUS => DATA_DELETE]);

            $data = $this->query('SHOW TABLE STATUS LIKE "' . DB_PREFIX . camelcase2underline($k) . '"');

            $data = array_map('array_change_key_case', $data);
            if (!empty($data)) {
                if ($data[0]["comment"] != '') {

                    $temp['model_path'] = $data[0]["comment"];

                } else {

                    $temp['model_path'] = '暂无注释信息';

                }
            } else {

                continue;
            }

            $list[] = $temp;

        }

        return $list;
    }

    /**
     * 获取回收站数据列表
     */
    public function getTrashDataList($model_name = '')
    {

        $trash_config = parse_config_array('trash_config');

        $dynamic_field = $trash_config[$model_name];

        $field = 'id,' . TIME_CT_NAME . ',' . TIME_UT_NAME . ',' . $dynamic_field;

        $list = $this->setname($model_name)->getDataList([DATA_COMMON_STATUS => DATA_DELETE], $field, 'id desc', 0, '', '', '', false);

        return compact('list', 'dynamic_field', 'model_name');
    }

    /**
     * 彻底删除数据
     */
    public function trashDataDel($model_name = '', $id = 0)
    {


        return $this->setname($model_name)->dataDel(['id' => $id], '删除成功', true);

    }

    /**
     * 恢复数据
     */
    public function restoreData($model_name = '', $id = 0)
    {


        return $this->setname($model_name)->setDataValue(['id' => $id], 'status', 1, '', '数据恢复成功');
    }

}
