<?php
class c_menu extends DtDatabase
{
	public $id;
	public $menuName;
	public $title;
	public $keywords;
	public $description;
	public $type;
	public $ordering;
	public $deep;
	public $parentId;
	public $isComment;
	public $level;
	public $isHidden;
	public $isTarget;
	public $originalPic;
	public $smallPic;
	public $width;
	public $hight;
	public $isExternalLinks;
	public $redirectUrl;
	public $related_common;
	public $dtLanguage;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'menu';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['menuName'])$this->menuName=$request['menuName'];
		$this->title=$request['title'];
		if($request['keywords'])$this->keywords=$request['keywords'];
		$this->description=$request['description'];
		if($request['type'])$this->type=$request['type'];
		if($request['ordering'])$this->ordering=$request['ordering'];
		if($request['deep'])$this->deep=$request['deep'];
		if($request['parentId'])$this->parentId=$request['parentId'];
		if($request['isComment'])$this->isComment=$request['isComment'];
		if($request['level'])$this->level=$request['level'];
		if($request['isHidden'])$this->isHidden=$request['isHidden'];
		if($request['isTarget'])$this->isTarget=$request['isTarget'];
		if($request['originalPic'])$this->originalPic=$request['originalPic'];
		if($request['smallPic'])$this->smallPic=$request['smallPic'];
		if($request['width'])$this->width=$request['width'];
		if($request['hight'])$this->hight=$request['hight'];
		if($request['isExternalLinks'])$this->isExternalLinks=$request['isExternalLinks'];
		if($request['redirectUrl'])$this->redirectUrl=$request['redirectUrl'];
		if($request['related_common'])$this->related_common=$request['related_common'];
		if($request['dtLanguage'])$this->dtLanguage=$request['dtLanguage'];
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
		$sql.=isset($this->menuName)?"menuName,":'';
		$sql.=isset($this->title)?"title,":'';
		$sql.=isset($this->keywords)?"keywords,":'';
		$sql.=isset($this->description)?"description,":'';
		$sql.=isset($this->type)?"type,":'';
		$sql.=isset($this->ordering)?"ordering,":'';
		$sql.=isset($this->deep)?"deep,":'';
		$sql.=isset($this->parentId)?"parentId,":'';
		$sql.=isset($this->isComment)?"isComment,":'';
		$sql.=isset($this->level)?"level,":'';
		$sql.=isset($this->isHidden)?"isHidden,":'';
		$sql.=isset($this->isTarget)?"isTarget,":'';
		$sql.=isset($this->originalPic)?"originalPic,":'';
		$sql.=isset($this->smallPic)?"smallPic,":'';
		$sql.=isset($this->width)?"width,":'';
		$sql.=isset($this->hight)?"hight,":'';
		$sql.=isset($this->isExternalLinks)?"isExternalLinks,":'';
		$sql.=isset($this->redirectUrl)?"redirectUrl,":'';
		$sql.=isset($this->related_common)?"related_common,":'';
		$sql.=isset($this->dtLanguage)?"dtLanguage,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->menuName)?"'$this->menuName',":'';
		$sql.=isset($this->title)?"'$this->title',":'';
		$sql.=isset($this->keywords)?"'$this->keywords',":'';
		$sql.=isset($this->description)?"'$this->description',":'';
		$sql.=isset($this->type)?"'$this->type',":'';
		$sql.=isset($this->ordering)?"'$this->ordering',":'';
		$sql.=isset($this->deep)?"'$this->deep',":'';
		$sql.=isset($this->parentId)?"'$this->parentId',":'';
		$sql.=isset($this->isComment)?"'$this->isComment',":'';
		$sql.=isset($this->level)?"'$this->level',":'';
		$sql.=isset($this->isHidden)?"'$this->isHidden',":'';
		$sql.=isset($this->isTarget)?"'$this->isTarget',":'';
		$sql.=isset($this->originalPic)?"'$this->originalPic',":'';
		$sql.=isset($this->smallPic)?"'$this->smallPic',":'';
		$sql.=isset($this->width)?"'$this->width',":'';
		$sql.=isset($this->hight)?"'$this->hight',":'';
		$sql.=isset($this->isExternalLinks)?"'$this->isExternalLinks',":'';
		$sql.=isset($this->redirectUrl)?"'$this->redirectUrl',":'';
		$sql.=isset($this->related_common)?"'$this->related_common',":'';
		$sql.=isset($this->dtLanguage)?"'$this->dtLanguage',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->menuName)?"`menuName`='$this->menuName',":'';
		$sql.=isset($this->title)?"`title`='$this->title',":'';
		$sql.=isset($this->keywords)?"`keywords`='$this->keywords',":'';
		$sql.=isset($this->description)?"`description`='$this->description',":'';
		$sql.=isset($this->type)?"`type`='$this->type',":'';
		$sql.=isset($this->ordering)?"`ordering`='$this->ordering',":'';
		$sql.=isset($this->deep)?"`deep`='$this->deep',":'';
		$sql.=isset($this->parentId)?"`parentId`='$this->parentId',":'';
		$sql.=isset($this->isComment)?"`isComment`='$this->isComment',":'';
		$sql.=isset($this->level)?"`level`='$this->level',":'';
		$sql.=isset($this->isHidden)?"`isHidden`='$this->isHidden',":'';
		$sql.=isset($this->isTarget)?"`isTarget`='$this->isTarget',":'';
		$sql.=isset($this->originalPic)?"`originalPic`='$this->originalPic',":'';
		$sql.=isset($this->smallPic)?"`smallPic`='$this->smallPic',":'';
		$sql.=isset($this->width)?"`width`='$this->width',":'';
		$sql.=isset($this->hight)?"`hight`='$this->hight',":'';
		$sql.=isset($this->isExternalLinks)?"`isExternalLinks`='$this->isExternalLinks',":'';
		$sql.=isset($this->redirectUrl)?"`redirectUrl`='$this->redirectUrl',":'';
		$sql.=isset($this->related_common)?"`related_common`='$this->related_common',":'';
		$sql.=isset($this->dtLanguage)?"`dtLanguage`='$this->dtLanguage',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>