<?php

namespace Home\Service;

use Think\Exception;

// require_once __DIR__ . '/../Common/Excel/PHPExcel/IOFactory.php';

/**
 * PHPExcel文件 Service
 *
 * @author James(张健)
 */
class ImportService extends PSIBaseService
{

  /**
   * 商品导入Service
   *
   * @param
   *        	$params
   * @return array
   * @throws \PHPExcel_Exception
   */
  public function importGoodsFromExcelFile($params)
  {
    $dataFile = $params["datafile"];
    $ext = $params["ext"];
    $message = "";
    if (!$dataFile || !$ext)
      return $this->bad("上传Excel文件失败，请重新上传");

    $inputFileType = 'Excel5';
    if ($ext == 'xlsx')
      $inputFileType = 'Excel2007';

    // 设置php服务器可用内存，上传较大文件时可能会用到
    ini_set('memory_limit', '1024M');
    ini_set('max_execution_time', 300); // 300 seconds = 5 minutes
    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
    // 设置只读，可取消类似"3.08E-05"之类自动转换的数据格式，避免写库失败
    $objReader->setReadDataOnly(true);

    // 载入文件
    $objPHPExcel = $objReader->load($dataFile);
    // 获取表中的第一个工作表
    $currentSheet = $objPHPExcel->getSheet(0);
    // 获取总行数
    $allRow = $currentSheet->getHighestRow();

    // 如果没有数据行，直接返回
    if ($allRow < 2) {
      return $this->bad("Excel中没有数据，无法导入");
    }

    $ps = new PinyinService();
    $idGen = new IdGenService();
    $bs = new BizlogService();
    $gs = new GoodsService();
    $db = M();
    $units = array(); // 将计量单位缓存，以免频繁访问数据库
    $categories = array(); // 同上
    $params = array(); // 数据参数

    $us = new UserService();
    $dataOrg = $us->getLoginUserDataOrg();

    $insertSql = "insert into t_goods (id, code, name, spec, category_id, unit_id, sale_price,	py, 
					            purchase_price, bar_code, data_org, memo, spec_py) values";
    $dataSql = "('%s', '%s', '%s', '%s', '%s', '%s', %f, '%s', %f, '%s', '%s', '%s', '%s'),";
    /**
     * 单元格定义
     * A 商品分类编码
     * B 商品编码
     * C 商品名称
     * D 规格型号
     * E 计量单位
     * F 销售单价
     * G 建议采购单价
     * H 条形码
     * I 备注
     */
    // 从第2行获取数据
    for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
      // 数据坐标
      $indexCategory = 'A' . $currentRow;
      $indexCode = 'B' . $currentRow;
      $indexName = 'C' . $currentRow;
      $indexSpec = 'D' . $currentRow;
      $indexUnit = 'E' . $currentRow;
      $indexSalePrice = 'F' . $currentRow;
      $indexPurchasePrice = 'G' . $currentRow;
      $indexBarcode = 'H' . $currentRow;
      $indexMemo = 'I' . $currentRow;
      // 读取到的数据，保存到数组$arr中
      $category = $currentSheet->getCell($indexCategory)->getValue();
      $code = $currentSheet->getCell($indexCode)->getValue();
      $name = $currentSheet->getCell($indexName)->getValue();
      $spec = $currentSheet->getCell($indexSpec)->getValue();
      $unit = $currentSheet->getCell($indexUnit)->getValue();
      $salePrice = $currentSheet->getCell($indexSalePrice)->getValue();
      $purchasePrice = $currentSheet->getCell($indexPurchasePrice)->getValue();
      $barcode = $currentSheet->getCell($indexBarcode)->getValue();
      $memo = $currentSheet->getCell($indexMemo)->getValue();

      // 如果为空则直接读取下一条记录
      if (!$category || !$code || !$name || !$unit)
        continue;

      $unitId = null;
      $categoryId = null;

      if ($units["{$unit}"]) {
        $unitId = $units["{$unit}"];
      } else {
        $sql = "select id, `name` from t_goods_unit where `name` = '%s' ";
        $data = $db->query($sql, $unit);
        if (!$data) {
          // 新增计量单位
          $newUnitParams = array(
            "name" => $unit
          );
          $newUnit = $gs->editUnit($newUnitParams);
          $unitId = $newUnit["id"];
        } else {
          $unitId = $data[0]["id"];
        }
        $units += array(
          "{$unit}" => "{$unitId}"
        );
      }

      if ($categories["{$category}"]) {
        $categoryId = $categories["{$category}"];
      } else {
        $sql = "select id, code from t_goods_category where code = '%s' ";
        $data = $db->query($sql, $category);
        if (!$data) {
          // 商品分类不存在
          $message .= "Excel中第{$currentRow}行记录 商品分类不存在，不能导入<br/><br/>";
          continue;
        } else {
          $categoryId = $data[0]["id"];
        }
        $categories += array(
          "{$category}" => "{$categoryId}"
        );
      }

      // 新增
      // 检查商品编码是否唯一
      $sql = "select 1  from t_goods where code = '%s' ";
      $data = $db->query($sql, $code);
      if ($data) {
        $message .= "Excel中第{$currentRow}行记录 商品: 商品编码 = {$code}, 品名 = {$name}, 规格型号 = {$spec} 已存在，不能导入<br/><br/>";
        continue;
      }

      // 如果录入了条形码，则需要检查条形码是否唯一
      if ($barcode) {
        $sql = "select 1  from t_goods where bar_code = '%s' ";
        $data = $db->query($sql, $barcode);
        if ($data) {
          $message .= "Excel中第{$currentRow}行记录 商品[ 商品编码 = {$code}, 品名 = {$name}, 规格型号 = {$spec}]的条形码 = {$barcode} 已存在，不能导入<br/><br/>";
          continue;
        }
      }

      $id = $idGen->newId();
      $py = $ps->toPY($name);
      $specPY = $ps->toPY($spec);

      $insertSql .= $dataSql;
      // 数据参数加入
      array_push(
        $params,
        $id,
        $code,
        $name,
        $spec,
        $categoryId,
        $unitId,
        $salePrice,
        $py,
        $purchasePrice,
        $barcode,
        $dataOrg,
        $memo,
        $specPY
      );
    } // end of for

    if ($message) {
      // 这个时候，Excel中有部分不能导入的数据
      if (mb_strlen($message) >  300) {
        // 防止太多的错误信息让前端页面乱，最大就返回300个字符的错误信息
        $message = mb_substr($message, 0, 300) . "......(错误信息超过300个字符，后面的信息已经省略)";
      }

      return $this->bad($message);
    }

    // 存在这种情况：所有的数据都非法，这样SQL就是不完整的，执行就会出错
    if (count($params) == 0) {
      return $this->bad("没有合格的数据可以导入");
    }

    $db->execute(rtrim($insertSql, ','), $params);
    $log = "以导入Excel方式新增商品";
    $bs->insertBizlog($log, "基础数据-商品");

    return $this->ok();
  }

  /**
   * 客户导入Service
   *
   * @param
   *        	$params
   * @return array
   * @throws \PHPExcel_Exception
   */
  public function importCustomerFromExcelFile($params)
  {
    $dataFile = $params["datafile"];
    $ext = $params["ext"];
    $message = "";
    $success = true;
    $result = array(
      "msg" => $message,
      "success" => $success
    );

    if (!$dataFile || !$ext)
      return $result;

    $inputFileType = 'Excel5';
    if ($ext == 'xlsx')
      $inputFileType = 'Excel2007';

    // 设置php服务器可用内存，上传较大文件时可能会用到
    ini_set('memory_limit', '1024M');
    // Deal with the Fatal error: Maximum execution time of 30 seconds exceeded
    ini_set('max_execution_time', 300); // 300 seconds = 5 minutes
    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
    // 设置只读，可取消类似"3.08E-05"之类自动转换的数据格式，避免写库失败
    $objReader->setReadDataOnly(true);

    try {
      // 载入文件
      $objPHPExcel = $objReader->load($dataFile);
      // 获取表中的第一个工作表
      $currentSheet = $objPHPExcel->getSheet(0);
      // 获取总行数
      $allRow = $currentSheet->getHighestRow();

      // 如果没有数据行，直接返回
      if ($allRow < 2)
        return $result;

      $ps = new PinyinService();
      $idGen = new IdGenService();
      $bs = new BizlogService();
      $db = M();
      $categories = array(); // 同上
      $params = array(); // 数据参数

      $us = new UserService();
      $dataOrg = $us->getLoginUserDataOrg();
      $companyId = $us->getCompanyId();

      $insertSql = "
				insert into t_customer (id, category_id, code, name, py,
					contact01, qq01, tel01, mobile01, contact02, qq02, tel02, mobile02, address,
					address_shipping, address_receipt,
					bank_name, bank_account, tax_number, fax, note, data_org)
				values('%s', '%s', '%s', '%s', '%s',
					'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
					'%s', '%s',
					'%s', '%s', '%s', '%s', '%s', '%s')";
      /**
       * 单元格定义
       * A category 客户分类编码
       * B code 客户编码
       * C name 客户名称 -- py 客户名称的拼音字头
       * D contact01 联系人
       * E tel01 联系人固话
       * F qq01 联系人QQ号
       * G mobile01 联系人手机
       * H contact02 备用联系人
       * I tel02 备用联系人固话
       * J qq02 备用联系人QQ号
       * K mobile02 备用联系人手机
       * L address 地址
       * M init_receivables 期初应收账款
       * N init_receivables_dt 期初应收账款截止日期
       * O address_shipping 发货地址
       * P address_receipt 收货地址
       * Q bank_name 开户行
       * R bank_account 开户行账号
       * S tax_number 税号
       * T fax 传真
       * U note 备注
       */
      // 从第2行获取数据
      for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
        // 数据坐标
        $indexCategory = 'A' . $currentRow;
        $indexCode = 'B' . $currentRow;
        $indexName = 'C' . $currentRow;
        $indexContact01 = 'D' . $currentRow;
        $indexTel01 = 'E' . $currentRow;
        $indexQQ01 = 'F' . $currentRow;
        $indexMobile01 = 'G' . $currentRow;
        $indexContact02 = 'H' . $currentRow;
        $indexTel02 = 'I' . $currentRow;
        $indexQQ02 = 'J' . $currentRow;
        $indexMobile02 = 'K' . $currentRow;
        $indexAddress = 'L' . $currentRow;
        $indexInitReceivables = 'M' . $currentRow;
        $indexInitReceivablesDt = 'N' . $currentRow;
        $indexAddressShipping = 'O' . $currentRow;
        $indexAddressReceipt = 'P' . $currentRow;
        $indexBankName = 'Q' . $currentRow;
        $indexBankAccount = 'R' . $currentRow;
        $indexTaxNumber = 'S' . $currentRow;
        $indexFax = 'T' . $currentRow;
        $indexNote = 'U' . $currentRow;
        // 读取到的数据，保存到数组$arr中
        $category = $currentSheet->getCell($indexCategory)->getValue();
        $code = $currentSheet->getCell($indexCode)->getValue();
        $name = $currentSheet->getCell($indexName)->getValue();
        $contact01 = $currentSheet->getCell($indexContact01)->getValue();
        $tel01 = $currentSheet->getCell($indexTel01)->getValue();
        $qq01 = $currentSheet->getCell($indexQQ01)->getValue();
        $mobile01 = $currentSheet->getCell($indexMobile01)->getValue();
        $contact02 = $currentSheet->getCell($indexContact02)->getValue();
        $tel02 = $currentSheet->getCell($indexTel02)->getValue();
        $qq02 = $currentSheet->getCell($indexQQ02)->getValue();
        $mobile02 = $currentSheet->getCell($indexMobile02)->getValue();
        $address = $currentSheet->getCell($indexAddress)->getValue();
        $initReceivables = $currentSheet->getCell($indexInitReceivables)->getValue();
        $initRDTValue = $currentSheet->getCell($indexInitReceivablesDt)->getValue();
        $initReceivablesDT = null;
        if ($initRDTValue) {
          $intRDTSeconds = intval(($initRDTValue - 25569) * 3600 * 24); // 转换成1970年以来的秒数
          $initReceivablesDT = gmdate('Y-m-d', $intRDTSeconds);
          if ($initReceivablesDT == "1970-01-01") {
            $initReceivablesDT = null;
          }
        }; // 格式化日期
        $addressShipping = $currentSheet->getCell($indexAddressShipping)->getValue();
        $addressReceipt = $currentSheet->getCell($indexAddressReceipt)->getValue();
        $bankName = $currentSheet->getCell($indexBankName)->getValue();
        $bankAccount = $currentSheet->getCell($indexBankAccount)->getValue();
        $taxNumber = $currentSheet->getCell($indexTaxNumber)->getValue();
        $fax = $currentSheet->getCell($indexFax)->getValue();
        $note = $currentSheet->getCell($indexNote)->getValue();

        // 如果为空则直接读取下一条记录
        if (!$category || !$code || !$name)
          continue;

        $categoryId = null;

        if ($categories["{$category}"]) {
          $categoryId = $categories["{$category}"];
        } else {
          $sql = "select id, code from t_customer_category where code = '%s' ";
          $data = $db->query($sql, $category);
          if (!$data) {
            // 新增分类
            continue;
          } else {
            $categoryId = $data[0]["id"];
          }
          $categories += array(
            "{$category}" => "{$categoryId}"
          );
        }

        // 新增
        // 检查商品编码是否唯一
        $sql = "select 1 from t_customer where code = '%s' ";
        $data = $db->query($sql, $code);
        if ($data) {
          $message .= "编码为 [{$code}] 的客户已经存在; \r\n";
          continue;
        }

        $id = $idGen->newId();
        $py = $ps->toPY($name);

        $db->execute(
          $insertSql,
          $id,
          $categoryId,
          $code,
          $name,
          $py,
          $contact01,
          $qq01,
          $tel01,
          $mobile01,
          $contact02,
          $qq02,
          $tel02,
          $mobile02,
          $address,
          $addressShipping,
          $addressReceipt,
          $bankName,
          $bankAccount,
          $taxNumber,
          $fax,
          $note,
          $dataOrg
        );

        // 处理应收账款
        $initReceivables = floatval($initReceivables);

        if ($initReceivables && $initReceivablesDT && $this->dateIsValid($initReceivablesDT)) {
          $sql = "select count(*) as cnt
					from t_receivables_detail
					where ca_id = '%s' and ca_type = 'customer' and ref_type <> '应收账款期初建账' 
							and company_id = '%s' ";
          $data = $db->query($sql, $id, $companyId);
          $cnt = $data[0]["cnt"];
          if ($cnt > 0) {
            // 已经有应收业务发生，就不再更改期初数据
            continue;
          }

          $sql = "update t_customer
							set init_receivables = %f, init_receivables_dt = '%s'
							where id = '%s' ";
          $db->execute($sql, $initReceivables, $initReceivablesDT, $id);

          // 应收明细账
          $sql = "select id from t_receivables_detail
							where ca_id = '%s' and ca_type = 'customer' and ref_type = '应收账款期初建账' 
							and company_id = '%s' ";
          $data = $db->query($sql, $id, $companyId);
          if ($data) {
            $rvId = $data[0]["id"];
            $sql = "update t_receivables_detail
								set rv_money = %f, act_money = 0, balance_money = %f, biz_date ='%s', 
									date_created = now()
								where id = '%s' ";
            $db->execute(
              $sql,
              $initReceivables,
              $initReceivables,
              $initReceivablesDT,
              $rvId
            );
          } else {
            $idGen = new IdGenService();
            $rvId = $idGen->newId();
            $sql = "insert into t_receivables_detail (id, rv_money, act_money, balance_money,
						biz_date, date_created, ca_id, ca_type, ref_number, ref_type, company_id)
						values ('%s', %f, 0, %f, '%s', now(), '%s', 'customer', '%s', '应收账款期初建账', '%s') ";
            $db->execute(
              $sql,
              $rvId,
              $initReceivables,
              $initReceivables,
              $initReceivablesDT,
              $id,
              $id,
              $companyId
            );
          }

          // 应收总账
          $sql = "select id from t_receivables 
							where ca_id = '%s' and ca_type = 'customer' 
								and company_id = '%s' ";
          $data = $db->query($sql, $id, $companyId);
          if ($data) {
            $rvId = $data[0]["id"];
            $sql = "update t_receivables
							set rv_money = %f, act_money = 0, balance_money = %f
							where id = '%s' ";
            $db->execute($sql, $initReceivables, $initReceivables, $rvId);
          } else {
            $idGen = new IdGenService();
            $rvId = $idGen->newId();
            $sql = "insert into t_receivables (id, rv_money, act_money, balance_money,
								ca_id, ca_type, company_id) 
								values ('%s', %f, 0, %f, '%s', 'customer', '%s')";
            $db->execute(
              $sql,
              $rvId,
              $initReceivables,
              $initReceivables,
              $id,
              $companyId
            );
          }
        }
      } // for

      $log = "导入方式新增客户";
      $bs->insertBizlog($log, "客户关系-客户资料");
    } catch (Exception $e) {
      $success = false;
      $message = $e;
    }

    return array(
      "msg" => $message,
      "success" => $success
    );
  }
}
