<?php
namespace app\common\model;

class Region extends App
{
    //关联模型
    public $assoc = [];
    public $display = 'title';
    public $cache = [];
    
    public function initialize()
    {        
        $this->form = [
            'id' => [
            	'type' => 'integer',
            	'name' => 'ID',
            	'elem' => 'hidden',
            ],
            'parent_id' => array(
                'type' => 'integer',
                'name' => '父级地区',
                'elem' => 'multi_select_parent.ajax',
                'multi_options' => [
                    'order' => ['list_order' => 'DESC','id' => 'ASC'],
                    'where' => []
                ],
            ),
            'title' => array(
                'type' => 'string',
                'name' => '地区名称',
                'elem' => 'text'
            ),
            'first_letter' => array(
                'type' => 'string',
                'name' => '地区首字母',
                'elem' => 'text',
                'info' => '不填写默认自动获取'
            ),
            'list_order' => array(
                'type' => 'integer',
                'name' => '排序权重',
                'elem' => 'number',
                'list' => 'edit'
            ),
            //其他字段
        ];
        call_user_func_array(['parent', __FUNCTION__], func_get_args());
    }
   
    //数据验证    
    protected $validate = [
        'title' => array(
            'rule' => 'require',
            'message' => '请填写地区名称'
        ),
        'parent_id' => array(
            array(
                'rule' => array('egt', 1),
                'message' => '请选择父级地区'
            ),
            array(
                'rule' => array('call', 'checkParent'),
                'on' => 'edit'
            )
        )
    ];
    
    public function checkParent($value, $rule, $data)
    {
        if ($value == $data['id'] || in_array($data['parent_id'], $this->getChildrenIds($data['id'], 0))) {
            return  '不能选择本地区以及其子地区做为父级';
        }
        
        return true;
    }
    
    public function before_write()
    {
        $parent_rslt = call_user_func(array('parent', __FUNCTION__));
        if ($this['title'] && !trim($this['first_letter'])) {
            $this['first_letter'] = strtoupper(getFirstLetter($this['title']));
        }
        return $parent_rslt;
    }
    
    public function after_delete()
    {
        $parent_rslt = call_user_func(array('parent', __FUNCTION__));
        if ($this['id']) {                   
            $this->where(array('id' => array('id', 'IN', $this->getChildrenIds($this['id'], 0))))->delete();##删除掉所有子级
        }   
        return $parent_rslt;
    }
}
