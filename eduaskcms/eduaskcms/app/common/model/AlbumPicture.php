<?php
namespace app\common\model;

class AlbumPicture extends Picture
{

    public $parentModel = 'Album';

    public $assoc = array(
        'Album' => array(
            'type' => 'belongsTo',
            'foreignKey' => 'foreign_id',
            'counterCache' => 'picture_count',
            'countWhere' => array(
                'module' => 'AlbumPicture'
            )
        ),
        'User' => array(
            'type' => 'belongsTo'
        )
    );

    public function initialize()
    {
        call_user_func_array(array('parent', __FUNCTION__), func_get_args());
        $this->form['foreign_id']['name'] = '关联图集';
        $this->form['foreign_id']['list'] = 'assoc';
        $this->form['foreign_id']['foreign'] = 'Album.title';
        $this->form['foreign_id']['elem'] = 'assoc_select';
    }
}
