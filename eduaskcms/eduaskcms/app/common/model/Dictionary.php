<?php
namespace app\common\model;

class Dictionary extends App
{
    public $display = 'title';
    public $assoc = array(
        'DictionaryItem' => array(
            'type' => 'hasMany'
        )
    );

    public function initialize()
    {
        $this->form = array(
            'id' => array(
                'type' => 'integer',
                'name' => 'ID',
                'elem' => 'hidden',
            ),
            'title' => array(
                'type' => 'string',
                'name' => '名称',
                'elem' => 'text',
            ),
            'model' => array(
                'type' => 'string',
                'name' => '模型',
                'elem' => 'select',
                'options' => $GLOBALS['Model_title'],
            ),
            'field' => array(
                'type' => 'string',
                'name' => '字段',
                'elem' => 'text',
            ),
            'dictionary_item_count' => array(
                'type' => 'integer',
                'name' => '字典项计数',
                'elem' => 0,
                'list' => 'counter',
                'counter' => 'DictionaryItem',
            ),
            'created' => array(
                'type' => 'datetime',
                'name' => '添加时间',
                'elem' => 0,
                'list' => 'datetime'
            )
        );

        call_user_func_array(array('parent', __FUNCTION__), func_get_args());
    }

    protected $validate = array(
        'title' => array(
            'rule' => 'require',
            'message' => '请设置名称'
        ),
        'model' => array(
            'rule' => 'require',
            'message' => '请选择模型'
        ),
        'field' => array(
            'rule' => 'require',
            'message' => '请选择字段名'
        )
    );

    public function after_insert()
    {
        $parent_rslt = call_user_func(array('parent', __FUNCTION__));
        $old_dict = dict();
        $old_dict[$this['model']][$this['field']] = [];
        write_file_cache($this->name, $old_dict);
        return $parent_rslt;
    }

    public function after_update()
    {
        $parent_rslt = call_user_func(array('parent', __FUNCTION__));
        if ($this->old_data) {
            if ($this->old_data['model'] != $this['model'] || $this->old_data['field'] != $this['field']) {
                $old_dict = dict();
                unset($old_dict[$this->old_data['model']][$this->old_data['field']]);
                write_file_cache($this->name, $old_dict);
                $this->write_file($this['id']);
            }
        }
        return $parent_rslt;
    }

    public function write_file($id)
    {
        $dictObj = $this->where('id', $id)->find();
        $data = [];
        if ($dictObj) {
            $data = $dictObj->getAssocData(
                array(
                    'DictionaryItem' => array(
                        'order' => array('list_order' => 'DESC', 'id' => 'DESC')
                    )
                )
            );
        }

        $dict = [];
        if ($data['DictionaryItem']) {
            foreach ($data['DictionaryItem'] as $item) {
                if ($item['key'] !== '' && $item['key'] !== NULL) {
                    $dict[trim($item['key'])] = $item['value'];
                } else {
                    $dict[$item['id']] = $item['value'];
                }
            }
        }
        $old_dict = dict();
        if ($data) {
            $old_dict[$data['model']][$data['field']] = $dict;
            write_file_cache($this->name, $old_dict);
            unset($GLOBALS[$this->name]);
        }
    }
}
