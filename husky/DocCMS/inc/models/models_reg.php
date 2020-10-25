<?php
class c_models_reg extends DtDatabase
{
	public $id;
	public $type;
	public $model_name;
	public $config;
	public $install;
	public $unstall;
	public $summary;
	public $version;
	public $readonly;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'models_reg';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['type'])$this->type=$request['type'];
		if($request['model_name'])$this->model_name=$request['model_name'];
		if($request['config'])$this->config=$request['config'];
		if($request['install'])$this->install=$request['install'];
		if($request['unstall'])$this->unstall=$request['unstall'];
		$this->summary=$request['summary'];
		if($request['version'])$this->version=$request['version'];
		if($request['readonly'])$this->readonly=$request['readonly'];
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
		$sql.=isset($this->type)?"type,":'';
		$sql.=isset($this->model_name)?"model_name,":'';
		$sql.=isset($this->config)?"config,":'';
		$sql.=isset($this->install)?"install,":'';
		$sql.=isset($this->unstall)?"unstall,":'';
		$sql.=isset($this->summary)?"summary,":'';
		$sql.=isset($this->version)?"version,":'';
		$sql.=isset($this->readonly)?"readonly,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->type)?"'$this->type',":'';
		$sql.=isset($this->model_name)?"'$this->model_name',":'';
		$sql.=isset($this->config)?"'$this->config',":'';
		$sql.=isset($this->install)?"'$this->install',":'';
		$sql.=isset($this->unstall)?"'$this->unstall',":'';
		$sql.=isset($this->summary)?"'$this->summary',":'';
		$sql.=isset($this->version)?"'$this->version',":'';
		$sql.=isset($this->readonly)?"'$this->readonly',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->type)?"`type`='$this->type',":'';
		$sql.=isset($this->model_name)?"`model_name`='$this->model_name',":'';
		$sql.=isset($this->config)?"`config`='$this->config',":'';
		$sql.=isset($this->install)?"`install`='$this->install',":'';
		$sql.=isset($this->unstall)?"`unstall`='$this->unstall',":'';
		$sql.=isset($this->summary)?"`summary`='$this->summary',":'';
		$sql.=isset($this->version)?"`version`='$this->version',":'';
		$sql.=isset($this->readonly)?"`readonly`='$this->readonly',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>