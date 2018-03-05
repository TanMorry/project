<?php

switch ($act) {
	//#####################@ 添加广告 @#####################//
	case 'add':
		if (isset($_p_pesubmit)) {
			pe_token_match();
			if ($_FILES['ad_logo']['size']) {
				pe_lead('include/class/upload.class.php');
				$upload = new upload($_FILES['ad_logo']);
				$_p_info['ad_logo'] = $upload->filehost;
			}
			$_p_info['category_id'] = intval($_p_info['category_id']);
			if ($db->pe_insert('ad', pe_dbhold($_p_info))) {
				cache_write('ad');
				pe_success('添加成功!', 'admin.php?mod=ad');
			}
			else {
				pe_error('添加失败...');
			}
		}
		$info['ad_state'] = 1;
		$seo = pe_seo($menutitle='添加广告', '', '', 'admin');
		include(pe_tpl('ad_add.html'));
	break;

	case 'status':
		pe_token_match();
		$design_id = is_array($_p_id) ? $_p_id : $_g_id;
		if ($db->pe_update('design', array('id'=>$design_id), array('status'=>$_g_status))) {
			pe_success("操作成功!");
		}
		else {
			pe_error("操作失败...");
		}
		break;
	//#####################@ 效果图列表 @#####################//
	default :
		$sql = "SELECT id,a.canvas_id,a.brand_pic_id,`status`,designer_name,up_time,`name`,intro,pic_addr,canvas_model,brand_pic_addr FROM pe_design a LEFT JOIN pe_designer b ON a.author_id = b.designer_id LEFT JOIN
pe_canvas c ON a.canvas_id = c.canvas_id LEFT JOIN pe_brand_pic d ON a.brand_pic_id = d.brand_pic_id";
		$info_list = $db->sql_selectall($sql, array(20, $_g_page));
		//var_dump($info_list);die;
		include(pe_tpl('design_list.html'));
	break;
}
?>