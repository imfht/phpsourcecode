<?php
namespace Home\Controller;
use Think\Controller;
class AccountController extends AclController {
    public function checkAcl(){
        if(session('hellomarkeruserlogin')==1 && session('hellomarkeruserid')>0){
            return 1;
        }else{
            return 0;
        }
    }
    public function index(){
        if($this->checkAcl()){
            $this->loginAccount();
        }else{
            $this->noLoginAccount();
        }
   }
   public function setBudget(){
    $this->display('setBudget');
   }
   public function loginAccount(){
        $userAccountArray=$this->userAccountAll();
        // echo "<pre>";
        //     print_r($userAccountArray['monthAccountRows']);
        // echo "</pre>";
        $todayOut=0;
        $weekOut=0;
        $monthOut=0;
        $yearOut=0;
        if($userAccountArray['todayAccountCount']>0){
            for($i=0;$i<$userAccountArray['todayAccountCount'];$i++){
                $todayOut=$todayOut+(int)$userAccountArray['todayAccountRows'][$i]['accountmoney'];
            }
        }
        if($userAccountArray['weekAccountCount']>0){
            for($i=0;$i<$userAccountArray['weekAccountCount'];$i++){
                $weekOut=$weekOut+(int)$userAccountArray['weekAccountRows'][$i]['accountmoney'];
            }
        }
        if($userAccountArray['monthAccountCount']>0){
            for($i=0;$i<$userAccountArray['monthAccountCount'];$i++){
                $monthOut=$monthOut+(int)$userAccountArray['monthAccountRows'][$i]['accountmoney'];
            }
        }
        if($userAccountArray['yearAccountCount']>0){
            for($i=0;$i<$userAccountArray['yearAccountCount'];$i++){
                $yearOut=$yearOut+(int)$userAccountArray['yearAccountRows'][$i]['accountmoney'];
            }
        }
        $accountOut=array(
            'todayOut' => $todayOut,
            'weekOut' => $weekOut,
            'monthOut' => $monthOut,
            'yearOut' => $yearOut
        );
        $this->assign('todayAccountRows',$userAccountArray['todayAccountRows']);
        $this->assign('todayAccountCount',$userAccountArray['todayAccountCount']);
        $this->assign('weekAccountRows',$userAccountArray['weekAccountRows']);
        $this->assign('weekAccountCount',$userAccountArray['weekAccountCount']);
        $this->assign('monthAccountRows',$userAccountArray['monthAccountRows']);
        $this->assign('monthAccountCount',$userAccountArray['monthAccountCount']);
        $this->assign('accountOut',$accountOut);
        $this->assign('userBudget',$userAccountArray['userAccountBudget']);
        $this->display('loginAccount');
   }
   public function userAccountAll(){
    //day stamp 86400
        $stampPlus=86400;
        $helloMarker=new \Think\Model();
        $userId=session('hellomarkeruserid');
        $monthBeginDate=date('Y-m-01', strtotime(date("Y-m-d")));
        $tFymdStamp=strtotime( $monthBeginDate);
        $tLymdStamp=$tFymdStamp+((date("t")-1)*$stampPlus);
        $weekArray=array(0 => 7,1 => 1,2 => 2, 3 => 3,4 => 4,5 => 5,6 =>6);
        $tW=$weekArray[date('w')];
        $todayStamp=strtotime(date('Y-m-d'));
        $fWeekStamp=$todayStamp-(($tW-1)*$stampPlus);
        $yearBeginStamp=strtotime(date('Y').'-01-01');
        $yearLastStamp=strtotime(date('Y').'-12-31');
        $allAccountRows=array();
        $monthAccountRows=array();
        $todayAccountRows=array();
        $yearAccountRows=array();
        $weekAccountRows=array();
        $sql="SELECT mk_account.*,mk_account_type.typename FROM mk_account,mk_account_type WHERE mk_account.userid=$userId AND mk_account_type.typeid=mk_account.typeid ORDER BY accounttime ASC;";
        $allAccountRows=$helloMarker->query($sql); 
        $allAccountCount=count($allAccountRows);
        $countFlagToday=0;
        $countFlagYear=0;
        $countFlagMonth=0;
        $countFlagWeek=0;
        for($i=0;$i<$allAccountCount;$i++){
            if($allAccountRows[$i]['accounttime']==$todayStamp){
                $todayAccountRows[$countFlagToday]=$allAccountRows[$i];
                $countFlagToday++;
            }
            if(((int)$allAccountRows[$i]['accounttime']>=$tFymdStamp) && ((int)$allAccountRows[$i]['accounttime']<=$tLymdStamp)){
                $monthAccountRows[$countFlagMonth]=$allAccountRows[$i];
                $countFlagMonth++;
            }
            if(((int)$allAccountRows[$i]['accounttime']>=$fWeekStamp) && ((int)$allAccountRows[$i]['accounttime']<=$todayStamp)){
                $weekAccountRows[$countFlagWeek]=$allAccountRows[$i];
                $countFlagWeek++;
            }
            if(((int)$allAccountRows[$i]['accounttime']>=$yearBeginStamp) && ((int)$allAccountRows[$i]['accounttime']<=$yearLastStamp)){
                $yearAccountRows[$countFlagYear]=$allAccountRows[$i];
                $countFlagYear++;
            }
        }
        $todayAccountCount=count($todayAccountRows);
        $monthAccountCount=count($monthAccountRows);
        $weekAccountCount=count($weekAccountRows);
        $yearAccountCount=count($yearAccountRows);

        $userAccountArray=array(
            'allAccountRows' => $allAccountRows,
            'allAccountCount' => $allAccountCount,
            'monthAccountRows' => $monthAccountRows,
            'monthAccountCount' => $monthAccountCount,
            'weekAccountRows' => $weekAccountRows,
            'weekAccountCount' => $weekAccountCount,
            'yearAccountRows' => $yearAccountRows,
            'yearAccountCount' => $yearAccountCount,
            'todayAccountRows' => $todayAccountRows,
            'todayAccountCount' => $todayAccountCount,
        ); 
        $sql="SELECT * FROM mk_account_budget WHERE userid=$userId;";
        $userBudget=$helloMarker->query($sql);
        $userAccountArray['userAccountBudget']=$userBudget[0]['budgetmoney'];
        return $userAccountArray;
   }
   public function budgetChange(){
      $budgetValue=$_POST['budgetValue'];
      $helloMarker=new \Think\Model();
      $userId=session('hellomarkeruserid');
      $sql="SELECT * FROM mk_account_budget WHERE userid=$userId;";
      $budgetFlag=$helloMarker->query($sql);
      if(count($budgetFlag)==0){
        $sql="INSERT INTO mk_account_budget (budgetmoney,userid) VALUES ($budgetValue,$userId);";
      }else if(count($budgetFlag)==1){
         $sql="UPDATE mk_account_budget SET budgetmoney = $budgetValue WHERE userid=$userId;";
      }
      if($helloMarker->execute($sql)){
        $this->assign('accountErrorFlag',2);
        $this->assign('accountErrorInfo','设置预算成功！');
        $this->loginAccount();
        exit();
      }else{
        $this->assign('accountErrorFlag',1);
        $this->assign('accountErrorInfo','设置预算失败！');
        $this->loginAccount();
        exit();
      }
   }
   public function deleteAccount(){
        $accountId=$_REQUEST['id'];
        $helloMarker=new \Think\Model();
        $userId=session('hellomarkeruserid');
        $sql="SELECT * FROM mk_account WHERE userid=$userId AND accountid=$accountId;";
        $accountFlag=$helloMarker->query($sql);
        if(count($accountFlag)==0){
            $this->assign('accountErrorFlag',1);
            $this->assign('accountErrorInfo','删除失败！');
            $this->loginAccount();
            exit();
        }else if(count($accountFlag)==1){
            $sql="DELETE FROM mk_account WHERE accountid=$accountId AND userid=$userId;";
        }
        if($helloMarker->execute($sql)){
            $this->assign('accountErrorFlag',2);
            $this->assign('accountErrorInfo','删除成功！');
            $this->loginAccount();
            exit();
        }else{
            $this->assign('accountErrorFlag',1);
            $this->assign('accountErrorInfo','删除失败！');
            $this->loginAccount();
            exit(); 
        }
   }
   public function indexShowPie(){
        $showPieRequest=$_POST['showPieRequest'];
        $helloMarker=new \Think\Model();
        $showPieRequest=1;
        $userId=session('hellomarkeruserid');
        $stampPlus=86400;
        $monthBeginDate=date('Y-m-01', strtotime(date("Y-m-d")));
        $tFymdStamp=strtotime( $monthBeginDate);//当月第一天
        $tLymdStamp=$tFymdStamp+((date("t",$tFymdStamp)-1)*$stampPlus);//当月最后一天
        $weekArray=array(0 => 7,1 => 1,2 => 2, 3 => 3,4 => 4,5 => 5,6 =>6);
        $tW=$weekArray[date('w')];
        $todayStamp=strtotime(date('Y-m-d'));//今天
        $fWeekStamp=$todayStamp-(($tW-1)*$stampPlus);//本周第一天
        $lFWeekStamp=$fWeekStamp-7*$stampPlus;//上周第一天
        $lLWeekStamp=$lFWeekStamp+6*$stampPlus;//上周最后一天
        $monthBeginDate=date('Y-m-01', strtotime('-1 month'));
        $lFymdStamp=strtotime( $monthBeginDate);//上月第一天
        $lLymdStamp=$tFymdStamp-$stampPlus;//上月最后一天
        if($showPieRequest==1){
            $sql="SELECT sum(mk_account.accountmoney) as monthout , mk_account_type.typename FROM mk_account,mk_account_type WHERE mk_account.userid=$userId AND mk_account_type.typeid=mk_account.typeid  AND mk_account.accounttime >= $tFymdStamp AND mk_account.accounttime <= $tLymdStamp GROUP BY mk_account.typeid ORDER BY mk_account.typeid ASC;";
        }else{
            $this->assign('accountErrorFlag',3);
            $this->assign('accountErrorInfo','部分图像显示失败!');
            $this->loginAccount();
            exit();
        }
        $monthRows=$helloMarker->query($sql);
        $monthCount=count($monthRows);

        $jsonCreateArray=array();
        $colorArray=array("#A6CEE3", "#1F78B4", "#B2DF8A","#33A02C","#FB9A99","#E31A1C", "#FDBF6F", "#FF7F00", "#CAB2D6", "#6A3D9A", "#B4B482", "#B15928","#0000FF", "#00CCFF","#666600","#9966FF","#FF0066","#FFFF66");
        $hightLightArray=array("#CEF6FF", "#47A0DC", "#DAFFB2", "#5BC854", "#FFC2C1", "#FF4244", "#FFE797", "#FFA728", "#F2DAFE", "#9265C2", "#DCDCAA", "#D98150","#A6CEE3", "#1F78B4", "#B2DF8A","#33A02C","#FB9A99","#B15928");
        $colorArrayCount=count($colorArray);
        $colorArrayItemFlag=0;
        for($i=0;$i<$monthCount;$i++){
            $jsonCreateArray[$i]['value']=$monthRows[$i]['monthout'];
            if($colorArrayItemFlag > $colorArrayCount){
                $colorArrayItemFlag=0;
            }
            $jsonCreateArray[$i]['color']=$colorArray[$colorArrayItemFlag];
            $jsonCreateArray[$i]['highlight']=$hightLightArray[$colorArrayItemFlag];
            $colorArrayItemFlag++;
            $jsonCreateArray[$i]['label']=$monthRows[$i]['typename'];
        }

        $sql="SELECT accountmoney,accounttime FROM mk_account WHERE userid=$userId;";
        $allPieRows=$helloMarker->query($sql);
        $tWeekSum=0;
        $lWeekSum=0;
        $tMonthSum=0;
        $lMonthSum=0;
        $tWeekDaySum=array(0,0,0,0,0,0,0);
        $allPieCount=count($allPieRows);

        for($i=0;$i<$allPieCount;$i++){
            if($allPieRows[$i]['accounttime'] >= $fWeekStamp && $allPieRows[$i]['accounttime'] <= $todayStamp){
                $weekDay=($allPieRows[$i]['accounttime']-$fWeekStamp)/$stampPlus;
                $tWeekDaySum[$weekDay]=$tWeekDaySum[$weekDay]+$allPieRows[$i]['accountmoney'];
              }
        }
        $jsonLineArray=array();
        $jsonLineArray['labels']=array('一','二','三','四','五','六','日');
        $jsonLineArray['datasets'][0]=array(
            'fillColor' => "#FFCCFF",
            'strokeColor' => "#FF3366",
            'pointColor' => "#330033",
            'pointStrokeColor' => "#330033",
            'data' => $tWeekDaySum
            );
        for($i=0;$i<$allPieCount;$i++){
            if($allPieRows[$i]['accounttime'] >= $tFymdStamp && $allPieRows[$i]['accounttime'] <= $tLymdStamp){
                $tMonthDaySum[$allPieRows[$i]['accounttime']]=$tMonthDaySum[$allPieRows[$i]['accounttime']]+$allPieRows[$i]['accountmoney'];
              }
        }
        arsort($tMonthDaySum);
        $monthMostMoney=array(
            'money' => $tMonthDaySum[key($tMonthDaySum)],
            'time' => date('Y-m-d',key($tMonthDaySum)),
            'week' => $jsonLineArray['labels'][date('w',key($tMonthDaySum))-1],
            );
        for($i=0;$i<$allPieCount;$i++){
            if($allPieRows[$i]['accounttime'] >= $lFWeekStamp && $allPieRows[$i]['accounttime'] <= $lLWeekStamp){
                $lWeekSum=$lWeekSum+$allPieRows[$i]['accountmoney'];
              }
            if($allPieRows[$i]['accounttime'] >= $fWeekStamp && $allPieRows[$i]['accounttime'] <= $todayStamp){
                $tWeekSum=$tWeekSum+$allPieRows[$i]['accountmoney'];
              }
            if($allPieRows[$i]['accounttime'] >= $lFymdStamp && $allPieRows[$i]['accounttime'] <= $lLymdStamp){
                $lMonthSum=$lMonthSum+$allPieRows[$i]['accountmoney'];
              }
            if($allPieRows[$i]['accounttime'] >= $tFymdStamp && $allPieRows[$i]['accounttime'] <= $tLymdStamp){
                $tMonthSum=$tMonthSum+$allPieRows[$i]['accountmoney'];
              }
        }
        $tWeekDayAvg=round($tWeekSum/7);
        $lWeekDayAvg=round($lWeekSum/7);
        $tMontDayhAvg=round($tMonthSum/((strtotime(date('Y-m-d'))-$tFymdStamp)/$stampPlus));
        $lMonthDayAvg=round($lMonthSum/(($lLymdStamp-$lFymdStamp)/$stampPlus));
        $jsonBarArray=array();
        $jsonBarArray['labels']=array('上周','本周','上月','本月');
        $avgMoneyArray=array($lWeekDayAvg,$tWeekDayAvg,$lMonthDayAvg,$tMontDayhAvg);
        $jsonBarArray['datasets'][0]=array(
            'fillColor' => "#A6CEE3",
            'strokeColor' => "#A6CEE3",
            'data' => $avgMoneyArray
            );
        $jsonDataArray=array(
            'barArray' => $jsonBarArray,
            'pieArray' => $jsonCreateArray,
            'lineArray' => $jsonLineArray,
            'monthMostMoney' => $monthMostMoney
            );
         $this->ajaxReturn($jsonDataArray,json);
   }
   public function allReport(){
        $todayMonth=date('Y-m');
        $todayYear=date('Y');
        $todayQuater=date('m');
        $today=date('Y-m-d');
        $this->assign('todayMonth',$todayMonth);
        $this->assign('todayYear',$todayYear);
        $this->assign('today',$today);
        $this->assign('todayQuater',$todayQuater);
        $this->display('allReport');
   }
   public function showAllReport(){
        if($this->checkAcl()){
        }else{
            $this->redirect('User/index');
            exit();
        }
        $userAccountArray=$this->userAccountAll();
        $userAccountArrayCount=count($userAccountArray);
        $stampPlus=86400;
        $showType=$_POST['showType'];
        $showValue=$_POST['showValue'];
        // $showType="pickLength";
        // $showValue="2016-03-04";
        if($showType=="todayMonth"){
            $showValueBegin=$showValue.'-01';
            $showValueBeginStamp=strtotime($showValue);
            $showValueEndStamp=$showValueBeginStamp+((date("t",$showValueBeginStamp)-1)*$stampPlus);
            $monthPieArray=array();
            $monthHeading=array();
            $monthHeading['dateHeading']=$showValue;
            $monthHeading['accountcount']=0;
            $monthHeading['moneycount']=0;
            $yearDayArray=array();
            $tempCount=0;
            for($i=0;$i<count($userAccountArray['allAccountRows']);$i++){
                if($userAccountArray['allAccountRows'][$i]['accounttime']>=$showValueBeginStamp && $userAccountArray['allAccountRows'][$i]['accounttime']<=$showValueEndStamp){
                     $monthHeading['accountcount']++;
                     $monthPieArray[$userAccountArray['allAccountRows'][$i]['typename']]=$monthPieArray[$userAccountArray['allAccountRows'][$i]['typename']]+$userAccountArray['allAccountRows'][$i]['accountmoney'];
                    $monthHeading['moneycount']=$monthHeading['moneycount']+$userAccountArray['allAccountRows'][$i]['accountmoney'];
                    $yearDayArray[$tempCount]=$userAccountArray['allAccountRows'][$i];
                    $tempCount++;
                }

            }

            if(count($monthPieArray)==0){
                $dataFlag=0;
                $this->ajaxReturn($dataFlag);
                return;
                exit();
            }
            $jsonCreateArray=array();
            $colorArray=array("#A6CEE3", "#1F78B4", "#B2DF8A","#33A02C","#FB9A99","#E31A1C", "#FDBF6F", "#FF7F00", "#CAB2D6", "#6A3D9A", "#B4B482", "#B15928","#0000FF", "#00CCFF","#666600","#9966FF","#FF0066","#FFFF66");
            $hightLightArray=array("#CEF6FF", "#47A0DC", "#DAFFB2", "#5BC854", "#FFC2C1", "#FF4244", "#FFE797", "#FFA728", "#F2DAFE", "#9265C2", "#DCDCAA", "#D98150","#A6CEE3", "#1F78B4", "#B2DF8A","#33A02C","#FB9A99","#B15928");
            $colorArrayCount=count($colorArray);
            $colorArrayItemFlag=0;
            $i=0;
            foreach($monthPieArray as $key=>$value){
                $jsonCreateArray[$i]['value']=$value;
                if($colorArrayItemFlag > $colorArrayCount){
                    $colorArrayItemFlag=0;
                }
                $jsonCreateArray[$i]['color']=$colorArray[$colorArrayItemFlag];
                $jsonCreateArray[$i]['highlight']=$hightLightArray[$colorArrayItemFlag];
                $colorArrayItemFlag++;
                $jsonCreateArray[$i]['label']=$key;
                $i++;
            }
            $monthDaySum=array();
            for($i=$showValueBeginStamp;$i<=$showValueEndStamp;$i=$i+$stampPlus){
                $monthDaySum[$i]["accounttime"]==$i;
                $monthDaySum[$i]["accountdate"]=date('Y-m-d',$i);
                $monthDaySum[$i]["accountmoney"]=0;
                 for($j=0;$j<count($yearDayArray);$j++){
                    if($yearDayArray[$j]['accounttime'] == $i){
                        $monthDaySum[$i]["accountmoney"]=$monthDaySum[$i]["accountmoney"]+$yearDayArray[$j]['accountmoney'];
                    }
                 }
            }
            // echo "<pre>";
            //     print_r($monthDaySum);
            // echo "</pre>";
            // exit();
            $lineMonthDayTmp=array();
            $lineMonthDayTmpLabel=array();
            $i=0;
            foreach($monthDaySum as $key=>$value){
                $lineMonthDayTmp[$i]=$value['accountmoney'];
                $lineMonthDayTmpLabel[$i]=$value['accountdate'];
                $i++;
            }
            $jsonLineArray=array();
            $jsonLineArray['labels']=$lineMonthDayTmpLabel;
            $jsonLineArray['datasets'][0]=array(
                'fillColor' => "#FFCCFF",
                'strokeColor' => "#FF3366",
                'pointColor' => "#330033",
                'pointStrokeColor' => "#330033",
                'data' => $lineMonthDayTmp
                );
            $data=array(
                'monthRows' => $yearDayArray,
                'monthPie' => $jsonCreateArray,
                'monthLine' => $jsonLineArray,
                'monthHeading'=>$monthHeading
            );
            $this->ajaxReturn($data,json);

        }else if($showType=="todayYear"){//if
            $showValueBegin=$showValue.'-1-01';
            $showValueBeginStamp=strtotime($showValueBegin);
            $showValueEndStamp=strtotime($showValue.'-12-31');
            $monthPieArray=array();
            $monthHeading=array();
            $yearDayArray=array();
            $monthHeading['dateHeading']=$showValue;
            $monthHeading['accountcount']==0;;
            $monthHeading['moneycount']=0;
            $tempCount=0;
            for($i=0;$i<count($userAccountArray['allAccountRows']);$i++){
                if($userAccountArray['allAccountRows'][$i]['accounttime']>=$showValueBeginStamp && $userAccountArray['allAccountRows'][$i]['accounttime']<=$showValueEndStamp){
                     $monthHeading['accountcount']++;
                     $monthPieArray[$userAccountArray['allAccountRows'][$i]['typename']]=$monthPieArray[$userAccountArray['allAccountRows'][$i]['typename']]+$userAccountArray['allAccountRows'][$i]['accountmoney'];
                    $monthHeading['moneycount']=$monthHeading['moneycount']+$userAccountArray['allAccountRows'][$i]['accountmoney'];
                    $yearDayArray[$tempCount]=$userAccountArray['allAccountRows'][$i];
                    $tempCount++;
                }

            }

            if(count($monthPieArray)==0){
                $dataFlag=0;
                $this->ajaxReturn($dataFlag);
                return;
                exit();
            }
            $jsonCreateArray=array();
            $colorArray=array("#A6CEE3", "#1F78B4", "#B2DF8A","#33A02C","#FB9A99","#E31A1C", "#FDBF6F", "#FF7F00", "#CAB2D6", "#6A3D9A", "#B4B482", "#B15928","#0000FF", "#00CCFF","#666600","#9966FF","#FF0066","#FFFF66");
            $hightLightArray=array("#CEF6FF", "#47A0DC", "#DAFFB2", "#5BC854", "#FFC2C1", "#FF4244", "#FFE797", "#FFA728", "#F2DAFE", "#9265C2", "#DCDCAA", "#D98150","#A6CEE3", "#1F78B4", "#B2DF8A","#33A02C","#FB9A99","#B15928");
            $colorArrayCount=count($colorArray);
            $colorArrayItemFlag=0;
            $i=0;
            foreach($monthPieArray as $key=>$value){
                $jsonCreateArray[$i]['value']=$value;
                if($colorArrayItemFlag > $colorArrayCount){
                    $colorArrayItemFlag=0;
                }
                $jsonCreateArray[$i]['color']=$colorArray[$colorArrayItemFlag];
                $jsonCreateArray[$i]['highlight']=$hightLightArray[$colorArrayItemFlag];
                $colorArrayItemFlag++;
                $jsonCreateArray[$i]['label']=$key;
                $i++;
            }
            $monthMonthSum=array();
            $temp=0;
            $showValueEndStampF=strtotime($showValue.'-12-01');
            for($i=$showValueBeginStamp;$i<=$showValueEndStampF;$i=strtotime(($showValue."-".(date('m',$i)+1)."-01"))){
                $temp++;
                if($temp==13){
                    break;
                }
                $monthMonthSum[$i]["accountdate"]=date('Y-m',$i);
                $monthMonthSum[$i]["accountmoney"]=0;
                for($j=0;$j<count($yearDayArray);$j++){
                    if($yearDayArray[$j]['accounttime'] >= $i && $yearDayArray[$j]['accounttime'] <= $i+(date('t',$i)-1)*$stampPlus){
                        $monthMonthSum[$i]["accountmoney"]=$monthMonthSum[$i]["accountmoney"]+$yearDayArray[$j]['accountmoney'];
                    }
                 }
            }
            $lineMonthDayTmp=array();
            $lineMonthDayTmpLabel=array();
            $i=0;
            foreach($monthMonthSum as $key=>$value){
                $lineMonthDayTmp[$i]=$value['accountmoney'];
                $lineMonthDayTmpLabel[$i]=$value['accountdate'];
                $i++;
            }
            $jsonLineArray=array();
            $jsonLineArray['labels']=$lineMonthDayTmpLabel;
            $jsonLineArray['datasets'][0]=array(
                'fillColor' => "#FFCCFF",
                'strokeColor' => "#FF3366",
                'pointColor' => "#330033",
                'pointStrokeColor' => "#330033",
                'data' => $lineMonthDayTmp
                );
            $data=array(
                'monthRows' => $yearDayArray,
                'monthPie' => $jsonCreateArray,
                'monthLine' => $jsonLineArray,
                'monthHeading'=>$monthHeading
            );
            $this->ajaxReturn($data,json);
        }else if($showType=="todayQuarter"){//if
            $quarterArray['01']['showValueBegin']=date('Y-')."01-01";
            $quarterArray['01']['showValueEnd']=date('Y-')."03-31";
            $quarterArray['02']['showValueBegin']=date('Y-')."01-01";
            $quarterArray['02']['showValueEnd']=date('Y-')."03-31";
            $quarterArray['03']['showValueBegin']=date('Y-')."01-01";
            $quarterArray['03']['showValueEnd']=date('Y-')."03-31";
            $quarterArray['04']['showValueBegin']=date('Y-')."04-01";
            $quarterArray['04']['showValueEnd']=date('Y-')."06-31";
            $quarterArray['05']['showValueBegin']=date('Y-')."04-01";
            $quarterArray['05']['showValueEnd']=date('Y-')."06-31";
            $quarterArray['06']['showValueBegin']=date('Y-')."04-01";
            $quarterArray['06']['showValueEnd']=date('Y-')."06-31";
            $quarterArray['07']['showValueBegin']=date('Y-')."07-01";
            $quarterArray['07']['showValueEnd']=date('Y-')."09-31";
            $quarterArray['08']['showValueBegin']=date('Y-')."07-01";
            $quarterArray['08']['showValueEnd']=date('Y-')."09-31";
            $quarterArray['09']['showValueBegin']=date('Y-')."07-01";
            $quarterArray['09']['showValueEnd']=date('Y-')."09-31";
            $quarterArray['10']['showValueBegin']=date('Y-')."10-01";
            $quarterArray['10']['showValueEnd']=date('Y-')."12-31";
            $quarterArray['11']['showValueBegin']=date('Y-')."10-01";
            $quarterArray['11']['showValueEnd']=date('Y-')."12-31";
            $quarterArray['12']['showValueBegin']=date('Y-')."10-01";
            $quarterArray['12']['showValueEnd']=date('Y-')."12-31";
            $showValueBeginStamp=strtotime($quarterArray[$showValue]['showValueBegin']);
            $showValueEndStamp=strtotime($quarterArray[$showValue]['showValueEnd']);
            $monthPieArray=array();
            $monthHeading=array();
            $yearDayArray=array();
            $monthHeading['dateHeading']=$showValue;
            $monthHeading['accountcount']==0;;
            $monthHeading['moneycount']=0;
            $tempCount=0;
            for($i=0;$i<count($userAccountArray['allAccountRows']);$i++){
                if($userAccountArray['allAccountRows'][$i]['accounttime']>=$showValueBeginStamp && $userAccountArray['allAccountRows'][$i]['accounttime']<=$showValueEndStamp){
                     $monthHeading['accountcount']++;
                     $monthPieArray[$userAccountArray['allAccountRows'][$i]['typename']]=$monthPieArray[$userAccountArray['allAccountRows'][$i]['typename']]+$userAccountArray['allAccountRows'][$i]['accountmoney'];
                    $monthHeading['moneycount']=$monthHeading['moneycount']+$userAccountArray['allAccountRows'][$i]['accountmoney'];
                    $yearDayArray[$tempCount]=$userAccountArray['allAccountRows'][$i];
                    $tempCount++;
                }
            }
            if(count($monthPieArray)==0){
                $dataFlag=0;
                $this->ajaxReturn($dataFlag);
                return;
                exit();
            }

            $jsonCreateArray=array();
            $colorArray=array("#A6CEE3", "#1F78B4", "#B2DF8A","#33A02C","#FB9A99","#E31A1C", "#FDBF6F", "#FF7F00", "#CAB2D6", "#6A3D9A", "#B4B482", "#B15928","#0000FF", "#00CCFF","#666600","#9966FF","#FF0066","#FFFF66");
            $hightLightArray=array("#CEF6FF", "#47A0DC", "#DAFFB2", "#5BC854", "#FFC2C1", "#FF4244", "#FFE797", "#FFA728", "#F2DAFE", "#9265C2", "#DCDCAA", "#D98150","#A6CEE3", "#1F78B4", "#B2DF8A","#33A02C","#FB9A99","#B15928");
            $colorArrayCount=count($colorArray);
            $colorArrayItemFlag=0;
            $i=0;
            foreach($monthPieArray as $key=>$value){
                $jsonCreateArray[$i]['value']=$value;
                if($colorArrayItemFlag > $colorArrayCount){
                    $colorArrayItemFlag=0;
                }
                $jsonCreateArray[$i]['color']=$colorArray[$colorArrayItemFlag];
                $jsonCreateArray[$i]['highlight']=$hightLightArray[$colorArrayItemFlag];
                $colorArrayItemFlag++;
                $jsonCreateArray[$i]['label']=$key;
                $i++;
            }
            $monthMonthSum=array();
            $temp=0;
            for($i=$showValueBeginStamp;$i<=$showValueEndStamp;$i=strtotime((date('Y')."-".(date('m',$i)+1)."-01"))){
                if($temp==3){
                    break;
                }
                $temp++;
                $monthMonthSum[$i]["accountdate"]=date('Y-m',$i);
                $monthMonthSum[$i]["accountmoney"]=0;
                for($j=0;$j<count($yearDayArray);$j++){
                    if($yearDayArray[$j]['accounttime'] >= $i && $yearDayArray[$j]['accounttime'] <= $i+(date('t',$i)-1)*$stampPlus){
                        $monthMonthSum[$i]["accountmoney"]=$monthMonthSum[$i]["accountmoney"]+$yearDayArray[$j]['accountmoney'];
                    }
                 }
            }
            // echo "<pre>";
            //     print_r($yearDayArray);
            // echo "</pre>";
            // exit();
            $lineMonthDayTmp=array();
            $lineMonthDayTmpLabel=array();
            $i=0;
            foreach($monthMonthSum as $key=>$value){
                $lineMonthDayTmp[$i]=$value['accountmoney'];
                $lineMonthDayTmpLabel[$i]=$value['accountdate'];
                $i++;
            }
            $jsonLineArray=array();
            $jsonLineArray['labels']=$lineMonthDayTmpLabel;
            $jsonLineArray['datasets'][0]=array(
                'fillColor' => "#FFCCFF",
                'strokeColor' => "#FF3366",
                'pointColor' => "#330033",
                'pointStrokeColor' => "#330033",
                'data' => $lineMonthDayTmp
                );

            $data=array(
                'monthRows' => $yearDayArray,
                'monthPie' => $jsonCreateArray,
                'monthLine' => $jsonLineArray,
                'monthHeading'=>$monthHeading
            );
            $this->ajaxReturn($data,json);
        }else if($showType=="pickLength"){//if
            $showValue2=$_POST['showValue2'];
            $showValueBeginStamp=strtotime($showValue);
            $showValueEndStamp=strtotime($showValue2);
            $monthPieArray=array();
            $monthHeading=array();
            $yearDayArray=array();
            $monthHeading['dateHeading']=$showValue."至".$showValue2;
            $monthHeading['accountcount']==0;;
            $monthHeading['moneycount']=0;
            $tempCount=0;
            for($i=0;$i<count($userAccountArray['allAccountRows']);$i++){
                if($userAccountArray['allAccountRows'][$i]['accounttime']>=$showValueBeginStamp && $userAccountArray['allAccountRows'][$i]['accounttime']<=$showValueEndStamp){
                     $monthHeading['accountcount']++;
                     $monthPieArray[$userAccountArray['allAccountRows'][$i]['typename']]=$monthPieArray[$userAccountArray['allAccountRows'][$i]['typename']]+$userAccountArray['allAccountRows'][$i]['accountmoney'];
                    $monthHeading['moneycount']=$monthHeading['moneycount']+$userAccountArray['allAccountRows'][$i]['accountmoney'];
                    $yearDayArray[$tempCount]=$userAccountArray['allAccountRows'][$i];
                    $tempCount++;
                }
            }
            if(count($monthPieArray)==0){
                $dataFlag=0;
                $this->ajaxReturn($dataFlag);
                return;
                exit();
            }
            // echo "<pre>";
            //     print_r($monthPieArray);
            // echo "</pre>";
            // exit();
            $jsonCreateArray=array();
            $colorArray=array("#A6CEE3", "#1F78B4", "#B2DF8A","#33A02C","#FB9A99","#E31A1C", "#FDBF6F", "#FF7F00", "#CAB2D6", "#6A3D9A", "#B4B482", "#B15928","#0000FF", "#00CCFF","#666600","#9966FF","#FF0066","#FFFF66");
            $hightLightArray=array("#CEF6FF", "#47A0DC", "#DAFFB2", "#5BC854", "#FFC2C1", "#FF4244", "#FFE797", "#FFA728", "#F2DAFE", "#9265C2", "#DCDCAA", "#D98150","#A6CEE3", "#1F78B4", "#B2DF8A","#33A02C","#FB9A99","#B15928");
            $colorArrayCount=count($colorArray);
            $colorArrayItemFlag=0;
            $i=0;
            foreach($monthPieArray as $key=>$value){
                $jsonCreateArray[$i]['value']=$value;
                if($colorArrayItemFlag > $colorArrayCount){
                    $colorArrayItemFlag=0;
                }
                $jsonCreateArray[$i]['color']=$colorArray[$colorArrayItemFlag];
                $jsonCreateArray[$i]['highlight']=$hightLightArray[$colorArrayItemFlag];
                $colorArrayItemFlag++;
                $jsonCreateArray[$i]['label']=$key;
                $i++;
            }
            $monthMonthSum=array();
            $temp=0;
            for($i=$showValueBeginStamp;$i<=$showValueEndStamp;$i=strtotime((date('Y')."-".(date('m',$i)+1)."-01"))){
                if($temp==3){
                    break;
                }
                $temp++;
                $monthMonthSum[$i]["accountdate"]=date('Y-m',$i);
                $monthMonthSum[$i]["accountmoney"]=0;
                for($j=0;$j<count($yearDayArray);$j++){
                    if($yearDayArray[$j]['accounttime'] >= $i && $yearDayArray[$j]['accounttime'] <= $i+(date('t',$i)-1)*$stampPlus){
                        $monthMonthSum[$i]["accountmoney"]=$monthMonthSum[$i]["accountmoney"]+$yearDayArray[$j]['accountmoney'];
                    }
                 }
            }

            $lineMonthDayTmp=array();
            $lineMonthDayTmpLabel=array();
            $i=0;
            foreach($monthMonthSum as $key=>$value){
                $lineMonthDayTmp[$i]=$value['accountmoney'];
                $lineMonthDayTmpLabel[$i]=$value['accountdate'];
                $i++;
            }
            $jsonLineArray=array();
            $jsonLineArray['labels']=$lineMonthDayTmpLabel;
            $jsonLineArray['datasets'][0]=array(
                'fillColor' => "#FFCCFF",
                'strokeColor' => "#FF3366",
                'pointColor' => "#330033",
                'pointStrokeColor' => "#330033",
                'data' => $lineMonthDayTmp
                );

            $data=array(
                'monthRows' => $yearDayArray,
                'monthPie' => $jsonCreateArray,
                'monthLine' => $jsonLineArray,
                'monthHeading'=>$monthHeading
            );
            $this->ajaxReturn($data,json);
        }//else end//else end//else end//else end
   }
   public function showTypeOption(){
    $showTypeOptionRequest=$_POST['showTypeOptionRequest'];
    if($showTypeOptionRequest==1){
        $helloMarker=new \Think\Model();
        $sql="SELECT * from mk_account_type";
        $typeRows=$helloMarker->query($sql);
        $this->ajaxReturn($typeRows,json);
    }
   }
   public function addAccount(){
        $helloMarker=new \Think\Model();
        $userId=session('hellomarkeruserid');
        $accountName=$_POST['accountname'];
        $accountMoney=$_POST['accountmoney'];
        $accountDate=$_POST['accounttime'];
        $accountTime=strtotime($accountDate);
        $accountTypeId=$_POST['accounttype'];
        $accountOther=$_POST['accountother'];
        $noteTime=date('Y-m-d');
        if(strlen($accountOther)==0){
            $accountOther="暂无相关备注!";
        }
        if((strlen($accountName)==0) || (ctype_space($accountName))){
                $accountErrorFlag=1;
                $accountErrorInfo="名称有误！";
                $this->assign('accountErrorFlag',$accountErrorFlag);
                $this->assign('accountErrorInfo',$accountErrorInfo);
                $this->index();
                exit();
            }
            $dateFormat=$this->checkDateIsValid($accountDate);
        if((strlen($accountDate)==0) || (ctype_space($accountDate)) || (!$dateFormat)){
                $accountErrorFlag=1;
                $accountErrorInfo="日期有误！";
                $this->assign('accountErrorFlag',$accountErrorFlag);
                $this->assign('accountErrorInfo',$accountErrorInfo);
                $this->index();
                exit();
        }
        $sql="INSERT INTO mk_account (accountname,typeid,accountmoney,accounttime,userid,notetime,accountdate,accountother) VALUES ('".$accountName."',".$accountTypeId.",".$accountMoney.",'".$accountTime."',".$userId.",'".$noteTime."','".$accountDate."','".$accountOther."');";
         if($helloMarker->execute($sql)){
                $accountErrorFlag=2;
                $accountErrorInfo="添加成功！";
                $this->assign('accountErrorFlag',$accountErrorFlag);
                $this->assign('accountErrorInfo',$accountErrorInfo);
                 $this->redirect('index');
                exit();
        }else{
                $accountErrorFlag=1;
                $accountErrorInfo="添加失败！";
                $this->assign('accountErrorFlag',$accountErrorFlag);
                $this->assign('accountErrorInfo',$accountErrorInfo);
                $this->index();
                exit();
        }
   }
   public function noLoginAccount(){
        session(null);
        cookie(null);
        $this->display('noLoginAccount');
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
}