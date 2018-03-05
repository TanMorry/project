<?php
/*
$category_indexlist = array();
foreach((array)$cache_category_arr[0] as $k=>$v) {
    //var_dump($v);
    $v['product_list'] = $db->pe_selectall('product', array('category_id'=>category_cidarr($v['category_id']), 'product_state'=>1, 'order by'=>'product_order asc, product_id desc'), '*', array(5));
    $v['ad'] = $db->pe_select('ad', array('ad_position'=>'index_category', 'category_id'=>$v['category_id'], 'ad_state'=>1, 'order by'=>'ad_order asc, ad_id desc'));
    $category_indexlist[] = $v;
}
*/
$canvas_info = $db->pe_selectall('canvas', array( 'canvas_state'=>1, 'order by'=>'canvas_order asc, canvas_id desc'), '*');



switch ($act) {
    //#####################@ 定制 @#####################//
    case 'aaaa':

        include(pe_tpl('customize.html'));
        break;

    //#####################@ 默认 @#####################//
    default:

        include(pe_tpl('customize.html'));
        break;
}
?>