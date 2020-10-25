<?php

namespace Home\Common;

/**
 * FId常数值
 *
 * @author 李静波
 */
class FIdConst
{

  /**
   * 自定义表单系统
   */
  const FORM_SYSTEM = "-7999";

  /**
   * 表单视图
   *
   * @var string
   */
  const FORM_VIEW_SYSTEM = "-7998";

  /**
   * 视图开发助手
   *
   * @var string
   */
  const FORM_VIEW_SYSTEM_DEV = "-7997";

  /**
   * 码表设置
   *
   * @var string
   */
  const CODE_TABLE = "-7996";

  /**
   * 主菜单维护
   *
   * @var string
   */
  const MAIN_MENU = "-7995";

  /**
   * 系统数据字典
   *
   * @var string
   */
  const SYS_DICT = "-7994";
  /**
   * 首页
   */
  const HOME = "-9997";

  /**
   * 重新登录
   */
  const RELOGIN = "-9999";

  /**
   * 修改我的密码
   */
  const CHANGE_MY_PASSWORD = "-9996";

  /**
   * 使用帮助
   */
  const HELP = "-9995";

  /**
   * 关于
   */
  const ABOUT = "-9994";

  /**
   * 购买商业服务
   */
  const PSI_SERVICE = "-9993";

  /**
   * 用户管理
   */
  const USR_MANAGEMENT = "-8999";

  /**
   * 用户管理 - 新增组织机构
   */
  const USER_MANAGEMENT_ADD_ORG = "-8999-03";

  /**
   * 用户管理 - 编辑组织机构
   */
  const USER_MANAGEMENT_EDIT_ORG = "-8999-04";

  /**
   * 用户管理 - 删除组织机构
   */
  const USER_MANAGEMENT_DELETE_ORG = "-8999-05";

  /**
   * 用户管理 - 新增用户
   */
  const USER_MANAGEMENT_ADD_USER = "-8999-06";

  /**
   * 用户管理 - 编辑用户
   */
  const USER_MANAGEMENT_EDIT_USER = "-8999-07";

  /**
   * 用户管理 - 删除用户
   */
  const USER_MANAGEMENT_DELETE_USER = "-8999-08";

  /**
   * 用户管理 - 修改用户密码
   */
  const USER_MANAGEMENT_CHANGE_USER_PASSWORD = "-8999-09";

  /**
   * 权限管理
   */
  const PERMISSION_MANAGEMENT = "-8996";

  /**
   * 权限管理 - 新增角色 - 按钮权限
   */
  const PERMISSION_MANAGEMENT_ADD = "-8996-01";

  /**
   * 权限管理 - 编辑角色 - 按钮权限
   */
  const PERMISSION_MANAGEMENT_EDIT = "-8996-02";

  /**
   * 权限管理 - 删除角色 - 按钮权限
   */
  const PERMISSION_MANAGEMENT_DELETE = "-8996-03";

  /**
   * 业务日志
   */
  const BIZ_LOG = "-8997";

  /**
   * 基础数据-仓库
   */
  const WAREHOUSE = "1003";

  /**
   * 仓库在业务单据中的使用权限
   */
  const WAREHOUSE_BILL = "1003-01";

  /**
   * 新增仓库
   */
  const WAREHOUSE_ADD = "1003-02";

  /**
   * 编辑仓库
   */
  const WAREHOUSE_EDIT = "1003-03";

  /**
   * 删除仓库
   */
  const WAREHOUSE_DELETE = "1003-04";

  /**
   * 修改仓库数据域
   */
  const WAREHOUSE_EDIT_DATAORG = "1003-05";

  /**
   * 基础数据-供应商档案
   */
  const SUPPLIER = "1004";

  /**
   * 供应商档案在业务单据中的使用权限
   */
  const SUPPLIER_BILL = "1004-01";

  /**
   * 供应商分类
   */
  const SUPPLIER_CATEGORY = "1004-02";

  /**
   * 新增供应商分类
   */
  const SUPPLIER_CATEGORY_ADD = "1004-03";

  /**
   * 编辑供应商分类
   */
  const SUPPLIER_CATEGORY_EDIT = "1004-04";

  /**
   * 删除供应商分类
   */
  const SUPPLIER_CATEGORY_DELETE = "1004-05";

  /**
   * 新增供应商
   */
  const SUPPLIER_ADD = "1004-06";

  /**
   * 编辑供应商
   */
  const SUPPLIER_EDIT = "1004-07";

  /**
   * 删除供应商
   */
  const SUPPLIER_DELETE = "1004-08";

  /**
   * 基础数据-商品
   */
  const GOODS = "1001";

  /**
   * 商品在业务单据中的使用权限
   */
  const GOODS_BILL = "1001-01";

  /**
   * 商品分类
   */
  const GOODS_CATEGORY = "1001-02";

  /**
   * 新增商品分类
   */
  const GOODS_CATEGORY_ADD = "1001-03";

  /**
   * 编辑商品分类
   */
  const GOODS_CATEGORY_EDIT = "1001-04";

  /**
   * 删除商品分类
   */
  const GOODS_CATEGORY_DELETE = "1001-05";

  /**
   * 新增商品
   */
  const GOODS_ADD = "1001-06";

  /**
   * 编辑商品
   */
  const GOODS_EDIT = "1001-07";

  /**
   * 删除商品
   */
  const GOODS_DELETE = "1001-08";

  /**
   * 导入商品
   */
  const GOODS_IMPORT = "1001-09";

  /**
   * 设置商品安全库存
   */
  const GOODS_SI = "1001-10";

  /**
   * 导出Excel
   */
  const GOODS_EXPORT_EXCEL = "1001-11";

  /**
   * 基础数据-商品计量单位
   */
  const GOODS_UNIT = "1002";

  /**
   * 客户资料
   */
  const CUSTOMER = "1007";

  /**
   * 客户资料在业务单据中的使用权限
   */
  const CUSTOMER_BILL = "1007-01";

  /**
   * 客户分类
   */
  const CUSTOMER_CATEGORY = "1007-02";

  /**
   * 新增客户分类
   */
  const CUSTOMER_CATEGORY_ADD = "1007-03";

  /**
   * 编辑客户分类
   */
  const CUSTOMER_CATEGORY_EDIT = "1007-04";

  /**
   * 删除客户分类
   */
  const CUSTOMER_CATEGORY_DELETE = "1007-05";

  /**
   * 新增客户
   */
  const CUSTOMER_ADD = "1007-06";

  /**
   * 编辑客户
   */
  const CUSTOMER_EDIT = "1007-07";

  /**
   * 删除客户
   */
  const CUSTOMER_DELETE = "1007-08";

  /**
   * 导入客户
   */
  const CUSTOMER_IMPORT = "1007-09";

  /**
   * 库存建账
   */
  const INVENTORY_INIT = "2000";

  /**
   * 采购入库
   */
  const PURCHASE_WAREHOUSE = "2001";

  /**
   * 采购入库 - 新建采购入库单
   */
  const PURCHASE_WAREHOUSE_ADD = "2001-01";

  /**
   * 采购入库 - 编辑采购入库单
   */
  const PURCHASE_WAREHOUSE_EDIT = "2001-02";

  /**
   * 采购入库 - 删除采购入库单
   */
  const PURCHASE_WAREHOUSE_DELETE = "2001-03";

  /**
   * 采购入库 - 提交入库
   */
  const PURCHASE_WAREHOUSE_COMMIT = "2001-04";

  /**
   * 采购入库 - 单据生成PDF
   */
  const PURCHASE_WAREHOUSE_PDF = "2001-05";

  /**
   * 采购入库 - 采购单价和金额可见
   */
  const PURCHASE_WAREHOUSE_CAN_VIEW_PRICE = "2001-06";

  /**
   * 采购入库 - 打印
   */
  const PURCHASE_WAREHOUSE_PRINT = "2001-07";

  /**
   * 库存账查询
   */
  const INVENTORY_QUERY = "2003";

  /**
   * 库存账查询 - 总账导出Excel
   */
  const INVENTORY_QUERY_EXPORT_EXCEL = "2003-01";

  /**
   * 应付账款管理
   */
  const PAYABLES = "2005";

  /**
   * 应收账款管理
   */
  const RECEIVING = "2004";

  /**
   * 销售出库
   */
  const WAREHOUSING_SALE = "2002";

  /**
   * 销售出库 - 新建销售出库单
   */
  const WAREHOUSING_SALE_ADD = "2002-02";

  /**
   * 销售出库 - 编辑销售出库单
   */
  const WAREHOUSING_SALE_EDIT = "2002-03";

  /**
   * 销售出库 - 删除销售出库单
   */
  const WAREHOUSING_SALE_DELETE = "2002-04";

  /**
   * 销售出库 - 提交出库
   */
  const WAREHOUSING_SALE_COMMIT = "2002-05";

  /**
   * 销售出库 - 单据生成PDF
   */
  const WAREHOUSING_SALE_PDF = "2002-06";

  /**
   * 销售出库 - 打印
   */
  const WAREHOUSING_SALE_PRINT = "2002-07";

  /**
   * 销售退货入库
   */
  const SALE_REJECTION = "2006";

  /**
   * 销售退货入库 - 新建销售退货入库单
   */
  const SALE_REJECTION_ADD = "2006-01";

  /**
   * 销售退货入库 - 编辑销售退货入库单
   */
  const SALE_REJECTION_EDIT = "2006-02";

  /**
   * 销售退货入库 - 删除销售退货入库单
   */
  const SALE_REJECTION_DELETE = "2006-03";

  /**
   * 销售退货入库 - 提交入库
   */
  const SALE_REJECTION_COMMIT = "2006-04";

  /**
   * 销售退货入库 - 单据生成PDF
   */
  const SALE_REJECTION_PDF = "2006-05";

  /**
   * 销售退货入库 - 打印
   */
  const SALE_REJECTION_PRINT = "2006-06";

  /**
   * 业务设置
   */
  const BIZ_CONFIG = "2008";

  /**
   * 库间调拨
   */
  const INVENTORY_TRANSFER = "2009";

  /**
   * 库间调拨 - 新建调拨单
   */
  const INVENTORY_TRANSFER_ADD = "2009-01";

  /**
   * 库间调拨 - 编辑调拨单
   */
  const INVENTORY_TRANSFER_EDIT = "2009-02";

  /**
   * 库间调拨 - 删除调拨单
   */
  const INVENTORY_TRANSFER_DELETE = "2009-03";

  /**
   * 库间调拨 - 提交调拨单
   */
  const INVENTORY_TRANSFER_COMMIT = "2009-04";

  /**
   * 库间调拨 - 单据生成PDF
   */
  const INVENTORY_TRANSFER_PDF = "2009-05";

  /**
   * 库间调拨 - 打印
   */
  const INVENTORY_TRANSFER_PRINT = "2009-06";

  /**
   * 库存盘点
   */
  const INVENTORY_CHECK = "2010";

  /**
   * 库存盘点 - 新建盘点单
   */
  const INVENTORY_CHECK_ADD = "2010-01";

  /**
   * 库存盘点 - 编辑盘点单
   */
  const INVENTORY_CHECK_EDIT = "2010-02";

  /**
   * 库存盘点 - 删除盘点单
   */
  const INVENTORY_CHECK_DELETE = "2010-03";

  /**
   * 库存盘点 - 提交盘点单
   */
  const INVENTORY_CHECK_COMMIT = "2010-04";

  /**
   * 库存盘点 - 单据生成PDF
   */
  const INVENTORY_CHECK_PDF = "2010-05";

  /**
   * 库存盘点 - 打印
   */
  const INVENTORY_CHECK_PRINT = "2010-06";

  /**
   * 采购退货出库
   */
  const PURCHASE_REJECTION = "2007";

  /**
   * 采购退货出库 - 新建采购退货出库单
   */
  const PURCHASE_REJECTION_ADD = "2007-01";

  /**
   * 采购退货出库 - 编辑采购退货出库单
   */
  const PURCHASE_REJECTION_EDIT = "2007-02";

  /**
   * 采购退货出库 - 删除采购退货出库单
   */
  const PURCHASE_REJECTION_DELETE = "2007-03";

  /**
   * 采购退货出库 - 提交采购退货出库单
   */
  const PURCHASE_REJECTION_COMMIT = "2007-04";

  /**
   * 采购退货出库 - 单据生成PDF
   */
  const PURCHASE_REJECTION_PDF = "2007-05";

  /**
   * 采购退货出库 - 打印
   */
  const PURCHASE_REJECTION_PRINT = "2007-06";

  /**
   * 首页-销售看板
   */
  const PORTAL_SALE = "2011-01";

  /**
   * 首页-库存看板
   */
  const PORTAL_INVENTORY = "2011-02";

  /**
   * 首页-采购看板
   */
  const PORTAL_PURCHASE = "2011-03";

  /**
   * 首页-资金看板
   */
  const PORTAL_MONEY = "2011-04";

  /**
   * 销售日报表(按商品汇总)
   */
  const REPORT_SALE_DAY_BY_GOODS = "2012";

  /**
   * 销售日报表(按客户汇总)
   */
  const REPORT_SALE_DAY_BY_CUSTOMER = "2013";

  /**
   * 销售日报表(按仓库汇总)
   */
  const REPORT_SALE_DAY_BY_WAREHOUSE = "2014";

  /**
   * 销售日报表(按业务员汇总)
   */
  const REPORT_SALE_DAY_BY_BIZUSER = "2015";

  /**
   * 销售月报表(按商品汇总)
   */
  const REPORT_SALE_MONTH_BY_GOODS = "2016";

  /**
   * 销售月报表(按客户汇总)
   */
  const REPORT_SALE_MONTH_BY_CUSTOMER = "2017";

  /**
   * 销售月报表(按仓库汇总)
   */
  const REPORT_SALE_MONTH_BY_WAREHOUSE = "2018";

  /**
   * 销售月报表(按业务员汇总)
   */
  const REPORT_SALE_MONTH_BY_BIZUSER = "2019";

  /**
   * 安全库存明细表
   */
  const REPORT_SAFETY_INVENTORY = "2020";

  /**
   * 应收账款账龄分析表
   */
  const REPORT_RECEIVABLES_AGE = "2021";

  /**
   * 应付账款账龄分析表
   */
  const REPORT_PAYABLES_AGE = "2022";

  /**
   * 库存超上限明细表
   */
  const REPORT_INVENTORY_UPPER = "2023";

  /**
   * 现金收支查询
   */
  const CASH_INDEX = "2024";

  /**
   * 预收款管理
   */
  const PRE_RECEIVING = "2025";

  /**
   * 预付款管理
   */
  const PRE_PAYMENT = "2026";

  /**
   * 采购订单
   */
  const PURCHASE_ORDER = "2027";

  /**
   * 采购订单 - 审核
   */
  const PURCHASE_ORDER_CONFIRM = "2027-01";

  /**
   * 采购订单 - 生成采购入库单
   */
  const PURCHASE_ORDER_GEN_PWBILL = "2027-02";

  /**
   * 采购订单 - 新建采购订单
   */
  const PURCHASE_ORDER_ADD = "2027-03";

  /**
   * 采购订单 - 编辑采购订单
   */
  const PURCHASE_ORDER_EDIT = "2027-04";

  /**
   * 采购订单 - 删除采购订单
   */
  const PURCHASE_ORDER_DELETE = "2027-05";

  /**
   * 采购订单 - 关闭采购订单 / 取消采购订单关闭状态
   */
  const PURCHASE_ORDER_CLOSE = "2027-06";

  /**
   * 采购订单 - 单据生成PDF
   */
  const PURCHASE_ORDER_PDF = "2027-07";

  /**
   * 采购订单 - 打印
   */
  const PURCHASE_ORDER_PRINT = "2027-08";

  /**
   * 采购订单 - 单据生成Excel
   */
  const PURCHASE_ORDER_EXCEL = "2027-09";

  /**
   * 销售订单
   */
  const SALE_ORDER = "2028";

  /**
   * 销售订单 - 审核
   */
  const SALE_ORDER_CONFIRM = "2028-01";

  /**
   * 销售订单 - 生成销售出库单
   */
  const SALE_ORDER_GEN_WSBILL = "2028-02";

  /**
   * 销售订单 - 新建销售订单
   */
  const SALE_ORDER_ADD = "2028-03";

  /**
   * 销售订单 - 编辑销售订单
   */
  const SALE_ORDER_EDIT = "2028-04";

  /**
   * 销售订单 - 删除销售订单
   */
  const SALE_ORDER_DELETE = "2028-05";

  /**
   * 销售订单 - 单据生成PDF
   */
  const SALE_ORDER_PDF = "2028-06";

  /**
   * 销售订单 - 打印
   */
  const SALE_ORDER_PRINT = "2028-07";

  /**
   * 销售订单 - 生成采购订单
   */
  const SALE_ORDER_GEN_POBILL = "2028-08";

  /**
   * 销售订单 - 关闭订单/取消关闭订单
   */
  const SALE_ORDER_CLOSE_BILL = "2028-09";

  /**
   * 基础数据 - 商品品牌
   */
  const GOODS_BRAND = "2029";

  /**
   * 商品构成 - 新增子商品
   */
  const GOODS_BOM_ADD = "2030-01";

  /**
   * 商品构成 - 编辑子商品
   */
  const GOODS_BOM_EDIT = "2030-02";

  /**
   * 商品构成 - 删除子商品
   */
  const GOODS_BOM_DELETE = "2030-03";

  /**
   * 价格体系
   *
   * @var string
   */
  const PRICE_SYSTEM = "2031";

  /**
   * 商品模块 - 设置商品价格体系
   *
   * 按钮权限
   *
   * @var string
   */
  const PRICE_SYSTEM_SETTING_GOODS = "2031-01";

  /**
   * 销售合同
   *
   * @var string
   */
  const SALE_CONTRACT = "2032";

  /**
   * 销售合同 - 新增销售合同
   *
   * @var string
   */
  const SALE_CONTRACT_ADD = "2032-01";

  /**
   * 销售合同 - 编辑销售合同
   *
   * @var string
   */
  const SALE_CONTRACT_EDIT = "2032-02";

  /**
   * 销售合同 - 删除销售合同
   *
   * @var string
   */
  const SALE_CONTRACT_DELETE = "2032-03";

  /**
   * 销售合同 - 审核/取消审核
   *
   * @var string
   */
  const SALE_CONTRACT_COMMIT = "2032-04";

  /**
   * 销售合同 - 生成销售订单
   *
   * @var string
   */
  const SALE_CONTRACT_GEN_SOBILL = "2032-05";

  /**
   * 销售合同 - 单据生成PDF
   *
   * @var string
   */
  const SALE_CONTRACT_PDF = "2032-06";

  /**
   * 销售合同 - 打印
   *
   * @var string
   */
  const SALE_CONTRACT_PRINT = "2032-07";

  /**
   * 存货拆分
   *
   * @var string
   */
  const WSP = "2033";

  /**
   * 存货拆分 - 新建拆分单
   *
   * @var string
   */
  const WSP_ADD = "2033-01";

  /**
   * 存货拆分 - 编辑拆分单
   *
   * @var string
   */
  const WSP_EDIT = "2033-02";

  /**
   * 存货拆分 - 删除拆分单
   *
   * @var string
   */
  const WSP_DELETE = "2033-03";

  /**
   * 存货拆分 - 提交拆分单
   *
   * @var string
   */
  const WSP_COMMIT = "2033-04";

  /**
   * 存货拆分 - 单据导出PDF
   *
   * @var string
   */
  const WSP_PDF = "2033-05";

  /**
   * 存货拆分 - 打印
   *
   * @var string
   */
  const WSP_PRINT = "2033-06";

  /**
   * 工厂
   *
   * @var string
   */
  const FACTORY = "2034";

  /**
   * 工厂 - 在业务单据中的数据权限
   *
   * @var string
   */
  const FACTORY_BILL = "2034-01";

  /**
   * 工厂 - 工厂分类的数据权限
   *
   * @var string
   */
  const FACTORY_CATEGORY = "2034-02";

  /**
   * 工厂 - 新增工厂分类
   *
   * @var string
   */
  const FACTORY_CATEGORY_ADD = "2034-03";

  /**
   * 工厂 - 编辑工厂分类
   *
   * @var string
   */
  const FACTORY_CATEGORY_EDIT = "2034-04";

  /**
   * 工厂 - 删除工厂分类
   *
   * @var string
   */
  const FACTORY_CATEGORY_DELETE = "2034-05";

  /**
   * 工厂 - 新增工厂
   *
   * @var string
   */
  const FACTORY_ADD = "2034-06";

  /**
   * 工厂 - 编辑工厂
   *
   * @var string
   */
  const FACTORY_EDIT = "2034-07";

  /**
   * 工厂 - 删除工厂
   *
   * @var string
   */
  const FACTORY_DELETE = "2034-08";

  /**
   * 成品委托生产订单
   *
   * @var string
   */
  const DMO = "2035";

  /**
   * 成品委托生产订单 - 新建成品委托生产订单
   *
   * @var string
   */
  const DMO_ADD = "2035-01";

  /**
   * 成品委托生产订单 - 编辑成品委托生产订单
   *
   * @var string
   */
  const DMO_EDIT = "2035-02";

  /**
   * 成品委托生产订单 - 删除成品委托生产订单
   *
   * @var string
   */
  const DMO_DELETE = "2035-03";

  /**
   * 成品委托生产订单 - 提交成品委托生产订单
   *
   * @var string
   */
  const DMO_COMMIT = "2035-04";

  /**
   * 成品委托生产订单 - 生成成品委托生产入库单
   *
   * @var string
   */
  const DMO_GEN_DMW_BILL = "2035-05";

  /**
   * 成品委托生产订单 - 关闭成品委托生产订单
   *
   * @var string
   */
  const DMO_CLOSE_BILL = "2035-06";

  /**
   * 成品委托生产订单 - 单据生成PDF
   *
   * @var string
   */
  const DMO_PDF = "2035-07";

  /**
   * 成品委托生产订单 - 打印
   *
   * @var string
   */
  const DMO_PRINT = "2035-08";

  /**
   * 成品委托生产入库单
   *
   * @var string
   */
  const DMW = "2036";

  /**
   * 成品委托生产入库单 - 新建成品委托生产入库单
   *
   * @var string
   */
  const DMW_ADD = "2036-01";

  /**
   * 成品委托生产入库单 - 编辑成品委托生产入库单
   *
   * @var string
   */
  const DMW_EDIT = "2036-02";

  /**
   * 成品委托生产入库单 - 删除成品委托生产入库单
   *
   * @var string
   */
  const DMW_DELETE = "2036-03";

  /**
   * 成品委托生产入库单 - 提交入库
   *
   * @var string
   */
  const DMW_COMMIT = "2036-04";

  /**
   * 成品委托生产入库单 - 单据生成PDF
   *
   * @var string
   */
  const DMW_PDF = "2036-05";

  /**
   * 成品委托生产入库单 - 打印
   *
   * @var string
   */
  const DMW_PRINT = "2036-06";

  /**
   * 采购入库明细表
   * 
   * @var string
   */
  const PURCHASE_DETAIL_REPORT = "2037";

  /**
   * 销售出库明细表
   */
  const SALE_DETAIL_REPORT = "2038";

  // -------------------------
  // 财务总账系统GL fid使用2100段
  // -------------------------

  /**
   * 会计科目
   *
   * @var string
   */
  const GL_SUBJECT = "2101";

  /**
   * 银行账户
   *
   * @var string
   */
  const GL_BANK_ACCOUNT = "2102";

  /**
   * 会计期间
   *
   * @var string
   */
  const GL_PERIOD = "2103";

  // --------------------------
  // 制造相关模块使用3100段
  // --------------------------

  /**
   * 物料单位
   */
  const MATERIAL_UNIT = "3101";

  /**
   * 原材料
   */
  const RAW_MATERIAL = "3102";

  /**
   * 原材料在业务单据中的使用权限
   */
  const RAW_MATERIAL_BILL = "3102-01";

  /**
   * 原材料分类
   */
  const RAW_MATERIAL_CATEGORY = "3102-02";

  /**
   * 新增原材料分类
   */
  const RAW_MATERIAL_CATEGORY_ADD = "3102-03";

  /**
   * 编辑原材料分类
   */
  const RAW_MATERIAL_CATEGORY_EDIT = "3102-04";

  /**
   * 删除原材料分类
   */
  const RAW_MATERIAL_CATEGORY_DELETE = "3102-05";

  /**
   * 新增原材料
   */
  const RAW_MATERIAL_ADD = "3102-06";

  /**
   * 编辑原材料
   */
  const RAW_MATERIAL_EDIT = "3102-07";

  /**
   * 删除原材料
   */
  const RAW_MATERIAL_DELETE = "3102-08";
}
