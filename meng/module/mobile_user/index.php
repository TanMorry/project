<?php
$menumark = 'index';
pe_lead('hook/order.hook.php');
$cache_payway = cache::get('payway');
switch($act) {
	//#####################@ 个人中心 @#####################//
	default:
		$info = $db->pe_select('user', array('user_id'=>$_s_user_id));
		//统计订单数量
		$tongji['wpay'] = $db->pe_num('order', array('user_id'=>$_s_user_id, 'order_state'=>'wpay'));
		$tongji['wsend'] = $db->pe_num('order', array('user_id'=>$_s_user_id, 'order_state'=>'wsend'));
		$tongji['wpj'] = $db->pe_num('order', array('user_id'=>$_s_user_id, 'order_state'=>'success', 'order_comment'=>0));
		$tongji['quan'] = $db->pe_num('quanlog', array('user_id'=>$_s_user_id, 'quanlog_state'=>0));
		
		$seo = pe_seo($menutitle='个人中心');
		include(pe_tpl('index.html'));
	break;
}
?>