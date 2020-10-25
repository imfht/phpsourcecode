<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends AclController {
    public function index(){
    	$this->indexShow();
    }
    public function indexShow(){
    	$this->display('indexShow');
    }
    public function shareShow(){
    	$helloMarker=new \Think\Model();
    	$sql="SELECT mk_note.* ,mk_user.username FROM  mk_note,mk_user WHERE mk_note.isshare = 1 AND mk_note.userid = mk_user.userid ORDER BY noteid DESC ;";
        $count=$helloMarker->query($sql);
        $count=count($count);
        $pageCount=10;
        $page = new \Think\Page($count,$pageCount);
        $page->setConfig('prev', '<<');
        $page->setConfig('next', '>>');
        $page->setConfig('last', '末');
        $page->setConfig('first', '首');
        $page->setConfig('theme', '<li>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </li>');
        $page->lastSuffix = false;
        $show = $page->show();
        $sql="SELECT  mk_note.* ,mk_user.username FROM  mk_note,mk_user WHERE mk_note.isshare = 1 AND mk_note.userid = mk_user.userid ORDER BY noteid DESC LIMIT $page->firstRow,$page->listRows ;";
        $shareRows=$helloMarker->query($sql);
        // echo "<pre>";
        // 	print_r($shareRows);
        // echo "</pre>";
        // exit();
    	$this->assign('shareRows',$shareRows);
    	$this->assign('count',$count);
    	$this->assign('show',$show);
    	$this->display('shareShow');
    }
    public function deleteShare(){
    	$helloMarker=new \Think\Model();
    	$backFlag=0;
    	$noteId=$_GET['id'];
    	$sql="SELECT * FROM mk_note WHERE noteid=$noteId AND isshare=1;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="该分享信息不存在！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->shareShow();
    		return 0;
 			exit();   		
    	}else{
    		$sql="UPDATE mk_note SET isshare=0 WHERE noteid=$noteId";
	    	if(!($helloMarker->execute($sql))){
	    		$backFlag=3;
	    		$backInfo="删除分享列表内容失败！";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->shareShow();
	    		return 0;
	 			exit();   
	    	}else{
	    		$sql="DELETE FROM mk_note_share WHERE noteid=$noteId;";
		    	if(! ($helloMarker->execute($sql))){
		    		$backFlag=3;
		    		$backInfo="删除收藏列表失败！";
		    		$this->assign('backFlag',$backFlag);
		    		$this->assign('backInfo',$backInfo);
		    		$this->shareShow();
		    		return 0;
		 			exit();   
		    	}
	    	}
    	}
    	$backFlag=2;
		$backInfo="删除成功，请确认您没有弄错哟！！！";
		$this->assign('backFlag',$backFlag);
		$this->assign('backInfo',$backInfo);
		$this->shareShow();
		return 0;
		exit();   
    }
    public function discussShow(){
    	$helloMarker=new \Think\Model();
    	$sql="SELECT mk_note_discuss.* FROM mk_note_discuss WHERE mk_note_discuss.isdelete=0;";
        $count=$helloMarker->query($sql);
        $count=count($count);
        $pageCount=10;
        $page = new \Think\Page($count,$pageCount);
        $page->setConfig('prev', '<<');
        $page->setConfig('next', '>>');
        $page->setConfig('last', '末');
        $page->setConfig('first', '首');
        $page->setConfig('theme', '<li>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </li>');
        $page->lastSuffix = false;
        $show = $page->show();
        $sql="SELECT mk_note_discuss.*,mk_user.username,mk_note.notename FROM mk_user,mk_note_discuss,mk_note WHERE mk_note.noteid=mk_note_discuss.noteid AND mk_note_discuss.isdelete=0 AND mk_user.userid=mk_note_discuss.userid ORDER BY mk_note_discuss.discussid DESC LIMIT $page->firstRow,$page->listRows ;";
    	// $sql="SELECT mk_note_discuss.* FROM mk_note_discuss WHERE mk_note_discuss.isdelete=0;";
        $shareRows=$helloMarker->query($sql);
        for($i=0;$i<count($shareRows);$i++){
        	$shareRows[$i]['discusstext']=htmlspecialchars_decode($shareRows[$i]['discusstext']);
        }
    	$this->assign('shareRows',$shareRows);
    	$this->assign('count',$count);
    	$this->assign('show',$show);
    	$this->display('discussShow');
    }
    public function deleteDiscuss(){
    	$helloMarker=new \Think\Model();
    	$backFlag=0;
    	$discussId=$_GET['id'];
    	$sql="SELECT * FROM mk_note_discuss WHERE discussid=$discussId AND isdelete=0;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="该评论不存在！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->discussShow();
    		return 0;
 			exit();   		
    	}else{
    		$sql="UPDATE mk_note_discuss SET isdelete=1 WHERE discussid=$discussId";
	    	if(!($helloMarker->execute($sql))){
	    		$backFlag=3;
	    		$backInfo="删除评论失败！";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->discussShow();
	    		return 0;
	 			exit();   
	    	}else{
	    		$sql="UPDATE mk_note SET  notediscusscount=notediscusscount-1 WHERE noteid=".$noteFlagRow[0]['noteid'].";";
	    		if(! ($helloMarker->execute($sql))){
		    		$backFlag=3;
		    		$backInfo="评论已经删除删除评论点赞失败！";
		    		$this->assign('backFlag',$backFlag);
		    		$this->assign('backInfo',$backInfo);
		    		$this->discussShow();
		    		return 0;
		 			exit();   
		    	}
	    		$sql="DELETE FROM mk_note_discuss_like WHERE discussid=$discussId;";
		    	if(! ($helloMarker->execute($sql))){
		    		$backFlag=3;
		    		$backInfo="删除评论点赞失败！";
		    		$this->assign('backFlag',$backFlag);
		    		$this->assign('backInfo',$backInfo);
		    		$this->discussShow();
		    		return 0;
		 			exit();   
		    	}
	    	}
    	}
    	$backFlag=2;
		$backInfo="删除成功，请确认您没有弄错哟！！！";
		$this->assign('backFlag',$backFlag);
		$this->assign('backInfo',$backInfo);
		$this->discussShow();
		return 0;
		exit();   
    }
    public function userShow(){
    	$helloMarker=new \Think\Model();
    	$sql="SELECT mk_user.* FROM mk_user WHERE useractive=1";
        $count=$helloMarker->query($sql);
        $count=count($count);
        $pageCount=10;
        $page = new \Think\Page($count,$pageCount);
        $page->setConfig('prev', '<<');
        $page->setConfig('next', '>>');
        $page->setConfig('last', '末');
        $page->setConfig('first', '首');
        $page->setConfig('theme', '<li>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </li>');
        $page->lastSuffix = false;
        $show = $page->show();
        $sql="SELECT mk_user.* FROM mk_user  WHERE  useractive=1 LIMIT $page->firstRow,$page->listRows ;";
        $userRows=$helloMarker->query($sql);
    	$this->assign('userRows',$userRows);
    	$this->assign('count',$count);
    	$this->assign('show',$show);
    	$this->display('userShow');
    }
    public function deleteUser(){
    	$helloMarker=new \Think\Model();
    	$backFlag=0;
    	$userId=$_GET['id'];
    	$sql="SELECT * FROM mk_user WHERE userid=$userId AND useractive=1;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="该用户不存在或已经注销！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->userShow();
    		return 0;
 			exit();   		
    	}else{
    		$sql="UPDATE mk_user SET useractive=0 WHERE userid=$userId";
	    	if(!($helloMarker->execute($sql))){
	    		$backFlag=3;
	    		$backInfo="注销用户失败！";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->userShow();
	    		return 0;
	 			exit();   
	    	}
    	}
    	$backFlag=2;
		$backInfo="注销用户成功，可再次激活！";
		$this->assign('backFlag',$backFlag);
		$this->assign('backInfo',$backInfo);
		$this->userShow();
		return 0;
		exit();   
    }
    public function articleShow(){
    	$helloMarker=new \Think\Model();
    	$sql="SELECT mk_article.* FROM mk_article  ORDER BY articletop DESC,articleid DESC;";
        $count=$helloMarker->query($sql);
        $count=count($count);
        $pageCount=10;
        $page = new \Think\Page($count,$pageCount);
        $page->setConfig('prev', '<<');
        $page->setConfig('next', '>>');
        $page->setConfig('last', '末');
        $page->setConfig('first', '首');
        $page->setConfig('theme', '<li>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </li>');
        $page->lastSuffix = false;
        $show = $page->show();
        $sql="SELECT mk_article.* FROM mk_article  ORDER BY articletop DESC ,  articleid DESC LIMIT $page->firstRow,$page->listRows ;";
        $articleRows=$helloMarker->query($sql);
        for($i=0;$i<count($articleRows);$i++){
        	$articleRows[$i]['articleabout']=htmlspecialchars_decode($articleRows[$i]['articleabout']);
        	$articleRows[$i]['articlecontent']=htmlspecialchars_decode($articleRows[$i]['articlecontent']);
        }
    	$this->assign('articleRows',$articleRows);
    	$this->assign('count',$count);
    	$this->assign('show',$show);
    	$this->display('articleShow');
    }
    public function deleteArticle(){
    	$helloMarker=new \Think\Model();
    	$backFlag=0;
    	$articleId=$_GET['id'];
    	$sql="SELECT * FROM mk_article WHERE articleid=$articleId ;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="文章不存在！！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->articleShow();
    		return 0;
 			exit();   		
    	}else{
    		$sql="DELETE FROM  mk_article WHERE articleid=$articleId";
	    	if(!($helloMarker->execute($sql))){
	    		$backFlag=3;
	    		$backInfo="文章删除失败！";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->articleShow();
	    		return 0;
	 			exit();   
	    	}
    	}
    	$backFlag=2;
		$backInfo="删除文章成功！注意删除是否有错误！";
		$this->assign('backFlag',$backFlag);
		$this->assign('backInfo',$backInfo);
		$this->articleShow();
		return 0;
		exit();   
    }
    public function topArticle(){
    	$helloMarker=new \Think\Model();
    	$backFlag=0;
    	$articleId=$_GET['id'];
    	$sql="SELECT * FROM mk_article WHERE articleid=$articleId ;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="文章不存在！！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->articleShow();
    		return 0;
 			exit();   		
    	}else{
    		$sql="UPDATE mk_article SET articletop =1 WHERE articleid=$articleId";
	    	if(!($helloMarker->execute($sql))){
	    		$backFlag=3;
	    		$backInfo="文章删除失败！";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->articleShow();
	    		return 0;
	 			exit();   
	    	}
	    	$sql="UPDATE mk_article SET articletop =0 WHERE articleid!=$articleId";
	    	if(!($helloMarker->execute($sql))){
	    		$backFlag=3;
	    		$backInfo="文章更新失败！";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->articleShow();
	    		return 0;
	 			exit();   
	    	}
    	}
    	$backFlag=2;
		$backInfo="置顶文章成功！！";
		$this->assign('backFlag',$backFlag);
		$this->assign('backInfo',$backInfo);
		$this->articleShow();
		return 0;
		exit();   
    }
    public function changeArticle(){
    	$helloMarker=new \Think\Model();
    	$backFlag=0;
    	$articleId=$_GET['id'];
    	$sql="SELECT * FROM mk_article WHERE articleid=$articleId ;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="文章不存在！！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->articleShow();
    		return 0;
 			exit();   		
    	}else{
    		$articleRow[0]['articlecontent']=htmlspecialchars_decode($articleRow[0]['articlecontent']);
    		$this->assign('articleRow',$noteFlagRow);
    		$this->display('changeArticle');
    	}
    }
    public function changeArticleWork(){
    	$helloMarker=new \Think\Model();
    	$backFlag=0;
    	$articleId=$_POST['articleid'];
    	$sql="SELECT * FROM mk_article WHERE articleid=$articleId ;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="文章不存在！！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->changeArticle();
 			exit();   		
    	}else{
    		$articleName=$_POST['articlename'];
    		$articleFrom=$_POST['articlefrom'];
    		$articleAbout=$_POST['articleabout'];
    		$articleContent=htmlspecialchars($_POST['content']);
    		$sql="UPDATE mk_article SET articlename='".$articleName."' , articlefrom='".$articleFrom."',articleabout='".$articleAbout."',articlecontent='".$articleContent."' WHERE articleid=$articleId;";

    		if(!$helloMarker->execute($sql)){
    			$backFlag=1;
	    		$backInfo="文章修改失败！！";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->articleShow();
	 			exit();   	
    		}
    		$backFlag=2;
			$backInfo="成功修改文章！！";
			$this->assign('backFlag',$backFlag);
			$this->assign('backInfo',$backInfo);
    		$this->articleShow();
    	}
    }
    public function changeArticleImage(){
    	$helloMarker=new \Think\Model();
    	$backFlag=0;
    	$articleId=$_GET['id'];
    	$sql="SELECT * FROM mk_article WHERE articleid=$articleId ;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="文章不存在！！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->articleShow();
    		return 0;
 			exit();   		
    	}else{
    		$this->assign('articleRow',$noteFlagRow);
    		$this->display('changeArticleImage');
    	}
    }
    public function changeArticleImageWork(){
    	$helloMarker=new \Think\Model();
    	$backFlag=0;
    	$articleId=$_POST['articleid'];
    	$sql="SELECT * FROM mk_article WHERE articleid=$articleId ;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="文章不存在！！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->changeArticleImage();
 			exit();   		
    	}else{
    		$articleImg="hm_".time();
    		$upload = new \Think\Upload();
	        $upload->maxSize   =     4145728 ;
	        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp');
	        $upload->rootPath  =     './Public/images/article/';
	        $upload->saveName  =     $articleImg;
	        $upload->replace   =     true;
	        $upload->autoSub  =      false;
	        $info   =   $upload->upload();
	        if(!$info) {
	            if($upload->getError()=='没有文件被上传！'){
	                $info['noteimage']['savename']='1.jpg';
	            }else{
	                $backFlag=1;
	                $this->assign('backFlag',$backFlag);
	                $backInfo=$upload->getError();
	                $this->assign('backInfo',$backInfo);
	                $this->articleShow();
	                exit();
	            }
	        }
	        // echo "<pre>";
	        // 	print_r($info);
	        // echo "</pre>";
	        // exit();
	        $sql="UPDATE mk_article SET articleimg='".$info['articleimage']['savename']."' WHERE articleid=".$articleId;
	        if(!$helloMarker->execute($sql)){
	        	$backFlag=1;
	    		$backInfo="图片写入失败！！";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->articleShow();
	 			exit();   		
	        }else{
	        	$backFlag=2;
				$backInfo="成功修改封面图片！！";
				$this->assign('backFlag',$backFlag);
				$this->assign('backInfo',$backInfo);
	    		$this->articleShow();
	        }
		}
    }
    public function changeArticleBanner(){
    	$helloMarker=new \Think\Model();
    	$backFlag=0;
    	$articleId=$_GET['id'];
    	$sql="SELECT * FROM mk_article WHERE articleid=$articleId ;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="文章不存在！！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->articleShow();
    		return 0;
 			exit();   		
    	}else{
    		$this->assign('articleRow',$noteFlagRow);
    		$this->display('changeArticleBanner');
    	}
    }
    public function changeArticleBannerWork(){
    	$helloMarker=new \Think\Model();
    	$backFlag=0;
    	$articleId=$_POST['articleid'];
    	$sql="SELECT * FROM mk_article WHERE articleid=$articleId ;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="文章不存在！！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->changeArticleImage();
 			exit();   		
    	}else{
    		$articleImg="hm_".time();
    		$upload = new \Think\Upload();
	        $upload->maxSize   =     4145728 ;
	        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp');
	        $upload->rootPath  =     './Public/images/article/banner/';
	        $upload->saveName  =     $articleImg;
	        $upload->replace   =     true;
	        $upload->autoSub  =      false;
	        $info   =   $upload->upload();
	        if(!$info) {
	            if($upload->getError()=='没有文件被上传！'){
	                $info['noteimage']['savename']='1.jpg';
	            }else{
	                $backFlag=1;
	                $this->assign('backFlag',$backFlag);
	                $backInfo=$upload->getError();
	                $this->assign('backInfo',$backInfo);
	                $this->articleShow();
	                exit();
	            }
	        }
	        // echo "<pre>";
	        // 	print_r($info);
	        // echo "</pre>";
	        // exit();
	        $sql="UPDATE mk_article SET articlebanner='".$info['articleimage']['savename']."' WHERE articleid=".$articleId;
	        if(!$helloMarker->execute($sql)){
	        	$backFlag=1;
	    		$backInfo="图片写入失败！！";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->articleShow();
	 			exit();   		
	        }else{
	        	$backFlag=2;
				$backInfo="成功修改Banner图片！！";
				$this->assign('backFlag',$backFlag);
				$this->assign('backInfo',$backInfo);
	    		$this->articleShow();
	        }
		}
    }
    public function articleAdd(){
    	$this->display('articleAdd');
    }
    public function articleAddWork(){
    	$helloMarker=new \Think\Model();
    	$articleTime=date('Y-m-d');
    	$articleName=$_POST['articlename'];
    	$articleFrom=$_POST['articlefrom'];
    	$articleAbout=$_POST['articleabout'];
    	$articleContent=htmlspecialchars($_POST['content']);
    	$backArray[0]=array(
            'articlename'=>$articleName,
            'articlefrom'=>$articleFrom,
            'articleabout'=>$articleAbout,
            'articlecontent'=>htmlspecialchars_decode($articleContent),
    		);
    	$sql="INSERT INTO mk_article (articlename,articletime,articlefrom,articleabout,articlecontent) VALUES('".$articleName."','".$articleTime."','".$articleFrom."','".$articleAbout."','".$articleContent."');";
    	if(!$helloMarker->execute($sql)){
    			$backFlag=1;
	    		$backInfo="文章发布失败！！";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->assigm('articleRow',$backArray);
	    		$this->articleAdd();
	 			exit();   	
    		}
    		$backFlag=2;
			$backInfo="成功发布文章！";
			$this->assign('backFlag',$backFlag);
			$this->assign('backInfo',$backInfo);
    		$this->articleShow();
    }
    public function bugShow(){
    	$helloMarker=new \Think\Model();
    	$sql="SELECT mk_bug.* FROM  mk_bug ORDER BY bugcheck ASC , bugid DESC ;";
        $count=$helloMarker->query($sql);
        $count=count($count);
        $pageCount=10;
        $page = new \Think\Page($count,$pageCount);
        $page->setConfig('prev', '<<');
        $page->setConfig('next', '>>');
        $page->setConfig('last', '末');
        $page->setConfig('first', '首');
        $page->setConfig('theme', '<li>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </li>');
        $page->lastSuffix = false;
        $show = $page->show();
        $sql="SELECT mk_bug.* FROM  mk_bug ORDER BY bugcheck ASC , bugid DESC LIMIT $page->firstRow,$page->listRows ;";
        $bugRows=$helloMarker->query($sql);
    	$this->assign('bugRows',$bugRows);
    	$this->assign('count',$count);
    	$this->assign('show',$show);
    	$this->display('bugShow');
    }
    public function bugChecked(){
    	$helloMarker=new \Think\Model();
    	$bugId=$_GET['id'];
    	$sql="SELECT * FROM mk_bug WHERE bugid=$bugId ;";
    	$noteFlagRow=$helloMarker->query($sql);
    	if(count($noteFlagRow)!=1){
    		$backFlag=1;
    		$backInfo="反馈不存在！！";
    		$this->assign('backFlag',$backFlag);
    		$this->assign('backInfo',$backInfo);
    		$this->bugShow();
    		return 0;
 			exit();   		
    	}else{
    		$sql="UPDATE mk_bug SET bugcheck =1 WHERE bugid=$bugId";
	    	if(!($helloMarker->execute($sql))){
	    		$backFlag=1;
	    		$backInfo="反馈更新失败!";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->bugShow();
	    		return 0;
	 			exit();   
	    	}else{
	    		$backFlag=2;
	    		$backInfo="反馈更新成功!";
	    		$this->assign('backFlag',$backFlag);
	    		$this->assign('backInfo',$backInfo);
	    		$this->bugShow();
	    		return 0;
	 			exit();   
	    	}
        }
    }
}