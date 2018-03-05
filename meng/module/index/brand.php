<?php
header("Content-type: text/html; charset=utf-8");
$cache_brand = cache::get('brand');

switch ($act) {
    //#####################@ 品牌列表 @#####################//
    case 'list':
//		$word_arr = range('A', 'Z');
//        var_dump($word_arr);
//		$info_list = $db->pe_selectall('brand', array('order by'=>'brand_word asc, brand_id desc'));
        $seo = pe_seo($menutitle='品牌专区');
        //先将字母分组
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
        $firstBrandId = $IPlist['hot'][0]['brand_id'];
        $json_list = json_encode($IPlist);
        $brand_id = $_g_brand_id;
        if(empty($brand_id))$brand_id = $firstBrandId;
        //1.查询当前品牌所有的pid为0的图片（原图）
        $info = $db->pe_selectall('brand_pic', array('brand_pic_bid'=>$brand_id,'brand_pic_pid'=>0),'brand_pic_id,brand_pic_name,brand_pic_addr,brand_pic_text,brand_pic_zan,brand_pic_comnum',array(20, $_g_page));
        //2.查询每个图片最新的两个评论（不包括回复）数据大的时候 ，独立查询的效率很高
        if(!empty($info)){
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
        }


        $index = $_g_index?$_g_index:0;
        $type = $_g_type?$_g_type:'hot';
//        echo 'id---'.$brand_id.'   index-----'.$index.'   type-------'.$type;

        //A-Z排序的品牌
        include(pe_tpl('brand_list.html'));
	break;
    //##########图片收藏#############//
    case 'collect':
        $brand_id = intval($_g_id);
        if (!pe_login('user')) pe_jsonshow(array('result'=>false, 'show'=>'请先登录...'));
        $info = $db->pe_select('brand_pic', array('brand_pic_id'=>$brand_id), 'brand_pic_id');
        if (!$info['brand_pic_id']) pe_jsonshow(array('result'=>false, 'show'=>'图片不存在'));
        $sql_where['product_id'] = $brand_id;
        $sql_where['user_id'] = $_s_user_id;
        if ($db->pe_num('collect', pe_dbhold($sql_where))) {
            $db->pe_delete('collect', pe_dbhold($sql_where));
            pe_jsonshow(array('result'=>true, 'show'=>'取消成功！'));
        }
        else {
            $sql_where['collect_atime'] = time();
            $sql_where['type'] = 2;
            $db->pe_insert('collect', pe_dbhold($sql_where));
            pe_jsonshow(array('result'=>true, 'show'=>'收藏成功！'));
        }
    break;
    case 'zan':
        $brand_id = intval($_g_id);
        if (!pe_login('user')) pe_jsonshow(array('result'=>false, 'show'=>'请先登录...'));
        $info = $db->pe_select('brand_pic', array('brand_pic_id'=>$brand_id), 'brand_pic_id');
        if (!$info['brand_pic_id']) pe_jsonshow(array('result'=>false, 'show'=>'图片不存在'));
        $sql_where['product_id'] = $brand_id;
        $sql_where['user_id'] = $_s_user_id;
        if ($db->pe_num('zan', pe_dbhold($sql_where))) {
            $db->pe_delete('zan', pe_dbhold($sql_where));
            //在图片表中追加点赞数（防止大数据下需要count分类统计）
            $sql = 'update '.dbpre.'brand_pic set brand_pic_zan=brand_pic_zan-1 where brand_pic_pid=0 and brand_pic_id='.$brand_id;
            $picList = $db->sql_update($sql);
            pe_jsonshow(array('result'=>true, 'show'=>'取消成功！'));
        }
        else {
            $sql_where['zan_time'] = time();
            $sql_where['type'] = 2;
            $db->pe_insert('zan', pe_dbhold($sql_where));
            $sql = 'update '.dbpre.'brand_pic set brand_pic_zan=brand_pic_zan+1 where brand_pic_pid=0 and brand_pic_id='.$brand_id;
            $picList = $db->sql_update($sql);
            pe_jsonshow(array('result'=>true, 'show'=>'点赞成功！'));
        }
    break;
    //#####################发表评论##################//
    case 'post_com':
        $brand_id = intval($_p_pic_id);
        if (!pe_login('user')) pe_jsonshow(array('result'=>false, 'show'=>'请先登录...'));
        $info = $db->pe_select('brand_pic', array('brand_pic_id'=>$brand_id), 'brand_pic_id');
        if (!$info['brand_pic_id']) pe_jsonshow(array('result'=>false, 'show'=>'图片不存在'));
        if (isset($_p_pesubmit)) {
            if(empty($_p_comment_content))pe_jsonshow(array('result'=>false, 'show'=>'请填写内容'));
            $sql_set['pic_id'] = $brand_id;
            $sql_set['user_id'] = $_s_user_id;
            $sql_set['content'] = $_p_comment_content;
            $sql_set['createtime'] = time();
            if($db->pe_insert('comment_pic', $sql_set)){
                //在图片表中追加评论数（防止大数据下需要count分类统计）
                $sql = 'update '.dbpre.'brand_pic set brand_pic_comnum=brand_pic_comnum+1 where brand_pic_pid=0 and brand_pic_id='.$brand_id;
                $picList = $db->sql_update($sql);
                pe_jsonshow(array('result'=>true));
            }else{
                pe_jsonshow(array('result'=>false, 'show'=>'评价失败！'));
            }
        }
//        include(pe_tpl('brand_detail.html'));
    break;
    //#####################回复评论##################//
    case 'reply_com':
        $brand_id = intval($_p_pic_id);
        $comment_pic_id = intval($_p_com_pid);
        if (!pe_login('user')) pe_jsonshow(array('result'=>false, 'show'=>'请先登录...'));
        $info = $db->pe_select('comment_pic', array('comment_pic_id'=>$comment_pic_id), 'comment_pic_id');
        if (!$info['comment_pic_id']) pe_jsonshow(array('result'=>false, 'show'=>'回复的评论不存在'));
        if (isset($_p_pesubmit_reply)) {
            if(empty($_p_comment_content_reply))pe_jsonshow(array('result'=>false, 'show'=>'请填写内容'));
            $sql_set['pic_id'] = $brand_id;
            $sql_set['comment_pid'] = $comment_pic_id;
            $sql_set['user_id'] = $_s_user_id;
            $sql_set['content'] = $_p_comment_content_reply;
            $sql_set['createtime'] = time();
            if($db->pe_insert('comment_pic', $sql_set)){
                pe_jsonshow(array('result'=>true));
            }else{
                pe_jsonshow(array('result'=>false, 'show'=>'评价失败！'));
            }
        }
    break;
    case 'del_reply':
        $comment_id = intval($_g_id);
        if($db->pe_delete('comment_pic', array('comment_pic_id'=>$comment_id))){
            pe_jsonshow(array('result'=>true, 'show'=>'删除成功！'));
        }else{
            pe_jsonshow(array('result'=>false, 'show'=>'删除失败！'));
        }
    break;
	//#####################@ 品牌图片详情 @#####################//
	default:
        $brand_id = intval($_g_pic_id);
        //根据id拿出该原图和旗下所有的子图
        $field = 'brand_pic_id,brand_pic_pid,brand_pic_name,brand_pic_addr,brand_pic_text,brand_pic_zan,brand_pic_comnum';
        $sql = 'select '.$field.' from '.dbpre.'brand_pic where brand_pic_id='.$brand_id.' union all select '.$field.' from '.dbpre.'brand_pic where brand_pic_pid='.$brand_id;
        $picList = $db->sql_selectall($sql);
        //取出数组中第一个并删掉原数组中第一个(不用)
//        $banner = array_shift($picList);
        //取出所有的模板（衣服，鞋子等）
        $temList = $db->pe_selectall('canvas',array('group by'=>'category_id'),'canvas_id,canvas_name,canvas_logo,canvas_money,category_id');
        //取出评论（每页30条）
        $sql = "select cp.*,u.user_name,u.user_logo from ".dbpre."comment_pic cp left join ".dbpre."user u on cp.user_id=u.user_id where cp.pic_id = ".$brand_id." and cp.comment_pid=0 order by cp.createtime desc";
        //$com = $db->pe_selectall('comment_pic', array('pic_id'=>$brand_id,'comment_pid'=>0),'*',array(2, $_g_page));
        $com = $db->sql_selectall($sql,array(30, $_g_page));

        //取出30条评论中每条评论的回复
        foreach($com as $key=>$value){
            $commsql .= "(select cp.*,u.user_name,u.user_logo from ".dbpre."comment_pic cp left join ".dbpre."user u on cp.user_id=u.user_id where cp.comment_pid = ".$value['comment_pic_id']." order by cp.createtime desc)";

            if (!empty($com[$key+1])) {
                $commsql .= " union all ";
            }
        }
        if($_s_user_id){
            $iszan = $db->pe_select('zan', array('product_id'=>$brand_id,'user_id'=>$_s_user_id), 'zan_id')?true:false;
            $iscollect = $db->pe_select('collect', array('product_id'=>$brand_id,'user_id'=>$_s_user_id), 'collect_id')?true:false;
        }
        $com_list = $db->sql_selectall($commsql);
        $tuijiansql = 'select p.product_id,p.product_logo,p.product_money,p.product_name from '.dbpre.'brand_pic b left join '.dbpre.'product p on b.brand_pic_bid=p.brand_id where b.brand_pic_id='.$brand_id.' order by p.product_sellnum desc limit 10';
        $tuijianList = $db->sql_selectall($tuijiansql);
        include(pe_tpl('brand_detail.html'));

}

?>