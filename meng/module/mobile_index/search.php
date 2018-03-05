<?php
switch ($act) {
	//#####################@ 搜索 @#####################//
	default:
		$info_list = pe_getcookie('search_history', 'array');

		$seo = pe_seo($menutile='搜索');
		include(pe_tpl('search.html'));
	break;
}
?>