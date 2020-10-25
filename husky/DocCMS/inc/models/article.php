<?php
class c_article extends DtDatabase
{
	public $id;
	public $channelId;
	public $pageId;
	public $title;
	public $keywords;
	public $description;
	public $content;
	public $dtTime;
	public $originalPic;
	public $indexPic;
	public $counts;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'article';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['channelId'])$this->channelId=$request['channelId'];
		if($request['pageId'])$this->pageId=$request['pageId'];
		$this->title=$request['title'];
		if($request['keywords'])$this->keywords=$request['keywords'];
		$this->description=$request['description'];
		$this->content=$request['content'];
		if($request['dtTime'])$this->dtTime=$request['dtTime'];
		if($request['originalPic'])$this->originalPic=$request['originalPic'];
		if($request['indexPic'])$this->indexPic=$request['indexPic'];
		if($request['counts'])$this->counts=$request['counts'];
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
		$sql.=isset($this->pageId)?"pageId,":'';
		$sql.=isset($this->title)?"title,":'';
		$sql.=isset($this->keywords)?"keywords,":'';
		$sql.=isset($this->description)?"description,":'';
		$sql.=isset($this->content)?"content,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
		$sql.=isset($this->originalPic)?"originalPic,":'';
		$sql.=isset($this->indexPic)?"indexPic,":'';
		$sql.=isset($this->counts)?"counts,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->pageId)?"'$this->pageId',":'';
		$sql.=isset($this->title)?"'$this->title',":'';
		$sql.=isset($this->keywords)?"'$this->keywords',":'';
		$sql.=isset($this->description)?"'$this->description',":'';
		$sql.=isset($this->content)?"'$this->content',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
		$sql.=isset($this->originalPic)?"'$this->originalPic',":'';
		$sql.=isset($this->indexPic)?"'$this->indexPic',":'';
		$sql.=isset($this->counts)?"'$this->counts',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->channelId)?"`channelId`='$this->channelId',":'';
		$sql.=isset($this->pageId)?"`pageId`='$this->pageId',":'';
		$sql.=isset($this->title)?"`title`='$this->title',":'';
		$sql.=isset($this->keywords)?"`keywords`='$this->keywords',":'';
		$sql.=isset($this->description)?"`description`='$this->description',":'';
		$sql.=isset($this->content)?"`content`='$this->content',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
		$sql.=isset($this->originalPic)?"`originalPic`='$this->originalPic',":'';
		$sql.=isset($this->indexPic)?"`indexPic`='$this->indexPic',":'';
		$sql.=isset($this->counts)?"`counts`='$this->counts',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>