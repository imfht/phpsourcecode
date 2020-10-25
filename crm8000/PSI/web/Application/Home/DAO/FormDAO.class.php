<?php

namespace Home\DAO;

/**
 * 自定义表单 DAO
 *
 * @author 李静波
 */
class FormDAO extends PSIBaseExDAO
{

  /**
   * 表单分类列表
   */
  public function categoryList($params)
  {
    $db = $this->db;

    $sql = "select id, code, name
            from t_form_category
            order by code";
    $data = $db->query($sql);

    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"]
      ];
    }

    return $result;
  }

  /**
   * 新增表单分类
   */
  public function addFormCategory(&$params)
  {
    $db = $this->db;

    $code = $params["code"] ?? "";
    $code = strtoupper($code);
    $name = $params["name"];

    // 检查编码是否存在
    if ($code) {
      $sql = "select count(*) as cnt from t_form_category where code = '%s' ";
      $data = $db->query($sql, $code);
      $cnt = $data[0]["cnt"];
      if ($cnt) {
        return $this->bad("表单分类编码[{$code}]已经存在");
      }
    } else {
      $code = "";
    }

    // 检查分类名称是否存在
    $sql = "select count(*) as cnt from t_form_category where name = '%s' ";
    $data = $db->query($sql, $name);
    $cnt = $data[0]["cnt"];
    if ($cnt) {
      return $this->bad("表单分类[{$name}]已经存在");
    }

    $id = $this->newId();
    $sql = "insert into t_form_category (id, code, name, parent_id)
            values ('%s', '%s', '%s', null)";

    $rc = $db->execute($sql, $id, $code, $name);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["id"] = $id;
    return null;
  }

  public function getCategoryById($id)
  {
    $db = $this->db;

    $sql = "select code, name from t_form_category where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return [
        "code" => $data[0]["code"],
        "name" => $data[0]["name"]
      ];
    } else {
      return null;
    }
  }

  /**
   * 编辑表单分类
   */
  public function updateFormCategory(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $code = $params["code"] ?? "";
    $code = strtoupper($code);
    $name = $params["name"];

    $category = $this->getCategoryById($id);
    if (!$category) {
      return $this->bad("要编辑的表单分类不存在");
    }

    // 检查编码是否已经存在
    if ($code) {
      $sql = "select count(*) as cnt from t_form_category 
              where code = '%s' and id <> '%s' ";
      $data = $db->query($sql, $code, $id);
      $cnt = $data[0]["cnt"];
      if ($cnt) {
        return $this->bad("表单分类编码[{$code}]已经存在");
      }
    } else {
      $code = "";
    }

    // 检查分类名称是否已经存在
    $sql = "select count(*) as cnt from t_form_category 
            where name = '%s' and id <> '%s' ";
    $data = $db->query($sql, $name, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt) {
      return $this->bad("表单分类[{$name}]已经存在");
    }

    $sql = "update t_form_category
            set code = '%s', name = '%s'
            where id = '%s' ";
    $rc = $db->execute($sql, $code, $name, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["id"] = $id;
    return null;
  }

  /**
   * 删除表单分类
   */
  public function deleteFormCategory(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $category = $this->getCategoryById($id);
    if (!$category) {
      return $this->bad("要删除的表单分类不存在");
    }
    $name = $category["name"];

    // 检查是否有下级分类
    $sql = "select count(*) as cnt from t_form_category where parent_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("分类[{$name}]还有下级分类，不能删除");
    }

    // 检查该分类下是否有表单
    $sql = "select count(*) as cnt from t_form where category_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("分类[{$name}]中还有表单，不能删除");
    }

    // 执行删除操作
    $sql = "delete from t_form_category where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["name"] = $name;
    return null;
  }

  /**
   * 表单分类自定义字段 - 查询数据
   */
  public function queryDataForCategory($params)
  {
    $db = $this->db;

    $queryKey = $params["queryKey"] ?? "";

    $sql = "select id, code, name
            from t_form_category
            where code like '%s' or name like '%s' 
            order by code";
    $queryParams = [];
    $queryParams[] = "%{$queryKey}%";
    $queryParams[] = "%{$queryKey}%";

    $data = $db->query($sql, $queryParams);

    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"]
      ];
    }

    return $result;
  }

  public function getFormById($id)
  {
    $db = $this->db;

    $sql = "select code, name, fid, table_name from t_form where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $v = $data[0];

      return [
        "id" => $id,
        "name" => $v["name"],
        "code" => $v["code"],
        "fid" => $v["fid"],
        "tableName" => $v["table_name"]
      ];
    } else {
      return null;
    }
  }

  /**
   * 根据id查询明细表
   */
  public function getDetailFormById($id)
  {
    $db = $this->db;

    $sql = "select name, table_name, form_id from t_form_detail where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $v = $data[0];

      return [
        "id" => $id,
        "name" => $v["name"],
        "tableName" => $v["table_name"],
        "mainFormId" => $v["form_id"],
      ];
    } else {
      return null;
    }
  }

  /**
   * 某个分类下的表单列表
   */
  public function formList($params)
  {
    $db = $this->db;

    $categoryId = $params["categoryId"];

    $sql = "select id, code, name, sys_form, md_version, memo,
              table_name, fid, module_name
            from t_form 
            where category_id = '%s'
            order by code";
    $data = $db->query($sql, $categoryId);
    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "sysForm" => $v["sys_form"],
        "mdVersion" => $v["md_version"],
        "memo" => $v["memo"],
        "tableName" => $v["table_name"],
        "fid" => $v["fid"],
        "moduleName" => $v["module_name"],
      ];
    }
    return $result;
  }

  private function checkTableName($tableName)
  {
    $tableName = strtolower($tableName);

    $len = strlen($tableName);
    if ($len < 6) {
      return $this->bad("数据库表名长度不能小于6");
    }

    $c = ord($tableName{
      0});
    $isABC = ord('a') <= $c && ord('z') >= $c;
    if (!$isABC) {
      return $this->bad("数据库表名需要以字符开头");
    }

    for ($i = 1; $i < $len; $i++) {
      $c = ord($tableName{
        $i});
      $isABC = ord('a') <= $c && ord('z') >= $c;
      $isNumber = ord('0') <= $c && ord('9') >= $c;
      $isOK = $isABC || $isNumber || ord('_') == $c;
      if (!$isOK) {
        $index = $i + 1;
        return $this->bad("数据库表名的第{$index}个字符非法");
      }
    }

    // 表单需要以t_开头
    if (!(substr($tableName, 0, 2) == "t_")) {
      return $this->bad("数据库表名需要以 <span style='color:red'>t_</span> 开头");
    }

    // 表名正确
    return null;
  }

  /**
   * 表单主表标准字段
   */
  private function getFormSysCols()
  {
    $result = [];

    // id
    $result[] = [
      "caption" => "id",
      "fieldName" => "id",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "editorXtype" => "displayfield",
      "colSpan" => 1,
      "dataIndex" => "id",
      "widthInView" => 120,
      "showOrderInView" => -1000,
    ];

    // bill_status
    $result[] = [
      "caption" => "状态",
      "fieldName" => "bill_status",
      "fieldType" => "int",
      "fieldLength" => 11,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => 1,
      "sysCol" => 1,
      "isVisible" => 1,
      "editorXtype" => "displayfield",
      "colSpan" => 1,
      "dataIndex" => "f1",
      "widthInView" => 120,
      "showOrderInView" => 1,
    ];

    // ref
    $result[] = [
      "caption" => "单号",
      "fieldName" => "ref",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => 2,
      "sysCol" => 1,
      "isVisible" => 1,
      "editorXtype" => "displayfield",
      "colSpan" => 1,
      "dataIndex" => "f2",
      "widthInView" => 120,
      "showOrderInView" => 2,
    ];

    // biz_dt
    $result[] = [
      "caption" => "业务日期",
      "fieldName" => "biz_dt",
      "fieldType" => "datetime",
      "fieldLength" => 0,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 2,
      "showOrder" => 3,
      "sysCol" => 1,
      "isVisible" => 1,
      "editorXtype" => "datefield",
      "colSpan" => 1,
      "dataIndex" => "f3",
      "widthInView" => 120,
      "showOrderInView" => 3,
    ];

    // biz_user_id
    $result[] = [
      "caption" => "业务员",
      "fieldName" => "biz_user_id",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 3,
      "valueFromTableName" => "t_user",
      "valueFromColName" => "id",
      "valueFromColNameDisplay" => "name",
      "mustInput" => 2,
      "showOrder" => 100,
      "sysCol" => 1,
      "isVisible" => 1,
      "editorXtype" => "psi_userfield",
      "colSpan" => 1,
      "dataIndex" => "f4",
      "widthInView" => 120,
      "showOrderInView" => 100,
    ];

    // date_created
    $result[] = [
      "caption" => "制单时间",
      "fieldName" => "date_created",
      "fieldType" => "datetime",
      "fieldLength" => 0,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => 102,
      "sysCol" => 1,
      "isVisible" => 2,
      "editorXtype" => "displayfield",
      "colSpan" => 1,
      "dataIndex" => "f5",
      "widthInView" => 120,
      "showOrderInView" => 102,
    ];

    // input_user_id
    $result[] = [
      "caption" => "制单人",
      "fieldName" => "input_user_id",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "t_user",
      "valueFromColName" => "id",
      "valueFromColNameDisplay" => "name",
      "mustInput" => 1,
      "showOrder" => 101,
      "sysCol" => 1,
      "isVisible" => 1,
      "editorXtype" => "displayfield",
      "colSpan" => 1,
      "dataIndex" => "f6",
      "widthInView" => 120,
      "showOrderInView" => 101,
    ];

    // bill_memo
    $result[] = [
      "caption" => "备注",
      "fieldName" => "bill_memo",
      "fieldType" => "varchar",
      "fieldLength" => 1000,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => 200,
      "sysCol" => 1,
      "isVisible" => 1,
      "editorXtype" => "textfield",
      "colSpan" => 1,
      "dataIndex" => "f7",
      "widthInView" => 300,
      "showOrderInView" => 200,
    ];

    // confirm_user_id
    $result[] = [
      "caption" => "审核人",
      "fieldName" => "confirm_user_id",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "t_user",
      "valueFromColName" => "id",
      "valueFromColNameDisplay" => "name",
      "mustInput" => 1,
      "showOrder" => 103,
      "sysCol" => 1,
      "isVisible" => 1,
      "editorXtype" => "displayfield",
      "colSpan" => 1,
      "dataIndex" => "f8",
      "widthInView" => 120,
      "showOrderInView" => 103,
    ];

    // confirm_dt
    $result[] = [
      "caption" => "审核时间",
      "fieldName" => "confirm_dt",
      "fieldType" => "datetime",
      "fieldLength" => 0,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => 104,
      "sysCol" => 1,
      "isVisible" => 1,
      "editorXtype" => "displayfield",
      "colSpan" => 1,
      "dataIndex" => "f9",
      "widthInView" => 120,
      "showOrderInView" => 104,
    ];

    // data_org
    $result[] = [
      "caption" => "数据域",
      "fieldName" => "data_org",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => 1000,
      "sysCol" => 1,
      "isVisible" => 1,
      "editorXtype" => "displayfield",
      "colSpan" => 1,
      "dataIndex" => "f10",
      "widthInView" => 120,
      "showOrderInView" => 1000,
    ];

    // company_id
    $result[] = [
      "caption" => "公司id",
      "fieldName" => "company_id",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "editorXtype" => "displayfield",
      "colSpan" => 1,
      "dataIndex" => "f11",
      "widthInView" => 120,
      "showOrderInView" => -1000,
    ];

    return $result;
  }

  /**
   * 表单明细表表标准字段
   */
  private function getFormDetailSysCols($pkName)
  {
    $result = [];

    // 主键：id
    $result[] = [
      "caption" => "id",
      "fieldName" => "id",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "editorXtype" => "displayfield",
      "widthInView" => 100,
      "dataIndex" => "id",
    ];

    // 外键
    $result[] = [
      "caption" => "外键",
      "fieldName" => $pkName,
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "editorXtype" => "displayfield",
      "widthInView" => 100,
      "dataIndex" => "f1",
    ];

    // date_created
    $result[] = [
      "caption" => "创建时间",
      "fieldName" => "date_created",
      "fieldType" => "datetime",
      "fieldLength" => 0,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "editorXtype" => "displayfield",
      "widthInView" => 100,
      "dataIndex" => "f2",
    ];

    // memo
    $result[] = [
      "caption" => "备注",
      "fieldName" => "memo",
      "fieldType" => "varchar",
      "fieldLength" => 1000,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => 100,
      "sysCol" => 1,
      "isVisible" => 1,
      "editorXtype" => "textfield",
      "widthInView" => 100,
      "dataIndex" => "f3",
    ];

    // show_order
    $result[] = [
      "caption" => "显示次序",
      "fieldName" => "show_order",
      "fieldType" => "int",
      "fieldLength" => 11,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 2,
      "showOrder" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "editorXtype" => "textfield",
      "widthInView" => 100,
      "dataIndex" => "f4",
    ];

    // data_org
    $result[] = [
      "caption" => "数据域",
      "fieldName" => "data_org",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "editorXtype" => "displayfield",
      "widthInView" => 100,
      "dataIndex" => "f5",
    ];

    // company_id
    $result[] = [
      "caption" => "公司id",
      "fieldName" => "company_id",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 5,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "editorXtype" => "displayfield",
      "widthInView" => 100,
      "dataIndex" => "f6",
    ];

    return $result;
  }

  /**
   * 新增表单
   */
  public function addForm(&$params)
  {
    $db = $this->db;

    $categoryId = $params["categoryId"];
    $category = $this->getCategoryById($categoryId);
    if (!$category) {
      return $this->badParam("categoryId");
    }

    $code = $params["code"];
    $name = $params["name"];
    $tableName = strtolower($params["tableName"]);
    $moduleName = $params["moduleName"];
    $memo = $params["memo"];
    $py = $params["py"];
    $pyModuleName = $params["pyModuleName"];

    // 1. 检查数据库表名是否正确
    $rc = $this->checkTableName($tableName);
    if ($rc) {
      return $rc;
    }

    // 2. 检查数据库表是否已经存在了
    $sql = "select count(*) as cnt from t_form
            where table_name = '%s' ";
    $data = $db->query($sql, $tableName);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("表名为[{$tableName}]的表单已经存在");
    }
    // 检查数据库中是否已经存在该表了
    $dbUtilDAO = new DBUtilDAO($db);
    if ($dbUtilDAO->tableExists($tableName)) {
      return $this->bad("表[{$tableName}]已经在数据库中存在了");
    }
    // 检查明细表是否已经存在在数据库中
    $detailTableName = $tableName . "_detail";
    if ($dbUtilDAO->tableExists($detailTableName)) {
      return $this->bad("表[{$detailTableName}]已经在数据库中存在了");
    }

    // 3. 保存元数据
    // 检查编码是否重复
    if ($code) {
      $sql = "select count(*) as cnt from t_form where code = '%s' ";
      $data = $db->query($sql, $code);
      $cnt = $data[0]["cnt"];
      if ($cnt > 0) {
        return $this->bad("编码为[$code]的表单已经存在");
      }
    }
    // 检查表单名称是否重复
    $sql = "select count(*) as cnt from t_form where name = '%s' ";
    $data = $db->query($sql, $name);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("表单名称为[$name]的表单已经存在");
    }

    // 3.1 主表元数据
    $id = $this->newId();
    $fid = "fm" . date("YmdHis");
    $sql = "insert into t_form (id, code, name, category_id, sys_form, md_version, memo, table_name, fid,
              module_name)
            values ('%s', '%s', '%s', '%s', 0, 1, '%s', '%s', '%s',
              '%s') ";
    $rc = $db->execute($sql, $id, $code, $name, $categoryId, $memo, $tableName, $fid, $moduleName);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 3.2 主表各个标准字段的元数据
    $mdCols = $this->getFormSysCols();;
    foreach ($mdCols as $v) {
      $sql = "insert into t_form_cols(id, form_id, caption, db_field_name, db_field_type,
                db_field_length, db_field_decimal, show_order, col_span, value_from,
                value_from_table_name, value_from_col_name, value_from_col_name_display, must_input,
                sys_col, is_visible, note, editor_xtype, data_index,
                width_in_view, show_order_in_view)
              values ('%s', '%s', '%s', '%s', '%s',
                %d, %d, %d, %d, %d,
                '%s', '%s', '%s', %d,
                %d, %d, '%s', '%s', '%s',
                %d, %d
                )";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $id,
        $v["caption"],
        $v["fieldName"],
        $v["fieldType"],
        $v["fieldLength"],
        $v["fieldDecimal"],
        $v["showOrder"],
        $v["colSpan"],
        $v["valueFrom"],
        $v["valueFromTableName"],
        $v["valueFromColName"],
        $v["valueFromColNameDisplay"],
        $v["mustInput"],
        $v["sysCol"],
        $v["isVisible"],
        "",
        $v["editorXtype"],
        $v["dataIndex"],
        $v["widthInView"],
        $v["showOrderInView"]
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 3.3 明细表元数据
    $detailId = $this->newId();
    $pkName = "bill_id";
    $sql = "insert into t_form_detail(id, form_id, name, table_name, fk_name, show_order)
            values ('%s', '%s', '%s', '%s', '%s', 1)";
    $rc = $db->execute($sql, $detailId, $id, "明细", "{$tableName}_detail", $pkName);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 3.4 明细表各个标准字段的元数据
    $mdCols = $this->getFormDetailSysCols($pkName);
    foreach ($mdCols as $v) {
      $sql = "insert into t_form_detail_cols(id, detail_id, caption, db_field_name, db_field_type,
                db_field_length, db_field_decimal, show_order, width_in_view,
                value_from, value_from_table_name, value_from_col_name, value_from_col_name_display,
                must_input, sys_col, is_visible, note, editor_xtype, data_index)
              values ('%s', '%s', '%s', '%s', '%s',
                %d, %d, %d, %d,
                %d, '%s', '%s', '%s',
                %d, %d, %d, '%s', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $detailId,
        $v["caption"],
        $v["fieldName"],
        $v["fieldType"],
        $v["fieldLength"],
        $v["fieldDecimal"],
        $v["showOrder"],
        $v["widthInView"],
        $v["valueFrom"],
        $v["valueFromTableName"],
        $v["valueFromColName"],
        $v["valueFromColNameDisplay"],
        $v["mustInput"],
        $v["sysCol"],
        $v["isVisible"],
        "",
        $v["editorXtype"],
        $v["dataIndex"]
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // fid: t_fid_plus
    $sql = "insert into t_fid_plus (fid, name, py, memo) values ('%s', '%s', '%s', '')";
    $rc = $db->execute($sql, $fid, $moduleName, $pyModuleName);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 权限: t_permission_plus
    $sql = "insert into t_permission_plus (id, fid, name, note, category, py, show_order)
            values ('%s', '%s', '%s', '%s', '%s','%s', %d)";
    $rc = $db->execute(
      $sql,
      $fid,
      $fid,
      $moduleName,
      "模块权限：通过菜单进入{$moduleName}模块的权限",
      $moduleName,
      "",
      100
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // TODO 权限细化到按钮


    // 4. 创建数据库表
    // 主表
    $sql = "CREATE TABLE IF NOT EXISTS $tableName (
              `id` varchar(255) NOT NULL,
              `ref` varchar(255) NOT NULL,
              `bill_status` int(11) NOT NULL,
              `biz_dt` datetime NOT NULL,
              `biz_user_id` varchar(255) NOT NULL,
              `date_created` datetime DEFAULT NULL,
              `input_user_id` varchar(255) NOT NULL,
              `confirm_user_id` varchar(255) DEFAULT NULL,
              `confirm_dt` datetime DEFAULT NULL,
              `bill_memo` varchar(255) DEFAULT NULL,
              `data_org` varchar(255) DEFAULT NULL,
              `company_id` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ";
    $rc = $db->execute($sql);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细表
    $sql = "CREATE TABLE IF NOT EXISTS {$tableName}_detail (
              `id` varchar(255) NOT NULL,
              `date_created` datetime DEFAULT NULL,
              `{$pkName}` varchar(255) NOT NULL,
              `show_order` int(11) NOT NULL,
              `data_org` varchar(255) DEFAULT NULL,
              `company_id` varchar(255) DEFAULT NULL,
              `memo` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ";
    $rc = $db->execute($sql);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["id"] = $id;
    return null;
  }

  public function updateForm(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $categoryId = $params["categoryId"];
    $category = $this->getCategoryById($categoryId);
    if (!$category) {
      return $this->badParam("categoryId");
    }

    $code = $params["code"];
    $name = $params["name"];
    $moduleName = $params["moduleName"];
    $memo = $params["memo"];
    $pyModuleName = $params["pyModuleName"];

    $form = $this->getFormById($id);
    if (!$form) {
      return $this->bad("要编辑的表单不存在");
    }
    $fid = $form["fid"];

    // 检查编码是否存在
    if ($code) {
      $sql = "select count(*) as cnt 
              from t_form 
              where code = '%s' and id <> '%s' ";
      $data = $db->query($sql, $code, $id);
      $cnt = $data[0]["cnt"];
      if ($cnt > 0) {
        return $this->bad("编码为[$code]的表单已经存在");
      }
    }
    // 检查表单名称是否重复
    $sql = "select count(*) as cnt 
            from t_form 
            where name = '%s' and id <> '%s' ";
    $data = $db->query($sql, $name, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("名称为[$name]的表单已经存在");
    }

    // 编辑主表元数据
    $sql = "update t_form
            set category_id = '%s', code = '%s', name = '%s',
              memo = '%s', md_version = md_version + 1,
              module_name = '%s' 
            where id = '%s' ";
    $rc = $db->execute($sql, $categoryId, $code, $name, $memo, $moduleName, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 更改fid中的模块名称
    $sql = "update t_fid_plus
            set name = '%s', py = '%s'
            where fid = '%s' ";
    $rc = $db->execute($sql, $moduleName, $pyModuleName, $fid);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 更改权限中的模块名称
    $sql = "update t_permission_plus
            set category = '%s', name = '%s', note = '%s'
            where fid = '%s' ";
    $rc = $db->execute($sql, $moduleName, $moduleName, "模块权限：通过菜单进入{$moduleName}模块的权限", $fid);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }
    // todo：按钮权限中的模块名称

    // 操作成功
    return null;
  }

  private function valueFromCodeToName($valueFrom)
  {
    switch ($valueFrom) {
      case 1:
        return "直接录入";
      case 2:
        return "引用系统数据字典";
      case 3:
        return "引用码表";
      case 5:
        return "自动生成";
      default:
        return "";
    }
  }

  /**
   * 表单主表列
   */
  public function formColList($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select id, caption, db_field_name, db_field_type,
              db_field_length, db_field_decimal, show_order,
              col_span, value_from, value_from_table_name,
              value_from_col_name, value_from_col_name_display,
              must_input, sys_col, is_visible, note, editor_xtype,
              data_index, width_in_view, show_order_in_view
            from t_form_cols
            where form_id = '%s' 
            order by show_order";
    $data = $db->query($sql, $id);
    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "caption" => $v["caption"],
        "fieldName" => $v["db_field_name"],
        "fieldType" => $v["db_field_type"],
        "fieldLength" => $v["db_field_length"],
        "fieldDecimal" => $v["db_field_decimal"],
        "valueFrom" => $this->valueFromCodeToName($v["value_from"]),
        "valueFromTableName" => $v["value_from_table_name"],
        "valueFromColName" => $v["value_from_col_name"],
        "valueFromColNameDisplay" => $v["value_from_col_name_display"],
        "sysCol" => $v["sys_col"] == 1 ? "系统列" : "",
        "isVisible" => $v["is_visible"] == 1 ? "可见" : "不可见",
        "note" => $v["note"],
        "showOrderInView" => $v["show_order_in_view"],
        "editorXtype" => $v["editor_xtype"],
        "mustInput" => $v["must_input"] == 2 ? "必录项" : "",
        "colSpan" => $v["col_span"],
        "showOrder" => $v["show_order"],
        "dataIndex" => $v["data_index"],
        "widthInView" => $v["width_in_view"],
        "showOrderInView" => $v["show_order_in_view"],
      ];
    }

    return $result;
  }

  /**
   * 表单明细表列表
   */
  public function formDetailList($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select id, name, table_name, fk_name, show_order
            from t_form_detail
            where form_id = '%s'
            order by show_order ";
    $data = $db->query($sql, $id);
    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "name" => $v["name"],
        "tableName" => $v["table_name"],
        "fkName" => $v["fk_name"],
        "showOrder" => $v["show_order"]
      ];
    }

    return $result;
  }

  /**
   * 表单明细表的列的列表
   */
  public function formDetailColList($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select id, caption, db_field_name, db_field_type,
              db_field_length, db_field_decimal, show_order, width_in_view,
              value_from, value_from_table_name, value_from_col_name, 
              value_from_col_name_display, must_input, sys_col, is_visible,
              note, editor_xtype, data_index
            from t_form_detail_cols
            where detail_id = '%s' 
            order by show_order";
    $data = $db->query($sql, $id);

    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "caption" => $v["caption"],
        "fieldName" => $v["db_field_name"],
        "fieldType" => $v["db_field_type"],
        "fieldLength" => $v["db_field_length"],
        "fieldDecimal" => $v["db_field_decimal"],
        "valueFrom" => $this->valueFromCodeToName($v["value_from"]),
        "valueFromTableName" => $v["value_from_table_name"],
        "valueFromColName" => $v["value_from_col_name"],
        "valueFromColNameDisplay" => $v["value_from_col_name_display"],
        "mustInput" => $v["must_input"] == 2 ? "必录项" : "",
        "isVisible" => $v["is_visible"] == 1 ? "可见" : "不可见",
        "showOrder" => $v["show_order"],
        "note" => $v["note"],
        "editorXtype" => $v["editor_xtype"],
        "widthInView" => $v["width_in_view"],
        "sysCol" => $v["sys_col"] == 1 ? "系统列" : "",
        "dataIndex" => $v["data_index"],
      ];
    }

    return $result;
  }

  /**
   * 删除表单元数据
   */
  public function deleteForm(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $form = $this->getFormById($id);
    if (!$form) {
      return $this->bad("要删除的表单不存在");
    }
    $name = $form["name"];
    $fid = $form["fid"];

    // 检查fid是否在菜单中使用了
    $sql = "select count(*) as cnt from t_menu_item where fid = '%s' ";
    $data = $db->query($sql, $fid);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("当前表单已经挂接在主菜单中了<br/>在菜单项没有从主菜单中删除之前，表单也不能删除");
    }
    $sql = "select count(*) as cnt from t_menu_item_plus where fid = '%s' ";
    $data = $db->query($sql, $fid);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("当前表单已经挂接在主菜单中了<br/>在菜单项没有从主菜单中删除之前，表单也不能删除");
    }

    // 删除明细表的列
    $sql = "select id from t_form_detail where form_id = '%s' ";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $detailId = $v["id"];
      $sql = "delete from t_form_detail_cols where detail_id = '%s' ";
      $rc = $db->execute($sql, $detailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 删除明细表
    $sql = "delete from t_form_detail where form_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 删除主表列
    $sql = "delete from t_form_cols where form_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 删除主表
    $sql = "delete from t_form where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // fid
    $sql = "delete from t_fid_plus where fid = '%s' ";
    $rc = $db->execute($sql, $fid);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 权限
    $sql = "delete from t_permission_plus where fid = '%s' ";
    $rc = $db->execute($sql, $fid);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 删除按钮权限
    $sql = "delete from t_permission_plus where parent_fid = '%s' ";
    $rc = $db->execute($sql, $fid);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["name"] = $name;
    return null;
  }

  /**
   * 获得表单主表元数据
   */
  public function formInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select f.code, f.name, f.memo, f.table_name, 
              f.category_id, c.name as category_name, f.module_name
            from t_form f, t_form_category c
            where f.category_id = c.id and f.id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $v = $data[0];
      return [
        "code" => $v["code"],
        "name" => $v["name"],
        "memo" => $v["memo"],
        "tableName" => $v["table_name"],
        "categoryName" => $v["category_name"],
        "categoryId" => $v["category_id"],
        "moduleName" => $v["module_name"],
      ];
    } else {
      return $this->emptyResult();
    }
  }

  /**
   * 返回表单主表列的信息
   */
  public function formColInfo($params)
  {
    $db = $this->db;

    $result = [];

    $sql = "select name, memo from t_sysdict_form_editor_xtype order by code";
    $data = $db->query($sql);
    $xtype = [];
    foreach ($data as $v) {
      $xtype[] = [
        "id" => $v["name"],
        "text" => $v["name"] . " - " . $v["memo"]
      ];
    }

    $result["editorXtype"] = $xtype;

    $id = $params["id"];
    if ($id) {
      // 编辑
      $sql = "select caption, db_field_name, db_field_type,
                db_field_length, db_field_decimal, show_order,
                col_span, value_from, value_from_table_name,
                value_from_col_name, value_from_col_name_display,
                must_input, sys_col, is_visible, note, editor_xtype,
                data_index, width_in_view, show_order_in_view
              from t_form_cols
              where id = '%s' ";
      $data = $db->query($sql, $id);
      if ($data) {
        $v = $data[0];
        $result["caption"] = $v["caption"];
        $result["fieldName"] = $v["db_field_name"];
        $result["fieldType"] = $v["db_field_type"];
        $result["fieldLength"] = $v["db_field_length"];
        $result["fieldDec"] = $v["db_field_decimal"];
        $result["colSpan"] = $v["col_span"];
        $result["valueFrom"] = $v["value_from"];
        $result["valueFromTableName"] = $v["value_from_table_name"];
        $result["valueFromColName"] = $v["value_from_col_name"];
        $result["valueFromColNameDisplay"] = $v["value_from_col_name_display"];
        $result["mustInput"] = $v["must_input"];
        $result["isVisible"] = $v["is_visible"];
        $result["memo"] = $v["note"];
        $result["editorXtypeValue"] = $v["editor_xtype"];
        $result["dataIndex"] = $v["data_index"];
        $result["widthInView"] = $v["width_in_view"];
        $result["showOrder"] = $v["show_order"];
        $result["showOrderInView"] = $v["show_order_in_view"];
      }
    }

    return $result;
  }

  /**
   * 检查字段名是否合法
   */
  private function checkFieldName($fieldName)
  {
    $c = ord($fieldName{
      0});
    $isABC = ord('a') <= $c && ord('z') >= $c;
    if (!$isABC) {
      return $this->bad("数据库字段名需要以字符开头");
    }

    $len = strlen($fieldName);
    for ($i = 1; $i < $len; $i++) {
      $c = ord($fieldName{
        $i});
      $isABC = ord('a') <= $c && ord('z') >= $c;
      $isNumber = ord('0') <= $c && ord('9') >= $c;
      $isOK = $isABC || $isNumber || ord('_') == $c;
      if (!$isOK) {
        $index = $i + 1;
        return $this->bad("数据库字段名的第{$index}个字符非法");
      }
    }

    // 字段名正确
    return null;
  }


  /**
   * 新增表单主表列
   */
  public function addFormCol(&$params)
  {
    $db = $this->db;

    $formId = $params["formId"];
    $form = $this->getFormById($formId);
    if (!$form) {
      return $this->bad("表单不存在");
    }
    $formName = $form["name"];
    $tableName = $form["tableName"];

    $caption = $params["caption"];
    $fieldName = $params["fieldName"];
    $fieldType = $params["fieldType"];
    $fieldLength = $params["fieldLength"];
    $fieldDecimal = $params["fieldDecimal"];
    $valueFrom = $params["valueFrom"];
    $valueFromTableName = $params["valueFromTableName"];
    $valueFromColName = $params["valueFromColName"];
    $valueFromColNameDisplay = $params["valueFromColNameDisplay"];
    $isVisible = $params["isVisible"];
    $mustInput = $params["mustInput"];
    $showOrder = $params["showOrder"];
    $editorXtype = $params["editorXtype"];
    $colSpan = $params["colSpan"];
    $widthInView = $params["widthInView"];
    $showOrderInView = $params["showOrderInView"];
    $memo = $params["memo"];

    // 检查字段名是否合法
    $rc = $this->checkFieldName($fieldName);
    if ($rc) {
      return $rc;
    }

    // 检查数据库中码表是否已经存在该字段了
    $dbUtilDAO = new DBUtilDAO($db);
    if ($dbUtilDAO->columnExists($tableName, $fieldName)) {
      return $this->bad("在表[{$tableName}]中已经存在字段[{$fieldName}]了");
    }

    // 检查字段类型
    if ($fieldType == "varchar") {
      $fieldLength = intval($fieldLength);
      if ($fieldLength <= 0) {
        $fieldLength = 255;
      }
    } else if ($fieldType == "int") {
      $fieldLength = 11;
    } else if ($fieldType == "decimal") {
      $fieldLength = 19;
      $fieldDecimal = intval($fieldDecimal);
      if ($fieldDecimal < 0) {
        $fieldDecimal = 0;
      }
      if ($fieldDecimal > 8) {
        return $this->bad("字段类型[decimal]的小数位数不能超过8位");
      }
    } else {
      return $this->bad("字段类型[{$fieldType}]目前还不支持");
    }

    // 检查字段值来源
    if ($valueFrom < 1 || $valueFrom > 5) {
      return $this->bad("字段值来源没有正确设置");
    }

    if ($valueFrom == 2) {
      // 引用系统数据字典
      // 检查系统数据字典是否存在
      $sql = "select count(*) as cnt from t_dict_table_md where table_name = '%s' ";
      $data = $db->query($sql, $valueFromTableName);
      $cnt = $data[0]["cnt"];
      if ($cnt == 0) {
        return $this->bad("系统数据字典[{$valueFromTableName}]的元数据不存在");
      }
      if (!$dbUtilDAO->tableExists($valueFromTableName)) {
        return $this->bad("系统数据字典[{$valueFromTableName}]在数据库中不存在");
      }
      if (!$dbUtilDAO->columnExists($valueFromTableName, $valueFromColName)) {
        return $this->bad("系统数据字典[{$valueFromTableName}]中不存在列[{$valueFromColName}]");
      }
      if (!$valueFromColNameDisplay) {
        // 没有设置显示字段，就默认取关联字段
        $valueFromColNameDisplay = $valueFromColName;
      } else {
        if (!$dbUtilDAO->columnExists($valueFromTableName, $valueFromColNameDisplay)) {
          return $this->bad("系统数据字典[{$valueFromTableName}]中不存在列[{$valueFromColNameDisplay}]");
        }
      }
    }
    if ($valueFrom == 3) {
      // 引用码表

      // 检查码表是否存在
      $sql = "select id from t_code_table_md where table_name = '%s' ";
      $data = $db->query($sql, $valueFromTableName);
      if (!$data) {
        return $this->bad("码表[{$valueFromTableName}]的元数据不存在");
      }
      $valueFromTableId = $data[0]["id"];
      $sql = "select count(*) as cnt from t_code_table_cols_md where table_id = '%s' and db_field_name = '%s' ";
      $data = $db->query($sql, $valueFromTableId, $valueFromColName);
      $cnt = $data[0]["cnt"];
      if ($cnt == 0) {
        return $this->bad("码表[$valueFromTableName]的列[{$valueFromColName}]的元数据不存在");
      }
      if (!$dbUtilDAO->tableExists($valueFromTableName)) {
        return $this->bad("码表[{$valueFromTableName}]在数据库中不存在");
      }
      if (!$dbUtilDAO->columnExists($valueFromTableName, $valueFromColName)) {
        return $this->bad("码表[{$valueFromTableName}]中不存在列[{$valueFromColName}]");
      }
      if (!$valueFromColNameDisplay) {
        // 没有设置显示字段，就默认取关联字段
        $valueFromColNameDisplay = $valueFromColName;
      } else {
        if (!$dbUtilDAO->columnExists($valueFromTableName, $valueFromColNameDisplay)) {
          return $this->bad("码表[{$valueFromTableName}]中不存在列[{$valueFromColNameDisplay}]");
        }
      }
    }

    // 生成data_index
    $sql = "select count(*) as cnt from t_form_cols where form_id = '%s' ";
    $data = $db->query($sql, $formId);
    $cnt = $data[0]["cnt"];
    $dataIndex = "f{$cnt}";
    // 检查dataIndex是否存在
    while (true) {
      // 因为删除字段的时候，会使dataIndex 断号
      $sql = "select * from t_form_cols where form_id = '%s' and data_index = '%ss' ";
      $data = $db->query($sql, $formId, $dataIndex);
      if ($data) {
        // dataIndex存在，流水号加一后继续查找
        $cnt++;
        $dataIndex = "f{$cnt}";
      } else {
        // dataIndex不存在了，跳出无限循环
        break;
      }
    }

    // 写入字段元数据
    $id = $this->newId();
    $sql = "insert into t_form_cols(id, form_id, caption, 
              db_field_name, db_field_type, db_field_length, db_field_decimal,
              show_order, value_from, value_from_table_name, value_from_col_name,
              must_input, sys_col, is_visible, width_in_view, note, 
              col_span, editor_xtype, value_from_col_name_display, data_index,
              show_order_in_view)
            values ('%s', '%s', '%s',
              '%s', '%s', %d, %d,
              %d, %d, '%s', '%s',
              %d, %d, %d, %d, '%s',
              %d, '%s', '%s', '%s',
              %d)";
    $rc = $db->execute(
      $sql,
      $id,
      $formId,
      $caption,
      $fieldName,
      $fieldType,
      $fieldLength,
      $fieldDecimal,
      $showOrder,
      $valueFrom,
      $valueFromTableName,
      $valueFromColName,
      $mustInput,
      2,
      $isVisible,
      $widthInView,
      $memo,
      $colSpan,
      $editorXtype,
      $valueFromColNameDisplay,
      $dataIndex,
      $showOrderInView
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 在数据库表中创建字段
    $sql = "alter table {$tableName} add {$fieldName} ";
    if ($fieldType == "varchar") {
      $sql .= " varchar({$fieldLength}) ";
    } else if ($fieldType == "int") {
      $sql .= " int(11) ";
    } else if ($fieldType = "decimal") {
      $sql .= " decimal({$fieldLength}, {$fieldDecimal}) ";
    } else {
      return $this->bad("字段类型[{$fieldType}]还不支持");
    }
    $sql .= " default null;";
    $rc = $db->execute($sql);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    //操作成功
    $params["log"] = "新增表单[{$formName}]主表列 ：{$caption}";
    $params["id"] = $id;
    return null;
  }

  /**
   * 编辑表单主表列
   */
  public function updateFormCol(&$params)
  {
    $db = $this->db;
    $formId = $params["formId"];
    $id = $params["id"];

    $form = $this->getFormById($formId);
    if (!$form) {
      return $this->bad("表单不存在");
    }
    $formName = $form["name"];

    $sql = "select sys_col, caption
            from t_form_cols
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要编辑的主表列不存在");
    }
    $sysCol = $data[0]["sys_col"];

    $caption = $params["caption"];
    $valueFrom = $params["valueFrom"];
    $valueFromTableName = $params["valueFromTableName"];
    $valueFromColName = $params["valueFromColName"];
    $valueFromColNameDisplay = $params["valueFromColNameDisplay"];
    $isVisible = $params["isVisible"];
    $mustInput = $params["mustInput"];
    $showOrder = $params["showOrder"];
    $editorXtype = $params["editorXtype"];
    $colSpan = $params["colSpan"];
    $widthInView = $params["widthInView"];
    $showOrderInView = $params["showOrderInView"];
    $memo = $params["memo"];

    if ($sysCol == 1) {
      // 系统字段
      $sql = "update t_form_cols
              set caption = '%s', value_from = '%s', value_from_table_name = '%s', value_from_col_name = '%s',
                value_from_col_name_display = '%s', is_visible = %d, must_input = %d, show_order = %d,
                col_span = %d, width_in_view = %d, show_order_in_view = %d, note = '%s'
              where id = '%s' ";
      $rc = $db->execute(
        $sql,
        $caption,
        $valueFrom,
        $valueFromTableName,
        $valueFromColName,
        $valueFromColNameDisplay,
        $isVisible,
        $mustInput,
        $showOrder,
        $colSpan,
        $widthInView,
        $showOrderInView,
        $memo,
        $id
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      // 用户自定义字段
      $sql = "update t_form_cols
              set caption = '%s', value_from = '%s', value_from_table_name = '%s', value_from_col_name = '%s',
                value_from_col_name_display = '%s', is_visible = %d, must_input = %d, show_order = %d,
                col_span = %d, width_in_view = %d, show_order_in_view = %d, note = '%s',
                editor_xtype = '%s'
              where id = '%s' ";
      $rc = $db->execute(
        $sql,
        $caption,
        $valueFrom,
        $valueFromTableName,
        $valueFromColName,
        $valueFromColNameDisplay,
        $isVisible,
        $mustInput,
        $showOrder,
        $colSpan,
        $widthInView,
        $showOrderInView,
        $memo,
        $editorXtype,
        $id
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    $log = "编辑表单[{$formName}]主表列[{$caption}]的元数据";
    $params["log"] = $log;
    return null;
  }

  /**
   * 删除表单主表列元数据
   */
  public function deleteFormCol(&$params)
  {
    $db = $this->db;
    $formId = $params["formId"];
    $id = $params["id"];

    $form = $this->getFormById($formId);
    if (!$form) {
      return $this->bad("表单不存在");
    }
    $formName = $form["name"];

    $sql = "select sys_col, caption
            from t_form_cols
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要删除的主表列不存在");
    }
    $sysCol = $data[0]["sys_col"];
    $caption = $data[0]["caption"];
    if ($sysCol == 1) {
      // 系统列不能删除
      return $this->bad("主表列[{$caption}]是系统字段，不能删除");
    }

    $sql = "delete from t_form_cols where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $log = "删除表单[{$formName}]主表列[{$caption}]的元数据";
    $params["log"] = $log;
    return null;
  }

  /**
   * 返回表单明细列的信息
   */
  public function formDetailColInfo($params)
  {
    $db = $this->db;

    $result = [];

    $sql = "select name, memo from t_sysdict_form_editor_xtype order by code";
    $data = $db->query($sql);
    $xtype = [];
    foreach ($data as $v) {
      $xtype[] = [
        "id" => $v["name"],
        "text" => $v["name"] . " - " . $v["memo"]
      ];
    }

    $result["editorXtype"] = $xtype;

    $id = $params["id"];
    if ($id) {
      // 编辑
      $sql = "select caption, db_field_name, db_field_type,
                db_field_length, db_field_decimal, show_order,
                value_from, value_from_table_name,
                value_from_col_name, value_from_col_name_display,
                must_input, sys_col, is_visible, note, editor_xtype,
                data_index, width_in_view
              from t_form_detail_cols
              where id = '%s' ";
      $data = $db->query($sql, $id);
      if ($data) {
        $v = $data[0];
        $result["caption"] = $v["caption"];
        $result["fieldName"] = $v["db_field_name"];
        $result["fieldType"] = $v["db_field_type"];
        $result["fieldLength"] = $v["db_field_length"];
        $result["fieldDec"] = $v["db_field_decimal"];
        $result["valueFrom"] = $v["value_from"];
        $result["valueFromTableName"] = $v["value_from_table_name"];
        $result["valueFromColName"] = $v["value_from_col_name"];
        $result["valueFromColNameDisplay"] = $v["value_from_col_name_display"];
        $result["mustInput"] = $v["must_input"];
        $result["isVisible"] = $v["is_visible"];
        $result["memo"] = $v["note"];
        $result["editorXtypeValue"] = $v["editor_xtype"];
        $result["dataIndex"] = $v["data_index"];
        $result["widthInView"] = $v["width_in_view"];
        $result["showOrder"] = $v["show_order"];
      }
    }

    return $result;
  }

  /**
   * 新增明细表列
   */
  public function addFormDetailCol(&$params)
  {
    $db = $this->db;

    $formId = $params["formId"];
    $form = $this->getDetailFormById($formId);
    if (!$form) {
      return $this->bad("明细表不存在");
    }
    $formName = $form["name"];
    $tableName = $form["tableName"];
    $mainFormId = $form["mainFormId"];
    $mainForm = $this->getFormById($mainFormId);
    if (!$mainForm) {
      return $this->bad("表单不存在");
    }
    $mainFormName = $mainForm["name"];

    $caption = $params["caption"];
    $fieldName = $params["fieldName"];
    $fieldType = $params["fieldType"];
    $fieldLength = $params["fieldLength"];
    $fieldDecimal = $params["fieldDecimal"];
    $valueFrom = $params["valueFrom"];
    $valueFromTableName = $params["valueFromTableName"];
    $valueFromColName = $params["valueFromColName"];
    $valueFromColNameDisplay = $params["valueFromColNameDisplay"];
    $isVisible = $params["isVisible"];
    $mustInput = $params["mustInput"];
    $showOrder = $params["showOrder"];
    $editorXtype = $params["editorXtype"];
    $widthInView = $params["widthInView"];
    $memo = $params["memo"];

    // 检查字段名是否合法
    $rc = $this->checkFieldName($fieldName);
    if ($rc) {
      return $rc;
    }

    // 检查数据库中码表是否已经存在该字段了
    $dbUtilDAO = new DBUtilDAO($db);
    if ($dbUtilDAO->columnExists($tableName, $fieldName)) {
      return $this->bad("在表[{$tableName}]中已经存在字段[{$fieldName}]了");
    }

    // 检查字段类型
    if ($fieldType == "varchar") {
      $fieldLength = intval($fieldLength);
      if ($fieldLength <= 0) {
        $fieldLength = 255;
      }
    } else if ($fieldType == "int") {
      $fieldLength = 11;
    } else if ($fieldType == "decimal") {
      $fieldLength = 19;
      $fieldDecimal = intval($fieldDecimal);
      if ($fieldDecimal < 0) {
        $fieldDecimal = 0;
      }
      if ($fieldDecimal > 8) {
        return $this->bad("字段类型[decimal]的小数位数不能超过8位");
      }
    } else {
      return $this->bad("字段类型[{$fieldType}]目前还不支持");
    }

    // 检查字段值来源
    if ($valueFrom < 1 || $valueFrom > 5) {
      return $this->bad("字段值来源没有正确设置");
    }

    if ($valueFrom == 2) {
      // 引用系统数据字典
      // 检查系统数据字典是否存在
      $sql = "select count(*) as cnt from t_dict_table_md where table_name = '%s' ";
      $data = $db->query($sql, $valueFromTableName);
      $cnt = $data[0]["cnt"];
      if ($cnt == 0) {
        return $this->bad("系统数据字典[{$valueFromTableName}]的元数据不存在");
      }
      if (!$dbUtilDAO->tableExists($valueFromTableName)) {
        return $this->bad("系统数据字典[{$valueFromTableName}]在数据库中不存在");
      }
      if (!$dbUtilDAO->columnExists($valueFromTableName, $valueFromColName)) {
        return $this->bad("系统数据字典[{$valueFromTableName}]中不存在列[{$valueFromColName}]");
      }
      if (!$valueFromColNameDisplay) {
        // 没有设置显示字段，就默认取关联字段
        $valueFromColNameDisplay = $valueFromColName;
      } else {
        if (!$dbUtilDAO->columnExists($valueFromTableName, $valueFromColNameDisplay)) {
          return $this->bad("系统数据字典[{$valueFromTableName}]中不存在列[{$valueFromColNameDisplay}]");
        }
      }
    }
    if ($valueFrom == 3) {
      // 引用码表

      // 检查码表是否存在
      $sql = "select id from t_code_table_md where table_name = '%s' ";
      $data = $db->query($sql, $valueFromTableName);
      if (!$data) {
        return $this->bad("码表[{$valueFromTableName}]的元数据不存在");
      }
      $valueFromTableId = $data[0]["id"];
      $sql = "select count(*) as cnt from t_code_table_cols_md where table_id = '%s' and db_field_name = '%s' ";
      $data = $db->query($sql, $valueFromTableId, $valueFromColName);
      $cnt = $data[0]["cnt"];
      if ($cnt == 0) {
        return $this->bad("码表[$valueFromTableName]的列[{$valueFromColName}]的元数据不存在");
      }
      if (!$dbUtilDAO->tableExists($valueFromTableName)) {
        return $this->bad("码表[{$valueFromTableName}]在数据库中不存在");
      }
      if (!$dbUtilDAO->columnExists($valueFromTableName, $valueFromColName)) {
        return $this->bad("码表[{$valueFromTableName}]中不存在列[{$valueFromColName}]");
      }
      if (!$valueFromColNameDisplay) {
        // 没有设置显示字段，就默认取关联字段
        $valueFromColNameDisplay = $valueFromColName;
      } else {
        if (!$dbUtilDAO->columnExists($valueFromTableName, $valueFromColNameDisplay)) {
          return $this->bad("码表[{$valueFromTableName}]中不存在列[{$valueFromColNameDisplay}]");
        }
      }
    }

    // 生成data_index
    $sql = "select count(*) as cnt from t_form_detail_cols where detail_id = '%s' ";
    $data = $db->query($sql, $formId);
    $cnt = $data[0]["cnt"];
    $dataIndex = "f{$cnt}";
    // 检查dataIndex是否存在
    while (true) {
      // 因为删除字段的时候，会使dataIndex 断号
      $sql = "select * from t_form_detail_cols where detail_id = '%s' and data_index = '%ss' ";
      $data = $db->query($sql, $formId, $dataIndex);
      if ($data) {
        // dataIndex存在，流水号加一后继续查找
        $cnt++;
        $dataIndex = "f{$cnt}";
      } else {
        // dataIndex不存在了，跳出无限循环
        break;
      }
    }

    // 写入字段元数据
    $id = $this->newId();
    $sql = "insert into t_form_detail_cols(id, detail_id, caption, 
              db_field_name, db_field_type, db_field_length, db_field_decimal,
              show_order, value_from, value_from_table_name, value_from_col_name,
              must_input, sys_col, is_visible, width_in_view, note, 
              editor_xtype, value_from_col_name_display, data_index)
            values ('%s', '%s', '%s',
              '%s', '%s', %d, %d,
              %d, %d, '%s', '%s',
              %d, %d, %d, %d, '%s',
              '%s', '%s', '%s')";
    $rc = $db->execute(
      $sql,
      $id,
      $formId,
      $caption,
      $fieldName,
      $fieldType,
      $fieldLength,
      $fieldDecimal,
      $showOrder,
      $valueFrom,
      $valueFromTableName,
      $valueFromColName,
      $mustInput,
      2,
      $isVisible,
      $widthInView,
      $memo,
      $editorXtype,
      $valueFromColNameDisplay,
      $dataIndex
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 在数据库表中创建字段
    $sql = "alter table {$tableName} add {$fieldName} ";
    if ($fieldType == "varchar") {
      $sql .= " varchar({$fieldLength}) ";
    } else if ($fieldType == "int") {
      $sql .= " int(11) ";
    } else if ($fieldType = "decimal") {
      $sql .= " decimal({$fieldLength}, {$fieldDecimal}) ";
    } else {
      return $this->bad("字段类型[{$fieldType}]还不支持");
    }
    $sql .= " default null;";
    $rc = $db->execute($sql);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    //操作成功
    $params["log"] = "新增表单[{$mainFormName}]明细单[{$formName}]列 ：{$caption}";
    $params["id"] = $id;
    return null;
  }

  /**
   * 编辑明细表列
   */
  public function updateFormDetailCol(&$params)
  {
    $db = $this->db;

    $formId = $params["formId"];
    $id = $params["id"];

    $form = $this->getDetailFormById($formId);
    if (!$form) {
      return $this->bad("明细表不存在");
    }
    $formName = $form["name"];
    $mainFormId = $form["mainFormId"];
    $mainForm = $this->getFormById($mainFormId);
    if (!$mainForm) {
      return $this->bad("表单不存在");
    }
    $mainFormName = $mainForm["name"];

    $sql = "select sys_col, caption
            from t_form_detail_cols
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要编辑的明细表列不存在");
    }
    $sysCol = $data[0]["sys_col"];

    $caption = $params["caption"];
    $valueFrom = $params["valueFrom"];
    $valueFromTableName = $params["valueFromTableName"];
    $valueFromColName = $params["valueFromColName"];
    $valueFromColNameDisplay = $params["valueFromColNameDisplay"];
    $isVisible = $params["isVisible"];
    $mustInput = $params["mustInput"];
    $showOrder = $params["showOrder"];
    $editorXtype = $params["editorXtype"];
    $widthInView = $params["widthInView"];
    $memo = $params["memo"];

    if ($sysCol == 1) {
      // 系统字段
      $sql = "update t_form_detail_cols
              set caption = '%s', value_from = '%s', value_from_table_name = '%s', value_from_col_name = '%s',
                value_from_col_name_display = '%s', is_visible = %d, must_input = %d, show_order = %d,
                width_in_view = %d, note = '%s'
              where id = '%s' ";
      $rc = $db->execute(
        $sql,
        $caption,
        $valueFrom,
        $valueFromTableName,
        $valueFromColName,
        $valueFromColNameDisplay,
        $isVisible,
        $mustInput,
        $showOrder,
        $widthInView,
        $memo,
        $id
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      // 用户自定义字段
      $sql = "update t_form_detail_cols
              set caption = '%s', value_from = '%s', value_from_table_name = '%s', value_from_col_name = '%s',
                value_from_col_name_display = '%s', is_visible = %d, must_input = %d, show_order = %d,
                width_in_view = %d, note = '%s',
                editor_xtype = '%s'
              where id = '%s' ";
      $rc = $db->execute(
        $sql,
        $caption,
        $valueFrom,
        $valueFromTableName,
        $valueFromColName,
        $valueFromColNameDisplay,
        $isVisible,
        $mustInput,
        $showOrder,
        $widthInView,
        $memo,
        $editorXtype,
        $id
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    $log = "编辑表单[{$mainFormName}]明细表[{$formName}]列[{$caption}]的元数据";
    $params["log"] = $log;
    return null;
  }

  /**
   * 删除表单明细表列元数据
   */
  public function deleteFormDetailCol(&$params)
  {
    $db = $this->db;
    $formId = $params["formId"];
    $id = $params["id"];

    $form = $this->getDetailFormById($formId);
    if (!$form) {
      return $this->bad("表单不存在");
    }
    $formName = $form["name"];
    $mainFormId = $form["mainFormId"];
    $mainForm = $this->getFormById($mainFormId);
    if (!$mainForm) {
      return $this->bad("表单不存在");
    }
    $mainFormName = $mainForm["name"];

    $sql = "select sys_col, caption
            from t_form_detail_cols
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要删除的明细表列不存在");
    }
    $sysCol = $data[0]["sys_col"];
    $caption = $data[0]["caption"];
    if ($sysCol == 1) {
      // 系统列不能删除
      return $this->bad("明细表列[{$caption}]是系统字段，不能删除");
    }

    $sql = "delete from t_form_detail_cols where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $log = "删除表单[{$mainFormName}]明细表[{$formName}]列[{$caption}]的元数据";
    $params["log"] = $log;
    return null;
  }
}
