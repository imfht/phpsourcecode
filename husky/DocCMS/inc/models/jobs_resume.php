<?php
class c_jobs_resume extends DtDatabase
{
	public $id;
	public $parentId;
	public $channelId;
	public $name;
	public $sex;
	public $birthday;
	public $nation;
	public $isMarried;
	public $nowJob;
	public $nowAddress;
	public $residence;
	public $educational;
	public $height;
	public $finishSchool;
	public $finishTime;
	public $speciality;
	public $experience;
	public $selfAppreciation;
	public $languageSkill;
	public $email;
	public $telphone;
	public $mobile;
	public $address;
	public $resume;
	public $dtTime;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'jobs_resume';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['parentId'])$this->parentId=$request['parentId'];
		if($request['channelId'])$this->channelId=$request['channelId'];
		if($request['name'])$this->name=$request['name'];
		if($request['sex'])$this->sex=$request['sex'];
		if($request['birthday'])$this->birthday=$request['birthday'];
		if($request['nation'])$this->nation=$request['nation'];
		if($request['isMarried'])$this->isMarried=$request['isMarried'];
		if($request['nowJob'])$this->nowJob=$request['nowJob'];
		if($request['nowAddress'])$this->nowAddress=$request['nowAddress'];
		if($request['residence'])$this->residence=$request['residence'];
		if($request['educational'])$this->educational=$request['educational'];
		if($request['height'])$this->height=$request['height'];
		if($request['finishSchool'])$this->finishSchool=$request['finishSchool'];
		if($request['finishTime'])$this->finishTime=$request['finishTime'];
		if($request['speciality'])$this->speciality=$request['speciality'];
		if($request['experience'])$this->experience=$request['experience'];
		if($request['selfAppreciation'])$this->selfAppreciation=$request['selfAppreciation'];
		if($request['languageSkill'])$this->languageSkill=$request['languageSkill'];
		if($request['email'])$this->email=$request['email'];
		if($request['telphone'])$this->telphone=$request['telphone'];
		if($request['mobile'])$this->mobile=$request['mobile'];
		if($request['address'])$this->address=$request['address'];
		if($request['resume'])$this->resume=$request['resume'];
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
		$sql.=isset($this->parentId)?"parentId,":'';
		$sql.=isset($this->channelId)?"channelId,":'';
		$sql.=isset($this->name)?"name,":'';
		$sql.=isset($this->sex)?"sex,":'';
		$sql.=isset($this->birthday)?"birthday,":'';
		$sql.=isset($this->nation)?"nation,":'';
		$sql.=isset($this->isMarried)?"isMarried,":'';
		$sql.=isset($this->nowJob)?"nowJob,":'';
		$sql.=isset($this->nowAddress)?"nowAddress,":'';
		$sql.=isset($this->residence)?"residence,":'';
		$sql.=isset($this->educational)?"educational,":'';
		$sql.=isset($this->height)?"height,":'';
		$sql.=isset($this->finishSchool)?"finishSchool,":'';
		$sql.=isset($this->finishTime)?"finishTime,":'';
		$sql.=isset($this->speciality)?"speciality,":'';
		$sql.=isset($this->experience)?"experience,":'';
		$sql.=isset($this->selfAppreciation)?"selfAppreciation,":'';
		$sql.=isset($this->languageSkill)?"languageSkill,":'';
		$sql.=isset($this->email)?"email,":'';
		$sql.=isset($this->telphone)?"telphone,":'';
		$sql.=isset($this->mobile)?"mobile,":'';
		$sql.=isset($this->address)?"address,":'';
		$sql.=isset($this->resume)?"resume,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->parentId)?"'$this->parentId',":'';
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->name)?"'$this->name',":'';
		$sql.=isset($this->sex)?"'$this->sex',":'';
		$sql.=isset($this->birthday)?"'$this->birthday',":'';
		$sql.=isset($this->nation)?"'$this->nation',":'';
		$sql.=isset($this->isMarried)?"'$this->isMarried',":'';
		$sql.=isset($this->nowJob)?"'$this->nowJob',":'';
		$sql.=isset($this->nowAddress)?"'$this->nowAddress',":'';
		$sql.=isset($this->residence)?"'$this->residence',":'';
		$sql.=isset($this->educational)?"'$this->educational',":'';
		$sql.=isset($this->height)?"'$this->height',":'';
		$sql.=isset($this->finishSchool)?"'$this->finishSchool',":'';
		$sql.=isset($this->finishTime)?"'$this->finishTime',":'';
		$sql.=isset($this->speciality)?"'$this->speciality',":'';
		$sql.=isset($this->experience)?"'$this->experience',":'';
		$sql.=isset($this->selfAppreciation)?"'$this->selfAppreciation',":'';
		$sql.=isset($this->languageSkill)?"'$this->languageSkill',":'';
		$sql.=isset($this->email)?"'$this->email',":'';
		$sql.=isset($this->telphone)?"'$this->telphone',":'';
		$sql.=isset($this->mobile)?"'$this->mobile',":'';
		$sql.=isset($this->address)?"'$this->address',":'';
		$sql.=isset($this->resume)?"'$this->resume',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->parentId)?"`parentId`='$this->parentId',":'';
		$sql.=isset($this->channelId)?"`channelId`='$this->channelId',":'';
		$sql.=isset($this->name)?"`name`='$this->name',":'';
		$sql.=isset($this->sex)?"`sex`='$this->sex',":'';
		$sql.=isset($this->birthday)?"`birthday`='$this->birthday',":'';
		$sql.=isset($this->nation)?"`nation`='$this->nation',":'';
		$sql.=isset($this->isMarried)?"`isMarried`='$this->isMarried',":'';
		$sql.=isset($this->nowJob)?"`nowJob`='$this->nowJob',":'';
		$sql.=isset($this->nowAddress)?"`nowAddress`='$this->nowAddress',":'';
		$sql.=isset($this->residence)?"`residence`='$this->residence',":'';
		$sql.=isset($this->educational)?"`educational`='$this->educational',":'';
		$sql.=isset($this->height)?"`height`='$this->height',":'';
		$sql.=isset($this->finishSchool)?"`finishSchool`='$this->finishSchool',":'';
		$sql.=isset($this->finishTime)?"`finishTime`='$this->finishTime',":'';
		$sql.=isset($this->speciality)?"`speciality`='$this->speciality',":'';
		$sql.=isset($this->experience)?"`experience`='$this->experience',":'';
		$sql.=isset($this->selfAppreciation)?"`selfAppreciation`='$this->selfAppreciation',":'';
		$sql.=isset($this->languageSkill)?"`languageSkill`='$this->languageSkill',":'';
		$sql.=isset($this->email)?"`email`='$this->email',":'';
		$sql.=isset($this->telphone)?"`telphone`='$this->telphone',":'';
		$sql.=isset($this->mobile)?"`mobile`='$this->mobile',":'';
		$sql.=isset($this->address)?"`address`='$this->address',":'';
		$sql.=isset($this->resume)?"`resume`='$this->resume',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>