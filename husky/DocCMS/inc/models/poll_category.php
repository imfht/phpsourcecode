<?php
class c_poll_category extends DtDatabase
{
	public $id;
	public $title;
	public $choice;
	public $client_ip;
	public $channelId;
	public $dtTime;
	public $ordering;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'poll_category';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		$this->title=$request['title'];
		if($request['choice'])$this->choice=$request['choice'];
		if($request['client_ip'])$this->client_ip=$request['client_ip'];
		if($request['channelId'])$this->channelId=$request['channelId'];
		if($request['dtTime'])$this->dtTime=$request['dtTime'];
		if($request['ordering'])$this->ordering=$request['ordering'];
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
		$sql.=isset($this->title)?"title,":'';
		$sql.=isset($this->choice)?"choice,":'';
		$sql.=isset($this->client_ip)?"client_ip,":'';
		$sql.=isset($this->channelId)?"channelId,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
		$sql.=isset($this->ordering)?"ordering,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->title)?"'$this->title',":'';
		$sql.=isset($this->choice)?"'$this->choice',":'';
		$sql.=isset($this->client_ip)?"'$this->client_ip',":'';
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
		$sql.=isset($this->ordering)?"'$this->ordering',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->title)?"`title`='$this->title',":'';
		$sql.=isset($this->choice)?"`choice`='$this->choice',":'';
		$sql.=isset($this->client_ip)?"`client_ip`='$this->client_ip',":'';
		$sql.=isset($this->channelId)?"`channelId`='$this->channelId',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
		$sql.=isset($this->ordering)?"`ordering`='$this->ordering',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>