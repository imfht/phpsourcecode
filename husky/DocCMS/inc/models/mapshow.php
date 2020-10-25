<?php
class c_mapshow extends DtDatabase
{
	public $id;
	public $channelId;
	public $title;
	public $keywords;
	public $description;
	public $mapKey;
	public $lat;
	public $lng;
	public $width;
	public $height;
	public $phone;
	public $address;
	public $content;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'mapshow';
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
		if($request['mapKey'])$this->mapKey=$request['mapKey'];
		if($request['lat'])$this->lat=$request['lat'];
		if($request['lng'])$this->lng=$request['lng'];
		if($request['width'])$this->width=$request['width'];
		if($request['height'])$this->height=$request['height'];
		if($request['phone'])$this->phone=$request['phone'];
		if($request['address'])$this->address=$request['address'];
		$this->content=$request['content'];
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
		$sql.=isset($this->mapKey)?"mapKey,":'';
		$sql.=isset($this->lat)?"lat,":'';
		$sql.=isset($this->lng)?"lng,":'';
		$sql.=isset($this->width)?"width,":'';
		$sql.=isset($this->height)?"height,":'';
		$sql.=isset($this->phone)?"phone,":'';
		$sql.=isset($this->address)?"address,":'';
		$sql.=isset($this->content)?"content,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->title)?"'$this->title',":'';
		$sql.=isset($this->keywords)?"'$this->keywords',":'';
		$sql.=isset($this->description)?"'$this->description',":'';
		$sql.=isset($this->mapKey)?"'$this->mapKey',":'';
		$sql.=isset($this->lat)?"'$this->lat',":'';
		$sql.=isset($this->lng)?"'$this->lng',":'';
		$sql.=isset($this->width)?"'$this->width',":'';
		$sql.=isset($this->height)?"'$this->height',":'';
		$sql.=isset($this->phone)?"'$this->phone',":'';
		$sql.=isset($this->address)?"'$this->address',":'';
		$sql.=isset($this->content)?"'$this->content',":'';
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
		$sql.=isset($this->mapKey)?"`mapKey`='$this->mapKey',":'';
		$sql.=isset($this->lat)?"`lat`='$this->lat',":'';
		$sql.=isset($this->lng)?"`lng`='$this->lng',":'';
		$sql.=isset($this->width)?"`width`='$this->width',":'';
		$sql.=isset($this->height)?"`height`='$this->height',":'';
		$sql.=isset($this->phone)?"`phone`='$this->phone',":'';
		$sql.=isset($this->address)?"`address`='$this->address',":'';
		$sql.=isset($this->content)?"`content`='$this->content',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>