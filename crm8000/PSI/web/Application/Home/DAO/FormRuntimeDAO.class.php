<?php

namespace Home\DAO;

/**
 * 自定义表单Runtime DAO
 *
 * @author 李静波
 */
class FormRuntimeDAO extends PSIBaseExDAO
{
  public function getFormMetadataForViewInit($params)
  {
    $db = $this->db;

    $fid = $params["fid"];

    $sql = "select module_name from t_form where fid = '%s'";
    $data = $db->query($sql, $fid);
    if ($data) {
      return [
        "title" => $data[0]["module_name"],
      ];
    } else {
      return null;
    }
  }

  public function getFormMetadataForRuntime($params)
  {
    $db = $this->db;

    $fid = $params["fid"];

    $sql = "select id, name from t_form where fid = '%s'";
    $data = $db->query($sql, $fid);
    if (!$data) {
      return null;
    }

    $v = $data[0];
    $formId = $v["id"];
    $result = [
      "name" => $v["name"],
    ];

    // 主表列
    $sql = "select caption, data_index, width_in_view
            from t_form_cols
            where form_id = '%s' and is_visible = 1 and show_order_in_view >= 0
            order by show_order_in_view";
    $data = $db->query($sql, $formId);
    $cols = [];
    foreach ($data as $v) {
      $cols[] = [
        "caption" => $v["caption"],
        "dataIndex" => $v["data_index"],
        "widthInView" => $v["width_in_view"]
      ];
    }
    $result["cols"] = $cols;

    // 明细表
    $sql = "select id, name
            from t_form_detail
            where form_id = '%s'
            order by show_order";
    $data = $db->query($sql, $formId);
    $details = [];
    foreach ($data as $v) {
      $detailId = $v["id"];
      $detailTable = [
        "name" => $v["name"]
      ];

      // 明细表的列
      $sql = "select caption, width_in_view, data_index
              from t_form_detail_cols
              where detail_id = '%s' and is_visible = 1
              order by show_order ";
      $d = $db->query($sql, $detailId);
      $cols = [];
      foreach ($d as $c) {
        $cols[] = [
          "caption" => $c["caption"],
          "widthInView" => $c["width_in_view"],
          "dataIndex" => $c["data_index"],
        ];
      }
      $detailTable["cols"] = $cols;

      $details[] = $detailTable;
    }
    $result["details"] = $details;

    return $result;
  }
}
