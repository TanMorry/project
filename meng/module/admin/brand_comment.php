<?php
/**
 * @copyright   2008-2015 简好网络 <http://www.phpshe.com>
 * @creatdate   2012-0501 koyshe <koyshe@gmail.com>
 */
$menumark = 'brand_comment';
pe_lead('hook/product.hook.php');
switch ($act) {
    //#####################@ 评价修改 @#####################//
    case 'edit':
        $comment_id = intval($_g_id);
        $sql = 'select cp.content,cp.createtime,cp.content,dp.brand_pic_bname,dp.brand_pic_name,brand_pic_addr,u.user_name from '.dbpre.'comment_pic cp left join '.dbpre.'brand_pic dp on cp.pic_id = dp.brand_pic_id left join '.dbpre.'user u on cp.user_id = u.user_id where cp.comment_pic_id='.$comment_id;
        $info = $db->sql_select($sql);
        if (isset($_p_pesubmit)) {
            pe_token_match();
            if ($db->pe_update('comment_pic', array('comment_pic_id'=>$comment_id), pe_dbhold($_p_info))) {
                pe_success('修改成功!', 'admin.php?mod=brand_comment');
            }
            else {
                pe_error('修改失败...');
            }
        }
        $seo = pe_seo($menutitle='修改图片评价', '', '', 'admin');
        include(pe_tpl('brand_comment_add.html'));
        break;
    //#####################@ 评价删除 @#####################//
    case 'del':
        pe_token_match();
//        $comment_id =  ? $_p_comment_pic_id : $_g_id;
        if(is_array($_p_comment_pic_id)){
            foreach ($_p_comment_pic_id as $val){
                $comment_id .= explode('--',$val)[0].',';
                $brand_pic_id[explode('--',$val)[1]][] = explode('--',$val)[1];
            }
        }else{
            $comment_id = explode('--',$_g_id)[0];
            $brand_pic_id[explode('--',$_g_id)[1]][] = explode('--',$_g_id)[1];
        }
        $del_sql = 'delete from '.dbpre.'comment_pic where comment_pic_id in('.trim($comment_id,',').') or comment_pid in('.trim($comment_id,',').')';
//        var_dump($del_sql);die;
        $del_res = $db->sql_delete($del_sql);
        if ($del_res) {
            //更新brand_pic 里面的评论数
            foreach($brand_pic_id as $key=>$val){
                $sql = 'update '.dbpre.'brand_pic set brand_pic_comnum=brand_pic_comnum-'.count($val).' where brand_pic_pid=0 and brand_pic_id='.$key;
                $picList = $db->sql_update($sql);
            }

            pe_success('删除成功!');
        }
        else {
            pe_error('删除失败...');
        }
        break;
    //#####################@ 图片评价列表 @#####################//
    default :
//        if(!empty(trim($_POST['search']))){
//            $sql = ' and brand_pic_name like '."'".'%'.trim($_POST['search']).'%'."'";
//            $info_list = $db->pe_selectall('brand_pic', $sql, '*', array(30, $_g_page));
//        }else{
//            $info_list = $db->pe_selectall('brand_pic', array('brand_pic_pid'=>0,'order by'=>'brand_pic_id desc'), '*', array(30, $_g_page));
////          var_dump($info_list);die;
//            $tongji['all'] = $db->pe_num('brand_pic');
//        }
        $sql = 'select cp.comment_pic_id,cp.pic_id,cp.user_id,cp.content,bp.brand_pic_name,bp.brand_pic_bname,bp.brand_pic_addr,u.user_name from '.dbpre.'comment_pic cp left join '.dbpre.'brand_pic bp on cp.pic_id = bp.brand_pic_id left join '.dbpre.'user u on cp.user_id = u.user_id where cp.comment_pid = 0 order by cp.createtime desc';
        $page = $_g_page?$_g_page:'1';
        $comment_list = $db->sql_selectall($sql,array(30, $_g_page));
        $seo = pe_seo($menutitle='图片评价管理', '', '', 'admin');
        include(pe_tpl('brand_comment.html'));
        break;
}
?>