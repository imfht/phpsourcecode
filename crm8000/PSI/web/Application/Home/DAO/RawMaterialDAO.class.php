<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 原材料 DAO
 *
 * @author 李静波
 */
class RawMaterialDAO extends PSIBaseExDAO
{
  private function toTaxRate($taxRate)
  {
    if (!$taxRate) {
      return null;
    }

    $r = intval($taxRate);
    if ($r >= 0 && $r <= 17) {
      return "{$r}%";
    } else {
      return null;
    }
  }

  /**
   * 原材料列表
   */
  public function rawMaterialList($params)
  {
    $db = $this->db;

    $categoryId = $params["categoryId"];
    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];

    $start = $params["start"];
    $limit = $params["limit"];

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $result = [];
    $sql = "select g.id, g.code, g.name, g.spec,  g.unit_id, u.name as unit_name,
              g.purchase_price, g.memo, g.data_org, g.record_status,
              g.tax_rate
            from t_raw_material g, t_material_unit u
            where (g.unit_id = u.id) and (g.category_id = '%s') ";
    $queryParam = [];
    $queryParam[] = $categoryId;
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::RAW_MATERIAL, "g", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParam = array_merge($queryParam, $rs[1]);
    }

    if ($code) {
      $sql .= " and (g.code like '%s') ";
      $queryParam[] = "%{$code}%";
    }
    if ($name) {
      $sql .= " and (g.name like '%s' or g.py like '%s') ";
      $queryParam[] = "%{$name}%";
      $queryParam[] = "%{$name}%";
    }
    if ($spec) {
      $sql .= " and (g.spec like '%s')";
      $queryParam[] = "%{$spec}%";
    }

    $sql .= " order by g.code limit %d, %d";
    $queryParam[] = $start;
    $queryParam[] = $limit;
    $data = $db->query($sql, $queryParam);

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "spec" => $v["spec"],
        "unitId" => $v["unit_id"],
        "unitName" => $v["unit_name"],
        "purchasePrice" => $v["purchase_price"] == 0 ? null : $v["purchase_price"],
        "memo" => $v["memo"],
        "dataOrg" => $v["data_org"],
        "recordStatus" => $v["record_status"],
        "taxRate" => $this->toTaxRate($v["tax_rate"])
      ];
    }

    $sql = "select count(*) as cnt from t_raw_material g where (g.category_id = '%s') ";
    $queryParam = [];
    $queryParam[] = $categoryId;
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::RAW_MATERIAL, "g", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParam = array_merge($queryParam, $rs[1]);
    }
    if ($code) {
      $sql .= " and (g.code like '%s') ";
      $queryParam[] = "%{$code}%";
    }
    if ($name) {
      $sql .= " and (g.name like '%s' or g.py like '%s') ";
      $queryParam[] = "%{$name}%";
      $queryParam[] = "%{$name}%";
    }
    if ($spec) {
      $sql .= " and (g.spec like '%s')";
      $queryParam[] = "%{$spec}%";
    }

    $data = $db->query($sql, $queryParam);
    $totalCount = $data[0]["cnt"];

    return [
      "dataList" => $result,
      "totalCount" => $totalCount
    ];
  }

  /**
   * 获得某个原材料的详情
   */
  public function getRawMaterialInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];
    $categoryId = $params["categoryId"];

    $sql = "select category_id, code, name, spec, unit_id, purchase_price,
              memo, record_status, tax_rate
            from t_raw_material
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $result = [];
      $categoryId = $data[0]["category_id"];
      $result["categoryId"] = $categoryId;

      $result["code"] = $data[0]["code"];
      $result["name"] = $data[0]["name"];
      $result["spec"] = $data[0]["spec"];
      $result["unitId"] = $data[0]["unit_id"];

      $v = $data[0]["purchase_price"];
      if ($v == 0) {
        $result["purchasePrice"] = null;
      } else {
        $result["purchasePrice"] = $v;
      }

      $result["memo"] = $data[0]["memo"];
      $result["recordStatus"] = $data[0]["record_status"];
      $result["taxRate"] = $data[0]["tax_rate"];

      $sql = "select full_name from t_raw_material_category where id = '%s' ";
      $data = $db->query($sql, $categoryId);
      if ($data) {
        $result["categoryName"] = $data[0]["full_name"];
      }

      return $result;
    } else {
      $result = [];

      $sql = "select full_name from t_raw_material_category where id = '%s' ";
      $data = $db->query($sql, $categoryId);
      if ($data) {
        $result["categoryId"] = $categoryId;
        $result["categoryName"] = $data[0]["full_name"];
      }
      return $result;
    }
  }

  /**
   * 新增原材料
   */
  public function addRawMaterial(&$params)
  {
    $db = $this->db;

    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];
    $categoryId = $params["categoryId"];
    $unitId = $params["unitId"];
    $purchasePrice = $params["purchasePrice"];
    $memo = $params["memo"];
    $recordStatus = $params["recordStatus"];
    $taxRate = $params["taxRate"];

    $dataOrg = $params["dataOrg"];
    $companyId = $params["companyId"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $py = $params["py"];
    $specPY = $params["specPY"];

    $unitDAO = new MaterialUnitDAO($db);
    $unit = $unitDAO->getMaterialUnitById($unitId);
    if (!$unit) {
      return $this->bad("物料单位不存在");
    }
    $unitRecordStatus = $unit["recordStatus"];
    if ($unitRecordStatus != 1) {
      return $this->bad("物料单位已经被停用");
    }

    $categoryDAO = new RawMaterialCategoryDAO($db);
    $category = $categoryDAO->getRawMaterialCategoryById($categoryId);
    if (!$category) {
      return $this->bad("原材料分类不存在");
    }

    // 检查编码是否唯一
    $sql = "select count(*) as cnt from t_raw_material where code = '%s' ";
    $data = $db->query($sql, $code);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为 [{$code}]的原材料已经存在");
    }

    $id = $this->newId();
    $sql = "insert into t_raw_material (id, code, name, spec, category_id, unit_id,
              py, purchase_price, memo, data_org, company_id, spec_py,
              record_status)
            values ('%s', '%s', '%s', '%s', '%s', '%s',
              '%s', %f, '%s', '%s', '%s', '%s',
              %d)";
    $rc = $db->execute(
      $sql,
      $id,
      $code,
      $name,
      $spec,
      $categoryId,
      $unitId,
      $py,
      $purchasePrice,
      $memo,
      $dataOrg,
      $companyId,
      $specPY,
      $recordStatus
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 税率
    if ($taxRate == -1) {
      $sql = "update t_raw_material set tax_rate = null where id = '%s' ";
      $rc = $db->execute($sql, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      $taxRate = intval($taxRate);
      if ($taxRate > 17) {
        $taxRate = 17;
      }
      if ($taxRate < 0) {
        $taxRate = 0;
      }
      $sql = "update t_raw_material set tax_rate = %d where id = '%s' ";
      $rc = $db->execute($sql, $taxRate, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $params["id"] = $id;

    // 操作成功
    return null;
  }

  /**
   * 通过原材料id查询原材料
   *
   * @param string $id        	
   * @return array|NULL
   */
  public function getRawMaterialById($id)
  {
    $db = $this->db;

    $sql = "select code, name, spec from t_raw_material where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return [
        "code" => $data[0]["code"],
        "name" => $data[0]["name"],
        "spec" => $data[0]["spec"]
      ];
    } else {
      return null;
    }
  }

  /**
   * 编辑原材料
   */
  public function updateRawMaterial(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];
    $categoryId = $params["categoryId"];
    $unitId = $params["unitId"];
    $purchasePrice = $params["purchasePrice"];
    $memo = $params["memo"];
    $recordStatus = $params["recordStatus"];
    $taxRate = $params["taxRate"];

    $py = $params["py"];
    $specPY = $params["specPY"];

    $rm = $this->getRawMaterialById($id);
    if (!$rm) {
      return $this->bad("要编辑的原材料不存在");
    }

    $unitDAO = new MaterialUnitDAO($db);
    $unit = $unitDAO->getMaterialUnitById($unitId);
    if (!$unit) {
      return $this->bad("单位不存在");
    }
    $unitRecordStatus = $unit["recordStatus"];
    if ($unitRecordStatus == 2) {
      // 计量单位被停用的时候，原材料的状态不能是启用
      if (intval($recordStatus) == 1000) {
        $unitName = $unit["name"];
        return $this->bad("单位[{$unitName}]被停用的时候，原材料的状态不能是启用");
      }
    }

    $categoryDAO = new RawMaterialCategoryDAO($db);
    $category = $categoryDAO->getRawMaterialCategoryById($categoryId);
    if (!$category) {
      return $this->bad("原材料分类不存在");
    }

    // 编辑
    // 检查编码是否唯一
    $sql = "select count(*) as cnt from t_raw_material where code = '%s' and id <> '%s' ";
    $data = $db->query($sql, $code, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为 [{$code}]的原材料已经存在");
    }

    $sql = "update t_raw_material
            set code = '%s', name = '%s', spec = '%s', category_id = '%s',
              unit_id = '%s', py = '%s', purchase_price = %f,
              memo = '%s', spec_py = '%s',
              record_status = %d
            where id = '%s' ";

    $rc = $db->execute(
      $sql,
      $code,
      $name,
      $spec,
      $categoryId,
      $unitId,
      $py,
      $purchasePrice,
      $memo,
      $specPY,
      $recordStatus,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 税率
    if ($taxRate == -1) {
      $sql = "update t_raw_material set tax_rate = null where id = '%s' ";
      $rc = $db->execute($sql, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      $taxRate = intval($taxRate);
      if ($taxRate > 17) {
        $taxRate = 17;
      }
      if ($taxRate < 0) {
        $taxRate = 0;
      }
      $sql = "update t_raw_material set tax_rate = %d where id = '%s' ";
      $rc = $db->execute($sql, $taxRate, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    return null;
  }

  /**
   * 查询原材料种类总数
   *
   * @param array $params        	
   * @return int
   */
  public function getTotalRawMaterialCount($params)
  {
    $db = $this->db;

    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];

    $loginUserId = $params["loginUserId"];

    $sql = "select count(*) as cnt
            from t_raw_material c
            where (1 = 1) ";
    $queryParam = array();
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::RAW_MATERIAL, "c", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParam = array_merge($queryParam, $rs[1]);
    }
    if ($code) {
      $sql .= " and (c.code like '%s') ";
      $queryParam[] = "%{$code}%";
    }
    if ($name) {
      $sql .= " and (c.name like '%s' or c.py like '%s') ";
      $queryParam[] = "%{$name}%";
      $queryParam[] = "%{$name}%";
    }
    if ($spec) {
      $sql .= " and (c.spec like '%s')";
      $queryParam[] = "%{$spec}%";
    }
    $data = $db->query($sql, $queryParam);

    return [
      "cnt" => $data[0]["cnt"]
    ];
  }

  /**
   * 删除原材料
   */
  public function deleteRawMaterial(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $rm = $this->getRawMaterialById($id);
    if (!$rm) {
      return $this->bad("要删除的原材料不存在");
    }
    $code = $rm["code"];
    $name = $rm["name"];
    $spec = $rm["spec"];

    // TODO 判断是否能删除
    return $this->todo("需要判断原材料是否能删除");
  }
}
