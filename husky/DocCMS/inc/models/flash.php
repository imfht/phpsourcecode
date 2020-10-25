<?php
class c_flash extends DtDatabase
{
	public $id;
	public $title;
	public $description;
	public $url;
	public $picpath;
	public $group_id;
	public $ordering;
	public $dtTime;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'flash';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		$this->title=$request['title'];
		$this->description=$request['description'];
		if($request['url'])$this->url=$request['url'];
		if($request['picpath'])$this->picpath=$request['picpath'];
		if($request['group_id'])$this->group_id=$request['group_id'];
		if($request['ordering'])$this->ordering=$request['ordering'];
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
		$sql.=isset($this->description)?"description,":'';
		$sql.=isset($this->url)?"url,":'';
		$sql.=isset($this->picpath)?"picpath,":'';
		$sql.=isset($this->group_id)?"group_id,":'';
		$sql.=isset($this->ordering)?"ordering,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->title)?"'$this->title',":'';
		$sql.=isset($this->description)?"'$this->description',":'';
		$sql.=isset($this->url)?"'$this->url',":'';
		$sql.=isset($this->picpath)?"'$this->picpath',":'';
		$sql.=isset($this->group_id)?"'$this->group_id',":'';
		$sql.=isset($this->ordering)?"'$this->ordering',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->title)?"`title`='$this->title',":'';
		$sql.=isset($this->description)?"`description`='$this->description',":'';
		$sql.=isset($this->url)?"`url`='$this->url',":'';
		$sql.=isset($this->picpath)?"`picpath`='$this->picpath',":'';
		$sql.=isset($this->group_id)?"`group_id`='$this->group_id',":'';
		$sql.=isset($this->ordering)?"`ordering`='$this->ordering',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>