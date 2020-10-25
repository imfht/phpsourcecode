<?php
namespace app\common\validate;
//文章验证器
use think\Validate;

class Archive extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'typeid|分类ID' => 'require|integer|is_allow',
        'title|标题' => 'require|max:45|min:5',
        'click|点击数' => 'require|integer|>=:0',
        'status|状态' => 'require|in:0,1',
        'create_time|创建时间' => 'require',
        'reply_num|回复数量' => 'number',
        'zan_num|赞数量' => 'number',
        'mod|mod扩展表' => 'require|alpha|is_mod',
        'content|内容'  => 'require',
    ];

    protected $scene = [
        'add'   => ['typeid', 'title','mod','content'],
        'edit'  => ['typeid', 'title','mod', 'click', 'status', 'create_time','id','content'],
        'status' => ['status','id'],
        'title' => ['title','id'],
        'writer' => ['writer','id'],
    ];
    // 自定义验证规则
    protected function is_mod($value,$rule,$data=[]) {
        $Arctype = new \app\common\model\Arctype;
        $Arctype_data = $Arctype->get(['id'=>$data['typeid']]);
        $mod = new \app\common\model\ArctypeMod;
        $ismod = $mod->get(['id'=>$Arctype_data['mid']]);
        if( !empty($ismod) ){
            if ( $ismod['mod'] !== $value ) {
                return 'mod扩展表名称错误，请填写：'.$ismod['mod'];
            }else{
                return true;
            }
        }else{
            return $ismod ? true : 'mod扩展表名称错误';
        }
    }
    protected function is_allow($value,$rule,$data=[]) {
        $Arctype = new \app\common\model\Arctype;
        $Arctype_data = $Arctype->get(['id'=>$data['typeid']]);
        if( empty($Arctype_data) ){
            return '选择的分类不存在';
        }else{
            $mod = new \app\common\model\ArctypeMod;
            $ismod = $mod->get(['id'=>$Arctype_data['mid']]);
            if( !empty($ismod) ){
                if ( $ismod['mod'] == 'addonpage' ) {
                    return '选择的分类不允许发布文章';
                }else{
                    return true;
                }
            }
        }
    }

}