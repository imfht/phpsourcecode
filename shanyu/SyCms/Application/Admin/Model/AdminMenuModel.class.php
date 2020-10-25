<?php
namespace Admin\Model;
use Think\Model;

class AdminMenuModel extends Model{

	public function getNav(){
        $admin_menu=M('AdminMenu a')
            ->join('LEFT JOIN __AUTH_RULE__ r ON a.rid=r.id')
            ->field('r.id as rid,r.title,r.name,r.icon,a.title as title_menu')
            ->where('a.uid='.UID)
            ->limit('0,9')
            ->select();
        return $admin_menu;
	}

}