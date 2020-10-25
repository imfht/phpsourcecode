<?php
namespace app\index\controller;
use app\common\controller\IndexBase;
use app\admin\model\Alonepage as AlonepageModel;

class Alonepage extends IndexBase
{
    public function index ($id = 0)
    {
        $info = getArray(AlonepageModel::get($id));
        if (empty($info)) {
            $this->error('内容不存在');
        }elseif (isset($info['status'])&&empty($info['status'])) {
            $this->error('内容已关闭');
        }
        $template = '';
        if ($info['template'] && is_file(TEMPLATE_PATH . 'index_style/' . $info['template'])) {
            $template = getTemplate(TEMPLATE_PATH . 'index_style/'  . $info['template']);       //如果不用pc_或wap_开头的文件名,能自动识别PC或WAP模板
        }
        AlonepageModel::where('id',$id)->setInc('view',1);
        $this->assign('info', $info);
		$this->assign('fid','alonepage'.$id);	//这里纯属是给PC头部菜单做选中样式调用
        return $this->fetch($template);
    }
}
