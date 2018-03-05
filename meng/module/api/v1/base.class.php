<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13
 * Time: 16:34
 */
class base
{
    public $db;
    function __construct($method)
    {
        global $pe;
        //链接数据库
        $this->db = new db($pe['db_host'], $pe['db_user'], $pe['db_pw'], $pe['db_name'], $pe['db_coding']);
        //检测请求的资源是否存在
        try{
            $this->$method();
        }catch(Exception $e){
            api_json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}