<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends AclController {
    public function index(){
        session('[destroy]'); 
        setcookie("hellomarkerusername", $userInfo[0]['username'], time()-3600,'/');
        setcookie("hellomarkerusernickname", $userInfo[0]['usernickname'], time()-3600,'/');
        setcookie("hellomarkeruserid", $userInfo[0]['userid'], time()-3600,'/');
        setcookie("hellomarkercookieflag", 1, time()-3600,'/');
        $this->display('index');
    }
    public function userHome(){
        $userLoginFlag=0;
        if(session('hellomarkeruserlogin')==1 && strlen(session('hellomarkerusername'))>1){
            $userLoginFlag=1;
        }
        $this->myInfo();
        $this->assign('userLoginFlag',$userLoginFlag);
        $this->display('userHome');
    }
    public function userLogin(){
        $userremember=0;
        $username=$_POST['username'];
        $userpassword=$_POST['userpassword'];
        $userremember=$_POST['userremember'];
        $loginverify=$_POST['loginverify'];
         $verify = new \Think\Verify();
        if(!($verify->check($loginverify))){
            $this->loginError('验证码错误');
        }
        if((strlen($username)<1) || (strlen($userpassword)<1)){
            $this->loginError('用户名或密码错误');
        }
        $hellomarker=new \Think\Model();
        $sql="SELECT username,userid,usernickname,userpassword FROM mk_user WHERE username = '".$username."' AND useractive=1;";
        $userInfo=array();
        $userInfo=$hellomarker->query($sql);
        if(count($userInfo)<1){
            $this->loginError('用户名或密码错误');
        }
        if($userInfo[0]['username']==$username){
            if($userInfo[0]['userpassword']==md5($userpassword)){
                session('hellomarkerusername',$userInfo[0]['username']);
                session('hellomarkerusernickname',$userInfo[0]['usernickname']);
                session('hellomarkeruserid',$userInfo[0]['userid']);
                session('hellomarkeruserlogin',1);
                if($userremember==1){
                setcookie("hellomarkerusername", $userInfo[0]['username'], time()+3600*24*30,'/');
                    setcookie("hellomarkerusernickname", $userInfo[0]['usernickname'], time()+3600*24*30,'/');
                    setcookie("hellomarkeruserid", $userInfo[0]['userid'], time()+3600*24*30,'/');
                    setcookie("hellomarkercookieflag", 1, time()+3600*24*30,'/');
                }
                $this->redirect('Index/index');
            }else{
                $this->loginError('用户名或密码错误');
            }
        }else{
            $this->loginError('用户名或密码错误');
        }
    }
    public function userFindPassword(){
        $this->display('findPassword');
    }
    public function userSign(){
        $this->display('sign');
    } 
    public function userSignForm(){
        $username=$_POST['username'];
        $userpassword=$_POST['userpassword'];
        $userpassword2=$_POST['userpassword2'];
        $useremail=$_POST['useremail'];
        $signverify=$_POST['signverify'];

        $infoValue=array(
            'username'=>$username,
            'useremail'=>$useremail,
        );
         $verify = new \Think\Verify();
        if(!($verify->check($signverify))){
            $this->signError('验证码错误',$infoValue);
            exit();
        }
        //username
        if((strlen($username)>=3)&&(strlen($username)<=15)){
            $usernameArray = str_split($username,1);
            for($i=0;$i<strlen($username);$i++){
                if((($usernameArray[$i]>='a')&&($usernameArray[$i]<='z')) ||(($usernameArray[$i]>='A') &&($usernameArray[$i]<='Z'))||(($usernameArray[$i]>='0') &&($usernameArray[$i]<='9'))){
                    continue;
                }else{
                    $this->signError("用户名格式错误",$infoValue);
                }
            }
        }else{
            $this->signError("用户名格式错误",$infoValue);
        }
        //usernameDB
        $hellomarker=new \Think\Model();
        $sql="SELECT username FROM mk_user WHERE username = '".$username."';";
        $usernameCheck=$hellomarker->query($sql);
        if(count($usernameCheck)!=0){
            $this->signError("用户名已存在",$infoValue);
        }   
        //useremialDB
        $sql="SELECT useremail FROM mk_user WHERE useremail = '".$useremail."';";
        $useremailCheck=$hellomarker->query($sql);
        if(count($useremailCheck)!=0){
            $this->signError("邮箱已被使用",$infoValue);
        }   
        //useremail
        $pattern="/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
        if(!preg_match($pattern,$useremail)){
            $this->signError("邮箱格式错误",$infoValue);
        } 
        //userpassword
        if((strlen($userpassword)>=8)&&(strlen($userpassword)<=16)){
            $userpasswordArray = str_split($userpassword,1);
            for($i=0;$i<strlen($userpassword);$i++){
                if($userpasswordArray[$i]==' '){
                    $this->signError("密码格式错误",$infoValue);
                }
            }
            if($userpassword != $userpassword2){
                 $this->signError("两次密码不一致",$infoValue);
            }
        }else{
             $this->signError("密码格式错误",$infoValue);
        }
        //usernameNote
        $regtime=date("Y-m-d");
        $sql="INSERT  INTO mk_user (username,usernickname,userpassword,useremail,userregtime) VALUES ('".$username."','".$username."','".md5($userpassword)."','".$useremail."','".$regtime."')";
        $key=md5('hello');
        $value=md5('marker');
        $activeUrlArray=array(
            $key."3"=>$value.$value,
            $key."1"=>md5($username).$value,
            $key."2"=>md5($useremail).$value,
            $key."4"=>$value.$value,

        );
        $activeUrl=U('User/userActive',$activeUrlArray);
        $activeUrl="http://".$_SERVER['SERVER_NAME'].$activeUrl;
        $activeText="亲爱的用户您好，<br/>
                    这封信是由 <strong>HelloMarker</strong> 发送的。<br/>
                    您收到这封邮件，是由于在 <strong>HelloMarker</strong> 进行了新用户注册。<br>
                    如果您并没有访问过 <strong>HelloMarker</strong> ，或没有进行操作，请忽略这封邮件。
                    您不需要退订或进行其他进一步的操作。<hr/>
                    <strong>用户激活说明:</strong><br/>
                    您只需点击下面的链接即可激活您的帐号：<br>
                    <a href='".$activeUrl."'>".$activeUrl."</a><br>
                    (如果上面不是链接形式，请将该地址手工粘贴到浏览器地址栏再访问)<hr>

                    感谢您的访问，祝您使用愉快！

                    <h3>HelloMarker</h3><br>
                    <h4>一本正经地吃喝玩乐！</h4><br>";
        if($hellomarker->execute($sql) && (sendMail($useremail,'一封测试邮件！',$activeText))){
            $this->signSuccess($useremail);
        }else{
          $this->signError("发生系统错误",$infoValue);
        }
    }

    public function userActive(){
        $key=md5('hello');
        $value=md5('marker');
        $activeUsername=str_replace($value, '',$_REQUEST[$key."1"]);
        $activeUserEmail=str_replace($value, '',$_REQUEST[$key."2"]);

        $helloMarker=new \Think\Model();
        $sql="SELECT username,useremail,userid,useractive,usernickname FROM mk_user WHERE md5(username) = '".$activeUsername."';";
        $userActiceInfo=$helloMarker->query($sql);
        if(count($userActiceInfo)==1){
            if($userActiceInfo[0]['useractive']==0){
                if(md5($userActiceInfo[0]['useremail'])==$activeUserEmail){
                    $sql="UPDATE mk_user SET useractive = 1";
                    if($helloMarker->execute($sql)){
                        session('hellomarkerusername',$userActiceInfo[0]['username']);
                        session('hellomarkerusernickname',$userActiceInfo[0]['usernickname']);
                        session('hellomarkeruserid',$userActiceInfo[0]['userid']);
                        session('hellomarkeruserlogin',1);
                        setcookie("hellomarkerusername", $userActiceInfo[0]['username'], time()+3600*24*30,'/');
                        setcookie("hellomarkerusernickname", $userActiceInfo[0]['usernickname'], time()+3600*24*30,'/');
                        setcookie("hellomarkeruserid", $userActiceInfo[0]['userid'], time()+3600*24*30,'/');
                        setcookie("hellomarkercookieflag", 1, time()+3600*24*30,'/');
                        $this->activeInfo('用户成功激活！',1);
                    }else{
                        $this->activeInfo('用户激活失败！',0);
                    }
                }else{
                    $this->activeInfo('用户信息验证失败！',0);
                }
            }else{
                $this->activeInfo('用户已激活，请直接登陆！',0);
            }
        }else{
            $this->activeInfo('用户不存在！',0);
        }
    }
    public function myInfo(){
        $helloMarker=new \Think\Model();
        if(strlen(session('hellomarkerusername'))>0){
            $userName=session('hellomarkerusername');
        }else{
            $userName="0";
        }
        $sql="SELECT * FROM mk_user WHERE username='".$userName."';";
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
    public function myChange(){
        $this->myInfo();
        $this->display('myChange');
    }
    public function myChangeWork(){
        $helloMarker=new \Think\Model();
        $userRow=$this->myInfo();
        $userSex=$_POST['usersex'];
        $userAddress=$_POST['useraddress'];
        $userQQ=$_POST['userqq'];
        $userWechat=$_POST['userwechat'];
        $userInterset=$_POST['userinterest'];
        if(strlen($userAddress)==0){
            $userAddress='暂无';
        }
        if(strlen($userQQ)==0){
            $userQQ='暂无';
        }
        if(strlen($userWechat)==0){
            $userWechat='暂无';
        }
        if(strlen($userInterset)==0){
            $userInterset='暂无';
        }
        $sql="UPDATE mk_user SET usersex='".$userSex."',useraddress='".$userAddress."',userqq='".$userQQ."',userwechat='".$userWechat."',userinterest='".$userInterset."' WHERE userid=".$userRow[0]['userid'].";";
        if($helloMarker->execute($sql)){
            $this->redirect('Index/myHome');
        }else{
            $myChangeErrorFlag=1;
            $myChangeError="资料更新失败";
            $this->assign('myChangeErrorFlag',$myChangeErrorFlag);
            $this->assign('myChangeError',$myChangeError);
            $this->display('myChange');
            exit();
        }
    }
    public function myChangePassword(){
        $userRow=$this->myInfo();
        $this->display('myPassword');
    }
    public function myChangePasswordWork(){
        $helloMarker=new \Think\Model();
        $userRow=$this->myInfo();
        $userOldPassword=md5($_POST['useroldpassword']);
        $userNewPassword=$_POST['usernewpassword'];
        $userNewPassword2=$_POST['usernewpassword2'];
        if($userOldPassword!=$userRow[0]['userpassword']){
            $myChangeErrorFlag=1;
            $this->assign('myChangeErrorFlag',$myChangeErrorFlag);
            $myChangeError="原始密码错误！";
            $this->assign('myChangeError',$myChangeError);
            $this->display('myPassword');
            exit();
        }
        if(((strlen($userNewPassword)>=8)&&(strlen($userNewPassword)<=16)) &&((strlen($userNewPassword2)>=8)&&(strlen($userNewPassword2)<=16))){
            $userpasswordArray = str_split($userNewPassword,1);
            for($i=0;$i<strlen($userNewPassword);$i++){
                if($userpasswordArray[$i]==' '){
                    $myChangeErrorFlag=1;
                    $this->assign('myChangeErrorFlag',$myChangeErrorFlag);
                    $myChangeError="密码格式错误！";
                    $this->assign('myChangeError',$myChangeError);
                    $this->display('myPassword');
                    exit();
                }
            }
            if($userNewPassword != $userNewPassword2){
                $myChangeErrorFlag=1;
                $this->assign('myChangeErrorFlag',$myChangeErrorFlag);
                $myChangeError="两次密码不一致！";
                $this->assign('myChangeError',$myChangeError);
                $this->display('myPassword');
                exit();
            }
        }else{
            $myChangeErrorFlag=1;
            $this->assign('myChangeErrorFlag',$myChangeErrorFlag);
            $myChangeError="密码格式错误!";
            $this->assign('myChangeError',$myChangeError);
            $this->display('myPassword');
            exit();
        }
        $sql="UPDATE mk_user SET userpassword='".md5($userNewPassword)."' WHERE userid=".$userRow[0]['userid'].";";
        if($helloMarker->execute($sql)){
           $this->redirect('userHome');
        }else{
            $myChangeErrorFlag=1;
            $this->assign('myChangeErrorFlag',$myChangeErrorFlag);
            $myChangeError="修改密码失败！";
            $this->assign('myChangeError',$myChangeError);
            $this->display('myPassword');
            exit();
        }
    }
    public function myChangeLogo(){
        $userRow=$this->myInfo();
        $this->display('myLogo');
    }
    public function myChangeLogoWork(){
        $helloMarker=new \Think\Model();
        $public=$_POST['public'];
        $upload = new \Think\Upload();
        $userRow=$this->myInfo();
        $upload->maxSize   =     2145728 ;
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp');
        $upload->rootPath  =     './Public/uploads/user/';
        $upload->saveName  =     $userRow[0]['username'];
        $upload->replace   =     true;
        $upload->autoSub  =      false;
        $info   =   $upload->upload();
        if(!$info) {
            $myChangeErrorFlag=1;
            $this->assign('myChangeErrorFlag',$myChangeErrorFlag);
            $myChangeError=$upload->getError();
            $this->assign('myChangeError',$myChangeError);
            $this->display('myLogo');
            exit();
        }
        $image = new \Think\Image(); 
        if($image->open('./Public/uploads/user/'.$info['userlogo']['savename'])){
            if($image->thumb(50, 50,\Think\Image::IMAGE_THUMB_FIXED)->save('./Public/uploads/user/'.$info['userlogo']['savename'])){
            }else{
                $myChangeErrorFlag=1;
                $this->assign('myChangeErrorFlag',$myChangeErrorFlag);
                $myChangeError="图片处理失败！";
                $this->assign('myChangeError',$myChangeError);
                $this->display('myLogo');
                exit();
            }
        }else{
                $myChangeErrorFlag=1;
                $this->assign('myChangeErrorFlag',$myChangeErrorFlag);
                $myChangeError="图片处理失败！";
                $this->assign('myChangeError',$myChangeError);
                $this->display('myLogo');
                exit();
        }
        $sql="UPDATE mk_user SET userlogo='".$info['userlogo']['savename']."' WHERE userid=".$userRow[0]['userid'].";";
        if($helloMarker->execute($sql)){
                $this->redirect('userHome');
                exit();
        }else{
                $this->redirect('userHome');
                $myChangeErrorFlag=1;
                $this->assign('myChangeErrorFlag',$myChangeErrorFlag);
                $myChangeError="头像更新成功！";
                $this->assign('myChangeError',$myChangeError);
                exit();
        }
    }
    public function userLogout(){
        session('[destroy]'); 
        setcookie("hellomarkerusername", "", time()-3600*24*30,'/');
        setcookie("hellomarkerusernickname",  "", time()-3600*24*30,'/');
        setcookie("hellomarkeruserid", "", time()-3600*24*30,'/');
        setcookie("hellomarkercookieflag", "", time()-3600*24*30,'/');
        $this->redirect('userHome');
    }

    public function verify(){
        $config =    array(
            'fontSize'    =>    15,    // 验证码字体大小
            'length'      =>    4,     // 验证码位数
            'useNoise'    =>    false, // 关闭验证码杂点
            'useCurve'    =>    false,
            'imageH'      =>    30,
        );
        $Verify =  new \Think\Verify($config);
        $Verify->entry();
    }
    public function loginError($info){
        $this->assign('loginErrorFlag',1);
        $this->assign('loginErrorInfo',$info);
        $this->display('index');
        exit();
    }

    public function signError($info,$infoValue){
        $this->assign('signErrorFlag',1);
        $this->assign('signErrorInfo',$info);
        $this->assign('signErrorValue',$infoValue);
        $this->display('sign');
        exit();
    }
    public function signSuccess($email){
        $this->assign('signUseremail',$email);
        $this->assign('signSuccessFlag',1);
        $this->display('sign');
        exit();
    }
    public function activeInfo($activeInfo,$activeFlag){
        $this->assign('activeFlag',$activeFlag);
        $this->assign('activeInfo',$activeInfo);
        $this->display('active');
        exit();
    }
}