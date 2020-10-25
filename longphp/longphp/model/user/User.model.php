<?php
/**
 * @require : none
 * @author : yu@wenlong.org
 * @date : 2015-09-02 15:45:05
 * @description : this is a new file 
 */
namespace Model;

if(!defined('DIR')){
	exit('Please correct access URL.');
}
 
class User extends Model {
    private static $table = 'user';

    function get_list(){
        $res = $this->select('name, age')->where('age >', 25)->order_by('age', 'desc')->order_by('id', 'asc')->group_by('name')->group_by('age')->get(self::$table);
        echo $this->getLastSql;
        return $res;
    }
}
