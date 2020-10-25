<?php
class c_guestbook extends DtDatabase
{
	public $id;
	public $name;
	public $contact;
	public $custom;
	public $content;
	public $content1;
	public $channelId;
	public $ip;
	public $uid;
	public $dtTime;
	public $auditing;
	public $isPublic;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'guestbook';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['name'])$this->name=$request['name'];
		if($request['contact'])$this->contact=$request['contact'];
		if($request['custom'])$this->custom=$request['custom'];
		$this->content=$request['content'];
		if($request['content1'])$this->content1=$request['content1'];
		if($request['channelId'])$this->channelId=$request['channelId'];
		if($request['ip'])$this->ip=$request['ip'];
		if($request['uid'])$this->uid=$request['uid'];
		if($request['dtTime'])$this->dtTime=$request['dtTime'];
		if($request['auditing'])$this->auditing=$request['auditing'];
		if($request['isPublic'])$this->isPublic=$request['isPublic'];
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
		$sql.=isset($this->name)?"name,":'';
		$sql.=isset($this->contact)?"contact,":'';
		$sql.=isset($this->custom)?"custom,":'';
		$sql.=isset($this->content)?"content,":'';
		$sql.=isset($this->content1)?"content1,":'';
		$sql.=isset($this->channelId)?"channelId,":'';
		$sql.=isset($this->ip)?"ip,":'';
		$sql.=isset($this->uid)?"uid,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
		$sql.=isset($this->auditing)?"auditing,":'';
		$sql.=isset($this->isPublic)?"isPublic,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->name)?"'$this->name',":'';
		$sql.=isset($this->contact)?"'$this->contact',":'';
		$sql.=isset($this->custom)?"'$this->custom',":'';
		$sql.=isset($this->content)?"'$this->content',":'';
		$sql.=isset($this->content1)?"'$this->content1',":'';
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->ip)?"'$this->ip',":'';
		$sql.=isset($this->uid)?"'$this->uid',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
		$sql.=isset($this->auditing)?"'$this->auditing',":'';
		$sql.=isset($this->isPublic)?"'$this->isPublic',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->name)?"`name`='$this->name',":'';
		$sql.=isset($this->contact)?"`contact`='$this->contact',":'';
		$sql.=isset($this->custom)?"`custom`='$this->custom',":'';
		$sql.=isset($this->content)?"`content`='$this->content',":'';
		$sql.=isset($this->content1)?"`content1`='$this->content1',":'';
		$sql.=isset($this->channelId)?"`channelId`='$this->channelId',":'';
		$sql.=isset($this->ip)?"`ip`='$this->ip',":'';
		$sql.=isset($this->uid)?"`uid`='$this->uid',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
		$sql.=isset($this->auditing)?"`auditing`='$this->auditing',":'';
		$sql.=isset($this->isPublic)?"`isPublic`='$this->isPublic',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>