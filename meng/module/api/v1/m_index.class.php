<?php
include ('base.class.php');
/**
 * 首页接口
 */
class m_index extends base {
    function __construct($method)
    {
        parent::__construct($method);
    }

    /**
	* 首页
	*/
	function attention_recommend()
	{
        if($GLOBALS['RequestMethod'] != 'GET'){
            throw new Exception('请求的方式不被允许',405);
        }
        if (!pe_login('user')) {
            //未登录显示销售数量从高到低的Ip
            $result = $this->hot_recommend();
        }else{
            //已登录根据用户的点赞数，推荐Ip，没有点赞数，则推荐销售数量从高到低的IP
            $zan_sql = "select * from ".dbpre.'zan z left join '.dbpre.'product p on z.product_id = p.brand_id where user_id = '.$_SESSION['user_id'] ;
            $result = $this->db->sql_selectall($zan_sql);
            var_dump($result);die;
        }
//        if(){
//
//        }
        $result = $this->db->pe_select('comment', array('comment_id'=>1));
	}

	/*
	 * 热门推荐
	 */
	public function hot_recommend(){

    }
}