<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\CustomerService;
use Home\Service\ImportService;
use Home\Service\UserService;

require_once __DIR__ . '/../Common/Excel/PHPExcel/IOFactory.php';

/**
 * 客户资料Controller
 *
 * @author 李静波
 *        
 */
class CustomerController extends PSIBaseController
{

  /**
   * 客户资料 - 主页面
   */
  public function index()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::CUSTOMER)) {
      $this->initVar();

      $this->assign("title", "客户资料");

      $this->assign(
        "pAddCategory",
        $us->hasPermission(FIdConst::CUSTOMER_CATEGORY_ADD) ? 1 : 0
      );
      $this->assign(
        "pEditCategory",
        $us->hasPermission(FIdConst::CUSTOMER_CATEGORY_EDIT) ? 1 : 0
      );
      $this->assign(
        "pDeleteCategory",
        $us->hasPermission(FIdConst::CUSTOMER_CATEGORY_DELETE) ? 1 : 0
      );
      $this->assign("pAddCustomer", $us->hasPermission(FIdConst::CUSTOMER_ADD) ? 1 : 0);
      $this->assign("pEditCustomer", $us->hasPermission(FIdConst::CUSTOMER_EDIT) ? 1 : 0);
      $this->assign("pDeleteCustomer", $us->hasPermission(FIdConst::CUSTOMER_DELETE) ? 1 : 0);
      $this->assign("pImportCustomer", $us->hasPermission(FIdConst::CUSTOMER_IMPORT) ? 1 : 0);

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Customer/index");
    }
  }

  /**
   * 获得客户分类列表
   */
  public function categoryList()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::CUSTOMER)) {
        die("没有权限");
      }

      $cs = new CustomerService();
      $params = array(
        "code" => I("post.code"),
        "name" => I("post.name"),
        "address" => I("post.address"),
        "contact" => I("post.contact"),
        "mobile" => I("post.mobile"),
        "tel" => I("post.tel"),
        "qq" => I("post.qq"),
        "recordStatus" => I("post.recordStatus")
      );

      $this->ajaxReturn($cs->categoryList($params));
    }
  }

  /**
   * 新增或编辑客户分类
   */
  public function editCategory()
  {
    if (IS_POST) {
      $us = new UserService();
      if (I("post.id")) {
        // 编辑客户分类
        if (!$us->hasPermission(FIdConst::CUSTOMER_CATEGORY_EDIT)) {
          die("没有权限");
        }
      } else {
        // 新增客户分类
        if (!$us->hasPermission(FIdConst::CUSTOMER_CATEGORY_ADD)) {
          die("没有权限");
        }
      }

      $params = array(
        "id" => I("post.id"),
        "code" => strtoupper(I("post.code")),
        "name" => I("post.name"),
        "psId" => I("post.psId")
      );
      $cs = new CustomerService();
      $this->ajaxReturn($cs->editCategory($params));
    }
  }

  /**
   * 删除客户分类
   */
  public function deleteCategory()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::CUSTOMER_CATEGORY_DELETE)) {
        die("没有权限");
      }

      $params = array(
        "id" => I("post.id")
      );
      $cs = new CustomerService();
      $this->ajaxReturn($cs->deleteCategory($params));
    }
  }

  /**
   * 新增或编辑客户资料
   */
  public function editCustomer()
  {
    if (IS_POST) {
      $us = new UserService();
      if (I("post.id")) {
        // 编辑客户
        if (!$us->hasPermission(FIdConst::CUSTOMER_EDIT)) {
          die("没有权限");
        }
      } else {
        // 新增客户
        if (!$us->hasPermission(FIdConst::CUSTOMER_ADD)) {
          die("没有权限");
        }
      }

      $params = [
        "id" => I("post.id"),
        "code" => strtoupper(I("post.code")),
        "name" => I("post.name"),
        "address" => I("post.address"),
        "addressReceipt" => I("post.addressReceipt"),
        "contact01" => I("post.contact01"),
        "mobile01" => I("post.mobile01"),
        "tel01" => I("post.tel01"),
        "qq01" => I("post.qq01"),
        "contact02" => I("post.contact02"),
        "mobile02" => I("post.mobile02"),
        "tel02" => I("post.tel02"),
        "qq02" => I("post.qq02"),
        "bankName" => I("post.bankName"),
        "bankAccount" => I("post.bankAccount"),
        "tax" => I("post.tax"),
        "fax" => I("post.fax"),
        "note" => I("post.note"),
        "categoryId" => I("post.categoryId"),
        "initReceivables" => I("post.initReceivables"),
        "initReceivablesDT" => I("post.initReceivablesDT"),
        "warehouseId" => I("post.warehouseId"),
        "recordStatus" => I("post.recordStatus")
      ];
      $cs = new CustomerService();
      $this->ajaxReturn($cs->editCustomer($params));
    }
  }

  /**
   * 获得客户列表
   */
  public function customerList()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::CUSTOMER)) {
        die("没有权限");
      }

      $params = array(
        "categoryId" => I("post.categoryId"),
        "code" => I("post.code"),
        "name" => I("post.name"),
        "address" => I("post.address"),
        "contact" => I("post.contact"),
        "mobile" => I("post.mobile"),
        "tel" => I("post.tel"),
        "qq" => I("post.qq"),
        "recordStatus" => I("post.recordStatus"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $cs = new CustomerService();
      $this->ajaxReturn($cs->customerList($params));
    }
  }

  /**
   * 删除客户
   */
  public function deleteCustomer()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::CUSTOMER_DELETE)) {
        die("没有权限");
      }

      $params = array(
        "id" => I("post.id")
      );
      $cs = new CustomerService();
      $this->ajaxReturn($cs->deleteCustomer($params));
    }
  }

  /**
   * 客户自定义字段，查询客户
   */
  public function queryData()
  {
    if (IS_POST) {
      $params = array(
        "queryKey" => I("post.queryKey")
      );
      $cs = new CustomerService();
      $this->ajaxReturn($cs->queryData($params));
    }
  }

  /**
   * 获得某个客户的信息
   */
  public function customerInfo()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::CUSTOMER)) {
        die("没有权限");
      }

      $id = I("post.id");
      $cs = new CustomerService();
      $this->ajaxReturn($cs->customerInfo($id));
    }
  }

  /**
   * 通过Excel导入客户资料
   */
  public function import()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::CUSTOMER_IMPORT)) {
        die("没有权限");
      }

      $us = new UserService();
      if (!$us->hasPermission(FIdConst::CUSTOMER_IMPORT)) {
        $this->ajaxReturn($this->noPermission("导入客户"));
        return;
      }

      $upload = new \Think\Upload();

      // 允许上传的文件后缀
      $upload->exts = array(
        'xls',
        'xlsx'
      );

      // 保存路径
      $upload->savePath = '/Customer/';

      // 先上传文件
      $fileInfo = $upload->uploadOne($_FILES['data_file']);
      if (!$fileInfo) {
        $this->ajaxReturn(array(
          "msg" => $upload->getError(),
          "success" => false
        ));
      } else {
        $uploadFileFullPath = './Uploads' . $fileInfo['savepath'] . $fileInfo['savename']; // 获取上传到服务器文件路径
        $uploadFileExt = $fileInfo['ext']; // 上传文件扩展名

        $params = array(
          "datafile" => $uploadFileFullPath,
          "ext" => $uploadFileExt
        );
        $cs = new ImportService();
        $this->ajaxReturn($cs->importCustomerFromExcelFile($params));
      }
    }
  }

  /**
   * 获得所有的价格体系中的价格
   */
  public function priceSystemList()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );
      $cs = new CustomerService();

      $this->ajaxReturn($cs->priceSystemList($params));
    }
  }
}
