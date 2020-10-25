<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends AclController {
    public function index(){
        $this->indexArticle();
        $this->indexNewShare();
        $this->noteShareCollect(3);
        $this->display("index");
    }
    public function checkAcl(){
        if(session('hellomarkeruserlogin')!=1){
          $this->redirect("User/index");
          exit();
        }
    }
    public function indexNewShare(){
        $helloMarker=new \Think\Model();
        $sql="SELECT mk_note.noteid,mk_note.notename,mk_note.userid,mk_note.isshare,mk_note.imagesrc,mk_user.userid,mk_user.usernickname from mk_note,mk_user WHERE mk_note.isshare=1 AND mk_note.userid=mk_user.userid ORDER BY noteid DESC limit 0,4;";
        $newShareRows=array();
        $newShareRows=$helloMarker->query($sql);
        $this->assign('newShareRows',$newShareRows);

        $sql="SELECT mk_note.noteid,mk_note.notename,mk_note.isshare,mk_note.notediscusscount from mk_note WHERE mk_note.isshare=1 ORDER BY notediscusscount DESC LIMIT 0,4;";
        $hotDiscussRows=array();
        $hotDiscussRows=$helloMarker->query($sql);
        $this->assign('hotDiscussRows',$hotDiscussRows);

    }
    public function indexArticle(){
        $helloMarker=new \Think\Model();
        $sql="SELECT articleid,articlename,articleimg,articletime,articlebanner FROM mk_article ORDER BY articleid desc LIMIT 0,4";
        $articleRows=array();
        $articleRows=$helloMarker->query($sql);
        $this->assign('articleRows',$articleRows);
    }
    public function markerShare(){
        $shareType=$_REQUEST['shareType'];
        $helloMarker=new \Think\Model();
        if($shareType!='address' && $shareType!='name' ){
            $shareType='all';
        }
        if($shareType=='all'){
            $sql1="SELECT count(mk_note.isshare)  from mk_note WHERE mk_note.isshare = 1;";
        }else if($shareType=='address'){
            $shareAddress=$_REQUEST['selectName'];
            $sql1="SELECT count(mk_note.isshare)  from mk_note WHERE mk_note.isshare = 1 AND mk_note.noteaddress LIKE '%".$shareAddress."%';";
        }else if($shareType=='name'){
            $shareName=$_REQUEST['selectName'];
            $sql1="SELECT count(mk_note.isshare)  from mk_note WHERE mk_note.isshare = 1 AND mk_note.notename LIKE '%".$shareName."%';";
        }
        $count=$helloMarker->query($sql1);
        $count=$count[0]['count(mk_note.isshare)'];
        // echo $count;
        // exit();
        $pageCount=10;
        $page = new \Think\Page($count,$pageCount);
        $page->setConfig('prev', '<<');
        $page->setConfig('next', '>>');
        $page->setConfig('last', '末');
        $page->setConfig('first', '首');
        $page->setConfig('theme', '<li>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </li>');
        $page->lastSuffix = false;
        $show = $page->show();

        if($shareType=='all'){
            $sql2="SELECT mk_note.noteid,mk_note.imagesrc,mk_note.notename,mk_note.notetime,mk_note.isshare,mk_note.notediscusscount,mk_note.userid,mk_user.userid,mk_user.usernickname,mk_user.userlogo ,mk_note_share.sharetime,mk_note_share.noteid from mk_note,mk_user,mk_note_share WHERE mk_note.isshare=1 AND mk_note.userid=mk_user.userid  AND mk_note_share.noteid=mk_note.noteid ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";
        }else if($shareType=='address'){
            $shareAddress=$_REQUEST['selectName'];
            $sql2="SELECT mk_note.noteid,mk_note.imagesrc,mk_note.notename,mk_note.notetime,mk_note.isshare,mk_note.notediscusscount,mk_note.userid,mk_user.userid,mk_user.usernickname,mk_user.userlogo,mk_note_share.sharetime,mk_note_share.noteid  from mk_note,mk_user,mk_note_share  WHERE mk_note.isshare=1 AND mk_note.userid=mk_user.userid  AND mk_note_share.noteid=mk_note.noteid AND mk_note.noteaddress LIKE '%".$shareAddress."%' ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";
        }else if($shareType=='name'){
            $shareName=$_REQUEST['selectName'];
            $sql2="SELECT mk_note.noteid,mk_note.imagesrc,mk_note.notename,mk_note.notetime,mk_note.isshare,mk_note.notediscusscount,mk_note.userid,mk_user.userid,mk_user.usernickname,mk_user.userlogo,mk_note_share.sharetime,mk_note_share.noteid  from mk_note,mk_user,mk_note_share  WHERE mk_note.isshare=1 AND mk_note.userid=mk_user.userid  AND mk_note_share.noteid=mk_note.noteid AND mk_note.notename LIKE '%".$shareName."%'ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";
        }

         $noteShare=$helloMarker->query($sql2);

         for($i=0;$i<count($noteShare);$i++){
            $sql3="SELECT mk_user.username AS discussname,mk_note_discuss.discusstime FROM mk_note_discuss,mk_user WHERE mk_note_discuss.userid=mk_user.userid AND mk_note_discuss.noteid=".$noteShare[$i]['noteid'] ." ORDER BY mk_note_discuss.discussid LIMIT 0,1;";
            $tempRow=$helloMarker->query($sql3);
            if(strlen($tempRow[0]['discussname'])<1){
                $tempRow[0]['discussname']=0;
                $tempRow[0]['discusstime']=0;
            }
            $noteShare[$i]['discussname']=$tempRow[0]['discussname'];
            $noteShare[$i]['discusstime']=$tempRow[0]['discusstime'];
         }
        // echo "<pre>";
        //     print_r($noteShare);
        // echo "</pre>";
        // exit();
        $this->noteShareCollect(4);

        $this->assign("navID",$shareType);
        $this->assign('noteShare',$noteShare);
        $this->assign('count',$count);
        $this->assign('show',$show);
        $this->display('share');
    }

    public function noteShareCollect($count){
       $helloMarker=new \Think\Model();
        $sql="SELECT mk_note.noteid,mk_note.notename,mk_note.isshare,mk_note.notecollectcount from mk_note WHERE mk_note.isshare=1 ORDER BY notecollectcount DESC LIMIT 0,$count;";
        $hotCollectRows=array();
        $hotCollectRows=$helloMarker->query($sql);
        $this->assign('hotCollectRows',$hotCollectRows);
    }

    public function noteShareInfo(){
        $noteId=$_REQUEST['id'];
        $helloMarker=new \Think\Model();
        $sql="SELECT mk_note.*,mk_user.userid,mk_user.usernickname from mk_note,mk_user WHERE mk_note.noteid=$noteId AND mk_user.userid=mk_note.userid;";
        $noteInfo=array();
        $noteInfo=$helloMarker->query($sql);
        $noteInfoFlag=1;
        if(count($noteInfo)<1){
            $noteInfoFlag=0;
        }else{
            if(($noteInfo[0]['isshare']==0 )&& ($noteInfo[0]['userid'] != session('hellomarkeruserid'))){
                $noteInfoFlag=0;
            }else{
                if(strlen($noteInfo[0]['noteother'])==0){
                  $noteInfo[0]['noteohterstrlen']=0;
                }else{
                    $noteInfo[0]['noteohterstrlen']=1;
                }
            }
        }
        // echo "<pre>";
        //     print_r($noteInfo);
        // echo "</pre>";
        // exit();
        if(strlen(session('hellomarkerusername'))<1){
            $sessionUserId=0;
        }else{
            $sessionUserId=session('hellomarkeruserid');
        }
        $sql="SELECT * FROM mk_note_collect WHERE userid=$sessionUserId AND noteid=$noteId;";
        $userNoteCollectRow=$helloMarker->query($sql);
        $userNoteCollectFlag=0;
        if(count($userNoteCollectRow)==1){
            $userNoteCollectFlag=1;
        }
        $this->assign('noteFlag',$noteFlag);
        $this->assign('userNoteCollectFlag',$userNoteCollectFlag);
        $this->assign('noteInfoFlag',$noteInfoFlag);
        $this->assign("noteInfo",$noteInfo);
        $this->noteDiscuss($noteId);
        $this->display("note");
    }
    public function noteDiscuss($noteId){
        $helloMarker=new \Think\Model();
        $sql="SELECT discussid FROM mk_note_discuss WHERE noteid=$noteId";
        $count=$helloMarker->query($sql);
        $count=count($count);
        $pageCount=15;
        $page = new \Think\Page($count,$pageCount);
        $page->setConfig('prev', '<<');
        $page->setConfig('next', '>>');
        $page->setConfig('last', '末');
        $page->setConfig('first', '首');
        $page->setConfig('theme', '<li>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </li>');
        $page->lastSuffix = false;
        $show = $page->show();

        $sql="SELECT mk_note_discuss.*,mk_user.userid,mk_user.usernickname,mk_user.userlogo from mk_note_discuss,mk_user WHERE mk_note_discuss.noteid=$noteId AND mk_user.userid=mk_note_discuss.userid ORDER BY mk_note_discuss.discussid DESC LIMIT $page->firstRow,$page->listRows ;";
        $noteDiscussRows=array();
        $noteDiscussRows=$helloMarker->query($sql);
        for($i=0;$i<count($noteDiscussRows);$i++){
            $noteDiscussRows[$i]['discusstext']=htmlspecialchars_decode($noteDiscussRows[$i]['discusstext']);
        }
        $noteDiscussFlag=0;
        if(count($noteDiscussRows)>0){
            $noteDiscussFlag=1;
            $mkDiscussLike=M('mk_note_discuss_like');
            for($i=0;$i<count($noteDiscussRows);$i++){
                if(session("hellomarkeruserid")<1){
                    $userSessionId=0;
                }else{
                    $userSessionId=session("hellomarkeruserid");
                }
                $sql="SELECT discusslikeid,islike FROM mk_note_discuss_like WHERE discussid=".$noteDiscussRows[$i]['discussid']." AND userid=$userSessionId";
                $mkDiscussLikeRow=$helloMarker->query($sql); 
                $noteDiscussRows[$i]['discusslikeid']=$mkDiscussLikeRow[0]['discusslikeid'];
                if($mkDiscussLikeRow[0]['islike']==1){
                    $noteDiscussRows[$i]['discusslike']=1;
                }else{
                    $noteDiscussRows[$i]['discusslike']=0;
                }
            }
        }
        $sql="SELECT discussid FROM mk_note_discuss WHERE isdelete=0 AND noteid=$noteId;";
        $trueDiscussCount=$helloMarker->query($sql);
        $trueDiscussCount=count($trueDiscussCount);
        // echo "<hr>";
        // echo "<pre>";
        //     print_r($noteDiscussRows);
        // echo "</pre>";
        // exit();

        $this->assign('noteDiscussFlag',$noteDiscussFlag);
        $this->assign('noteDiscussCount',$trueDiscussCount);
        $this->assign("noteDiscussRows",$noteDiscussRows);
        $this->assign("noteDiscussPage",$page->firstRow/5+1);
        $this->assign("show",$show);
    }
    public function noteAddDiscuss(){
        $helloMarker=new \Think\Model();
        $content=htmlspecialchars($_POST['content']);
        if(strlen($content)<1){
            $content="很不错!";
        }
        $noteId=$_POST['noteid'];
        $userId=$_SESSION['hellomarkeruserid'];
        $discussTime=date('Y-m-d');
        // $sql1="INSERT INTO mk_note_discuss (noteid,userid,discusstime,discusstext) VALUES ($noteId,$userId,$content,'".$discussTime."')";
        $data=array(
            'noteid'=>$noteId,
            'userid'=>$userId,
            'discusstext'=>$content,
            'discusstime'=>$discussTime
        );
        $mkNoteDiscuss=M('mk_note_discuss');
        $mkNoteDiscuss->create($data);
        $sql2="UPDATE mk_note SET notediscusscount=notediscusscount+1 WHERE noteid=$noteId";
        $addDiscussFlag=2;
        if($mkNoteDiscuss->add()){
            if($helloMarker->execute($sql2)){
                $addDiscussFlag=1;
            }
        }
        $this->redirect("Index/noteShareInfo",array('id' => $noteId));
    }
    public function noteDiscussLike(){
        if(strlen(session('hellomarkerusername'))<1){
            exit();
        }
        $helloMarker=new \Think\Model();
        $userId=$_POST['userid'];
        $discussId=$_POST['discussid'];
        $mkNoteDiscussLike=M('mk_note_discuss_like');
        $mkNoteDiscuss=M('mk_note_discuss');
        $sql="SELECT * FROM mk_note_discuss_like WHERE userid=$userId AND discussid=$discussId;";
        $userDiscussLikeRow=$helloMarker->query($sql);
        $updateFlag=0;
        if(count($userDiscussLikeRow)==0){
            $data=array(
                'userid'=>$userId,
                'discussid'=>$discussId,
                'islike'=>1
            );
            $mkNoteDiscussLike->create($data);
            if($mkNoteDiscussLike->add()){
                $sql="UPDATE mk_note_discuss SET discusslikecount=discusslikecount+1 WHERE discussid=$discussId;";
                if($helloMarker->execute($sql)){
                    $updateFlag=1;
                }
            }
        }else if($userDiscussLikeRow[0]['islike']==1){
            $sql="DELETE FROM  mk_note_discuss_like  WHERE discussid=$discussId AND userid=$userId;";
            if($helloMarker->execute($sql)){
                $sql="UPDATE mk_note_discuss SET discusslikecount=discusslikecount-1 WHERE discussid=$discussId;";
                if($helloMarker->execute($sql)){
                    $updateFlag=2;
                }
            }
        }
        $discussRow=$mkNoteDiscuss->where('discussid='.$discussId)->select();
        $arr['discusslikecount']=$discussRow[0]['discusslikecount'];
        $arr['updateFlag']=$updateFlag;
        $this->ajaxReturn($arr,json);
    }
    public function noteDiscussDelete(){
        if(strlen(session('hellomarkerusername'))<1){
            exit();
        }
        $helloMarker=new \Think\Model();
        $userId=$_POST['userid'];
        $discussId=$_POST['discussid'];
        $sql="UPDATE mk_note_discuss SET isdelete = 1,discusstext='no' WHERE discussid=$discussId;";
        $updateFlag=0;
        if($helloMarker->execute($sql)){
            $sql="UPDATE mk_note SET notediscusscount=notediscusscount-1 WHERE noteid=(SELECT noteid FROM mk_note_discuss WHERE discussid=$discussId)";
            $helloMarker->execute($sql);
            $sql="DELETE FROM mk_note_discuss_like WHERE discussid=$discussId;";
            $helloMarker->execute($sql);
                $updateFlag=1;
        }        
        $arr['updateFlag']=$updateFlag;
        $this->ajaxReturn($arr,json);
    }
    public function noteCollectWork(){
        if(strlen(session('hellomarkerusername'))<1){
            exit();
        }
        if(session('hellomarkerusername')==$_POST['usernotename']){
            $updateFlag=3;
        }else{
            $helloMarker=new \Think\Model();
            $userId=$_POST['userid'];
            $noteId=$_POST['noteid'];
            $mkNote=M('mk_note');
            $mkNoteCollect=M('mk_note_collect');
            $sql="SELECT * FROM mk_note_collect WHERE userid=$userId AND noteid=$noteId;";
            $userNoteCollectRow=$helloMarker->query($sql);
            $updateFlag=0;
            if(count($userNoteCollectRow)==0){
                $data=array(
                    'userid'=>$userId,
                    'noteid'=>$noteId,
                    'collecttime'=>date('Y-m-d')
                );
                $mkNoteCollect->create($data);
                if($mkNoteCollect->add()){
                    $sql="UPDATE mk_note SET notecollectcount=notecollectcount+1 WHERE noteid=$noteId;";
                    if($helloMarker->execute($sql)){
                        $updateFlag=1;
                    }
                }
            }else{
                $sql="DELETE FROM  mk_note_collect  WHERE noteid=$noteId AND userid=$userId;";
                if($helloMarker->execute($sql)){
                    $sql="UPDATE mk_note SET notecollectcount=notecollectcount-1 WHERE noteid=$noteId;";
                    if($helloMarker->execute($sql)){
                        $updateFlag=2;
                    }
                }
            }
            $noteCollectCount=$mkNote->where('noteid='.$noteId)->getField('notecollectcount');
        }
        $arr['notecollectcount']=$noteCollectCount;
        $arr['updateFlag']=$updateFlag;
        $this->ajaxReturn($arr,json);
    }
    public function myHome(){
        $helloMarker=new \Think\Model();
        $userName=session('hellomarkerusername');
        if(strlen($userName)<1){
            $username='a';
        }
        $sql="SELECT * FROM mk_user WHERE username='".$userName."';";
        $userHomeRow=$helloMarker->query($sql);
        $userHomeFlag=0;
        if(count($userHomeRow)==1 && $userHomeRow[0]['useractive']==1){
            $userHomeFlag=1;
        }
        $this->assign('myFlag',1);
        $this->assign('userHomeRow',$userHomeRow);
        $this->assign('userHomeFlag',$userHomeFlag);
        $this->display('userHome'); 
    }
    public function userHome(){
        $helloMarker=new \Think\Model();
        $userName=$_GET['user'];
        if(strlen($userName)<1){
            $username='a';
            $myFlag=0;
        }else{
            if($userName==session('hellomarkerusername')){
                $myFlag=1;
            }
        }
        $sql="SELECT * FROM mk_user WHERE username='".$userName."';";
        $userHomeRow=$helloMarker->query($sql);
        $userHomeFlag=0;
        if(count($userHomeRow)==1 && $userHomeRow[0]['useractive']==1){
            $userHomeFlag=1;
        }
        $this->assign('userHomeRow',$userHomeRow);
        $this->assign('userHomeFlag',$userHomeFlag);
        $this->assign('myFlag',$myFlag);
        $this->display('userHome');
    }
    public function userHomeShare(){
        $helloMarker=new \Think\Model();
        $userName=$_GET['user'];
        if(strlen($userName)<1){
            $username='a';
            $myFlag=0;
        }else{
            if($userName==session('hellomarkerusername')){
                $myFlag=1;
            }
        }
        $sql="SELECT username,userid,useractive FROM mk_user WHERE username='".$userName."';";
        $userHomeRow=$helloMarker->query($sql);
        if(count($userHomeRow)==1 && $userHomeRow[0]['useractive']==1){
            $userHomeFlag=1;
        }else{
            $userHomeRow[0]['userid']=0;
            $userHomeFlag=0;
        }

        $sql="SELECT noteid FROM mk_note WHERE isshare=1 AND userid=".$userHomeRow[0]['userid'];
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
        $sql2="SELECT mk_note.noteid,mk_note.notename,mk_note.notediscusscount,mk_note_share.sharetime,mk_note.imgsrc from mk_note,mk_note_share WHERE mk_note.userid=".$userHomeRow[0]['userid']." AND mk_note.isshare=1 AND mk_note_share.noteid=mk_note.noteid ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";
        $userHomeShareRows=$helloMarker->query($sql2);
        $userHomeShareFlag=0;
        if(count($userHomeShareRows)>0){
            $userHomeShareFlag=1;
        }
        $this->assign('userHomeRow',$userHomeRow);
        $this->assign('userHomeFlag',$userHomeFlag);
        $this->assign('userHomeShareFlag',$userHomeShareFlag);
        $this->assign('userHomeShareRows',$userHomeShareRows);
        $this->assign('myFlag',$myFlag);
        $this->assign('show',$show);
        $this->display('userHomeShare');
    }
    public function userHomeCollect(){
        $helloMarker=new \Think\Model();
        $userName=$_GET['user'];
        if(strlen($userName)<1){
            $username='a';
            $myFlag=0;
        }else{
            if($userName==session('hellomarkerusername')){
                $myFlag=1;
            }
        }
        $sql="SELECT username,userid,useractive FROM mk_user WHERE username='".$userName."';";
        $userHomeRow=$helloMarker->query($sql);
        if(count($userHomeRow)==1 && $userHomeRow[0]['useractive']==1){
            $userHomeFlag=1;
        }else{
            $userHomeRow[0]['userid']=0;
            $userHomeFlag=0;
        }

        $sql="SELECT noteid FROM mk_note_collect WHERE  userid=".$userHomeRow[0]['userid'];
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
        $sql2="SELECT mk_note.noteid,mk_note.notename,mk_note.notediscusscount,mk_note_collect.collecttime,mk_note.imgsrc from mk_note,mk_note_collect WHERE mk_note_collect.userid=".$userHomeRow[0]['userid']." AND mk_note.isshare=1 AND mk_note_collect.noteid=mk_note.noteid ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";
        $userHomeShareRows=$helloMarker->query($sql2);
        $userHomeShareFlag=0;
        if(count($userHomeShareRows)>0){
            $userHomeShareFlag=1;
        }
        $this->assign('userHomeRow',$userHomeRow);
        $this->assign('userHomeFlag',$userHomeFlag);
        $this->assign('userHomeShareFlag',$userHomeShareFlag);
        $this->assign('userHomeShareRows',$userHomeShareRows);
        $this->assign('myFlag',$myFlag);
        $this->assign('show',$show);
        $this->display('userHomeCollect');
    }
    public function userHomeNote(){
        $helloMarker=new \Think\Model();
        $userName=session('hellomarkerusername');
        $myFlag=1;
        if(strlen($userName)<1){
            $username='a';
            $myFlag=0;
        }
        $sql="SELECT username,userid,useractive FROM mk_user WHERE username='".$userName."';";
        $userHomeRow=$helloMarker->query($sql);
        if(count($userHomeRow)==1 && $userHomeRow[0]['useractive']==1){
            $userHomeFlag=1;
        }else{
            $userHomeRow[0]['userid']=0;
            $userHomeFlag=0;
        }
        $this->userNoteWork($userHomeRow[0]['userid']);
        $this->assign('userHomeFlag',$userHomeFlag);
        $this->assign('userHomeRow',$userHomeRow);
        $this->assign('myFlag',$myFlag);
        $this->display('userHomeNote');
    }
    public function userNoteWork($userId='0'){
        $helloMarker=new \Think\Model();
        $sql="SELECT noteid FROM mk_note WHERE  userid=$userId;";
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
        $sql2="SELECT mk_note.* FROM mk_note WHERE mk_note.userid=$userId ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";
        $userNoteRows=$helloMarker->query($sql2);
        $userNoteFlag=0;
        if(count($userNoteRows)>0){
            $userNoteFlag=1;
        }
        $this->assign('userNoteFlag',$userNoteFlag);
        $this->assign('userNoteRows',$userNoteRows);
        $this->assign('show',$show);
    }
    public function myInfo(){
        $helloMarker=new \Think\Model();
        if(strlen(session('hellomarkerusername'))>0){
            $userName=session('hellomarkerusername');
        }else{
            $userName="0";
        }
        $sql="SELECT username,userid,useractive FROM mk_user WHERE username='".$userName."';";
        $userHomeRow=$helloMarker->query($sql);
        if(count($userHomeRow)==1 && $userHomeRow[0]['useractive']==1){
            $userHomeFlag=1;
        }else{
            $userHomeRow[0]['userid']=0;
            $userHomeFlag=0;
        }
        $this->assign('userHomeFlag',$userHomeFlag);
        $this->assign('userHomeRow',$userHomeRow);
        return $userHomeRow;
    }
    public function myNote(){
        $helloMarker=new \Think\Model();
        $myRow=$this->myInfo();
        $userId=$myRow[0]['userid'];
        $sql="SELECT noteid FROM mk_note WHERE  userid=$userId;";
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
        $sql2="SELECT mk_note.* FROM mk_note WHERE mk_note.userid=$userId ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";
        $myNoteRows=$helloMarker->query($sql2);
        $myNoteFlag=0;
        if(count($myNoteRows)>0){
            $myNoteFlag=1;
        }
        $this->assign('myNoteCount',$count);
        $this->assign('myNoteFlag',$myNoteFlag);
        $this->assign('myNoteRows',$myNoteRows);
        $this->assign('show',$show);
        $this->display('myNote');
    }

    public function myShare(){
        $helloMarker=new \Think\Model();
        $myRow=$this->myInfo();
        $userId=$myRow[0]['userid'];
        $sql="SELECT noteid FROM mk_note WHERE  userid=$userId AND isshare=1";
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
        $sql2="SELECT mk_note.*,mk_note_share.sharetime FROM mk_note,mk_note_share WHERE mk_note.userid=$userId AND isshare=1 AND mk_note.noteid=mk_note_share.noteid ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";
        $myNoteRows=$helloMarker->query($sql2);
        $myNoteFlag=0;
        if(count($myNoteRows)>0){
            $myNoteFlag=1;
        }
        $this->assign('myNoteCount',$count);
        $this->assign('myNoteFlag',$myNoteFlag);
        $this->assign('myNoteRows',$myNoteRows);
        $this->assign('show',$show);
        $this->display('myShare');
    }
    public function myCollect(){
        $helloMarker=new \Think\Model();
        $myRow=$this->myInfo();
        $userId=$myRow[0]['userid'];
        $sql="SELECT noteid FROM mk_note_collect WHERE  userid=$userId ";
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
        $sql2="SELECT noteid,collecttime FROM mk_note_collect WHERE userid=$userId ORDER BY noteid DESC LIMIT $page->firstRow,$page->listRows ;";
        $myNoteCollectRows=$helloMarker->query($sql2);
        for($i=0;$i<count($myNoteCollectRows);$i++){
            $sql="SELECT * from mk_note Where noteid=".$myNoteCollectRows[$i]['noteid'].";";
            $tempRow=$helloMarker->query($sql);
            $myNoteRows[$i]=$tempRow[0];
            $myNoteRows[$i]['collecttime']=$myNoteCollectRows[$i]['collecttime'];
        }
        $myNoteFlag=0;
        if(count($myNoteCollectRows)>0){
            $myNoteFlag=1;
        }
        $this->assign('myNoteCount',$count);
        $this->assign('myNoteFlag',$myNoteFlag);
        $this->assign('myNoteRows',$myNoteRows);
        $this->assign('show',$show);
        $this->display('myCollect');
    }
    public function mySearch(){
        $helloMarker=new \Think\Model();
        $myRow=$this->myInfo();
        $userId=$myRow[0]['userid'];
        $searchType=$_REQUEST['searchType'];
        $selectType=$_REQUEST['selectType'];
        $selectName=$_REQUEST['selectName'];
        if($searchType!='share'){
            $shareType='all';
        }
        if($selectType!='address' && $selectType!='name' ){
            $selectType='address';
        }
        if($searchType=='all'){
            if($selectType=='address'){
                $sql1="SELECT count(mk_note.noteid)  from mk_note WHERE userid=$userId AND mk_note.noteaddress LIKE '%".$selectName."%';";
            }else{
                $sql1="SELECT count(mk_note.noteid)  from mk_note WHERE userid=$userId AND mk_note.notename LIKE '%".$selectName."%';";
            }
        }else if($searchType=='share'){
            if($selectType=='address'){
                $sql1="SELECT count(mk_note.noteid)  from mk_note WHERE userid=$userId AND mk_note.isshare=1 AND mk_note.noteaddress LIKE '%".$selectName."%';";
            }else{
                $sql1="SELECT count(mk_note.noteid)  from mk_note WHERE userid=$userId AND mk_note.isshare=1 AND mk_note.notename LIKE '%".$selectName."%';";
            }
        }
        $count=$helloMarker->query($sql1);
        $count=$count[0]['count(mk_note.noteid)'];
        $pageCount=10;
        $page = new \Think\Page($count,$pageCount);
        $page->setConfig('prev', '<<');
        $page->setConfig('next', '>>');
        $page->setConfig('last', '末');
        $page->setConfig('first', '首');
        $page->setConfig('theme', '<li>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% </li>');
        $page->lastSuffix = false;
        $show = $page->show();
        if($searchType=='all'){
            if($selectType=='address'){
              $sql2="SELECT mk_note.noteid,mk_note.imgsrc,mk_note.notename,mk_note.notetime,mk_note.isshare,mk_note.notediscusscount,mk_note.userid,mk_user.userid,mk_user.usernickname,mk_user.userlogo from mk_note,mk_user WHERE mk_note.userid=mk_user.userid AND mk_note.noteaddress LIKE '%".$selectName."%' ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";

            }else{
              $sql2="SELECT mk_note.noteid,mk_note.imgsrc,mk_note.notename,mk_note.notetime,mk_note.isshare,mk_note.notediscusscount,mk_note.userid,mk_user.userid,mk_user.usernickname,mk_user.userlogo from mk_note,mk_user WHERE mk_note.userid=mk_user.userid AND mk_note.noteaddress LIKE '%".$selectName."%' ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";
            }
        }else if($searchType=='share'){
            if($selectType=='address'){
              $sql2="SELECT mk_note.noteid,mk_note.imgsrc,mk_note.notename,mk_note.notetime,mk_note.isshare,mk_note.notediscusscount,mk_note.userid,mk_user.userid,mk_user.usernickname,mk_user.userlogo,mk_note_share.sharetime,mk_note_share.noteid  from mk_note,mk_user,mk_note_share  WHERE mk_note.isshare=1 AND mk_note.userid=mk_user.userid  AND mk_note_share.noteid=mk_note.noteid AND mk_note.noteaddress LIKE '%".$selectName."%' ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";
            }else{
              $sql2="SELECT mk_note.noteid,mk_note.imgsrc,mk_note.notename,mk_note.notetime,mk_note.isshare,mk_note.notediscusscount,mk_note.userid,mk_user.userid,mk_user.usernickname,mk_user.userlogo,mk_note_share.sharetime,mk_note_share.noteid  from mk_note,mk_user,mk_note_share  WHERE mk_note.isshare=1 AND mk_note.userid=mk_user.userid  AND mk_note_share.noteid=mk_note.noteid AND mk_note.noteaddress LIKE '%".$selectName."%' ORDER BY mk_note.noteid DESC LIMIT $page->firstRow,$page->listRows ;";
            }

        }
         $mySearchRows=$helloMarker->query($sql2);
         $mySearchFlag=0;
         if(count($mySearchRows)>0){
            $mySearchFlag=1;
         }
        // echo "<pre>";
        //     print_r($noteShare);
        // echo "</pre>";
        // exit();
        $this->noteShareCollect(4);
        $this->assign('searchCount',$count);
        $this->assign('mySearchFlag',$mySearchFlag);
        $this->assign('mySearchRows',$mySearchRows);
        $this->assign('show',$show);
        $this->display('mysearch');
    }
    public function myNoteDelete(){
        $helloMarker=new \Think\Model();
        $noteId=$_REQUEST['id'];
        $userId=session('hellomarkeruserid');
        $sql="SELECT userid FROM mk_note WHERE noteid=$noteId";
        $noteUserRow=$helloMarker->query($sql);
        if($noteUserRow[0]['userid']!=$userId){
            $this->redirect('myNote');
            exit();
        }
        $sql="DELETE FROM mk_note WHERE noteid=$noteId AND userid=$userId;";
        if($helloMarker->execute($sql)){
            $sql="DELETE FROM mk_note_share WHERE noteid=$noteId AND userid=$userId;";
            if($helloMarker->execute($sql)){
                $sql="DELETE FROM mk_note_collect WHERE noteid=$noteId";
                if($helloMarker->execute($sql)){
                    $sql="SELECT discussid FROM mk_note_discuss WHERE noteid=$noteId";
                    $noteDiscussRows=$helloMarker->query($sql);
                    for($i=0;$i<count($noteDiscussRows);$i++){
                        $sql="DELETE FROM mk_note_discuss_like WHERE discussid=".$noteDiscussRows[$i]['discussid'].";";
                        if($helloMarker->execute($sql)){
                            $this->assign('myErrorFlag',2);
                            $this->assign('myErrorInfo',"成功删除！");
                            $this->myNote();
                            exit();                          
                        }else{
                            $this->assign('myErrorFlag',1);
                            $this->assign('myErrorInfo',"删除失败");
                            $this->myNote();
                            exit();
                        }
                    }
                    if(count($noteDiscussRows)>0){
                        $sql="DELETE FROM mk_note_discuss WHERE noteid=$noteId ";
                        if($helloMarker->execute($sql)){
                            $this->assign('myErrorFlag',2);
                            $this->assign('myErrorInfo',"删除成功！");
                            $this->myNote();
                            exit();
                        }else{
                            $this->assign('myErrorFlag',3);
                            $this->assign('myErrorInfo',"部分信息清除失败");
                            $this->myNote();
                            exit();
                        }
                    }
                }else{
                    $this->assign('myErrorFlag',3);
                    $this->assign('myErrorInfo',"部分信息清除失败");
                    $this->myNote();
            exit();                    
                }
            }else{
                $this->assign('myErrorFlag',3);
                $this->assign('myErrorInfo',"部分信息清除失败");
                $this->myNote();
                exit();
            }
        }else{
            $this->assign('myErrorFlag',1);
            $this->assign('myErrorInfo',"删除失败");
            $this->myNote();
            exit();
        }
    }
    public function myNoteShareDelete(){
        $helloMarker=new \Think\Model();
        $noteId=$_REQUEST['id'];
        $userId=session('hellomarkeruserid');
        $sql="SELECT userid FROM mk_note WHERE noteid=$noteId";
        $noteUserRow=$helloMarker->query($sql);
        if($noteUserRow[0]['userid']!=$userId){
            $this->redirect('myNote');
            exit();
        }
        $sql="UPDATE mk_note SET isshare = 0 WHERE noteid=$noteId AND userid=$userId;";
        if($helloMarker->execute($sql)){
            $sql="DELETE FROM mk_note_share WHERE noteid=$noteId AND userid=$userId;";
            if($helloMarker->execute($sql)){
                $sql="DELETE FROM mk_note_collect WHERE noteid=$noteId";
                if($helloMarker->execute($sql)){
                    $sql="SELECT discussid FROM mk_note_discuss WHERE noteid=$noteId";
                    $noteDiscussRows=$helloMarker->query($sql);
                    for($i=0;$i<count($noteDiscussRows);$i++){
                        $sql="DELETE FROM mk_note_discuss_like WHERE discussid=".$noteDiscussRows[$i]['discussid'].";";
                        if($helloMarker->execute($sql)){
                            $this->assign('myErrorFlag',2);
                            $this->assign('myErrorInfo',"成功取消分享！");
                            $this->myNote();
                            exit();
                        }else{
                            $this->assign('myErrorFlag',1);
                            $this->assign('myErrorInfo',"取消分享失败");
                            $this->myShare();
                            exit();
                        }
                    }
                    if(count($noteDiscussRows)>0){
                        $sql="DELETE FROM mk_note_discuss WHERE noteid=$noteId ";
                        if($helloMarker->execute($sql)){
                            $this->assign('myErrorFlag',2);
                            $this->assign('myErrorInfo',"成功取消分享！");
                            $this->myShare();
                            exit();
                        }else{
                            $this->assign('myErrorFlag',3);
                            $this->assign('myErrorInfo',"评论删除失败！");
                            $this->myShare();
                            exit();
                        }
                    }
                }else{
                    $this->assign('myErrorFlag',3);
                    $this->assign('myErrorInfo',"收藏删除失败！");
                    $this->myShare();
                    exit();                  
                }
            }else{
                $this->assign('myErrorFlag',3);
                $this->assign('myErrorInfo',"分享删除失败！");
                $this->myShare();
                exit(); 
            }
        }else{
            $this->assign('myErrorFlag',1);
            $this->assign('myErrorInfo',"取消分享失败！");
            $this->myShare();
            exit(); 
        }

    }
    public function myNoteShareAdd(){
        $helloMarker=new \Think\Model();
        $noteId=$_REQUEST['id'];
        $userId=session('hellomarkeruserid');
        $sql="SELECT userid FROM mk_note WHERE noteid=$noteId";
        $noteUserRow=$helloMarker->query($sql);
        if($noteUserRow[0]['userid']!=$userId){
            $this->redirect('myNote');
            exit();
        }
        $sql="UPDATE mk_note SET isshare = 1 WHERE noteid=$noteId AND userid=$userId;";
        if($helloMarker->execute($sql)){
            $shareTime=date('Y-m-d');
            $sql="INSERT INTO mk_note_share (noteid,userid,sharetime) VALUES ($noteId,$userId,'".$shareTime."');";
            if($helloMarker->execute($sql)){
                $this->assign('myErrorFlag',2);
                $this->assign('myErrorInfo',"分享成功");
                $this->myShare();
                exit();
            }else{
                $this->assign('myErrorFlag',3);
                $this->assign('myErrorInfo',"分享插入失败");
                $this->myShare();
                exit();
            }
        }else{
            $this->assign('myErrorFlag',1);
            $this->assign('myErrorInfo',"分享失败！");
            $this->myNote();
            exit(); 
        }
    }
    public function myNoteCollectDelete(){
        $helloMarker=new \Think\Model();
        $noteId=$_REQUEST['id'];
        $userId=session('hellomarkeruserid');
        $sql="DELETE FROM mk_note_collect WHERE userid=$userId AND noteid = $noteId;";
        if($helloMarker->execute($sql)){
            $sql="UPDATE mk_note SET notecollectcount=notecollectcount-1 WHERE noteid = $noteId;";
            if($helloMarker->execute($sql)){
                $this->assign('myErrorFlag',2);
                $this->assign('myErrorInfo',"成功取消收藏！");
                $this->myCollect();
                exit(); 
            }else{
                $this->assign('myErrorFlag',3);
                $this->assign('myErrorInfo',"数据更新失败！");
                $this->myCollect();
                exit();                 
            }
        }else{
            $this->assign('myErrorFlag',1);
            $this->assign('myErrorInfo',"未取消收藏！");
            $this->myCollect();
            exit(); 
        }
    }
    public function newNote(){
        $this->checkAcl();
        $this->display('newNote');
    }
    public function newNoteAdd(){
        $userId=session('hellomarkeruserid');
        $noteName=$_POST['notetitle'];
        $noteAddress=$_POST['noteaddress'];
        $noteOther=$_POST['noteother'];
        if(strlen($noteOther)==0){
            $noteOtherFlag=0;
        }else{
            $noteOtherFlag=1;
        }
        $noteShare=$_POST['noteshare'];
        if($noteShare!=1){
            $noteShare=0;
        }
        $noteTime=date("Y-m-d");
        $public=$_POST['public'];
        $imagesrc="hm_".time();
        $noteArray=array(
            'notetitle' => $noteName,
            'noteaddress' => $noteAddress,
            'noteother' => $noteOther
        );
        if((strlen($noteAddress)==0) || (ctype_space($noteName))){
            $newNoteErrorFlag=1;
            $newNoteErrorInfo="请正确填写地址！";
            $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
            $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
            $this->assign('noteArray',$noteArray);
            $this->newNote();
            exit();
        }
        if((strlen($noteName)==0) || (ctype_space($noteName))){
            $newNoteErrorFlag=1;
            $newNoteErrorInfo="请正确填写标题！";
            $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
            $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
            $this->assign('noteArray',$noteArray);
            $this->newNote();
            exit();
        }
        $helloMarker=new \Think\Model();
        $upload = new \Think\Upload();
        $userRow=$this->myInfo();
        $upload->maxSize   =     2145728 ;
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp');
        $upload->rootPath  =     './Public/uploads/markerimage/';
        $upload->saveName  =     $imagesrc;
        $upload->replace   =     true;
        $upload->autoSub  =      false;
        $info   =   $upload->upload();
        if(!$info) {
            if($upload->getError()=='没有文件被上传！'){
                $info['noteimage']['savename']='1.jpg';
            }else{
                $newNoteErrorFlag=1;
                $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
                $newNoteErrorInfo=$upload->getError();
                $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
                $this->assign('noteArray',$noteArray);
                $this->newNote();
                exit();
            }
        }
        $image = new \Think\Image(); 
        if($image->open('./Public/uploads/markerimage/'.$info['noteimage']['savename'])){
            if($image->thumb(300, 200,\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/uploads/markerimage/'.$info['noteimage']['savename'])){
            }else{
                $newNoteErrorFlag=1;
                $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
                $newNoteErrorInfo="图片处理失败！";
                $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
                $this->assign('noteArray',$noteArray);
                $this->newNote();
                exit();
            }
            if($image->thumb(100, 100,\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/uploads/markerimage/src/'.$info['noteimage']['savename'])){
            }else{
                $newNoteErrorFlag=1;
                $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
                $newNoteErrorInfo="图片处理失败！";
                $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
                $this->assign('noteArray',$noteArray);
                $this->newNote();
                exit();
            }
        }else{
                $newNoteErrorFlag=1;
                $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
                $newNoteErrorInfo="图片处理失败！";
                $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
                $this->assign('noteArray',$noteArray);
                $this->newNote();
                exit();
        }
        if($noteOtherFlag==0){
            $sql="INSERT INTO mk_note (notename,noteaddress,notetime,userid,isshare,imagesrc,imgsrc) VALUES('".$noteName."','".$noteAddress."','".$noteTime."',$userId,$noteShare,'".$info['noteimage']['savename']."','".$info['noteimage']['savename']."');";
        }else{
            $sql="INSERT INTO mk_note (notename,noteaddress,notetime,userid,isshare,imagesrc,imgsrc,noteOther) VALUES('".$noteName."','".$noteAddress."','".$noteTime."',$userId,$noteShare,'".$info['noteimage']['savename']."','".$info['noteimage']['savename']."','".$noteOther."');";
        }
        if($helloMarker->execute($sql)){
            if($noteShare==1){
                $sql="SELECT noteid,userid FROM mk_note WHERE imagesrc='".$info['noteimage']['savename']."'";
                $tempRow=$helloMarker->query($sql);
                if(count($tempRow)==1){
                    $sql="INSERT INTO mk_note_share (noteid,userid,sharetime) VALUES (".$tempRow[0]['noteid'].",".$tempRow[0]['userid'].",$noteTime);";
                    if($helloMarker->execute($sql)){
                        $this->assign('myErrorFlag',2);
                        $this->assign('myErrorInfo',"成功添加！");
                        $this->myNote();
                        exit();
                    }else{
                        $this->assign('myErrorFlag',1);
                        $this->assign('myErrorInfo',"添加分享失败");
                        $this->myNote();
                        exit();
                    }
                }else{
                    $this->assign('myErrorFlag',1);
                    $this->assign('myErrorInfo',"多数据冲突");
                    $this->myNote();
                    exit();
                }
            }else{
                $this->assign('myErrorFlag',2);
                $this->assign('myErrorInfo',"未分享");
                $this->myNote();
                exit();
            }
        }else{
                $newNoteErrorFlag=1;
                $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
                $newNoteErrorInfo="数据错误";
                $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
                $this->assign('noteArray',$noteArray);
                $this->myNote();
                exit();
        }
    }
    public function editNote(){
        $helloMarker=new \Think\Model();
        $noteId=$_REQUEST['id'];
        if($noteId>0){

        }else{
            $noteId=0;
        }
        $sql="SELECT noteid,notename,userid,noteaddress,noteother,imgsrc,imagesrc FROM mk_note WHERE noteid=$noteId;";
        $editInfo=$helloMarker->query($sql);
        $editFlag=1;
        if(($editInfo[0]['userid']!=session('hellomarkeruserid'))||count($editInfo)!=1){
            $editFlag=0;
        }
        $this->assign('editFlag',$editFlag);
        $this->assign('editInfo',$editInfo);
        $this->display('editNote');
    }
    public function editNoteAdd(){
        $userId=session('hellomarkeruserid');
        $noteName=$_POST['notename'];
        $noteAddress=$_POST['noteaddress'];
        $noteTime=date("Y-m-d");
        $noteOther=$_POST['noteother'];
        $noteId=$_POST['noteid'];
        $imageSrc=$_POST['imagesrc'];
        if(strlen($noteOther)==0){
            $noteOther=" ";
            $noteOtherFlag=0;
        }else{
            $noteOtherFlag=1;
        }
        $public=$_POST['public'];
        $editInfo[0]=array(
            'noteid' => $noteId,
            'notename' => $noteName,
            'noteaddress' => $noteAddress,
            'noteother' => $noteOther,
            'imgsrc' => $imageSrc,
            'imagesrc'=>$imageSrc
        );
        // echo "<pre>";
        //     print_r($editInfo);
        // echo "</pre>";
        // exit();
        if((strlen($noteAddress)==0) || (ctype_space($noteName))){
            $editErrorFlag=1;
            $editErrorInfo="请正确填写地址！";
            $this->assign('editErrorFlag',$editErrorFlag);
            $this->assign('editErrorInfo',$editErrorInfo);
            $this->assign('editInfo',$editInfo);
            $this->display("editNote");
            exit();
        }
        if((strlen($noteName)==0) || (ctype_space($noteName))){
            $newNoteErrorFlag=1;
            $newNoteErrorInfo="请正确填写标题！";
            $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
            $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
            $this->assign('noteArray',$noteArray);
            $this->redirect("editNote");
            exit();
        }
        $helloMarker=new \Think\Model();
        $upload = new \Think\Upload();
        $userRow=$this->myInfo();
        $upload->maxSize   =     2145728 ;
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp');
        $upload->rootPath  =     './Public/uploads/markerimage/';
        $upload->saveName  =     "hm_".time();
        $upload->replace   =     true;
        $upload->autoSub  =      false;
        $imageUploadFlag=1;
        $info   =   $upload->upload();
        if(!$info) {
            if($upload->getError()=='没有文件被上传！'){
                $imageUploadFlag=0;
            }else{
                $newNoteErrorFlag=1;
                $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
                $newNoteErrorInfo=$upload->getError();
                $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
                $this->assign('noteArray',$noteArray);
                $this->editNote();
                exit();
            }
        }

        if($imageUploadFlag!=0){
            if($editInfo[0]['imagesrc']=='1.jpg'){
                $editInfo[0]['imagesrc']=$info['noteimage']['savename'];
                $editInfo[0]['imgsrc']=$info['noteimage']['savename'];
            }
            $image = new \Think\Image(); 
            if($image->open('./Public/uploads/markerimage/'.$info['noteimage']['savename'])){
                if($image->thumb(300, 200,\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/uploads/markerimage/'.$editInfo[0]['imagesrc'])){
                }else{
                    $newNoteErrorFlag=1;
                    $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
                    $newNoteErrorInfo="图片处理失败！";
                    $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
                    $this->assign('noteArray',$noteArray);
                    $this->editNote();
                    exit();
                }
                if($image->thumb(100, 100,\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/uploads/markerimage/src/'.$editInfo[0]['imgsrc'])){
                }else{
                    $newNoteErrorFlag=1;
                    $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
                    $newNoteErrorInfo="图片处理失败！";
                    $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
                    $this->assign('noteArray',$noteArray);
                    $this->editNote();
                    exit();
                }
            }else{
                    $newNoteErrorFlag=1;
                    $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
                    $newNoteErrorInfo="图片处理失败！";
                    $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
                    $this->assign('noteArray',$noteArray);
                    $this->editNote();
                    exit();
            }
        }
        
        if($noteOtherFlag==0){
            $sql="UPDATE mk_note SET notename='".$noteName."',noteaddress='".$noteAddress."',notetime='".$noteTime."',userid=".$userId.",imagesrc='".$editInfo[0]['imagesrc']."',imgsrc='".$editInfo[0]['imagesrc']."' WHERE noteid=$noteId;";
        }else if($noteOtherFlag==1){
            $sql="UPDATE mk_note SET notename='".$noteName."',noteaddress='".$noteAddress."',notetime='".$noteTime."',userid=".$userId.",imagesrc='".$editInfo[0]['imagesrc']."',imgsrc='".$editInfo[0]['imagesrc']."',noteother='".$noteOther."'  WHERE noteid=$noteId;";
        }
        if($helloMarker->execute($sql)){
            if($noteShare==1){
                $sql="SELECT noteid,userid FROM mk_note WHERE imagesrc='".$info['noteimage']['savename']."'";
                $tempRow=$helloMarker->query($sql);
                if(count($tempRow)==1){
                    $sql="INSERT INTO mk_note_share (noteid,userid,sharetime) VALUES (".$tempRow[0]['noteid'].",".$tempRow[0]['userid'].",$noteTime);";
                    if($helloMarker->execute($sql)){
                        $this->assign('myErrorFlag',2);
                        $this->assign('myErrorInfo',"成功添加！");
                        $this->myNote();
                        exit();
                    }else{
                        $this->assign('myErrorFlag',1);
                        $this->assign('myErrorInfo',"添加分享失败");
                        $this->myNote();
                        exit();
                    }
                }else{
                    $this->assign('myErrorFlag',1);
                    $this->assign('myErrorInfo',"多数据冲突");
                    $this->myNote();
                    exit();
                }
            }else{
                $this->assign('myErrorFlag',2);
                $this->assign('myErrorInfo',"未分享");
                $this->myNote();
                exit();
            }
        }else{
                $newNoteErrorFlag=1;
                $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
                $newNoteErrorInfo="数据错误";
                $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
                $this->assign('noteArray',$noteArray);
                $this->myNote();
                exit();
        }
    }
}
