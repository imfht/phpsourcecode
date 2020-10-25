<?php
namespace app\common\model;

class ProductPicture extends Picture
{

    public $parentModel = 'Product';

    public $assoc = array(
        'Product' => array(
            'type' => 'belongsTo',
            'foreignKey' => 'foreign_id',
            'counterCache' => 'picture_count',
            'countWhere' => array(
                'module' => 'ProductPicture'
            )
        ),
        'User' => array(
            'type' => 'belongsTo'
        )
    );

    public function initialize()
    {
        call_user_func_array(array('parent', __FUNCTION__), func_get_args());
        $this->form['foreign_id']['list'] = 'assoc';
        $this->form['foreign_id']['elem'] = 'assoc_select';
        $this->form['foreign_id']['foreign'] = 'Product.title';
    }
}
