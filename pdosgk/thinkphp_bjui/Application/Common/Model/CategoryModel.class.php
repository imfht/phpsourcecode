<?php 
/*
 * 文章分类模型
 */
namespace Common\Model;
use Think\Model;
class CategoryModel extends Model {
    public $categorys;
    
    /**
     * 更新缓存并修复栏目
     */
    public function public_cache(){
        $this->repair();
        $this->file_cache();
    }
    
    private function repair(){
        //取出当前栏目

        $categorys = array();
        $categorys = $this->limit(0,100)->order('listorder ASC, catid ASC')->select();
        
        if(is_array($categorys)){
            foreach ($categorys as $v){
                    $categorys[$v[catid]] = $this->categorys[$v[catid]] = $v;
            }
        }

        if(is_array($categorys)) {
            foreach($categorys as $catid => $cat) {
                if($cat['type'] == 1){  //单页面检测
                    $exist_page = D('Page')->where('catid='.$cat['catid'])->field('catid')->find();
                    if(!$exist_page){
                        D('Page')->add(array('catid' => $cat['catid']));
                    }
                }
                /* if($cat['type'] == 2) continue;
                $arrparentid = $this->get_arrparentid($catid);
                $setting = string2array($cat['setting']);
                $arrchildid = $this->get_arrchildid($catid);
                $child = is_numeric($arrchildid) ? 0 : 1;
                if($categorys[$catid]['arrparentid']!=$arrparentid || $categorys[$catid]['arrchildid']!=$arrchildid || $categorys[$catid]['child']!=$child) $this->db->update(array('arrparentid'=>$arrparentid,'arrchildid'=>$arrchildid,'child'=>$child),array('catid'=>$catid));
        
                $parentdir = $this->get_parentdir($catid);
                $catname = $cat['catname'];
                $letters = gbk_to_pinyin($catname);
                $letter = strtolower(implode('', $letters)); */
                //更新父级栏目和子栏目
                $arrparentid = $this->get_arrparentid($cat['catid']);
                //$setting = string2array($cat['setting']);
                $arrchildid = $this->get_arrchildid($cat['catid']);
                $child = is_numeric($arrchildid) ? 0 : 1;
                if($categorys[$cat['catid']]['arrparentid']!=$arrparentid || $categorys[$cat['catid']]['arrchildid']!=$arrchildid || $categorys[$cat['catid']]['child']!=$child) 
                    $this->where('catid='.$cat['catid'])->save(array('arrparentid'=>$arrparentid,'arrchildid'=>$arrchildid,'child'=>$child));
                    //$this->db->update(array('arrparentid'=>$arrparentid,'arrchildid'=>$arrchildid,'child'=>$child),array('catid'=>$catid));
                
                
                $listorder = $cat['listorder'] ? $cat['listorder'] : $cat['catid'];
        
                /* $this->sethtml = $setting['create_to_html_root'];
                //检查是否生成到根目录
                $this->get_sethtml($catid);
                $sethtml = $this->sethtml ? 1 : 0;
        
                if($setting['ishtml']) {
                    //生成静态时
                    $url = $this->update_url($catid);
                    if(!preg_match('/^(http|https):\/\//i', $url)) {
                        $url = $sethtml ? '/'.$url : $html_root.'/'.$url;
                    }
                } else {
                    //不生成静态时
                    $url = $this->update_url($catid);
                    $url = APP_PATH.$url;
                }
                if($cat['url']!=$url) $this->db->update(array('url'=>$url), array('catid'=>$catid)); 
                if($categorys[$catid]['parentdir']!=$parentdir || $categorys[$catid]['sethtml']!=$sethtml || $categorys[$catid]['letter']!=$letter || $categorys[$catid]['listorder']!=$listorder) 
                */
        
        
        
                if($categorys[$cat['catid']]['listorder']!=$listorder) 
                    $this->where('catid='.$cat['catid'])->save(array('listorder'=>$listorder));
                    //$this->db->update(array('parentdir'=>$parentdir,'sethtml'=>$sethtml,'letter'=>$letter,'listorder'=>$listorder), array('catid'=>$catid));
            }
        }
        return true;
    }
    
    public function file_cache(){
        //$this->categorys = $this->db->select(array('siteid'=>$this->siteid, 'module'=>'content'),'*',10000,'listorder ASC');
        $this->categorys = $this->limit(0,100)->order('listorder ASC')->select();
        foreach($this->categorys as $r) {
            unset($r['module']);
            //unset($r['setting']);
            $r['setting'] = unserialize($r['setting']);
            /* $setting = string2array($r['setting']);
            $r['create_to_html_root'] = $setting['create_to_html_root'];
            $r['ishtml'] = $setting['ishtml'];
            $r['content_ishtml'] = $setting['content_ishtml'];
            $r['category_ruleid'] = $setting['category_ruleid'];
            $r['show_ruleid'] = $setting['show_ruleid'];
            $r['workflowid'] = $setting['workflowid'];
            $r['isdomain'] = '0';
            if(!preg_match('/^(http|https):\/\//', $r['url'])) {
                $r['url'] = siteurl($r['siteid']).$r['url'];
            } elseif ($r['ishtml']) {
                $r['isdomain'] = '1';
            } */
            $categorys[$r['catid']] = $r;
        }
        F('category_content',$categorys);
        return true;
    }
    
    /**
     * 删除子栏目
     * @param integer $catid        栏目ID
     * @return boolean
     */
    public function delete_child($catid){
        $catid = intval($catid);
        if (empty($catid)) return array();
        /* $r = $this->where('parentid='.$catid)->field('catid')->find();
        if($r) {
            $this->delete_child($r['catid']);
            $this->where('catid='.$r['catid'])->delete();
        } */
        $r = $this->where('parentid='.$catid)->field('catid')->select();
        $arr_catid = array();
        if($r) {
            foreach ($r as $cat_child){
                $result = $this->delete_child($cat_child['catid']);
                $this->where('catid='.$cat_child['catid'])->delete();
                $result[] = $cat_child['catid'];
                //合并结果
                $arr_catid = $arr_catid ? array_merge($arr_catid, $result) : $result;
            }
        }
        return $arr_catid;
    }
    
    /**
     *
     * 获取父栏目ID列表
     * @param integer $catid              栏目ID
     * @param array $arrparentid          父目录ID
     * @param integer $n                  查找的层次
     */
    private function get_arrparentid($catid, $arrparentid = '', $n = 1) {
        if($n > 5 || !is_array($this->categorys) || !isset($this->categorys[$catid])) return false;
        $parentid = $this->categorys[$catid]['parentid'];
        $arrparentid = $arrparentid ? $parentid.','.$arrparentid : $parentid;
        if($parentid) {
            $arrparentid = $this->get_arrparentid($parentid, $arrparentid, ++$n);
        } else {
            $this->categorys[$catid]['arrparentid'] = $arrparentid;
        }
        $parentid = $this->categorys[$catid]['parentid'];
        return $arrparentid;
    }
    
    /**
     *
     * 获取子栏目ID列表
     * @param $catid 栏目ID
     */
    private function get_arrchildid($catid) {
        $arrchildid = $catid;
        if(is_array($this->categorys)) {
            foreach($this->categorys as $id => $cat) {
                if($cat['parentid'] && $id != $catid && $cat['parentid']==$catid) {
                    $arrchildid .= ','.$this->get_arrchildid($id);
                }
            }
        }
        return $arrchildid;
    }
}
