<?php
class c_models_set extends DtDatabase
{
	public $id;
	public $channelId;
	public $type;
	public $field;
	public $field_tab;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'models_set';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['channelId'])$this->channelId=$request['channelId'];
		if($request['type'])$this->type=$request['type'];
		if($request['field'])$this->field=$request['field'];
		if($request['field_tab'])$this->field_tab=$request['field_tab'];
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
		$sql.=isset($this->type)?"type,":'';
		$sql.=isset($this->field)?"field,":'';
		$sql.=isset($this->field_tab)?"field_tab,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->type)?"'$this->type',":'';
		$sql.=isset($this->field)?"'$this->field',":'';
		$sql.=isset($this->field_tab)?"'$this->field_tab',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->channelId)?"`channelId`='$this->channelId',":'';
		$sql.=isset($this->type)?"`type`='$this->type',":'';
		$sql.=isset($this->field)?"`field`='$this->field',":'';
		$sql.=isset($this->field_tab)?"`field_tab`='$this->field_tab',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>