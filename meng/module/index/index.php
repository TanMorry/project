<?php
/**
 * @copyright   2008-2014 简好网络 <http://www.phpshe.com>
 * @creatdate   2012-0501 koyshe <koyshe@gmail.com>
 */

//网站公告
$notice_list = $db->pe_selectall('article', array('class_id'=>1,'order by'=>'`article_atime` desc'), '*', array(5));
//新品推荐
$product_newlist = product_newlist();
//分类下商品列表
//$category_indexlist = array();
//foreach((array)$cache_category_arr[0] as $k=>$v) {
//	//var_dump($v);
//	$v['product_list'] = $db->pe_selectall('product', array('category_id'=>category_cidarr($v['category_id']), 'product_state'=>1, 'order by'=>'product_order asc, product_id desc'), '*', array(8));
//	$v['ad'] = $db->pe_select('ad', array('ad_position'=>'index_category', 'category_id'=>$v['category_id'], 'ad_state'=>1, 'order by'=>'ad_order asc, ad_id desc'));
//	$category_indexlist[] = $v;
//}

//品牌下图片列表
$brand_indexlist = array();

$sql="SELECT SUM(product_sellnum) as sellnum,b.* from ".dbpre."product as a LEFT JOIN ".dbpre."brand as b ON a.brand_id = b.brand_id GROUP BY a.brand_id ORDER BY sellnum DESC limit 10";
$hotBrand = $db->sql_selectall($sql);
foreach ($hotBrand as $key=>$val){
    if(!empty($val['brand_id'])){
        $sql_list2 .= "(select * from ".dbpre."brand_pic where brand_pic_bid=".$val['brand_id']." and brand_pic_pid=0 limit 10)";
        if (!empty($hotBrand[$key+1]['brand_id'])) {
            $sql_list2 .= " union all ";
        }
    }else{
        unset($hotBrand[$key]);
    }
}
$list = $db->sql_selectall($sql_list2);
foreach($hotBrand as $k=>$v){
    $new = array();
    $comsql = "";
    foreach($list as $ke=>$va){
        if($va['brand_pic_bid'] == $v['brand_id']){
            $new[] = $va;
        }
        $comsql .= "(select cp.*,u.user_name,u.user_logo from ".dbpre."comment_pic cp left join ".dbpre."user u on cp.user_id=u.user_id where cp.pic_id = ".$va['brand_pic_id']." and cp.comment_pid=0 order by cp.createtime desc limit 1)";
        if (!empty($list[$ke+1])) {
            $comsql .= " union all ";
        }
    }
    $commentList = $db->sql_selectall($comsql);

    //拼接每张图的两个评论
    foreach($new as $key=>$value){
        foreach($commentList as $kk=>$vv){
            if($value['brand_pic_id'] == $vv['pic_id'] ){
                $new[$key]['comment'][$kk] = $vv;
            }
        }
    }
    $hotBrand[$k]['brand_pic_list'] = $new;
}
//var_dump($hotBrand);die;
$brand_indexlist = $hotBrand;


//顶部最新IP导航

$seo = pe_seo();
include(pe_tpl('index.html'));
?>