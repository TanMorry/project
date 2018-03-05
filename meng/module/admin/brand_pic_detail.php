<?php
$menumark = 'brand_pic';
pe_lead('hook/cache.hook.php');
switch ($act) {
	//#####################@ 添加品牌图片 @#####################//
	case 'add':
		if (isset($_p_pesubmit)) {
			pe_token_match();
//			pe_lead('include/class/pinyin.class.php');
//			$pinyin = new pinyin();
//			$_p_info['brand_word'] = strtoupper(substr($pinyin->output($_p_info['brand_name']), 0, 1));
            $_p_pic_info['brand_pic_addtime'] = $_p_pic_info['brand_pic_edittime'] = time();
			if ($brand_pic_id = $db->pe_insert('brand_pic', pe_dbhold($_p_pic_info))) {
				if ($_FILES['brand_pic']['size']) {
					pe_lead('include/class/upload.class.php');
//                    var_dump($_FILES);die;
					$upload = new upload($_FILES['brand_pic'], 'data/attachment/brand/pic/', array('filename'=>$brand_pic_id));
					$db->pe_update('brand_pic', array('brand_pic_id'=>$brand_pic_id), array('brand_pic_addr'=>$upload->filehost));
				}
				cache_write('brand_pic');
				pe_success('添加成功!', 'admin.php?mod=brand_pic');
			}
			else {
				pe_error('添加失败!');
			}
		}
		$seo = pe_seo($menutitle='添加品牌图片', '', '', 'admin');
        $info_list = $db->pe_selectall('brand', array('order by'=>'brand_order asc, brand_id desc'), 'brand_id,brand_name');
//        $name_list = $db->pe_selectall('brand_pic', array('brand_pic_pid'=>'0','order by'=>'brand_pic_id desc'), 'brand_pic_id,brand_pic_name');
//        var_dump($name_list);die;
		include(pe_tpl('brand_pic_add.html'));
	break;
	//#####################@ 修改品牌图片 @#####################//
	case 'edit':
        $brand_pic_id = intval($_g_id);
		if (isset($_p_pesubmit)) {
			pe_token_match();
			if ($_FILES['brand_pic']['size']) {
				pe_lead('include/class/upload.class.php');
				$upload = new upload($_FILES['brand_pic'], 'data/attachment/brand/pic/', array('filename'=>$brand_pic_id));
                $_p_pic_info['brand_pic_addr'] = $upload->filehost;
			}
//			pe_lead('include/class/pinyin.class.php');
//			$pinyin = new pinyin();
//			$_p_info['brand_word'] = strtoupper(substr($pinyin->output($_p_info['brand_name']), 0, 1));

            $_p_pic_info['brand_pic_edittime'] = time();
			if ($db->pe_update('brand_pic', array('brand_pic_id'=>$brand_pic_id), pe_dbhold($_p_pic_info))) {
				cache_write('brand_pic');
				pe_success('修改成功!', 'admin.php?mod=brand_pic');
			}
			else {
				pe_error('修改失败...');
			}
		}
        $pic_info = $db->pe_select('brand_pic', array('brand_pic_id'=>$brand_pic_id));
        $info_list = $db->pe_selectall('brand', array('order by'=>'brand_order asc, brand_id desc'), 'brand_id,brand_name');
        $seo = pe_seo($menutitle='修改品牌图片', '', '', 'admin');
		include(pe_tpl('brand_pic_add.html'));
	break;
	//#####################@ 品牌图片排序（暂时不用） @#####################//
	case 'order':
		pe_token_match();
		foreach ($_p_brand_order as $k=>$v) {
			$result = $db->pe_update('brand', array('brand_id'=>$k), array('brand_order'=>$v));
		}
		if ($result) {
			cache_write('brand');
			pe_success('排序成功!');
		}
		else {
			pe_error('排序失败...');
		}
	break;
	//#####################@ 品牌图片删除 @#####################//
	case 'del':
		pe_token_match();
		$brand_pic_id = is_array($_p_brand_pic_id) ? $_p_brand_pic_id : intval($_g_id);
		if ($db->pe_delete('brand_pic', array('brand_pic_id'=>$brand_pic_id))) {
			pe_success('删除成功!');
		}
		else {
			pe_error('删除失败...');
		}
	break;
	//#####################@ 品牌原图片列表 @#####################//
	default :
		$info_list = $db->pe_selectall('brand_pic', array('brand_pic_pid'=>0,'order by'=>'brand_pic_id desc'), '*', array(30, $_g_page));
//        var_dump($info_list);die;
        $tongji['all'] = $db->pe_num('brand_pic');
		$seo = pe_seo($menutitle='品牌管理', '', '', 'admin');
		include(pe_tpl('brand_pic_list.html'));
	break;
}
?>