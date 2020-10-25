<?php 
	/**
	  * PlutoFramework
	  * Function:Route
      * @author Alien <a457418121@gmail.com>
	  */
	  
function parseUri(){
	$realUri = preg_replace('~^'.WS_FOLDER.'~','',$_SERVER['REQUEST_URI'],1);
	$arrayUri = explode('/',$realUri);
	//if the last element empty，get rid of it
	if(empty($arrayUri[count($arrayUri)-1])){
		array_pop($arrayUri); //get rid of last element
	}
	//if the first element empty,get rid of it
	if(empty($arrayUri[0])){
		array_shift($arrayUri); //get rid of first element
	}
	return $arrayUri;	//return parse uri
}

//get rid of the uri into an array at the slashes
function remove_unwanted_slashes($dirty_path){
	return preg_replace('~(?<!:)//~','/',$dirty_path);  //对正则表达式的解释：~ 开始限定符 ()用于定义反向观察  ?<! 让正则表达式查看匹配部分前一个字符 : 不想要匹配的字符 // 查找的字符，这里是双斜线
}

/**
 * @param $arrayUri URI
 * @return string   The controller classname
 */
function getControllername($arrayUri){      //暂时无需改写变量，去掉& 有需要再加
    $controller = array_shift($arrayUri);
    return ucfirst($controller);
}

function classAutoloader($className){
    $fname = strtolower($className);

    $classLocations = array(
        FW_PATH . '/core/class.' . $fname . '.php',
        FW_PATH . '/model/class.' . $fname . '.php',
        FW_PATH . '/controller/class.' . $fname . '.php',
    );

    //loop through the location array and checks for a file to load
    foreach($classLocations as $loc){
        if(file_exists($loc)){
            require_once $loc;
            return TRUE;
        }
    }

    //if a valid class not found
    throw new exception("Class $className not found.");
    }