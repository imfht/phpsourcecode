<?php
/**
 * 主mysql信息
 * 数据库用户应该具有主mysql root帐号的权限
 */
$master_host = '192.168.199.130';
$master_user = 'root';
$master_pass = 'admin';

/**
 * 用于复制主mysql数据到从mysql的用户
 * 该用户应该是主服务器上的用户，并且对同步的数据库拥有所有的权限
 */
$copy_user_on_master = 'root';
$copy_pass_on_master = 'admin';

/**
 * 从mysql信息
 * 数据库用户应该具有主mysql root帐号的权限
 */
$slave_host = '192.168.199.25';
$slave_user = 'root';
$slave_pass = 'admin';

#=================配置结束==================
$conn_master = mysql_connect($master_host, $master_user, $master_pass) or die("Fail connect to master\n");
$conn_slave = mysql_connect($slave_host, $slave_user, $slave_pass) or die("Fail connect to slave\n");

switch (@$argv[1]) {
    case 'slave_status':
        checkSlaveStatus($conn_slave);
        break;
    case 'master_status':
        checkMasterStatus($conn_master);
        break;
    case 'sync':
        lockMaster($conn_master);
        $master_status = getMasterStatus($conn_master);
        if (!$master_status) {
            unLockMaster($conn_master);
            exit("Get master status failed.\n");
        }
        $change_master_sql = "change master to master_host='{$master_host}',master_user='{$copy_user_on_master}',master_password='{$copy_pass_on_master}',master_log_file='{$master_status['File']}' ,master_log_pos={$master_status['Position']}";

        if (!mysql_query('slave stop', $conn_slave)) {
            unLockMaster($conn_master);
            exit('stop slave failed.[' . mysql_error($conn_slave) . "]\n");
        }
        if (!mysql_query($change_master_sql, $conn_slave)) {
            unLockMaster($conn_master);
            exit('chage master failed..[' . mysql_error($conn_slave) . "]\n");
        }

        if (!mysql_query('slave start', $conn_slave)) {
            unLockMaster($conn_master);
            exit('start slave failed.[' . mysql_error($conn_slave) . "]\n");
        }
        $slave_status = getSlaveStatus($conn_slave);
        if (!$slave_status) {
            unLockMaster($conn_master);
            exit('start slave failed.[' . mysql_error($conn_slave) . "]\n");
        }
        echo "Slave_IO_Running:{$slave_status['Slave_IO_Running']}\nSlave_SQL_Running:{$slave_status['Slave_SQL_Running']}\n";
        unLockMaster($conn_master);
        break;
    default:
        printUsage();
        break;
}
mysql_close($conn_master);
mysql_close($conn_slave);
function printUsage() {
    global $argv;
    echo "Usage: php $argv[0] <slave_status|master_status|sync>\n";
}

function checkSlaveStatus($link) {
    if (($slave_status = getSlaveStatus($link))) {
        foreach ($slave_status as $key => $value) {
            echo "{$key}:{$value}\n";
        }
    } else {
        exit("Get slave status failed.\n");
    }
}

function checkMasterStatus($link) {
    $master_status = getMasterStatus($link);
    if ($master_status) {
        foreach ($master_status as $key => $value) {
            echo "{$key}:{$value}\n";
        }
    } else {
        exit("Get master status failed.\n");
    }
}

function getMasterStatus($link) {
    $query = 'show master status';
    $rs = mysql_fetch_assoc(mysql_query($query, $link));
    return $rs;
}

function getSlaveStatus($link) {
    $query = 'show slave status';
    $rs = mysql_fetch_assoc(mysql_query($query, $link));
    return $rs;
}

function lockMaster($link) {
    mysql_query('FLUSH TABLES WITH READ LOCK', $link);
}

function unLockMaster($link) {
    mysql_query('unlock tables', $link);
}
