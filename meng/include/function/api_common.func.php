<?php
/**
 * HTTP状态码
 */
$GLOBALS['status_code'] = [
    '200'=>'Ok',
    '204'=>'No Content',
    '400'=>'Bad Request',
    '401'=>'Unauthorized',
    '403'=>'Forbidden',
    '404'=>'Not Found',
    '405'=>'Method Not Allowed',
    '500'=>'Server Internet Error',
];

/**
 * 允许请求资源方法
 */
$GLOBALS['allowRequestMethods'] = array('GET','POST','PUT','DELETE','OPTIONS');

/**
 * 允许请求的资源列表
 */
$GLOBALS['allowResources'] = [
                                'attention_recommend',//首页关注推荐
                                'login',//登录
                                'register'//注册
                                ];
/**
 * 请求方法
 */
$GLOBALS['RequestMethod'] = strtoupper(trim($_SERVER['REQUEST_METHOD']));
/**
 * 请求的资源路径
 */
$GLOBALS['path'] = $_SERVER['PATH_INFO'];

$m_pre='m_';
$v='v1';


/**
 * 初始化请求方法
 */
function SetUpRequestMethod()
{
	if(!in_array($GLOBALS['RequestMethod'],$GLOBALS['allowRequestMethods']))
	{
		throw new Exception('请求的方法不被允许',404);
	}
}

/**
 * 初始化请求资源
 */
function SetUpResource()
{
    global $v;
	$params = array_values(array_filter(explode('/',$GLOBALS['path'])));
    if($params[0] != $v)
    {
        throw new Exception( '没有该版本的api',404);
    }
//    var_dump($params);die;
	if(!in_array($params[2],$GLOBALS['allowResources']))
	{
		throw new Exception( '请求资源不存在',404);
	}
}


/**
 * API json返回
 */
function api_json($array,$code = 0)
{
	if($code>0 && $code != 200 && $code != 204){
		header("HTTP/1.1 ".$code." ".$GLOBALS['status_code'][$code]);
	}
    header('Content-Type:application/json;charset=utf-8');
    echo json_encode($array,JSON_UNESCAPED_UNICODE);
	exit();
}