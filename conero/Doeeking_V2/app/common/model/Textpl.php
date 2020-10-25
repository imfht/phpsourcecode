<?php
namespace app\common\model;
use app\common\model\BaseModel;
// 系统模板变量
class Textpl extends BaseModel{
    protected $table = 'sys_texttpl';
    protected $pk = 'tpl_no';
    private $renderArray;
    /* 文本生成
     * $tplno 模板编号
     * $data 渲染数据
    */
    public function renderContent($tplno,$data=[]){
        $source = $this->get($tplno)->toArray();
        return $this->parseTpl(isset($source['tpl'])? $source['tpl']:null,$data);
    }
    public function parseTpl($tpl=null,$data=[])
    {
        $tpl = $tpl? $tpl:'';
        if($tpl){
            $pattern = '/{[a-zA-Z0-d-_$]+}/';
            preg_match_all($pattern,$tpl,$tmpArr);
            $tmpArr = isset($tmpArr[0])? $tmpArr[0]:[];
            foreach($tmpArr as $v){
                $name = preg_replace('/{|}/','',$v);
                $this->renderArray[] = $name;
                $value = isset($data[$name])? $data[$name]:'';
                $tpl = str_replace($v,$value,$tpl);
            }
        }
        return $tpl;
    }
    // 返回解析出的变量
    public function getRenderArray(){
        $ret = $this->renderArray;
        return $ret? $ret:[];
    }
}