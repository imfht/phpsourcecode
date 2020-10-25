<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 开发平台
 */
namespace app\system\validate;
use think\Validate;

class Open extends Validate{

    protected $rule = [
        'cate'             => 'require',
        'scene'            => 'require|array',
        'method'           => 'require|array',
        'has_audit_team'   => 'require',
    ];
    
    protected $message = [
        'cate'           => '小程序类目必须填写',
        'scene'          => 'UGC场景必须选择必须填写',
        'method'         => 'UGC安全机制必须填写',
        'has_audit_team' => 'UGC审核团队必须填写',  
    ];

    protected $scene = [
        'addpass' => ['cate','scene','method','has_audit_team']
    ];
}