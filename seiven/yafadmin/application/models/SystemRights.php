<?php
/**
 * 权限资源
 * @author user
 *
 */
class SystemRights extends ActiveRecord\Model {
    // explicit table name since our table is not "books"
    static $table_name = 'admin_rights';
    
    // explicit pk since our pk is not "id"
    static $primary_key = 'id';
    /**
	 * 获得所有controller,action
	 * @return array
	 */
    static function getControllers(){
        $controllerList = scandir(APPLICATION_PATH . '/application/modules/Admin/controllers');
        $controller = array();
        foreach($controllerList as $file){
            if(!in_array($file, array(
                '.',
                '..',
                //'Ajax.php',
                'Login.php' 
            ))){
                $content = file_get_contents(APPLICATION_PATH . '/application/modules/Admin/controllers' . '/' . $file);
                preg_match_all("/[\S]*Action/i", $content, $methods);
                foreach($methods as $item){
                    foreach($item as $action){
                        $controller[trim($file, '.php')][] = preg_replace('/Action/', '', $action);
                    }
                }
            }
        }
        return $controller;
    }
}
