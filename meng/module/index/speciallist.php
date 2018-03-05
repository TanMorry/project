<?php
switch ($act) {
    //#####################@ 小编推荐 @#####################//
    case 'tuijian':
        $product_info =$db->pe_selectall('product','','product_id,product_name,product_text,product_logo');
        include(pe_tpl('choice.html'));
        break;

    //#####################@ 分类商品列表 @#####################//
    case 'list':
        $category_id = intval($id);
        $product_info =$db->pe_selectall('product',array('category_id'=>$category_id),'product_id,product_name,product_text,product_logo');
        include(pe_tpl('choice.html'));
        break;

    //#####################@ 默认 @#####################//
    default:
        $page = $_GET['page'];
        $category_id = intval($_GET['c']);
        $brand_id = intval($_g_brand_id);
        $field = "product_id,product_name,product_text,product_logo,user_name,a.userid";
        if($category_id){
//            $product_info =$db->pe_selectall('product',array('category_id'=>$category_id),'product_id,product_name,product_text,product_logo',array(10,$page));
            $sql = "select {$field} from ".dbpre."product a left join ".dbpre."user b on a.userid=b.user_id where category_id = {$category_id} and userid=''";
            if(!empty($brand_id)){
                $sql .= " and brand_id={$brand_id}";
            }
        }else{
//            $product_info =$db->pe_selectall('product','','product_id,product_name,product_text,product_logo',array(10,$page));
            $sql = "select {$field} from ".dbpre."product a left join ".dbpre."user b on a.userid=b.user_id where userid=''";
            if(!empty($brand_id)){
                $sql .= "and product_istuijian=1 and brand_id={$brand_id}";
            }else{
                $sql .= "and product_istuijian=1";
            }
        }

		//var_dump($sql);die();
        $product_info =$db->sql_selectall($sql,array(10,$page));
        foreach ($product_info as $k=>$v) {
            $product_info[$k]['product_text'] = strip_tags($v['product_text']);
        }
        //var_dump($product_info);
        $data['result'] = $product_info;
        if(!empty( $data['result'])){
            echo json_encode($data);
        }else{
            echo false;
        }
        break;
}

?>