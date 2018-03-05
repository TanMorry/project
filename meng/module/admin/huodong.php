<?php
/**
 * @copyright   2008-2014 简好网络 <http://www.phpshe.com>
 * @creatdate   2012-1116 koyshe <koyshe@gmail.com>
 */
$menumark = 'huodong';
$cache_category = cache::get('category');
switch ($act) {
	//#####################@ 活动添加 @#####################//
	case 'add':
		if (isset($_p_pesubmit)) {
			pe_token_match();
			$_p_info['huodong_atime'] = time();			
			$_p_info['huodong_stime'] = strtotime($_p_info['huodong_stime']);
			$_p_info['huodong_etime'] = strtotime($_p_info['huodong_etime']);		
			if ($huodong_id = $db->pe_insert('huodong', pe_dbhold($_p_info))) {
				if (is_array($_p_huodong_money)) {
					foreach ($_p_huodong_money as $k=>$v) {
						$sql_set['huodong_id'] = $huodong_id;
						$sql_set['huodong_tag'] = $_p_info['huodong_tag'];
						$sql_set['huodong_stime'] = $_p_info['huodong_stime'];
						$sql_set['huodong_etime'] = $_p_info['huodong_etime'];
						$sql_set['huodong_money'] = $v;	
						$sql_set['product_id'] = $k;
						$db->pe_insert('huodongdata', pe_dbhold($sql_set));
					}				
				}
				pe_success('添加成功!', 'admin.php?mod=huodong');
			}
			else {
				pe_error('添加失败...');
			}
		}
		$info['huodong_tag'] = '团购';
		$info_list = array();
		$info['huodong_stime'] = time();
		$info['huodong_etime'] = strtotime('+10 day');	
		$seo = pe_seo($menutitle='添加活动', '', '', 'admin');
		include(pe_tpl('huodong_add.html'));
	break;
	//#####################@ 活动修改 @#####################//
	case 'edit':
		$huodong_id = intval($_g_id);
		if (isset($_p_pesubmit)) {
			pe_token_match();
			$_p_info['huodong_stime'] = strtotime($_p_info['huodong_stime']);			
			$_p_info['huodong_etime'] = strtotime($_p_info['huodong_etime']);
			if ($db->pe_update('huodong', array('huodong_id'=>$huodong_id), pe_dbhold($_p_info))) {
				$db->pe_update('product', " and `product_id` in(select `product_id` from `".dbpre."huodongdata` where `huodong_id` = '{$huodong_id}')", "product_money = product_smoney, product_hd_tag = '', product_hd_stime = 0, product_hd_etime = 0");
				$db->pe_delete('huodongdata', array('huodong_id'=>$huodong_id));
				if (is_array($_p_huodong_money)) {
					foreach ($_p_huodong_money as $k=>$v) {
						$sql_set['huodong_id'] = $huodong_id;
						$sql_set['huodong_tag'] = $_p_info['huodong_tag'];
						$sql_set['huodong_stime'] = $_p_info['huodong_stime'];
						$sql_set['huodong_etime'] = $_p_info['huodong_etime'];
						$sql_set['huodong_money'] = $v;	
						$sql_set['product_id'] = $k;
						$db->pe_insert('huodongdata', pe_dbhold($sql_set));
					}		
				}
				pe_success('修改成功!', 'admin.php?mod=huodong');
			}
			else {
				pe_error('修改失败...' );
			}
		}
		$info = $db->pe_select('huodong', array('huodong_id'=>$huodong_id));
		$sql = "select a.`huodong_money`, b.* from `".dbpre."huodongdata` a, `".dbpre."product` b where a.`product_id` = b.`product_id` and a.`huodong_id` = '{$huodong_id}' order by a.`huodongdata_id` asc";
		$info_list = $db->index('product_id')->sql_selectall($sql);

		$seo = pe_seo($menutitle='修改活动', '', '', 'admin');
		include(pe_tpl('huodong_add.html'));
	break;
	//#####################@ 活动删除 @#####################//
	case 'del':
		pe_token_match();
		$huodong_id = intval($_g_id);
		if ($db->pe_delete('huodong', array('huodong_id'=>$huodong_id))) {
			$db->pe_delete('huodongdata', array('huodong_id'=>$huodong_id));
			$db->pe_update('product', " and `product_id` in(select `product_id` from `".dbpre."huodongdata` where `huodong_id` = '{$huodong_id}')", "product_money = product_smoney, product_hd_tag = '', product_hd_stime = 0, product_hd_etime = 0");
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
		<td>{$info['product_id']}</td>
		<td><img src="{$product_logo}" width="40" height="40" class="imgbg" /></td>
		<td class="aleft" style="padding-left:0"><a class="cblue" href="{$product_url}" target="_blank">{$info['product_name']}</a></td>
		<td>{$cache_category[$info['category_id']]['category_name']}</td>
		<td><span class="num corg">{$info['product_smoney']}</span></td>
		<td><input type="text" name="huodong_money[{$info['product_id']}]" value="" product_money="{$info['product_smoney']}" class="inputall input60" /></td>
		<td><a href="javascript:;" class="admin_btn">删除</a></td>
		<td></td>
	</tr>
html;
		echo json_encode(array('html'=>$html));
	break;
	//#####################@ 活动列表 @#####################//
	default:
		$sqlwhere['order by']= '`huodong_id` desc';		
		$info_list = $db->pe_selectall('huodong', $sqlwhere, '*', array(20, $_g_page));
		$tongji['all'] = $db->pe_num('huodong');
		$seo = pe_seo($menutitle='促销活动', '', '', 'admin');
		include(pe_tpl('huodong_list.html'));
	break;
}
?>