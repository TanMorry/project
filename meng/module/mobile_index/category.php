<?php
switch ($act) {
	//#####################@ 商品分类 @#####################//
	default:
		$seo = pe_seo($menutitle='全部分类');
		include(pe_tpl('category_list.html'));
	break;
}
?>