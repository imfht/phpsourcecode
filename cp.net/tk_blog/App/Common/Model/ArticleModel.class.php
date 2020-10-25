<?php
/**
 * 文章表模型
 */
namespace Common\Model;
use Think\Model;
class ArticleModel extends Model {
    //自动验证
    protected $_validate = array(
        array('title', 'require', '标题不能为空.^_^', 1 ),
        array('cid', 'require', '所属分类必须选择.^_^', 1 ),
        array('excerpt', 'require', '摘要不能为空.^_^', 1 ),
        array('img', 'require', '图片必须上传,不能为空.^_^', 1 ),
        array('content', 'require', '内容不能为空.^_^', 1 ),
        array('post_keywords', 'require', '关键字不能为空.^_^', 1 ),
    );

    //添加或编辑操作
    public function send_addData(){
        $tags_id = I('post.cp_tags_tid',0,'intval');//标签ID
        if (!$this->create()) return false;
        if (!$tags_id) {
            $this->error = '所属标签必须选择.^_^';
            return false;
        }
        $aid = I('post.aid',0,'intval');//文章ID

        $cid = I('post.cid',0,'intval');
        $status = I('post.status') ? '1' : '0';//是否审核
        $comment_status = I('post.comment_status') ? '1' : '0';//是否允许评论
        $is_top = I('post.is_top') ? '1' : '0';//是否置顶
        $recommended = I('post.recommended') ? '1' : '0';//是否推荐
        $post_source = empty(I('post.post_source')) ? '原创' : I('post.post_source');//来源
        $uid = $_SESSION['admin_user']['uid'];
        //文章数据
        $data = array(
            'excerpt' => trim(I('post.excerpt','')),
            'content' => trim($_POST['content']),
            'img' => trim(I('post.img','')),
            'title' => trim(I('post.title','')),
            'comment_status' => (int) $comment_status,
            'add_time' => time(),
            'is_top' => (int) $is_top,
            'recommended' => (int) $recommended,
            'post_source' => $post_source,
            'post_keywords' => trim(I('post.post_keywords','')),
            'users_uid' => $uid,
            'cid' => $cid,
            'status' => (int) $status
        );
        if ($aid) {
            //编辑操作
            $data['update_time'] = time();
            unset($data['add_time']);
            $this->where(array('aid'=>$aid))->save($data);
            return true;
        } else {
            //添加操作
            if ($id = $this->add($data)) {
                //添加标签数据
                $tags = array(
                    'cp_tags_tid' => $tags_id,
                    'cp_article_aid' => $id
                );
                if (M('Article_tags')->add($tags)) {
                    return true;
                } else {
                    $this->error = '标签操作失败.ㄒoㄒ~';
                    return false;
                }
            } else {
                $this->error = '文章操作失败.ㄒoㄒ~';
                return false;
            }
        }
    }

    //查询一条数据
    public function getFindData($aid){
        $where = array('aid'=>$aid);
        $data = $this->where($where)->find();
        $artData = M('Article_tags a')->join('cp_tags t ON t.tid = a.cp_tags_tid')->where(array('cp_article_aid'=>$aid))->field('t.tname,t.tid')->find();
        $data['tid'] = $artData['tid'];
        $data['tname'] = $artData['tname'];
        $data['author'] = M('Users')->where(array('uid'=>$data['users_uid']))->getField('uname');
        $data['cname'] = M('category')->where(array('cid'=>$data['cid']))->getField('name');
        return $data;
    }

    //查询所属分类
    public function getCategoryData(){
        return M('Category')->select();
    }

    //查询所属标签
    public function getTagsData(){
        return M('Tags')->select();
    }

    //删除数据
    public function execDelData($aid){
        if (!$aid) return false;
        $where = array('aid'=>$aid);
        $img = $this->where($where)->getField('img');
        if ($img) {
            //删除文章图片
            unlink(substr($img,1));
        }
        //删除文章信息
        $bool = $this->where($where)->delete();
        //删除文章中间表数据
        M('Article_tags')->where(array('cp_article_aid'=>$aid))->delete();
        //删除评论
        M('Comments')->where($where)->delete();
        return $bool;
    }

    /**
     * 查询所有数据并且显示分页
     * @param int $limit 每页显示多少条数据 默认显示10条
     * @param string|array $where 查询条件
     * @param string  $order  排序条件
     * @param Array  $setConfig  设置分页样式
     * @return array
     */
    public function getListData($limit=10,$where='',$order='',$setConfig = array()){
        if (empty($order)) {
            $order = "aid ASC";
        }
        $count = $this->where($where)->count();   // 查询满足要求的总记录数
        $Page = new \Think\Page($count,$limit); // 实例化分页类 传入总记录数和每页显示的记录数
        //设置分页显示
        if (!$setConfig) {
            $Page->setConfig('prev','Prev');
            $Page->setConfig('next','Next');
        } else {
            $Page->setConfig('prev',$setConfig['prev']);
            $Page->setConfig('next',$setConfig['next']);
        }
        $show = $Page->show(); // 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $this->where($where)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
//        echo $this->_sql();die;
        //获取分类数据,标签数据
        $new_list = $this->getcate($list);
        //获取评论量数据
        $result_list = $this->getComments($new_list);
        //查出分类
        $result = array(
            'list' => $result_list,
            'page' => $show,
            'count'=> $count
        );
        return $result;
    }

    /**
     * 获取分类数据,标签数据
     * @param $data 文章表数据
     * @param $tid  标签id
     */
    protected function getcate($data){
        if (!is_array($data)) return false;
        foreach($data as $k => $v){
            $where = array('cp_article_aid'=>$v['aid']);
            $data[$k]['cname'] = M('category')->where(array('cid'=>$v['cid']))->getField('name');
            $artData = M('Article_tags a')->join('cp_tags t ON t.tid = a.cp_tags_tid')->where($where)->field('t.tname,t.tid')->find();
            $data[$k]['tname'] = $artData['tname'];
            $data[$k]['tid']   = $artData['tid'];
            $data[$k]['author'] = M('Users')->where(array('uid'=>$v['users_uid']))->getField('nickname');
        }
        return $data;
    }

    /**
     * 获取每个文章的评论量
     * @param $data 文章表数据对象
     */
    protected function getComments($data){
        if (!$data) return false;
        foreach($data as $k => $v){
            $data[$k]['comment_count'] = M('Comments')->where(array('aid'=>$v['aid']))->count();
        }
        return $data;
    }
}