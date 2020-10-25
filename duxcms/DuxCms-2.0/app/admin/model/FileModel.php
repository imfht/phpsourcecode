<?php
namespace app\admin\model;
use app\base\model\BaseModel;
/**
 * 文件操作
 */
class FileModel extends BaseModel {
    //完成
    protected $_auto = array (
        array('time','time',3,'function'),
     );

    /**
     * 上传数据
     * @return array 文件信息
     */
    public function uploadData()
    {
        $upload = target('base/Upload');
        $config = array();
        $config['DIR_NAME'] = date('Y-m-d');
        $data = $upload->upload($config);
        if(!$data){
            $this->error = $upload->getError();
            return false;
        }
        $this->add($data);
        return $data;
    }

}
