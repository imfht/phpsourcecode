<?php
namespace Home\Logic;

class SearchLogic{
//按栏目分组导航
    public function channelGroup(){

        $sql="select c.title,c.name,count(a.id) as total from sy_category c left join sy_article a on a.cid=c.id where c.is_menu=0 and c.mid =1 group by c.id order by total desc";
        $result=M()->query($sql);
        return $result;
        //$this->assign('channel_group',$channel_group);
    }

//按月份分组导航
    public function monthGroup(){
        $sql="select DATE_FORMAT(add_time,'%Y年%m月') as title,DATE_FORMAT(add_time,'%Y-%m') as date,count(id) as total from sy_article group by date";
        $result=M()->query($sql);
        return $result;
        //$this->assign('month_group',$month_group);
    }
//按Tag分组导航
    public function tagGroup(){
        $sql="select id,title,view from sy_tag order by view desc";
        $result=M()->query($sql);
        return $result;
        //$this->assign('month_group',$month_group);
    }
}