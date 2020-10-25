<?php
namespace Home\Controller;
use Common\Controller\HomebaseController;
class SearchController extends  HomebaseController {
    public function index(){
        $articleModel = new \Common\Model\ArticleModel();
        $cid = I('get.cid',0,'intval');
        $tid = I('get.tid',0,'intval');
        $keyWords = trim(I('get.k',''));
        $order = " hits DESC , add_time DESC";
        if ($cid) {
            //通过分类ID 查询数据
            //检测恶意输入
            if (!checkId('aid',$cid)) {
                //直接跳至首页
                redirect(__ROOT__.'/');
                exit();
            }
            $where = array('is_recycle'=>0,'status'=>1,'cid'=>$cid);
            $data = $articleModel->getListData(5,$where,$order);
            $cname = M('Category')->where(array('cid'=>$cid))->getField('name');
            $this->assign('cocolait','Cocolait博客-' . $cname);
            $cname = strrpos($cname,'.^_^') ? $cname : $cname . '.^_^';
            $this->assign('titleName',$cname . ". <span class='badge' style='background-color: #f4645f;float:right;margin-right:0px;'>{$data['count']}</span>");
            $this->assign('data',$data);
            $this->assign('cid',$cid);
            $this->display();
            exit();
        }

        if ($tid) {
            //检测恶意输入
            if (!checkId('aid',$tid)) {
                //直接跳至首页
                redirect(__ROOT__.'/');
                exit();
            }
            //通过标签进行查询
            $where = array('is_recycle'=>0,'status'=>1);
            $aids = $this->getAids($tid);
            if ($aids) {
                $where['aid'] = array('IN',$aids);
                $data = $articleModel->getListData(5,$where,$order);
            } else {
                $data = array(
                    'list' => array(),
                    'page' => ''
                );
            }
            $this->assign('data',$data);
            $tname = M('Tags')->where(array('tid'=>$tid))->getField('tname');
            $this->assign('cocolait','Cocolait博客-' . $tname);
            $this->assign('titleName',$tname. ' .^_^' . ". <span class='badge' style='background-color: #f4645f;float:right;margin-right:0px;'>{$data['count']}</span>");
            $this->display();
            exit();
        }

        if (!$keyWords) {
            $this->assign('titleName','没有与' . $keyWords . '相关的数据 . ㄒoㄒ');
            $data = array(
                'list' => array(),
                'page' => ''
            );
            $this->assign('data',$data);
        } else {
            //通过关键字 搜索文章
//            $where = array('is_recycle'=>0,'status'=>1);
            $where  = "(`is_recycle` = 0 AND `status` = 1 AND (`title` LIKE '%{$keyWords}%' OR `title` LIKE '%{$keyWords}' OR `title` LIKE '{$keyWords}%'))";
            $where .= " OR (`is_recycle` = 0 AND `status` = 1 AND (`post_keywords` LIKE '%{$keyWords}%' OR `post_keywords` LIKE '%{$keyWords}' OR `post_keywords` LIKE '{$keyWords}%'))";
            $where .= " OR (`is_recycle` = 0 AND `status` = 1 AND (`content` LIKE '%{$keyWords}%' OR `content` LIKE '%{$keyWords}' OR `content` LIKE '{$keyWords}%'))";
            /*$where['_complex'];
            $where['title'] = array('like',array("%{$keyWords}%","%{$keyWords}","{$keyWords}%"),'OR');
            $where['post_keywords'] = array('like',array("%{$keyWords}%","%{$keyWords}","{$keyWords}%"),'OR');
            $where['_logic']  = 'AND';
            $where['content'] = array('like',array("%{$keyWords}%","%{$keyWords}","{$keyWords}%"),'OR');*/
            $data = $articleModel->getListData(5,$where,$order);
            $this->assign('data',$data);
            $this->assign('titleName',$keyWords. ' .^_^' . ". <span class='badge' style='background-color: #f4645f;float:right;margin-right:0px;'>{$data['count']}</span>");
            $this->assign('keyWords',$keyWords);
        }
        $this->assign('cocolait','Cocolait博客-' . $this->titleName);
        $this->display();
    }

    /**
     * 获取所有的aid
     */
    protected function getAids($tid){
        $temp = array();
        $data = M('Article_tags')->where(array('cp_tags_tid'=>$tid))->field('cp_article_aid aid')->select();
        foreach($data as $aid){
            $temp[] = (string) $aid['aid'];
        }
        return $temp;
    }
}