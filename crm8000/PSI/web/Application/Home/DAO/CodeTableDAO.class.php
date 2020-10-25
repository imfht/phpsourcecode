<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 码表DAO
 *
 * @author 李静波
 */
class CodeTableDAO extends PSIBaseExDAO
{

  /**
   * 码表分类列表
   */
  public function categoryList($params)
  {
    $db = $this->db;

    $sql = "select id, code, name, is_system
            from t_code_table_category
            order by code";
    $data = $db->query($sql);

    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "isSystem" => $v["is_system"],
        "isSystemCaption" => $v["is_system"] == 1 ? "▲" : "",
      ];
    }

    return $result;
  }

  /**
   * 新增码表分类
   *
   * @param array $params
   * @return null|array
   */
  public function addCodeTableCategory(&$params)
  {
    $db = $this->db;

    $code = $params["code"] ?? "";
    $code = strtoupper($code);
    $name = $params["name"];

    // 检查编码是否存在
    if ($code) {
      $sql = "select count(*) as cnt from t_code_table_category where code = '%s' ";
      $data = $db->query($sql, $code);
      $cnt = $data[0]["cnt"];
      if ($cnt) {
        return $this->bad("码表分类编码[{$code}]已经存在");
      }
    } else {
      $code = "";
    }

    // 检查分类名称是否存在
    $sql = "select count(*) as cnt from t_code_table_category where name = '%s' ";
    $data = $db->query($sql, $name);
    $cnt = $data[0]["cnt"];
    if ($cnt) {
      return $this->bad("码表分类[{$name}]已经存在");
    }

    $id = $this->newId();
    $sql = "insert into t_code_table_category (id, code, name, parent_id)
            values ('%s', '%s', '%s', null)";

    $rc = $db->execute($sql, $id, $code, $name);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["id"] = $id;
    return null;
  }

  /**
   * 编辑码表分类
   *
   * @param array $params
   */
  public function updateCodeTableCategory($params)
  {
    $db = $this->db;

    $id = $params["id"];
    $code = $params["code"] ?? "";
    $code = strtoupper($code);
    $name = $params["name"];

    $category = $this->getCodeTableCategoryById($id);
    if (!$category) {
      return $this->bad("要编辑的码表分类不存在");
    }
    $isSystem = $category["isSystem"];
    if ($isSystem == 1) {
      $n = $category["name"];
      return $this->bad("分类[{$n}]是系统固有分类，不能编辑");
    }

    // 检查编码是否存在
    if ($code) {
      $sql = "select count(*) as cnt from t_code_table_category 
              where code = '%s' and id <> '%s' ";
      $data = $db->query($sql, $code, $id);
      $cnt = $data[0]["cnt"];
      if ($cnt) {
        return $this->bad("码表分类编码[{$code}]已经存在");
      }
    } else {
      $code = "";
    }

    // 检查分类名称是否存在
    $sql = "select count(*) as cnt from t_code_table_category 
            where name = '%s' and id <> '%s' ";
    $data = $db->query($sql, $name, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt) {
      return $this->bad("码表分类[{$name}]已经存在");
    }

    $sql = "update t_code_table_category
            set code = '%s', name = '%s'
            where id = '%s' ";

    $rc = $db->execute($sql, $code, $name, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  public function getCodeTableCategoryById($id)
  {
    $db = $this->db;

    $sql = "select code, name, is_system from t_code_table_category where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return [
        "code" => $data[0]["code"],
        "name" => $data[0]["name"],
        "isSystem" => $data[0]["is_system"],
      ];
    } else {
      return null;
    }
  }

  /**
   * 删除码表分类
   */
  public function deleteCodeTableCategory(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $category = $this->getCodeTableCategoryById($id);
    if (!$category) {
      return $this->bad("要删除的码表分类不存在");
    }
    $name = $category["name"];
    $isSystem = $category["isSystem"];
    if ($isSystem == 1) {
      return $this->bad("分类[{$name}]是系统固有分类，不能删除");
    }

    // 查询该分类是否被使用了
    $sql = "select count(*) as cnt from t_code_table_md
            where category_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("码表分类[$name]下还有码表，不能删除");
    }

    $sql = "delete from t_code_table_category where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["name"] = $name;
    return null;
  }

  /**
   * 码表列表
   */
  public function codeTableList($params)
  {
    $db = $this->db;

    $categoryId = $params["categoryId"];

    $sql = "select id, code, name, table_name, memo, fid, md_version, is_fixed,
              enable_parent_id, handler_class_name, module_name, edit_col_cnt,
              view_paging, auto_code_length
            from t_code_table_md
            where category_id = '%s' 
            order by code, table_name";
    $data = $db->query($sql, $categoryId);

    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "moduleName" => $v["module_name"],
        "tableName" => $v["table_name"],
        "fid" => $v["fid"],
        "memo" => $v["memo"],
        "mdVersion" => $v["md_version"],
        "isFixed" => $v["is_fixed"],
        "isFixedName" => $v["is_fixed"] == 1 ? "▲" : "",
        "viewPaging" => $v["view_paging"] == 1 ? "▲" : "",
        "enableParentId" => $v["enable_parent_id"],
        "handlerClassName" => $v["handler_class_name"],
        "editColCnt" => $v["edit_col_cnt"],
        "autoCodeLength" => $v["auto_code_length"] > 0 ? $v["auto_code_length"] : null,
      ];
    }
    return $result;
  }

  /**
   * 码表分类自定义字段 - 查询数据
   */
  public function queryDataForCategory($params)
  {
    $db = $this->db;

    $queryKey = $params["queryKey"] ?? "";

    $sql = "select id, code, name
            from t_code_table_category
            where code like '%s' or name like '%s' ";
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

    // 码表需要以t_ct开头
    if (!(substr($tableName, 0, 5) == "t_ct_")) {
      return $this->bad("数据库表名需要以 <span style='color:red'>t_ct_</span> 开头");
    }

    // 表名正确
    return null;
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
   * 返回码表的系统固有列
   *
   * @return array
   */
  private function getCodeTableSysCols()
  {
    $result = [];

    // id
    $result[] = [
      "caption" => "id",
      "fieldName" => "id",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -1000,
      "showOrderInView" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "widthInView" => 0,
      "editorXtype" => "textfield"
    ];

    // code
    $result[] = [
      "caption" => "编码",
      "fieldName" => "code",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 2,
      "showOrder" => 0,
      "showOrderInView" => 0,
      "sysCol" => 1,
      "isVisible" => 1,
      "widthInView" => 120,
      "editorXtype" => "textfield"
    ];

    // name
    $result[] = [
      "caption" => "名称",
      "fieldName" => "name",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 2,
      "showOrder" => 1,
      "showOrderInView" => 1,
      "sysCol" => 1,
      "isVisible" => 1,
      "widthInView" => 200,
      "editorXtype" => "textfield"
    ];

    // 拼音字头
    $result[] = [
      "caption" => "拼音字头",
      "fieldName" => "py",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -900,
      "showOrderInView" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "widthInView" => 0,
      "editorXtype" => "textfield"
    ];

    // 数据域data_org
    $result[] = [
      "caption" => "数据域",
      "fieldName" => "data_org",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -800,
      "showOrderInView" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "widthInView" => 0,
      "editorXtype" => "textfield"
    ];

    // company_id
    $result[] = [
      "caption" => "公司id",
      "fieldName" => "company_id",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -700,
      "showOrderInView" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "widthInView" => 0,
      "editorXtype" => "textfield"
    ];

    // 记录创建时间
    $result[] = [
      "caption" => "记录创建时间",
      "fieldName" => "date_created",
      "fieldType" => "datetime",
      "fieldLength" => 0,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -699,
      "showOrderInView" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "widthInView" => 0,
      "editorXtype" => "textfield"
    ];

    // 记录创建人id
    $result[] = [
      "caption" => "记录创建人id",
      "fieldName" => "create_user_id",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -698,
      "showOrderInView" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "widthInView" => 0,
      "editorXtype" => "textfield"
    ];

    // 记录最后一次编辑时间
    $result[] = [
      "caption" => "最后一次编辑时间",
      "fieldName" => "update_dt",
      "fieldType" => "datetime",
      "fieldLength" => 0,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -697,
      "showOrderInView" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "widthInView" => 0,
      "editorXtype" => "textfield"
    ];

    // 记录最后一次编辑人id
    $result[] = [
      "caption" => "最后一次编辑人id",
      "fieldName" => "update_user_id",
      "fieldType" => "varchar",
      "fieldLength" => 255,
      "fieldDecimal" => 0,
      "valueFrom" => 1,
      "valueFromTableName" => "",
      "valueFromColName" => "",
      "valueFromColNameDisplay" => "",
      "mustInput" => 1,
      "showOrder" => -696,
      "showOrderInView" => -1000,
      "sysCol" => 1,
      "isVisible" => 2,
      "widthInView" => 0,
      "editorXtype" => "textfield"
    ];

    // 状态
    $result[] = [
      "caption" => "状态",
      "fieldName" => "record_status",
      "fieldType" => "int",
      "fieldLength" => 11,
      "fieldDecimal" => 0,
      "valueFrom" => 2,
      "valueFromTableName" => "t_sysdict_record_status",
      "valueFromColName" => "code_int",
      "valueFromColNameDisplay" => "name",
      "mustInput" => 2,
      "showOrder" => 2,
      "showOrderInView" => 2,
      "sysCol" => 1,
      "isVisible" => 1,
      "widthInView" => 80,
      "editorXtype" => "textfield"
    ];

    return $result;
  }

  /**
   * 新增码表
   *
   * @param array $params
   * @return array|null
   */
  public function addCodeTable(&$params)
  {
    $db = $this->db;

    $categoryId = $params["categoryId"];
    if (!$this->getCodeTableCategoryById($categoryId)) {
      return $this->bad("码表分类不存在");
    }

    $code = strtoupper($params["code"] ?? "");
    $name = $params["name"];
    $moduleName = $params["moduleName"] ?? $name;
    $memo = $params["memo"] ?? "";
    $py = $params["py"];
    $tableName = strtolower($params["tableName"]);
    $enableParetnId = intval($params["enableParentId"] ?? 0);
    if ($enableParetnId != 0 && $enableParetnId != 1) {
      //$enableParentId只能取值0或1
      $enableParetnId = 0;
    }
    $handlerClassName = $params["handlerClassName"];

    $editColCnt = intval($params["editColCnt"]);
    if ($editColCnt < 1) {
      $editColCnt = 1;
    } else if ($editColCnt > 4) {
      return $this->bad("编辑布局列数不能超过4");
    }
    $autoCodeLength = intval($params["autoCodeLength"]);
    if ($autoCodeLength < 0) {
      $autoCodeLength = 0;
    }
    if ($autoCodeLength > 20) {
      return $this->bad("自动编码长度不能超过20");
    }

    $viewPaging = intval($params["viewPaging"]);
    if ($viewPaging != 1 && $viewPaging != 2) {
      $viewPaging = 2;
    }
    if ($enableParetnId == 1) {
      if ($viewPaging == 1) {
        return $this->bad("层级数据视图不能分页");
      }
    }

    // 检查编码是否已经存在
    if ($code) {
      $sql = "select count(*) as cnt from t_code_table_md
              where code = '%s' ";
      $data = $db->query($sql, $code);
      $cnt = $data[0]["cnt"];
      if ($cnt > 0) {
        return $this->bad("编码为[{$code}]的码表已经存在");
      }
    }

    // 检查名称是否已经存在
    $sql = "select count(*) as cnt from t_code_table_md
            where name = '%s' ";
    $data = $db->query($sql, $name);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("名称为[{$name}]的码表已经存在");
    }

    // 检查表名是否正确
    $rc = $this->checkTableName($tableName);
    if ($rc) {
      return $rc;
    }
    // 检查表名是否已经存在
    $sql = "select count(*) as cnt from t_code_table_md
					where table_name = '%s' ";
    $data = $db->query($sql, $tableName);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("表名为[{$tableName}]的码表已经存在");
    }
    // 检查数据库中是否已经存在该表了
    $dbUtilDAO = new DBUtilDAO($db);
    if ($dbUtilDAO->tableExists($tableName)) {
      return $this->bad("表[{$tableName}]已经在数据库中存在了");
    }

    if ($handlerClassName) {
      // 判断后台业务处理类是否已经存在
      if (!class_exists($handlerClassName)) {
        return $this->bad("后台业务逻辑处理类[{$handlerClassName}]不存在");
      }
    }

    $id = $this->newId();
    $fid = "ct" . date("YmdHis");

    $sql = "insert into t_code_table_md (id, category_id, code, name, table_name, py, memo, fid,
              enable_parent_id, handler_class_name, module_name, edit_col_cnt, view_paging,
              auto_code_length)
            values ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
              %d, '%s', '%s', %d, %d,
              %d)";
    $rc = $db->execute(
      $sql,
      $id,
      $categoryId,
      $code,
      $name,
      $tableName,
      $py,
      $memo,
      $fid,
      $enableParetnId,
      $handlerClassName,
      $moduleName,
      $editColCnt,
      $viewPaging,
      $autoCodeLength
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 码表标准列
    $cols = $this->getCodeTableSysCols();
    if ($enableParetnId == 1) {
      // 启用层级数据，则创建新列parent_id和full_name
      $cols[] = [
        "caption" => "上级",
        "fieldName" => "parent_id",
        "fieldType" => "varchar",
        "fieldLength" => 255,
        "fieldDecimal" => 0,
        "valueFrom" => 4,
        "valueFromTableName" => $tableName,
        "valueFromColName" => "id",
        "valueFromColNameDisplay" => "full_name",
        "mustInput" => 0,
        "showOrder" => 4,
        "showOrderInView" => -1000,
        "sysCol" => 1,
        "isVisible" => 1,
        "widthInView" => 0,
        "editorXtype" => "psi_codetable_parentidfield"
      ];

      $cols[] = [
        "caption" => "全名",
        "fieldName" => "full_name",
        "fieldType" => "varchar",
        "fieldLength" => 1000,
        "fieldDecimal" => 0,
        "valueFrom" => 5,
        "valueFromTableName" => "",
        "valueFromColName" => "",
        "valueFromColNameDisplay" => "",
        "mustInput" => 0,
        "showOrder" => -1000,
        "showOrderInView" => 3,
        "sysCol" => 1,
        "isVisible" => 2,
        "widthInView" => 300,
        "editorXtype" => "textfield"
      ];
    }

    foreach ($cols as $v) {
      $sql = "insert into t_code_table_cols_md (id, table_id,
                caption, db_field_name, db_field_type, db_field_length,
                db_field_decimal, show_order, value_from, value_from_table_name,
                value_from_col_name, must_input, sys_col, is_visible, width_in_view,
                show_order_in_view, editor_xtype, value_from_col_name_display)
              values ('%s', '%s',
                '%s', '%s', '%s', %d,
                %d, %d, %d, '%s',
                '%s', %d, %d, %d, %d,
                %d, '%s', '%s')";
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
        $v["valueFrom"],
        $v["valueFromTableName"],
        $v["valueFromColName"],
        $v["mustInput"],
        $v["sysCol"],
        $v["isVisible"],
        $v["widthInView"],
        $v["showOrderInView"],
        $v["editorXtype"],
        $v["valueFromColNameDisplay"]
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // fid: t_fid_plus
    $sql = "insert into t_fid_plus (fid, name, py, memo) values ('%s', '%s', '%s', '')";
    $rc = $db->execute($sql, $fid, $name, $py);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 权限: t_permission_plus
    $sql = "insert into t_permission_plus (id, fid, name, note, category, py, show_order)
            values ('%s', '%s', '%s', '%s', '%s','%s', %d)";
    $rc = $db->execute($sql, $fid, $fid, $moduleName, "模块权限：通过菜单进入{$moduleName}模块的权限", $moduleName, "", 100);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 按钮
    $defaultButtons = $this->getDefaultButtons($fid, $name);
    foreach ($defaultButtons as $v) {
      $sql = "insert into t_code_table_buttons(id, table_id, caption, fid, on_click_frontend, show_order)
              values ('%s', '%s', '%s', '%s', '%s', %d)";
      $rc = $db->execute($sql, $this->newId(), $id, $v["caption"], $v["fid"], $v["onClick"], $v["showOrder"]);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      if ($v["fid"] != $fid) {
        // 按钮权限
        $sql = "insert into t_permission_plus (id, parent_fid, fid, name, note, category, py, show_order)
                values ('%s', '%s', '%s', '%s', '%s', '%s','%s', %d)";
        $buttonCaption = $v["caption"];
        $rc = $db->execute(
          $sql,
          $v["fid"],
          $fid,
          $v["fid"],
          $buttonCaption,
          "按钮权限：{$moduleName}模块[{$buttonCaption}]按钮的权限",
          $moduleName,
          "",
          200 + intval($v["showOrder"])
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    }

    // 码表数据权限
    $sql = "insert into t_permission_plus (id, parent_fid, fid, name, note, category, py, show_order)
            values ('%s', '%s', '%s', '%s', '%s', '%s','%s', %d)";
    $buttonCaption = $v["caption"];
    $fidData  = $fid . "-dataorg";
    $rc = $db->execute(
      $sql,
      $fidData,
      $fid,
      $fidData,
      "{$name}在业务单据中的使用权限",
      "数据域权限：{$name}在业务单据中的使用权限",
      $moduleName,
      "",
      300
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }


    // 创建数据库表
    $sql = "CREATE TABLE IF NOT EXISTS `{$tableName}` (";
    foreach ($cols as $v) {
      $fieldName = $v["fieldName"];
      $fieldType = $v["fieldType"];
      $fieldLength = $v["fieldLength"];
      $fieldDecimal = $v["fieldDecimal"];
      $mustInput = $v["mustInput"];

      $type = $fieldType;

      if ($fieldType == "varchar") {
        $type .= "({$fieldLength})";
      } else if ($fieldType == "decimal") {
        $type .= "(19, {$fieldDecimal})";
      } else if ($fieldType == "int") {
        $type .= "(11)";
      }

      $sql .= "`{$fieldName}` {$type} ";
      if ($mustInput == 1) {
        $sql .= " NOT NULL";
      } else {
        $sql .= " DEFAULT NULL";
      }
      $sql .= ",";
    }
    $sql .= "PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    $rc = $db->execute($sql);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["id"] = $id;
    return null;
  }

  /**
   * 编辑码表主表元数据
   *
   * @param array $params
   */
  public function updateCodeTable(&$params)
  {
    $db = $this->db;

    // 码表id
    $id = $params["id"];
    $code = strtoupper($params["code"]) ?? "";
    $name = $params["name"];
    $moduleName = $params["moduleName"];
    $categoryId = $params["categoryId"];
    $handlerClassName = $params["handlerClassName"];
    $memo = $params["memo"] ?? "";
    $py = $params["py"];

    $t = $this->isPSISystemCodeTable($id);
    if ($t) {
      return $this->bad("{$t}的元数据不允许修改");
    }

    $editColCnt = intval($params["editColCnt"]);
    if ($editColCnt < 1) {
      $editColCnt = 1;
    } else if ($editColCnt > 4) {
      return $this->bad("编辑布局列数不能超过4");
    }
    $autoCodeLength = intval($params["autoCodeLength"]);
    if ($autoCodeLength < 0) {
      $autoCodeLength = 0;
    }
    if ($autoCodeLength > 20) {
      return $this->bad("自动编码长度不能超过20");
    }

    $viewPaging = intval($params["viewPaging"]);
    if ($viewPaging != 1 && $viewPaging != 2) {
      $viewPaging = 2;
    }

    if (!$this->getCodeTableCategoryById($categoryId)) {
      return $this->bad("码表分类不存在");
    }

    $codeTable = $this->getCodeTableById($id);
    if (!$codeTable) {
      return $this->bad("要编辑的码表不存在");
    }
    $fid = $codeTable["fid"];
    $enableParentId = $codeTable["enableParentId"];
    if ($enableParentId == 1) {
      if ($viewPaging == 1) {
        return $this->bad("层级数据视图不能分页");
      }
    }

    if ($handlerClassName) {
      // 判断后台业务处理类是否已经存在
      if (!class_exists($handlerClassName)) {
        return $this->bad("后台业务逻辑处理类[{$handlerClassName}]不存在");
      }
    }

    $sql = "update t_code_table_md
            set code = '%s', name = '%s', module_name = '%s',
              category_id = '%s', memo = '%s',
              handler_class_name = '%s',
              md_version = md_version + 1,
              edit_col_cnt = %d, view_paging = %d,
              auto_code_length = %d
            where id = '%s' ";
    $rc = $db->execute(
      $sql,
      $code,
      $name,
      $moduleName,
      $categoryId,
      $memo,
      $handlerClassName,
      $editColCnt,
      $viewPaging,
      $autoCodeLength,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // fid
    $sql = "update t_fid_plus
              set name = '%s', py = '%s'
            where fid = '%s'";
    $rc = $db->execute($sql, $moduleName, $py, $fid);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 权限
    $sql = "update t_permission_plus
              set category = '%s', name = '%s',
                note = '%s', py = '%s' 
            where fid = '%s' ";
    $rc = $db->execute($sql, $moduleName, $moduleName, "模块权限：通过菜单进入{$moduleName}模块的权限", $py, $fid);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 按钮及权限
    $sql = "select fid
            from t_code_table_buttons
            where table_id = '%s'
            order by show_order";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $fidButton = $v["fid"];
      $buttonCaption = "";

      if ($fidButton == $fid) {
        // 不需要处理额外处理权限的按钮，其权限是进入模块的权限
        continue;
      } else
      if ($fidButton == $fid . "-add") {
        $buttonCaption = "新增{$name}";
      } else if ($fidButton == $fid . "-update") {
        $buttonCaption = "编辑{$name}";
      } else if ($fidButton == $fid . "-delete") {
        $buttonCaption = "删除{$name}";
      } else {
        return $this->badParam("fidButton");
      }

      $sql = "update t_permission_plus
              set category = '%s', name = '%s',
                note = '%s' 
              where parent_fid = '%s' and fid = '%s'";
      $rc = $db->execute(
        $sql,
        $moduleName,
        $buttonCaption,
        "按钮权限：{$moduleName}模块[{$buttonCaption}]按钮的权限",
        $fid,
        $fidButton
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 数据源权限
    $sql = "update t_permission_plus
            set category = '%s', name = '%s',
              note = '%s' 
            where parent_fid = '%s' and fid = '%s'";
    $rc = $db->execute(
      $sql,
      $moduleName,
      "{$name}在业务单据中的使用权限",
      "数据域权限：{$name}在业务单据中的使用权限",
      $fid,
      $fid . "-dataorg"
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

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
        return "引用其他码表";
      case 4:
        return "引用自身数据";
      case 5:
        return "自动生成";
      default:
        return "";
    }
  }

  /**
   * 某个码表的列
   */
  public function codeTableColsList($params)
  {
    $db = $this->db;

    // 码表id
    $id = $params["id"];

    $sql = "select id, caption, db_field_name, db_field_type, db_field_length,
              db_field_decimal, show_order, value_from, value_from_table_name,
              value_from_col_name, must_input, sys_col, is_visible, width_in_view,
              note, show_order_in_view, editor_xtype, value_from_col_name_display,
              col_span
            from t_code_table_cols_md
            where table_id = '%s' 
            order by show_order";
    $data = $db->query($sql, $id);

    $result = [];
    foreach ($data as $v) {
      $isVisible = $v["is_visible"] == 1;
      $result[] = [
        "id" => $v["id"],
        "caption" => $v["caption"],
        "fieldName" => $v["db_field_name"],
        "fieldType" => $v["db_field_type"],
        "fieldLength" => $v["db_field_type"] == "datetime" ? null : $v["db_field_length"],
        "fieldDecimal" => $v["db_field_type"] == "decimal" ? $v["db_field_decimal"] : null,
        "showOrder" => $v["show_order"],
        "valueFrom" => $this->valueFromCodeToName($v["value_from"]),
        "valueFromTableName" => $v["value_from_table_name"],
        "valueFromColName" => $v["value_from_col_name"],
        "valueFromColNameDisplay" => $v["value_from_col_name_display"],
        "mustInput" => $v["must_input"] == 2 ? "必录项" : "",
        "sysCol" => $v["sys_col"] == 1 ? "系统列" : "",
        "isVisible" => $isVisible ? "可见" : "不可见",
        "widthInView" => $isVisible ? ($v["width_in_view"] ?? 100) : null,
        "note" => $v["note"],
        "showOrderInView" => $v["show_order_in_view"],
        "editorXtype" => $v["editor_xtype"],
        "colSpan" => $v["col_span"],
      ];
    }

    return $result;
  }

  public function getCodeTableById($id)
  {
    $db = $this->db;

    $sql = "select code, name, fid, is_fixed, table_name, enable_parent_id 
            from t_code_table_md where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return [
        "code" => $data[0]["code"],
        "name" => $data[0]["name"],
        "fid" => $data[0]["fid"],
        "isFixed" => $data[0]["is_fixed"],
        "tableName" => $data[0]["table_name"],
        "enableParentId" => $data[0]["enable_parent_id"],
      ];
    } else {
      return null;
    }
  }

  /**
   * 删除码表
   */
  public function deleteCodeTable(&$params)
  {
    $db = $this->db;

    // 码表id
    $id = $params["id"];

    $codeTable = $this->getCodeTableById($id);
    if (!$codeTable) {
      return $this->bad("要删除的码表不存在");
    }
    $name = $codeTable["name"];
    $fid = $codeTable["fid"];
    $isFixed = $codeTable["isFixed"];
    if ($isFixed == 1) {
      return $this->bad("码表[$name]是系统固有码表，不能删除");
    }
    $tableName = $codeTable["tableName"];

    // 检查fid是否在菜单中使用了
    $sql = "select count(*) as cnt from t_menu_item where fid = '%s' ";
    $data = $db->query($sql, $fid);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("当前码表已经挂接在主菜单中了<br/>在菜单项没有从主菜单中删除之前，码表也不能删除");
    }
    $sql = "select count(*) as cnt from t_menu_item_plus where fid = '%s' ";
    $data = $db->query($sql, $fid);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("当前码表已经挂接在主菜单中了<br/>在菜单项没有从主菜单中删除之前，码表也不能删除");
    }

    // 码表有了数据，也不能删除元数据
    // 这样限制，是为了在系统上线后，减少误删元数据的情形发生
    $sql = "select count(*) as cnt from {$tableName}";
    $data = $db->query($sql);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("当前码表已经有数据，这时不能删除元数据");
    }

    // 列
    $sql = "delete from t_code_table_cols_md where table_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 主表
    $sql = "delete from t_code_table_md where id = '%s' ";
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

    // 删除按钮
    $sql = "delete from t_code_table_buttons where table_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["name"] = $name;
    return null;
  }

  /**
   * 返回码表的默认按钮
   */
  private function getDefaultButtons($fid, $name)
  {
    $result = [];

    // 新增
    $result[] = [
      "caption" => "新增{$name}",
      "showOrder" => 1,
      "fid" => "{$fid}-add",
      "onClick" => "onAddCodeTableRecord",
    ];
    // 编辑
    $result[] = [
      "caption" => "编辑{$name}",
      "showOrder" => 2,
      "fid" => "{$fid}-update",
      "onClick" => "onEditCodeTableRecord",
    ];
    //删除
    $result[] = [
      "caption" => "删除{$name}",
      "showOrder" => 3,
      "fid" => "{$fid}-delete",
      "onClick" => "onDeleteCodeTableRecord",
    ];

    // 分隔符
    $result[] = [
      "caption" => "-",
      "showOrder" => 4,
      "fid" => $fid,
      "onClick" => "",
    ];

    // 刷新
    $result[] = [
      "caption" => "刷新",
      "showOrder" => 5,
      "fid" => $fid,
      "onClick" => "onRefreshCodeTableRecord",
    ];

    return $result;
  }

  /**
   * 查询码表主表元数据
   */
  public function codeTableInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select c.name as category_name, d.code, d.name,
              d.table_name, d.category_id, d.memo, d.enable_parent_id,
              d.handler_class_name, d.module_name, d.edit_col_cnt,
              d.view_paging, d.auto_code_length
            from t_code_table_md d, t_code_table_category c
            where d.id = '%s' and d.category_id = c.id ";
    $data = $db->query($sql, $id);
    if ($data) {
      $v = $data[0];
      return [
        "code" => $v["code"],
        "name" => $v["name"],
        "moduleName" => $v["module_name"],
        "tableName" => $v["table_name"],
        "categoryId" => $v["category_id"],
        "categoryName" => $v["category_name"],
        "enableParentId" => $v["enable_parent_id"],
        "handlerClassName" => $v["handler_class_name"],
        "memo" => $v["memo"],
        "editColCnt" => $v["edit_col_cnt"],
        "viewPaging" => $v["view_paging"],
        "autoCodeLength" => $v["auto_code_length"],
      ];
    } else {
      return $this->emptyResult();
    }
  }

  /**
   * 根据fid获得码表的元数据
   *
   * @param string $fid
   * @return array|NULL
   */
  public function getMetaDataByFid($fid)
  {
    $db = $this->db;

    $sql = "select caption from t_menu_item_plus where fid = '%s' ";
    $data = $db->query($sql, $fid);
    if ($data) {
      $result = [];
      $result["title"] = $data[0]["caption"];
      return $result;
    } else {
      return null;
    }
  }

  /**
   * 查询码表元数据 - 运行界面用
   */
  public function getMetaDataForRuntime($params, $forBackend = false)
  {
    $db = $this->db;

    $fid = $params["fid"];

    $sql = "select id, name, table_name, enable_parent_id, edit_col_cnt, view_paging, auto_code_length ";
    if ($forBackend) {
      $sql .= ",handler_class_name ";
    }
    $sql .=  " from t_code_table_md where fid = '%s' ";
    $data = $db->query($sql, $fid);
    if (!$data) {
      return null;
    }
    $v = $data[0];

    $id = $v["id"];

    $result = [
      "fid" => $fid,
      "tableName" => $v["table_name"],
      "name" => $v["name"],
      "treeView" => $v["enable_parent_id"] == 1,
      "editColCnt" => $v["edit_col_cnt"],
      "viewPaging" => $v["view_paging"],
      "autoCodeLength" => $v["auto_code_length"],
    ];
    if ($forBackend) {
      $result["handlerClassName"] = $v["handler_class_name"];
    }

    // 列
    $sql = "select caption, 
              db_field_name, db_field_type, db_field_length, db_field_decimal,
              sys_col, is_visible, width_in_view, must_input, value_from,
              value_from_table_name, value_from_col_name, editor_xtype, value_from_col_name_display,
              col_span
            from t_code_table_cols_md
            where table_id = '%s' 
            order by show_order";
    $data = $db->query($sql, $id);
    $cols = [];
    foreach ($data as $v) {
      $isVisible = $v["is_visible"] == 1;
      $valueFrom = $v["value_from"];
      $valueFromTableName = $v["value_from_table_name"];
      $valueFromColName = $v["value_from_col_name"];
      $valueFromExtData = [];

      $col = [
        "caption" => $v["caption"],
        "fieldName" => $v["db_field_name"],
        "isVisible" => $isVisible,
        "widthInView" => $isVisible ? ($v["width_in_view"] ?? 100) : null,
        "mustInput" => $v["must_input"] == 2,
        "valueFrom" => $v["value_from"],
        "valueFromTableName" => $v["value_from_table_name"],
        "valueFromColName" => $v["value_from_col_name"],
        "valueFromColNameDisplay" => $v["value_from_col_name_display"],
        "isSysCol" => $v["sys_col"] == 1,
        "editorXtype" => $v["editor_xtype"],
        "colSpan" => $v["col_span"],
      ];

      if ($forBackend) {
        $col["fieldType"] = $v["db_field_type"];
      }

      if ($valueFrom == 2) {
        // 引用系统数据字典
        $sql = "select %s as col_1, name
                from %s
                order by show_order";
        $d = $db->query($sql, $valueFromColName, $valueFromTableName);
        foreach ($d as $item) {
          $valueFromExtData[] = [
            "{$valueFromColName}" => $item["col_1"],
            "name" => $item["name"]
          ];
        }
        $col["valueFromExtData"] = $valueFromExtData;
      } else if ($valueFrom == 3) {
        // 引用其他码表
        $sql = "select fid from t_code_table_md where table_name = '%s' ";
        $d = $db->query($sql, $valueFromTableName);
        if ($d) {
          $col["valueFromFid"] = $d[0]["fid"];
        }
      }

      $cols[] = $col;
    }
    $result["cols"] = $cols;

    $sql = "select caption, db_field_name, width_in_view
            from t_code_table_cols_md
            where table_id = '%s' and show_order_in_view >= 0
            order by show_order_in_view";
    $data = $db->query($sql, $id);
    $colsForView = [];
    foreach ($data as $v) {
      $colsForView[] = [
        "caption" => $v["caption"],
        "fieldName" => $v["db_field_name"],
        "widthInView" => $v["width_in_view"] ?? 100,
      ];
    }
    $result["colsForView"] = $colsForView;

    // 按钮
    $loginUserId = $params["loginUserId"];
    $us = $params["userService"];
    $buttons = [];
    $sql = "select fid, caption, on_click_frontend
            from t_code_table_buttons
            where table_id = '%s'
            order by show_order";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $caption = $v["caption"];
      $fid = $v["fid"];
      if ($caption != "-") {
        // 检查权限
        if ($us->hasPermission($fid)) {
          $buttons[] = [
            "fid" => $fid,
            "caption" => $caption,
            "onClick" => $v["on_click_frontend"],
          ];
        }
      } else {
        $buttons[] = [
          "caption" => "-"
        ];
      }
    }

    $result["buttons"] = $buttons;

    return $result;
  }

  /**
   * 自动生成码表记录的编码
   */
  private function autoCode($md)
  {
    $autoCodeLength = intval($md["autoCodeLength"]);
    if ($autoCodeLength < 0 || $autoCodeLength > 20) {
      return null;
    }

    $db = $this->db;

    $c = 1;

    $tableName = $md["tableName"];
    $sql = "select code from %s order by code desc limit 1";
    $data = $db->query($sql, $tableName);
    if ($data) {
      $code = $data[0]["code"];
      // 自动编码的时候，生成的编码都是数字流水号
      $c = intval($code) + 1;
    }

    return str_pad($c, $autoCodeLength - 1, "0", STR_PAD_LEFT);;
  }

  /**
   * 新增码表记录
   *
   * @param array $params
   * @return array|NULL
   */
  public function addRecord(&$params, $pyService)
  {
    $db = $this->db;
    $fid = $params["fid"];

    $dataOrg = $params["dataOrg"];
    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $md = $this->getMetaDataForRuntime($params, true);

    if (!$md) {
      return $this->badParam("fid");
    }

    $codeTableName = $md["name"];

    // true: 层级数据
    $treeView = $md["treeView"];

    $autoCodeLength = intval($md["autoCodeLength"]);
    $code = $params["code"];
    if ($autoCodeLength > 0) {
      $code = $this->autoCode($md);
    }
    if (!$code) {
      return $this->bad("没有输入编码");
    }
    $name = $params["name"];
    $recordStatus = $params["record_status"];

    $id = $this->newId();

    $tableName = $md["tableName"];

    // 检查编码是否重复
    $sql = "select count(*) as cnt from %s where code = '%s' ";
    $queryParams = [];
    $queryParams[] = $tableName;
    $queryParams[] = $code;
    $data = $db->query($sql, $queryParams);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为[$code]的记录已经存在");
    }

    $sql = "insert into %s (id, py, data_org, company_id, 
              date_created, create_user_id, code, name, 
              record_status";
    $sqlParams = [];
    $sqlParams[] = $tableName;

    foreach ($md["cols"] as $colMd) {
      if ($colMd["isSysCol"]) {
        continue;
      }

      // 非系统字段
      $fieldName = $colMd["fieldName"];

      $sql .= ", %s";
      $sqlParams[] = $fieldName;
    }

    if ($treeView) {
      $sql .= ", parent_id, full_name";
    }

    $sql .= ") values ('%s', '%s', '%s', '%s', 
						now(), '%s', '%s', '%s', 
						%d";
    $sqlParams[] = $id;
    $sqlParams[] = $pyService->toPY($name);
    $sqlParams[] = $dataOrg;
    $sqlParams[] = $companyId;
    $sqlParams[] = $loginUserId;
    $sqlParams[] = $code;
    $sqlParams[] = $name;
    $sqlParams[] = $recordStatus;

    foreach ($md["cols"] as $colMd) {
      if ($colMd["isSysCol"]) {
        continue;
      }

      // 非系统字段
      $fieldName = $colMd["fieldName"];
      $fieldType = $colMd["fieldType"];
      if ($fieldType == "int") {
        $sql .= ", %d";
      } else if ($fieldType == "decimal") {
        $sql .= ", %f";
      } else {
        $sql .= ", '%s'";
      }
      $sqlParams[] = $params[$fieldName];
    }

    if ($treeView) {
      $sql .= ", '%s', '%s'";
      $parentId = $params["parent_id"];
      $sqlParams[] = $parentId;
      $fullName = $name;

      $foundParentId = false;
      if ($parentId) {
        $q = [];
        $sqlQuery = "select full_name from %s where id = '%s' ";
        $q[] = $tableName;
        $q[] = $parentId;
        $d = $db->query($sqlQuery, $q);
        if ($d) {
          $foundParentId = true;
          $fullName = $d[0]["full_name"] . "\\" . $name;
        }
      }
      $sqlParams[] = $fullName;
    }
    $sql .= ")";

    $rc = $db->execute($sql, $sqlParams);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    if ($treeView && !$foundParentId) {
      // 保证顶级数据的parent_id值为null
      $q = [];
      $sql = "update %s set parent_id = null where id = '%s' ";
      $q[] = $tableName;
      $q[] = $id;
      $rc = $db->execute($sql, $q);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $handlerClassName = $md["handlerClassName"];
    if ($handlerClassName) {
      // 处理自定义业务逻辑
      if (!class_exists($handlerClassName)) {
        return $this->bad("后台业务逻辑类[{$handlerClassName}]不存在");
      }

      $hc = new $handlerClassName;
      if (!method_exists($hc, "afterAdd")) {
        return $this->bad("[{$handlerClassName}]没有方法afterAdd");
      }

      $rc = $hc->afterAdd($db, $fid, $id);
      if ($rc) {
        return $this->bad($rc);
      }
    }

    // 操作成功
    $code = $params["code"];
    $name = $params["name"];
    $params["id"] = $id;
    $params["log"] = "新增{$codeTableName} ：{$code}-{$name}";
    $params["logCategory"] = $codeTableName;
    return null;
  }

  /**
   * 编辑码表记录
   */
  public function updateRecord(&$params, $pyService)
  {
    $db = $this->db;
    $fid = $params["fid"];

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $md = $this->getMetaDataForRuntime($params, true);

    if (!$md) {
      return $this->badParam("fid");
    }

    $codeTableName = $md["name"];

    // true: 层级数据
    $treeView = $md["treeView"];

    $code = $params["code"];
    $name = $params["name"];
    $recordStatus = $params["record_status"];

    $id = $params["id"];

    $tableName = $md["tableName"];

    // 检查记录是否存在
    $sql = "select count(*) as cnt from %s where id = '%s' ";
    $data = $db->query($sql, $tableName, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt == 0) {
      return $this->bad("要编辑的记录不存在");
    }

    // 检查编码是否重复
    $sql = "select count(*) as cnt from %s where code = '%s' and id <> '%s' ";
    $queryParams = [];
    $queryParams[] = $tableName;
    $queryParams[] = $code;
    $queryParams[] = $id;
    $data = $db->query($sql, $queryParams);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为[$code]的记录已经存在");
    }

    if ($treeView) {
      // 检查上级id
      $parentId = $params["parent_id"];
      if ($parentId) {
        $sql = "select count(*) as cnt from %s where id = '%s' ";
        $data = $db->query($sql, $tableName, $parentId);
        if ($data) {
          // 检查上级id是否构成了循环引用
          if ($id == $parentId) {
            return $this->bad("不能把自身设置为上级");
          }

          while ($parentId) {
            $sql = "select parent_id from %s where id = '%s' ";
            $data = $db->query($sql, $tableName, $parentId);
            if ($data) {
              $parentId = $data[0]["parent_id"];
              if ($parentId == $id) {
                return $this->bad("不能选用当前记录的下级作为上级");
              }
            } else {
              $parentId = null;
            }
          }
        } else {
          // 传入的parent_id在数据库中不存在
          $params["parent_id"] = null;
        }
      }
    }

    $sql = "update %s 
            set code = '%s', name = '%s', update_dt = now(), update_user_id = '%s',
              record_status = %d, py = '%s' ";
    $queryParams = [];
    $queryParams[] = $tableName;
    $queryParams[] = $code;
    $queryParams[] = $name;
    $queryParams[] = $loginUserId;
    $queryParams[] = $recordStatus;
    $queryParams[] = $pyService->toPY($name);
    foreach ($md["cols"] as $colMd) {
      if ($colMd["isSysCol"]) {
        continue;
      }

      // 非系统字段
      $fieldName = $colMd["fieldName"];

      $sql .= ", %s = ";
      $queryParams[] = $fieldName;

      $fieldType = $colMd["fieldType"];
      if ($fieldType == "int") {
        $sql .= "%d";
      } else if ($fieldType == "decimal") {
        $sql .= "%f";
      } else {
        $sql .= "'%s'";
      }
      $queryParams[] = $params[$fieldName];
    }

    $sql .= " where id = '%s' ";
    $queryParams[] = $id;
    $rc = $db->execute($sql, $queryParams);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    if ($treeView) {
      $parentId = $params["parent_id"];
      if ($parentId) {
        $sql = "select full_name from %s where id = '%s' ";
        $data = $db->query($sql, $tableName, $parentId);
        if ($data) {
          $fullName = $data[0]["full_name"] . "\\" . $name;
          $sql = "update %s set parent_id = '%s', full_name = '%s' where id = '%s' ";
          $rc = $db->execute($sql, $tableName, $parentId, $fullName, $id);
          if ($rc === false) {
            return $this->sqlError(__METHOD__, __LINE__);
          }
        } else {
          $parentId = null;
        }
      }

      if ($parentId == null) {
        $sql = "update %s set parent_id = null, full_name = '%s' where id = '%s' ";
        $rc = $db->execute($sql, $tableName, $name, $id);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    }

    $handlerClassName = $md["handlerClassName"];
    if ($handlerClassName) {
      // 处理自定义业务逻辑
      if (!class_exists($handlerClassName)) {
        return $this->bad("后台业务逻辑类[{$handlerClassName}]不存在");
      }

      $hc = new $handlerClassName;
      if (!method_exists($hc, "afterUpdate")) {
        return $this->bad("[{$handlerClassName}]没有方法afterUpdate");
      }

      $rc = $hc->afterUpdate($db, $fid, $id);
      if ($rc) {
        return $this->bad($rc);
      }
    }

    // 操作成功
    $code = $params["code"];
    $name = $params["name"];
    $params["id"] = $id;
    $params["log"] = "编辑{$codeTableName} ：{$code}-{$name}";
    $params["logCategory"] = $codeTableName;
    return null;
  }

  /**
   * 删除码表记录
   */
  public function deleteRecord(&$params)
  {
    $db = $this->db;
    $fid = $params["fid"];
    $md = $this->getMetaDataForRuntime($params, true);

    if (!$md) {
      return $this->badParam("fid");
    }

    $codeTableName = $md["name"];

    // true: 层级数据
    $treeView = $md["treeView"];

    $code = $params["code"];
    $name = $params["name"];
    $recordStatus = $params["record_status"];

    $id = $params["id"];

    $tableName = $md["tableName"];

    // 检查记录是否存在
    $sql = "select code, name from %s where id = '%s' ";
    $data = $db->query($sql, $tableName, $id);
    if (!$data) {
      return $this->bad("要删除的记录不存在");
    }
    $code = $data[0]["code"];
    $name = $data[0]["name"];

    if ($treeView) {
      // 检查是否有下级
      $sql = "select count(*) as cnt from %s where parent_id = '%s' ";
      $data = $db->query($sql, $tableName, $id);
      $cnt = $data[0]["cnt"];
      if ($cnt > 0) {
        return $this->bad("当前记录还有下级，不能删除");
      }
    }

    // 判断其他码表是否引用了当前记录
    // 这里处理的通过id引用来引用的场景，这类似处理数据库外键
    // 如果是通过其他字段的引用，需要通过handlerClassName来判断
    $sql = "select distinct t.name, t.table_name, c.db_field_name
            from t_code_table_md t, t_code_table_cols_md c
            where t.id = c.table_id and c.value_from = 3
              and c.value_from_table_name = '%s' 
              and c.value_from_col_name = 'id' ";
    $data = $db->query($sql, $tableName);
    foreach ($data as $v) {
      $foreignName = $v["name"];
      $foreignTableName = $v["table_name"];
      $foreignFieldName = $v["db_field_name"];
      $sql = "select count(*) as cnt from %s where %s = '%s'";
      $d = $db->query($sql, $foreignTableName, $foreignFieldName, $id);
      $cnt = $d[0]["cnt"];
      if ($cnt > 0) {
        return $this->bad("码表[{$foreignName}]中引用了当前记录，所以不能删除当前记录");
      }
    }

    // 表单主表是否有引用该码表记录
    $sql = "select distinct f.name, f.table_name, c.db_field_name
            from t_form_cols c, t_form f
            where c.value_from_table_name = '%s' and c.form_id = f.id";
    $data = $db->query($sql, $tableName);
    foreach ($data as $v) {
      $foreignName = $v["name"];
      $foreignTableName = $v["table_name"];
      $foreignFieldName = $v["db_field_name"];
      $sql = "select count(*) as cnt from %s where %s = '%s'";
      $d = $db->query($sql, $foreignTableName, $foreignFieldName, $id);
      $cnt = $d[0]["cnt"];
      if ($cnt > 0) {
        return $this->bad("表单[{$foreignName}]主表中引用了当前记录，所以不能删除当前记录");
      }
    }

    // 表单明细表列是否有引用该码表记录
    $sql = "select distinct f.name, f.table_name, c.db_field_name, fm.name as form_name
            from t_form_detail_cols c, t_form_detail f, t_form fm
            where c.value_from_table_name = '%s' and c.detail_id = f.id and f.form_id = fm.id";
    $data = $db->query($sql, $tableName);
    foreach ($data as $v) {
      $formName = $v["form_name"];
      $foreignName = $v["name"];
      $foreignTableName = $v["table_name"];
      $foreignFieldName = $v["db_field_name"];
      $sql = "select count(*) as cnt from %s where %s = '%s'";
      $d = $db->query($sql, $foreignTableName, $foreignFieldName, $id);
      $cnt = $d[0]["cnt"];
      if ($cnt > 0) {
        return $this->bad("表单[{$formName}]的[{$foreignName}]表中引用了当前记录，所以不能删除当前记录");
      }
    }

    $handlerClassName = $md["handlerClassName"];
    if ($handlerClassName) {
      // 处理自定义业务逻辑
      if (!class_exists($handlerClassName)) {
        return $this->bad("后台业务逻辑类[{$handlerClassName}]不存在");
      }

      $hc = new $handlerClassName;
      if (!method_exists($hc, "beforeDelete")) {
        return $this->bad("[{$handlerClassName}]没有方法beforeDelete");
      }

      $rc = $hc->beforeDelete($db, $fid, $id);
      if ($rc) {
        return $this->bad($rc);
      }
    }

    $sql = "delete from %s where id = '%s' ";
    $rc = $db->execute($sql, $tableName, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["log"] = "删除{$codeTableName}记录 ：{$code}-{$name}";
    $params["logCategory"] = $codeTableName;
    return null;
  }

  /**
   * 码表记录列表
   */
  public function codeTableRecordList($params)
  {
    $db = $this->db;

    $fid = $params["fid"];
    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $md = $this->getMetaDataForRuntime($params, true);
    if (!$md) {
      return $this->emptyResult();
    }

    $tableName = $md["tableName"];

    $viewPaging = $md["viewPaging"] == 1;

    $sql = "select cr.id, cr.code, cr.name, u.name as create_user_name, 
            r.name as record_status, r.code_int as record_status_code_int";

    foreach ($md["cols"] as $colMd) {
      if ($colMd["isSysCol"]) {
        continue;
      }

      if ($colMd["isVisible"]) {
        $sql .= ", cr." . $colMd["fieldName"];
      }
    }

    $sql .= " from %s cr, t_user u, t_sysdict_record_status r ";
    $queryParams = [];
    $queryParams[] = $tableName;

    $sql .= " where (cr.create_user_id = u.id) and (cr.record_status = r.code_int)";
    // 数据域
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL($fid, "cr", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by cr.record_status desc, cr.code ";
    if ($viewPaging) {
      $start = $params["start"];
      $limit = $params["limit"];
      $sql .= " limit %d, %d ";
      $queryParams[] = $start;
      $queryParams[] = $limit;
    }

    $data = $db->query($sql, $queryParams);
    $result = [];
    foreach ($data as $v) {
      $item = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "record_status" => $v["record_status"],
        "record_status_code_int" => $v["record_status_code_int"],
      ];

      foreach ($md["cols"] as $colMd) {
        if ($colMd["isSysCol"]) {
          continue;
        }

        if ($colMd["isVisible"]) {
          $fieldName = $colMd["fieldName"];

          $fieldType = $colMd["fieldType"];
          if ($fieldType == "datetime") {
            $item[$fieldName] = $this->toYMD($v[$fieldName]);
          } else {
            $item[$fieldName] = $v[$fieldName];
          }
        }

        $valueFrom = $colMd["valueFrom"];
        $valueFromTableName = $colMd["valueFromTableName"];
        $valueFromColName = $colMd["valueFromColName"];
        $valueFromColNameDisplay = $colMd["valueFromColNameDisplay"];
        if ($valueFrom == 2) {
          //系统数据字典
          $sql = "select {$valueFromColNameDisplay} as nv from {$valueFromTableName}
                  where {$valueFromColName} = '%s' ";
          $d = $db->query($sql, $v[$fieldName]);
          if ($d) {
            $item[$fieldName] = $d[0]["nv"];
          }
        } else if ($valueFrom == 3) {
          // 引用其他码表
          $sql = "select {$valueFromColNameDisplay} as nv from {$valueFromTableName}
                  where {$valueFromColName} = '%s' ";
          $d = $db->query($sql, $v[$fieldName]);
          if ($d) {
            $item[$fieldName] = $d[0]["nv"];
          } else {
            $item[$fieldName] = null;
          }
        }
      }

      $result[] = $item;
    }

    $cnt = 0;
    if ($viewPaging) {
      $sql = "select count(*) as cnt from %s cr where (1 =1 ) ";
      $queryParams = [];
      $queryParams[] = $tableName;

      // 数据域
      $ds = new DataOrgDAO($db);
      $rs = $ds->buildSQL($fid, "cr", $loginUserId);
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParams = array_merge($queryParams, $rs[1]);
      }
      $data = $db->query($sql, $queryParams);
      $cnt = $data[0]["cnt"];
    } else {
      $cnt = count($result);
    }
    return ["dataList" => $result, "totalCount" => $cnt];
  }

  private function codeTableRecordListForTreeViewInternal($db, $id, $md, $rs)
  {
    $tableName = $md["tableName"];

    $sql = "select cr.id, cr.code, cr.name, cr.full_name, u.name as create_user_name, r.name as record_status";

    foreach ($md["cols"] as $colMd) {
      if ($colMd["isSysCol"]) {
        continue;
      }

      if ($colMd["isVisible"]) {
        $sql .= ", cr." . $colMd["fieldName"];
      }
    }

    $sql .= " from %s cr, t_user u, t_sysdict_record_status r ";
    $queryParams = [];
    $queryParams[] = $tableName;

    $sql .= " where (cr.parent_id = '%s') and (cr.create_user_id = u.id) and (cr.record_status = r.code_int)";
    $queryParams[] = $id;
    // 数据域
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by code";

    $data = $db->query($sql, $queryParams);
    $result = [];
    foreach ($data as $v) {
      // 递归调用自身
      $children = $this->codeTableRecordListForTreeViewInternal($db, $v["id"], $md, $rs);
      $v["children"] = $children;
      $v["leaf"] = count($children) == 0;
      $v["expanded"] = true;
      $v["iconCls"] = "PSI-CodeTable-DataCategory";

      $result[] = $v;
    }

    return $result;
  }

  /**
   * 码表记录 - 树状结构
   */
  public function codeTableRecordListForTreeView($params)
  {
    $db = $this->db;

    $fid = $params["fid"];
    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }
    $md = $this->getMetaDataForRuntime($params);
    if (!$md) {
      return $this->emptyResult();
    }

    $tableName = $md["tableName"];

    $sql = "select cr.id, cr.code, cr.name, cr.full_name, u.name as create_user_name, r.name as record_status";

    foreach ($md["cols"] as $colMd) {
      if ($colMd["isSysCol"]) {
        continue;
      }

      if ($colMd["isVisible"]) {
        $sql .= ", cr." . $colMd["fieldName"];
      }
    }

    $sql .= " from %s cr, t_user u, t_sysdict_record_status r ";
    $queryParams = [];
    $queryParams[] = $tableName;

    $sql .= " where (cr.parent_id is null) and (cr.create_user_id = u.id) and (cr.record_status = r.code_int)";
    // 数据域
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL($fid, "cr", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by code";

    $data = $db->query($sql, $queryParams);
    $result = [];
    foreach ($data as $v) {
      $children = $this->codeTableRecordListForTreeViewInternal($db, $v["id"], $md, $rs);
      $v["children"] = $children;
      $v["leaf"] = count($children) == 0;
      $v["expanded"] = true;
      $v["iconCls"] = "PSI-CodeTable-DataCategory";

      $result[] = $v;
    }

    return $result;
  }

  /**
   * 新增码表列
   *
   * @param array $params
   * @return null|array
   */
  public function addCodeTableCol(&$params)
  {
    $db = $this->db;

    $codeTableId = $params["codeTableId"];
    $caption = $params["caption"];
    $fieldName = strtolower($params["fieldName"]);
    $fieldType = strtolower($params["fieldType"]);
    $fieldLength = $params["fieldLength"];
    $fieldDecimal = $params["fieldDecimal"];
    $valueFrom = intval($params["valueFrom"]);
    $valueFromTableName = $params["valueFromTableName"];
    $valueFromColName = $params["valueFromColName"];
    $valueFromColNameDisplay = $params["valueFromColNameDisplay"];
    $mustInput = $params["mustInput"];
    $widthInView = $params["widthInView"];
    $showOrder = $params["showOrder"];
    $showOrderInView = $params["showOrderInView"];
    $isVisible = $params["isVisible"];
    $editorXtype = $params["editorXtype"];
    $memo = $params["memo"];
    $colSpan = intval($params["colSpan"]);
    if ($colSpan < 1) {
      $colSpan = 1;
    }

    $t = $this->isPSISystemCodeTable($codeTableId);
    if ($t) {
      return $this->bad("{$t}不允许新建字段");
    }

    // 检查码表是否存在
    $codeTable = $this->getCodeTableById($codeTableId);
    if (!$codeTable) {
      return $this->bad("要新增列的码表不存在");
    }
    $tableName = $codeTable["tableName"];
    $codeTableName = $codeTable["name"];

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

    if ($fieldName == "record_status_code_int") {
      return $this->bad("[record_status_code_int]是系统保留的字段名，不能使用这个字段名");
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
    } else if ($fieldType == "datetime") {
      //占位
    } else {
      return $this->bad("字段类型[{$fieldType}]目前还不支持");
    }

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
      // 引用其他码表
      if ($tableName == $valueFromTableName) {
        // TODO 这个限制是否合理，待定
        return $this->bad("字段值来源不能引用自身");
      }

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
    if ($valueFrom == 4) {
      // 引用自身
      $valueFromTableName = $tableName;
      $sql = "select count(*) as cnt from t_code_table_cols_md where table_id = '%s' and db_field_name = '%s' ";
      $data = $db->query($sql, $codeTableId, $valueFromColName);
      $cnt = $data[0]["cnt"];
      if ($cnt == 0) {
        return $this->bad("码表[$valueFromTableName]的列[{$valueFromColName}]的元数据不存在");
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

    // 写入字段元数据
    $id = $this->newId();
    $sql = "insert into t_code_table_cols_md (id, table_id, caption, 
              db_field_name, db_field_type, db_field_length, db_field_decimal,
              show_order, value_from, value_from_table_name, value_from_col_name,
              must_input, sys_col, is_visible, width_in_view, note, 
              show_order_in_view, editor_xtype, value_from_col_name_display,
              col_span)
            values ('%s', '%s', '%s',
              '%s', '%s', %d, %d,
              %d, %d, '%s', '%s',
              %d, %d, %d, %d, '%s',
              %d, '%s', '%s',
              %d)";
    $rc = $db->execute(
      $sql,
      $id,
      $codeTableId,
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
      $showOrderInView,
      $editorXtype,
      $valueFromColNameDisplay,
      $colSpan
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
    } else if ($fieldType == "decimal") {
      $sql .= " decimal({$fieldLength}, {$fieldDecimal}) ";
    } else if ($fieldType == "datetime") {
      $sql .= " datetime ";
    } else {
      return $this->bad("字段类型[{$fieldType}]还不支持");
    }
    $sql .= " default null;";
    $rc = $db->execute($sql);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    //操作成功
    $params["log"] = "新增码表[{$codeTableName}]列 ：{$caption}";
    $params["id"] = $id;
    return null;
  }

  /**
   * 编辑码表列
   */
  public function updateCodeTableCol(&$params)
  {
    $db = $this->db;
    $id = $params["id"];
    $codeTableId = $params["codeTableId"];
    $codeTable = $this->getCodeTableById($codeTableId);
    if (!$codeTable) {
      return $this->badParam("codeTableId");
    }
    $codeTableName = $codeTable["name"];

    $t = $this->isPSISystemCodeTable($codeTableId);
    if ($t) {
      return $this->bad("{$t}不允许编辑字段");
    }

    // 检查码表列元数据是否存在
    $sql = "select sys_col from t_code_table_cols_md 
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要编辑的码表列的元数据不存在");
    }

    $sysCol = $data[0]["sys_col"];

    $caption = $params["caption"];
    $widthInView = $params["widthInView"];
    $showOrder = $params["showOrder"];
    $showOrderInView = $params["showOrderInView"];
    $note = $params["memo"];
    $colSpan = intval($params["colSpan"]);
    if ($colSpan < 1) {
      $colSpan = 1;
    }

    if ($sysCol == 1) {
      // 系统列
      $sql = "update t_code_table_cols_md
              set caption = '%s', width_in_view = %d, show_order = %d,
                show_order_in_view = %d, note = '%s', col_span = %d
              where id = '%s' ";
      $rc = $db->execute(
        $sql,
        $caption,
        $widthInView,
        $showOrder,
        $showOrderInView,
        $note,
        $colSpan,
        $id
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      // 用户自定义列
      $sql = "update t_code_table_cols_md
              set caption = '%s', width_in_view = %d, show_order = %d,
                show_order_in_view = %d, note = '%s', col_span = %d
              where id = '%s' ";
      $rc = $db->execute(
        $sql,
        $caption,
        $widthInView,
        $showOrder,
        $showOrderInView,
        $note,
        $colSpan,
        $id
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    $params["log"] = "编辑码表[{$codeTableName}]的列[{$caption}]的元数据";
    return null;
  }

  /**
   * 查询码表记录的详情
   */
  public function recordInfo($params)
  {
    $db = $this->db;
    $id = $params["id"];
    $fid = $params["fid"];
    if (!$id) {
      return $this->emptyResult();
    }

    $md = $this->getMetaDataForRuntime($params, true);
    if (!$md) {
      // 传入了错误的fid值
      return $this->emptyResult();
    }

    $tableName = $md["tableName"];
    $treeView = $md["treeView"];
    $sql = "select cr.code, cr.name, cr.record_status";
    if ($treeView) {
      $sql .= ",parent_id";
    }

    foreach ($md["cols"] as $colMd) {
      if ($colMd["isSysCol"]) {
        continue;
      }

      if ($colMd["isVisible"]) {
        $sql .= ", cr." . $colMd["fieldName"];
      }
    }

    $sql .= " from %s cr 
              where (cr.id = '%s')";
    $queryParams = [];
    $queryParams[] = $tableName;
    $queryParams[] = $id;

    $data = $db->query($sql, $queryParams);
    if (!$data) {
      return $this->emptyResult();
    }

    $result = $data[0];
    foreach ($md["cols"] as $colMd) {
      if ($colMd["isSysCol"]) {
        continue;
      }

      if ($colMd["isVisible"]) {
        if ($colMd["fieldType"] == "datetime") {
          $fieldName = $colMd["fieldName"];
          $result[$fieldName] = $this->toYMD($result[$fieldName]);
        }
      }
    }

    if ($treeView) {
      $parentId = $data[0]["parent_id"];
      if ($parentId) {
        $sql = "select full_name from %s where id = '%s' ";
        $d = $db->query($sql, $tableName, $parentId);
        if ($d) {
          $result["parent_id_value"] = $d[0]["full_name"];
        }
      }
    }

    // 处理码表
    foreach ($md["cols"] as $colMd) {
      if ($colMd["valueFrom"] == 3) {
        $valueFromTableName = $colMd["valueFromTableName"];
        $valueFromColName = $colMd["valueFromColName"];
        $valueFromColNameDisplay = $colMd["valueFromColNameDisplay"];
        $sql = "select {$valueFromColNameDisplay} as nd from {$valueFromTableName}
                where {$valueFromColName} = '%s' ";
        $d = $db->query($sql, $result[$colMd["fieldName"]]);
        if ($d) {
          $result[$colMd["fieldName"] . "_display_value"] = $d[0]["nd"];
        }
      }
    }

    return $result;
  }

  /**
   * 码表某列的详细信息
   */
  public function codeTableColInfo($params)
  {
    $db = $this->db;
    $tableId = $params["tableId"];

    // col id
    $id = $params["id"];

    $result = [];

    $sql = "select name, memo from t_sysdict_editor_xtype order by code";
    $data = $db->query($sql);
    $xtype = [];
    foreach ($data as $v) {
      $xtype[] = [
        "id" => $v["name"],
        "text" => $v["name"] . " - " . $v["memo"]
      ];
    }

    $result["editorXtype"] = $xtype;

    if ($id) {
      // 编辑
      $sql = "select caption, db_field_name, db_field_type, db_field_length,
                db_field_decimal, note, show_order, value_from, 
                value_from_table_name, value_from_col_name, value_from_col_name_display,
                must_input, sys_col, is_visible, width_in_view, show_order_in_view,
                editor_xtype, col_span
              from t_code_table_cols_md
              where id = '%s' ";
      $data = $db->query($sql, $id);
      if ($data) {
        $v = $data[0];
        $result["col"] = [
          "caption" => $v["caption"],
          "fieldName" => $v["db_field_name"],
          "fieldType" => $v["db_field_type"],
          "fieldLength" => $v["db_field_length"],
          "fieldDecimal" => $v["db_field_decimal"],
          "memo" => $v["note"],
          "showOrder" => $v["show_order"],
          "valueFrom" => $v["value_from"],
          "valueFromTableName" => $v["value_from_table_name"],
          "valueFromColName" => $v["value_from_col_name"],
          "valueFromColNameDisplay" => $v["value_from_col_name_display"],
          "mustInput" => $v["must_input"],
          "sysCol" => $v["sys_col"],
          "isVisible" => $v["is_visible"],
          "widthInView" => $v["width_in_view"],
          "showOrderInView" => $v["show_order_in_view"],
          "editorXtype" => $v["editor_xtype"],
          "colSpan" => $v["col_span"],
        ];
      }
    }
    return $result;
  }

  /**
   * 删除码表列
   */
  public function deleteCodeTableCol(&$params)
  {
    $db = $this->db;
    // 码表id
    $tableId = $params["tableId"];
    // 列id
    $id = $params["id"];

    $sql = "select table_name, name from t_code_table_md where id = '%s' ";
    $data = $db->query($sql, $tableId);
    if (!$data) {
      return $this->bad("码表不存在");
    }
    $tableName = $data[0]["table_name"];
    $name = $data[0]["name"];

    $sql = "select caption, sys_col, db_field_name 
            from t_code_table_cols_md 
            where id = '%s' and table_id = '%s' ";
    $data = $db->query($sql, $id, $tableId);
    if (!$data) {
      return $this->bad("要删除的列不存在");
    }
    $caption = $data[0]["caption"];
    $sysCol = $data[0]["sys_col"];
    if ($sysCol == 1) {
      return $this->bad("列[{$caption}]是系统固有列，不能删除");
    }
    $colName = $data[0]["db_field_name"];

    // 检查该字段是否被其他码表使用
    $sql = "select t.name, c.caption
            from t_code_table_md t, t_code_table_cols_md c
            where t.id = c.table_id and c.value_from = 3
              and c.value_from_table_name = '%s'
              and c.value_from_col_name = '%s' ";
    $data = $db->query($sql, $tableName, $colName);
    if ($data) {
      $n = $data[0]["name"];
      $c = $data[0]["caption"];
      return $this->bad("码表[{$n}]的列[{$c}]引用当前字段，所以不能删除当前字段");
    }

    // 执行删除操作
    $sql = "delete from t_code_table_cols_md where id = '%s' and table_id = '%s' ";
    $rc = $db->execute($sql, $id, $tableId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["log"] = "删除码表[{$name}]列[{$caption}]的元数据";
    return null;
  }

  /**
   * 码表记录引用字段 - 查询数据
   */
  public function queryDataForRecordRef($params)
  {
    $db = $this->db;
    $fid = $params["fid"];
    $queryKey = $params["queryKey"];
    $loginUserId = $params["loginUserId"];

    $sql = "select table_name from t_code_table_md where fid = '%s' ";
    $data = $db->query($sql, $fid);

    if (!$data) {
      return $this->emptyResult();
    }
    $tableName = $data[0]["table_name"];

    $sql = "select id, code, name from {$tableName} ct
            where (code like '%s' or name like '%s' or py like '%s') and (record_status = 1000) ";
    $queryParams = [];
    $queryParams[] = "%{$queryKey}%";
    $queryParams[] = "%{$queryKey}%";
    $queryParams[] = "%{$queryKey}%";
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL("{$fid}-dataorg", "ct", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }
    $sql .= " order by code limit 20 ";

    $data = $db->query($sql, $queryParams);

    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
      ];
    }
    return $result;
  }

  /**
   * 查询是否是固有码表，例如：t_org、t_user等
   * 
   * @param $id 码表的id
   * 
   * @return null:非固有码表。非null，则是对于的码表数据库名称,例如：t_org
   */
  private function isPSISystemCodeTable($id)
  {
    $list = [
      ["id" => "AFB52688-851E-11EA-B071-E86A641ED142", "tableName" => "t_org"],
      ["id" => "1C7AE1C9-85CC-11EA-A819-E86A641ED142", "tableName" => "t_user"],
      ["id" => "49F3F27F-8607-11EA-A0E2-E86A641ED142", "tableName" => "t_warehouse"],
      ["id" => "C68DBABE-860B-11EA-A0E2-E86A641ED142", "tableName" => "t_goods_category"],
      ["id" => "8BD2B19E-8623-11EA-B463-E86A641ED142", "tableName" => "t_goods_unit"],
      ["id" => "D7D9D328-8834-11EA-8C36-E86A641ED142", "tableName" => "t_goods_brand"],
    ];

    foreach ($list as $v) {
      if ($v["id"] == $id) {
        return $v["tableName"];
      }
    }

    // 不是固有码表
    return null;
  }

  /**
   * 把码表转化为系统固有码表
   */
  public function convertCodeTable(&$params)
  {
    $db = $this->db;
    $id = $params["id"];

    $codeTable = $this->getCodeTableById($id);
    if (!$codeTable) {
      return $this->bad("要操作的表单不存在");
    }
    $isFixed = $codeTable["isFixed"];
    $codeTableName = $codeTable["name"];

    if ($isFixed == 1) {
      return $this->bad("[{$codeTableName}]已经是系统固有码表了，不需要再次转换");
    }

    $sql = "update t_code_table_md
            set is_fixed = 1
            where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "update t_code_table_cols_md
            set sys_col = 1
            where table_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["name"] = $codeTableName;
    return null;
  }

  /**
   * 保存列视图布局
   */
  public function saveColViewLayout(&$params)
  {
    $db = $this->db;

    $fid = $params["fid"];
    $json = $params["json"];
    $layout = json_decode(html_entity_decode($json), true);
    if (!$layout) {
      return $this->badParam("json");
    }

    $sql = "select id, name
            from t_code_table_md
            where fid = '%s' ";
    $data = $db->query($sql, $fid);
    if (!$data) {
      return $this->badParam("fid");
    }
    $v = $data[0];
    $codeTableName = $v["name"];
    $id = $v["id"];

    foreach ($layout as $i => $v) {
      $sql = "update t_code_table_cols_md
              set show_order_in_view = %d, width_in_view = %d
              where table_id = '%s' and db_field_name = '%s' ";
      $rc = $db->execute($sql, $i, $v["width"], $id, $v["dataIndex"]);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    $params["name"] = $codeTableName;
    return null;
  }

  /**
   * 查询码表编辑界面字段的显示次序
   */
  public function queryCodeTableEditColShowOrder($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select caption, db_field_name
            from t_code_table_cols_md
            where table_id = '%s' and is_visible = 1 
            order by show_order";
    $data = $db->query($sql, $id);
    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "caption" => $v["caption"],
        "dataIndex" => $v["db_field_name"],
      ];
    }
    return $result;
  }

  /**
   * 保存编辑界面字段显示次序
   */
  public function saveColEditShowOrder(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $json = $params["json"];
    $layout = json_decode(html_entity_decode($json), true);
    if (!$layout) {
      return $this->badParam("json");
    }

    $sql = "select name
            from t_code_table_md
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("码表不存在");
    }
    $v = $data[0];
    $codeTableName = $v["name"];

    foreach ($layout as $i => $v) {
      $sql = "update t_code_table_cols_md
              set show_order = %d
              where table_id = '%s' and db_field_name = '%s' ";
      $rc = $db->execute($sql, $i, $id, $v["dataIndex"]);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    $params["name"] = $codeTableName;
    return null;
  }

  /**
   * 码表生成SQL
   */
  public function codeTableGenSQL($params)
  {
    $db = $this->db;
    $id = $params["id"];
    $sql = "select code, name, table_name, category_id, memo,
              py, fid, md_version, is_fixed, enable_parent_id, handler_class_name,
              module_name, edit_col_cnt, view_paging
            from t_code_table_md 
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("码表不存在");
    }
    $v = $data[0];

    // t_code_table_md
    $code = $v["code"];
    $name = $v["name"];
    $tableName = $v["table_name"];
    $categoryId = $v["category_id"];
    $memo = $v["memo"];
    $py = $v["py"];
    $fid = $v["fid"];
    $mdVersion = $v["md_version"];
    $isFixed = $v["is_fixed"];
    $enableParentId = $v["enable_parent_id"];
    $handlerClassName = $v["handler_class_name"];
    $moduleName = $v["module_name"];
    $editColCnt = $v["edit_col_cnt"];
    $viewPaging = $v["view_paging"];
    $result = "# 码表：{$name}\n";
    $result .= "DELETE FROM `t_code_table_md` where `id` = '{$id}';\n";
    $result .= "INSERT INTO `t_code_table_md` (`id`, `code`, `name`, `table_name`, `category_id`, `memo`,";
    $result .= " `py`, `fid`, `md_version`, `is_fixed`, `enable_parent_id`, `handler_class_name`,";
    $result .= " `module_name`, `edit_col_cnt`, `view_paging`)\n";
    $result .= "VALUES ('{$id}', '{$code}', '{$name}', '{$tableName}', '{$categoryId}', '{$memo}',";
    $result .= " '{$py}', '{$fid}', $mdVersion, $isFixed, $enableParentId, '{$handlerClassName}',";
    $result .= " '{$moduleName}', $editColCnt, $viewPaging);\n\n";

    // t_code_table_cols_md
    $result .= "DELETE FROM `t_code_table_cols_md` where `table_id` = '{$id}';\n";
    $sql = "select id, caption, db_field_name, db_field_type, db_field_length, db_field_decimal,
              show_order, value_from, value_from_table_name, value_from_col_name,
              value_from_col_name_display, must_input, sys_col, is_visible, width_in_view,
              note, show_order_in_view, editor_xtype, col_span
            from t_code_table_cols_md
            where table_id = '%s'";
    $data = $db->query($sql, $id);
    $cnt = count($data);
    if ($cnt > 0) {
      $result .= "INSERT INTO `t_code_table_cols_md` (`id`, `table_id`, `caption`, `db_field_name`,";
      $result .= " `db_field_type`, `db_field_length`, `db_field_decimal`, `show_order`, `value_from`,";
      $result .= " `value_from_table_name`, `value_from_col_name`, `value_from_col_name_display`,";
      $result .= " `must_input`, `sys_col`, `is_visible`, `width_in_view`, `note`, `show_order_in_view`,";
      $result .= " `editor_xtype`, `col_span`) VALUES\n";
    }
    foreach ($data as $i => $v) {
      $colId = $v["id"];
      $caption = $v["caption"];
      $fieldName = $v["db_field_name"];
      $fieldType = $v["db_field_type"];
      $fieldLength = $v["db_field_length"];
      $fieldDecimal = $v["db_field_decimal"];
      $showOrder = $v["show_order"];
      $valueFrom = $v["value_from"];
      $valueFromTableName = $v["value_from_table_name"];
      $valueFromColName = $v["value_from_col_name"];
      $valueFromColNameDisplay = $v["value_from_col_name_display"];
      $mustInput = $v["must_input"];
      $sysCol = $v["sys_col"];
      $isVisible = $v["is_visible"];
      $widthInView = $v["width_in_view"];
      $note = $v["note"];
      $showOrderInView = $v["show_order_in_view"];
      $editorXtype = $v["editor_xtype"];
      $colSpan = $v["col_span"];

      $result .= "('{$colId}', '{$id}', '{$caption}', '{$fieldName}',";
      $result .= " '{$fieldType}', {$fieldLength}, {$fieldDecimal}, {$showOrder}, {$valueFrom},";
      $result .= " '{$valueFromTableName}', '{$valueFromColName}', '{$valueFromColNameDisplay}',";
      $result .= " {$mustInput}, {$sysCol}, {$isVisible}, {$widthInView}, '{$note}', {$showOrderInView},";
      $result .= " '{$editorXtype}', {$colSpan})";
      $result .= $i < $cnt - 1 ? "," : ";";
      $result .= "\n";
    }
    $result .= "\n";

    // t_code_table_buttons
    $result .= "DELETE FROM `t_code_table_buttons` where `table_id` = '{$id}';\n";
    $sql = "select id, caption, fid, on_click_frontend, on_click_backend, show_order
            from t_code_table_buttons
            where table_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = count($data);
    if ($cnt > 0) {
      $result .= "INSERT INTO `t_code_table_buttons` (`id`, `table_id`, `caption`, `fid`,";
      $result .= " `on_click_frontend`, `on_click_backend`, `show_order`) VALUES\n";
    }
    foreach ($data as $i => $v) {
      $btnId = $v["id"];
      $caption = $v["caption"];
      $btnFid = $v["fid"];
      $onClickFrontend = $v["on_click_frontend"];
      $onClickBackend = $v["on_click_backend"];
      $showOrder = $v["show_order"];
      $result .= "('{$btnId}', '{$id}', '{$caption}', '{$btnFid}',";
      $result .= " '{$onClickFrontend}', '{$onClickBackend}', $showOrder)";
      $result .= $i < $cnt - 1 ? "," : ";";
      $result .= "\n";
    }
    $result .= "\n";

    // t_fid_plus
    $result .= "DELETE FROM `t_fid_plus` where `fid` = '{$fid}';\n";
    $sql = "select name ,py, memo from t_fid_plus where fid = '%s' ";
    $data = $db->query($sql, $fid);
    if ($data) {
      $v = $data[0];
      $name = $v["name"];
      $py = $v["py"];
      $memo = $v["memo"];
      $result .= "INSERT INTO `t_fid_plus` (`fid`, `name`, `py`, `memo`) VALUES\n";
      $result .= "('{$fid}', '{$name}', '{$py}', '{$memo}');\n";
    }
    $result .= "\n";

    // t_permission_plus
    $result .= "DELETE FROM `t_permission_plus` where fid = '{$fid}' or parent_fid = '{$fid}';\n";

    $sql = "select id, fid, name, note, category, py, show_order, parent_fid
            from t_permission_plus
            where fid = '%s' or parent_fid = '%s'
            order by show_order";
    $data = $db->query($sql, $fid, $fid);
    $cnt = count($data);
    if ($cnt > 0) {
      $result .= "INSERT INTO `t_permission_plus` (`id`, `fid`, `name`, `note`, `category`,";
      $result .= " `py`, `show_order`, `parent_fid`) VALUES\n";
    }
    foreach ($data as $i => $v) {
      $permissionId = $v["id"];
      $permissionFid = $v["fid"];
      $name = $v["name"];
      $note = $v["note"];
      $category = $v["category"];
      $py = $v["py"];
      $showOrder = $v["show_order"];
      $parentFid = $v["parent_fid"];
      $result .= "('{$permissionId}', '{$permissionFid}', '{$name}', '{$note}', '{$category}',";
      $result .= " '{$py}', $showOrder,";
      if ($parentFid) {
        $result .= " '{$parentFid}'";
      } else {
        $result .= " null";
      }
      $result .= ")";
      $result .= $i < $cnt - 1 ? "," : ";";
      $result .= "\n";
    }

    return ["sql" => $result, "success" => true];
  }
}
