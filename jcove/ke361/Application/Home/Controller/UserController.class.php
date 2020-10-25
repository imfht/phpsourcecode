<?php
namespace Home\Controller;
use User\Api\UserApi;
use Home\Model\UserAddressModel;
use Home\Model\OrderModel;
use Home\Model\GoodsModel;
class UserController extends HomeController{
    public function index(){
        $this->isLogin();
        if(IS_POST){
            $where['uid'] = UID;
            $data['nickname'] = I('post.nickname');
            if(D("Member")->where($where)->save($data)){
                $this->success('操作成功');
            }else {
                $this->error('操作失败');
            }
        }
        $this->setSiteTitle('会员中心');
        $this->setKeyWords('会员,'.$this->my['nickname']);
        $this->setDescription('会员中心');
        $this->display();
    }
    public function info(){
        $this->isLogin();
        if(IS_POST){
            $where['uid'] = UID;
            $data['nickname'] = I('post.nickname');
            if(D("Member")->where($where)->save($data)){
                $this->success('操作成功');
            }else {
                $this->error('操作失败');
            }
        }
        $this->setSiteTitle('个人设置');
        $this->display();
    }

    public function unFavor(){
        $this->isLogin();
        if(IS_POST){
            $goodsId = I('id');
            if(D('Member')->delFavor(UID, $goodsId)){
                $this->success('取消成功');   
            }else{
                $this->success('您没有收藏该商品');   
            }            
        }
    }

    public function favor(){
        $this->isLogin();
       
        if(IS_POST){
            $goods_id = I('id');
            if(D('Member')->addFavor($this->my['uid'], $goods_id)){
                $this->success('收藏成功');   
            }else{
                $this->success('您已经收藏该商品');   
            }            
        }
        $where['uid'] = UID;
        $res = M('Favor')->field('goods_id')->where($where)->select();
        $in =array();
        foreach ($res as $row){
            $in[] = $row['goods_id'];
        }
        $in = implode(',', $in);
        $map['id'] =array('in',$in);
        $GoodsModel = new GoodsModel();
        $goods = $this->lists($GoodsModel,$map) ; 
        $this->assign('goods',$goods);
        if(IS_AJAX){
            $result['p']=I('get.p')+1;
            $result['content']=$this->fetch('ajaxfavor');
            $result['errno']=0;
            $this->ajaxReturn($result);
        }
        $this->display();
    }
    
   
    public function register($username = '', $password = '', $repassword = '', $email = '', $mobile = '', $verify = ''){
        if(!C('USER_ALLOW_REGISTER')){
            $this->error('注册已关闭');
        }
		if(IS_POST){ //注册用户
			
			/* 检测密码 */
			if($password != $repassword){
				$this->error('密码和重复密码不一致！');
			}			

			/* 调用注册接口注册用户 */
            $User = new UserApi;
			$uid = $User->register($username, $password, $email, $mobile);
			if(0 < $uid){ //注册成功

				$shareUserId = session('share_user');
				if(!empty($shareUserId) && intval($shareUserId)>0){
				    $shareUser = D('Member')->info($shareUserId);
				    $score = C('SHARE_SCORE');
                    $userScore = $shareUser['score']+$score;
                    D('Member')->setScore($shareUserId,$userScore);
				}
				$this->success('注册成功！',U('login'));
			} else { //注册失败，显示错误信息
				$this->error($this->showRegError($uid));
			}

		} else { //显示注册表单
		    $this->setSiteTitle('注册');
		    $this->setKeyWords('注册');
		    $this->setDescription('注册页');
			$this->display();
		}
	}
    
    public function signIn(){
        $this->isLogin();
        $this->display();
    }
    
    public function login($username = '', $password = '', $verify = ''){
      
        if(IS_POST){
            /* 调用UC登录接口登录 */
			$user = new UserApi;
			$uid = $user->login($username, $password);
            if(0 < $uid){ //UC登录成功
				/* 登录用户 */
				$Member = D('Member');
				if($Member->login($uid)){ //登录用户
					//TODO:跳转到登录前页面
					$this->success('登录成功！',U('Home/Index/index'));
					
				} else {
					$this->error($Member->getError());
				}

			 } else { //登录失败
				switch($uid) {
					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = '密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				$this->error($error);
			 }
        }else {
            if(UID){
                $this->redirect('User/index');
            }
            $this->setSiteTitle('登录');
            $this->setKeyWords('登录');
            $this->setDescription('登录页面');
            $this->display();
        }
       
    }

    public function resetPassword(){
        $this->isLogin();
       
        if(IS_POST){
            $oldPassWord = I('oldPassWord');
            $data['password'] = I('newPassWord');
            $newPassWord2 = I('newPassWord2');
            if($data['password'] != $newPassWord2){
                $this->error('两次密码不一致');
            }
   
            $Api    =   new UserApi();
            $res    =   $Api->updateInfo(UID, $oldPassWord, $data);
            if($res['status']){
                $this->success('修改密码成功！');
                
            }else{
                $this->error($res['info']);
            }
           
        }else {
            $this->display();
        }
        
    }

    public function logout(){
        if(is_login()){
            D('Member')->logout();
            session('[destroy]');
            $this->success('退出成功！', U('login'));
        } else {
            $this->redirect('login');
        }
    }

    public function forgetpwd(){
        //sendMail($mail,$msg);
        $this->success('新密码已发送至您注册邮箱，请及时修改密码！');
    }
    
    public function avatar(){
       
        if(IS_POST){
            $aCrop = I('post.crop', '');
            
            $aPath = I('post.path', '');
                    
            if (empty($aCrop)) {
                $this->success('保存成功！');
                }
            $returnPath = A('File')->cropPicture($aCrop,$aPath);
            $driver = C('PICTURE_UPLOAD_DRIVER');
            $data = array('uid' => UID,'avatar' => $returnPath);
            $res = M('Member')->where(array('uid' => UID))->save($data);
            $this->success('头像更新成功！', $redirect_url);
                  
          
        }else {
            $this->display();
        }
       
    }
    public function address(){
        $UserAddressModel = new UserAddressModel();
        $method = I('method');
        $id = I('get.id',0,'intval');
        if(!empty($method)){
            if($id <=0){
                $this->error('无效的id');
            }
        }
        if(IS_POST){
            if($UserAddressModel->addAdress()){
                $this->success('添加成功');
            }else {
                $this->error($UserAddressModel->getError());
            }
        }else{
            if ($method =='edit'){
                $userAddress = $UserAddressModel->info($id);
                $this->assign('user_address',$userAddress);
            }
            if ($method =='del'){
                $where['id'] = $id;
                if($UserAddressModel->where($where)->delete()){
                    $this->success('删除成功');
                }else {
                    $this->error($UserAddressModel->getError());
                }
            }
            if($method == 'set_default'){
                $res = $UserAddressModel->setDefault($id);
                if($res){
                    $this->success('操作成功');
                }else {
                   $this->error($UserAddressModel->getError()); 
                }
            }

            $addressList = $UserAddressModel->addressList();
            $this->assign('_list',$addressList);
            $this->display();
        }
        
    }
    public function editAddress(){
        $UserAddressModel = new UserAddressModel();
        
        $id = I('get.id',0,'intval');
        $userAddress = $UserAddressModel->info($id);
        $this->assign('user_address',$userAddress);
        if(IS_POST){
            if($UserAddressModel->addAdress()){
                $this->success('添加成功');
            }else {
                $this->error($UserAddressModel->getError());
            }
        }else{
            $this->display();
        }
    
    }
    public function ajaxAddressList(){
        $UserAddressModel = new UserAddressModel();
        $addressList = $UserAddressModel->addressList();
        $this->assign('_list',$addressList);
        $this->display();
    }
    public function order(){
        $orderList = $this->lists('Order',array('uid'=>UID));
        foreach ($orderList as $k=>$v){
            $orderList[$k]['goods_list'] = $this->listAll('OrderGoods',array('order_id'=>$v['id']));
        }
     
        $this->assign('_list',$orderList);
        $this->display();
    }
    public function orderInfo($id = 0){
        if($id <=0){
            $this->error('订单无效');
        }
        $where['id'] = $id;
        $OrderModel = new OrderModel();
        $order = $OrderModel->where($where)->find();
        $map['order_id'] =$id;
        $goodsList = D("OrderGoods")->where($map)->select();
        $this->assign('order',$order);
        $this->assign('goods_list',$goodsList);
        $this->display();
    }

    public function forgetPass(){
        session('step',1);
        $this->display();
    }
    /**
     * 找回密码
     */
    public function findPass(){
        //禁止缓存
        header('Cache-Control:no-cache,must-revalidate');
        header('Pragma:no-cache');
        $step = I('step');
        $verify = I('verify');
        switch ($step) {
            case 1:#第二步，验证身份
                if(!check_verify($verify)){
                    $this->error('验证码输入错误！');
                }
                $username = I('username');
                $user = new UserApi;
			    $info = $user->info($username,true);
                if ($info != false) {
                    session('findPass',array('uid'=>$info['0'],'username'=>$username,'mobile'=>$info['3'],'email'=>$info['2']) );
                    if($info['3']!='')$info['3'] = ke_str_replace($info['3'],'*',3);
                    if($info['2']!='')$info['2'] = ke_str_replace($info['2'],'*',2,'@');
                    $this->assign('forgetInfo',$info);
                    $this->display('forgetpass2');
                }else $this->error('该用户不存在！');
                break;
            case 2:#第三步,设置新密码
                if (session('findPass.username') != null ){
                    if (session('findPass.email')==null) {
                        $this->error('你没有预留邮箱，请通过手机号码找回密码！');
            }
                    if ( session('findPass.mobile') == null) {
                        $this->error('你没有预留手机号码，请通过邮箱方式找回密码！');
                    }
                }else $this->error('页面过期！');
                break;
            case 3:#设置成功
                $resetPass = session('REST_success');
                if($resetPass!='1')$this->error("非法的操作!");
                $password = I('password');
                $repassword = I('repassword');
                if ($password == $repassword) {
                    $user = new UserApi;
                    $rs = $user->setPassWord(session('REST_userId'), $password);
                    if($rs['status']){
                        $this->display('forgetpass4');
                    }else{
                       // $this->error($rs['info']);
                    }
                }else $this->error('两次密码不同！');
                break;
            default:
                $this->error('页面过期！');
                break;
        }
    }
    /**
     * 发送验证邮件
     */
    public function getEmailVerify(){
        $rs = array('status'=>-1);
        $keyFactory = new \Think\Crypt();
        $key = $keyFactory->encrypt("0_".session('findPass.uid')."_".time(),C('SESSION_PREFIX'),30*60);
        $url = "http://".$_SERVER['HTTP_HOST'].U('User/toResetPass',array('key'=>$key));
        $html="您好，会员 ".session('findPass.username')."：<br>
		您在".date('Y-m-d H:i:s')."发出了重置密码的请求,请点击以下链接进行密码重置:<br>
		<a href='".$url."'>".$url."</a><br>
		<br>如果您的邮箱不支持链接点击，请将以上链接地址拷贝到你的浏览器地址栏中。<br>
		该验证邮件有效期为30分钟，超时请重新发送邮件。<br>
		<br><br>*此邮件为系统自动发出的，请勿直接回复。";
        $sendRs = send_mail(session('findPass.email'),C('WEB_SITE_TITLE').'用户','密码重置',$html);
        if(true===$sendRs){
            $this->success('邮件发送成功，请注意查收');
        }else{
            $this->error($sendRs);
            }
        
    }
    /**
     * 跳到重置密码
     */
    public function toResetPass(){
        $key = I('key');
        $keyFactory = new \Think\Crypt();
        $key = $keyFactory->decrypt($key,C('SESSION_PREFIX'));
        $key = explode('_',$key);
        if(time()>floatval($key[2])+30*60)$this->error('连接已失效！');
        if(intval($key[1])==0)$this->error('无效的用户！');
        session('REST_userId',$key[1]);
        session('REST_Time',$key[2]);
        session('REST_success','1');
        $this->display('forgetpass3');
    }
    public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(1);
    }
    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0){
        switch ($code) {
            case -1:  $error = '用户名长度必须在16个字符以内！'; break;
            case -2:  $error = '用户名被禁止注册！'; break;
            case -3:  $error = '用户名被占用！'; break;
            case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
            case -5:  $error = '邮箱格式不正确！'; break;
            case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
            case -7:  $error = '邮箱被禁止注册！'; break;
            case -8:  $error = '邮箱被占用！'; break;
            case -9:  $error = '手机格式不正确！'; break;
            case -10: $error = '手机被禁止注册！'; break;
            case -11: $error = '手机号被占用！'; break;
            default:  $error = '未知错误';
        }
        return $error;
    }
}