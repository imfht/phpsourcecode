<?php
class c_flash_group extends DtDatabase
{
	public $id;
	public $title;
	public $summary;
	public $type;
	public $boxId;
	public $pattern;
	public $times;
	public $adTrigger;
	public $auto;
	public $width;
	public $height;
	public $txtHeight;
	public $dtTime;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'flash_group';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		$this->title=$request['title'];
		$this->summary=$request['summary'];
		if($request['type'])$this->type=$request['type'];
		if($request['boxId'])$this->boxId=$request['boxId'];
		if($request['pattern'])$this->pattern=$request['pattern'];
		if($request['times'])$this->times=$request['times'];
		if($request['adTrigger'])$this->adTrigger=$request['adTrigger'];
		if($request['auto'])$this->auto=$request['auto'];
		if($request['width'])$this->width=$request['width'];
		if($request['height'])$this->height=$request['height'];
		$this->txtHeight=$request['txtHeight'];
		if($request['dtTime'])$this->dtTime=$request['dtTime'];
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
		$sql.=isset($this->summary)?"summary,":'';
		$sql.=isset($this->type)?"type,":'';
		$sql.=isset($this->boxId)?"boxId,":'';
		$sql.=isset($this->pattern)?"pattern,":'';
		$sql.=isset($this->times)?"times,":'';
		$sql.=isset($this->adTrigger)?"adTrigger,":'';
		$sql.=isset($this->auto)?"auto,":'';
		$sql.=isset($this->width)?"width,":'';
		$sql.=isset($this->height)?"height,":'';
		$sql.=isset($this->txtHeight)?"txtHeight,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->title)?"'$this->title',":'';
		$sql.=isset($this->summary)?"'$this->summary',":'';
		$sql.=isset($this->type)?"'$this->type',":'';
		$sql.=isset($this->boxId)?"'$this->boxId',":'';
		$sql.=isset($this->pattern)?"'$this->pattern',":'';
		$sql.=isset($this->times)?"'$this->times',":'';
		$sql.=isset($this->adTrigger)?"'$this->adTrigger',":'';
		$sql.=isset($this->auto)?"'$this->auto',":'';
		$sql.=isset($this->width)?"'$this->width',":'';
		$sql.=isset($this->height)?"'$this->height',":'';
		$sql.=isset($this->txtHeight)?"'$this->txtHeight',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->title)?"`title`='$this->title',":'';
		$sql.=isset($this->summary)?"`summary`='$this->summary',":'';
		$sql.=isset($this->type)?"`type`='$this->type',":'';
		$sql.=isset($this->boxId)?"`boxId`='$this->boxId',":'';
		$sql.=isset($this->pattern)?"`pattern`='$this->pattern',":'';
		$sql.=isset($this->times)?"`times`='$this->times',":'';
		$sql.=isset($this->adTrigger)?"`adTrigger`='$this->adTrigger',":'';
		$sql.=isset($this->auto)?"`auto`='$this->auto',":'';
		$sql.=isset($this->width)?"`width`='$this->width',":'';
		$sql.=isset($this->height)?"`height`='$this->height',":'';
		$sql.=isset($this->txtHeight)?"`txtHeight`='$this->txtHeight',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>