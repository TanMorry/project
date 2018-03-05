<?php
include ('base.class.php');
/**
 * 用户接口(考虑是不是要用类还是直接方法)
 */
class m_user extends base {
	function __construct($method)
    {
        parent::__construct($method);
    }

    /**
	* 用户登录
	*/
	function login()
	{
//	    echo 123;die;
		$this->handleUser();
	}
	
	/**
	* 用户注册
	*/
	function register()
	{
        if($GLOBALS['RequestMethod'] != 'POST'){
            throw new Exception('请求的方式不被允许',405);
        }
	}
	
	/**
	 * 请求用户资源
	 */
	 function handleUser()
	 {
		 if($GLOBALS['RequestMethod'] != 'POST'){
			throw new Exception('请求的方式不被允许',405);
		 }
	 }
	 
}