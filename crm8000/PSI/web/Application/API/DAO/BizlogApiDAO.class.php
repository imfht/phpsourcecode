<?php

namespace API\DAO;

use Home\DAO\PSIBaseExDAO;

/**
 * 业务日志API DAO
 *
 * @author 李静波
 */
class BizlogApiDAO extends PSIBaseExDAO {

	public function insertBizlog($params) {
		$db = $this->db;
		
		$loginUserId = $params["loginUserId"];
		$log = $params["log"];
		$category = $params["category"];
		$ip = $params["ip"];
		$ipFrom = $params["ipFrom"];
		$dataOrg = $params["dataOrg"];
		$companyId = $params["companyId"];
		
		$sql = "insert into t_biz_log (user_id, info, ip, date_created, log_category, data_org,
						ip_from, company_id)
				values ('%s', '%s', '%s',  now(), '%s', '%s', '%s', '%s')";
		$rc = $db->execute($sql, $loginUserId, $log, $ip, $category, $dataOrg, $ipFrom, $companyId);
		
		if ($rc === false) {
			return $this->sqlError(__METHOD__, __LINE__);
		}
		
		// 操作成功
		return null;
	}
}