<?php
class c_comment extends DtDatabase
{
	public $id;
	public $recordId;
	public $channelId;
	public $name;
	public $content;
	public $email;
	public $homepage;
	public $ip;
	public $dtTime;
	public $auditing;
	public $ordering;
	public $memberId;
	public $memberTableName;
	public $answerId;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'comment';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['recordId'])$this->recordId=$request['recordId'];
		if($request['channelId'])$this->channelId=$request['channelId'];
		if($request['name'])$this->name=$request['name'];
		$this->content=$request['content'];
		if($request['email'])$this->email=$request['email'];
		if($request['homepage'])$this->homepage=$request['homepage'];
		if($request['ip'])$this->ip=$request['ip'];
		if($request['dtTime'])$this->dtTime=$request['dtTime'];
		if($request['auditing'])$this->auditing=$request['auditing'];
		if($request['ordering'])$this->ordering=$request['ordering'];
		if($request['memberId'])$this->memberId=$request['memberId'];
		if($request['memberTableName'])$this->memberTableName=$request['memberTableName'];
		if($request['answerId'])$this->answerId=$request['answerId'];
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
		$sql.=isset($this->recordId)?"recordId,":'';
		$sql.=isset($this->channelId)?"channelId,":'';
		$sql.=isset($this->name)?"name,":'';
		$sql.=isset($this->content)?"content,":'';
		$sql.=isset($this->email)?"email,":'';
		$sql.=isset($this->homepage)?"homepage,":'';
		$sql.=isset($this->ip)?"ip,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
		$sql.=isset($this->auditing)?"auditing,":'';
		$sql.=isset($this->ordering)?"ordering,":'';
		$sql.=isset($this->memberId)?"memberId,":'';
		$sql.=isset($this->memberTableName)?"memberTableName,":'';
		$sql.=isset($this->answerId)?"answerId,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->recordId)?"'$this->recordId',":'';
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->name)?"'$this->name',":'';
		$sql.=isset($this->content)?"'$this->content',":'';
		$sql.=isset($this->email)?"'$this->email',":'';
		$sql.=isset($this->homepage)?"'$this->homepage',":'';
		$sql.=isset($this->ip)?"'$this->ip',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
		$sql.=isset($this->auditing)?"'$this->auditing',":'';
		$sql.=isset($this->ordering)?"'$this->ordering',":'';
		$sql.=isset($this->memberId)?"'$this->memberId',":'';
		$sql.=isset($this->memberTableName)?"'$this->memberTableName',":'';
		$sql.=isset($this->answerId)?"'$this->answerId',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->recordId)?"`recordId`='$this->recordId',":'';
		$sql.=isset($this->channelId)?"`channelId`='$this->channelId',":'';
		$sql.=isset($this->name)?"`name`='$this->name',":'';
		$sql.=isset($this->content)?"`content`='$this->content',":'';
		$sql.=isset($this->email)?"`email`='$this->email',":'';
		$sql.=isset($this->homepage)?"`homepage`='$this->homepage',":'';
		$sql.=isset($this->ip)?"`ip`='$this->ip',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
		$sql.=isset($this->auditing)?"`auditing`='$this->auditing',":'';
		$sql.=isset($this->ordering)?"`ordering`='$this->ordering',":'';
		$sql.=isset($this->memberId)?"`memberId`='$this->memberId',":'';
		$sql.=isset($this->memberTableName)?"`memberTableName`='$this->memberTableName',":'';
		$sql.=isset($this->answerId)?"`answerId`='$this->answerId',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>