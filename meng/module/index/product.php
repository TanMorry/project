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
			pe_jsonshow(array('result'=>true, 'show'=>'取消成功！', 'num'=>$info['product_collectnum']-1));
		}
		else {
			$sql_where['collect_atime'] = time();
			$db->pe_insert('collect', pe_dbhold($sql_where));
			product_num($product_id, 'collectnum');
			pe_jsonshow(array('result'=>true, 'show'=>'收藏成功！', 'num'=>$info['product_collectnum']+1));
		}
	break;
    //#####################@ 生成定制商品 @#####################//
    case 'dz_add_pro';
        if(isset($_p_pesubmit)){
//            pe_token_match();
            //先通过category_id查出该分类下的模板的价格和规格等基础信息  分类id要换成canvas_id
//            $category_id = intval($_p_category_id);
            $canvasid = intval($_p_canvasId);
            $field = 'canvas_keys,canvas_desc,canvas_money,canvas_smoney,canvas_mmoney,canvas_wlmoney,canvas_point,canvas_mark,canvas_weight,canvas_state,canvas_order,canvas_num,canvas_sellnum,canvas_clicknum,canvas_collectnum,canvas_asknum,canvas_commentnum,canvas_commentrate,canvas_commentstar,canvas_hd_tag,canvas_hd_stime,canvas_hd_etime,canvas_istuijian,canvas_rule,category_id,brand_id,rule_id';
            $pro_info = $db->pe_select('canvas', array('canvas_id'=>$canvasid),$field);
//            var_dump($pro_info);die;
            $array = array();
            foreach ($pro_info as $key=>$val){
                $newkey = str_ireplace("canvas","product",$key);
                $array[$newkey] = $val;
            }
//            var_dump($array);die;
            $array['product_logo'] = trim($_POST['logo']);
            $array['product_album'] = trim($_POST['logo']);
            $array['product_name'] = trim($_POST['name']);
            $array['product_linian'] = trim($_POST['text']);
            $array['brand_id'] = intval($_POST['brand_id']);
            $array['canvas_id'] = intval($canvasid);//前台需要
//            var_dump($_SESSION);die;
            $array['userid'] = $_SESSION['user_id'];
            $array['product_atime'] = time();
            //先从详情表中拿到与canvas_id和brand_pic_id相符的详情图片，然后存入产品表中
//            var_dump($array);
            $detail_img = $db->pe_selectall('product_detail',array('canvas_id'=>intval($canvasid),'ip_id'=>intval($_POST['brand_id'])),'detail_src');
//            $array['product_text'] = stripslashes('<img src="http://localhost/'.$detail_img[0]['detail_src'].'" alt="" />');
			if($detail_img){
				$array['product_text'] = stripslashes('<img src="http://www.9000meng.com/'.$detail_img[0]['detail_src'].'" alt="" />');
			}

//            exit(var_dump($_REQUEST));
            if ($product_id = $db->pe_insert('product', pe_dbhold($array,array('product_rule')))) {
//                product_callback($product_id);
//                cache_write('category');
                $pro_dz['pid'] = $product_id;
                $pro_dz['ip_img_ads'] =  substr($_POST['drawImage'],1);
                $pro_dz['base_img_ads'] = substr($_POST['baseImage'],1);
                $pro_dz['mask_img_ads'] = substr($_POST['maskImage'],1);
                $pro_dz['ip_coordinate'] = $_POST['dix'].','.$_POST['diy'];
                $pro_dz['ip_scale'] = $_POST['dscalex'].','.$_POST['dscaley'];
                $pro_dz['base_coordinate'] = $_POST['basex'].','.$_POST['basey'];
                $pro_dz['f_coordinate'] = $_POST['fontx'].','.$_POST['fonty'];
                $pro_dz['f_content'] = $_POST['fontText'];
                $pro_dz['f_rotation'] = $_POST['rotation'].','.$_POST['angleIndex'];
                $pro_dz['f_size'] = intval($_POST['fontSize']);
                $pro_dz['ip_rotate'] = intval($_POST['ip_rotate']);
                $pro_dz['f_color'] = $_POST['fontColor'];
                $dz_id = $db->pe_insert('product_dz',pe_dbhold($pro_dz));
                if(!empty($pro_info['canvas_rule'])){
                    $prorule = $db->pe_selectall('prorule',array('product_id'=>0,'canvas_id'=>$canvasid));
                    foreach ($prorule as $key=>$val){
                        $prorule[$key]['product_id'] = $product_id;
                        unset($prorule[$key]['prorule_id']);
                    }
//                var_dump($prorule);die;
                    $db->pe_insert('prorule', $prorule);
                }

//                die;
                if($dz_id){
                    pe_jsonshow(array('result'=>true, 'show'=>'定制成功','pid'=>$product_id));
                }else{
                    pe_jsonshow(array('result'=>false, 'show'=>'定制失败...'));
                }
            }
            else {
                pe_jsonshow(array('result'=>false, 'show'=>'定制失败...'));
            }
        }else{
            pe_jsonshow(array('result'=>false, 'show'=>'定制失败...'));
        }
    break;
	//#####################@ 商品列表 @#####################//
	case 'list':
		$category_id = intval($id);
		$category_sid = is_array($cache_category_arr[$category_id]) ? $category_id : intval($cache_category[$category_id]['category_pid']);
		$category_sname = $category_sid ? $cache_category[$category_sid]['category_name'] : '所有分类';

		$info = $db->pe_select('category', array('category_id'=>$category_id));
		//搜索
		$sql_where = " and `product_state` = 1";
		if ($category_id) {
			$sql_where .= is_array($category_cidarr = category_cidarr($category_id)) ? " and `category_id` in('".implode("','", $category_cidarr)."')" : " and `category_id` = '{$category_id}'";
		}
		$_g_brand && $sql_where .= " and `brand_id` = ".intval($_g_brand);
		//$_g_keyword && $sql_where .= " and `product_name` like '%".pe_dbhold($_g_keyword)."%'";
		$_g_keyword && $sql_where .= " and( `product_name` like '%".pe_dbhold($_g_keyword)."%'OR brand_id IN (SELECT brand_id FROM pe_brand WHERE brand_name LIKE '%".pe_dbhold($_g_keyword)."%'))";
		$orderby_arr = array('sellnum_desc', 'money_desc', 'money_asc', 'commentnum_desc', 'clicknum_desc');
		if (in_array($_g_orderby, $orderby_arr)) {
			$orderby = explode('_', $_g_orderby);
			$sql_where .= " order by `product_{$orderby[0]}` {$orderby[1]}";
		}
		else {
			$sql_where .= " order by `product_order` asc, `product_id` desc";
		}
		$info_list = $db->pe_selectall('product', $sql_where, '*', array(40, $_g_page));
		$brand_list = cache::get('category_brand');
		$brand_list = $brand_list[$category_id];
		//热销排行
		$product_hotlist = product_hotlist();
		//当前路径
		if (isset($_g_keyword)) {
			$nowpath = category_path(0, '搜索&nbsp;&nbsp;“'.htmlspecialchars($_g_keyword)."”");
			$seo = pe_seo('站内搜索');
		}
		else {
			$nowpath = category_path($category_id);
			$seo = pe_seo($info['category_title']?$info['category_title']:$info['category_name'], $info['category_keys'], pe_text($info['category_desc']));
		}
		include(pe_tpl('product_list.html'));
	break;
	//#####################@ IP分类 @#####################//
	case 'brand':
		$brand_id = intval($id);
		$category_sid = is_array($cache_category_arr[$category_id]) ? $category_id : intval($cache_category[$category_id]['category_pid']);
		$category_sname = $category_sid ? $cache_category[$category_sid]['category_name'] : '所有分类';

		$info = $db->pe_select('brand', array('brand_id'=>$brand_id));
		//搜索
		if($brand_id){
			$sql_where = "  and `product_state` = 1 and `brand_id`= ".$brand_id." order by `product_order` asc, `product_id` desc";
		}

		$info_list = $db->pe_selectall('product', $sql_where, '*', array(40, $_g_page));
		$brand_list = cache::get('category_brand');
		$brand_list = $brand_list[$category_id];
		//热销排行
		$product_hotlist = product_hotlist();
		//当前路径
		if (isset($_g_keyword)) {
			$nowpath = category_path(0, '搜索&nbsp;&nbsp;“'.htmlspecialchars($_g_keyword)."”");
			$seo = pe_seo('站内搜索');
		}
		else {
			$nowpath = category_path($category_id);
			$seo = pe_seo($info['category_title']?$info['category_title']:$info['category_name'], $info['category_keys'], pe_text($info['category_desc']));
		}
		include(pe_tpl('product_list.html'));
		break;
	//#####################@ 商品内容 @#####################//
	default:
		$product_id = intval($act);
		$info = $db->pe_select('product', array('product_id'=>$product_id));
//        var_dump($info);die;
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
		//热销排行
		$product_hotlist = product_hotlist();
		//新品推荐
		$product_newlist = product_newlist(2);
		//当前路径
		$nowpath = category_path($info['category_id'], $info['product_name']);
//        var_dump(htmlspecialchars_decode($info['product_text']));die;
		$info['product_desc'] = $info['product_desc'] ? $info['product_desc'] : pe_cut(pe_text($info['product_text']), 200);
		$seo = pe_seo($info['product_name'], $info['product_keys'], $info['product_desc']);
		include(pe_tpl('product_view.html'));
	break;
}

?>