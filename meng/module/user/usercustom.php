<?php
/**
 * @copyright   2008-2014 简好网络 <http://www.phpshe.com>
 * @creatdate   2016-0101 koyshe <koyshe@gmail.com>
 */
$menumark = 'usercustom';
switch ($act) {
	//#####################@ 用户自定义商品 @#####################//
	default:
        $page = isset($_REQUEST["page"])?$_REQUEST["page"]:1;
        $field = 'product_id,product_name,product_text,product_logo,user_name';
        $sql = 'select '.$field.' from '.dbpre.'product p left join '.dbpre.'user u on p.userid = user_id where p.userid='.intval($_SESSION['user_id']);
        $product_info =$db->sql_selectall($sql,array(12,$page));
		$tongji['all'] = $db->pe_num('product', array('userid'=>$_s_user_id));
		$seo = pe_seo($menutitle='自定义商品');
		include(pe_tpl('user_custom.html'));
	break;
}
?>