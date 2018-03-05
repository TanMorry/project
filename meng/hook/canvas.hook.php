<?php
//热销商品
function canvas_hotlist($num = 5) {
	global $db;
	return $db->pe_selectall('canvas', array('order by'=>'`canvas_sellnum` desc'), '*', array($num));
}

//新品推荐
function canvas_newlist($num = 5) {
	global $db;
	return $db->pe_selectall('canvas', array('canvas_istuijian'=>1, 'canvas_state'=>1, 'order by'=>'canvas_order asc, canvas_id desc'), '*', array($num));
}

//商品数量更新
function canvas_num($id, $type) {
	global $db;
	switch ($type) {
		case 'addnum':
		case 'delnum':
			$order_id = pe_dbhold($id);
			$orderdata_list = $db->pe_selectall('orderdata', array('order_id'=>$order_id));
			if ($type == 'addnum') {
				foreach ($orderdata_list as $v) {
					$db->pe_update('canvas', array('canvas_id'=>$v['canvas_id']), "`canvas_num`=`canvas_num`+{$v['canvas_num']}");
					if ($v['prorule_key'] && $db->pe_num('prorule', array('canvas_id'=>$v['canvas_id'], 'prorule_key'=>$v['prorule_key']))) {
						$db->pe_update('prorule', array('canvas_id'=>$v['canvas_id'], 'prorule_key'=>$v['prorule_key']), "`canvas_num`=`canvas_num`+{$v['canvas_num']}");
					}
				}
			}
			else {
				foreach ($orderdata_list as $v) {
					$db->pe_update('canvas', array('canvas_id'=>$v['canvas_id']), "`canvas_num`=`canvas_num`-{$v['canvas_num']}");
					if ($v['prorule_key'] && $db->pe_num('prorule', array('canvas_id'=>$v['canvas_id'], 'prorule_key'=>$v['prorule_key']))) {
						$db->pe_update('prorule', array('canvas_id'=>$v['canvas_id'], 'prorule_key'=>$v['prorule_key']), "`canvas_num`=`canvas_num`-{$v['canvas_num']}");
					}
				}
			}
		break;
		case 'sellnum':
			$order_id = pe_dbhold($id);
			$orderdata_list = $db->pe_selectall('orderdata', array('order_id'=>$order_id));
			foreach ($orderdata_list as $v) {
				$db->pe_update('canvas', array('canvas_id'=>$v['canvas_id']), "`canvas_sellnum` = `canvas_sellnum` + {$v['canvas_num']}");
			}
		break;
		case 'clicknum':
			$canvas_id = intval($id);
			$db->pe_update('canvas', array('canvas_id'=>$canvas_id), "`canvas_clicknum` = `canvas_clicknum` + ".rand(3, 5)."");
		break;
		default:
			$canvas_id = intval($id);
			if (in_array($type, array('collectnum', 'asknum'))) {
				$num = $db->pe_num(substr($type, 0, -3), array('canvas_id'=>$canvas_id));
				$db->pe_update('canvas', array('canvas_id'=>$canvas_id), array("canvas_{$type}"=>$num));
			}
			else if($type == 'commentnum') {
				$num_hao = $db->pe_num('comment', array('canvas_id'=>$canvas_id, 'comment_star'=>array(4,5)));
				$num_zhong = $db->pe_num('comment', array('canvas_id'=>$canvas_id, 'comment_star'=>3));
				$num_cha = $db->pe_num('comment', array('canvas_id'=>$canvas_id, 'comment_star'=>array(1,2)));
				$comment = $db->pe_select('comment', array('canvas_id'=>$canvas_id), "sum(comment_star) as comment_star");
				$sql_comment['canvas_commentnum'] = $num_hao + $num_zhong + $num_cha;
				$sql_comment['canvas_commentrate'] = "{$num_hao},{$num_zhong},{$num_cha}";
				$sql_comment['canvas_commentstar'] = $comment['comment_star'];
				$db->pe_update('canvas', array('canvas_id'=>$canvas_id), $sql_comment);
			}
		break;
	}
}

//计算商品活动价
function huodong_money($huodong_money, $money, $type, $value) {
	if ($huodong_money) return $huodong_money;
	if ($type == 'zhe') {
		$money = round($money * $value, 1);
	} 
	else {
		$money = round($money - $value, 1);	
	}
	return $money;
}

//活动标签
function huodong_tag($text) {
	return 'huodong_tag'.intval(strlen($text)/3);
}
?>