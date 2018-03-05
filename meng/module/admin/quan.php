<?php
/**
 * @copyright   2008-2014 简好网络 <http://www.phpshe.com>
 * @creatdate   2012-1116 koyshe <koyshe@gmail.com>
 */
$menumark = 'quan';
$cache_category = cache::get('category');
switch ($act) {
	//#####################@ 优惠券添加 @#####################//
	case 'add':
		if (isset($_p_pesubmit)) {
			pe_token_match();
			$_p_info['quan_key'] = substr(md5(time()), 0, 10);	
			$_p_info['quan_atime'] = time();
			$_p_info['product_id'] = is_array($_p_product_id) ? implode(',', $_p_product_id) : '';
			if ($quan_id = $db->pe_insert('quan', pe_dbhold($_p_info))) {
				pe_success('添加成功!', 'admin.php?mod=quan');
			}
			else {
				pe_error('添加失败...');
			}
		}
		$product_list = array();
		$info['quan_sdate'] = date('Y-m-d');
		$info['quan_edate'] = date('Y-m-d', strtotime('+1 month'));		
		$seo = pe_seo($menutitle='添加优惠券', '', '', 'admin');
		include(pe_tpl('quan_add.html'));
	break;
	//#####################@ 优惠券修改(优惠券) @#####################//
	case 'edit':
		$quan_id = intval($_g_id);
		$info = $db->pe_select('quan', array('quan_id'=>$quan_id));
		$product_ids = $info['product_id'] ? explode(',', $info['product_id']) : array();
		$product_list = $db->pe_selectall('product', array('product_id'=>$product_ids));
		if ($info['quan_num_get']) $disabled = 'disabled="disabled"';
		if (isset($_p_pesubmit)) {
			pe_token_match();
			if ($_p_info['quan_num'] < $info['quan_num_get']) pe_error('发放量应大于已领取数');
			$_p_info['product_id'] = is_array($_p_product_id) ? implode(',', $_p_product_id) : '';
			if ($db->pe_update('quan', array('quan_id'=>$quan_id), pe_dbhold($_p_info))) {
				$db->pe_update('quanlog', array('quan_id'=>$quan_id), array('product_id'=>$_p_info['product_id']));
				pe_success('修改成功!', 'admin.php?mod=quan');
			}
			else {
				pe_error('修改失败...' );
			}
		}
		$seo = pe_seo($menutitle='修改优惠券', '', '', 'admin');
		include(pe_tpl('quan_add.html'));
	break;
	//#####################@ 优惠券删除 @#####################//
	case 'del':
		pe_token_match();
		if ($db->pe_delete('quan', array('quan_id'=>intval($_g_id)))) {
			pe_success('删除成功!');
		}
		else {
			pe_error('删除失败...');
		}
	break;
	//#####################@ 选择商品 @#####################//
	case 'product_list':
		$quan_id = intval($_g_id);
		pe_lead('hook/category.hook.php');
		$category_treelist = category_treelist();
		$cache_brand = cache::get('brand');
		$_g_name && $sqlwhere .= " and `product_name` like '%{$_g_name}%'";
		$_g_category_id && $sqlwhere .= is_array($category_cidarr = category_cidarr($_g_category_id)) ? " and `category_id` in('".implode("','", $category_cidarr)."')" : " and `category_id` = '{$_g_category_id}'";
		$_g_brand_id && $sqlwhere .= " and `brand_id` = '{$_g_brand_id}'";
		$sqlwhere .= " order by `product_id` desc";
		$info_list = $db->pe_selectall('product', $sqlwhere, '*', array(20, $_g_page));
		$product_idarr = $_g_product_id ? explode(',', $_g_product_id) : array();

		$seo = pe_seo($menutitle='选择商品', '', '', 'admin');
		include(pe_tpl('quan_product_list.html'));
	break;
	//#####################@ 添加商品 @#####################//
	case 'product_add':
		$info = $db->pe_select('product', array('product_id'=>$_g_product_id));
		$product_logo = pe_thumb($info['product_logo'], 40, 40);
		$product_url = pe_url('product-'.$info['product_id']);
$html = <<<html
	<tr id="{$info['product_id']}" class="product_id">
		<td>{$info['product_id']}<input type="hidden" name="product_id[]" value="{$info['product_id']}" /></td>
		<td><img src="{$product_logo}" width="40" height="40" class="imgbg" /></td>
		<td class="aleft"><a class="cblue" href="{$product_url}" target="_blank">{$info['product_name']}</a></td>
		<td>{$cache_category[$info['category_id']]['category_name']}</td>
		<td><span class="num corg">{$info['product_money']}</span></td>
		<td><a href="javascript:;" class="admin_btn">删除</a></td>
		<td></td>
	</tr>
html;
		echo json_encode(array('html'=>$html));
	break;
	//#####################@ 领取记录 @#####################//
	case 'log':
		pe_lead('hook/user.hook.php');
		user_quancheck();
		$_g_id && $sqlwhere['quan_id'] = $_g_id;
		$_g_user_name && $sqlwhere['user_name'] = $_g_user_name;
		if ($_g_state == 1) {
			$sqlwhere['quanlog_state'] = 1;
			$sqlwhere['order by'] = 'quanlog_utime desc';
		}
		else {
			$sqlwhere['order by'] = 'quanlog_atime desc';		
		}	
		//领取数和使用数
		$tongji['all'] = $db->pe_num('quan');
		$tongji['get'] = $db->pe_num('quanlog');
		$tongji['use'] = $db->pe_num('quanlog', array('quanlog_state'=>1));		

		$info_list = $db->pe_selectall('quanlog', $sqlwhere, '*', array(20, $_g_page));
		$seo = pe_seo($menutitle='优惠券列表', '', '', 'admin');
		include(pe_tpl('quan_log.html'));
	break;
	//#####################@ 优惠券列表 @#####################//
	default:		
		pe_lead('hook/user.hook.php');
		user_quancheck();
		$info_list = $db->pe_selectall('quan', array('order by'=>'quan_id desc'), '*', array(20, $_g_page));

		//领取数和使用数
		$tongji['all'] = $db->pe_num('quan');
		$tongji['get'] = $db->pe_num('quanlog');
		$tongji['use'] = $db->pe_num('quanlog', array('quanlog_state'=>1));		

		$seo = pe_seo($menutitle='优惠券列表', '', '', 'admin');
		include(pe_tpl('quan_list.html'));
	break;
}
?>