<?php
class c_download extends DtDatabase
{
	public $id;
	public $channelId;
	public $title;
	public $keywords;
	public $description;
	public $fileSize;
	public $dtTime;
	public $content;
	public $filePath;
	public $counts;
	public $ordering;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'download';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['channelId'])$this->channelId=$request['channelId'];
		$this->title=$request['title'];
		if($request['keywords'])$this->keywords=$request['keywords'];
		$this->description=$request['description'];
		if($request['fileSize'])$this->fileSize=$request['fileSize'];
		if($request['dtTime'])$this->dtTime=$request['dtTime'];
		$this->content=$request['content'];
		if($request['filePath'])$this->filePath=$request['filePath'];
		if($request['counts'])$this->counts=$request['counts'];
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
		$sql.=isset($this->title)?"title,":'';
		$sql.=isset($this->keywords)?"keywords,":'';
		$sql.=isset($this->description)?"description,":'';
		$sql.=isset($this->fileSize)?"fileSize,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
		$sql.=isset($this->content)?"content,":'';
		$sql.=isset($this->filePath)?"filePath,":'';
		$sql.=isset($this->counts)?"counts,":'';
		$sql.=isset($this->ordering)?"ordering,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->title)?"'$this->title',":'';
		$sql.=isset($this->keywords)?"'$this->keywords',":'';
		$sql.=isset($this->description)?"'$this->description',":'';
		$sql.=isset($this->fileSize)?"'$this->fileSize',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
		$sql.=isset($this->content)?"'$this->content',":'';
		$sql.=isset($this->filePath)?"'$this->filePath',":'';
		$sql.=isset($this->counts)?"'$this->counts',":'';
		$sql.=isset($this->ordering)?"'$this->ordering',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->channelId)?"`channelId`='$this->channelId',":'';
		$sql.=isset($this->title)?"`title`='$this->title',":'';
		$sql.=isset($this->keywords)?"`keywords`='$this->keywords',":'';
		$sql.=isset($this->description)?"`description`='$this->description',":'';
		$sql.=isset($this->fileSize)?"`fileSize`='$this->fileSize',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
		$sql.=isset($this->content)?"`content`='$this->content',":'';
		$sql.=isset($this->filePath)?"`filePath`='$this->filePath',":'';
		$sql.=isset($this->counts)?"`counts`='$this->counts',":'';
		$sql.=isset($this->ordering)?"`ordering`='$this->ordering',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>