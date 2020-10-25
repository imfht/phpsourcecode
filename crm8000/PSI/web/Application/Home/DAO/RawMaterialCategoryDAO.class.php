<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 原材料分类 DAO
 *
 * @author 李静波
 */
class RawMaterialCategoryDAO extends PSIBaseExDAO
{

  private function allCategoriesInternal($db, $parentId, $rs, $params)
  {
    $result = array();
    $sql = "select id, code, name, full_name, tax_rate
            from t_raw_material_category c
            where (parent_id = '%s')
		    		";
    $queryParam = array();
    $queryParam[] = $parentId;
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParam = array_merge($queryParam, $rs[1]);
    }

    $sql .= " order by code";
    $data = $db->query($sql, $queryParam);
    foreach ($data as $i => $v) {
      $id = $v["id"];
      $result[$i]["id"] = $v["id"];
      $result[$i]["text"] = $v["name"];
      $result[$i]["code"] = $v["code"];
      $fullName = $v["full_name"];
      if (!$fullName) {
        $fullName = $v["name"];
      }
      $result[$i]["fullName"] = $fullName;
      $result[$i]["taxRate"] = $this->toTaxRate($v["tax_rate"]);

      $children = $this->allCategoriesInternal($db, $id, $rs, $params); // 自身递归调用

      $result[$i]["children"] = $children;
      $result[$i]["leaf"] = count($children) == 0;
      $result[$i]["expanded"] = true;
      $result[$i]["iconCls"] = "PSI-RawMaterialCategory";

      $result[$i]["cnt"] = $this->getRawMaterialCountWithAllSub($db, $id, $params, $rs);
    }

    return $result;
  }
  /**
   * 获得某个原材料分类及其所属子分类下的所有原材料的种类数
   */
  private function getRawMaterialCountWithAllSub($db, $categoryId, $params, $rs)
  {
    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];

    $sql = "select count(*) as cnt 
            from t_raw_material c
            where c.category_id = '%s' ";
    $queryParam = array();
    $queryParam[] = $categoryId;
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
    $result = $data[0]["cnt"];

    // 子分类
    $sql = "select id
            from t_raw_material_category c
            where (parent_id = '%s')
    				";
    $queryParam = array();
    $queryParam[] = $categoryId;
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParam = array_merge($queryParam, $rs[1]);
    }

    $data = $db->query($sql, $queryParam);
    foreach ($data as $v) {
      // 递归调用自身
      $result += $this->getRawMaterialCountWithAllSub($db, $v["id"], $params, $rs);
    }
    return $result;
  }

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
   * 把分类中原材料数量是0的分类过滤掉
   *
   * @param array $data
   * @return array
   */
  private function filterCategory($data)
  {
    $result = [];
    foreach ($data as $v) {
      if ($v["cnt"] == 0) {
        continue;
      }

      $result[] = $v;
    }

    return $result;
  }

  /**
   * 返回所有的原材料分类
   *
   */
  public function allRawMaterialCategories($params)
  {
    $db = $this->db;

    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];

    $inQuery = false;
    if ($code || $name || $spec) {
      $inQuery = true;
    }

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $sql = "select id, code, name, full_name, tax_rate
            from t_raw_material_category c
            where (parent_id is null)
            ";
    $queryParam = array();
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::RAW_MATERIAL_CATEGORY, "c", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParam = array_merge($queryParam, $rs[1]);
    }

    $sql .= " order by code";

    $data = $db->query($sql, $queryParam);
    $result = array();
    foreach ($data as $i => $v) {
      $id = $v["id"];
      $result[$i]["id"] = $v["id"];
      $result[$i]["text"] = $v["name"];
      $result[$i]["code"] = $v["code"];
      $fullName = $v["full_name"];
      if (!$fullName) {
        $fullName = $v["name"];
      }
      $result[$i]["fullName"] = $fullName;
      $result[$i]["taxRate"] = $this->toTaxRate($v["tax_rate"]);

      $children = $this->allCategoriesInternal($db, $id, $rs, $params);

      $result[$i]["children"] = $children;
      $result[$i]["leaf"] = count($children) == 0;
      $result[$i]["expanded"] = true;
      $result[$i]["iconCls"] = "PSI-RawMaterialCategory";

      $result[$i]["cnt"] = $this->getRawMaterialCountWithAllSub($db, $id, $params, $rs);
    }

    if ($inQuery) {
      $result = $this->filterCategory($result);
    }

    return $result;
  }

  /**
   * 通过原材料分类id查询原材料分类
   *
   * @param string $id
   *        	原材料分类id
   * @return array|NULL
   */
  public function getRawMaterialCategoryById($id)
  {
    $db = $this->db;

    $sql = "select code, name from t_raw_material_category where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return array(
        "code" => $data[0]["code"],
        "name" => $data[0]["name"]
      );
    } else {
      return null;
    }
  }


  /**
   * 新增原材料分类
   *
   */
  public function addRawMaterialCategory(&$params)
  {
    $db = $this->db;

    $code = trim($params["code"]);
    $name = trim($params["name"]);
    $parentId = $params["parentId"];

    $dataOrg = $params["dataOrg"];
    $companyId = $params["companyId"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    if ($this->isEmptyStringAfterTrim($code)) {
      return $this->bad("分类编码不能为空");
    }

    if ($this->isEmptyStringAfterTrim($name)) {
      return $this->bad("分类名称不能为空");
    }

    if ($parentId) {
      // 检查parentId是否存在
      $parentCategory = $this->getRawMaterialCategoryById($parentId);
      if (!$parentCategory) {
        return $this->bad("上级分类不存在");
      }
    }

    // 检查同编码的分类是否存在
    $sql = "select count(*) as cnt from t_raw_material_category where code = '%s' ";
    $data = $db->query($sql, $code);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为 [{$code}] 的分类已经存在");
    }

    $taxRate = $params["taxRate"];

    $id = $this->newId();

    if ($parentId) {
      $sql = "select full_name from t_raw_material_category where id = '%s' ";
      $data = $db->query($sql, $parentId);
      $fullName = "";
      if ($data) {
        $fullName = $data[0]["full_name"];
        $fullName .= "\\" . $name;
      }

      $sql = "insert into t_raw_material_category (id, code, name, data_org, parent_id,
                full_name, company_id)
              values ('%s', '%s', '%s', '%s', '%s', '%s', '%s')";
      $rc = $db->execute($sql, $id, $code, $name, $dataOrg, $parentId, $fullName, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      $sql = "insert into t_raw_material_category (id, code, name, data_org, full_name, company_id)
              values ('%s', '%s', '%s', '%s', '%s', '%s')";
      $rc = $db->execute($sql, $id, $code, $name, $dataOrg, $name, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    if ($taxRate == -1) {
      $sql = "update t_raw_material_category set tax_rate = null where id = '%s' ";
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
      $sql = "update t_raw_material_category set tax_rate = %d where id = '%s' ";
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
   * 同步子分类的full_name字段
   */
  private function updateSubCategoryFullName($db, $id)
  {
    $sql = "select full_name from t_raw_material_category where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return true;
    }

    $fullName = $data[0]["full_name"];
    $sql = "select id, name from t_raw_material_category where parent_id = '%s' ";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $subId = $v["id"];
      $name = $v["name"];

      $subFullName = $fullName . "\\" . $name;
      $sql = "update t_raw_material_category
              set full_name = '%s'
              where id = '%s' ";
      $rc = $db->execute($sql, $subFullName, $subId);
      if ($rc === false) {
        return false;
      }

      $rc = $this->updateSubCategoryFullName($db, $subId); // 递归调用自身
      if ($rc === false) {
        return false;
      }
    }

    return true;
  }

  /**
   * 编辑原材料分类
   *
   * @param array $params
   * @return NULL|array
   */
  public function updateRawMaterialCategory(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $code = trim($params["code"]);
    $name = trim($params["name"]);
    $parentId = $params["parentId"];
    $taxRate = $params["taxRate"];

    if ($this->isEmptyStringAfterTrim($code)) {
      return $this->bad("分类编码不能为空");
    }

    if ($this->isEmptyStringAfterTrim($name)) {
      return $this->bad("分类名称不能为空");
    }

    $category = $this->getRawMaterialCategoryById($id);
    if (!$category) {
      return $this->bad("要编辑的原材料分类不存在");
    }

    if ($parentId) {
      // 检查parentId是否存在
      $parentCategory = $this->getRawMaterialCategoryById($parentId);
      if (!$parentCategory) {
        return $this->bad("上级分类不存在");
      }
    }

    // 检查同编码的分类是否存在
    $sql = "select count(*) as cnt from t_raw_material_category where code = '%s' and id <> '%s' ";
    $data = $db->query($sql, $code, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为 [{$code}] 的分类已经存在");
    }

    if ($parentId) {
      if ($parentId == $id) {
        return $this->bad("上级分类不能是自身");
      }

      $tempParentId = $parentId;
      while ($tempParentId != null) {
        $sql = "select parent_id from t_raw_material_category where id = '%s' ";
        $d = $db->query($sql, $tempParentId);
        if ($d) {
          $tempParentId = $d[0]["parent_id"];

          if ($tempParentId == $id) {
            return $this->bad("不能选择下级分类作为上级分类");
          }
        } else {
          $tempParentId = null;
        }
      }

      $sql = "select full_name from t_raw_material_category where id = '%s' ";
      $data = $db->query($sql, $parentId);
      $fullName = $name;
      if ($data) {
        $fullName = $data[0]["full_name"] . "\\" . $name;
      }

      $sql = "update t_raw_material_category
              set code = '%s', name = '%s', parent_id = '%s', full_name = '%s'
              where id = '%s' ";
      $rc = $db->execute($sql, $code, $name, $parentId, $fullName, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      $sql = "update t_raw_material_category
              set code = '%s', name = '%s', parent_id = null, full_name = '%s'
              where id = '%s' ";
      $rc = $db->execute($sql, $code, $name, $name, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步子分类的full_name字段
    $rc = $this->updateSubCategoryFullName($db, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 税率
    if ($taxRate == -1) {
      $sql = "update t_raw_material_category set tax_rate = null where id = '%s' ";
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
      $sql = "update t_raw_material_category set tax_rate = %d where id = '%s' ";
      $rc = $db->execute($sql, $taxRate, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    return null;
  }

  /**
   * 获得某个原材料分类的详情
   */
  public function getRawMaterialCategoryInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $result = [];

    $sql = "select code, name, parent_id, tax_rate 
            from t_raw_material_category
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $v = $data[0];
      $result["code"] = $v["code"];
      $result["name"] = $v["name"];
      $result["taxRate"] = $v["tax_rate"];
      $parentId = $v["parent_id"];
      $result["parentId"] = $parentId;
      if ($parentId) {
        $sql = "select full_name from t_raw_material_category where id = '%s' ";
        $data = $db->query($sql, $parentId);
        $result["parentName"] = $data[0]["full_name"];
      } else {
        $result["parentName"] = null;
      }
    }

    return $result;
  }

  /**
   * 删除原材料分类
   */
  public function deleteRawMaterialCategory(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $category = $this->getRawMaterialCategoryById($id);

    if (!$category) {
      return $this->bad("要删除的原材料分类不存在");
    }
    $code = $category["code"];
    $name = $category["name"];

    $sql = "select count(*) as cnt from t_raw_material where category_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("还有属于分类 [{$name}] 的原材料，不能删除该分类");
    }

    // 判断是否还有子分类
    $sql = "select count(*) as cnt from t_raw_material_category
            where parent_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("分类[{$name}]还有子分类，不能删除");
    }

    $sql = "delete from t_raw_material_category where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["code"] = $code;
    $params["name"] = $name;

    // 操作成功
    return null;
  }
}
