<?php
namespace app\common\traits;
//use app\common\builder\Form;
use app\index\model\Labelhy AS LabelModel;
//use app\common\traits\AddEditList;

trait LabelhyEdit {
    use LabelEdit;
    
    /**
     * 检查是否有设置当前圈子黄页的权限
     * @return boolean
     */
    protected function check_power(){
        $info = input('hy_id')?fun('qun@getByid',input('hy_id')):[];
        if ( ($info['uid'] && $info['uid']==$this->user['uid']) || defined('LABEL_SET') && LABEL_SET===true ) {
            return true;
        }
    }
    
    /**
     * 自动生成表格
     * @param unknown $info
     * @param unknown $tab_items
     * @return mixed|string
     */
    protected function get_form_table($info,$tab_items) {
        
        if($this->tab_ext['template']){
            $this->tab_ext['template'] = TEMPLATE_PATH.$this->tab_ext['template'].'.'.config('template.view_suffix');
            if (!is_file($this->tab_ext['template'])) {
                $this->tab_ext['template'] = null;
            }
        }
        
        $this->form_items = $tab_items;
//         if (input('hy_tags')) {
//             $this->form_items[] = ['number','top_size','上边距离(像素)'];
//             $this->form_items[] = ['number','bottom_size','下边距离(像素)'];
//         }
        //$this->form_items[] = ['number','cache_time','标签缓存时间','单位是秒'];
        return $this->editContent($info);
    }
    
    /**
     * 删除某个标签
     * @param string $name 标签名
     * @param number $hy_id 圈子黄页ID
     * @param number $hy_tags 同名标签编号
     */
    public function delete($name='',$hy_id=0,$hy_tags=0){
        if (LabelModel::where(['name'=>$name,'ext_id'=>$hy_id,'fid'=>intval($hy_tags)])->delete()) {
            $this -> success('删除成功');
        } else {
            $this -> error('删除失败');
        } 
    }
    
    /**
     * 取得某条标签数据 
     * @return array|NULL[]|unknown
     */
    protected function getTagInfo(){
        return getArray( LabelModel::get([
                'name'=>input('name'),
                'ext_id'=>input('hy_id'),
                'fid'=>intval(input('hy_tags')), 
        ]) );
    }
    
    //保存标签数据
    protected function save($array){
        unset($array['view_tpl']);  //安全起见,不允许设置模板代码
        $result = LabelModel::save_data($array);
        if($result===true){
            if($this->request->isAjax()){
                $this->success('设置成功');
            }else{
                echo '<script type="text/javascript">
                    parent.layer.msg("设置成功!");
                    parent.layer.close(parent.layer.getFrameIndex(window.name));parent.location.reload();
                    </script>';
                exit;
            }
        }else{
            $this->error('设置失败'.$result);
        }
    }

} 
