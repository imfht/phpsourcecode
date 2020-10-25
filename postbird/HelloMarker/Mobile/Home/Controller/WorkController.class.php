<?php
namespace Home\Controller;
use Think\Controller;
class WorkController extends AclController {
    public function checkAcl(){
        if(session('hellomarkeruserlogin')==1 && session('hellomarkeruserid')>0){
            return 1;
        }else{
            return 0;
        }
    }
    public function index(){
        if($this->checkAcl()){
            $this->loginWork();
        }else{
            $this->noLoginWork();
        }
    }
    public function loginWork(){
        // echo strtotime(date('Y-m-d'));
        $this->nowWork();
        $this->oldFutureWork();
        $this->display("loginWork");
    }
    public function nowWork(){
        $this->checkAcl();
        $dateNow=date('Y-m-d');
        $dateNowStamp=strtotime($dateNow);
        $userId=session('hellomarkeruserid');
        $helloMarker=new \Think\Model();
        $nowWorkRows=array();
        $sql="SELECT * FROM mk_work WHERE userid = $userId AND worktime=$dateNowStamp ORDER BY worklevel desc;";
        $nowWorkRows=$helloMarker->query($sql);
        $nowWorkCount=count($nowWorkRows);
        $this->assign('dateNow',$dateNow);
        $this->assign('nowWorkRows',$nowWorkRows);
        $this->assign('nowWorkCount',$nowWorkCount);
    }
    public function findClickDay(){
        $dateClick=$_POST['date'];
        $dateClickStamp=strtotime($dateClick);
        $userId=session('hellomarkeruserid');
        $helloMarker=new \Think\Model();
        $clickWorkRows=array();
        $sql="SELECT * FROM mk_work WHERE userid = $userId AND worktime=$dateClickStamp ORDER BY worklevel desc;";
        $clickWorkRows=$helloMarker->query($sql);
        $clickWorkCount=count($clickWorkRows);
        $arr['clickWorkRows']=$clickWorkRows;
        $arr['clickWorkCount']=$clickWorkCount;
        $arr['dateClickStamp']=$dateClickStamp;
        $this->ajaxReturn($arr,json);
    }
     public function oldFutureWork(){
        // $this->checkAcl();
        $postdate=$_POST['date'];
        $dateNow=date('Y-m-d');
        $dateNowStamp=strtotime($dateNow);
        $userId=session('hellomarkeruserid');
        $helloMarker=new \Think\Model();
        $oldWorkRows=array();
        $futureWorkRows=array();
        $sql1="SELECT worktime,GROUP_CONCAT(workname),GROUP_CONCAT(workid),GROUP_CONCAT(workother),GROUP_CONCAT(worklevel) FROM mk_work WHERE userid = $userId AND worktime<$dateNowStamp  GROUP BY worktime ORDER BY worktime ASC , worklevel desc;";
        $sql2="SELECT worktime,GROUP_CONCAT(workname),GROUP_CONCAT(workid),GROUP_CONCAT(workother),GROUP_CONCAT(worklevel) FROM mk_work WHERE userid = $userId AND worktime>=$dateNowStamp  GROUP BY worktime ORDER BY worktime ASC , worklevel desc;";
        $oldWorkRows=$helloMarker->query($sql1);
        $futureWorkRows=$helloMarker->query($sql2);
        $oldWorkCount=count($oldWorkRows);
        $futureWorkCount=count($futureWorkRows);
        $oldHtmlRows=array();
        $futureHtmlRows=array();
        for($i=0;$i<$oldWorkCount;$i++){
            $oldHtmlRows[$i]['worktime']=date('Y-m-d',$oldWorkRows[$i]['worktime']);
            $oldWorkRows[$i]['workname']=explode(',',$oldWorkRows[$i]['group_concat(workname)']);
            $oldWorkRows[$i]['workother']=explode(',',$oldWorkRows[$i]['group_concat(workother)']);
            $oldWorkRows[$i]['worklevel']=explode(',',$oldWorkRows[$i]['group_concat(worklevel)']);
            $oldWorkRows[$i]['workid']=explode(',',$oldWorkRows[$i]['group_concat(workid)']);
        }
        for($i=0;$i<$futureWorkCount;$i++){
            $futureHtmlRows[$i]['worktime']=date('Y-m-d',$futureWorkRows[$i]['worktime']);
            $futureWorkRows[$i]['workname']=explode(',',$futureWorkRows[$i]['group_concat(workname)']);
            $futureWorkRows[$i]['workother']=explode(',',$futureWorkRows[$i]['group_concat(workother)']);
            $futureWorkRows[$i]['worklevel']=explode(',',$futureWorkRows[$i]['group_concat(worklevel)']);
            $futureWorkRows[$i]['workid']=explode(',',$futureWorkRows[$i]['group_concat(workid)']);
        }
        for($i=0;$i<$futureWorkCount;$i++){
            for($j=0;$j<count($futureWorkRows[$i]['workname']);$j++){
                $futureHtmlRows[$i][$j]['workother']=$futureWorkRows[$i]['workother'][$j];
                $futureHtmlRows[$i][$j]['workname']=$futureWorkRows[$i]['workname'][$j];
                $futureHtmlRows[$i][$j]['worklevel']=$futureWorkRows[$i]['worklevel'][$j];
                $futureHtmlRows[$i][$j]['workid']=$futureWorkRows[$i]['workid'][$j];
            }
        }
        for($i=0;$i<$oldWorkCount;$i++){
            for($j=0;$j<count($oldWorkRows[$i]['workname']);$j++){
                $oldHtmlRows[$i][$j]['workother']=$oldWorkRows[$i]['workother'][$j];
                $oldHtmlRows[$i][$j]['workname']=$oldWorkRows[$i]['workname'][$j];
                $oldHtmlRows[$i][$j]['worklevel']=$oldWorkRows[$i]['worklevel'][$j];
                $oldHtmlRows[$i][$j]['workid']=$oldWorkRows[$i]['workid'][$j];
            }
        }
        $oldHtmlCount=count($oldHtmlRows);
        $futureHtmlCount=count($futureHtmlRows);
        $this->assign('futureHtmlCount',$futureHtmlCount);
        $this->assign('oldHtmlCount',$oldHtmlCount);
        $this->assign('futureHtmlRows',$futureHtmlRows);
        $this->assign('oldHtmlRows',$oldHtmlRows);
        // echo "<pre>";
        //     print_r($futureHtmlRows);
        // echo "</pre>";
        // exit();
        // $dateNow="2016-03-03";
        // echo strtotime($dateNow);
        // exit();
    }
    public function noLoginWork(){
        $this->display('noLoginWork');
    }
    public function newWork(){
        $this->checkAcl();
        $this->display('newWork');
    }
    public function newWorkAdd(){
        if(!$this->checkAcl()){
            $this->index();
            exit();
        }
        $userId=session('hellomarkeruserid');
        $workTitle=$_POST['worktitle'];
        $workTime=$_POST['worktime'];
        $workOther=$_POST['workother'];
        $workLevel=$_POST['worklevel'];
        $workArray=array(
            'worktitle' => $workTitle,
            'workother' => $workOther,
            'worktime' => $workTime
        );
        if((strlen($workOther)==0)){
           $workOther="暂无其他";
        }
        if((strlen($workTitle)==0) || (ctype_space($workTitle))){
            $newNoteErrorFlag=1;
            $newNoteErrorInfo="标题有误！";
            $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
            $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
            $this->assign('workArray',$workArray);
            $this->newWork();
            exit();
        }
        $dateFormat=$this->checkDateIsValid($workTime);
        if((strlen($workTime)==0) || (ctype_space($workTime)) || (!$dateFormat)){
            $newNoteErrorFlag=1;
            $newNoteErrorInfo="日期有误！";
            $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
            $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
            $this->assign('workArray',$workArray);
            $this->newWork();
            exit();
        }
        $workTimeStamp=strtotime($workTime);
        $helloMarker=new \Think\Model();
        $sql="INSERT INTO mk_work (workname,worktime,workother,userid,worklevel) VALUES ('".$workTitle."','".$workTimeStamp."','".$workOther."',$userId,$workLevel);";
        if($helloMarker->execute($sql)){
            $newNoteErrorFlag=2;
            $newNoteErrorInfo="添加成功,点击返回!";
            $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
            $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
            $this->newWork();
            exit();
        }else{
            $newNoteErrorFlag=1;
            $newNoteErrorInfo="添加失败,请重试!";
            $this->assign('newNoteErrorFlag',$newNoteErrorFlag);
            $this->assign('newNoteErrorInfo',$newNoteErrorInfo);
            $this->assign('workArray',$workArray);
            $this->newWork();
            exit();
        }
    }
    public function checkDateIsValid($date, $formats = array("Y-m-d", "Y/m/d")) {
        $unixTime = strtotime($date);
        if (!$unixTime) { 
            return false;
        }
        foreach ($formats as $format) {
            if (date($format, $unixTime) == $date) {
                return true;
            }
        }
        return false;
    }
    public function myWork(){
        if(!$this->checkAcl()){
            $this->index();
            exit();
        }
        $helloMarker=new \Think\Model();
        $userId=session('hellomarkeruserid');
        $sql="SELECT * FROM mk_work WHERE userid=$userId ORDER BY worktime desc , worklevel desc;";
        $myWorkRows=$helloMarker->query($sql);
        $myWorkCount=count($myWorkRows);
        $myWorkFlag=0;
        if($myWorkCount>=1){
            $myWorkFlag=1;
        }
        for($i=0;$i<$myWorkCount;$i++){
            $myWorkRows[$i]['worktime']=date('Y-m-d',$myWorkRows[$i]['worktime']);
        }
        $this->assign('myWorkFlag',$myWorkFlag);
        $this->assign('myWorkCount',$myWorkCount);
        $this->assign('myWorkRows',$myWorkRows);
        $this->display('myWork');
    }
    public function myWorkDelete(){
        if(!$this->checkAcl()){
            $this->index();
            exit();
        }
        $helloMarker=new \Think\Model();
        $workId=$_REQUEST['id'];
        $userId=session('hellomarkeruserid');
        $sql="SELECT * FROM mk_work WHERE workid=$workId AND userid=$userId";
        $rowFlag=$helloMarker->query($sql);
        if(count($rowFlag)==0){
            $myErrorFlag=1;
            $myErrorInfo="删除失败!";
            $this->assign('myErrorFlag',$myErrorFlag);
            $this->assign('myErrorInfo',$myErrorInfo);
            $this->myWork();
            exit();
        }
        $sql="DELETE FROM mk_work WHERE workid=$workId AND userid = $userId";
        if($helloMarker->execute($sql)){
            $myErrorFlag=2;
            $myErrorInfo="删除成功!";
            $this->assign('myErrorFlag',$myErrorFlag);
            $this->assign('myErrorInfo',$myErrorInfo);
            $this->myWork();
            exit();
        }else{
            $myErrorFlag=1;
            $myErrorInfo="删除失败!";
            $this->assign('myErrorFlag',$myErrorFlag);
            $this->assign('myErrorInfo',$myErrorInfo);
            $this->myWork();
            exit();
        }
        $this->display('myWork');
    }
}