<?php

namespace Home\DAO;

/**
 * 价格体系 DAO
 *
 * @author 李静波
 */
class PriceSystemDAO extends PSIBaseExDAO
{
  private $LOG_CATEGORY = "价格体系";

  /**
   * 价格列表
   */
  public function priceSystemList($params)
  {
    $db = $this->db;

    $sql = "select id, name, factor 
				from t_price_system
				order by name";

    $result = [];
    $data = $db->query($sql);

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "name" => $v["name"],
        "factor" => $v["factor"]
      ];
    }

    return $result;
  }

  /**
   * 新增价格体系
   *
   * @param array $params        	
   * @return NULL|array
   */
  public function addPriceSystem(&$params)
  {
    $db = $this->db;

    $name = $params["name"];
    $factor = $params["factor"];

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    $dataOrg = $params["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }

    $factor = floatval($factor);
    if ($factor < 0) {
      return $this->bad("基准价格倍数不能是负数");
    }

    // 检查价格是否已经存在
    $sql = "select count(*) as cnt 
            from t_price_system 
            where name = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $name, $companyId);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("价格[$name]已经存在");
    }

    $id = $this->newId($db);

    $sql = "insert into t_price_system(id, name, data_org, company_id, factor)
            values ('%s', '%s', '%s', '%s', %f)";
    $rc = $db->execute($sql, $id, $name, $dataOrg, $companyId, $factor);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["id"] = $id;
    // 操作成功
    return null;
  }

  /**
   * 编辑价格体系
   *
   * @param array $params        	
   * @return NULL|array
   */
  public function updatePriceSystem(&$params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $id = $params["id"];
    $name = $params["name"];
    $factor = $params["factor"];

    $factor = floatval($factor);
    if ($factor < 0) {
      return $this->bad("基准价格倍数不能是负数");
    }

    // 检查价格是否已经存在
    $sql = "select count(*) as cnt from t_price_system
            where name = '%s' and id <> '%s' and company_id = '%s' ";
    $data = $db->query($sql, $name, $id, $companyId);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("价格[$name]已经存在");
    }

    $sql = "update t_price_system
            set name = '%s', factor = %f
            where id = '%s' ";

    $rc = $db->execute($sql, $name, $factor, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  public function getPriceSystemById($id)
  {
    $db = $this->db;
    $sql = "select name from t_price_system where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    } else {
      return [
        "name" => $data[0]["name"]
      ];
    }
  }

  /**
   * 删除价格
   */
  public function deletePriceSystem(&$params)
  {
    $id = $params["id"];

    $db = $this->db;

    // 检查要删除的价格是否存在
    $priceSystem = $this->getPriceSystemById($id);
    if (!$priceSystem) {
      return $this->bad("要删除的价格不存在");
    }

    $name = $priceSystem["name"];
    // 检查该价格是否已经被使用
    $sql = "select count(*) as cnt from t_customer_category
            where ps_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("价格[$name]在客户分类中使用了，不能删除");
    }

    $sql = "delete from t_price_system where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["name"] = $name;

    // 删除成功
    return null;
  }

  /**
   * 查询某个商品的所有价格体系里面的价格列表
   */
  public function goodsPriceSystemList($params)
  {
    $db = $this->db;

    // id: 商品id
    $id = $params["id"];

    $sql = "select p.name, g.price
            from t_price_system p
            left join  t_goods_price g
              on p.id = g.ps_id
                and g.goods_id = '%s' ";
    $data = $db->query($sql, $id);

    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "name" => $v["name"],
        "price" => $v["price"]
      ];
    }

    return $result;
  }

  /**
   * 查询某个商品的价格体系中所有价格的值
   */
  public function goodsPriceSystemInfo($params)
  {
    $db = $this->db;

    // id: 商品id
    $id = $params["id"];

    $sql = "select p.id, p.name, p.factor, g.price
            from t_price_system p
            left join  t_goods_price g
              on p.id = g.ps_id
                and g.goods_id = '%s' ";
    $data = $db->query($sql, $id);

    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "name" => $v["name"],
        "factor" => $v["factor"],
        "price" => $v["price"]
      ];
    }

    $sql = "select sale_price from t_goods
            where id = '%s' ";
    $data = $db->query($sql, $id);
    $baseSalePrice = $data[0]["sale_price"];

    return [
      "priceList" => $result,
      "baseSalePrice" => $baseSalePrice
    ];
  }

  /**
   * 设置商品价格体系中的价格
   */
  public function editGoodsPriceSystem(&$bill)
  {
    $db = $this->db;

    $dataOrg = $bill["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    $companyId = $bill["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $goodsId = $bill["id"];
    $baseSalePrice = $bill["basePrice"];

    $sql = "select code, name, spec from t_goods
            where id = '%s' ";
    $data = $db->query($sql, $goodsId);
    if (!$data) {
      return $this->bad("商品不存在");
    }

    $code = $data[0]["code"];
    $name = $data[0]["name"];
    $spec = $data[0]["spec"];

    $sql = "update t_goods
            set sale_price = %f
            where id = '%s' ";
    $rc = $db->execute($sql, $baseSalePrice, $goodsId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_goods_price where goods_id = '%s' ";
    $rc = $db->execute($sql, $goodsId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $items = $bill["items"];

    foreach ($items as $v) {
      $psId = $v["id"];
      $price = $v["price"];

      $id = $this->newId($db);

      $sql = "insert into t_goods_price (id, goods_id, ps_id, price, data_org, company_id)
              values ('%s', '%s', '%s', %f, '%s', '%s')";
      $rc = $db->execute($sql, $id, $goodsId, $psId, $price, $dataOrg, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $bill["code"] = $code;
    $bill["name"] = $name;
    $bill["spec"] = $spec;

    // 操作成功
    return null;
  }
}
