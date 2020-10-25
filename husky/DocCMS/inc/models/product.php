<?php
class c_product extends DtDatabase
{
	public $id;
	public $channelId;
	public $title;
	public $keywords;
	public $description;
	public $sn;
	public $spec;
	public $dtTime;
	public $ispush;
	public $sellingPrice;
	public $preferPrice;
	public $content;
	public $originalPic;
	public $middlePic;
	public $smallPic;
	public $indexPic;
	public $categoryId;
	public $counts;
	public $ordering;
	public $hassplitpages;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'product';
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
		if($request['sn'])$this->sn=$request['sn'];
		if($request['spec'])$this->spec=$request['spec'];
		if($request['dtTime'])$this->dtTime=$request['dtTime'];
		if($request['ispush'])$this->ispush=$request['ispush'];
		if($request['sellingPrice'])$this->sellingPrice=$request['sellingPrice'];
		if($request['preferPrice'])$this->preferPrice=$request['preferPrice'];
		$this->content=$request['content'];
		if($request['originalPic'])$this->originalPic=$request['originalPic'];
		if($request['middlePic'])$this->middlePic=$request['middlePic'];
		if($request['smallPic'])$this->smallPic=$request['smallPic'];
		if($request['indexPic'])$this->indexPic=$request['indexPic'];
		if($request['categoryId'])$this->categoryId=$request['categoryId'];
		if($request['counts'])$this->counts=$request['counts'];
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
		$sql.=isset($this->keywords)?"keywords,":'';
		$sql.=isset($this->description)?"description,":'';
		$sql.=isset($this->sn)?"sn,":'';
		$sql.=isset($this->spec)?"spec,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
		$sql.=isset($this->ispush)?"ispush,":'';
		$sql.=isset($this->sellingPrice)?"sellingPrice,":'';
		$sql.=isset($this->preferPrice)?"preferPrice,":'';
		$sql.=isset($this->content)?"content,":'';
		$sql.=isset($this->originalPic)?"originalPic,":'';
		$sql.=isset($this->middlePic)?"middlePic,":'';
		$sql.=isset($this->smallPic)?"smallPic,":'';
		$sql.=isset($this->indexPic)?"indexPic,":'';
		$sql.=isset($this->categoryId)?"categoryId,":'';
		$sql.=isset($this->counts)?"counts,":'';
		$sql.=isset($this->ordering)?"ordering,":'';
		$sql.=isset($this->hassplitpages)?"hassplitpages,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->title)?"'$this->title',":'';
		$sql.=isset($this->keywords)?"'$this->keywords',":'';
		$sql.=isset($this->description)?"'$this->description',":'';
		$sql.=isset($this->sn)?"'$this->sn',":'';
		$sql.=isset($this->spec)?"'$this->spec',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
		$sql.=isset($this->ispush)?"'$this->ispush',":'';
		$sql.=isset($this->sellingPrice)?"'$this->sellingPrice',":'';
		$sql.=isset($this->preferPrice)?"'$this->preferPrice',":'';
		$sql.=isset($this->content)?"'$this->content',":'';
		$sql.=isset($this->originalPic)?"'$this->originalPic',":'';
		$sql.=isset($this->middlePic)?"'$this->middlePic',":'';
		$sql.=isset($this->smallPic)?"'$this->smallPic',":'';
		$sql.=isset($this->indexPic)?"'$this->indexPic',":'';
		$sql.=isset($this->categoryId)?"'$this->categoryId',":'';
		$sql.=isset($this->counts)?"'$this->counts',":'';
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
		$sql.=isset($this->keywords)?"`keywords`='$this->keywords',":'';
		$sql.=isset($this->description)?"`description`='$this->description',":'';
		$sql.=isset($this->sn)?"`sn`='$this->sn',":'';
		$sql.=isset($this->spec)?"`spec`='$this->spec',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
		$sql.=isset($this->ispush)?"`ispush`='$this->ispush',":'';
		$sql.=isset($this->sellingPrice)?"`sellingPrice`='$this->sellingPrice',":'';
		$sql.=isset($this->preferPrice)?"`preferPrice`='$this->preferPrice',":'';
		$sql.=isset($this->content)?"`content`='$this->content',":'';
		$sql.=isset($this->originalPic)?"`originalPic`='$this->originalPic',":'';
		$sql.=isset($this->middlePic)?"`middlePic`='$this->middlePic',":'';
		$sql.=isset($this->smallPic)?"`smallPic`='$this->smallPic',":'';
		$sql.=isset($this->indexPic)?"`indexPic`='$this->indexPic',":'';
		$sql.=isset($this->categoryId)?"`categoryId`='$this->categoryId',":'';
		$sql.=isset($this->counts)?"`counts`='$this->counts',":'';
		$sql.=isset($this->ordering)?"`ordering`='$this->ordering',":'';
		$sql.=isset($this->hassplitpages)?"`hassplitpages`='$this->hassplitpages',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>