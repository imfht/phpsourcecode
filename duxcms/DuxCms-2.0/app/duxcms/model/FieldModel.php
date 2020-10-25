<?php
namespace app\duxcms\model;
use app\base\model\BaseModel;
/**
 * 字段操作
 */
class FieldModel extends BaseModel {

    //验证
    protected $_validate = array(
        array('fieldset_id', 'require', '无法获取字段集ID', 1),
        array('name','require', '字段名称未填写', 1),
        array('field', 'validateField', '已存在相同的字段', 1, 'callback'),
        array('type','require', '字段类型未选择', 1),
        array('verify_type','require', '验证类型未选择', 1),
    );
    //完成
    protected $_auto = array (
        //全部
        array('fieldset_id','intval',3,'function'), //字段集ID
        array('name','htmlspecialchars',3,'function'), //字段名
        array('sequence','intval',3,'function'), //顺序
        array('verify_data','base64_encode',3,'function'), //验证规则
        array('verify_data_js','base64_encode',3,'function'), //JS验证规则
        //编辑
        array('field_id','intval',2,'function'), //字段ID
     );

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array()){
        return $this->where($where)->order('sequence asc')->select();
    }

    /**
     * 获取信息
     * @param int $fieldId ID
     * @return array 信息
     */
    public function getInfo($fieldId)
    {
        $map = array();
        $map['field_id'] = $fieldId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->where($where)->find();
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveData($type = 'add'){
        $data = $this->create();
        if(!$data){
            return false;
        }
        //字段集信息
        $fieldsetInfo=target('duxcms/Fieldset')->getInfo($data['fieldset_id']);
        //获取字段类型属性
        $typeField = $this->typeField();
        $propertyField = $this->propertyField();
        $typeData = $typeField[$data['type']];
        $property = $propertyField[$typeData['property']];
        if($property['decimal']){
            $property['decimal']=','.$property['decimal'];
        }else{
            $property['decimal']='';
        }
        if($type == 'add'){
            //插入字段
            $sqlText="
            ALTER TABLE {pre}ext_{$fieldsetInfo['table']} ADD {$data['field']} {$property['name']}({$property['maxlen']}{$property['decimal']}) DEFAULT NULL
            ";
            $sql = $this->execute($sqlText);

            if($sql === false){
                return false;
            }
            //写入数据
            return $this->add($data);
        }
        if($type == 'edit'){
            if(empty($data['field_id'])){
                return false;
            }
            //获取信息
            $info = $this->getInfo($data['field_id']);
             //修改字段
            if($info['type']<>$data['type']||$info['field']<>$data['field']){
                $sql="
                ALTER TABLE {pre}ext_{$fieldsetInfo['table']} CHANGE {$info['field']} {$data['field']} {$property['name']}({$property['maxlen']}{$property['decimal']})
                ";
                $statusSql = $this->execute($sql);
                if($statusSql === false){
                    return false;
                }
            }
            //修改数据
			$where = array();
			$where['field_id'] = $data['field_id'];
            $status = $this->where($where)->data($data)->save();
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 删除信息
     * @param int $fieldId ID
     * @return bool 删除状态
     */
    public function delData($fieldId)
    {
        $map = array();
        $map['field_id'] = $fieldId;
        //获取信息
        $info = $this->getWhereInfo($map);
        $fieldsetInfo = target('duxcms/Fieldset')->getInfo($info['fieldset_id']);
        if(empty($fieldsetInfo)){
            return false;
        }
        //删除字段
        $sql="
             ALTER TABLE {pre}ext_{$fieldsetInfo['table']} DROP {$info['field']}
            ";
        $statusSql = $this->execute($sql);
        if($statusSql === false){
            return false;
        }
        //删除数据
        return $this->where($map)->delete();
    }

    /**
     * 验证字段是否重复
     * @param int $field 字段名
     * @return bool 状态
     */
    public function validateField($field)
    {
        if(empty($field)){
            return false;
        }
        $fieldsetId = request('post.fieldset_id',0);
        $fieldId = request('post.field_id',0);
        $map = array();
        $map['fieldset_id'] = $fieldsetId;
        if($fieldId){
            $map[] = 'field_id <> '.$fieldId;
        }
        $map['field'] = $field;
        $info = $this->getWhereInfo($map);
        if(empty($info)){
            return true;
        }else{
            return false;
        }

    }

    /**
     * 字段类型
     * @param int $fieldsetId ID
     * @return bool 删除状态
     */
    public function typeField()
    {
        $list=array(
            1=> array(
                'name'=>'文本框',
                'property'=>1,
                'html'=>'text',
                ),
            2=> array(
                'name'=>'多行文本',
                'property'=>3,
                'html'=>'textarea',
                ),
            3=> array(
                'name'=>'编辑器',
                'property'=>3,
                'html'=>'editor',
                ),
            4=> array(
                'name'=>'文件上传',
                'property'=>1,
                'html'=>'fileUpload',
                ),
            5=> array(
                'name'=>'单图片上传',
                'property'=>1,
                'html'=>'imgUpload',
                ),
            6=> array(
                'name'=>'多图上传',
                'property'=>3,
                'html'=>'imagesUpload',
                ),
            7=> array(
                'name'=>'下拉菜单',
                'property'=>3,
                'html'=>'select',
                ),
            8=> array(
                'name'=>'单选',
                'property'=>3,
                'html'=>'radio',
                ),
            9=> array(
                'name'=>'多选',
                'property'=>3,
                'html'=>'checkbox',
                ),
            10=> array(
                'name'=>'日期和时间',
                'property'=>2,
                'html'=>'textTime',
                ),
            11=> array(
                'name'=>'货币',
                'property'=>4,
                'html'=>'currency',
                ),
            
        );
        return $list;
    }

    /**
     * 字段SQL属性
     * @param int $type 字段类型
     * @return array 字段类型列表
     */
    public function propertyField()
    {
        $list=array(
            1=> array(
                'name'=>'varchar',
                'maxlen'=>250,
                'decimal'=>0,
                ),
            2=> array(
                'name'=>'int',
                'maxlen'=>10,
                'decimal'=>0,
                ),
            3=> array(
                'name'=>'text',
                'maxlen'=>0,
                'decimal'=>0,
                ),
            4=> array(
                'name'=>'decimal',
                'maxlen'=>10,
                'decimal'=>2,
                ),
        );
        return $list;
    }

    /**
     * 字段验证属性
     * @param int $type 字段类型
     * @return array 字段类型列表
     */
    public function typeVerify()
    {
        return array(
            1 => array(
                'name' => '正则验证(可用内置)',
                'data' => 'regex',
                ),
            2 => array(
                'name' => '验证长度(1,2)',
                'data' => 'length',
                ),
            );
    }

    /**
     * 字段验证规则
     * @param int $type 字段类型
     * @return array 字段类型列表
     */
    public function ruleVerify()
    {
        return array(
            0 => array(
                'name' => '必填',
                'data' => 'require',
                ),
            1 => array(
                'name' => '邮箱',
                'data' => 'email',
                ),
            2 => array(
                'name' => '网址',
                'data' => 'url',
                ),
            3 => array(
                'name' => '货币',
                'data' => 'currency',
                ),
            4 => array(
                'name' => '数字',
                'data' => 'number',
                ),
            );
    }

    /**
     * JS字段验证规则
     * @param int $type 字段类型
     * @return array 字段类型列表
     */
    public function ruleVerifyJs()
    {
        return array(
            0 => array(
                'name' => '必填',
                'data' => '*',
                ),
            1 => array(
                'name' => '数字',
                'data' => 'n',
                ),
            2 => array(
                'name' => '字符串',
                'data' => 's',
                ),
            3 => array(
                'name' => '邮政编码',
                'data' => 'p',
                ),
            4 => array(
                'name' => '手机号码',
                'data' => 'm',
                ),
            5 => array(
                'name' => '邮箱',
                'data' => 'e',
                ),
            6 => array(
                'name' => 'url',
                'data' => '网址',
                ),
            );
    }

    /**
     * 完整信息HTML
     * @param array $value 字段信息
     * @param string $data 字段值
     * @param string $model 其他模块
     * @return string HTML信息
     */
    public function htmlFieldFull($value,$data = null,$model = 'duxcms/Field')
    {
        //获取字段属性
        $typeField=$this->typeField();
        //生成新配置
        $config=array();
        $config['type']=$typeField[$value['type']]['html'];
        $config['title']=$value['name'];
        $config['name']='Fieldset_'.$value['field'];
        if($data){
            $config['value'] = $data;
        }else{
            $config['value'] = $value['default'];
        }
        $config['verify_data_js']=base64_decode($value['verify_data_js']);
        $config['tip']=$value['tip'];
        $config['errormsg']=$value['errormsg'];
        $config['config']=$value['config'];
        //返回字段HTML
        return target($model)->htmlField($config);
    }

    /**
     * 字段HTML
     * @param array $config 字段配置
     * @return string HTML信息
     */
    public function htmlField($config)
    {
        //设置统一JS验证
        if($config['verify_data_js']){
            $verifyHtml = 'datatype="'.$config['verify_data_js'].'" errormsg="'.$config['errormsg'].'"';
        }
        //设置HTML
        $html = '<div class="form-group"><div class="label"><label>'.$config['title'].'</label></div><div class="field">';
        switch ($config['type']) {
            case 'text':
                $html .= '
                    <input type="text" class="input" id="'.$config['name'].'" name="'.$config['name'].'" size="60" '.$verifyHtml.' value="'.$config['value'].'">
                ';
                break;
            case 'textarea':
                $html .= '
                    <textarea class="input" id="'.$config['name'].'" name="'.$config['name'].'" rows="3" cols="62" '.$verifyHtml.'>'.$config['value'].'</textarea>
                ';
                break;
            case 'editor':
                $html .= '
                    <textarea class="input js-editor" id="'.$config['name'].'" name="'.$config['name'].'" rows="20" >'.html_out($config['value']).'</textarea>
                ';
                break;
            case 'fileUpload':
                $html .= '
                    <input type="text" class="input" id="'.$config['name'].'" name="'.$config['name'].'" size="40" '.$verifyHtml.' value="'.$config['value'].'">
                    <a class="button bg-blue button-small js-file-upload" data="'.$config['name'].'" id="'.$config['name'].'_upload" href="javascript:;" ><span class="icon-upload"> 上传</span></a>
                ';
                break;
            case 'imgUpload':
                $html .= '
                    <input type="text" class="input" id="'.$config['name'].'" name="'.$config['name'].'" size="38" '.$verifyHtml.' value="'.$config['value'].'">
                    <a class="button bg-blue button-small js-img-upload" data="'.$config['name'].'" id="'.$config['name'].'_upload" preview="'.$config['name'].'_preview" href="javascript:;" ><span class="icon-upload"> 上传</span></a>
                    <a class="button bg-blue button-small icon-picture-o" id="'.$config['name'].'_preview" href="javascript:;" > 预览</a>
                ';
                break;
            case 'imagesUpload':
                $html .= '
                    <div>
                    <a class="button bg-blue button-small js-multi-upload" data="'.$config['name'].'" id="'.$config['name'].'_button" href="javascript:;" ><span class="icon-upload"> 上传</span></a>
                    <span class="suffix">上传后可拖动图片进行排序</span>
                    </div>
                    <br>
                    <div class="media-inline  clearfix dux-multi-image" id="'.$config['name'].'">';
                    if(!empty($config['value'])){
                        $list = unserialize($config['value']);
                        if(is_array($list)&&!empty($list)){
                            foreach ($list as $value) {
                                $html.='
                                <div class="media radius clearfix">
                                    <a class="del" href="javascript:;" alt="删除"><img src="'.$value['url'].'" ></a>
                                    <div class="media-body">
                                        <input name="'.$config['name'].'[url][]" type="hidden" class="input" value="'.$value['url'].'" />
                                        <input name="'.$config['name'].'[title][]" type="text" class="input" value="'.$value['title'].'" />
                                    </div>
                                </div>
                                ';
                            }
                        }
                    }
                $html .= '</div>
                ';
                break;
            case 'select':
                $html .= '<select class="input" name="'.$config['name'].'" id="'.$config['name'].'">';
                $list = explode(',', $config['config']);
                $i = 0;
                foreach ($list as $vo) {
                    $i++;
                    if($i == $config['value']){
                        $html .= '<option value="'.$i.'" selected>'.$vo.'</option>';
                    }else{
                        $html .= '<option value="'.$i.'">'.$vo.'</option>';
                    }
                }
                $html .= '</select>';
                break;
            case 'radio':
                $html .= '<div class="padding-top">';
                $list = explode(',', $config['config']);
                $i = 0;
                foreach ($list as $vo) {
                    $i++;
                    $html .= ' <label>';
                    if($i == $config['value']){
                        $html .= '<input name="'.$config['name'].'" value="'.$i.'" checked="checked" type="radio">';
                    }else{
                        $html .= '<input name="'.$config['name'].'" value="'.$i.'" type="radio">';
                    }
                    $html .= ' '.$vo.'</label> ';
                }
                $html .= '</div>';
                break;
            case 'checkbox':
                $html .= '<div class="padding-top">';
                $list = explode(',', $config['config']);
                $val = explode(',', $config['value']);
                $i = 0;
                foreach ($list as $vo) {
                    $i++;
                    $html .= ' <label>';
                    if(in_array($i,$val)){
                        $html .= '<input name="'.$config['name'].'[]" value="'.$i.'" checked="checked" type="checkbox">';
                    }else{
                        $html .= '<input name="'.$config['name'].'[]" value="'.$i.'" type="checkbox">';
                    }
                    $html .= ' '.$vo.'</label> ';
                }
                $html .= '</div>';
                break;
            case 'textTime':
                if(!empty($config['value'])){
                    $config['value'] = date('Y/m/d H:i:s',$config['value']);
                }
                $html .= '
                    <input type="text" class="input  js-time" id="'.$config['name'].'" name="'.$config['name'].'" size="60" '.$verifyHtml.' value="'.$config['value'].'">
                ';
                break;
            case 'currency':
                $html .= '
                    <input type="text" class="input" id="'.$config['name'].'" name="'.$config['name'].'" size="60" '.$verifyHtml.' value="'.number_format($config['value'], 2, '.', '').'">
                ';
                break;
        }
        $html .= '<div class="input-note">'.$config['tip'].'</div>';
        $html .= '</div></div>';
        return $html;

    }

}
