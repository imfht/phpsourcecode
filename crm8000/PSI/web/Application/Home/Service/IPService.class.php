<?php

namespace Home\Service;

require __DIR__ . '/../Common/ip2region/Ip2Region.class.php';

/**
 * IP Service
 *
 * @author 李静波
 */
class IPService
{

  /**
   * 根据IP查询所在地区
   *
   * @param string $ip        	
   * @return string
   */
  public function toRegion($ip)
  {
    $r = new \Ip2Region();
    $data = $r->btreeSearch($ip);
    $region = $data["region"];
    if (!$region) {
      return "";
    }

    $regionArray = explode("|", $region);

    if (!$regionArray) {
      // 出现莫名错误了
      return $region;
    }

    $result = "";
    foreach ($regionArray as $i => $v) {
      if ($v == "内网IP") {
        return "内网IP";
      }

      if ($v == "0") {
        continue;
      }
      if ($i == count($regionArray) - 1) {
        // 最后一个是运营商
        $result .= " $v";
      } else {
        $result .= $v;
      }
    }

    return $result;
  }
}
