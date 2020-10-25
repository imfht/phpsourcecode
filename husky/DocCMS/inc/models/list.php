<?php
class c_list extends DtDatabase
{
	public $id;
	public $channelId;
	public $title;
	public $style;
	public $keywords;
	public $description;
	public $author;
	public $source;
	public $counts;
	public $dtTime;
	public $editTime;
	public $recommend;
	public $content;
	public $sourceUrl;
	public $originalPic;
	public $indexPic;
	public $ordering;
	public $hassplitpages;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'list';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['channelId'])$this->channelId=$request['channelId'];
		$this->title=$request['title'];
		if($request['style'])$this->style=$request['style'];
		if($request['keywords'])$this->keywords=$request['keywords'];
		$this->description=$request['description'];
		if($request['author'])$this->author=$request['author'];
		if($request['source'])$this->source=$request['source'];
		if($request['counts'])$this->counts=$request['counts'];
		if($request['dtTime'])$this->dtTime=$request['dtTime'];
		if($request['editTime'])$this->editTime=$request['editTime'];
		if($request['recommend'])$this->recommend=$request['recommend'];
		$this->content=$request['content'];
		if($request['sourceUrl'])$this->sourceUrl=$request['sourceUrl'];
		if($request['originalPic'])$this->originalPic=$request['originalPic'];
		if($request['indexPic'])$this->indexPic=$request['indexPic'];
		if($request['ordering'])$this->ordering=$request['ordering'];
		if($request['hassplitpages'])$this->hassplitpages=$request['hassplitpages'];
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
		$sql.=isset($this->style)?"style,":'';
		$sql.=isset($this->keywords)?"keywords,":'';
		$sql.=isset($this->description)?"description,":'';
		$sql.=isset($this->author)?"author,":'';
		$sql.=isset($this->source)?"source,":'';
		$sql.=isset($this->counts)?"counts,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
		$sql.=isset($this->editTime)?"editTime,":'';
		$sql.=isset($this->recommend)?"recommend,":'';
		$sql.=isset($this->content)?"content,":'';
		$sql.=isset($this->sourceUrl)?"sourceUrl,":'';
		$sql.=isset($this->originalPic)?"originalPic,":'';
		$sql.=isset($this->indexPic)?"indexPic,":'';
		$sql.=isset($this->ordering)?"ordering,":'';
		$sql.=isset($this->hassplitpages)?"hassplitpages,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->title)?"'$this->title',":'';
		$sql.=isset($this->style)?"'$this->style',":'';
		$sql.=isset($this->keywords)?"'$this->keywords',":'';
		$sql.=isset($this->description)?"'$this->description',":'';
		$sql.=isset($this->author)?"'$this->author',":'';
		$sql.=isset($this->source)?"'$this->source',":'';
		$sql.=isset($this->counts)?"'$this->counts',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
		$sql.=isset($this->editTime)?"'$this->editTime',":'';
		$sql.=isset($this->recommend)?"'$this->recommend',":'';
		$sql.=isset($this->content)?"'$this->content',":'';
		$sql.=isset($this->sourceUrl)?"'$this->sourceUrl',":'';
		$sql.=isset($this->originalPic)?"'$this->originalPic',":'';
		$sql.=isset($this->indexPic)?"'$this->indexPic',":'';
		$sql.=isset($this->ordering)?"'$this->ordering',":'';
		$sql.=isset($this->hassplitpages)?"'$this->hassplitpages',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->channelId)?"`channelId`='$this->channelId',":'';
		$sql.=isset($this->title)?"`title`='$this->title',":'';
		$sql.=isset($this->style)?"`style`='$this->style',":'';
		$sql.=isset($this->keywords)?"`keywords`='$this->keywords',":'';
		$sql.=isset($this->description)?"`description`='$this->description',":'';
		$sql.=isset($this->author)?"`author`='$this->author',":'';
		$sql.=isset($this->source)?"`source`='$this->source',":'';
		$sql.=isset($this->counts)?"`counts`='$this->counts',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
		$sql.=isset($this->editTime)?"`editTime`='$this->editTime',":'';
		$sql.=isset($this->recommend)?"`recommend`='$this->recommend',":'';
		$sql.=isset($this->content)?"`content`='$this->content',":'';
		$sql.=isset($this->sourceUrl)?"`sourceUrl`='$this->sourceUrl',":'';
		$sql.=isset($this->originalPic)?"`originalPic`='$this->originalPic',":'';
		$sql.=isset($this->indexPic)?"`indexPic`='$this->indexPic',":'';
		$sql.=isset($this->ordering)?"`ordering`='$this->ordering',":'';
		$sql.=isset($this->hassplitpages)?"`hassplitpages`='$this->hassplitpages',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>