<?php
switch ($act) {
	//#####################@ 商品收藏 @#####################//
	case 'collect':
		$product_id = intval($_g_id);
		if (!pe_login('user')) pe_jsonshow(array('result'=>false, 'show'=>'请先登录...'));
		$info = $db->pe_select('product', array('product_id'=>$product_id), 'product_id, product_collectnum');
		if (!$info['product_id']) pe_jsonshow(array('result'=>false, 'show'=>'无效商品...'));
		$sql_where['product_id'] = $product_id;	
		$sql_where['user_id'] = $_s_user_id;
		if ($db->pe_num('collect', pe_dbhold($sql_where))) {
			$db->pe_delete('collect', pe_dbhold($sql_where));
			product_num($product_id, 'collectnum');
			pe_jsonshow(array('result'=>true, 'show'=>'取消成功！', 'type'=>'del'));
		}
		else {
			$sql_where['collect_atime'] = time();
			$db->pe_insert('collect', pe_dbhold($sql_where));
			product_num($product_id, 'collectnum');
			pe_jsonshow(array('result'=>true, 'show'=>'收藏成功！', 'type'=>'add'));
		}
	break;
	//#####################@ 商品列表 @#####################//
	case 'list':
		if ($_g_keyword) {
			$search_history = pe_getcookie('search_history', 'array');
			array_unshift($search_history, pe_dbhold($_g_keyword));
			pe_setcookie('search_history', array_unique($search_history));
		}
		//分类筛选
		$keyword = $_g_keyword;
		$category_id = intval($id);
		//var_dump($category_id);die;
		$category_name = $category_id ? $cache_category[$category_id]['category_name'] : '全部分类';
		$category_zk_id = is_array($cache_category_arr[$category_id]) ? $category_id : intval($cache_category[$category_id]['category_pid']);
		//品牌筛选
		$brand_list = cache::get('category_brand');
		$brand_list = $category_id ? $brand_list[$category_id] : cache::get('brand');
		$brand_default = array(0=>array('brand_id'=>0, 'brand_name'=>'全部品牌'));
		$brand_list = count($brand_list) ? ($brand_default + $brand_list) : $brand_default;
		$brand_id = intval($_g_brand);
		$brand_name = $brand_list[$brand_id]['brand_name'];		
		//排序筛选
		$orderby_list[''] = array('name'=>'综合排序', 'url'=>pe_updateurl('orderby', ''));
		$orderby_list['sellnum_desc'] = array('name'=>'销量优先', 'url'=>pe_updateurl('orderby', 'sellnum_desc'));		
		$orderby_list['clicknum_desc'] = array('name'=>'人气优先', 'url'=>pe_updateurl('orderby', 'clicknum_desc'));
		$orderby_list['commentnum_desc'] = array('name'=>'评价优先', 'url'=>pe_updateurl('orderby', 'commentnum_desc'));
		$orderby_list['money_desc'] = array('name'=>'价格由高到低', 'url'=>pe_updateurl('orderby', 'money_desc'));
		$orderby_list['money_asc'] = array('name'=>'价格由低到高', 'url'=>pe_updateurl('orderby', 'money_asc'));
		$orderby_id = $_g_orderby;
		$orderby_name = $orderby_list[$orderby_id]['name'];
		//检索条件
		$sql_where = " and `product_state` = 1";
		if ($category_id) {
			$sql_where .= is_array($category_cidarr = category_cidarr($category_id)) ? " and `category_id` in('".implode("','", $category_cidarr)."')" : " and `category_id` = '{$category_id}'";
		}
		$_g_brand && $sql_where .= " and `brand_id` = ".intval($_g_brand);
		//$_g_keyword && $sql_where .= " and `product_name` like '%".pe_dbhold($_g_keyword)."%'";
		$_g_keyword && $sql_where .= " and (`product_name` like '%".pe_dbhold($_g_keyword)."%'OR brand_id IN (SELECT brand_id FROM pe_brand WHERE brand_name LIKE '%".pe_dbhold($_g_keyword)."%'))";
		$orderby_arr = array('sellnum_desc', 'money_desc', 'money_asc', 'commentnum_desc', 'clicknum_desc');
		if (in_array($_g_orderby, $orderby_arr)) {
			$orderby = explode('_', $_g_orderby);
			$sql_where .= " order by `product_{$orderby[0]}` {$orderby[1]}";
		}
		else {
			$sql_where .= " order by `product_order` asc, `product_id` desc";
		}
		$info_list = $db->pe_selectall('product', $sql_where, '*', array(40, $_g_page));
		$menutitle = '商品列表';
		$info = $db->pe_select('category', array('category_id'=>$category_id));
		$seo = pe_seo($info['category_title']?$info['category_title']:$info['category_name'], $info['category_keys'], pe_text($info['category_desc']));
		include(pe_tpl('product_list.html'));
	break;
	//#####################@ 每日精选 @#####################//
	case 'choice':
		if ($_g_keyword) {
			$search_history = pe_getcookie('search_history', 'array');
			array_unshift($search_history, pe_dbhold($_g_keyword));
			pe_setcookie('search_history', array_unique($search_history));
		}
		//分类筛选
		$keyword = $_g_keyword;
		$category_id = intval($id);
		//var_dump($category_id);die;
		$category_name = $category_id ? $cache_category[$category_id]['category_name'] : '小编推荐';
		$category_zk_id = is_array($cache_category_arr[$category_id]) ? $category_id : intval($cache_category[$category_id]['category_pid']);
		//品牌筛选
		$sql="SELECT SUM(product_sellnum) as sellnum,b.brand_name,b.brand_id from ".dbpre."product as a LEFT JOIN ".dbpre."brand as b ON a.brand_id = b.brand_id GROUP BY a.brand_id ORDER BY sellnum DESC limit 10";
        $brand_list = $db->sql_selectall($sql);
		//$brand_list = cache::get('category_brand');
		//$brand_list = $category_id ? $brand_list[$category_id] : cache::get('brand');
	
		//$brand_default = array('hot'=>array('brand_id'=>0, 'brand_name'=>'热门IP'));
		//$brand_list = count($brand_list) ? ($brand_default + $brand_list) : $brand_default;
		var_dump($brand_list);
		$brand_id = intval($_g_brand);
		var_dump($brand_id);
		$brand_name = $brand_list[$brand_id]['brand_name'];
		var_dump($brand_name);
		//排序筛选
		$orderby_list[''] = array('name'=>'综合排序', 'url'=>pe_updateurl('orderby', ''));
		$orderby_list['sellnum_desc'] = array('name'=>'销量优先', 'url'=>pe_updateurl('orderby', 'sellnum_desc'));		
		$orderby_list['clicknum_desc'] = array('name'=>'人气优先', 'url'=>pe_updateurl('orderby', 'clicknum_desc'));
		$orderby_list['commentnum_desc'] = array('name'=>'评价优先', 'url'=>pe_updateurl('orderby', 'commentnum_desc'));
		$orderby_list['money_desc'] = array('name'=>'价格由高到低', 'url'=>pe_updateurl('orderby', 'money_desc'));
		$orderby_list['money_asc'] = array('name'=>'价格由低到高', 'url'=>pe_updateurl('orderby', 'money_asc'));
		$orderby_id = $_g_orderby;
		$orderby_name = $orderby_list[$orderby_id]['name'];
		//检索条件
		$sql_where = " and `product_state` = 1";
		if ($category_id) {
			var_dump($category_id);
			$sql_where .= is_array($category_cidarr = category_cidarr($category_id)) ? " and `category_id` in('".implode("','", $category_cidarr)."')" : " and `category_id` = '{$category_id}'";
		}else{
			//小编推荐
			$sql_where .=" and product_istuijian=1";
		}
		$_g_brand && $sql_where .= " and `brand_id` = ".intval($_g_brand);
		//$_g_keyword && $sql_where .= " and `product_name` like '%".pe_dbhold($_g_keyword)."%'";
		$_g_keyword && $sql_where .= " and (`product_name` like '%".pe_dbhold($_g_keyword)."%'OR brand_id IN (SELECT brand_id FROM pe_brand WHERE brand_name LIKE '%".pe_dbhold($_g_keyword)."%'))";
		$orderby_arr = array('sellnum_desc', 'money_desc', 'money_asc', 'commentnum_desc', 'clicknum_desc');
		if (in_array($_g_orderby, $orderby_arr)) {
			$orderby = explode('_', $_g_orderby);
			$sql_where .= " order by `product_{$orderby[0]}` {$orderby[1]}";
		}
		else {
			$sql_where .= " order by `product_order` asc, `product_id` desc";
		}
		
		$info_list = $db->pe_selectall('product', $sql_where, '*', array(40, $_g_page));

		$menutitle = '商品列表';
		$info = $db->pe_select('category', array('category_id'=>$category_id));
		$seo = pe_seo($info['category_title']?$info['category_title']:$info['category_name'], $info['category_keys'], pe_text($info['category_desc']));
		include(pe_tpl('choice_list.html'));
	break;
	//#####################@ 商品内容 @#####################//
	default:
		$product_id = intval($act);
		$info = $db->pe_select('product', array('product_id'=>$product_id));
		if (!$info['product_id']) pe_404();
		$category_id = $info['category_id'];
		$category_pid = $cache_category[$category_id]['category_pid'];
		$comment_ratearr = explode(',', $info['product_commentrate']);
		if ($info['product_commentnum']) {
			$comment_star = pe_num($info['product_commentstar']/$info['product_commentnum'], 'round', 1, true);
			$comment_rate_hao = intval($comment_ratearr[0]/$info['product_commentnum']*100);
			$comment_rate_zhong = intval($comment_ratearr[1]/$info['product_commentnum']*100);
			$comment_rate_cha = intval($comment_ratearr[2]/$info['product_commentnum']*100);
		}
		else {
			$comment_star = '5.0';
			$comment_rate_hao = '100';
			$comment_rate_zhong = '0';
			$comment_rate_cha = '0';
		}
		$comment_point = ($cache_setting['point_state'] && $cache_setting['point_comment']) ? "(+{$cache_setting['point_comment']}积分)":'';
		//优惠券列表
		$quan_list = $db->pe_selectall('quan', " and `quan_edate` >= '".date('Y-m-d')."' and (`product_id` = '' or find_in_set({$product_id}, `product_id`)) order by `quan_money` asc");
		//商品规格
		$rule_list = $info['product_rule'] ? unserialize($info['product_rule']) : array();
		$prorule_list = $db->pe_selectall('prorule', array('product_id'=>$product_id));
		foreach ($prorule_list as $k=>$v) {
			if ($info['product_money'] != $info['product_smoney']) $prorule_list[$k]['product_money'] = $info['product_money'];
			if (!$v['product_num']) unset($prorule_list[$k]);
		}
		$prorule_list = json_encode($prorule_list);
		//更新点击
		product_num($product_id, 'clicknum');		
		//商品相册
		$album_list = explode(',', $info['product_album']);
		//判断收藏
		$collect = $db->pe_select('collect', array('product_id'=>$product_id, 'user_id'=>$_s_user_id), 'collect_id');

		$menutitle = '商品详情';
		$info['product_desc'] = $info['product_desc'] ? $info['product_desc'] : pe_cut(pe_text($info['product_text']), 200);
		$seo = pe_seo($info['product_name'], $info['product_keys'], $info['product_desc']);
		include(pe_tpl('product_view.html'));
	break;
}
?>