<?php
namespace backend\services;

use backend\models\AdminModule;
use Yii;
class AdminModuleService extends AdminModule{

    function __construct()
    {}
    
    /**
     * 取用户所有模块
     */
    public function getUserModuleList($userId=0)
    {
        $sql = "select module.id as mid,module.display_label as mlb,
				func.id as fid,func.display_label as flb,func.entry_url as furl,
				sru1.right_id as rid,sr.display_label as rlb,sru1.url,sru1.para_name,sru1.para_value
				from admin_right_url sru1
				left outer join admin_right sr on sru1.right_id=sr.id
				left outer join admin_menu func on sr.menu_id=func.id
				left outer join admin_module module on module.id=func.module_id
				where sru1.right_id in (
				select sru.right_id from admin_right_url sru
				left outer join admin_role_right srr on sru.right_id=srr.right_id
				left outer join admin_user_role sur on sur.role_id=srr.role_id
				where 1=1 ";
    
        if($userId != 0)
        {
            $sql .= " and sur.user_id=$userId";
        }
        $sql .= " group by sru.right_id) order by module.display_order,module.id,func.display_order,func.id;";
    
        $connection = Yii::$app->db;
        $command=$connection->createCommand($sql);
        $rows=$command->queryAll();
        //         $rows=$dataReader->readAll();
        return $rows;
    }
    
    /**
     * 管理员urls
     */
    public function getUserUrls($userId = 0)
    {
        $sql = 'SELECT ru.id AS urlid,ru.url, ru.para_name, ru.para_value, rr.role_id, rr.right_id FROM
        admin_right_url ru LEFT JOIN admin_role_right rr ON ru.right_id = rr.right_id
        LEFT JOIN admin_user_role ur ON rr.role_id = ur.role_id
        WHERE ur.user_id = '.$userId;
        //         $sql = "select url.* from admin_right_url url
        // 				left outer join system_role_right rrt on url.right_id=rrt.right_id
        // 				left outer join admin_user_role ru on ru.role_id=rrt.role_id
        // 				where ru.user_id = $userId
        // 				group by url.id ";
        $connection = Yii::$app->db;
        $command=$connection->createCommand($sql);
        $rows=$command->queryAll();
        return $rows;
    }
    /**
     * 获取所有系统功能
     */
    public function getAllFunctions(){
        $sql = 'SELECT r.id AS right_id, r.menu_id, r.right_name, f.entry_url, f.menu_name, m.display_label
            FROM admin_right r LEFT JOIN admin_menu f ON r.menu_id = f.id
            LEFT JOIN admin_module m ON f.module_id = m.id';
        $connection = Yii::$app->db;
        $command=$connection->createCommand($sql);
        $rows=$command->queryAll();
        return $rows;
    }
}
