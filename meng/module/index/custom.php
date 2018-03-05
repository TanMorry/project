<?php
/**
 * @copyright   2008-2014 简好网络 <http://www.phpshe.com>
 * @creatdate   2012-0501 koyshe <koyshe@gmail.com>
 */
switch ($act) {
	//#####################@ 文章列表 @#####################//
	case 'detail':
		$userid = $db->pe_select('product',array('product_id'=>$_g_pid),'userid');
		if($userid['userid']==='0'){
			pe_error('该商品不支持定制');
		}
		include(pe_tpl('custom_detail.html'));
	break;
	//#####################@ 文章内容 @#####################//
	default:
		$category_indexlist = array();
		foreach((array)$cache_category_arr[0] as $k=>$v) {
			$v['product_list'] = $db->pe_selectall('product', array('category_id'=>category_cidarr($v['category_id']), 'product_state'=>1, 'order by'=>'product_order asc, product_id desc'), '*', array(8));
			$v['ad'] = $db->pe_select('ad', array('ad_position'=>'index_category', 'category_id'=>$v['category_id'], 'ad_state'=>1, 'order by'=>'ad_order asc, ad_id desc'));
			$category_indexlist[] = $v;
		}
		include(pe_tpl('custom_list.html'));
	break;
}
?>