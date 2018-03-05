<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/14
 * Time: 9:52
 */
include ('common.php');

/**
 * 检查请求的资源和请求方法是或存在
 */
try{
	SetUpRequestMethod();
	SetUpResource();
} catch (Exception $e){
	api_json(['error' => $e->getMessage()], $e->getCode());
}
$params = array_values(array_filter(explode('/',$GLOBALS['path'])));
if (in_array($params[1].".class.php", pe_dirlist ("{$pe['path_root']}module/api/".$v."/*.class.php"))) {
    include("{$pe['path_root']}module/api/".$v."/".$params[1].".class.php");
}
//var_dump($params);die;
$api = new $params[1]($params[2]);
pe_result();