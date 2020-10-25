<?php

/**
 * 等级管理
 * @author  Mr.Gkx <189709040@qq.com>
 */

namespace app\member\admin; 

class MemberGradeAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MemberGrade';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [ 
            'info' => [
                'name' => '等级管理',
                'description' => '会员等级管理',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name'
        ];
    }


}