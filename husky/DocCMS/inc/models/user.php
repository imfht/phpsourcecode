<?php
class c_user extends DtDatabase
{
	public $id;
	public $nickname;
	public $email;
	public $username;
	public $pwd;
	public $role;
	public $right;
	public $originalPic;
	public $smallPic;
	public $cropPic;
	public $dtTime;
	public $auditing;
	public $ip;
	public $qq;
	public $msn;
	public $name;
	public $sex;
	public $mtel;
	public $address;
	public $age;
	public $lastlogin;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'user';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['nickname'])$this->nickname=$request['nickname'];
		if($request['email'])$this->email=$request['email'];
		if($request['username'])$this->username=$request['username'];
		if($request['pwd'])$this->pwd=$request['pwd'];
		if($request['role'])$this->role=$request['role'];
		if($request['right'])$this->right=$request['right'];
		if($request['originalPic'])$this->originalPic=$request['originalPic'];
		if($request['smallPic'])$this->smallPic=$request['smallPic'];
		if($request['cropPic'])$this->cropPic=$request['cropPic'];
		if($request['dtTime'])$this->dtTime=$request['dtTime'];
		if($request['auditing'])$this->auditing=$request['auditing'];
		if($request['ip'])$this->ip=$request['ip'];
		if($request['qq'])$this->qq=$request['qq'];
		if($request['msn'])$this->msn=$request['msn'];
		if($request['name'])$this->name=$request['name'];
		if($request['sex'])$this->sex=$request['sex'];
		if($request['mtel'])$this->mtel=$request['mtel'];
		if($request['address'])$this->address=$request['address'];
		if($request['age'])$this->age=$request['age'];
		if($request['lastlogin'])$this->lastlogin=$request['lastlogin'];
		}
		}

	public function addnew($request=array())
	{
		$this->im_virgin =true;		if(!empty($request)){
		$this->get_request($request);
		}
		}

	public function save()
	{
		if($this->im_virgin){
		eval("\$this->$this->primary_key=NULL;");
		$sql="INSERT INTO `$this->table_name` (";
		$sql.=isset($this->nickname)?"nickname,":'';
		$sql.=isset($this->email)?"email,":'';
		$sql.=isset($this->username)?"username,":'';
		$sql.=isset($this->pwd)?"pwd,":'';
		$sql.=isset($this->role)?"role,":'';
		$sql.=isset($this->right)?"right,":'';
		$sql.=isset($this->originalPic)?"originalPic,":'';
		$sql.=isset($this->smallPic)?"smallPic,":'';
		$sql.=isset($this->cropPic)?"cropPic,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
		$sql.=isset($this->auditing)?"auditing,":'';
		$sql.=isset($this->ip)?"ip,":'';
		$sql.=isset($this->qq)?"qq,":'';
		$sql.=isset($this->msn)?"msn,":'';
		$sql.=isset($this->name)?"name,":'';
		$sql.=isset($this->sex)?"sex,":'';
		$sql.=isset($this->mtel)?"mtel,":'';
		$sql.=isset($this->address)?"address,":'';
		$sql.=isset($this->age)?"age,":'';
		$sql.=isset($this->lastlogin)?"lastlogin,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->nickname)?"'$this->nickname',":'';
		$sql.=isset($this->email)?"'$this->email',":'';
		$sql.=isset($this->username)?"'$this->username',":'';
		$sql.=isset($this->pwd)?"'$this->pwd',":'';
		$sql.=isset($this->role)?"'$this->role',":'';
		$sql.=isset($this->right)?"'$this->right',":'';
		$sql.=isset($this->originalPic)?"'$this->originalPic',":'';
		$sql.=isset($this->smallPic)?"'$this->smallPic',":'';
		$sql.=isset($this->cropPic)?"'$this->cropPic',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
		$sql.=isset($this->auditing)?"'$this->auditing',":'';
		$sql.=isset($this->ip)?"'$this->ip',":'';
		$sql.=isset($this->qq)?"'$this->qq',":'';
		$sql.=isset($this->msn)?"'$this->msn',":'';
		$sql.=isset($this->name)?"'$this->name',":'';
		$sql.=isset($this->sex)?"'$this->sex',":'';
		$sql.=isset($this->mtel)?"'$this->mtel',":'';
		$sql.=isset($this->address)?"'$this->address',":'';
		$sql.=isset($this->age)?"'$this->age',":'';
		$sql.=isset($this->lastlogin)?"'$this->lastlogin',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->nickname)?"`nickname`='$this->nickname',":'';
		$sql.=isset($this->email)?"`email`='$this->email',":'';
		$sql.=isset($this->username)?"`username`='$this->username',":'';
		$sql.=isset($this->pwd)?"`pwd`='$this->pwd',":'';
		$sql.=isset($this->role)?"`role`='$this->role',":'';
		$sql.=isset($this->right)?"`right`='$this->right',":'';
		$sql.=isset($this->originalPic)?"`originalPic`='$this->originalPic',":'';
		$sql.=isset($this->smallPic)?"`smallPic`='$this->smallPic',":'';
		$sql.=isset($this->cropPic)?"`cropPic`='$this->cropPic',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
		$sql.=isset($this->auditing)?"`auditing`='$this->auditing',":'';
		$sql.=isset($this->ip)?"`ip`='$this->ip',":'';
		$sql.=isset($this->qq)?"`qq`='$this->qq',":'';
		$sql.=isset($this->msn)?"`msn`='$this->msn',":'';
		$sql.=isset($this->name)?"`name`='$this->name',":'';
		$sql.=isset($this->sex)?"`sex`='$this->sex',":'';
		$sql.=isset($this->mtel)?"`mtel`='$this->mtel',":'';
		$sql.=isset($this->address)?"`address`='$this->address',":'';
		$sql.=isset($this->age)?"`age`='$this->age',":'';
		$sql.=isset($this->lastlogin)?"`lastlogin`='$this->lastlogin',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>