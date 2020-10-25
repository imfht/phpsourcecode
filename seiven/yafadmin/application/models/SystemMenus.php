<?php
/**
 * 后台菜单
 * @author user
 *
 */
class SystemMenus extends ActiveRecord\Model {
    // explicit table name since our table is not "books"
    static $table_name = 'admin_menus';
    
    // explicit pk since our pk is not "id"
    static $primary_key = 'id';
    static $has_many = array(
        array(
            'children',
            'foreign_key'=> 'parentid',
            'class_name'=> 'SystemMenus' 
        ) 
    );
    static $before_save = array(
        'save_default_value' 
    );
    /**
     * 默认值
     */
    public function save_default_value(){
        if(empty($this->icon)) $this->icon = 'icon-desktop';
        if(empty($this->url)) $this->url = '/admin/index/main';
    }
    /**
     * 获得所有菜单
     */
    static function getMenus(){
        $return = array();
        $menus = self::find('all', array(
            'order'=> 'sort desc',
            'conditions'=> 'parentid=0' 
        ));
        foreach($menus as $item){
            
            $return[$item->title] = array(
                'icon'=> $item->icon,
                'url'=> $item->url 
            );
            foreach($item->children as $child){
                $return[$item->title]['list'][$child->title] = array(
                    'icon'=> $child->icon,
                    'url'=> $child->url 
                );
            }
        }
        return $return;
    }
}
