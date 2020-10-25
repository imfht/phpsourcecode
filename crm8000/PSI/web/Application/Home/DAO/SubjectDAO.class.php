<?php

namespace Home\DAO;

use Home\Service\PinyinService;

/**
 * 会计科目 DAO
 *
 * @author 李静波
 */
class SubjectDAO extends PSIBaseExDAO
{

  private function subjectListInternal($parentId, $companyId)
  {
    $db = $this->db;

    $sql = "select id, code, name, category, is_leaf from t_subject
            where parent_id = '%s' and company_id = '%s'
            order by code ";
    $data = $db->query($sql, $parentId, $companyId);
    $result = [];
    foreach ($data as $v) {
      // 递归调用自己
      $children = $this->subjectListInternal($v["id"], $companyId);

      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "category" => $v["category"],
        "isLeaf" => $v["is_leaf"] == 1 ? "末级科目" : null,
        "children" => $children,
        "leaf" => count($children) == 0,
        "iconCls" => "PSI-Subject",
        "expanded" => true
      ];
    }

    return $result;
  }

  /**
   * 某个公司的科目码列表
   *
   * @param array $params
   * @return array
   */
  public function subjectList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];

    // 判断$companyId是否是公司id
    $sql = "select count(*) as cnt
            from t_org where id = '%s' and parent_id is null ";
    $data = $db->query($sql, $companyId);
    $cnt = $data[0]["cnt"];
    if ($cnt == 0) {
      return $this->emptyResult();
    }

    $result = [];

    $sql = "select id, code, name, category, is_leaf from t_subject
            where parent_id is null and company_id = '%s'
            order by code ";
    $data = $db->query($sql, $companyId);
    foreach ($data as $v) {
      $children = $this->subjectListInternal($v["id"], $companyId);

      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "category" => $v["category"],
        "isLeaf" => $v["is_leaf"] == 1 ? "末级科目" : null,
        "children" => $children,
        "leaf" => count($children) == 0,
        "iconCls" => "PSI-Subject",
        "expanded" => true
      ];
    }

    return $result;
  }

  private function insertSubjectInternal($code, $name, $category, $companyId, $py, $dataOrg)
  {
    $db = $this->db;

    $sql = "select count(*) as cnt from t_subject where code = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $code, $companyId);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return;
    }

    $id = $this->newId();

    $sql = "insert into t_subject(id, category, code, name, is_leaf, py, data_org, company_id, parent_id)
            values ('%s', '%s', '%s', '%s', 0, '%s', '%s', '%s', null)";
    $rc = $db->execute($sql, $id, $category, $code, $name, $py, $dataOrg, $companyId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    return null;
  }

  /**
   * 国家标准科目表
   *
   * @return array
   */
  private function getStandardSubjectList()
  {
    $result = [];

    $result[] = [
      "code" => "1001",
      "name" => "库存现金",
      "category" => 1
    ];
    $result[] = [
      "code" => "1002",
      "name" => "银行存款",
      "category" => 1
    ];
    $result[] = [
      "code" => "1012",
      "name" => "其他货币资金",
      "category" => 1
    ];
    $result[] = [
      "code" => "1101",
      "name" => "交易性金融资产",
      "category" => 1
    ];
    $result[] = [
      "code" => "1121",
      "name" => "应收票据",
      "category" => 1
    ];
    $result[] = [
      "code" => "1122",
      "name" => "应收账款",
      "category" => 1
    ];
    $result[] = [
      "code" => "1123",
      "name" => "预付账款",
      "category" => 1
    ];
    $result[] = [
      "code" => "1131",
      "name" => "应收股利",
      "category" => 1
    ];
    $result[] = [
      "code" => "1132",
      "name" => "应收利息",
      "category" => 1
    ];
    $result[] = [
      "code" => "1221",
      "name" => "其他应收款",
      "category" => 1
    ];
    $result[] = [
      "code" => "1231",
      "name" => "坏账准备",
      "category" => 1
    ];
    $result[] = [
      "code" => "1401",
      "name" => "材料采购",
      "category" => 1
    ];
    $result[] = [
      "code" => "1402",
      "name" => "在途物资",
      "category" => 1
    ];
    $result[] = [
      "code" => "1403",
      "name" => "原材料",
      "category" => 1
    ];
    $result[] = [
      "code" => "1405",
      "name" => "库存商品",
      "category" => 1
    ];
    $result[] = [
      "code" => "1406",
      "name" => "发出商品",
      "category" => 1
    ];
    $result[] = [
      "code" => "1408",
      "name" => "委托加工物资",
      "category" => 1
    ];
    $result[] = [
      "code" => "1411",
      "name" => "周转材料",
      "category" => 1
    ];
    $result[] = [
      "code" => "1511",
      "name" => "长期股权投资",
      "category" => 1
    ];
    $result[] = [
      "code" => "1601",
      "name" => "固定资产",
      "category" => 1
    ];
    $result[] = [
      "code" => "1602",
      "name" => "累计折旧",
      "category" => 1
    ];
    $result[] = [
      "code" => "1604",
      "name" => "在建工程",
      "category" => 1
    ];
    $result[] = [
      "code" => "1605",
      "name" => "工程物资",
      "category" => 1
    ];
    $result[] = [
      "code" => "1606",
      "name" => "固定资产清理",
      "category" => 1
    ];
    $result[] = [
      "code" => "1701",
      "name" => "无形资产",
      "category" => 1
    ];
    $result[] = [
      "code" => "1702",
      "name" => "累计摊销",
      "category" => 1
    ];
    $result[] = [
      "code" => "1801",
      "name" => "长期待摊费用",
      "category" => 1
    ];
    $result[] = [
      "code" => "1901",
      "name" => "待处理财产损溢",
      "category" => 1
    ];
    $result[] = [
      "code" => "2001",
      "name" => "短期借款",
      "category" => 2
    ];
    $result[] = [
      "code" => "2201",
      "name" => "应付票据",
      "category" => 2
    ];
    $result[] = [
      "code" => "2202",
      "name" => "应付账款",
      "category" => 2
    ];
    $result[] = [
      "code" => "2203",
      "name" => "预收账款",
      "category" => 2
    ];
    $result[] = [
      "code" => "2211",
      "name" => "应付职工薪酬",
      "category" => 2
    ];
    $result[] = [
      "code" => "2221",
      "name" => "应交税费",
      "category" => 2
    ];
    $result[] = [
      "code" => "2231",
      "name" => "应付利息",
      "category" => 2
    ];
    $result[] = [
      "code" => "2232",
      "name" => "应付股利",
      "category" => 2
    ];
    $result[] = [
      "code" => "2241",
      "name" => "其他应付款",
      "category" => 2
    ];
    $result[] = [
      "code" => "2501",
      "name" => "长期借款",
      "category" => 2
    ];
    $result[] = [
      "code" => "4001",
      "name" => "实收资本",
      "category" => 4
    ];
    $result[] = [
      "code" => "4002",
      "name" => "资本公积",
      "category" => 4
    ];
    $result[] = [
      "code" => "4101",
      "name" => "盈余公积",
      "category" => 4
    ];
    $result[] = [
      "code" => "4103",
      "name" => "本年利润",
      "category" => 4
    ];
    $result[] = [
      "code" => "4104",
      "name" => "利润分配",
      "category" => 4
    ];
    $result[] = [
      "code" => "5001",
      "name" => "生产成本",
      "category" => 5
    ];
    $result[] = [
      "code" => "5101",
      "name" => "制造费用",
      "category" => 5
    ];
    $result[] = [
      "code" => "5201",
      "name" => "劳务成本",
      "category" => 5
    ];
    $result[] = [
      "code" => "6001",
      "name" => "主营业务收入",
      "category" => 6
    ];
    $result[] = [
      "code" => "6051",
      "name" => "其他业务收入",
      "category" => 6
    ];
    $result[] = [
      "code" => "6111",
      "name" => "投资收益",
      "category" => 6
    ];
    $result[] = [
      "code" => "6301",
      "name" => "营业外收入",
      "category" => 6
    ];
    $result[] = [
      "code" => "6401",
      "name" => "主营业务成本",
      "category" => 6
    ];
    $result[] = [
      "code" => "6402",
      "name" => "其他业务成本",
      "category" => 6
    ];
    $result[] = [
      "code" => "6403",
      "name" => "营业税金及附加",
      "category" => 6
    ];
    $result[] = [
      "code" => "6601",
      "name" => "销售费用",
      "category" => 6
    ];
    $result[] = [
      "code" => "6602",
      "name" => "管理费用",
      "category" => 6
    ];
    $result[] = [
      "code" => "6603",
      "name" => "财务费用",
      "category" => 6
    ];
    $result[] = [
      "code" => "6701",
      "name" => "资产减值损失",
      "category" => 6
    ];
    $result[] = [
      "code" => "6711",
      "name" => "营业外支出",
      "category" => 6
    ];
    $result[] = [
      "code" => "6801",
      "name" => "所得税费用",
      "category" => 6
    ];

    return $result;
  }

  /**
   * 初始国家标准科目
   */
  public function init(&$params, $pinYinService)
  {
    $db = $this->db;

    $dataOrg = $params["dataOrg"];

    $companyId = $params["id"];
    $sql = "select name 
            from t_org
            where id = '%s' and parent_id is null";
    $data = $db->query($sql, $companyId);
    if (!$data) {
      return $this->badParam("companyId");
    }

    $companyName = $data[0]["name"];

    $sql = "select count(*) as cnt from t_subject where company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("国家科目表已经初始化完毕，不能再次初始化");
    }

    $subjectList = $this->getStandardSubjectList();
    foreach ($subjectList as $v) {
      $code = $v["code"];
      $name = $v["name"];
      $category = $v["category"];

      $rc = $this->insertSubjectInternal(
        $code,
        $name,
        $category,
        $companyId,
        $pinYinService->toPY($name),
        $dataOrg
      );
      if ($rc) {
        return $rc;
      }
    }

    // 操作成功
    $params["companyName"] = $companyName;

    return null;
  }

  /**
   * 上级科目字段 - 查询数据
   *
   * @param string $queryKey
   */
  public function queryDataForParentSubject($queryKey, $companyId)
  {
    $db = $this->db;

    // length(code) < 8 : 只查询一级二级科目
    $sql = "select code, name
            from t_subject
            where (code like '%s') and (length(code) < 8) 
              and (company_id = '%s') 
            order by code 
            limit 20 ";
    $queryParams = [];
    $queryParams[] = "{$queryKey}%";
    $queryParams[] = $companyId;
    $data = $db->query($sql, $queryParams);

    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "code" => $v["code"],
        "name" => $v["name"]
      ];
    }

    return $result;
  }

  /**
   * 新增科目
   *
   * @param array $params
   */
  public function addSubject(&$params)
  {
    $db = $this->db;

    $dataOrg = $params["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    $code = $params["code"];
    $name = $params["name"];
    $isLeaf = $params["isLeaf"];

    $parentCode = $params["parentCode"];
    $sql = "select id, category 
            from t_subject 
            where company_id = '%s' and code = '%s' ";
    $data = $db->query($sql, $companyId, $parentCode);
    if (!$data) {
      return $this->bad("上级科目不存在");
    }
    $parentId = $data[0]["id"];
    $category = $data[0]["category"];

    // 检查科目码是否正确
    if (strlen($parentCode) == 4) {
      // 上级科目是一级科目
      if (strlen($code) != 6) {
        return $this->bad("二级科目码的长度需要是6位");
      }
      if (substr($code, 0, 4) != $parentCode) {
        return $this->bad("二级科目码的前四位必须是一级科目码");
      }
    } else if (strlen($parentCode) == 6) {
      // 上级科目是二级科目
      if (strlen($code) != 8) {
        return $this->bad("三级科目码的长度需要是8位");
      }
      if (substr($code, 0, 6) != $parentCode) {
        return $this->bad("三级科目码的前六位必须是二级科目码");
      }
    } else {
      return $this->bad("上级科目只能是一级科目或者是二级科目");
    }

    // 判断科目码是否已经存在
    $sql = "select count(*) as cnt from t_subject
            where company_id = '%s' and code = '%s' ";
    $data = $db->query($sql, $companyId, $code);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("科目码[{$code}]已经存在");
    }

    $ps = new PinyinService();
    $py = $ps->toPY($name);

    $id = $this->newId();
    $sql = "insert into t_subject(id, category, code, name, is_leaf, py, data_org,
              company_id, parent_id)
            values ('%s', '%s', '%s', '%s', %d, '%s', '%s',
              '%s', '%s')";
    $rc = $db->execute(
      $sql,
      $id,
      $category,
      $code,
      $name,
      $isLeaf,
      $py,
      $dataOrg,
      $companyId,
      $parentId
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["id"] = $id;
    return null;
  }

  /**
   * 编辑科目
   *
   * @param array $params
   */
  public function updateSubject(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $name = $params["name"];
    $isLeaf = $params["isLeaf"];

    $sql = "select parent_id from t_subject where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要编辑的科目不存在");
    }

    $parentId = $data[0]["parent_id"];
    if (!$parentId) {
      // 当前科目是一级科目，一级科目只能编辑“末级科目”
      $sql = "update t_subject set is_leaf = %d
              where id = '%s' ";
      $rc = $db->execute($sql, $isLeaf, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      // 二级或三级科目
      $ps = new PinyinService();
      $py = $ps->toPY($name);
      $sql = "update t_subject
              set name = '%s', py = '%s', is_leaf = %d
              where id = '%s' ";
      $rc = $db->execute($sql, $name, $py, $isLeaf, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    return null;
  }

  /**
   * 某个科目的详情
   *
   * @param array $params
   */
  public function subjectInfo($params)
  {
    $db = $this->db;

    // 科目id
    $id = $params["id"];

    $sql = "select code, name, is_leaf, parent_id 
            from t_subject
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->emptyResult();
    }

    $v = $data[0];

    $result = [
      "code" => $v["code"],
      "name" => $v["name"],
      "isLeaf" => $v["is_leaf"],
      "parentCode" => "[无]"
    ];

    $parentId = $v["parent_id"];
    $sql = "select code, name
            from t_subject
            where id = '%s' ";
    $data = $db->query($sql, $parentId);
    if ($data) {
      $result["parentCode"] = $data[0]["code"];
    }

    return $result;
  }

  /**
   * 删除科目
   *
   * @param array $params
   */
  public function deleteSubject(&$params)
  {
    $db = $this->db;

    // 科目id
    $id = $params["id"];

    $sql = "select code, parent_id, company_id from t_subject where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要删除的科目不存在");
    }
    $companyId = $data[0]["company_id"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->bad("当前科目的companyId字段值异常");
    }

    $code = $data[0]["code"];
    $parentId = $data[0]["parent_id"];
    if (!$parentId) {
      return $this->bad("不能删除一级科目");
    }

    // 检查科目是否有下级科目
    $sql = "select count(*) as cnt from t_subject where parent_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("科目[{$code}]还有下级科目，不能删除");
    }

    // 判断科目是否在账样中使用
    $sql = "select count(*) as cnt 
            from t_acc_fmt
            where company_id = '%s' and subject_code = '%s' ";
    $data = $db->query($sql, $companyId, $code);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("科目[{$code}]已经在账样中使用，不能删除");
    }

    $sql = "delete from t_subject where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["code"] = $code;
    return null;
  }

  private function insertFmtCols($fmtId, $dbFieldName, $dbFieldType, $dbFieldLength, $dbFieldDecimal, $showOrder, $caption)
  {
    $db = $this->db;

    $sql = "insert into t_acc_fmt_cols (id, fmt_id, db_field_name, db_field_type,
              db_field_length, db_field_decimal, show_order, caption, sys_col)
            values ('%s', '%s', '%s', '%s',
              %d, %d, %d, '%s', 1)";
    $rc = $db->execute(
      $sql,
      $this->newId(),
      $fmtId,
      $dbFieldName,
      $dbFieldType,
      $dbFieldLength,
      $dbFieldDecimal,
      $showOrder,
      $caption
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    return null;
  }

  private function getStandardFmtCols()
  {
    return [
      [
        "name" => "subject_code",
        "type" => "varchar",
        "length" => 255,
        "decimal" => 0,
        "showOrder" => 1,
        "caption" => "科目"
      ],
      [
        "name" => "voucher_dt",
        "type" => "date",
        "length" => 0,
        "decimal" => 0,
        "showOrder" => 2,
        "caption" => "凭证日期"
      ],
      [
        "name" => "voucher_year",
        "type" => "int",
        "length" => 11,
        "decimal" => 0,
        "showOrder" => 3,
        "caption" => "凭证年度"
      ],
      [
        "name" => "voucher_month",
        "type" => "int",
        "length" => 11,
        "decimal" => 0,
        "showOrder" => 4,
        "caption" => "凭证月份"
      ],
      [
        "name" => "voucher_word",
        "type" => "varchar",
        "length" => 255,
        "decimal" => 0,
        "showOrder" => 5,
        "caption" => "凭证字"
      ],
      [
        "name" => "voucher_number",
        "type" => "int",
        "length" => 11,
        "decimal" => 0,
        "showOrder" => 6,
        "caption" => "凭证号"
      ],
      [
        "name" => "je_number",
        "type" => "int",
        "length" => 0,
        "decimal" => 0,
        "showOrder" => 7,
        "caption" => "分录号"
      ],
      [
        "name" => "acc_user_name",
        "type" => "varchar",
        "length" => 255,
        "decimal" => 0,
        "showOrder" => 8,
        "caption" => "会计经办"
      ],
      [
        "name" => "acc_user_id",
        "type" => "varchar",
        "length" => 255,
        "decimal" => 0,
        "showOrder" => -1000,
        "caption" => "会计经办id"
      ],
      [
        "name" => "biz_user_name",
        "type" => "varchar",
        "length" => 255,
        "decimal" => 0,
        "showOrder" => 9,
        "caption" => "业务责任人"
      ],
      [
        "name" => "biz_user_is",
        "type" => "varchar",
        "length" => 255,
        "decimal" => 0,
        "showOrder" => -1000,
        "caption" => "业务责任人id"
      ],
      [
        "name" => "acc_db",
        "type" => "decimal",
        "length" => 19,
        "decimal" => 2,
        "showOrder" => 10,
        "caption" => "借方金额"
      ],
      [
        "name" => "acc_cr",
        "type" => "decimal",
        "length" => 19,
        "decimal" => 2,
        "showOrder" => 11,
        "caption" => "贷方金额"
      ],
      [
        "name" => "acc_balance_dbcr",
        "type" => "varchar",
        "length" => 255,
        "decimal" => 0,
        "showOrder" => 12,
        "caption" => "余额借贷方向"
      ],
      [
        "name" => "acc_balance",
        "type" => "decimal",
        "length" => 19,
        "decimal" => 2,
        "showOrder" => 13,
        "caption" => "余额金额"
      ]
    ];
  }

  /**
   * 初始化科目的标准账样
   *
   * @param array $params
   */
  public function initFmt(&$params)
  {
    $db = $this->db;

    $dataOrg = $params["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }

    // id:科目id
    $id = $params["id"];
    $companyId = $params["companyId"];

    $sql = "select code, is_leaf from t_subject where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("科目不存在");
    }
    $subjectCode = $data[0]["code"];
    $isLeaf = $data[0]["is_leaf"] == 1;
    if (!$isLeaf) {
      return $this->bad("科目[{$subjectCode}]不是末级科目，不能设置账样");
    }

    $sql = "select count(*) as cnt from t_acc_fmt 
            where company_id = '%s' and subject_code = '%s' ";
    $data = $db->query($sql, $companyId, $subjectCode);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("科目[{$subjectCode}]已经完成了标准账样的初始化，不能再次初始化");
    }

    $accNumber = str_pad($subjectCode, 8, "0", STR_PAD_RIGHT);

    $tableName = "t_acc_" . $accNumber;
    $sql = "select count(*) as cnt from t_acc_fmt where db_table_name_prefix like '%s' ";
    $data = $db->query($sql, "{$tableName}%");
    $cnt = $data[0]["cnt"];
    $cnt += 1;
    if ($cnt < 10) {
      $t = "_0{$cnt}";
    } else {
      $t = "_{$cnt}";
    }
    $tableName .= $t;

    $id = $this->newId();

    $sql = "insert into t_acc_fmt (id, acc_number, subject_code, memo,
              date_created, data_org, company_id, in_use, db_table_name_prefix)
            values ('%s', '%s', '%s', '',
              now(), '%s', '%s', 1, '%s')";
    $rc = $db->execute($sql, $id, $accNumber, $subjectCode, $dataOrg, $companyId, $tableName);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 标准列
    $fmtId = $id;
    $cols = $this->getStandardFmtCols();
    foreach ($cols as $v) {
      $rc = $this->insertFmtCols(
        $fmtId,
        $v["name"],
        $v["type"],
        $v["length"],
        $v["decimal"],
        $v["showOrder"],
        $v["caption"]
      );
      if ($rc) {
        return $rc;
      }
    }

    // 操作成功
    $params["code"] = $subjectCode;
    return null;
  }

  /**
   * 某个科目的账样属性
   *
   * @param array $params
   */
  public function fmtPropList($params)
  {
    $db = $this->db;
    $result = [];

    // id: 科目id
    $id = $params["id"];
    $companyId = $params["companyId"];

    $sql = "select f.acc_number, f.in_use, f.db_table_name_prefix, f.date_created
            from t_subject s, t_acc_fmt f
            where s.id = '%s' and  s.code = f.subject_code and f.company_id = '%s' ";

    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $v = $data[0];
      $result[] = [
        "propName" => "账簿号",
        "propValue" => $v["acc_number"]
      ];

      $result[] = [
        "propName" => "状态",
        "propValue" => $v["in_use"] == 1 ? "启用" : "停用"
      ];

      $result[] = [
        "propName" => "表名前缀",
        "propValue" => $v["db_table_name_prefix"]
      ];

      $result[] = [
        "propName" => "初始化时间",
        "propValue" => $v["date_created"]
      ];
    }

    return $result;
  }

  /**
   * 某个科目的账样字段列表
   *
   * @param array $params
   */
  public function fmtColsList($params)
  {
    $db = $this->db;
    $result = [];

    // id: 科目id
    $id = $params["id"];
    $companyId = $params["companyId"];

    $sql = "select c.id, c.show_order, c.caption, c.db_field_name, c.db_field_type,
              c.db_field_length, c.db_field_decimal
            from t_subject s, t_acc_fmt f, t_acc_fmt_cols c
            where s.id = '%s' and s.code = f.subject_code and f.company_id = '%s'
              and f.id = c.fmt_id and c.show_order > 0
            order by c.show_order";
    $data = $db->query($sql, $id, $companyId);
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "showOrder" => $v["show_order"],
        "caption" => $v["caption"],
        "fieldName" => $v["db_field_name"],
        "fieldType" => $v["db_field_type"],
        "fieldLength" => $v["db_field_length"] == 0 ? null : $v["db_field_length"],
        "fieldDecimal" => $v["db_field_decimal"] == 0 ? null : $v["db_field_decimal"]
      ];
    }

    return $result;
  }

  private function tableExists($tableName)
  {
    $db = $this->db;

    $dbName = C('DB_NAME');
    $sql = "select count(*) as cnt
            from information_schema.columns
            where table_schema = '%s'
              and table_name = '%s' ";
    $data = $db->query($sql, $dbName, $tableName);
    return $data[0]["cnt"] != 0;
  }

  /**
   * 清空科目的标准账样
   */
  public function undoInitFmt(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $companyId = $params["companyId"];

    $sql = "select code from t_subject where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("科目不存在");
    }
    $code = $data[0]["code"];

    $sql = "select id, db_table_name_prefix from t_acc_fmt
            where subject_code = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $code, $companyId);
    if (!$data) {
      return $this->bad("科目[{$code}]还没有初始化标准账样");
    }
    $fmtId = $data[0]["id"];

    // 检查是否已经创建了数据库账样表
    $tableName = $data[0]["db_table_name_prefix"];
    if ($this->tableExists($tableName)) {
      return $this->bad("科目[{$code}]的账样已经创建数据库表，不能再做清除操作");
    }

    // 检查账样是否增加了自定义字段
    $sql = "select count(*) as cnt from t_acc_fmt_cols
            where fmt_id = '%s' ";
    $data = $db->query($sql, $fmtId);
    $cnt = $data[0]["cnt"];
    $standardList = $this->getStandardSubjectList();
    if ($cnt > count($standardList)) {
      return $this->bad("账样已经设置了自定义字段，不能清空标准账样了");
    }

    $sql = "delete from t_acc_fmt_cols where fmt_id = '%s' ";
    $rc = $db->execute($sql, $fmtId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_acc_fmt where id = '%s' ";
    $rc = $db->execute($sql, $fmtId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["code"] = $code;
    return null;
  }

  /**
   * 盘点字符串中是否都是小写字母或是下划线
   *
   * @param string $s
   * @return boolean
   */
  private function strIsAllLetters($s)
  {
    for ($i = 0; $i < strlen($s); $i++) {
      $c = ord($s[$i]);
      if ($c == ord('_')) {
        continue;
      }

      if (ord('a') > $c || $c > ord('z')) {
        return false;
      }
    }

    return true;
  }

  /**
   * 新增账样字段
   *
   * @param array $params
   */
  public function addFmtCol(&$params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    $subjectCode = $params["subjectCode"];
    $fieldCaption = $params["fieldCaption"];
    $fieldName = strtolower($params["fieldName"]);
    $fieldType = $params["fieldType"];

    // 检查账样
    $sql = "select id, db_table_name_prefix as cnt from t_acc_fmt
            where company_id = '%s' and subject_code = '%s' ";
    $data = $db->query($sql, $companyId, $subjectCode);
    if (!$data) {
      return $this->bad("科目[$subjectCode]的标准账样还没有初始化");
    }
    $dbTableName = $data[0]["db_table_name_prefix"];
    $fmtId = $data[0]["id"];

    // 检查账样是否已经启用
    if ($this->tableExists($dbTableName)) {
      return $this->bad("科目[{$subjectCode}]的账样已经启用，不能再新增账样字段");
    }

    // 检查字段名是否合格
    if (strlen($fieldName) == 0) {
      return $this->bad("没有输入数据库字段名");
    }
    if (!$this->strIsAllLetters($fieldName)) {
      return $this->bad("数据库字段名需要是小写字母");
    }
    $sql = "select count(*) as cnt from t_acc_fmt_cols 
            where fmt_id = '%s' and db_field_name = '%s' ";
    $data = $db->query($sql, $fmtId, $fieldName);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("科目[{$subjectCode}]的账样中已经存在字段[{$fieldName}]");
    }

    $type = "varchar";
    $length = 255;
    $dec = 0;
    switch ($fieldType) {
      case 1:
        $type = "varchar";
        $length = 255;
        $dec = 0;
        break;
      case 2:
        $type = "date";
        $length = 0;
        $dec = 0;
        break;
      case 3:
        $type = "decimal";
        $length = 19;
        $dec = 2;
        break;
      default:
        return $this->bad("字段类型不正确");
    }

    $sql = "select max(show_order) as max_show_order from t_acc_fmt_cols
            where fmt_id = '%s' and show_order > 0 ";
    $data = $db->query($sql, $fmtId);
    $cnt = $data[0]["max_show_order"];
    $showOrder = $cnt + 1;

    $id = $this->newId();
    $sql = "insert into t_acc_fmt_cols (id, fmt_id, caption, db_field_name,
              db_field_type, db_field_length, db_field_decimal, show_order, sys_col)
            values ('%s', '%s', '%s', '%s',
              '%s', %d, %d, %d, 0)";
    $rc = $db->execute(
      $sql,
      $id,
      $fmtId,
      $fieldCaption,
      $fieldName,
      $type,
      $length,
      $dec,
      $showOrder
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["id"] = $id;
    return null;
  }

  /**
   * 编辑账样字段
   *
   * @param array $params
   */
  public function updateFmtCol(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $subjectCode = $params["subjectCode"];
    $fieldCaption = $params["fieldCaption"];
    $fieldName = strtolower($params["fieldName"]);
    $fieldType = $params["fieldType"];

    $sql = "select fmt_id, sys_col from t_acc_fmt_cols where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要编辑的账样字段不存在");
    }
    $fmtId = $data[0]["fmt_id"];
    $sysCol = $data[0]["sys_col"] == 1;

    $sql = "select db_table_name_prefix from t_acc_fmt where id = '%s' ";
    $data = $db->query($sql, $fmtId);
    if (!$data) {
      return $this->bad("账样不存在");
    }
    $tableName = $data[0]["db_table_name_prefix"];
    $inited = $this->tableExists($tableName);

    if ($inited) {
      // $inited:ture 账样已经创建了数据库表，这个时候就不能修改账样的字段类型了

      $sql = "update t_acc_fmt_cols
              set caption = '%s' 
              where id = '%s' ";
      $rc = $db->execute($sql, $fieldCaption, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      if ($sysCol) {
        // 系统账样字段，也只能修改标题，不能修改字段类型
        $sql = "update t_acc_fmt_cols
                set caption = '%s'
                where id = '%s' ";
        $rc = $db->execute($sql, $fieldCaption, $id);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        // 用户自定义字段，并且还没有创建数据库表
        // 检查字段名是否合格
        if (strlen($fieldName) == 0) {
          return $this->bad("没有输入数据库字段名");
        }
        if (!$this->strIsAllLetters($fieldName)) {
          return $this->bad("数据库字段名需要是小写字母");
        }
        $sql = "select count(*) as cnt from t_acc_fmt_cols
                where fmt_id = '%s' and db_field_name = '%s' 
                  and id <> '%s' ";
        $data = $db->query($sql, $fmtId, $fieldName, $id);
        $cnt = $data[0]["cnt"];
        if ($cnt > 0) {
          return $this->bad("科目[{$subjectCode}]的账样中已经存在字段[{$fieldName}]");
        }

        $type = "varchar";
        $length = 255;
        $dec = 0;
        switch ($fieldType) {
          case 1:
            $type = "varchar";
            $length = 255;
            $dec = 0;
            break;
          case 2:
            $type = "date";
            $length = 0;
            $dec = 0;
            break;
          case 3:
            $type = "decimal";
            $length = 19;
            $dec = 2;
            break;
          default:
            return $this->bad("字段类型不正确");
        }

        $sql = "update t_acc_fmt_cols
                set caption = '%s', db_field_name = '%s',
                  db_field_type = '%s',
                  db_field_length = %d,
                  db_field_decimal = %d
                where id = '%s' ";
        $rc = $db->execute($sql, $fieldCaption, $fieldName, $type, $length, $dec, $id);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    }

    // 操作成功
    return null;
  }

  private function fieldTypeNameToCode($name)
  {
    switch ($name) {
      case "varchar":
        return 1;
      case "date":
        return 2;
      case "decimal":
        return 3;
      default:
        return 0;
    }
  }

  /**
   * 获得某个账样字段的详情
   */
  public function fmtColInfo($params)
  {
    $db = $this->db;
    $id = $params["id"];

    $result = [];

    $sql = "select caption, db_field_name, sys_col,
              db_field_type, fmt_id 
            from t_acc_fmt_cols 
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $v = $data[0];

      $result["caption"] = $v["caption"];
      $result["fieldName"] = $v["db_field_name"];
      $result["sysCol"] = $v["sys_col"];
      $result["fieldType"] = $this->fieldTypeNameToCode($v["db_field_type"]);

      $fmtId = $v["fmt_id"];
      $sql = "select db_table_name_prefix from t_acc_fmt where id = '%s' ";
      $data = $db->query($sql, $fmtId);
      if ($data) {
        $tableName = $data[0]["db_table_name_prefix"];
        if ($this->tableExists($tableName)) {
          $result["dbTableCreated"] = 1;
        } else {
          $result["dbTableCreated"] = 0;
        }
      }
    }

    return $result;
  }

  /**
   * 删除某个账样字段
   */
  public function deleteFmtCol(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select fmt_id, caption, sys_col 
            from t_acc_fmt_cols
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要删除的账样字段不存在");
    }
    $v = $data[0];
    $fmtId = $v["fmt_id"];
    $caption = $v["caption"];
    $sysCol = $v["sys_col"];
    if ($sysCol == 1) {
      return $this->bad("账样字段[{$caption}]是标准账样字段，不能删除");
    }

    $sql = "select subject_code, acc_number, db_table_name_prefix 
            from t_acc_fmt 
            where id = '%s' ";
    $data = $db->query($sql, $fmtId);
    if (!$data) {
      return $this->bad("账样不存在");
    }
    $v = $data[0];
    $tableName = $v["db_table_name_prefix"];
    if ($this->tableExists($tableName)) {
      return $this->bad("账样创建数据库表之后不能删除账样字段");
    }
    $subjectCode = $v["subject_code"];
    $accNumber = $v["acc_number"];

    $sql = "delete from t_acc_fmt_cols where id = '%s' ";
    $rc = $db->execute($sql, $id);

    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["caption"] = $caption;
    $params["subjectCode"] = $subjectCode;
    $params["accNumber"] = $accNumber;
    return null;
  }

  /**
   * 某个账样所有字段 - 设置字段显示次序用
   */
  public function fmtGridColsList($params)
  {
    $db = $this->db;

    // id - 科目的id
    $id = $params["id"];
    $sql = "select c.id, c.caption
            from t_subject s, t_acc_fmt f, t_acc_fmt_cols c
            where s.id = '%s' 
              and s.company_id = f.company_id and s.code = f.subject_code
              and f.id = c.fmt_id and c.show_order > 0
            order by c.show_order";
    $result = [];
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "caption" => $v["caption"]
      ];
    }

    return $result;
  }

  /**
   * 编辑账样字段的显示次序
   */
  public function editFmtColShowOrder(&$params)
  {
    $db = $this->db;

    // id:科目id
    $id = $params["id"];

    // 账样字段id，以逗号分隔形成的List
    $idList = $params["idList"];

    $idArray = explode(",", $idList);

    $sql = "select company_id, code from t_subject where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("科目不存在");
    }
    $v = $data[0];
    $subjectCode = $v["code"];

    foreach ($idArray as $i => $colId) {
      $showOrder = $i + 1;

      $sql = "update t_acc_fmt_cols
              set show_order = %d
              where id = '%s' ";
      $rc = $db->execute($sql, $showOrder, $colId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    $params["subjectCode"] = $subjectCode;
    return null;
  }

  /**
   * 关联商品 - 已经设置的商品分类
   */
  public function grCategoryList($params)
  {
    $db = $this->db;

    // 供应商id
    $id = $params["id"];

    $sql = "select r.id, c.code, c.full_name
            from t_supplier_goods_range r, t_goods_category c
            where r.supplier_id = '%s' and r.g_id_type = 2
              and r.g_id = c.id
            order by c.code";
    $data = $db->query($sql, $id);
    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["full_name"]
      ];
    }

    return $result;
  }
}
