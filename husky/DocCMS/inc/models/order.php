<?php
class c_order extends DtDatabase
{
	public $id;
	public $title;
	public $custom;
	public $remark;
	public $handling;
	public $result;
	public $ispay;
	public $channelId;
	public $payprice;
	public $orderId;
	public $customer;
	public $dtTime;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'order';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		$this->title=$request['title'];
		if($request['custom'])$this->custom=$request['custom'];
		if($request['remark'])$this->remark=$request['remark'];
		if($request['handling'])$this->handling=$request['handling'];
		if($request['result'])$this->result=$request['result'];
		if($request['ispay'])$this->ispay=$request['ispay'];
		if($request['channelId'])$this->channelId=$request['channelId'];
		if($request['payprice'])$this->payprice=$request['payprice'];
		if($request['orderId'])$this->orderId=$request['orderId'];
		if($request['customer'])$this->customer=$request['customer'];
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
		$sql.=isset($this->custom)?"custom,":'';
		$sql.=isset($this->remark)?"remark,":'';
		$sql.=isset($this->handling)?"handling,":'';
		$sql.=isset($this->result)?"result,":'';
		$sql.=isset($this->ispay)?"ispay,":'';
		$sql.=isset($this->channelId)?"channelId,":'';
		$sql.=isset($this->payprice)?"payprice,":'';
		$sql.=isset($this->orderId)?"orderId,":'';
		$sql.=isset($this->customer)?"customer,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->title)?"'$this->title',":'';
		$sql.=isset($this->custom)?"'$this->custom',":'';
		$sql.=isset($this->remark)?"'$this->remark',":'';
		$sql.=isset($this->handling)?"'$this->handling',":'';
		$sql.=isset($this->result)?"'$this->result',":'';
		$sql.=isset($this->ispay)?"'$this->ispay',":'';
		$sql.=isset($this->channelId)?"'$this->channelId',":'';
		$sql.=isset($this->payprice)?"'$this->payprice',":'';
		$sql.=isset($this->orderId)?"'$this->orderId',":'';
		$sql.=isset($this->customer)?"'$this->customer',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->title)?"`title`='$this->title',":'';
		$sql.=isset($this->custom)?"`custom`='$this->custom',":'';
		$sql.=isset($this->remark)?"`remark`='$this->remark',":'';
		$sql.=isset($this->handling)?"`handling`='$this->handling',":'';
		$sql.=isset($this->result)?"`result`='$this->result',":'';
		$sql.=isset($this->ispay)?"`ispay`='$this->ispay',":'';
		$sql.=isset($this->channelId)?"`channelId`='$this->channelId',":'';
		$sql.=isset($this->payprice)?"`payprice`='$this->payprice',":'';
		$sql.=isset($this->orderId)?"`orderId`='$this->orderId',":'';
		$sql.=isset($this->customer)?"`customer`='$this->customer',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>