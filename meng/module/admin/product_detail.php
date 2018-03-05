<?php
/**
 * @copyright   2008-2015 简好网络 <http://www.phpshe.com>
 * @creatdate   2012-0501 koyshe <koyshe@gmail.com>
 */
$menumark = 'product_detail';
pe_lead('hook/cache.hook.php');
//pe_lead('hook/category.hook.php');
//$category_treelist = category_treelist();
$cache_brand = cache::get('brand');

switch ($act) {
	//#####################@ 商品详情添加 @#####################//
	case 'add':
		if (isset($_p_pesubmit)) {
            pe_token_match();
			$_p_info['ip_id'] = $_p_info['ip_id'];
			$_p_info['canvas_id'] = $_p_info['canvas_id'];
			$_p_info['detail_src'] = $_p_product_detail['0'];
//			var_dump($_p_info);die;
			if ($detail_id = $db->pe_insert('product_detail', pe_dbhold($_p_info))) {
				cache_write('category');
				pe_success('添加成功!', 'admin.php?mod=product_detail&state=1');
			}
			else {
				pe_error('添加失败...');
			}
		}
		$album_list = array();
        //拿出画布去重复
        $sql = "select canvas_id,canvas_name from ".dbpre."canvas group by canvas_name";
        $canvas_list = $db->sql_selectall($sql);
//        var_dump($canvas_list);die;
		$seo = pe_seo($menutitle='添加商品', '', '', 'admin');
		include(pe_tpl('product_detail_add.html'));
	break;
    //#####################@ 商品详情修改 @#####################//
	case 'edit':
		$detail_id = intval($_g_id);
		if (isset($_p_pesubmit)) {
			pe_token_match();
            $_p_info['ip_id'] = $_p_info['ip_id'];
            $_p_info['canvas_id'] = $_p_info['canvas_id'];
            $_p_info['detail_src'] = $_p_product_detail['0'];
			if ($db->pe_update('product_detail', array('id'=>$detail_id), pe_dbhold($_p_info))) {
				pe_success('修改成功!', $_g_fromto);
			}
			else {
				pe_error('修改失败!' );
			}
		}
		$info = $db->pe_select('product_detail', array('id'=>$detail_id));
//        var_dump()
		$album_list = explode(',', $info['detail_src']);
        $sql = "select canvas_id,canvas_name from ".dbpre."canvas group by canvas_name";
        $canvas_list = $db->sql_selectall($sql);
		$seo = pe_seo($menutitle='修改商品', '', '', 'admin');
		include(pe_tpl('product_detail_add.html'));
	break;
	//#####################@ 商品详情删除 @#####################//
	case 'del':
		pe_token_match();
		$detail_id = is_array($_p_detail_id) ? $_p_detail_id : $_g_id;
		if ($db->pe_delete('product_detail', array('id'=>$detail_id))) {
			pe_success('删除成功!');
		}
		else {
			pe_error('删除失败...');
		}
	break;

	//#####################@ 详情列表 @#####################//*/
	default :
        $sql = "select pd.id,detail_src,b.brand_name,canvas_name from ".dbpre."product_detail pd left join ".dbpre."brand b on pd.ip_id = b.brand_id left join ".dbpre."canvas c on c.canvas_id = pd.canvas_id where 1=1";
            $_g_canvas_id && $sql .= " and c.canvas_id={$_g_canvas_id}";//" and `product_name` like '%{$_g_name}%'";
//        }
        $_g_brand_id && $sql .= " and b.brand_id ={$_g_brand_id}";
//        echo $sql;
        $canvas_list = $db->sql_selectall($sql, array(20, $_g_page));
        $all_canvas = $db->pe_selectall('canvas','','canvas_id,canvas_name');
//        var_dump($all_canvas);die;
//        print_r($_GET);
        $tongji['all'] = $db->pe_num('product_detail');
		$seo = pe_seo($menutitle='商品详情', '', '', 'admin');
		include(pe_tpl('product_detail.html'));
	break;
}
?>