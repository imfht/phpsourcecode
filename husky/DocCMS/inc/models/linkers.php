<?php
class c_linkers extends DtDatabase
{
	public $id;
	public $channelId;
	public $links;
	public $title;
	public $linkAddress;
	public $originalPic;
	public $smallPic;
	public $description;
	public $dtTime;
	public $ordering;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'linkers';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['channelId'])$this->channelId=$request['channelId'];
		if($request['links'])$this->links=$request['links'];
		$this->title=$request['title'];
		if($request['linkAddress'])$this->linkAddress=$request['linkAddress'];
		if($request['originalPic'])$this->originalPic=$request['originalPic'];
		if($request['smallPic'])$this->smallPic=$request['smallPic'];
		$this->description=$request['description'];
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
		$sql.=isset($this->channelId)?"channelId,":'';
		$sql.=isset($this->links)?"links,":'';
		$sql.=isset($this->title)?"title,":'';
		$sql.=isset($this->linkAddress)?"linkAddress,":'';
		$sql.=isset($this->originalPic)?"originalPic,":'';
		$sql.=isset($this->smallPic)?"smallPic,":'';
		$sql.=isset($this->description)?"description,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
		$sql.=isset($this->ordering)?"ordering,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->links)?"'$this->links',":'';
		$sql.=isset($this->title)?"'$this->title',":'';
		$sql.=isset($this->linkAddress)?"'$this->linkAddress',":'';
		$sql.=isset($this->originalPic)?"'$this->originalPic',":'';
		$sql.=isset($this->smallPic)?"'$this->smallPic',":'';
		$sql.=isset($this->description)?"'$this->description',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
		$sql.=isset($this->ordering)?"'$this->ordering',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->channelId)?"`channelId`='$this->channelId',":'';
		$sql.=isset($this->links)?"`links`='$this->links',":'';
		$sql.=isset($this->title)?"`title`='$this->title',":'';
		$sql.=isset($this->linkAddress)?"`linkAddress`='$this->linkAddress',":'';
		$sql.=isset($this->originalPic)?"`originalPic`='$this->originalPic',":'';
		$sql.=isset($this->smallPic)?"`smallPic`='$this->smallPic',":'';
		$sql.=isset($this->description)?"`description`='$this->description',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
		$sql.=isset($this->ordering)?"`ordering`='$this->ordering',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>