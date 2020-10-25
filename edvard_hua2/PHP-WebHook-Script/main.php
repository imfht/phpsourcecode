<?php
/**
 * Class Main
 * @author Edward
 * @time 2015.08.23 08:13
 */

/**
 * format var_dump
 * @param void $varVal 
 * @param str $varName 
 * @param bool $isExit 
 */
function dump($varVal, $isExit = FALSE){
    ob_start();
    var_dump($varVal);
    $varVal = ob_get_clean();
    $varVal = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $varVal);
    echo '<pre>'.$varVal.'</pre>';
    $isExit && exit();
}

/**
 * @param str $msg
 */
function writeLog($msg){
    date_default_timezone_set("Asia/Shanghai");
    file_put_contents('log.txt',"\r\n".date("h:i:sa").': '.$msg,FILE_APPEND|LOCK_EX);
}

/**
 * @see http://php.net/manual/en/function.proc-open.php
 */
$descriptorspec = array(
    0 => array(
        "pipe",
        "r"
    ), // stdin is a pipe that the child will read from
    1 => array(
        "pipe",
        "w"
    ), // stdout is a pipe that the child will write to
    2 => array(
        "file",
        __DIR__ . "/error-output.txt",
        "a"
    ) // stderr is a file to write to
);

$postData = json_decode($_POST['hook']);
if($postData['password'] !== 'password'){
    writeLog('un-authorization request:'.$_SERVER['HTTP_HOST']);
    die;
}

$username = '##You Username##';
$password = '##You Password##';

$cwd = '####';  //The initial working dir for the command. This must be an absolute directory path
$projectSuffix = '##owner##/##project_name##.git';
$branch = '##Branch Name##';

$updateUrl = 'https://'.$username.':'.$password.'@git.oschina.net/'.$projectSuffix;

// change authority 
$processes[0] = proc_open('touch log.txt',$descriptorspec,$pipes,__DIR__,NULL);
$processes[1] = proc_open('chmod 777 log.txt',$descriptorspec,$pipes,__DIR__,NULL);
$processes[3] = proc_open('touch error-output.txt',$descriptorspec,$pipes,__DIR__,NULL);
$processes[4] = proc_open('chmod 777 error-output.txt',$descriptorspec,$pipes,__DIR__,NULL);
// update code
$processes[5] = proc_open('git pull '.$updateUrl.' '.$branch,$descriptorspec,$pipes,$cwd,NULL);
// can change the file authority created by itself
$processes[6] = proc_open('chmod -R 777 '.$cwd,$descriptorspec,$pipes,$cwd,NULL);

$count = count($processes);
for($i = 0; $i < $count; $i ++){
    if(is_resource($processes[$i]))
        $return_value = proc_close($processes[$i]);
    
    if($return_value == 0){
        writeLog('Command '.$i.' success \r\n');
    }else
        writeLog('Command '.$i.' faild.');
}

?>