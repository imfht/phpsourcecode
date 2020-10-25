<?php
namespace app\duxcms\model;
use app\base\model\BaseModel;
/**
 * 扩展字段数据操作
 */
class FieldDataModel extends BaseModel {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where,$limit = 0,$order='data_id DESC'){
        return  $this->where($where)->limit($limit)->order($order)->select();
    }

    /**
     * 获取数量
     * @return array 数量
     */
    public function countList($where){
        return $this->where($where)->count();
    }

    /**
     * 获取信息
     * @param int $dataId ID
     * @return array 信息
     */
    public function getInfo($dataId)
    {
        $map = array();
        $map['data_id'] = $dataId;
        return $this->where($map)->find();
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @param array $fieldsetInfo 字段集信息
     * @param bool $prefix POST前缀
     * @return bool 更新状态
     */
    public function saveData($type = 'add' , $fieldsetInfo){
        if(is_array($fieldsetInfo)){
            $fieldsetId = $fieldsetInfo['fieldset_id'];
        }else{
            $fieldsetId = $fieldsetInfo;
        }
        //获取字段列表
        $where = array();
        $where['fieldset_id'] = $fieldsetId;
        $fieldList=target('duxcms/Field')->loadList($where);
        if(empty($fieldList)||!is_array($fieldList)){
            return;
        }
        //设置数据列表
        $valiRules = array();
        $autoRules = array();
        $data = array();
        foreach ($fieldList as $value) {
            $data[$value['field']] = request('post.Fieldset_'.$value['field']);
            $verify_data = base64_decode($value['verify_data']);
            if($verify_data){
                $errormsg = $value['errormsg'];
                if(empty($errormsg)){
                    $errormsg = $value['name'].'填写不正确！';
                }
                $valiRules[] = array($value['field'], $verify_data ,$errormsg,$value['verify_condition'],$value['verify_type'],3);
            }
            $autoRules[] = array($value['field'],'formatField',3,'callback',array($value['field'],$value['type']));
        }

        $data = $this->auto($autoRules)->validate($valiRules)->create($data);
        if(!$data){
            return false;
        }
        $data['data_id'] = request('post.data_id');
        if($type == 'add'){
            return $this->add($data);
        }
        if($type == 'edit'){
            if(empty($data['data_id'])){
                return false;
            }
            $where = array();
            $where['data_id'] = $data['data_id'];
            $status = $this->where($where)->save();
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 删除信息
     * @param int $dataId ID
     * @return bool 删除状态
     */
    public function delData($dataId)
    {
        $map = array();
        $map['data_id'] = $dataId;
        return $this->where($map)->delete();
    }

    /**
     * 格式化字段信息
     * @param string $field 字段名
     * @param int $type 字段类型
     * @return 格式化后数据
     */
    public function formatField($field,$type)
    {
        $data = $_POST['Fieldset_'.$field];
        switch ($type) {
            case '2':
            case '3':
                return $data;
                break;
            case '6':
                $fileData=array();
                if(is_array($data)){
                    foreach ($data['url'] as $key => $value) {
                        $fileData[$key]['url'] = $value;
                        $fileData[$key]['title'] = $data['title'][$key];
                    }
                    return serialize($fileData);
                }
                break;
            case '7':
            case '8':
                return intval($data);
                break;
            case '10':
                if(!empty($data)){
                    return strtotime($data);
                }else{
                    return time();
                }
                break;
            case '9':
                if(!empty($data)&&is_array($data)){
                    return implode(',',$data);
                }
                break;
            default:
                return request('post.Fieldset_'.$field);
                break;
        }

    }

    /**
     * 还原字段信息
     * @param string $field 字段名
     * @param int $type 字段类型
     * @param int $type 字段配置信息
     * @return 还原后数据
     */
    public function revertField($data,$type,$config)
    {
        switch ($type) {
            case '6':
                //文件列表
                if(empty($data)){
                    return ;
                }
                $list=unserialize($data);
                return $list;
                break;
            case '7':
            case '8':
                if(empty($config)){
                    return $data;
                }
                $list = explode(",",trim($config));
                $listData = array();
                $i = 0;
                foreach ($list as $value) {
                    $i++;
                    $listData[$i] =  $value;
                }
                return array(
                        'list' => $listData,
                        'value' => intval($data),
                        );
                break;
            case '9':
                if(empty($config)){
                    return $data;
                }
                $list = explode(",",trim($config));
                $listData = array();
                $i = 0;
                foreach ($list as $value) {
                    $i++;
                    $listData[$i] =  $value;
                }
                return array(
                        'list' => $listData,
                        'value' => explode(",",trim($data)),
                        );
                break;
            case '11':
                return number_format($data,2);
                break;
            default:
                return html_out($data);
                break;
        }

    }


    /**
     * 字段列表显示
     * @param int $type 字段类型
     * @return array 字段类型列表
     */
    public function showListField($data,$type,$config)
    {
        switch ($type) {
            case '5':
                if($data){
                    return '<img name="" src="'.$data.'" alt="" style="max-width:170px; max-height:90px;" />';
                }else{
                    return '无';
                }
                break;
            case '6':
                //文件列表
                if(empty($data)){
                    return '无';
                }
                $list=unserialize($data);
                $html='';
                if(!empty($list)){
                    foreach ($list as $key => $value) {
                        $html.=$value['url'].'<br>';
                    }
                }
                return $html;
                break;
            case '7':
            case '8':
                if(empty($config)){
                    return $data;
                }
                $list=explode(",",trim($config));
                foreach ($list as $key => $vo) {
                    if($data==intval($key)+1){
                        return $vo;
                    }
                }
                break;
            case '9':
                if(empty($config)){
                    return $data;
                }
                $list = explode(",",trim($config));
                $newList = array();
                $i = 0;
                foreach ($list as $value) {
                    $i++;
                    $newList[$i] =  $value;
                }
                $data = explode(",",trim($data));
                $html='';
                foreach ($data as $key => $vo) {
                    $html.=' '.$newList[$vo].' |';

                }
                return substr($html,0,-1);
                break;
            case '10':
                return date('Y-m-d H:i:s',$data);
                break;
            case '11':
                return number_format($data,2);
                break;
            default:
                return $data;
                break;
        }
    }

    

}
