<?php

$category_info = $db->pe_selectall('category','','category_id,category_name');
switch ($act) {
    //#####################@ 小编推荐 @#####################//
    case 'tuijian':

        include(pe_tpl('choice.html'));
        break;

    //#####################@ 分类商品列表 @#####################//
    case 'list':
        include(pe_tpl('choice.html'));
        break;

    //#####################@ 默认 @#####################//
    default:
        $category_id = intval($_g_category_id);
        //0的时候是小编推荐

        $cate_sort = array();
        $sort_arr = array();
        $j=0;
        $k=0;
        for($i=65;$i<91;$i++){
            if($j%3==0 && $j!=0){
                $k++;
                $str = "";
            }
            $str .= strtoupper(chr($i));
            $cate_sort[$k] = $str;
            $sort_arr[$k][$j] .= strtoupper(chr($i));
            $j++;
        }
        $IPlist['sort'] = $sort_arr;
//        var_dump($sort_arr);

        //查询所有的品牌
//        $allBrand = $db->pe_selectall('brand', array('order by'=>'brand_word asc, brand_id desc'),'brand_id,brand_name,brand_word');
        $allBrand = $cache_brand = cache::get('brand');;
        $sort_dta =array();
        foreach ($allBrand as $ke=>$va){
            foreach($sort_arr as $k=>$v){
                foreach($v as $key=>$value){
                    if($va['brand_word'] == $value) {
                        $sort_dta[$value][] = $va;
                        continue;
                    }
                }
            }
        }
        $IPlist['data'] = $sort_dta;
        //所有热门的品牌
        $sql="SELECT SUM(product_sellnum) as sellnum,b.brand_name,b.brand_id from ".dbpre."product as a LEFT JOIN ".dbpre."brand as b ON a.brand_id = b.brand_id GROUP BY a.brand_id ORDER BY sellnum DESC limit 10";
        $IPlist['hot'] = $db->sql_selectall($sql);
//        $firstBrandId = $IPlist['hot'][0]['brand_id'];
        $json_list = json_encode($IPlist);
        //var_dump($IPlist);die;
        $brand_id = $_g_brand_id;
//        if(empty($brand_id))$brand_id = $firstBrandId;
        //1.查询当前品牌所有的pid为0的图片（原图）
//        $info = $db->pe_selectall('brand_pic', array('brand_pic_bid'=>$brand_id,'brand_pic_pid'=>0),'brand_pic_id,brand_pic_name,brand_pic_addr,brand_pic_text,brand_pic_zan,brand_pic_comnum',array(20, $_g_page));
        //2.查询每个图片最新的两个评论（不包括回复）数据大的时候 ，独立查询的效率很高
/*        if(!empty($info)){
            foreach($info as $key=>$value){
                $commsql .= "(select cp.*,u.user_name,u.user_logo from ".dbpre."comment_pic cp left join ".dbpre."user u on cp.user_id=u.user_id where cp.pic_id = ".$value['brand_pic_id']." and cp.comment_pid=0 order by cp.createtime desc limit 1)";

                if (!empty($info[$key+1])) {
                    $commsql .= " union all ";
                }
            }
            $comment = $db->sql_selectall($commsql);

            //拼接两个查询结果
            foreach($info as $key=>$value){
                foreach($comment as $k=>$v){
                    if($value['brand_pic_id'] == $v['pic_id']){
                        $info[$key]['comment'][$k] = $v;
                    }
                }
            }
        }*/
        $index = $_g_index?$_g_index:0;
        $type = $_g_type?$_g_type:'hot';
//        print_r($_GET);
        $menutitle = '每日精选';
        include(pe_tpl('special.html'));
        break;
}
?>