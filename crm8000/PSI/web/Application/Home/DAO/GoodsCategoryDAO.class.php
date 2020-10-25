<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 商品分类 DAO
 *
 * @author 李静波
 */
class GoodsCategoryDAO extends PSIBaseExDAO
{

  private function allCategoriesInternal($db, $parentId, $rs, $params)
  {
    $result = array();
    $sql = "select id, code, name, full_name, tax_rate, m_type
            from t_goods_category c
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
      $result[$i]["mType"] = $this->categoryMTypeCodeToName($v["m_type"]);

      $children = $this->allCategoriesInternal($db, $id, $rs, $params); // 自身递归调用

      $result[$i]["children"] = $children;
      $result[$i]["leaf"] = count($children) == 0;
      $result[$i]["expanded"] = true;
      $result[$i]["iconCls"] = "PSI-GoodsCategory";

      $result[$i]["cnt"] = $this->getGoodsCountWithAllSub($db, $id, $params, $rs);
    }

    return $result;
  }

  /**
   * 获得某个商品分类及其所属子分类下的所有商品的种类数
   */
  private function getGoodsCountWithAllSub($db, $categoryId, $params, $rs)
  {
    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];
    $barCode = $params["barCode"];
    $brandId = $params["brandId"];

    $sql = "select count(*) as cnt 
            from t_goods c
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
    if ($barCode) {
      $sql .= " and (c.bar_code = '%s') ";
      $queryParam[] = $barCode;
    }
    if ($brandId) {
      $sql .= " and (c.brand_id = '%s') ";
      $queryParam[] = $brandId;
    }

    $data = $db->query($sql, $queryParam);
    $result = $data[0]["cnt"];

    // 子分类
    $sql = "select id
            from t_goods_category c
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
      $result += $this->getGoodsCountWithAllSub($db, $v["id"], $params, $rs);
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

  private function categoryMTypeCodeToName($code)
  {
    switch ($code) {
      case -1:
        return "不限";
      case 1000:
        return "原材料";
      case 2000:
        return "半成品";
      case 3000:
        return "产成品";
      case 4000:
        return "商品";
      default:
        return "";
    }
  }

  /**
   * 返回所有的商品分类
   *
   * @param array $params
   * @return array
   */
  public function allCategories($params)
  {
    $db = $this->db;

    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];
    $barCode = $params["barCode"];
    $brandId = $params["brandId"];

    $inQuery = false;
    if ($code || $name || $spec || $barCode || $brandId) {
      $inQuery = true;
    }

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $sql = "select id, code, name, full_name, tax_rate, m_type
            from t_goods_category c
            where (parent_id is null)
            ";
    $queryParam = array();
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::GOODS_CATEGORY, "c", $loginUserId);
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
      $result[$i]["mType"] = $this->categoryMTypeCodeToName($v["m_type"]);

      $children = $this->allCategoriesInternal($db, $id, $rs, $params);

      $result[$i]["children"] = $children;
      $result[$i]["leaf"] = count($children) == 0;
      $result[$i]["expanded"] = true;
      $result[$i]["iconCls"] = "PSI-GoodsCategory";

      $result[$i]["cnt"] = $this->getGoodsCountWithAllSub($db, $id, $params, $rs);
    }

    if ($inQuery) {
      $result = $this->filterCategory($result);
    }

    return $result;
  }

  /**
   * 把分类中商品数量是0的分类过滤掉
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
   * 通过商品分类id查询商品分类
   *
   * @param string $id
   *        	商品分类id
   * @return array|NULL
   */
  public function getGoodsCategoryById($id)
  {
    $db = $this->db;

    $sql = "select code, name, m_type from t_goods_category where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return array(
        "code" => $data[0]["code"],
        "name" => $data[0]["name"],
        "mType" => $data[0]["m_type"],
      );
    } else {
      return null;
    }
  }

  /**
   * 新增商品分类
   *
   * @param array $params
   * @return NULL|array
   */
  public function addGoodsCategory(&$params)
  {
    $db = $this->db;

    $code = trim($params["code"]);
    $name = trim($params["name"]);
    $parentId = $params["parentId"];
    $mType = $params["mType"];

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
      $parentCategory = $this->getGoodsCategoryById($parentId);
      if (!$parentCategory) {
        return $this->bad("上级分类不存在");
      }
    }

    // 检查同编码的分类是否存在
    $sql = "select count(*) as cnt from t_goods_category where code = '%s' ";
    $data = $db->query($sql, $code);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为 [{$code}] 的分类已经存在");
    }

    $taxRate = $params["taxRate"];

    $id = $this->newId();

    if ($parentId) {
      $sql = "select full_name from t_goods_category where id = '%s' ";
      $data = $db->query($sql, $parentId);
      $fullName = "";
      if ($data) {
        $fullName = $data[0]["full_name"];
        $fullName .= "\\" . $name;
      }

      $sql = "insert into t_goods_category (id, code, name, data_org, parent_id,
                full_name, company_id, m_type)
              values ('%s', '%s', '%s', '%s', '%s', '%s', '%s', %d)";
      $rc = $db->execute($sql, $id, $code, $name, $dataOrg, $parentId, $fullName, $companyId, $mType);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      $sql = "insert into t_goods_category (id, code, name, data_org, full_name, company_id, m_type)
              values ('%s', '%s', '%s', '%s', '%s', '%s', %d)";
      $rc = $db->execute($sql, $id, $code, $name, $dataOrg, $name, $companyId, $mType);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    if ($taxRate == -1) {
      $sql = "update t_goods_category set tax_rate = null where id = '%s' ";
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
      $sql = "update t_goods_category set tax_rate = %d where id = '%s' ";
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
    $sql = "select full_name from t_goods_category where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return true;
    }

    $fullName = $data[0]["full_name"];
    $sql = "select id, name from t_goods_category where parent_id = '%s' ";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $subId = $v["id"];
      $name = $v["name"];

      $subFullName = $fullName . "\\" . $name;
      $sql = "update t_goods_category
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
   * 编辑商品分类
   *
   * @param array $params
   * @return NULL|array
   */
  public function updateGoodsCategory(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $code = trim($params["code"]);
    $name = trim($params["name"]);
    $parentId = $params["parentId"];
    $taxRate = $params["taxRate"];
    $mType = $params["mType"];

    if ($this->isEmptyStringAfterTrim($code)) {
      return $this->bad("分类编码不能为空");
    }

    if ($this->isEmptyStringAfterTrim($name)) {
      return $this->bad("分类名称不能为空");
    }

    $category = $this->getGoodsCategoryById($id);
    if (!$category) {
      return $this->bad("要编辑的商品分类不存在");
    }

    if ($parentId) {
      // 检查parentId是否存在
      $parentCategory = $this->getGoodsCategoryById($parentId);
      if (!$parentCategory) {
        return $this->bad("上级分类不存在");
      }
    }

    // 检查同编码的分类是否存在
    $sql = "select count(*) as cnt from t_goods_category where code = '%s' and id <> '%s' ";
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
        $sql = "select parent_id from t_goods_category where id = '%s' ";
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

      $sql = "select full_name from t_goods_category where id = '%s' ";
      $data = $db->query($sql, $parentId);
      $fullName = $name;
      if ($data) {
        $fullName = $data[0]["full_name"] . "\\" . $name;
      }

      $sql = "update t_goods_category
              set code = '%s', name = '%s', parent_id = '%s', full_name = '%s',
                m_type = %d
              where id = '%s' ";
      $rc = $db->execute($sql, $code, $name, $parentId, $fullName, $mType, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      $sql = "update t_goods_category
              set code = '%s', name = '%s', parent_id = null, full_name = '%s',
                m_type = %d
              where id = '%s' ";
      $rc = $db->execute($sql, $code, $name, $name, $mType, $id);
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
      $sql = "update t_goods_category set tax_rate = null where id = '%s' ";
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
      $sql = "update t_goods_category set tax_rate = %d where id = '%s' ";
      $rc = $db->execute($sql, $taxRate, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    return null;
  }

  /**
   * 删除商品分类
   *
   * @param array $params
   * @return NULL|array
   */
  public function deleteCategory(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $category = $this->getGoodsCategoryById($id);

    if (!$category) {
      return $this->bad("要删除的商品分类不存在");
    }
    $code = $category["code"];
    $name = $category["name"];

    $sql = "select count(*) as cnt from t_goods where category_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("还有属于商品分类 [{$name}] 的商品，不能删除该分类");
    }

    // 判断是否还有子分类
    $sql = "select count(*) as cnt from t_goods_category
            where parent_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("分类[{$name}]还有子分类，不能删除");
    }

    $sql = "delete from t_goods_category where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["code"] = $code;
    $params["name"] = $name;

    // 操作成功
    return null;
  }

  /**
   * 获得某个商品分类的详情
   *
   * @param array $params
   * @return array
   */
  public function getCategoryInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $result = array();

    $sql = "select code, name, parent_id, tax_rate, m_type 
            from t_goods_category
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
        $sql = "select full_name from t_goods_category where id = '%s' ";
        $data = $db->query($sql, $parentId);
        $result["parentName"] = $data[0]["full_name"];
      } else {
        $result["parentName"] = null;
      }
      $result["mType"] = $v["m_type"];
    }

    return $result;
  }
}
