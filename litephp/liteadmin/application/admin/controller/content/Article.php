<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/11
 * Time: 11:02
 */

namespace app\admin\controller\content;

use app\common\controller\BaseAdmin;
use app\common\model\content\Article as ArticleModel;
use League\HTMLToMarkdown\HtmlConverter;
use LiteAdmin\Tree;
use think\Db;
use think\facade\Config;

/**
 * @title 文章管理
 * Class Article
 * @package app\admin\controller\content
 */
class Article extends BaseAdmin
{
    /**
     * @title 列表页
     * @return mixed
     */
    public function index()
    {
        $db = ArticleModel::with('category')->where('is_deleted','=',0)
            ->order('id desc');

        $search = $this->request->get();
        // 精准查询
        foreach (['state'] as $field){
            if (isset($search[$field]) && $search[$field] !== ''){
                $db->where($field,'=', $search[$field]);
            }
        }
        // 模糊查询
        foreach (['title'] as $field){
            if (isset($search[$field]) && $search[$field] !== ''){
                $db->whereLike($field, "%{$search[$field]}%");
            }
        }
        // 分类查询
        if (isset($search['cid']) && $search['cid'] !== ''){
            $ids = Db::name('ContentCategory')
                ->alias('c1')
                ->join('__CONTENT_CATEGORY__ c2','c1.path LIKE CONCAT(c2.path, "%")','LEFT')
                ->where('c2.id','=',$search['cid'])
                ->where('c1.is_deleted','=',0)
                ->column("c1.id");
            $db->whereIn('cid',$ids);
        }

        return $this->_list($db, true, $search);
    }

    protected function _index_list_before(){
        if ($this->request->isGet()){
            $list = Db::name('ContentCategory')->select();
            $cates = Tree::array2list($list);
            $this->assign('cates',$cates);
        }
    }

    /**
     * @title 添加操作
     * @return array|mixed
     */
    public function add()
    {
        return $this->_form(new ArticleModel(), 'form');
    }

    /**
     * @title 编辑操作
     * @return array|mixed
     */
    public function edit()
    {
        return $this->_form(new ArticleModel(), 'form');
    }

    protected function _form_before(&$data)
    {
        if ($this->request->isGet()){
            $list = Db::name('ContentCategory')
                ->where('is_deleted',0)
                ->where('state',1)
                ->select();
            $cates = Tree::array2list($list);
            $this->assign('cates',$cates);
        }else{
            $editor = Config::get('liteadmin.editor');

            // markdown渲染和内容过滤
            if ($editor === 'markdown'){
                $md_content = $this->request->post('md_content','','strval');
                $parsedown = new \Parsedown();
                $data['content'] = $parsedown->setSafeMode(true)->text($md_content);
                $data['md_content'] = $md_content;
            }else{
                $data['content'] = $this->request->post('content','','strval');
                $htmlconverter = new HtmlConverter();
                $data['md_content'] = $htmlconverter->convert($data['content']);
            }
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
            $config->set('HTML.TargetBlank',true);
            $config->set('HTML.Nofollow',true);

            $purfier = new \HTMLPurifier($config);
            $data['content'] = $purfier->purify($data['content']);
        }
    }

    protected function _form_after($data){
        if ($this->request->isPost()){
            // 标签提取
            if (!empty($data['id'])){
                Db::name('ContentTagsMap')->where('article_id',$data['id'])->delete();
            }

            $data['keyword'] = str_replace('，',',',$data['keyword']);
            $tags = explode(',',$data['keyword']);
            foreach ($tags as $tag){
                if (empty($tag)){
                    continue;
                }
                $item = Db::name('ContentTags')->where('tag',$tag)->find();
                if (empty($item)){
                    $tag_id = Db::name('ContentTags')->insertGetId(['tag'=>$tag]);
                }else{
                    $tag_id = $item['id'];
                }
                Db::name('ContentTagsMap')->insert([
                    'tag_id'=>$tag_id,
                    'article_id'=>$data['id']
                ]);
            }
            // 建立搜索索引

            if (Config::get('liteadmin.xunsearch') === 'enabled'){
                $docData = [
                    'id'=>$data['id'],
                    'title'=>$data['title'],
                    'content'=>strip_tags($data['content']),
                    'create_time'=>$data['create_time'],
                    'state'=>strval($data['state']),
                ];

                $doc = new \XSDocument();
                $doc->setFields($docData);
                $xs = new \XS(config('liteadmin.xsini'));
                try{
                    if ($this->request->has('id','post')){
                        $xs->index->update($doc);
                    }else{
                        $xs->index->add($doc);
                    }
                }catch (\XSException $e){
                    $this->error('修改搜索引擎索引失败！');
                }
            }
        }
    }

    /**
     * 添加前置
     * @param $data
     */
    protected function _add_form_before(&$data){
        if ($this->request->isPost()){
            $data['create_time'] = $this->request->time();
            $data['update_time'] = $this->request->time();
            $data['is_deleted'] = 0;
            $data['state'] = 0;
            $data['click'] = 0;

        }
    }

    /**
     * 编辑前置
     * @param $data
     */
    protected function _edit_form_before(&$data){
        if ($this->request->isPost()){
            $data['update_time'] = $this->request->time();
        }
    }

    /**
     * @title 删除
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $ids = $this->request->post('ids');

        if (Config::get('liteadmin.xunsearch') === 'enabled') {
            try {
                $xs = new \XS(config('liteadmin.xsini'));
                $xs->index->del(explode(',', $ids));
            } catch (\XSException $e) {
                $this->error('修改搜索引擎索引失败！');
            }
        }

        $this->_del(new ArticleModel(), $ids);
    }

    /**
     * @title 禁用/启用
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function change()
    {
        $id = $this->request->post('id');
        $state = $this->request->post('state');

        if (Config::get('liteadmin.xunsearch') === 'enabled') {
            try {
                $doc = new \XSDocument();
                $item = ArticleModel::find($id);
                $docData = [
                    'id' => $item['id'],
                    'title' => $item['title'],
                    'content' => strip_tags($item['content']),
                    'create_time' => $item['create_time'],
                    'state' => strval($state),
                ];
                $doc->setFields($docData);
                $xs = new \XS(config('liteadmin.xsini'));
                $xs->index->update($doc);
            } catch (\XSException $e) {
                $this->error('修改搜索引擎索引失败！');
            }
        }

        $this->_change(new ArticleModel(), $id, ['state' => $state]);
    }

    /**
     * @title 重建索引
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function rebuild()
    {
        if ($this->request->isPost()){

            if (Config::get('liteadmin.xunsearch') === 'enabled') {

                $xs = new \XS(config('liteadmin.xsini'));
                try {
                    $xs->index->beginRebuild();

                    $list = ArticleModel::where('is_deleted', 0)
                        ->select();
                    foreach ($list as $item) {
                        $docData = [
                            'id' => $item['id'],
                            'title' => $item['title'],
                            'content' => strip_tags($item['content']),
                            'create_time' => $item['create_time'],
                            'state' => strval($item['state']),
                        ];
                        $doc = new \XSDocument();
                        $doc->setFields($docData);
                        $xs->index->add($doc);
                    }

                    $xs->index->endRebuild();
                } catch (\XSException $e) {
                    $this->error("索引重建失败" . $e->getMessage());
                }
            }

            $this->success("操作成功!",'');
        }
    }
}