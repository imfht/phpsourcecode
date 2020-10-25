<?php
/* 2017年2月27日 星期一
 * 全网文本编辑器， 可自动保存
*/
namespace app\index\controller;
use think\Controller;
class Textedit extends Controller
{
    /*
    {
        model: 模式名称
        table: 数据表名
        map: 查询条件   *
        dataid: 数据id *
        title: 标题字段 *
        content: 内容字段   *
    }
    */        
    public function index()
    {
        $data = count($_POST)>0? $_POST:$_GET;
        $title = '文本编辑器 - Conero';
        if($data){
            if(isset($data['uid']) && count($data) == 1) $data = bsjson($data['uid']);
            $saveAble = false;
            try{
                list($db,$map,$field) = $this->paramParse($data);
                list($ktitle,$kctt) = $field;
                $field = implode(',',$field);
                $source = is_object($db)? $db->field($field)->where($map)->find()->toArray() : $this->croDb($db)->where($map)->field($field)->find();
                $setData = [
                    'content' => $source[$kctt],
                    'title'   => $source[$ktitle],
                ];
                $title = $source[$ktitle].' - 文本编辑器 - Conero';
                $saveAble = true;
            }catch(\Exception $e){
                $log = "Error>> \r\n".$e->getMessage()."\r\n"
                    .  "Trace>> \r\n".$e->getTraceAsString()."\r\n"
                    .  "data>> \r\n". json_encode($data)
                    ;
                debugOut($log);
                $setData = [
                    'content' => '<h4 style="color:red;">参数获取失败!!!!!</h4><p>'.sysdate().'</p>',
                    'title'   => '参数失败',
                ];
                $saveAble = false;
            }
            $this->assign('data',$setData);
            $jsvar = [
                'save_mk' => $saveAble? 'Y':'N'
            ];
            $this->_JsVar('data',$jsvar);
            $this->_JsVar('uid',$data);
            $this->assign('page',$jsvar);
        }
        $this->loadScript([
            'title'=>$title,'bootstrap'=>true,'require'=>['tinymce'],'js'=>['textedit/index']
        ]);
        return $this->fetch();
    }
    // 请求删除解析
    private function paramParse($data,$type=null)
    {
        $feild = '';
        $db = isset($data['model'])? model($data['model']): (isset($data['table'])? $data['table']:'');
        $kctt = $data['content'];
        $ktitle = $data['title'];
        $kid = $data['dataid'];
        if($type === 'string'){
            $field = implode(',',[$ktitle,$kctt]);
        }
        else{
            $feild = [$ktitle,$kctt];
        }
        $map = isset($data['map'])? $data['map']:null;
        if(empty($map) && isset($data['get'])){
            $map = [];
            $map[$kid] = $data['get'];
        }
        return [$db,$map,$feild];
    }
    // 数据库保存
    public function save(){
        list($item,$data) = $this->_getAjaxData();
        $ret = 0;
        switch($item){
            case 'text_save_req':
                $uid = bsjson($data['uid']);
                list($db,$map,$field) = $this->paramParse($uid);
                $text = base64_decode($data['text']);
                list($t,$content) = $field;
                $svdata = [];
                $svdata[$content] = $text;
                // println($map,$field,$text,$svdata);
                try{
                    $ret = is_object($db)? $db->where($map)->update($svdata) : $this->croDb($db)->where($map)->update($svdata);
                    $ret = $ret? 1 : -1;
                }catch(\Exception $e){
                    $ret = -1;
                }
            break;
        }
        echo $ret;
    }
}