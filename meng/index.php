<?php
include('common.php');
$cache_category = cache::get('category');
$cache_category_arr = cache::get('category_arr');
$cache_class = cache::get('class');
$cache_class_arr = cache::get('class_arr');
$cache_menu = cache::get('menu');
$cache_link = cache::get('link');
$cache_ad = cache::get('ad');
$cache_brand = cache::get('brand');
$cache_brand_arr = cache::get('brand_arr');
pe_lead('hook/ad.hook.php');
pe_lead('hook/product.hook.php');
pe_lead('hook/category.hook.php');
pe_lead('hook/user.hook.php');
pe_lead('hook/wechat.hook.php');
wechat_run();
//var_dump($cache_ad);die();
//品牌下图片列表(销量前10的品牌和该品牌的最新的两条评论)
$brand_indexlist = array();

$sql="SELECT SUM(product_sellnum) as sellnum,b.* from ".dbpre."product as a LEFT JOIN ".dbpre."brand as b ON a.brand_id = b.brand_id GROUP BY a.brand_id ORDER BY sellnum DESC limit 10";
$hotBrand = $db->sql_selectall($sql);
foreach ($hotBrand as $key=>$val){
    if(!empty($val['brand_id'])){
        $sql_list .= "(select * from ".dbpre."brand_pic where brand_pic_bid=".$val['brand_id']." and brand_pic_pid=0 limit 10)";
        if (!empty($hotBrand[$key+1]['brand_id'])) {
            $sql_list .= " union all ";
        }
    }else{
        unset($hotBrand[$key]);
    }
}
//echo $sql_list;die;
$list = $db->sql_selectall($sql_list);
foreach($hotBrand as $k=>$v){
    foreach($list as $ke=>$va){
        if($va['brand_pic_bid'] == $v['brand_id']){
            $hotBrand[$k]['brand_pic_list'][] = $va;
        }
    }
}
$brand_indexlist = $hotBrand;

if (!$db->pe_num('iplog', array('iplog_ip'=>pe_dbhold(pe_ip()), 'iplog_adate'=>date('Y-m-d')))) {
	$db->pe_insert('iplog', array('iplog_ip'=>pe_dbhold(pe_ip()), 'iplog_adate'=>date('Y-m-d'), 'iplog_atime'=>time()));
}
/*$info_list = $db->pe_selectall('huodongdata', " and `huodongdata_id` in(select `huodongdata_id` from `".dbpre."huodongdata` where `huodong_stime` <= '".time()."' and `huodong_etime` > '".time()."' order by `huodong_stime` asc) group by `product_id`");
foreach ($info_list as $v) {
	$db->pe_update('product', array('product_id'=>$v['product_id']), array('product_money'=>$v['product_money'], 'product_hd_tag'=>$v['huodong_tag'], 'product_hd_stime'=>$v['huodong_stime'], 'product_hd_etime'=>$v['huodong_etime']));
}*/

if (in_array("{$mod}.php", pe_dirlist("{$pe['path_root']}module/{$module}/*.php"))) {
	include("{$pe['path_root']}module/{$module}/{$mod}.php");
}
pe_result();
?>