<?php
/*
 * 后台系统配置管理类
 *
 */	
class HelpDoc extends Action{	
	
	private $cacheDir='';//缓存目录
	private $auth;
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
	}	

	public function help_doc(){
		$type=$this->_REQUEST('type');
		$html='';
		switch($type){
			case 'cst_customer':
				$html .='<p>1、私有客户只能被隶属人及其上级用户看到，同级之间数据隔离，公共客户所有人都能看到;</p>
						 <p>2、公共客户只能被隶属人及其上级用户编辑删除，其他用户只能查看;</p>
						 <p>3、客户名称和电话不能同时重复，导入时系统会自动判断;</p>';	
				break;
			case 'cst_chance':
				$html .='<p>1、销售机会主要管理销售过程中的售前部分，或者说从一个销售机会的确立到签署合同之间的过程管理。</p>
						 <p>2、销售机会的跟踪是一个延续性的工作，在这期间很多数据会不断地变化;</p>
						 <p>3、比如阶段、可能性、状态等，只有及时地更新这些数据，系统才能正常的发挥作用，比如销售漏斗、销售预测等。</p>';	
				break;
			case 'sal_contract':
				$html .='	<p>1、合同状态分为<font color="#FAD733">临时单</font>、<font color="#23B7E5">执行中</font>、<font color="#27C24C">完成</font>、<font color="#F05050">撤销</font>，只有在临时单状态下才能进行编辑、删除操作。</p>
							<p>2、付款状态分为<font color="#FAD733">未付</font>、<font color="#23B7E5">部分</font>、<font color="#27C24C">已付</font>，可在财务管理-》汇款管理-》回款记录中进行新增和删除。</p>
							<p>3、交付状态分为<font color="#FAD733">需要</font>、<font color="#23B7E5">部分</font>、<font color="#27C24C">全部</font>，先生成交付计划，再进行交付，直到累计交付金额等于合同金额，交付状态为全部。</p>
							<p>4、开票状态分为<font color="#FAD733">需要</font>、<font color="#23B7E5">部分</font>、<font color="#27C24C">全部</font>，可在财务管理-》回款管理-》开票记录中进行新增和删除。</p>
							<p>5、如要撤销单据，必须先删除对应的收款记录、交付记录，开票记录，单据状态重新回到临时单状态，方可进行修改、删除。</p>
						 ';	
				break;
			case 'pos_contract':
				$html .='	<p>1、采购单分为<font color="#FAD733">临时单</font>、<font color="#23B7E5">执行中</font>、<font color="#27C24C">完成</font>、<font color="#F05050">撤销</font>，只有在临时单状态下才能进行编辑、删除操作。</p>
							<p>2、入库状态分为：<font color="#FAD733">需要</font>、<font color="#7266BA">已录明细</font>、<font color="#F05050">待入库</font>、<font color="#23B7E5">部分</font>、<font color="#27C24C">全部</font>，录入明细的总额要跟单据总额一致，然后生成入库单，库管确认入库。</p>
							<p>3、付款状态分为<font color="#FAD733">未付</font>、<font color="#23B7E5">部分</font>、<font color="#27C24C">已付</font>，可在财务管理-》付款管理-》付款记录中进行新增和删除。</p>
							<p>4、收票状态分为<font color="#FAD733">需要</font>、<font color="#23B7E5">部分</font>、<font color="#27C24C">全部</font>，可在财务管理-》付款管理-》收票记录中进行新增和删除。</p>
							<p>5、如要撤销入库单，必须先撤销入库记录。</p>
						 ';	
				break;
			case 'stock_goods_sku':
				$html .='	<p>1、已存在库存的仓库不能进行初始化，初始化操作只能进行一次。</p>
							<p>2、只有此仓库的管理员有权限进行初始化（仓库管理员在仓库管理功能中设置）</p>
							<p>3、初始化操作后将自动产生一个入库单，状态为已入库</p>
							<p>4、要撤销初始化操作，可删除其产生的入库单即可。</p>
						 ';	
				break;
			case 'stock_into':
				$html .='	<p>1、入库单由采购单生成，不能单独新建入库单，采购单生成入库单后，入库单据状态为未入库。</p>
							<p>2、库管确认，只有此仓库的管理员有权限确认（仓库管理员在仓库管理功能中设置），确认后单据状态为已入库，至此完成入库。</p>
							<p><font color="#F05050">流程：创建采购合同 -> 添加采购明细 -> 生成入库单 -> 入库单确认</font></p>
						 ';	
				break;			
			case 'stock_out':
				$html .='	<p>1、出库单由销售订单生成，不能单独新建出库单，订单生成出库单后，出库单据状态为未出库。</p>
							<p>2、库管确认，只有此仓库的管理员有权限确认（仓库管理员在仓库管理功能中设置），确认后单据状态为已出库，等待订单创建人发货。</p>
							<p>3、如果库管本人就是订单创建人，可以直接发货，发货完毕后，状态为已发货，出库流程结束。</p>
							<p><font color="#F05050">操作流程：创建销售合同 -> 录入销售明细 -> 生成出库单 -> 出库单确认</font></p>
						 ';	
				break;			
			case 'goods_sku'://商品sku
				$html .='	<p>1、库存数=库存清单所有仓库中的总合</p>
							<p>2、成本价=成本金额/库存数 </p>
							<p>3、总成本=库存清单所有仓库中的成本金额总合</p>
						 ';	
				break;
			default:
			
		}
		echo $html;
	}
   



}//end class
?>