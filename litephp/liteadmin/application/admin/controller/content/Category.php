<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/10
 * Time: 17:58
 */

namespace app\admin\controller\content;

use app\common\controller\BaseAdmin;
use app\common\model\content\Category as CategoryModel;
use LiteAdmin\Tree;
use think\Db;

/**
 * @title 分类管理
 * Class Category
 * @package app\admin\controller\content
 */
class Category extends BaseAdmin
{
    /**
     * @title 列表页
     * @return mixed
     */
    public function index()
    {
        $db = CategoryModel::where('is_deleted',0)->order('id asc');
        return $this->_list($db,false);
    }

    protected function _index_list_before(&$data)
    {
        foreach ($data as $key => $value){
            $data[$key] = $value->toArray();
        }
        $data = Tree::array2list($data,'id','pid','_child');
    }

    /**
     * @title 添加操作
     * @return array|mixed
     */
    public function add()
    {
        return $this->_form(new CategoryModel(), 'form');
    }

    /**
     * @title 编辑操作
     * @return array|mixed
     */
    public function edit()
    {
        return $this->_form(new CategoryModel(), 'form');
    }

    protected function _form_before(&$data)
    {
        if ($this->request->isGet()){
            $list = CategoryModel::select();
            foreach ($list as $key => $value){
                $list[$key] = $value->toArray();
            }
            $tree = Tree::array2tree($list);

            $func = function (&$tree) use ($data, &$func){
                $idnow = empty($data)?0:$data['id'];
                foreach ($tree as $key => &$item){
                    if ($item['id'] == $idnow) {
                        unset($tree[$key]);
                        return true;
                    }
                    if (isset($item['_child'])){
                        if ($func($item['_child'])){
                            return true;
                        }
                    }
                }
            };
            $func($tree);

            $cates = Tree::tree2list($tree);

            $this->assign('cates',$cates);
        }else{
            $content = $this->request->post('content','','strval');
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('HTML.SafeIframe',true);
            $config->set('URI.SafeIframeRegexp','%^http://player.youku.com%');
            $config->set('Attr.AllowedFrameTargets',[
                'height' => true,
                'width' => true,
                'src' => true,
                'frameborder' => true,
                'allowfullscreen' => true
            ]);
            $purfier = new \HTMLPurifier($config);
            $data['content'] = $purfier->purify($content);;
        }
    }

    protected function _form_after(&$data)
    {
        $ppath = CategoryModel::where('id','=',$data['pid'])->value('path')?:0;

        $children = CategoryModel::whereLike('path', $data['path'].',%')->column('path','id');
        CategoryModel::where('id','=',$data['id'])->update([
            'path'=>$ppath.','.$data['id']
        ]);

        foreach ($children as $id =>$path){
            CategoryModel::where('id','=',$id)->update([
                'path'=>str_replace($data['path'],$ppath.','.$data['id'], $path)
            ]);
        }
    }

    /**
     * @title 删除操作
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $ids = $this->request->post('ids');
        $son = CategoryModel::whereIn('pid',$ids)
            ->where('is_deleted',0)
            ->count();
        if ((int)$son !== 0){
            $this->error("存在子类，不能删除！");
        }
        $this->_del(new CategoryModel(),$ids);
    }

    /**
     * @title 禁用/启用
     */
    public function change()
    {
        $id = $this->request->post('id');
        $state = $this->request->post('state');
        $this->_change(new CategoryModel(), $id, ['state' => $state]);
    }
}