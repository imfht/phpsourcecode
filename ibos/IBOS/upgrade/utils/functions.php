<?php

if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    if (isset($_GET)) {
        $_GET = istripSlashes($_GET);
    }
    if (isset($_POST)) {
        $_POST = istripSlashes($_POST);
    }
    if (isset($_REQUEST)) {
        $_REQUEST = istripSlashes($_REQUEST);
    }
    if (isset($_COOKIE)) {
        $_COOKIE = istripSlashes($_COOKIE);
    }
}

/**
 * Strips slashes from input data.
 * This method is applied when magic quotes is enabled.
 *
 * @param mixed $data input data to be processed
 * @return mixed processed data
 */
function istripSlashes(&$data)
{
    if (is_array($data)) {
        if (count($data) == 0) {
            return $data;
        }
        $keys = array_map('istripSlashes', array_keys($data));
        $data = array_combine($keys, array_values($data));
        return array_map('istripSlashes', $data);
    } else {
        return stripslashes($data);
    }
}

/**
 * 翻译语言
 *
 * @param string $key
 * @return string
 */
function t($key)
{
    $lang = getLang();
    return isset($lang[$key]) ? $lang[$key] : $key;
}

/**
 * Returns the named GET parameter value.
 * If the GET parameter does not exist, the second parameter to this method will be returned.
 *
 * @param string $name the GET parameter name
 * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
 * @return mixed the GET parameter value
 */
function getQuery($name, $defaultValue = null)
{
    return isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
}

/**
 * Returns the named POST parameter value.
 * If the POST parameter does not exist, the second parameter to this method will be returned.
 *
 * @param string $name the POST parameter name
 * @param mixed $defaultValue the default parameter value if the POST parameter does not exist.
 * @return mixed the POST parameter value
 */
function getPost($name, $defaultValue = null)
{
    return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
}

/**
 * Returns the named GET or POST parameter value.
 * If the GET or POST parameter does not exist, the second parameter to this method will be returned.
 * If both GET and POST contains such a named parameter, the GET parameter takes precedence.
 *
 * @param string $name the GET parameter name
 * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
 * @return mixed the GET parameter value
 * @see getQuery
 * @see getPost
 */
function getParam($name, $defaultValue = null)
{
    return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
}

/**
 * Get the upgrade sql file.
 *
 * @param  string $version
 * @return string
 */
function getUpgradeSQLFile($version)
{
    return PATH_ROOT . '/upgrade/db/update' . $version . '.sql';
}

/**
 * Create the confirm contents.
 *
 * @param  string $fromVersion
 * @return string
 */
function getConfirm($fromVersion)
{
    $confirmContent = '';
    $sqlFile = getUpgradeSQLFile($fromVersion);

    if (file_exists($sqlFile)) {
        $confirmContent .= file_get_contents($sqlFile);
    }
    switch ($fromVersion) {
    }
    return $confirmContent;
}

/**
 * 获取语言包
 *
 * @staticvar array $lang
 * @return array
 */
function getLang()
{
    static $lang = array();
    if (empty($lang)) {
        $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
        if (preg_match("/zh-c/i", $language) || preg_match("/zh/i", $language)) {
            $local = 'cn';
        } else {
            $local = 'en';
        }
        $lang = require PATH_UPGRADE . DS . 'lang/' . $local . '.php';
    }
    return $lang;
}

/**
 * 重定向到另一个页面。
 *
 * @param string $url the target url.
 * @return  void
 */
function locate($url)
{
    header("location: $url");
    exit;
}

/**
 *
 * @return string
 */
function getNewVersion()
{
    $version = strtolower(VERSION . ' ' . VERSION_TYPE);
    return $version;
}

/**
 * 将对象转换成数组
 * @param object $object 对象数组
 * @return mixed
 */
function object2array($object) {
    if (is_object($object)) {
        foreach ($object as $key => $value) {
            $array[$key] = $value;
        }
    }
    else {
        $array = $object;
    }
    return $array;
}

/**
 *
 * @param string $version
 */
function execute($version)
{
    $upgradeSqlFile = getUpgradeSQLFile($version);
    if (file_exists($upgradeSqlFile)) {
        execSQL($upgradeSqlFile);
    }

    switch ($version) {
        case '4.1.0 pro':
            upgradeTo20170103();
            break;
        case '4.2.0 pro':
            upgradeTo20170203();
            break;
        case '4.3.0 pro':
            upgradeTo20170511();
            break;
        case '4.4.0 pro':
            upgradeTo20170609();
            break;
        case '4.4.2 pro':
            upgradeTo20171128();
            break;
        case '4.4.2 open':
            upgradeTo20170511();
            upgradeTo20171128();
            break;
        case '4.4.3 pro':
            upgradeTo20171228();
            break;
        default:
            break;
    }
}

/**
 * 数据库更新到 v4.1.0 pro 版本
 */
function upgradeTo20170103()
{
    $queryBuilder = getQB();

    // 通讯录模块更新
    $contactMenuRow = $queryBuilder->table('menu')->where('name', '=', '通讯录')->first();
    if (empty($contactMenuRow)) {
        $queryBuilder->table('menu')->insert(array(
            'name' => '通讯录',
            'pid' => '0',
            'm' => 'contact',
            'c' => 'dashboard',
            'a' => 'index',
            'param' => '',
            'sort' => '2',
            'disabled' => '0',
        ));
    }

    // 新闻模块更新
    $queryBuilder->table('article_approval')->addColumnsIfNotExists(array(
        array(
            'columnName' => 'time',
            'columnType' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核时间'",
        ),
        array(
            'columnName' => 'isdel',
            'columnType' => "tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '软删除。0为未删除，1为已删除'",
        ),
    ));

    // CRM 模块更新
    $queryBuilder->table('crm_client')->addColumnIfNotExists('phone',
        "char(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '客户电话'");


    // 投票模块数据表变动
    $queryBuilder->table('vote')->addColumnsIfNotExists(array(
        array(
            'columnName' => 'content',
            'columnType' => "text NOT NULL COMMENT '投票描述'",
        ),
        array(
            'columnName' => 'deptid',
            'columnType' => "text NOT NULL COMMENT '阅读范围部门'",
        ),
        array(
            'columnName' => 'positionid',
            'columnType' => "text NOT NULL COMMENT '阅读范围职位'",
        ),
        array(
            'columnName' => 'roleid',
            'columnType' => "text NOT NULL COMMENT '阅读范围角色'",
        ),
        array(
            'columnName' => 'scopeuid',
            'columnType' => "text NOT NULL COMMENT '阅读范围人员'",
        ),
        array(
            'columnName' => 'addtime',
            'columnType' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间'",
        ),
        array(
            'columnName' => 'updatetime',
            'columnType' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间'",
        ),
    ));

    $queryBuilder->table('vote_item')->addColumnIfNotExists('topicid',
        "int(11) unsigned NOT NULL COMMENT '投票题目 id'");

    $queryBuilder->table('vote_item_count')->addColumnsIfNotExists(array(
        array(
            'columnName' => 'voteid',
            'columnType' => "mediumint(9) unsigned NOT NULL COMMENT '投票 id'",
        ),
        array(
            'columnName' => 'topicid',
            'columnType' => "mediumint(9) unsigned NOT NULL COMMENT '投票话题 id'",
        ),
    ));

    $queryBuilder->table('vote')->setTableEngine('InnoDB');
    $queryBuilder->table('vote_item')->setTableEngine('InnoDB');
    $queryBuilder->table('vote_item_count')->setTableEngine('InnoDB');

    $voteNavRow = $queryBuilder->table('nav')->where('name', '调查投票')->first();
    if (empty($voteNavRow)) {
        $queryBuilder->table('nav')->insert(array(
            'pid' => 5,
            'name' => '调查投票',
            'url' => 'vote/default/index',
            'targetnew' => 0,
            'system' => 1,
            'disabled' => 0,
            'sort' => 7,
            'module' => 'vote',
        ));
    }

    $votePublishNode = $queryBuilder->table('notify_node')->where('node', 'vote_publish_message')->first();
    if (empty($votePublishNode)) {
        $queryBuilder->table('notify_node')->insert(array(
            'node' => 'vote_publish_message',
            'nodeinfo' => '投票发布提醒',
            'module' => 'vote',
            'titlekey' => 'vote/default/New message title',
            'contentkey' => 'vote/default/New message content',
            'sendemail' => '1',
            'sendmessage' => '1',
            'sendsms' => '1',
            'type' => '2',
        ));
    }

    $voteUpdateNode = $queryBuilder->table('notify_node')->where('node', 'vote_update_message')->first();
    if (empty($voteUpdateNode)) {
        $queryBuilder->table('notify_node')->insert(array(
            'node' => 'vote_update_message',
            'nodeinfo' => '投票更新提醒',
            'module' => 'vote',
            'titlekey' => 'vote/default/Update message title',
            'contentkey' => 'vote/default/Update message content',
            'sendemail' => '1',
            'sendmessage' => '1',
            'sendsms' => '1',
            'type' => '2',
        ));
    }

    // 投票模块数据迁移
    $votes = $queryBuilder->table('vote')->get();

    foreach ($votes as $vote) {
        $voteId = $vote->voteid;
        $articleId = $vote->relatedid;

        $voteItemModel = $queryBuilder->table('vote_item')->where('voteid', '=', $voteId)->first();
        if (!empty($voteItemModel)) {
            // 添加投票话题（vote topic）
            $itemNum = $queryBuilder->table('vote_item')->where('voteid', '=', $voteId)->count();

            $subject = empty($vote->subject) ? '' : $vote->subject;
            $maxselectnum = (int)$vote->maxselectnum;
            $type = (int)$voteItemModel->type;
            $queryBuilder->table('vote_topic')->insert(array(
                'voteid' => $voteId,
                'subject' => $subject,
                'type' => $type,
                'maxselectnum' => $maxselectnum,
                'itemnum' => $itemNum,
            ));
        }

        $articleModel = $queryBuilder->table('article')->where('articleid', '=', $articleId)->first();
        if (!empty($articleModel)) {
            // 更新投票记录选择范围数据
            $queryBuilder->table('vote')->where('voteid', '=', $voteId)
                ->update(array(
                    'deptid' => $articleModel->deptid,
                    'positionid' => $articleModel->positionid,
                    'roleid' => $articleModel->roleid,
                    'scopeuid' => $articleModel->uid,
                    'addtime' => time(),
                    'updatetime' => time(),
                ));
        }

    }

    // 更新 vote item 数据
    $voteItems = $queryBuilder->table('vote_item')->get();
    if (!empty($voteItems)) {
        foreach ($voteItems as $voteItem) {
            $topic = $queryBuilder->table('vote_topic')->where('voteid', '=', $voteItem->voteid)->first();
            if (!empty($topic)) {
                $queryBuilder->table('vote_item')->where('itemid', '=', $voteItem->itemid)
                    ->update(array('topicid' => $topic->topicid));
            }
        }
    }

    // 更新 vote item count 数据
    $voteItemCounts = $queryBuilder->table('vote_item_count')->get();
    if (!empty($voteItemCounts)) {
        foreach ($voteItemCounts as $voteItemCount) {
            $item = $queryBuilder->table('vote_item')->where('itemid', '=', $voteItemCount->itemid)->first();

            if (!empty($item)) {
                $queryBuilder->table('vote_item_count')->where('voteid', '=', $item->itemid)
                    ->update(array(
                        'voteid' => $item->voteid,
                        'topicid' => $item->topicid,
                    ));
            }

        }
    }

}

/**
 * 更新数据库数据库语句到4.2.0 pro
 */
function upgradeTo20170203()
{
    //更新新闻模块数据库
    $queryBuilder = getQB();
    $approval = $queryBuilder->table('article_approval')->get();
    if (!empty($approval)) {
        //将对象转成数组
        $record = object2array($approval);
        $length = count($approval);
        $all = array();
        for ($i = 0; $i < $length; $i++) {
            if ($record[$i]['step'] == 0) {
                $all[] = array('article', $record[$i]['articleid'], $record[$i]['uid'], $record[$i]['step'], 0, 3, '');
            } else {
                $all[] = array('article', $record[$i]['articleid'], $record[$i]['uid'], $record[$i]['step'], 0, 1, '');
            }
            $last = $queryBuilder->table('article_approval')->where('articleid', '=', $record[$i]['articleid'])->where('step', '=', $record[$i]['step'] + 1)->get();
            $isBack = $queryBuilder->table('article_approval')->where('articleid', '=', $record[$i]['articleid'])->get();
            if (empty($last) && !empty($isBack)) {
                $back = $queryBuilder->table('article_back')->where('articleid', '=', $record[$i]['articleid'])->first();
                $backStep = $record[$i]['step'] + 1;
                $all[] = array(
                    'article',
                    $record[$i]['articleid'],
                    $back->uid,
                    $backStep,
                    $back->time,
                    0,
                    $back->reason,
                );
            }
        }
        if (!empty($all)) {
            for ($i = 0; $i < count($all); $i++) {
                $queryBuilder->table('approval_record')->insert(array(
                    'module' => $all[$i][0],
                    'relateid' => $all[$i][1],
                    'uid' => $all[$i][2],
                    'step' => $all[$i][3],
                    'time' => $all[$i][4],
                    'status' => $all[$i][5],
                    'reason' => $all[$i][6],
                ));
            }
        }
    }
}

//4.3.0数据库更新语句
function upgradeTo20170511()
{
    $queryBuilder = getQB();
    //更新菜单和导航
    $queryBuilder->table('nav')->where('module', '=', 'report')->update(array('name' => '工作汇报'));
    $queryBuilder->table('menu')->where('m', '=', 'report')->update(array('name' => '工作汇报'));
    $queryBuilder->table('notify_node')->where('node', '=', 'report_message')->update(array('nodeinfo' => '工作汇报消息提醒'));
    $queryBuilder->table('credit_rule')->where('action', '=', 'addreport')->update(array('rulename' => '发表工作汇报'));
    $queryBuilder->table('menu_common')->where('module', '=', 'report')->update(array('name' => '汇报'));
    //更新汇报数据库表结构
    $prefix = $queryBuilder->getPrefix();
    $report_statistics = $prefix . 'report_statistics';
    if ($queryBuilder->isExistTable('report_statistics')){
        if (!($queryBuilder->table('report_statistics')->isColumnExists('tid'))){
            if ($queryBuilder->table('report_statistics')->isColumnExists('typeid')){
                $sql1 = "ALTER TABLE `{$report_statistics}` CHANGE COLUMN `typeid` `tid`  tinyint(3) unsigned NOT NULL  COMMENT '汇报类型id';";
                $queryBuilder->query($sql1);
            }
        }
    }
    if ($queryBuilder->isExistTable('report')){
        $report = $prefix . 'report';
        if (!($queryBuilder->table('report')->isColumnExists('tid'))){
            if ($queryBuilder->table('report')->isColumnExists('typeid')){
                $sql2 = "ALTER TABLE `{$report}` CHANGE COLUMN `typeid` `tid`  tinyint(3) UNSIGNED NOT NULL  COMMENT '汇报模板id，用户自己的模板' AFTER `addtime`;";
                $queryBuilder->query($sql2);
            }
        }
        $queryBuilder->table('report')->addColumnsIfNotExists(array(
            array(
                'columnName' => 'place',
                'columnType' => "varchar(255) DEFAULT NULL COMMENT '填写汇报的地点'",
            ),
            array(
                'columnName' => 'isdel',
                'columnType' => "tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除，1表示已删除，0表示未删除'",
            ),
        ));
        $queryBuilder->table('report')->update(array('status' => 1));
    }
    if ($queryBuilder->isExistTable('report_record')){
        $queryBuilder->table('report_record')->addColumnsIfNotExists(array(
            array(
                'columnName' => 'fieldid',
                'columnType' => "int(11) NOT NULL COMMENT '字段的id'",
            ),
            array(
                'columnName' => 'fieldname',
                'columnType' => "varchar(255) NOT NULL COMMENT '字段名称'",
            ),
            array(
                'columnName' => 'fieldtype',
                'columnType' => "int(11) NOT NULL DEFAULT '0' COMMENT '是否必填，0表示不需要，1表示需要'",
            ),
            array(
                'columnName' => 'iswrite',
                'columnType' => "int(11) NOT NULL COMMENT '字段类型，1表示长文本，2表示短文本，3表示数字，4表示日期与时间，5表示时间，6表示日期，7表示下拉'",
            ),
            array(
                'columnName' => 'fieldvalue',
                'columnType' => "text COMMENT '字段值'",
            ),
        ));
    }
    if ($queryBuilder->isExistTable('report')) {
        $reportRecord = $queryBuilder->table('report')->get();
        if (!empty($reportRecord)){
            foreach ($reportRecord as $value) {
                //第一步先把report表中的content字段内容移到report_record表中
                $summary = getTmplField($queryBuilder, $value->tid, 8, '工作总结');
                if (!empty($summary)){
                    $queryBuilder->table('report_record')->insert(array(
                        'repid' => $value->repid,
                        'content' => $value->content,
                        'fieldid' => $summary->fid,
                        'fieldname' => $summary->fieldname,
                        'iswrite' => $summary->iswrite,
                        'fieldtype' => 8,
                        'fieldvalue' => '',
                    ));
                }
                //第二步将原计划，计划外，下次计划分别进行合并成一个
                $originPlanContent = getRecordByPlanflag($queryBuilder, $value->repid, 0);
                if (!empty($originPlanContent)) {
                    $originPlan = getTmplField($queryBuilder, $value->tid, 1, '原计划');
                    if (!empty($originPlan)){
                        $queryBuilder->table('report_record')->insert(array(
                            'repid' => $value->repid,
                            'content' => $originPlanContent,
                            'fieldid' => $originPlan->fid,
                            'fieldname' => $originPlan->fieldname,
                            'iswrite' => $originPlan->iswrite,
                            'fieldtype' => 1,
                            'fieldvalue' => '',
                        ));
                        delRecordByPlanflag($queryBuilder, $value->repid, 0);
                    }
                }
                $unPlanContent = getRecordByPlanflag($queryBuilder, $value->repid, 1);
                if (!empty($unPlanContent)) {
                    $unPlan = getTmplField($queryBuilder, $value->tid, 1, '计划外');
                    if (!empty($unPlan)){
                        $queryBuilder->table('report_record')->insert(array(
                            'repid' => $value->repid,
                            'content' => $unPlanContent,
                            'fieldid' => $unPlan->fid,
                            'fieldname' => $unPlan->fieldname,
                            'iswrite' => $unPlan->iswrite,
                            'fieldtype' => 1,
                            'fieldvalue' => '',
                        ));
                        delRecordByPlanflag($queryBuilder, $value->repid, 1);
                    }
                }
                $nextPlanContent = getRecordByPlanflag($queryBuilder, $value->repid, 2);
                if (!empty($nextPlanContent)) {
                    $nextPlan = getTmplField($queryBuilder, $value->tid, 1, '下次计划');
                    if (!empty($nextPlan)){
                        $queryBuilder->table('report_record')->insert(array(
                            'repid' => $value->repid,
                            'content' => $nextPlanContent,
                            'fieldid' => $nextPlan->fid,
                            'fieldname' => $nextPlan->fieldname,
                            'iswrite' => $nextPlan->iswrite,
                            'fieldtype' => 1,
                            'fieldvalue' => '',
                        ));
                        delRecordByPlanflag($queryBuilder, $value->repid, 2);
                    }
                }
                //第三步将readeruid字段的值移到module_reader表
                if (!empty($value->readeruid)) {
                    $readeruids = explode(',', $value->readeruid);
                    foreach ($readeruids as $readeruid) {
                        $queryBuilder->table('module_reader')->insert(array(
                            'module' => 'report',
                            'relateid' => $value->repid,
                            'uid' => $readeruid,
                            'addtime' => time(),
                            'readername' => getUserRealname($queryBuilder, $readeruid),
                        ));
                    }
                }
            }
        }
    }
}

function getTmplField($queryBuilder, $tid, $fieldtype, $fieldname)
{
    return $queryBuilder->table('template_field')->where('tid', '=', $tid)
        ->where('fieldtype', '=', $fieldtype)->where('fieldname', '=', $fieldname)
        ->first();
}

function getRecordByPlanflag($queryBuilder, $repid, $planflag)
{
    $records = $queryBuilder->table('report_record')
        ->where('repid', '=', $repid)->where('planflag', '=', $planflag)
        ->where('fieldname', '!=', '工作总结')
        ->get();
    $return = array();
    foreach ($records as $record) {
        array_push($return, $record->content);
    }
    return implode('<br>', $return);
}

function delRecordByPlanflag($queryBuilder, $repid, $planflag)
{
    $queryBuilder->table('report_record')->where('repid', '=', $repid)
        ->where('planflag', '=', $planflag)
        ->delete();
}

function getUserRealname($queryBuilder, $uid)
{
    $user = $queryBuilder->table('user')->where('uid', '=', $uid)
        ->first();
    return $user->realname;
}

//4.4.0 pro 数据库更新语句
function upgradeTo20170609()
{
    $queryBuilder = getQB();
    $queryBuilder->table('crm_client')->addColumnsIfNotExists(array(
        array(
            'columnName' => 'movetime',
            'columnType' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '移入公海时间'",
        ),
    ));
    $queryBuilder->table('crm_contact')->addColumnsIfNotExists(array(
        array(
            'columnName' => 'mobile',
            'columnType' => "char(11) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '联系人手机号码'",
        ),
        array(
            'columnName' => 'initial',
            'columnType' => "char(2) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '联系人首字母'",
        ),
    ));
    require_once PATH_ROOT . "/upgrade/utils/Py.php";
    if ($queryBuilder->isExistTable('crm_contact')){
        $contacts = $queryBuilder->table('crm_contact')->get();
        if (!empty($contacts)){
            foreach ($contacts as $contact){
                if (empty($contact->name)){
                    $inital = '';
                }else{
                    $char = mb_substr($contact->name, 0, 1);
                    if (preg_match('/^[\x{4E00}-\x{9FA5}]+$/u', $char)){
                        $inital = getPy($char);
                    }else{
                        $inital = $char;
                    }
                }
                $queryBuilder->table('crm_contact')
                    ->where('contactid', '=', $contact->contactid)
                    ->update(array(
                        'initial' => $inital,
                    ));
            }
        }
    }
    $queryBuilder->table('crm_contract')->addColumnsIfNotExists(array(
        array(
            'columnName' => 'name',
            'columnType' => "char(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '合同名称'",
        ),
        array(
            'columnName' => 'expiretime',
            'columnType' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合同期限'",
        ),
    ));
    $queryBuilder->table('crm_event')->addColumnsIfNotExists(array(
        array(
            'columnName' => 'contractid',
            'columnType' => "char(60) NOT NULL DEFAULT '' COMMENT '合同ID'",
        ),
        array(
            'columnName' => 'source',
            'columnType' => "char(20) NOT NULL DEFAULT '' COMMENT '事件来源'",
        ),
        array(
            'columnName' => 'at',
            'columnType' => "char(20) NOT NULL DEFAULT '' COMMENT 'at用户uid'",
        ),
    ));
    $queryBuilder->table('crm_highseas')->addColumnsIfNotExists(array(
        array(
            'columnName' => 'status',
            'columnType' => "tinyint(2) NOT NULL DEFAULT '0' COMMENT '0表示不转入，1表示没跟进转入，2表示没商机转入'",
        ),
        array(
            'columnName' => 'cday',
            'columnType' => "int(10) DEFAULT NULL COMMENT '没事件多少天转入'",
        ),
        array(
            'columnName' => 'oday',
            'columnType' => "int(10) DEFAULT NULL COMMENT '没有商机多少天转入'",
        ),
    ));
    $queryBuilder->table('crm_highseas')->update(array(
        'status' => 0,
        'cday' => 0,
        'oday' => 0,
    ));
    $queryBuilder->table('crm_lead')->addColumnsIfNotExists(array(
        array(
            'columnName' => 'reason',
            'columnType' => "text CHARACTER SET utf8 NOT NULL COMMENT '关闭理由'",
        ),
    ));
    if ($queryBuilder->isExistTable('crm_lead')){
        $reason = array(
            1 => '无法联系',
            2 => '已取消',
            3 => '丢失',
            4 => '不感兴趣',
        );
        $status = array_keys($reason);
        $leads = $queryBuilder->table('crm_lead')->get();
        if (!empty($leads)){
            foreach ($leads as $lead){
                if (in_array($lead->status, $status)){
                    $queryBuilder->table('crm_lead')->update(array(
                        'status' => 4,
                        'reason' => $reason[$lead->status],
                    ));
                }
            }
        }
    }
    $queryBuilder->table('crm_receipt')->addColumnsIfNotExists(array(
        array(
            'columnName' => 'remark',
            'columnType' => "text COMMENT '备注'",
        ),
    ));
    $queryBuilder->table('crm_target')->addColumnsIfNotExists(array(
        array(
            'columnName' => 'firqua',
            'columnType' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '第一季度'",
        ),
        array(
            'columnName' => 'secqua',
            'columnType' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '第二季度'",
        ),
        array(
            'columnName' => 'thiqua',
            'columnType' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '第三季度'",
        ),
        array(
            'columnName' => 'forqua',
            'columnType' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '第四季度'",
        ),
        array(
            'columnName' => 'yearnum',
            'columnType' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '年总数'",
        ),
    ));
    if ($queryBuilder->isExistTable('notify_node')){
        $queryBuilder->table('notify_node')->insert(array(
            'node' => 'crm_all_notice',
            'nodeinfo' => 'crm消息提醒',
            'module' => 'crm',
            'titlekey' => 'crm/default/Crm all notice',
            'contentkey' => '',
            'sendemail' => '1',
            'sendmessage' => '1',
            'sendsms' => '1',
            'type' => '2',
        ));
    }
    if ($queryBuilder->isExistTable('cron')){
        $queryBuilder->table('cron')->insert(array(
            'available' => '1',
            'type' => 'system',
            'module' => 'crm',
            'name' => 'crm消息提醒',
            'filename' => 'CronCrmRemind.php',
            'lastrun' => '1393516800',
            'nextrun' => '1393603200',
            'weekday' => '-1',
            'day' => '-1',
            'hour' => '-1',
            'minute' => '*/15',
        ));
    }
    if ($queryBuilder->isExistTable('crm_tag_group')){
        $group = $queryBuilder->table('crm_tag_group')->where('groupid', '=', 3)->first();
        if ($group->name == '机会进度'){
            $queryBuilder->table('crm_tag_group')->where('groupid', '=', 3)->update(array(
                'name' => '商机进度'
            ));
        }
    }
    if ($queryBuilder->isExistTable('crm_tag')){
        $group = $queryBuilder->table('crm_tag')->where('tagid', '=', 13)->first();
        if ($group->name == '2-机会评估'){
            $queryBuilder->table('crm_tag')->where('tagid', '=', 13)->update(array(
                'name' => '2-商机评估'
            ));
        }
    }
}

function getCross($fromVersion)
{
    global $versions;
    $fromVersionArr = explode(' ', $fromVersion);
    $versionType = !empty($fromVersionArr[1]) && !empty($versions[$fromVersionArr[1]])? $fromVersionArr[1] : 'pro';
    $typeVersions = $versions[$versionType];
    $counter = 0;
    $gotcha = false;
    foreach ($typeVersions as $key => $ver) {
        if ($key == $fromVersion) {
            $gotcha = true;
            break;
        }
        $counter++;
    }
    if ($gotcha) {
        $crossVersions = array_slice($typeVersions, $counter+1);
        return array_keys($crossVersions);
    } else {
        return array();
    }
}

/**
 * Execute a sql.
 *
 * @param  string $sqlFile
 * @return void
 */
function execSQL($sqlFile)
{
    $mysqlVersion = getMysqlVersion();
    $ignoreCode = '|1050|1060|1062|1091|1169|1061|';
    $pdo = getPdo();
    $config = getConfig();
    // Read the sql file to lines, remove the comment lines, then join theme by ';'.
    $sqls = explode("\n", file_get_contents($sqlFile));
    foreach ($sqls as $key => $line) {
        $line = trim($line);
        $sqls[$key] = $line;
        // Skip sql that is note.
        if (preg_match('/^--|^#|^\/\*/', $line) or empty($line)) {
            unset($sqls[$key]);
        }
    }
    $sqls = explode(';', join("\n", $sqls));


    foreach ($sqls as $sql) {
        if (empty($sql)) {
            continue;
        }
        if ($mysqlVersion <= 4.1) {
            $sql = str_replace('DEFAULT CHARSET=utf8', '', $sql);
            $sql = str_replace('CHARACTER SET utf8 COLLATE utf8_general_ci', '', $sql);
        }

        // 替换表前缀，Example：{{user}} => prefix_user、ibos_user => YourPrefix_user
        $sql = str_replace('ibos_', $config['tableprefix'], $sql);
        $sql = preg_replace('/{{(.+?)}}/', $config['tableprefix'] . '\1', $sql);

        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
            $errorCode = $errorInfo[1];
            if (strpos($ignoreCode, "|$errorCode|") === false) {
                ErrorLogger::log($e->getMessage() . "<p>The sql is: $sql</p>", 'pdo');
            }
        }
    }
}

function upgradeTo20171128()
{
    $queryBuilder = getQB();

    if ($queryBuilder->isExistTable('notify_message')) {
        $queryBuilder->table('notify_message')->addColumnsIfNotExists(array(
            array(
                'columnName' => 'isalarm',
                'columnType' => "tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否为闹钟提醒'",
            ),
            array(
                'columnName' => 'senduid',
                'columnType' => "mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '主动提醒发送用户ID'",
            )
        ));
    }

    if ($queryBuilder->isExistTable('notify_node')){
        $moduleArr = getModuleArr();
        $notifyNodeInsertData = array();

        if(in_array('message', $moduleArr)) {
            $notifyNodeInsertData[] = array(
                'node' => 'normal_alarm_notily',
                'nodeinfo' => '普通提醒',
                'module' => 'message',
                'titlekey' => 'message/default/Alarm title',
                'contentkey' => 'message/default/Alarm content',
                'sendemail' => '1',
                'sendmessage' => '1',
                'sendsms' => '1',
                'type' => '1',
            );
        }

        if (!empty($moduleArr)) {
            if(in_array('meeting', $moduleArr)) {
                $notifyNodeInsertData[] = array(
                    'node' => 'meeting_management',
                    'nodeinfo' => '会议管理',
                    'module' => 'meeting',
                    'titlekey' => 'message/default/Alarm title',
                    'contentkey' => 'message/default/Alarm content',
                    'sendemail' => '1',
                    'sendmessage' => '1',
                    'sendsms' => '1',
                    'type' => '1',
                );
            }

            if(in_array('assignment', $moduleArr)) {
                $notifyNodeInsertData[] = array(
                    'node' => 'assignment_task',
                    'nodeinfo' => '任务指派',
                    'module' => 'assignment',
                    'titlekey' => 'message/default/Alarm title',
                    'contentkey' => 'message/default/Alarm content',
                    'sendemail' => '1',
                    'sendmessage' => '1',
                    'sendsms' => '1',
                    'type' => '1',
                );
            }

            if(in_array('vote', $moduleArr)) {
                $notifyNodeInsertData[] = array(
                    'node' => 'vote_survey',
                    'nodeinfo' => '调查投票',
                    'module' => 'vote',
                    'titlekey' => 'message/default/Alarm title',
                    'contentkey' => 'message/default/Alarm content',
                    'sendemail' => '1',
                    'sendmessage' => '1',
                    'sendsms' => '1',
                    'type' => '1',
                );
            }

            if(in_array('assets', $moduleArr)) {
                $notifyNodeInsertData[] = array(
                    'node' => 'fixed_assets',
                    'nodeinfo' => '固定资产',
                    'module' => 'assets',
                    'titlekey' => 'message/default/Alarm title',
                    'contentkey' => 'message/default/Alarm content',
                    'sendemail' => '1',
                    'sendmessage' => '1',
                    'sendsms' => '1',
                    'type' => '1',
                );
            }

            if(in_array('activity', $moduleArr)) {
                $notifyNodeInsertData[] = array(
                    'node' => 'activity_center',
                    'nodeinfo' => '活动中心',
                    'module' => 'activity',
                    'titlekey' => 'message/default/Alarm title',
                    'contentkey' => 'message/default/Alarm content',
                    'sendemail' => '1',
                    'sendmessage' => '1',
                    'sendsms' => '1',
                    'type' => '1',
                );
            }

            if(in_array('thread', $moduleArr)) {
                $notifyNodeInsertData[] = array(
                    'node' => 'project_thread',
                    'nodeinfo' => '项目主线',
                    'module' => 'thread',
                    'titlekey' => 'message/default/Alarm title',
                    'contentkey' => 'message/default/Alarm content',
                    'sendemail' => '1',
                    'sendmessage' => '1',
                    'sendsms' => '1',
                    'type' => '1',
                );
            }

            if(in_array('workflow', $moduleArr)) {
                $notifyNodeInsertData[] = array(
                    'node' => 'handling_work',
                    'nodeinfo' => '办理工作',
                    'module' => 'workflow',
                    'titlekey' => 'message/default/Alarm title',
                    'contentkey' => 'message/default/Alarm content',
                    'sendemail' => '1',
                    'sendmessage' => '1',
                    'sendsms' => '1',
                    'type' => '1',
                );
            }

            if(in_array('crm', $moduleArr)) {
                $notifyNodeInsertData[] = array(
                    'node' => 'event',
                    'nodeinfo' => '跟进',
                    'module' => 'crm',
                    'titlekey' => 'message/default/Alarm title',
                    'contentkey' => 'message/default/Alarm content',
                    'sendemail' => '1',
                    'sendmessage' => '1',
                    'sendsms' => '1',
                    'type' => '1',
                );
                $notifyNodeInsertData[] = array(
                    'node' => 'contract',
                    'nodeinfo' => '合同',
                    'module' => 'crm',
                    'titlekey' => 'message/default/Alarm title',
                    'contentkey' => 'message/default/Alarm content',
                    'sendemail' => '1',
                    'sendmessage' => '1',
                    'sendsms' => '1',
                    'type' => '1',
                );
                $notifyNodeInsertData[] = array(
                    'node' => 'client',
                    'nodeinfo' => '客户',
                    'module' => 'crm',
                    'titlekey' => 'message/default/Alarm title',
                    'contentkey' => 'message/default/Alarm content',
                    'sendemail' => '1',
                    'sendmessage' => '1',
                    'sendsms' => '1',
                    'type' => '1',
                );
                $notifyNodeInsertData[] = array(
                    'node' => 'opportunity',
                    'nodeinfo' => '商机',
                    'module' => 'crm',
                    'titlekey' => 'message/default/Alarm title',
                    'contentkey' => 'message/default/Alarm content',
                    'sendemail' => '1',
                    'sendmessage' => '1',
                    'sendsms' => '1',
                    'type' => '1',
                );
            }
        }

        if (!empty($notifyNodeInsertData)) {
            $queryBuilder->table('notify_node')->insert($notifyNodeInsertData);
        }
    }

    if ($queryBuilder->isExistTable('cron')){
        $queryBuilder->table('cron')->insert(array(
            'available' => '1',
            'type' => 'system',
            'module' => 'message',
            'name' => '发送通用提醒',
            'filename' => 'CronSentNoifyAlarm.php',
            'lastrun' => '1511160683',
            'nextrun' => '1511160720',
            'weekday' => '-1',
            'day' => '-1',
            'hour' => '-1',
            'minute' => '*/1',
        ));
    }

}

/**
 * 升级CRM任务调用 4.4.3 pro
 */
function upgradeTo20171228()
{
    $queryBuilder = getQB();

    if ($queryBuilder->isExistTable('assignment')) {
        $queryBuilder->table('assignment')->addColumnsIfNotExists(array(
            array(
                'columnName' => 'associatedmodule',
                'columnType' => "varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '关联模块'",
            ),
            array(
                'columnName' => 'associatednode',
                'columnType' => "varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '关联节点'",
            ),
            array(
                'columnName' => 'associatedid',
                'columnType' => "varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '关联id'",
            ),
        ));
    }

    // 将没有10点升级包任务的添加计划任务
    $autoPathCron = $queryBuilder->table('cron')
        ->where('module', '=', 'dashboard')
        ->where('filename', '=', 'CronAutoPatch.php')
        ->first();
    if (empty($autoPathCron)) {
        $queryBuilder->table('cron')->insert(array(
                'available' => 1,
                'type' => 'system',
                'module' => 'dashboard',
                'name' => '自动补丁',
                'filename' => 'CronAutoPatch.php',
                'lastrun' => '1457661663',
                'nextrun' => '1457748000',
                'weekday' => '-1',
                'day' => '-1',
                'hour' => '10',
                'minute' => '0',
            ));
    }
}

function getModuleArr()
{
    $queryBuilder = getQB();
    $module = $queryBuilder->table('module')->get();

    $moduleArr = array();
    if(empty($module)) {
        return $moduleArr;
    }
    foreach ($module as $value) {
        if (!empty($value->module)) {
            $moduleArr[] = $value->module;
        }
    }

    return $moduleArr;
}
