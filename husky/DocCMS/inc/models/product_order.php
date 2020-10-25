<?php
class c_product_order extends DtDatabase
{
	public $id;
	public $orderId;
	public $usertype;
	public $userid;
	public $ispay;
	public $m_tel;
	public $address;
	public $orederinfo;
	public $dtTime;
	public $stauts;
	public $payprice;
	public $customer;
	public $remark;

	public $primary_key='id';

	protected $table_name;
	private $im_virgin=false;

	public function __construct()
	{
		$this->table_name = TB_PREFIX.'product_order';
		$this->DtDatabase();		
}
	public function get_request($request=array())
	{
		if(!empty($request)){
		if($request['id'])$this->id=$request['id'];
		if($request['orderId'])$this->orderId=$request['orderId'];
		if($request['usertype'])$this->usertype=$request['usertype'];
		if($request['userid'])$this->userid=$request['userid'];
		if($request['ispay'])$this->ispay=$request['ispay'];
		if($request['m_tel'])$this->m_tel=$request['m_tel'];
		if($request['address'])$this->address=$request['address'];
		if($request['orederinfo'])$this->orederinfo=$request['orederinfo'];
		if($request['dtTime'])$this->dtTime=$request['dtTime'];
		if($request['stauts'])$this->stauts=$request['stauts'];
		if($request['payprice'])$this->payprice=$request['payprice'];
		if($request['customer'])$this->customer=$request['customer'];
		if($request['remark'])$this->remark=$request['remark'];
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
		$sql.=isset($this->orderId)?"orderId,":'';
		$sql.=isset($this->usertype)?"usertype,":'';
		$sql.=isset($this->userid)?"userid,":'';
		$sql.=isset($this->ispay)?"ispay,":'';
		$sql.=isset($this->m_tel)?"m_tel,":'';
		$sql.=isset($this->address)?"address,":'';
		$sql.=isset($this->orederinfo)?"orederinfo,":'';
		$sql.=isset($this->dtTime)?"dtTime,":'';
		$sql.=isset($this->stauts)?"stauts,":'';
		$sql.=isset($this->payprice)?"payprice,":'';
		$sql.=isset($this->customer)?"customer,":'';
		$sql.=isset($this->remark)?"remark,":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=")VALUES (";
		$sql.=isset($this->orderId)?"'$this->orderId',":'';
		$sql.=isset($this->usertype)?"'$this->usertype',":'';
		$sql.=isset($this->userid)?"'$this->userid',":'';
		$sql.=isset($this->ispay)?"'$this->ispay',":'';
		$sql.=isset($this->m_tel)?"'$this->m_tel',":'';
		$sql.=isset($this->address)?"'$this->address',":'';
		$sql.=isset($this->orederinfo)?"'$this->orederinfo',":'';
		$sql.=isset($this->dtTime)?"'$this->dtTime',":'';
		$sql.=isset($this->stauts)?"'$this->stauts',":'';
		$sql.=isset($this->payprice)?"'$this->payprice',":'';
		$sql.=isset($this->customer)?"'$this->customer',":'';
		$sql.=isset($this->remark)?"'$this->remark',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);		$sql.=')';

		}
		else{

		eval('$pid=$this->'.$this->primary_key.';$this->'.$this->primary_key.'=NULL;');

		$sql.="UPDATE `$this->table_name` SET ";
		$sql.=isset($this->id)?"`id`='$this->id',":'';
		$sql.=isset($this->orderId)?"`orderId`='$this->orderId',":'';
		$sql.=isset($this->usertype)?"`usertype`='$this->usertype',":'';
		$sql.=isset($this->userid)?"`userid`='$this->userid',":'';
		$sql.=isset($this->ispay)?"`ispay`='$this->ispay',":'';
		$sql.=isset($this->m_tel)?"`m_tel`='$this->m_tel',":'';
		$sql.=isset($this->address)?"`address`='$this->address',":'';
		$sql.=isset($this->orederinfo)?"`orederinfo`='$this->orederinfo',":'';
		$sql.=isset($this->dtTime)?"`dtTime`='$this->dtTime',":'';
		$sql.=isset($this->stauts)?"`stauts`='$this->stauts',":'';
		$sql.=isset($this->payprice)?"`payprice`='$this->payprice',":'';
		$sql.=isset($this->customer)?"`customer`='$this->customer',":'';
		$sql.=isset($this->remark)?"`remark`='$this->remark',":'';
if(substr($sql,strlen($str)-1,1)==',')$sql=substr($sql,0,strlen($str)-1);
		$sql.=" WHERE `$this->primary_key` ='$pid' LIMIT 1";
		}
		return $this->query($sql);
	}
}
?>